<?php
/**
 * Privacy exporter.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Exports personal data stored in plugin-owned tables.
 */
class ALYNT_AG_Privacy_Exporter {

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
					__( 'Return Path', 'alynt-account-gateway' ) => $registration->return_path ?? '',
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
