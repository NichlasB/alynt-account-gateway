<?php
/**
 * Settings page security-failure-triage component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused security-failure-triage behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Security_Failure_Triage extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render provider failure triage guidance from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return void
	 */
	public function render_security_provider_failure_triage( $logs ) {
		$items = $this->security_provider_failure_triage_items( $logs );
		?>
		<div class="alynt-ag-security-triage" aria-label="<?php esc_attr_e( 'Provider failure triage', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Provider Failure Triage', 'alynt-account-gateway' ); ?></h4>
			<p class="description"><?php esc_html_e( 'Use these focused checks when provider errors appear. They separate configuration gaps from connectivity problems and policy decisions.', 'alynt-account-gateway' ); ?></p>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $items as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h5><?php echo esc_html( $item['label'] ); ?></h5>
						<p>
							<strong><?php echo esc_html( (string) $item['count'] ); ?></strong>
							<?php echo esc_html( $item['message'] ); ?>
						</p>
						<?php if ( ! empty( $item['latest'] ) ) : ?>
							<p class="description alynt-ag-security-card__meta">
								<?php
								printf(
									/* translators: %s: latest provider failure timestamp. */
									esc_html__( 'Latest seen: %s.', 'alynt-account-gateway' ),
									esc_html( $item['latest'] )
								);
								?>
							</p>
						<?php endif; ?>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Return provider failure triage items from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return array<int,array{label:string,status:string,count:int,message:string,latest:string}>
	 */
	public function security_provider_failure_triage_items( $logs ) {
		$turnstile_missing         = $this->count_security_logs_by_provider_statuses(
			$logs,
			'turnstile',
			array( 'alynt_ag_turnstile_missing' )
		);
		$turnstile_network         = $this->count_security_logs_by_provider_statuses(
			$logs,
			'turnstile',
			array( 'alynt_ag_turnstile_request_failed' )
		);
		$turnstile_rejected        = $this->count_security_logs_by_provider_statuses(
			$logs,
			'turnstile',
			array( 'alynt_ag_turnstile_failed' )
		);
		$turnstile_missing_latest  = $this->latest_security_log_time_by_provider_statuses(
			$logs,
			'turnstile',
			array( 'alynt_ag_turnstile_missing' )
		);
		$turnstile_network_latest  = $this->latest_security_log_time_by_provider_statuses(
			$logs,
			'turnstile',
			array( 'alynt_ag_turnstile_request_failed' )
		);
		$turnstile_rejected_latest = $this->latest_security_log_time_by_provider_statuses(
			$logs,
			'turnstile',
			array( 'alynt_ag_turnstile_failed' )
		);
		$reoon_missing             = $this->count_security_logs_by_provider_statuses(
			$logs,
			'reoon',
			array( 'alynt_ag_reoon_missing' )
		);
		$reoon_network             = $this->count_security_logs_by_provider_statuses(
			$logs,
			'reoon',
			array( 'alynt_ag_reoon_request_failed' )
		);
		$reoon_unexpected          = $this->count_security_logs_by_provider_statuses(
			$logs,
			'reoon',
			array( 'alynt_ag_reoon_invalid_response' )
		);
		$reoon_missing_latest      = $this->latest_security_log_time_by_provider_statuses(
			$logs,
			'reoon',
			array( 'alynt_ag_reoon_missing' )
		);
		$reoon_network_latest      = $this->latest_security_log_time_by_provider_statuses(
			$logs,
			'reoon',
			array( 'alynt_ag_reoon_request_failed' )
		);
		$reoon_unexpected_latest   = $this->latest_security_log_time_by_provider_statuses(
			$logs,
			'reoon',
			array( 'alynt_ag_reoon_invalid_response' )
		);

		return array(
			array(
				'label'   => __( 'Turnstile Configuration', 'alynt-account-gateway' ),
				'status'  => $turnstile_missing > 0 ? 'action' : 'ready',
				'count'   => $turnstile_missing,
				'message' => __( 'recent missing-token or key configuration failures. Confirm both keys are saved and belong to the same Cloudflare Turnstile site.', 'alynt-account-gateway' ),
				'latest'  => $turnstile_missing_latest,
			),
			array(
				'label'   => __( 'Turnstile Connectivity', 'alynt-account-gateway' ),
				'status'  => $turnstile_network > 0 ? 'action' : 'ready',
				'count'   => $turnstile_network,
				'message' => __( 'recent Cloudflare Siteverify connection failures. Check outbound HTTP, DNS, firewall rules, and the saved secret key.', 'alynt-account-gateway' ),
				'latest'  => $turnstile_network_latest,
			),
			array(
				'label'   => __( 'Turnstile Challenge Rejections', 'alynt-account-gateway' ),
				'status'  => $turnstile_rejected > 0 ? 'warning' : 'ready',
				'count'   => $turnstile_rejected,
				'message' => __( 'recent rejected challenges. Confirm the registration domain is allowed in Cloudflare and compare with bot traffic before changing policy.', 'alynt-account-gateway' ),
				'latest'  => $turnstile_rejected_latest,
			),
			array(
				'label'   => __( 'Reoon Configuration', 'alynt-account-gateway' ),
				'status'  => $reoon_missing > 0 ? 'action' : 'ready',
				'count'   => $reoon_missing,
				'message' => __( 'recent missing API-key failures. Confirm the Reoon key is saved before registration relies on email verification.', 'alynt-account-gateway' ),
				'latest'  => $reoon_missing_latest,
			),
			array(
				'label'   => __( 'Reoon Connectivity', 'alynt-account-gateway' ),
				'status'  => $reoon_network > 0 ? 'action' : 'ready',
				'count'   => $reoon_network,
				'message' => __( 'recent Reoon API connection failures. Check outbound HTTP, DNS, provider availability, and key permissions.', 'alynt-account-gateway' ),
				'latest'  => $reoon_network_latest,
			),
			array(
				'label'   => __( 'Reoon Unexpected Responses', 'alynt-account-gateway' ),
				'status'  => $reoon_unexpected > 0 ? 'action' : 'ready',
				'count'   => $reoon_unexpected,
				'message' => __( 'recent malformed or unexpected Reoon responses. Test the key in Reoon and review provider availability before enabling stricter blocking.', 'alynt-account-gateway' ),
				'latest'  => $reoon_unexpected_latest,
			),
		);
	}

	/**
	 * Return the latest matching security log timestamp.
	 *
	 * @param array<int,object> $logs            Recent verification logs.
	 * @param string            $provider        Provider key.
	 * @param array<int,string> $statuses        Exact status keys.
	 * @param array<int,string> $status_suffixes Status suffixes.
	 * @return string
	 */
	public function latest_security_log_time_by_provider_statuses( $logs, $provider, $statuses, $status_suffixes = array() ) {
		$latest          = 0;
		$provider        = sanitize_key( $provider );
		$statuses        = array_map( 'sanitize_key', $statuses );
		$status_suffixes = array_map( 'sanitize_key', $status_suffixes );

		foreach ( $logs as $log ) {
			$log_provider = isset( $log->provider ) ? sanitize_key( $log->provider ) : '';
			$status       = isset( $log->status ) ? sanitize_key( $log->status ) : '';

			if ( $provider !== $log_provider || '' === $status || empty( $log->created_at ) ) {
				continue;
			}

			if ( ! in_array( $status, $statuses, true ) ) {
				$matches_suffix = false;
				foreach ( $status_suffixes as $suffix ) {
					if ( $this->status_has_suffix( $status, $suffix ) ) {
						$matches_suffix = true;
						break;
					}
				}

				if ( ! $matches_suffix ) {
					continue;
				}
			}

			$timestamp = strtotime( (string) $log->created_at );
			if ( $timestamp && $timestamp > $latest ) {
				$latest = $timestamp;
			}
		}

		if ( ! $latest ) {
			return '';
		}

		$date_format = get_option( 'date_format', 'Y-m-d' );
		$time_format = get_option( 'time_format', 'H:i' );

		return date_i18n( $date_format . ' ' . $time_format, $latest, true );
	}
}
