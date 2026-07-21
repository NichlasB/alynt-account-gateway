<?php
/**
 * Registration protection facade methods.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Delegates registration protection and activity operations.
 */
trait ALYNT_AG_Registration_Protection_Facade {

	/**
	 * Validate configured registration protection providers.
	 *
	 * @param string              $email           Email address.
	 * @param string              $turnstile_token Turnstile response token.
	 * @param array<string,mixed> $settings        Settings.
	 * @return true|WP_Error
	 */
	public function validate_registration_protection( $email, $turnstile_token, $settings ) {
		return $this->collaborators['protection']->run_validate_registration_protection(
			$email,
			$turnstile_token,
			$settings
		);
	}

	/**
	 * Log a provider verification result.
	 *
	 * @param string                            $email    Submitted email.
	 * @param string                            $provider Provider key.
	 * @param true|array<string,mixed>|WP_Error $result   Verification result.
	 * @return bool
	 */
	public function log_verification_result( $email, $provider, $result ) {
		return $this->collaborators['activity']->run_log_verification_result( $email, $provider, $result );
	}

	/**
	 * Log a registration-flow outcome to the security activity stream.
	 *
	 * @param string $email   Submitted email.
	 * @param string $status  Compact status code.
	 * @param bool   $blocked Whether the flow was blocked.
	 * @return bool
	 */
	public function log_registration_flow_result( $email, $status, $blocked = true ) {
		return $this->collaborators['activity']->run_log_registration_flow_result( $email, $status, $blocked );
	}

	/**
	 * Apply the configured policy for Reoon flagged statuses.
	 *
	 * @param true|array<string,mixed>|WP_Error $result   Reoon verification result.
	 * @param array<string,mixed>               $settings Settings.
	 * @return true|array<string,mixed>|WP_Error
	 */
	public function apply_reoon_flagged_policy( $result, $settings ) {
		return $this->collaborators['protection']->run_apply_reoon_flagged_policy( $result, $settings );
	}

	/**
	 * Validate the public registration terms acceptance checkbox.
	 *
	 * @param mixed $accepted Submitted checkbox value.
	 * @return true|WP_Error
	 */
	public function validate_terms_acceptance( $accepted ) {
		return $this->collaborators['protection']->run_validate_terms_acceptance( $accepted );
	}

	/**
	 * Validate a registration-related rate limit.
	 *
	 * @param string              $bucket     Registration bucket.
	 * @param string              $identifier Identifier such as email.
	 * @param array<string,mixed> $settings   Settings.
	 * @return true|WP_Error
	 */
	public function validate_rate_limit( $bucket, $identifier, $settings ) {
		return $this->collaborators['protection']->run_validate_rate_limit( $bucket, $identifier, $settings );
	}
}
