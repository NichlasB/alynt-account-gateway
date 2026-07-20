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
	use ALYNT_AG_Webhook_Queue;

	const DELIVERY_HOOK = 'alynt_ag_deliver_account_created_webhook';
	const RETRY_HOOK    = 'alynt_ag_retry_account_created_webhook';
	const MAX_RETRIES   = 2;

	/**
	 * Register retry processing.
	 *
	 * @return void
	 */
	public function register() {
		add_action( self::DELIVERY_HOOK, array( $this, 'deliver_account_created' ) );
		add_action( self::RETRY_HOOK, array( $this, 'retry_account_created' ), 10, 2 );
	}

	/**
	 * Dispatch account-created webhook when configured.
	 *
	 * @param int                 $user_id  User ID.
	 * @param array<string,mixed> $settings Settings.
	 * @param int                 $retry_count Retry attempt number.
	 * @return true|WP_Error
	 */
	public function dispatch_account_created( $user_id, $settings, $retry_count = 0 ) {
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
		return $this->dispatch_payload( 'account.created', $url, $user->ID, $payload, $settings, absint( $retry_count ), true );
	}

	/**
	 * Retry a failed account-created webhook.
	 *
	 * @param int $user_id     User ID.
	 * @param int $retry_count Retry attempt number.
	 * @return true|WP_Error
	 */
	public function retry_account_created( $user_id, $retry_count ) {
		return $this->dispatch_account_created( absint( $user_id ), ALYNT_AG_Settings_Schema::get_settings(), absint( $retry_count ) );
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

		return $this->dispatch_payload( 'account.created.test', $url, $user->ID, $payload, $settings, 0, false );
	}

	/**
	 * Dispatch a JSON payload and record the response metadata.
	 *
	 * @param string              $event_name Event name.
	 * @param string              $url        Destination URL.
	 * @param int                 $user_id    User ID.
	 * @param array<string,mixed> $payload    Payload.
	 * @param array<string,mixed> $settings   Settings.
	 * @param int                 $retry_count   Retry attempt number.
	 * @param bool                $allow_retries Whether failures may be queued.
	 * @return true|WP_Error
	 */
	private function dispatch_payload( $event_name, $url, $user_id, $payload, $settings, $retry_count, $allow_retries ) {
		$body = wp_json_encode( $payload );
		if ( ! is_string( $body ) ) {
			$error = new WP_Error( 'alynt_ag_webhook_encoding_failed', __( 'The webhook payload could not be encoded.', 'alynt-account-gateway' ) );
			$this->log_dispatch( $event_name, $url, $user_id, array(), $error, $settings, $retry_count );

			return $error;
		}

		$headers = $this->build_headers( $event_name, $body, $settings );

		$response = $this->send_request(
			$url,
			array(
				'timeout' => 10,
				'headers' => $headers,
				'body'    => $body,
			)
		);

		$log_result = $this->log_dispatch( $event_name, $url, $user_id, $payload, $response, $settings, $retry_count );

		if ( is_wp_error( $response ) ) {
			$this->maybe_schedule_retry( $user_id, $retry_count, $allow_retries );
			return $response;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( $code < 200 || $code >= 300 ) {
			$this->maybe_schedule_retry( $user_id, $retry_count, $allow_retries );
			return new WP_Error( 'alynt_ag_webhook_http_error', __( 'The webhook endpoint returned an unsuccessful response.', 'alynt-account-gateway' ) );
		}

		return is_wp_error( $log_result ) ? $log_result : true;
	}

	/**
	 * Queue a bounded retry after a transport or HTTP failure.
	 *
	 * @param int  $user_id       User ID.
	 * @param int  $retry_count   Current retry count.
	 * @param bool $allow_retries Whether retries are enabled for this event.
	 * @return bool
	 */
	private function maybe_schedule_retry( $user_id, $retry_count, $allow_retries ) {
		return ( new ALYNT_AG_Webhook_Retry_Scheduler() )->schedule(
			self::RETRY_HOOK,
			self::MAX_RETRIES,
			$user_id,
			$retry_count,
			$allow_retries
		);
	}

	/**
	 * Send a webhook request through WordPress URL validation.
	 *
	 * Explicit local development URLs retain their existing support because
	 * WordPress's safe HTTP helper rejects private network destinations.
	 *
	 * @param string              $url  Destination URL.
	 * @param array<string,mixed> $args Request arguments.
	 * @return array<string,mixed>|WP_Error
	 */
	private function send_request( $url, $args ) {
		if ( $this->is_local_development_url( $url ) ) {
			return wp_remote_post( $url, $args );
		}

		return wp_safe_remote_post( $url, $args );
	}

	/**
	 * Build outbound webhook headers.
	 *
	 * @param string              $event_name Event name.
	 * @param string|false        $body       Encoded JSON body.
	 * @param array<string,mixed> $settings   Settings.
	 * @return array<string,string>
	 */
	public function build_headers( $event_name, $body, $settings ) {
		$timestamp = (string) time();
		$body      = is_string( $body ) ? $body : '';
		$headers   = array(
			'Content-Type'       => 'application/json',
			'Accept'             => 'application/json',
			'X-Alynt-AG-Event'   => sanitize_text_field( $event_name ),
			'X-Alynt-AG-Time'    => $timestamp,
			'X-Alynt-AG-Version' => '1',
		);

		$secret = isset( $settings['webhook_signing_secret'] ) ? trim( (string) $settings['webhook_signing_secret'] ) : '';
		if ( '' === $secret ) {
			return $headers;
		}

		$signed_payload = implode( '.', array( $timestamp, $event_name, $body ) );
		$signature      = hash_hmac( 'sha256', $signed_payload, $secret );

		$headers['X-Alynt-AG-Signature'] = 'sha256=' . $signature;

		return $headers;
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

		return $this->is_local_development_url( $url );
	}

	/**
	 * Return whether a URL targets an explicitly supported local host.
	 *
	 * @param string $url Candidate URL.
	 * @return bool
	 */
	private function is_local_development_url( $url ) {
		$scheme = (string) wp_parse_url( $url, PHP_URL_SCHEME );
		$host   = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );

		return 'http' === $scheme && ( in_array( $host, array( 'localhost', '127.0.0.1', '::1' ), true ) || '.local' === substr( $host, -6 ) );
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
	 * @param int                          $retry_count Retry attempt number.
	 * @return true|WP_Error
	 */
	public function log_dispatch( $event_name, $url, $user_id, $payload, $response, $settings, $retry_count = 0 ) {
		return ( new ALYNT_AG_Webhook_Delivery_Logger() )->store(
			$event_name,
			$url,
			$user_id,
			$payload,
			$response,
			$settings,
			$retry_count
		);
	}
}
