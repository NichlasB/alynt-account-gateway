<?php
/**
 * Privacy service.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers privacy hooks.
 */
class ALYNT_AG_Privacy_Service {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_init', array( $this, 'add_privacy_policy_content' ) );
		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporter' ) );
		add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ) );
	}

	/**
	 * Add privacy policy helper content.
	 *
	 * @return void
	 */
	public function add_privacy_policy_content() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}

		wp_add_privacy_policy_content(
			__( 'Alynt Account Gateway', 'alynt-account-gateway' ),
			wp_kses_post(
				__( 'Alynt Account Gateway may process account registration data, email verification results, webhook delivery metadata, and consent records. Site owners should disclose configured third-party services such as Cloudflare Turnstile, Reoon Email Verifier, and outgoing webhooks.', 'alynt-account-gateway' )
			)
		);
	}

	/**
	 * Register personal data exporter.
	 *
	 * @param array<string,mixed> $exporters Exporters.
	 * @return array<string,mixed>
	 */
	public function register_exporter( $exporters ) {
		$exporters['alynt-account-gateway'] = array(
			'exporter_friendly_name' => __( 'Alynt Account Gateway', 'alynt-account-gateway' ),
			'callback'               => array( $this, 'export_personal_data' ),
		);

		return $exporters;
	}

	/**
	 * Register personal data eraser.
	 *
	 * @param array<string,mixed> $erasers Erasers.
	 * @return array<string,mixed>
	 */
	public function register_eraser( $erasers ) {
		$erasers['alynt-account-gateway'] = array(
			'eraser_friendly_name' => __( 'Alynt Account Gateway', 'alynt-account-gateway' ),
			'callback'             => array( $this, 'erase_personal_data' ),
		);

		return $erasers;
	}

	/**
	 * Record registration consent.
	 *
	 * @param string              $email    Email address.
	 * @param array<string,mixed> $settings Settings.
	 * @param int                 $user_id  User ID.
	 * @return bool
	 */
	public function record_registration_consent( $email, $settings, $user_id = 0 ) {
		global $wpdb;

		$email = sanitize_email( $email );
		if ( ! is_email( $email ) ) {
			return false;
		}

		$terms_path    = isset( $settings['terms_path'] ) ? sanitize_text_field( $settings['terms_path'] ) : '';
		$privacy_path  = isset( $settings['privacy_path'] ) ? sanitize_text_field( $settings['privacy_path'] ) : '';
		$tables        = ALYNT_AG_Database::tables();
		$settings_hash = hash(
			'sha256',
			wp_json_encode(
				array(
					'terms_path'   => $terms_path,
					'privacy_path' => $privacy_path,
				)
			)
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Plugin-owned consent table.
		return (bool) $wpdb->insert(
			$tables['consent_records'],
			array(
				'user_id'         => absint( $user_id ),
				'email'           => $email,
				'terms_path'      => $terms_path,
				'privacy_path'    => $privacy_path,
				'context'         => 'registration',
				'consent_version' => ALYNT_AG_VERSION,
				'settings_hash'   => $settings_hash,
				'created_at'      => current_time( 'mysql', true ),
			),
			array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);
	}

	/**
	 * Attach pending consent records to a created user.
	 *
	 * @param string $email   Email address.
	 * @param int    $user_id User ID.
	 * @return bool
	 */
	public function attach_registration_consent_to_user( $email, $user_id ) {
		global $wpdb;

		$tables = ALYNT_AG_Database::tables();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Plugin-owned consent table.
		return false !== $wpdb->update(
			$tables['consent_records'],
			array( 'user_id' => absint( $user_id ) ),
			array(
				'email'   => sanitize_email( $email ),
				'user_id' => 0,
				'context' => 'registration',
			),
			array( '%d' ),
			array( '%s', '%d', '%s' )
		);
	}

	/**
	 * Export personal data stored by the plugin.
	 *
	 * @param string $email_address Email address.
	 * @param int    $page          Page number.
	 * @return array<string,mixed>
	 */
	public function export_personal_data( $email_address, $page = 1 ) {
		unset( $page );

		global $wpdb;

		$email   = sanitize_email( $email_address );
		$user    = function_exists( 'get_user_by' ) ? get_user_by( 'email', $email ) : false;
		$user_id = $user && isset( $user->ID ) ? absint( $user->ID ) : 0;
		$tables  = ALYNT_AG_Database::tables();
		$data    = array();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Exporter reads plugin-owned tables.
		if ( $user_id ) {
			$consents = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$tables['consent_records']} WHERE email = %s OR user_id = %d ORDER BY created_at DESC",
					$email,
					$user_id
				)
			);
		} else {
			$consents = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM {$tables['consent_records']} WHERE email = %s ORDER BY created_at DESC",
					$email
				)
			);
		}
		$pending      = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$tables['pending_registrations']} WHERE email = %s ORDER BY created_at DESC",
				$email
			)
		);
		$verification = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$tables['verification_logs']} WHERE email = %s ORDER BY created_at DESC",
				$email
			)
		);
		$webhooks     = $user_id ? $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$tables['webhook_logs']} WHERE user_id = %d ORDER BY created_at DESC",
				$user_id
			)
		) : array();
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		foreach ( (array) $consents as $consent ) {
			$data[] = $this->export_item(
				'consent-' . $consent->id,
				__( 'Account Gateway Consent', 'alynt-account-gateway' ),
				array(
					__( 'Email', 'alynt-account-gateway' ) => $consent->email,
					__( 'Context', 'alynt-account-gateway' ) => $consent->context,
					__( 'Terms Path', 'alynt-account-gateway' ) => $consent->terms_path,
					__( 'Privacy Path', 'alynt-account-gateway' ) => $consent->privacy_path,
					__( 'Consent Version', 'alynt-account-gateway' ) => $consent->consent_version ?? '',
					__( 'Created At', 'alynt-account-gateway' ) => $consent->created_at,
				)
			);
		}

		foreach ( (array) $pending as $registration ) {
			$data[] = $this->export_item(
				'pending-registration-' . $registration->id,
				__( 'Pending Account Registration', 'alynt-account-gateway' ),
				array(
					__( 'Email', 'alynt-account-gateway' ) => $registration->email,
					__( 'First Name', 'alynt-account-gateway' ) => $registration->first_name,
					__( 'Last Name', 'alynt-account-gateway' ) => $registration->last_name,
					__( 'Status', 'alynt-account-gateway' ) => $registration->status,
					__( 'Created At', 'alynt-account-gateway' ) => $registration->created_at,
					__( 'Expires At', 'alynt-account-gateway' ) => $registration->expires_at,
				)
			);
		}

		foreach ( (array) $verification as $log ) {
			$data[] = $this->export_item(
				'verification-' . $log->id,
				__( 'Email Verification Log', 'alynt-account-gateway' ),
				array(
					__( 'Email', 'alynt-account-gateway' ) => $log->email,
					__( 'Provider', 'alynt-account-gateway' ) => $log->provider,
					__( 'Status', 'alynt-account-gateway' ) => $log->status,
					__( 'Blocked', 'alynt-account-gateway' ) => ! empty( $log->blocked ) ? __( 'Yes', 'alynt-account-gateway' ) : __( 'No', 'alynt-account-gateway' ),
					__( 'Review Decision', 'alynt-account-gateway' ) => $log->review_decision ?? '',
					__( 'Reviewed At', 'alynt-account-gateway' ) => $log->reviewed_at ?? '',
					__( 'Created At', 'alynt-account-gateway' ) => $log->created_at,
				)
			);
		}

		foreach ( (array) $webhooks as $log ) {
			$data[] = $this->export_item(
				'webhook-' . $log->id,
				__( 'Webhook Delivery Metadata', 'alynt-account-gateway' ),
				array(
					__( 'Event', 'alynt-account-gateway' ) => $log->event_name,
					__( 'Host', 'alynt-account-gateway' )  => $log->destination_host,
					__( 'HTTP Status', 'alynt-account-gateway' ) => $log->http_status,
					__( 'Success', 'alynt-account-gateway' ) => ! empty( $log->success ) ? __( 'Yes', 'alynt-account-gateway' ) : __( 'No', 'alynt-account-gateway' ),
					__( 'Created At', 'alynt-account-gateway' ) => $log->created_at,
				)
			);
		}

		return array(
			'data' => $data,
			'done' => true,
		);
	}

	/**
	 * Erase personal data stored by the plugin.
	 *
	 * @param string $email_address Email address.
	 * @param int    $page          Page number.
	 * @return array<string,mixed>
	 */
	public function erase_personal_data( $email_address, $page = 1 ) {
		unset( $page );

		global $wpdb;

		$email   = sanitize_email( $email_address );
		$user    = function_exists( 'get_user_by' ) ? get_user_by( 'email', $email ) : false;
		$user_id = $user && isset( $user->ID ) ? absint( $user->ID ) : 0;
		$tables  = ALYNT_AG_Database::tables();
		$removed = false;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Eraser deletes plugin-owned personal data.
		$removed = (bool) $wpdb->delete( $tables['pending_registrations'], array( 'email' => $email ), array( '%s' ) ) || $removed;
		$removed = (bool) $wpdb->delete( $tables['verification_logs'], array( 'email' => $email ), array( '%s' ) ) || $removed;
		$removed = (bool) $wpdb->delete( $tables['consent_records'], array( 'email' => $email ), array( '%s' ) ) || $removed;

		if ( $user_id ) {
			$removed = (bool) $wpdb->delete( $tables['consent_records'], array( 'user_id' => $user_id ), array( '%d' ) ) || $removed;
			$removed = (bool) $wpdb->delete( $tables['webhook_logs'], array( 'user_id' => $user_id ), array( '%d' ) ) || $removed;
			$removed = (bool) $wpdb->delete( $tables['audit_logs'], array( 'user_id' => $user_id ), array( '%d' ) ) || $removed;
		}
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		return array(
			'items_removed'  => $removed,
			'items_retained' => false,
			'messages'       => array(),
			'done'           => true,
		);
	}

	/**
	 * Format one exporter item.
	 *
	 * @param string              $item_id     Item ID.
	 * @param string              $group_label Group label.
	 * @param array<string,mixed> $values      Values.
	 * @return array<string,mixed>
	 */
	private function export_item( $item_id, $group_label, $values ) {
		$data = array();

		foreach ( $values as $name => $value ) {
			$data[] = array(
				'name'  => $name,
				'value' => (string) $value,
			);
		}

		return array(
			'group_id'    => 'alynt-account-gateway',
			'group_label' => $group_label,
			'item_id'     => $item_id,
			'data'        => $data,
		);
	}
}
