<?php
/**
 * Webhook dispatcher.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dispatches account gateway webhooks.
 */
class ALYNT_AG_Webhook_Dispatcher {

	/**
	 * Dispatch account-created webhook when configured.
	 *
	 * @param int                 $user_id  User ID.
	 * @param array<string,mixed> $settings Settings.
	 * @return true|WP_Error
	 */
	public function dispatch_account_created( $user_id, $settings ) {
		$url = ! empty( $settings['account_created_webhook'] ) ? esc_url_raw( $settings['account_created_webhook'] ) : '';
		if ( ! $url ) {
			return true;
		}

		if ( ! $this->is_allowed_delivery_url( $url ) ) {
			return new WP_Error( 'alynt_ag_webhook_insecure_url', __( 'Webhook URLs must use HTTPS unless they point to a local development host.', 'alynt-account-gateway' ) );
		}

		$user = get_userdata( absint( $user_id ) );
		if ( ! $user ) {
			return new WP_Error( 'alynt_ag_webhook_user_missing', __( 'The webhook user could not be found.', 'alynt-account-gateway' ) );
		}

		$payload = $this->build_account_created_payload( $user );

		return $this->dispatch_payload( 'account.created', $url, $user->ID, $payload, $settings );
	}

	/**
	 * Dispatch a test account-created webhook for admin verification.
	 *
	 * @param int                 $user_id  User ID used for sample payload data.
	 * @param array<string,mixed> $settings Settings.
	 * @return true|WP_Error
	 */
	public function dispatch_account_created_test( $user_id, $settings ) {
		$url = ! empty( $settings['account_created_webhook'] ) ? esc_url_raw( $settings['account_created_webhook'] ) : '';
		if ( ! $url ) {
			return new WP_Error( 'alynt_ag_webhook_missing_url', __( 'Add an account-created webhook URL before sending a test.', 'alynt-account-gateway' ) );
		}

		if ( ! $this->is_allowed_delivery_url( $url ) ) {
			return new WP_Error( 'alynt_ag_webhook_insecure_url', __( 'Webhook URLs must use HTTPS unless they point to a local development host.', 'alynt-account-gateway' ) );
		}

		$user = get_userdata( absint( $user_id ) );
		if ( ! $user ) {
			return new WP_Error( 'alynt_ag_webhook_user_missing', __( 'The webhook user could not be found.', 'alynt-account-gateway' ) );
		}

		$payload                 = $this->build_account_created_payload( $user );
		$payload['event']        = 'account.created.test';
		$payload['test']         = true;
		$payload['triggered_by'] = 'admin';

		return $this->dispatch_payload( 'account.created.test', $url, $user->ID, $payload, $settings );
	}

	/**
	 * Dispatch a JSON payload and record the response metadata.
	 *
	 * @param string              $event_name Event name.
	 * @param string              $url        Destination URL.
	 * @param int                 $user_id    User ID.
	 * @param array<string,mixed> $payload    Payload.
	 * @param array<string,mixed> $settings   Settings.
	 * @return true|WP_Error
	 */
	private function dispatch_payload( $event_name, $url, $user_id, $payload, $settings ) {
		$response = wp_remote_post(
			$url,
			array(
				'timeout' => 10,
				'headers' => array(
					'Content-Type' => 'application/json',
					'Accept'       => 'application/json',
				),
				'body'    => wp_json_encode( $payload ),
			)
		);

		$log_result = $this->log_dispatch( $event_name, $url, $user_id, $payload, $response, $settings );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( $code < 200 || $code >= 300 ) {
			return new WP_Error( 'alynt_ag_webhook_http_error', __( 'The webhook endpoint returned an unsuccessful response.', 'alynt-account-gateway' ) );
		}

		return is_wp_error( $log_result ) ? $log_result : true;
	}

	/**
	 * Return whether a webhook URL is safe for delivery.
	 *
	 * @param string $url Webhook URL.
	 * @return bool
	 */
	public function is_allowed_delivery_url( $url ) {
		$scheme = (string) wp_parse_url( $url, PHP_URL_SCHEME );
		$host   = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );

		if ( 'https' === $scheme ) {
			return true;
		}

		if ( 'http' !== $scheme ) {
			return false;
		}

		return in_array( $host, array( 'localhost', '127.0.0.1', '::1' ), true ) || '.local' === substr( $host, -6 );
	}

	/**
	 * Build account-created payload.
	 *
	 * @param WP_User $user User object.
	 * @return array<string,mixed>
	 */
	public function build_account_created_payload( $user ) {
		return array(
			'event'       => 'account.created',
			'occurred_at' => current_time( 'mysql', true ),
			'user'        => array(
				'id'           => absint( $user->ID ),
				'user_login'   => $user->user_login,
				'user_email'   => $user->user_email,
				'first_name'   => get_user_meta( $user->ID, 'first_name', true ),
				'last_name'    => get_user_meta( $user->ID, 'last_name', true ),
				'display_name' => isset( $user->display_name ) ? $user->display_name : '',
				'roles'        => isset( $user->roles ) && is_array( $user->roles ) ? array_values( $user->roles ) : array(),
				'registered'   => isset( $user->user_registered ) ? $user->user_registered : '',
			),
			'site'        => array(
				'name' => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
				'url'  => home_url( '/' ),
			),
		);
	}

	/**
	 * Store webhook response metadata and optional debug payload.
	 *
	 * @param string                       $event_name Event name.
	 * @param string                       $url        Destination URL.
	 * @param int                          $user_id    User ID.
	 * @param array<string,mixed>          $payload    Payload.
	 * @param array<string,mixed>|WP_Error $response Response.
	 * @param array<string,mixed>          $settings   Settings.
	 * @return true|WP_Error
	 */
	public function log_dispatch( $event_name, $url, $user_id, $payload, $response, $settings ) {
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

		$host = (string) wp_parse_url( $url, PHP_URL_HOST );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Plugin-owned webhook log table.
		$inserted = $wpdb->insert(
			$tables['webhook_logs'],
			array(
				'event_name'       => sanitize_text_field( $event_name ),
				'user_id'          => absint( $user_id ),
				'destination_host' => sanitize_text_field( $host ),
				'http_status'      => $http_status,
				'success'          => $success ? 1 : 0,
				'retry_count'      => 0,
				'payload'          => ! empty( $settings['debug_payload_logging'] ) ? wp_json_encode( $payload ) : null,
				'error_message'    => sanitize_text_field( $error ),
				'created_at'       => current_time( 'mysql', true ),
			),
			array( '%s', '%d', '%s', '%d', '%d', '%d', '%s', '%s', '%s' )
		);

		if ( ! $inserted ) {
			return new WP_Error( 'alynt_ag_webhook_log_failed', __( 'The webhook delivery log could not be stored.', 'alynt-account-gateway' ) );
		}

		return true;
	}
}
