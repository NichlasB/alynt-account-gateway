<?php
/**
 * Handles registration credentials and confirmation tokens.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles registration credentials and confirmation tokens.
 */
class ALYNT_AG_Registration_Credentials extends ALYNT_AG_Service_Collaborator {

	/**
	 * Validate password and confirmation together.
	 *
	 * @param string $password         Password.
	 * @param string $password_confirm Password confirmation.
	 * @return true|WP_Error
	 */
	public function run_validate_password_pair( $password, $password_confirm ) {
		if ( $password !== $password_confirm ) {
			return new WP_Error( 'password_mismatch', __( 'The passwords do not match.', 'alynt-account-gateway' ) );
		}

		return $this->validate_password( $password );
	}

	/**
	 * Generate a unique WordPress username from settings.
	 *
	 * @param string              $first_name First name.
	 * @param string              $last_name  Last name.
	 * @param array<string,mixed> $settings   Settings.
	 * @return string
	 */
	public function run_generate_username( $first_name, $last_name, $settings ) {
		$format = ! empty( $settings['username_format'] ) ? (string) $settings['username_format'] : '@User_{first_name}_{last_name}';
		$base   = strtr(
			$format,
			array(
				'{first_name}' => $first_name,
				'{last_name}'  => $last_name,
				'{first}'      => $first_name,
				'{last}'       => $last_name,
			)
		);

		$base = preg_replace( '/\s+/', '_', $base );
		$base = sanitize_user( $base, true );
		$base = $base ? $base : 'user';

		$username = $base;
		$suffix   = 2;

		while ( username_exists( $username ) ) {
			$username = $base . '_' . $suffix;
			++$suffix;
		}

		return $username;
	}

	/**
	 * Generate a raw confirmation token.
	 *
	 * @return string
	 */
	public function run_generate_confirmation_token() {
		return wp_generate_password( 32, false, false );
	}

	/**
	 * Hash a confirmation token for storage.
	 *
	 * @param string $token Raw token.
	 * @return string
	 */
	public function run_hash_token( $token ) {
		return hash_hmac( 'sha256', (string) $token, wp_salt( 'auth' ) );
	}

	/**
	 * Verify a raw token against a stored hash.
	 *
	 * @param string $token Raw token.
	 * @param string $hash  Stored token hash.
	 * @return bool
	 */
	public function run_token_matches_hash( $token, $hash ) {
		return hash_equals( (string) $hash, $this->hash_token( $token ) );
	}

	/**
	 * Build the confirmation URL for a raw token.
	 *
	 * @param string              $token    Raw token.
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	public function run_build_confirmation_url( $token, $settings ) {
		return add_query_arg(
			array(
				'action'         => 'setpassword',
				'alynt_ag_token' => rawurlencode( $token ),
			),
			home_url( $settings['account_action_base'] )
		);
	}

	/**
	 * Validate password policy.
	 *
	 * @param string $password Password.
	 * @return true|WP_Error
	 */
	public function run_validate_password( $password ) {
		if ( strlen( $password ) < ALYNT_AG_Registration_Service::MIN_PASSWORD_LENGTH ) {
			return new WP_Error( 'alynt_ag_password_length', __( 'Password must be at least 12 characters.', 'alynt-account-gateway' ) );
		}

		if ( ! preg_match( '/[A-Z]/', $password ) || ! preg_match( '/[a-z]/', $password ) || ! preg_match( '/[0-9]/', $password ) || ! preg_match( '/[^A-Za-z0-9]/', $password ) ) {
			return new WP_Error( 'alynt_ag_password_complexity', __( 'Password must include uppercase, lowercase, number, and symbol characters.', 'alynt-account-gateway' ) );
		}

		return true;
	}
}
