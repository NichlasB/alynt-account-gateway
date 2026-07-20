<?php
/**
 * Settings page security-verification-guidance component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused security-verification-guidance behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Security_Verification_Guidance extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Return admin guidance for a verification log row.
	 *
	 * @param object $log Verification log row.
	 * @return string
	 */
	public function security_verification_guidance( $log ) {
		$provider = isset( $log->provider ) ? sanitize_key( $log->provider ) : '';
		$status   = isset( $log->status ) ? sanitize_key( $log->status ) : '';
		$blocked  = ! empty( $log->blocked );

		if ( 'rate_limit' === $provider ) {
			if ( 'registration_rate_limited' === $status ) {
				return __( 'Registration attempt was blocked by the rate limit.', 'alynt-account-gateway' );
			}

			if ( 'resend_confirmation_rate_limited' === $status ) {
				return __( 'Confirmation resend was blocked by the rate limit. Ask the customer to wait for the configured resend window before trying again.', 'alynt-account-gateway' );
			}

			if ( 'login_rate_limited' === $status ) {
				return __( 'Login attempt was blocked by the rate limit.', 'alynt-account-gateway' );
			}

			if ( 'lostpassword_rate_limited' === $status ) {
				return __( 'Password reset request was blocked by the rate limit.', 'alynt-account-gateway' );
			}

			return __( 'Account action was blocked by a rate limit.', 'alynt-account-gateway' );
		}

		if ( 'reoon' === $provider ) {
			if ( $this->status_has_suffix( $status, '_flagged_blocked' ) ) {
				return __( 'Reoon blocked this flagged email because the flagged-status policy is set to block.', 'alynt-account-gateway' );
			}

			if ( $this->status_has_suffix( $status, '_flagged' ) ) {
				return __( 'Reoon allowed this email, but the status should be reviewed.', 'alynt-account-gateway' );
			}

			if ( 'alynt_ag_reoon_blocked' === $status ) {
				return __( 'Reoon blocked this email by policy.', 'alynt-account-gateway' );
			}

			if ( 'alynt_ag_reoon_missing' === $status ) {
				return __( 'Reoon was not configured when verification ran. Confirm the API key before enabling public registration.', 'alynt-account-gateway' );
			}

			if ( 'alynt_ag_reoon_request_failed' === $status ) {
				return __( 'Reoon could not be reached. Check outbound HTTP connectivity, API availability, and the saved API key.', 'alynt-account-gateway' );
			}

			if ( 'alynt_ag_reoon_invalid_response' === $status ) {
				return __( 'Reoon returned an unexpected response. Review provider availability and test the saved API key.', 'alynt-account-gateway' );
			}

			return $blocked
				? __( 'Reoon blocked this registration.', 'alynt-account-gateway' )
				: __( 'Reoon accepted this email.', 'alynt-account-gateway' );
		}

		if ( 'turnstile' === $provider ) {
			if ( 'alynt_ag_turnstile_failed' === $status ) {
				return __( 'Turnstile rejected the challenge response. Ask the customer to retry and confirm the site key matches the secret key.', 'alynt-account-gateway' );
			}

			if ( 'alynt_ag_turnstile_missing' === $status ) {
				return __( 'Turnstile was not configured when verification ran. Confirm both the site key and secret key before launch.', 'alynt-account-gateway' );
			}

			if ( 'alynt_ag_turnstile_request_failed' === $status ) {
				return __( 'Turnstile verification could not reach Cloudflare. Check outbound HTTP connectivity and the saved secret key.', 'alynt-account-gateway' );
			}

			return $blocked
				? __( 'Turnstile blocked this registration.', 'alynt-account-gateway' )
				: __( 'Turnstile challenge passed.', 'alynt-account-gateway' );
		}

		if ( 'registration_flow' === $provider ) {
			if ( 'terms_required' === $status ) {
				return __( 'Registration was blocked because terms and privacy consent was not accepted.', 'alynt-account-gateway' );
			}

			if ( 'pending_registration_failed' === $status ) {
				return __( 'The pending registration record could not be stored.', 'alynt-account-gateway' );
			}

			if ( 'consent_record_failed' === $status ) {
				return __( 'Registration consent evidence could not be stored.', 'alynt-account-gateway' );
			}

			if ( 'confirmation_email_failed' === $status ) {
				return __( 'The registration confirmation email could not be sent.', 'alynt-account-gateway' );
			}

			if ( 'confirmation_resent' === $status ) {
				return __( 'A fresh confirmation email was sent for an existing pending registration.', 'alynt-account-gateway' );
			}

			if ( 'password_mismatch' === $status ) {
				return __( 'Account creation was blocked because the password confirmation did not match.', 'alynt-account-gateway' );
			}

			if ( in_array( $status, array( 'alynt_ag_password_length', 'alynt_ag_password_complexity' ), true ) ) {
				return __( 'Account creation was blocked because the password did not meet the strength rules.', 'alynt-account-gateway' );
			}

			if ( 'email_unavailable' === $status ) {
				return __( 'Account creation was blocked because the email address became unavailable.', 'alynt-account-gateway' );
			}

			return $blocked
				? __( 'Registration flow blocked this account action.', 'alynt-account-gateway' )
				: __( 'Registration flow recorded this account action.', 'alynt-account-gateway' );
		}

		if ( $this->status_has_suffix( $status, '_flagged' ) ) {
			return __( 'Verification passed, but the status should be reviewed.', 'alynt-account-gateway' );
		}

		return $blocked
			? __( 'Verification blocked this registration.', 'alynt-account-gateway' )
			: __( 'Verification passed.', 'alynt-account-gateway' );
	}

	/**
	 * Return the recommended next step for a verification log row.
	 *
	 * @param object $log Verification log row.
	 * @return string
	 */
	public function security_verification_next_step( $log ) {
		$provider = isset( $log->provider ) ? sanitize_key( $log->provider ) : '';
		$status   = isset( $log->status ) ? sanitize_key( $log->status ) : '';
		$blocked  = ! empty( $log->blocked );

		if ( 'rate_limit' === $provider ) {
			if ( 'resend_confirmation_rate_limited' === $status ) {
				return __( 'Ask the customer to wait for the resend window; check email delivery if resend blocks repeat.', 'alynt-account-gateway' );
			}

			if ( 'login_rate_limited' === $status ) {
				return __( 'Review login lockout pressure before changing limits or support guidance.', 'alynt-account-gateway' );
			}

			if ( 'lostpassword_rate_limited' === $status ) {
				return __( 'Review reset-request pressure and delivery reports before changing limits.', 'alynt-account-gateway' );
			}

			return __( 'Review active rate-limit buckets and support reports before loosening limits.', 'alynt-account-gateway' );
		}

		if ( 'reoon' === $provider ) {
			if ( $this->status_has_suffix( $status, '_flagged_blocked' ) ) {
				return __( 'Check support tickets for false positives before keeping strict flagged-status blocking.', 'alynt-account-gateway' );
			}

			if ( $this->status_has_suffix( $status, '_flagged' ) ) {
				return __( 'Review masked email and domain patterns before changing the flagged-status policy.', 'alynt-account-gateway' );
			}

			if ( 'alynt_ag_reoon_missing' === $status || 'alynt_ag_reoon_request_failed' === $status || 'alynt_ag_reoon_invalid_response' === $status ) {
				return __( 'Test the Reoon API key and outbound HTTP path before relying on email verification.', 'alynt-account-gateway' );
			}

			return $blocked
				? __( 'Review support reports before manually recovering a blocked registrant.', 'alynt-account-gateway' )
				: __( 'No action needed unless this status pattern changes.', 'alynt-account-gateway' );
		}

		if ( 'turnstile' === $provider ) {
			if ( 'alynt_ag_turnstile_failed' === $status ) {
				return __( 'Confirm domain and key pairing, then watch for bot traffic if challenge failures rise.', 'alynt-account-gateway' );
			}

			if ( 'alynt_ag_turnstile_missing' === $status || 'alynt_ag_turnstile_request_failed' === $status ) {
				return __( 'Confirm Turnstile keys and outbound HTTP connectivity before public launch.', 'alynt-account-gateway' );
			}

			return $blocked
				? __( 'Review challenge failures before changing Turnstile settings.', 'alynt-account-gateway' )
				: __( 'No action needed unless challenge failures rise.', 'alynt-account-gateway' );
		}

		if ( 'registration_flow' === $provider ) {
			if ( 'terms_required' === $status ) {
				return __( 'Review Terms and Privacy copy if consent blocks repeat.', 'alynt-account-gateway' );
			}

			if ( in_array( $status, array( 'pending_registration_failed', 'consent_record_failed', 'confirmation_email_failed' ), true ) ) {
				return __( 'Check database writes and email delivery before public launch.', 'alynt-account-gateway' );
			}

			if ( 'confirmation_resent' === $status ) {
				return __( 'Watch resend volume and confirmation-email instructions for customer confusion.', 'alynt-account-gateway' );
			}

			if ( in_array( $status, array( 'password_mismatch', 'alynt_ag_password_length', 'alynt_ag_password_complexity' ), true ) ) {
				return __( 'Review password guidance if account setup blocks repeat.', 'alynt-account-gateway' );
			}

			if ( 'email_unavailable' === $status ) {
				return __( 'No action needed unless email-unavailable blocks repeat.', 'alynt-account-gateway' );
			}

			return $blocked
				? __( 'Review the related registration setting or support reports.', 'alynt-account-gateway' )
				: __( 'No action needed unless this registration-flow pattern rises.', 'alynt-account-gateway' );
		}

		return $blocked
			? __( 'Review this blocked verification before changing policy.', 'alynt-account-gateway' )
			: __( 'No action needed unless this verification pattern changes.', 'alynt-account-gateway' );
	}
}
