<?php
/**
 * Persists and resolves pending registrations.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Persists and resolves pending registrations.
 */
class ALYNT_AG_Registration_Pending_Store extends ALYNT_AG_Service_Collaborator {

	/**
	 * Return destination helper.
	 *
	 * @var ALYNT_AG_Return_Destination
	 */
	private $destinations;

	/**
	 * Constructor.
	 *
	 * @param object                      $service      Public service facade.
	 * @param ALYNT_AG_Return_Destination $destinations Return destination helper.
	 */
	public function __construct( $service, $destinations ) {
		parent::__construct( $service );
		$this->destinations = $destinations;
	}

	/**
	 * Create or replace a pending registration.
	 *
	 * @param string              $first_name First name.
	 * @param string              $last_name  Last name.
	 * @param string              $email      Email address.
	 * @param array<string,mixed> $settings   Settings.
	 * @param string              $return_path Validated same-site return path.
	 * @return array<string,mixed>|WP_Error
	 */
	public function run_create_pending_registration( $first_name, $last_name, $email, $settings, $return_path = '' ) {
		global $wpdb;

		$first_name  = sanitize_text_field( $first_name );
		$last_name   = sanitize_text_field( $last_name );
		$email       = sanitize_email( $email );
		$return_path = $this->destinations->relative_path( $return_path, $settings );

		if ( ! $first_name || ! $last_name || ! $email ) {
			return new WP_Error( 'missing_required_fields', __( 'Please complete all required fields.', 'alynt-account-gateway' ) );
		}

		if ( ! is_email( $email ) ) {
			return new WP_Error( 'invalid_email', __( 'Please enter a valid email address.', 'alynt-account-gateway' ) );
		}

		if ( email_exists( $email ) ) {
			return new WP_Error( 'email_unavailable', __( 'If this email address can be used, a confirmation email will be sent.', 'alynt-account-gateway' ) );
		}

		$token      = $this->generate_confirmation_token();
		$token_hash = $this->hash_token( $token );
		$now        = current_time( 'mysql', true );
		$expires_at = gmdate( 'Y-m-d H:i:s', time() + ( max( 1, absint( $settings['registration_token_hours'] ) ) * HOUR_IN_SECONDS ) );
		$tables     = ALYNT_AG_Database::tables();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Plugin-owned pending registration table.
		$wpdb->delete(
			$tables['pending_registrations'],
			array(
				'email'  => $email,
				'status' => 'pending',
			),
			array( '%s', '%s' )
		);

		$inserted = $wpdb->insert(
			$tables['pending_registrations'],
			array(
				'email'        => $email,
				'first_name'   => $first_name,
				'last_name'    => $last_name,
				'user_id'      => 0,
				'token_hash'   => $token_hash,
				'return_path'  => $return_path,
				'status'       => 'pending',
				'expires_at'   => $expires_at,
				'created_at'   => $now,
				'confirmed_at' => null,
			),
			array( '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', null )
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( ! $inserted ) {
			$this->log_registration_flow_result( $email, 'pending_registration_failed' );
			return new WP_Error( 'pending_registration_failed', __( 'The registration could not be started. Please try again.', 'alynt-account-gateway' ) );
		}

		$privacy = new ALYNT_AG_Privacy_Service();
		if ( ! $privacy->record_registration_consent( $email, $settings ) ) {
			$this->log_registration_flow_result( $email, 'consent_record_failed' );
			return new WP_Error( 'consent_record_failed', __( 'The registration consent record could not be stored. Please try again.', 'alynt-account-gateway' ) );
		}

		return array(
			'id'               => (int) $wpdb->insert_id,
			'email'            => $email,
			'first_name'       => $first_name,
			'last_name'        => $last_name,
			'token'            => $token,
			'token_hash'       => $token_hash,
			'return_path'      => $return_path,
			'expires_at'       => $expires_at,
			'confirmation_url' => $this->build_confirmation_url( $token, $settings ),
		);
	}

	/**
	 * Find a pending registration by raw token.
	 *
	 * @param string $token Raw token.
	 * @return object|null|WP_Error
	 */
	public function run_find_pending_by_token( $token ) {
		global $wpdb;

		$token_hash = $this->hash_token( $token );
		$tables     = ALYNT_AG_Database::tables();
		$now        = current_time( 'mysql', true );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Plugin-owned pending registration table.
		$pending = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$tables['pending_registrations']} WHERE token_hash = %s AND status IN ('pending', 'email_confirmed') AND expires_at >= %s LIMIT 1",
				$token_hash,
				$now
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( null === $pending && ! empty( $wpdb->last_error ) ) {
			return new WP_Error( 'pending_registration_lookup_failed', __( 'The confirmation link could not be checked. Please try again.', 'alynt-account-gateway' ) );
		}

		return $pending;
	}

	/**
	 * Find the latest registration that can receive a fresh confirmation token.
	 *
	 * @param string $email Email address.
	 * @return object|null|WP_Error
	 */
	public function run_find_resendable_pending_by_email( $email ) {
		global $wpdb;

		$email  = sanitize_email( $email );
		$tables = ALYNT_AG_Database::tables();

		if ( ! is_email( $email ) ) {
			return null;
		}

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Plugin-owned pending registration table.
		$pending = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$tables['pending_registrations']} WHERE email = %s AND status IN ('pending', 'email_confirmed') ORDER BY id DESC LIMIT 1",
				$email
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( null === $pending && ! empty( $wpdb->last_error ) ) {
			return new WP_Error( 'pending_registration_lookup_failed', __( 'The pending registration could not be checked. Please try again.', 'alynt-account-gateway' ) );
		}

		return $pending;
	}

	/**
	 * Mark a pending registration as email-confirmed without creating a user.
	 *
	 * @param string $token Raw token.
	 * @return object|WP_Error
	 */
	public function run_confirm_pending_token( $token ) {
		global $wpdb;

		$pending = $this->find_pending_by_token( $token );
		if ( is_wp_error( $pending ) ) {
			return $pending;
		}

		if ( ! $pending ) {
			return new WP_Error( 'invalid_or_expired_token', __( 'This confirmation link is invalid or has expired.', 'alynt-account-gateway' ) );
		}

		if ( 'email_confirmed' === $pending->status ) {
			return $pending;
		}

		$tables = ALYNT_AG_Database::tables();
		$now    = current_time( 'mysql', true );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Plugin-owned pending registration table.
		$updated = $wpdb->update(
			$tables['pending_registrations'],
			array(
				'status'       => 'email_confirmed',
				'confirmed_at' => $now,
			),
			array( 'id' => (int) $pending->id ),
			array( '%s', '%s' ),
			array( '%d' )
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( false === $updated ) {
			return new WP_Error( 'pending_confirmation_failed', __( 'The email confirmation could not be saved. Please try again.', 'alynt-account-gateway' ) );
		}

		$pending->status       = 'email_confirmed';
		$pending->confirmed_at = $now;

		return $pending;
	}
}
