<?php
/**
 * Short-lived operation locks.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides privacy-preserving, atomic locks for brief state transitions.
 */
class ALYNT_AG_Operation_Lock {

	/**
	 * Acquire a lock.
	 *
	 * @param string $scope      Operation scope.
	 * @param string $identifier Private operation identifier.
	 * @param int    $ttl        Lock lifetime in seconds.
	 * @return string|WP_Error Owner token or error.
	 */
	public static function acquire( $scope, $identifier, $ttl = 10 ) {
		$name  = self::option_name( $scope, $identifier );
		$token = wp_generate_password( 24, false, false );
		$value = array(
			'token'      => $token,
			'expires_at' => time() + max( 1, absint( $ttl ) ),
		);

		if ( add_option( $name, $value, '', false ) ) {
			return $token;
		}

		$current = get_option( $name, array() );
		if ( is_array( $current ) && ! empty( $current['expires_at'] ) && (int) $current['expires_at'] < time() ) {
			delete_option( $name );
			if ( add_option( $name, $value, '', false ) ) {
				return $token;
			}
		}

		return new WP_Error(
			'alynt_ag_operation_locked',
			__( 'This operation is already in progress. Please try again.', 'alynt-account-gateway' )
		);
	}

	/**
	 * Release a lock only when the caller still owns it.
	 *
	 * @param string $scope      Operation scope.
	 * @param string $identifier Private operation identifier.
	 * @param string $token      Owner token.
	 * @return bool
	 */
	public static function release( $scope, $identifier, $token ) {
		$name    = self::option_name( $scope, $identifier );
		$current = get_option( $name, array() );

		if ( ! is_array( $current ) || empty( $current['token'] ) || ! hash_equals( (string) $current['token'], (string) $token ) ) {
			return false;
		}

		return delete_option( $name );
	}

	/**
	 * Build a bounded option name without exposing the identifier.
	 *
	 * @param string $scope      Operation scope.
	 * @param string $identifier Private operation identifier.
	 * @return string
	 */
	private static function option_name( $scope, $identifier ) {
		$digest = hash_hmac( 'sha256', (string) $identifier, wp_salt( 'auth' ) );
		return 'alynt_ag_lock_' . sanitize_key( $scope ) . '_' . $digest;
	}
}
