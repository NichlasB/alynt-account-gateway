<?php
/**
 * Rate limiting service.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides transient-backed rate limits for account gateway actions.
 */
class ALYNT_AG_Rate_Limiter {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_filter( 'authenticate', array( $this, 'limit_login_attempts' ), 1, 3 );
		add_action( 'lostpassword_post', array( $this, 'limit_lost_password_attempts' ), 1 );
	}

	/**
	 * Limit login attempts before authentication runs.
	 *
	 * @param null|WP_User|WP_Error $user     User value.
	 * @param string                $username Username/email submitted.
	 * @param string                $password Password submitted.
	 * @return null|WP_User|WP_Error
	 */
	public function limit_login_attempts( $user, $username, $password ) {
		if ( is_wp_error( $user ) ) {
			return $user;
		}

		if ( '' === (string) $username || '' === (string) $password ) {
			return $user;
		}

		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$result   = $this->check_and_increment(
			'login',
			$username,
			$settings['login_rate_limit_count'],
			$settings['login_rate_limit_window']
		);

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $user;
	}

	/**
	 * Limit password reset attempts.
	 *
	 * @param WP_Error $errors Lost password error object.
	 * @return void
	 */
	public function limit_lost_password_attempts( $errors ) {
		$settings = ALYNT_AG_Settings_Schema::get_settings();
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Core lostpassword_post hook runs during a verified native form submission.
		$identifier = isset( $_POST['user_login'] ) ? sanitize_text_field( wp_unslash( $_POST['user_login'] ) ) : '';
		$result     = $this->check_and_increment(
			'lostpassword',
			$identifier,
			$settings['lostpassword_rate_limit_count'],
			$settings['lostpassword_rate_limit_window']
		);

		if ( is_wp_error( $result ) && is_object( $errors ) && method_exists( $errors, 'add' ) ) {
			$errors->add( $result->get_error_code(), $result->get_error_message() );
		}
	}

	/**
	 * Check and increment a bucket.
	 *
	 * @param string $action      Action name.
	 * @param string $identifier  Submitted identifier, such as email.
	 * @param int    $limit       Maximum attempts.
	 * @param int    $window_mins Window in minutes.
	 * @return true|WP_Error
	 */
	public function check_and_increment( $action, $identifier, $limit, $window_mins ) {
		$limit       = max( 1, absint( $limit ) );
		$window_mins = max( 1, absint( $window_mins ) );
		$key         = $this->get_bucket_key( $action, $identifier );
		$meta_key    = $this->get_bucket_meta_key( $key );
		$count       = (int) get_transient( $key );

		if ( $count >= $limit ) {
			$this->set_bucket_meta( $meta_key, $action, $count, $limit, $window_mins, true );

			return new WP_Error(
				'alynt_ag_rate_limited',
				__( 'Too many attempts. Please wait and try again.', 'alynt-account-gateway' )
			);
		}

		set_transient( $key, $count + 1, $window_mins * MINUTE_IN_SECONDS );
		$this->set_bucket_meta( $meta_key, $action, $count + 1, $limit, $window_mins, false );

		return true;
	}

	/**
	 * Build a privacy-preserving transient bucket key.
	 *
	 * @param string $action     Action name.
	 * @param string $identifier Submitted identifier.
	 * @return string
	 */
	public function get_bucket_key( $action, $identifier = '' ) {
		$parts = array(
			sanitize_key( $action ),
			strtolower( sanitize_text_field( $identifier ) ),
			$this->get_remote_ip(),
		);

		return 'alynt_ag_rl_' . hash_hmac( 'sha256', implode( '|', $parts ), wp_salt( 'auth' ) );
	}

	/**
	 * Build the metadata transient key for a rate-limit bucket.
	 *
	 * @param string $bucket_key Privacy-preserving bucket key.
	 * @return string
	 */
	private function get_bucket_meta_key( $bucket_key ) {
		return 'alynt_ag_rl_meta_' . preg_replace( '/^alynt_ag_rl_/', '', (string) $bucket_key );
	}

	/**
	 * Store aggregate metadata for admin visibility without identifiers.
	 *
	 * @param string $meta_key    Metadata transient key.
	 * @param string $action      Rate-limit action.
	 * @param int    $count       Current attempt count.
	 * @param int    $limit       Configured limit.
	 * @param int    $window_mins Window in minutes.
	 * @param bool   $locked      Whether the latest attempt was blocked.
	 * @return void
	 */
	private function set_bucket_meta( $meta_key, $action, $count, $limit, $window_mins, $locked ) {
		$existing   = get_transient( $meta_key );
		$expires_at = is_array( $existing ) && ! empty( $existing['expires_at'] )
			? max( time() + 1, absint( $existing['expires_at'] ) )
			: time() + ( max( 1, absint( $window_mins ) ) * MINUTE_IN_SECONDS );

		if ( ! $locked ) {
			$expires_at = time() + ( max( 1, absint( $window_mins ) ) * MINUTE_IN_SECONDS );
		}

		set_transient(
			$meta_key,
			array(
				'action'     => sanitize_key( $action ),
				'count'      => max( 0, absint( $count ) ),
				'limit'      => max( 1, absint( $limit ) ),
				'locked'     => (bool) $locked,
				'expires_at' => $expires_at,
			),
			max( 1, $expires_at - time() )
		);
	}

	/**
	 * Return best-effort visitor IP.
	 *
	 * @return string
	 */
	private function get_remote_ip() {
		foreach ( array( 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ) as $key ) {
			if ( ! empty( $_SERVER[ $key ] ) ) {
				$value = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
				$parts = explode( ',', $value );
				return trim( $parts[0] );
			}
		}

		return '';
	}
}
