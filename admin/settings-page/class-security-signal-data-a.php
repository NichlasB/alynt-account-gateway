<?php
/**
 * Settings page security-signal-data-a component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused security-signal-data-a behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Security_Signal_Data_A extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Return registration abuse signal items from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	public function security_registration_abuse_signal_items( $logs ) {
		$registration_limits = $this->count_security_logs_by_provider_statuses(
			$logs,
			'rate_limit',
			array( 'registration_rate_limited' )
		);
		$resend_limits       = $this->count_security_logs_by_provider_statuses(
			$logs,
			'rate_limit',
			array( 'resend_confirmation_rate_limited' )
		);
		$flagged_blocks      = $this->count_security_logs_by_provider_statuses(
			$logs,
			'reoon',
			array( 'alynt_ag_reoon_blocked' ),
			array( '_flagged_blocked' )
		);
		$setup_friction      = $this->count_security_logs_by_provider_statuses(
			$logs,
			'registration_flow',
			array( 'password_mismatch', 'alynt_ag_password_length', 'alynt_ag_password_complexity', 'email_unavailable' )
		);

		return array(
			array(
				'label'   => __( 'Registration Rate Limits', 'alynt-account-gateway' ),
				'status'  => $registration_limits > 0 ? 'warning' : 'ready',
				'count'   => $registration_limits,
				'message' => __( 'recent registration attempts blocked before verification. Watch for bursts from the same campaign or customer support reports.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Resend Rate Limits', 'alynt-account-gateway' ),
				'status'  => $resend_limits > 0 ? 'warning' : 'ready',
				'count'   => $resend_limits,
				'message' => __( 'recent confirmation resend attempts blocked by throttling. Repeated blocks can indicate inbox delivery delays or automated retries.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Flagged Email Blocks', 'alynt-account-gateway' ),
				'status'  => $flagged_blocks > 0 ? 'warning' : 'ready',
				'count'   => $flagged_blocks,
				'message' => __( 'recent Reoon policy blocks for low-quality or flagged addresses. Review if legitimate business domains appear in support tickets.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Setup Friction Blocks', 'alynt-account-gateway' ),
				'status'  => $setup_friction > 0 ? 'warning' : 'ready',
				'count'   => $setup_friction,
				'message' => __( 'recent password or email-availability blocks during account setup. Improve form guidance if legitimate customers abandon setup here.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Return access-control signal items from recent verification and diagnostics logs.
	 *
	 * @param array<int,object> $logs              Recent verification logs.
	 * @param array<int,object> $diagnostic_events Recent diagnostics events.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	public function security_access_control_signal_items( $logs, $diagnostic_events ) {
		$login_lockouts          = $this->count_security_logs_by_provider_statuses(
			$logs,
			'rate_limit',
			array( 'login_rate_limited' )
		);
		$password_reset_lockouts = $this->count_security_logs_by_provider_statuses(
			$logs,
			'rate_limit',
			array( 'lostpassword_rate_limited' )
		);
		$admin_blocks            = $this->count_diagnostics_events_by_code( $diagnostic_events, 'wp_admin_access_blocked' );
		$admin_block_detail      = $this->latest_wp_admin_block_detail( $diagnostic_events );
		$admin_block_message     = __( 'recent wp-admin redirects recorded by diagnostics. Repeated blocks can mean customers are following admin links or a role rule needs review.', 'alynt-account-gateway' );
		if ( '' !== $admin_block_detail ) {
			$admin_block_message .= ' ' . $admin_block_detail;
		}

		return array(
			array(
				'label'   => __( 'Login Lockouts', 'alynt-account-gateway' ),
				'status'  => $login_lockouts > 0 ? 'warning' : 'ready',
				'count'   => $login_lockouts,
				'message' => __( 'recent login rate-limit blocks. Review for credential stuffing or customers stuck at login.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Password Reset Lockouts', 'alynt-account-gateway' ),
				'status'  => $password_reset_lockouts > 0 ? 'warning' : 'ready',
				'count'   => $password_reset_lockouts,
				'message' => __( 'recent password-reset rate-limit blocks. Watch for repeated reset requests against the same account.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Blocked Admin Access', 'alynt-account-gateway' ),
				'status'  => $admin_blocks > 0 ? 'warning' : 'ready',
				'count'   => $admin_blocks,
				'message' => $admin_block_message,
			),
		);
	}

	/**
	 * Return safe detail from the most recent blocked wp-admin event.
	 *
	 * @param array<int,object> $diagnostic_events Recent diagnostics events.
	 * @return string
	 */
	public function latest_wp_admin_block_detail( $diagnostic_events ) {
		foreach ( $diagnostic_events as $event ) {
			$code = isset( $event->event_code ) ? sanitize_key( $event->event_code ) : '';
			if ( 'wp_admin_access_blocked' !== $code ) {
				continue;
			}

			$context          = $this->diagnostics_event_context( $event );
			$request_path     = isset( $context['request_path'] ) && is_scalar( $context['request_path'] ) ? sanitize_text_field( (string) $context['request_path'] ) : '';
			$destination_path = isset( $context['destination_path'] ) && is_scalar( $context['destination_path'] ) ? sanitize_text_field( (string) $context['destination_path'] ) : '';
			$query_keys       = $this->diagnostics_context_query_keys( $context );

			if ( '' === $request_path && isset( $context['path'] ) && is_scalar( $context['path'] ) ) {
				$request_path = sanitize_text_field( (string) $context['path'] );
			}

			if ( '' === $request_path && '' === $destination_path && empty( $query_keys ) ) {
				return '';
			}

			$detail = array();

			if ( '' !== $request_path && '' !== $destination_path ) {
				$detail[] = sprintf(
					/* translators: 1: blocked request path, 2: redirect destination path. */
					__( 'Latest blocked path: %1$s -> %2$s.', 'alynt-account-gateway' ),
					$request_path,
					$destination_path
				);
			} elseif ( '' !== $request_path ) {
				$detail[] = sprintf(
					/* translators: %s: blocked request path. */
					__( 'Latest blocked path: %s.', 'alynt-account-gateway' ),
					$request_path
				);
			}

			if ( ! empty( $query_keys ) ) {
				$detail[] = sprintf(
					/* translators: %s: comma-separated query argument names. */
					__( 'Query keys: %s.', 'alynt-account-gateway' ),
					implode( ', ', $query_keys )
				);
			}

			return implode( ' ', $detail );
		}

		return '';
	}
}
