<?php
/**
 * Registration credentials facade methods.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Delegates username, password, and confirmation-token operations.
 */
trait ALYNT_AG_Registration_Credentials_Facade {

	/**
	 * Validate password and confirmation together.
	 *
	 * @param string $password         Password.
	 * @param string $password_confirm Password confirmation.
	 * @return true|WP_Error
	 */
	public function validate_password_pair( $password, $password_confirm ) {
		return $this->collaborators['credentials']->run_validate_password_pair( $password, $password_confirm );
	}

	/**
	 * Generate a unique WordPress username from settings.
	 *
	 * @param string              $first_name First name.
	 * @param string              $last_name  Last name.
	 * @param array<string,mixed> $settings   Settings.
	 * @return string
	 */
	public function generate_username( $first_name, $last_name, $settings ) {
		return $this->collaborators['credentials']->run_generate_username( $first_name, $last_name, $settings );
	}

	/**
	 * Generate a raw confirmation token.
	 *
	 * @return string
	 */
	public function generate_confirmation_token() {
		return $this->collaborators['credentials']->run_generate_confirmation_token();
	}

	/**
	 * Hash a confirmation token for storage.
	 *
	 * @param string $token Raw token.
	 * @return string
	 */
	public function hash_token( $token ) {
		return $this->collaborators['credentials']->run_hash_token( $token );
	}

	/**
	 * Verify a raw token against a stored hash.
	 *
	 * @param string $token Raw token.
	 * @param string $hash  Stored token hash.
	 * @return bool
	 */
	public function token_matches_hash( $token, $hash ) {
		return $this->collaborators['credentials']->run_token_matches_hash( $token, $hash );
	}

	/**
	 * Build the confirmation URL for a raw token.
	 *
	 * @param string              $token    Raw token.
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	public function build_confirmation_url( $token, $settings ) {
		return $this->collaborators['credentials']->run_build_confirmation_url( $token, $settings );
	}

	/**
	 * Validate password policy.
	 *
	 * @param string $password Password.
	 * @return true|WP_Error
	 */
	public function validate_password( $password ) {
		return $this->collaborators['credentials']->run_validate_password( $password );
	}
}
