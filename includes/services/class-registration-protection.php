<?php
/**
 * Validates registration protection and consent.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validates registration protection and consent.
 */
class ALYNT_AG_Registration_Protection extends ALYNT_AG_Service_Collaborator {

	/**
	 * Validate configured registration protection providers.
	 *
	 * @param string              $email           Email address.
	 * @param string              $turnstile_token Turnstile response token.
	 * @param array<string,mixed> $settings        Settings.
	 * @return true|WP_Error
	 */
	public function run_validate_registration_protection( $email, $turnstile_token, $settings ) {
		$checks = array();
		$email  = sanitize_email( $email );

		if ( ! empty( $settings['turnstile_secret_key'] ) && ! empty( $settings['turnstile_site_key'] ) ) {
			$turnstile = new ALYNT_AG_Turnstile_Client();
			$checks[]  = array(
				'provider' => 'turnstile',
				'result'   => $turnstile->verify( sanitize_text_field( $turnstile_token ), $settings['turnstile_secret_key'] ),
			);
		}

		if ( ! empty( $settings['reoon_api_key'] ) && is_email( $email ) ) {
			$reoon    = new ALYNT_AG_Reoon_Client();
			$result   = $reoon->verify( $email, $settings['reoon_api_key'], $settings['reoon_mode'] ?? 'quick' );
			$checks[] = array(
				'provider' => 'reoon',
				'result'   => $this->apply_reoon_flagged_policy( $result, $settings ),
			);
		}

		if ( empty( $checks ) ) {
			return true;
		}

		$requires_all = ! empty( $settings['protection_mode'] ) && 'turnstile_and_reoon' === $settings['protection_mode'];
		$has_success  = false;
		$last_error   = null;

		foreach ( $checks as $check ) {
			$this->log_verification_result( $email, $check['provider'], $check['result'] );

			if ( is_wp_error( $check['result'] ) ) {
				$last_error = $check['result'];
				if ( $requires_all ) {
					return $check['result'];
				}
				continue;
			}

			$has_success = true;
		}

		if ( $has_success ) {
			return true;
		}

		return $last_error ? $last_error : new WP_Error( 'alynt_ag_registration_protection_failed', __( 'Registration verification failed. Please try again.', 'alynt-account-gateway' ) );
	}

	/**
	 * Apply the configured policy for Reoon flagged statuses.
	 *
	 * @param true|array<string,mixed>|WP_Error $result   Reoon verification result.
	 * @param array<string,mixed>               $settings Settings.
	 * @return true|array<string,mixed>|WP_Error
	 */
	public function run_apply_reoon_flagged_policy( $result, $settings ) {
		if ( is_wp_error( $result ) || ! is_array( $result ) || empty( $result['flagged'] ) ) {
			return $result;
		}

		$policy = ! empty( $settings['reoon_flagged_policy'] ) ? sanitize_key( $settings['reoon_flagged_policy'] ) : 'allow';
		if ( 'block' !== $policy ) {
			return $result;
		}

		$status = ! empty( $result['status'] ) ? sanitize_key( $result['status'] ) : 'unknown';

		return new WP_Error(
			'alynt_ag_reoon_flagged_blocked',
			__( 'This email address cannot be used.', 'alynt-account-gateway' ),
			array(
				'status'  => $status,
				'flagged' => true,
			)
		);
	}

	/**
	 * Validate the public registration terms acceptance checkbox.
	 *
	 * @param mixed $accepted Submitted checkbox value.
	 * @return true|WP_Error
	 */
	public function run_validate_terms_acceptance( $accepted ) {
		if ( empty( $accepted ) ) {
			return new WP_Error( 'terms_required', __( 'Please accept the terms and privacy policy to continue.', 'alynt-account-gateway' ) );
		}

		return true;
	}

	/**
	 * Validate a registration-related rate limit.
	 *
	 * @param string              $bucket     Registration bucket.
	 * @param string              $identifier Identifier such as email.
	 * @param array<string,mixed> $settings   Settings.
	 * @return true|WP_Error
	 */
	public function run_validate_rate_limit( $bucket, $identifier, $settings ) {
		$limiter = new ALYNT_AG_Rate_Limiter();

		if ( 'resend_confirmation' === $bucket ) {
			$result = $limiter->check_and_increment(
				'resend_confirmation',
				$identifier,
				$settings['resend_confirmation_rate_limit_count'],
				$settings['resend_confirmation_rate_limit_window']
			);

			if ( is_wp_error( $result ) ) {
				$this->log_verification_result( $identifier, 'rate_limit', new WP_Error( 'resend_confirmation_rate_limited', $result->get_error_message() ) );
			}

			return $result;
		}

		$result = $limiter->check_and_increment(
			'registration',
			$identifier,
			$settings['registration_rate_limit_count'],
			$settings['registration_rate_limit_window']
		);

		if ( is_wp_error( $result ) ) {
			$this->log_verification_result( $identifier, 'rate_limit', new WP_Error( 'registration_rate_limited', $result->get_error_message() ) );
		}

		return $result;
	}
}
