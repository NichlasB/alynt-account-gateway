<?php
/**
 * Webhook delivery logging.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stores webhook response metadata and optional debug payloads.
 */
class ALYNT_AG_Webhook_Delivery_Logger {

	/**
	 * Store webhook response metadata and optional debug payload.
	 *
	 * @param string                       $event_name Event name.
	 * @param string                       $url        Destination URL.
	 * @param int                          $user_id    User ID.
	 * @param array<string,mixed>          $payload    Payload.
	 * @param array<string,mixed>|WP_Error $response   Response.
	 * @param array<string,mixed>          $settings   Settings.
	 * @param int                          $retry_count Retry attempt number.
	 * @return true|WP_Error
	 */
	public function store( $event_name, $url, $user_id, $payload, $response, $settings, $retry_count ) {
		global $wpdb;

		$tables      = ALYNT_AG_Database::tables();
		$http_status = is_wp_error( $response ) ? 0 : (int) wp_remote_retrieve_response_code( $response );
		$success     = ! is_wp_error( $response ) && $http_status >= 200 && $http_status < 300;
		$error       = '';

		if ( is_wp_error( $response ) ) {
			$error = $response->get_error_message();
		} elseif ( ! $success ) {
			$error = wp_remote_retrieve_response_message( $response );
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Plugin-owned webhook log table.
		$inserted = $wpdb->insert(
			$tables['webhook_logs'],
			array(
				'event_name'       => sanitize_text_field( $event_name ),
				'user_id'          => absint( $user_id ),
				'destination_host' => sanitize_text_field( (string) wp_parse_url( $url, PHP_URL_HOST ) ),
				'http_status'      => $http_status,
				'success'          => $success ? 1 : 0,
				'retry_count'      => absint( $retry_count ),
				'payload'          => ! empty( $settings['debug_payload_logging'] ) ? wp_json_encode( $payload ) : null,
				'error_message'    => sanitize_text_field( $error ),
				'created_at'       => current_time( 'mysql', true ),
			),
			array( '%s', '%d', '%s', '%d', '%d', '%d', '%s', '%s', '%s' )
		);

		return $inserted
			? true
			: new WP_Error( 'alynt_ag_webhook_log_failed', __( 'The webhook delivery log could not be stored.', 'alynt-account-gateway' ) );
	}
}
