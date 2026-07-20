<?php
/**
 * Renews and delivers registration confirmations.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renews and delivers registration confirmations.
 */
class ALYNT_AG_Registration_Confirmation extends ALYNT_AG_Service_Collaborator {

	/**
	 * Send registration confirmation email.
	 *
	 * @param array<string,mixed> $pending  Pending registration data.
	 * @param array<string,mixed> $settings Settings.
	 * @return true|WP_Error
	 */
	public function run_send_confirmation_email( $pending, $settings ) {
		$expiry_hours = max( 1, absint( $settings['registration_token_hours'] ) );
		$email        = new ALYNT_AG_Email_Template_Service();
		$sent         = $email->send(
			'registration_confirmation',
			$pending['email'],
			array(
				'first_name'       => $pending['first_name'],
				'last_name'        => $pending['last_name'],
				'user_email'       => $pending['email'],
				'confirmation_url' => $pending['confirmation_url'],
				'expiry_hours'     => (string) $expiry_hours,
			),
			$settings
		);

		if ( is_wp_error( $sent ) ) {
			return new WP_Error( 'confirmation_email_failed', __( 'The confirmation email could not be sent. Please try again.', 'alynt-account-gateway' ) );
		}

		return true;
	}

	/**
	 * Renew a pending registration with a fresh confirmation token.
	 *
	 * @param object              $pending  Pending registration row.
	 * @param array<string,mixed> $settings Settings.
	 * @return array<string,mixed>|WP_Error
	 */
	public function run_renew_pending_confirmation( $pending, $settings ) {
		global $wpdb;

		$token      = $this->generate_confirmation_token();
		$token_hash = $this->hash_token( $token );
		$now        = current_time( 'mysql', true );
		$expires_at = gmdate( 'Y-m-d H:i:s', time() + ( max( 1, absint( $settings['registration_token_hours'] ) ) * HOUR_IN_SECONDS ) );
		$tables     = ALYNT_AG_Database::tables();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Plugin-owned pending registration table.
		$updated = $wpdb->update(
			$tables['pending_registrations'],
			array(
				'token_hash'   => $token_hash,
				'status'       => 'pending',
				'expires_at'   => $expires_at,
				'created_at'   => $now,
				'confirmed_at' => null,
			),
			array( 'id' => (int) $pending->id ),
			array( '%s', '%s', '%s', '%s', null ),
			array( '%d' )
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( false === $updated ) {
			return new WP_Error( 'pending_registration_failed', __( 'The confirmation link could not be renewed. Please try again.', 'alynt-account-gateway' ) );
		}

		return array(
			'id'               => (int) $pending->id,
			'email'            => $pending->email,
			'first_name'       => $pending->first_name,
			'last_name'        => $pending->last_name,
			'token'            => $token,
			'token_hash'       => $token_hash,
			'return_path'      => isset( $pending->return_path ) ? (string) $pending->return_path : '',
			'expires_at'       => $expires_at,
			'confirmation_url' => $this->build_confirmation_url( $token, $settings ),
		);
	}

	/**
	 * Resend a confirmation link without exposing whether a pending registration exists.
	 *
	 * @param string              $email    Email address.
	 * @param array<string,mixed> $settings Settings.
	 * @return true|WP_Error
	 */
	public function run_resend_confirmation( $email, $settings ) {
		$email = sanitize_email( $email );

		if ( ! is_email( $email ) ) {
			return new WP_Error( 'invalid_email', __( 'Please enter a valid email address.', 'alynt-account-gateway' ) );
		}

		if ( email_exists( $email ) ) {
			return true;
		}

		$pending = $this->find_resendable_pending_by_email( $email );
		if ( is_wp_error( $pending ) ) {
			$this->log_registration_flow_result( $email, $pending->get_error_code() );
			return $pending;
		}

		if ( ! $pending ) {
			return true;
		}

		$renewed = $this->renew_pending_confirmation( $pending, $settings );
		if ( is_wp_error( $renewed ) ) {
			$this->log_registration_flow_result( $email, $renewed->get_error_code() );
			return $renewed;
		}

		$sent = $this->send_confirmation_email( $renewed, $settings );
		if ( is_wp_error( $sent ) ) {
			$this->log_registration_flow_result( $email, $sent->get_error_code() );
		} else {
			$this->log_registration_flow_result( $email, 'confirmation_resent', false );
		}

		return $sent;
	}
}
