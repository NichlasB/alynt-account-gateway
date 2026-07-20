<?php
/**
 * Settings page security-signal-data-b component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused security-signal-data-b behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Security_Signal_Data_B extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Return auth redirect signal items from recent diagnostics logs.
	 *
	 * @param array<int,object> $diagnostic_events Recent diagnostics events.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	public function security_auth_redirect_signal_items( $diagnostic_events ) {
		$native_redirects = $this->count_native_login_redirects_with_preserved_keys( $diagnostic_events );
		$reset_redirects  = $this->count_native_login_redirects_with_preserved_keys( $diagnostic_events, array( 'key', 'login' ) );
		$target_redirects = $this->count_native_login_redirects_with_preserved_keys( $diagnostic_events, array( 'redirect_to' ) );

		return array(
			array(
				'label'   => __( 'Native Login Redirects', 'alynt-account-gateway' ),
				'status'  => $native_redirects > 0 ? 'warning' : 'ready',
				'count'   => $native_redirects,
				'message' => __( 'recent native wp-login.php redirects. If this rises, update menus, emails, and third-party links to use branded account routes.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Reset Link Redirects', 'alynt-account-gateway' ),
				'status'  => $reset_redirects > 0 ? 'warning' : 'ready',
				'count'   => $reset_redirects,
				'message' => __( 'recent reset-link redirects preserved password setup keys. Confirm branded set-password handling stays healthy.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Redirect-To Preserved', 'alynt-account-gateway' ),
				'status'  => $target_redirects > 0 ? 'warning' : 'ready',
				'count'   => $target_redirects,
				'message' => __( 'recent login redirects preserved a destination. Review protected-page links if customers seem bounced through login often.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Return branded authentication signal items from recent diagnostics logs.
	 *
	 * @param array<int,object> $diagnostic_events Recent diagnostics events.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	public function security_branded_auth_signal_items( $diagnostic_events ) {
		$login_failures  = $this->count_diagnostics_events_by_codes(
			$diagnostic_events,
			array( 'branded_login_failed', 'branded_login_rate_limited' )
		);
		$login_successes = $this->count_diagnostics_events_by_code( $diagnostic_events, 'branded_login_succeeded' );
		$reset_requests  = $this->count_diagnostics_events_by_code( $diagnostic_events, 'branded_password_reset_requested' );
		$reset_issues    = $this->count_diagnostics_events_by_codes(
			$diagnostic_events,
			array( 'branded_password_reset_failed', 'branded_password_reset_email_failed', 'branded_password_reset_rate_limited' )
		);
		$reset_completed = $this->count_diagnostics_events_by_code( $diagnostic_events, 'branded_password_reset_completed' );

		return array(
			array(
				'label'   => __( 'Gateway Login Failures', 'alynt-account-gateway' ),
				'status'  => $login_failures > 0 ? 'warning' : 'ready',
				'count'   => $login_failures,
				'message' => __( 'recent branded login failures or rate-limit blocks. Review if customers report login trouble or if the count rises suddenly.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Gateway Login Successes', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'count'   => $login_successes,
				'message' => __( 'recent successful branded login completions recorded by diagnostics.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Password Reset Requests', 'alynt-account-gateway' ),
				'status'  => $reset_requests > 0 ? 'warning' : 'ready',
				'count'   => $reset_requests,
				'message' => __( 'recent neutral branded password-reset requests. Watch for spikes against customer accounts or delivery support reports.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Password Reset Issues', 'alynt-account-gateway' ),
				'status'  => $reset_issues > 0 ? 'action' : 'ready',
				'count'   => $reset_issues,
				'message' => __( 'recent reset completion, email delivery, or rate-limit issues. Check reset email delivery and customer password guidance.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Password Reset Completions', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'count'   => $reset_completed,
				'message' => __( 'recent successful branded password-reset completions recorded by diagnostics.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Return registration flow signal items from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	public function security_registration_flow_signal_items( $logs ) {
		$consent_blocks  = $this->count_security_logs_by_provider_statuses(
			$logs,
			'registration_flow',
			array( 'terms_required', 'consent_record_failed' )
		);
		$system_failures = $this->count_security_logs_by_provider_statuses(
			$logs,
			'registration_flow',
			array( 'pending_registration_failed', 'confirmation_email_failed' )
		);
		$password_blocks = $this->count_security_logs_by_provider_statuses(
			$logs,
			'registration_flow',
			array( 'password_mismatch', 'alynt_ag_password_length', 'alynt_ag_password_complexity', 'email_unavailable' )
		);
		$resends         = $this->count_security_logs_by_provider_statuses(
			$logs,
			'registration_flow',
			array( 'confirmation_resent' )
		);

		return array(
			array(
				'label'   => __( 'Consent Blocks', 'alynt-account-gateway' ),
				'status'  => $consent_blocks > 0 ? 'warning' : 'ready',
				'count'   => $consent_blocks,
				'message' => __( 'recent consent-related blocks. Check Terms and Privacy copy if legitimate customers are stopping here.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Registration System Failures', 'alynt-account-gateway' ),
				'status'  => $system_failures > 0 ? 'action' : 'ready',
				'count'   => $system_failures,
				'message' => __( 'recent pending-record or confirmation-email failures. Review database writes and email delivery before public launch.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Password Setup Blocks', 'alynt-account-gateway' ),
				'status'  => $password_blocks > 0 ? 'warning' : 'ready',
				'count'   => $password_blocks,
				'message' => __( 'recent password or email-availability blocks. Review password guidance if customers struggle to complete setup.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Confirmation Resends Sent', 'alynt-account-gateway' ),
				'status'  => $resends > 0 ? 'warning' : 'ready',
				'count'   => $resends,
				'message' => __( 'recent successful resends. Repeated resends can point to delivery delays or unclear confirmation instructions.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Return account delivery signal items from recent diagnostics and webhook logs.
	 *
	 * @param array<int,object> $external_events Recent external diagnostics events.
	 * @param array<int,object> $webhook_logs    Recent webhook logs.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	public function security_delivery_signal_items( $external_events, $webhook_logs ) {
		$welcome_failures  = $this->count_diagnostics_events_by_code( $external_events, 'account_created_welcome_failed' );
		$webhook_failures  = $this->count_diagnostics_events_by_code( $external_events, 'account_created_webhook_failed' );
		$failed_deliveries = $this->count_failed_webhook_logs( $webhook_logs );

		return array(
			array(
				'label'   => __( 'Welcome Email Failures', 'alynt-account-gateway' ),
				'status'  => $welcome_failures > 0 ? 'action' : 'ready',
				'count'   => $welcome_failures,
				'message' => __( 'recent account-created welcome email failures. Check mail delivery before relying on account onboarding.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Account Webhook Failures', 'alynt-account-gateway' ),
				'status'  => $webhook_failures > 0 ? 'action' : 'ready',
				'count'   => $webhook_failures,
				'message' => __( 'recent account-created webhook dispatch failures. Review endpoint configuration and signing before relying on automation.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Failed Webhook Deliveries', 'alynt-account-gateway' ),
				'status'  => $failed_deliveries > 0 ? 'action' : 'ready',
				'count'   => $failed_deliveries,
				'message' => __( 'recent failed webhook delivery rows. Open the Webhooks tab to review destinations, HTTP status, and error messages.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Return provider health signal items from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	public function security_provider_health_signal_items( $logs ) {
		$turnstile_challenges = $this->count_security_logs_by_provider_statuses(
			$logs,
			'turnstile',
			array( 'alynt_ag_turnstile_failed' )
		);
		$turnstile_failures   = $this->count_security_logs_by_provider_statuses(
			$logs,
			'turnstile',
			array( 'alynt_ag_turnstile_missing', 'alynt_ag_turnstile_request_failed' )
		);
		$reoon_blocks         = $this->count_security_logs_by_provider_statuses(
			$logs,
			'reoon',
			array( 'alynt_ag_reoon_blocked' ),
			array( '_flagged_blocked' )
		);
		$reoon_failures       = $this->count_security_logs_by_provider_statuses(
			$logs,
			'reoon',
			array( 'alynt_ag_reoon_missing', 'alynt_ag_reoon_request_failed', 'alynt_ag_reoon_invalid_response' )
		);

		return array(
			array(
				'label'   => __( 'Turnstile Challenges', 'alynt-account-gateway' ),
				'status'  => $turnstile_challenges > 0 ? 'warning' : 'ready',
				'count'   => $turnstile_challenges,
				'message' => __( 'recent challenge rejections. Confirm the site key matches the secret key and watch for bot traffic if this rises.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Turnstile Connectivity', 'alynt-account-gateway' ),
				'status'  => $turnstile_failures > 0 ? 'action' : 'ready',
				'count'   => $turnstile_failures,
				'message' => __( 'recent configuration or network failures. Check both Turnstile keys and outbound HTTP connectivity.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Reoon Email Blocks', 'alynt-account-gateway' ),
				'status'  => $reoon_blocks > 0 ? 'warning' : 'ready',
				'count'   => $reoon_blocks,
				'message' => __( 'recent email-quality blocks. Review the policy if legitimate customers are affected.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Reoon Provider Failures', 'alynt-account-gateway' ),
				'status'  => $reoon_failures > 0 ? 'action' : 'ready',
				'count'   => $reoon_failures,
				'message' => __( 'recent configuration, connectivity, or response failures. Test the API key and outbound HTTP connectivity.', 'alynt-account-gateway' ),
			),
		);
	}
}
