<?php
/**
 * Settings page security-review-queue component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused security-review-queue behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Security_Review_Queue extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render manual-review queue guidance from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return void
	 */
	public function render_security_manual_review_queue( $logs ) {
		$items = $this->security_manual_review_queue_items( $logs );
		?>
		<div class="alynt-ag-security-manual-review" aria-label="<?php esc_attr_e( 'Manual review queue', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Manual Review Queue', 'alynt-account-gateway' ); ?></h4>
			<p class="description"><?php esc_html_e( 'Highlights unresolved Reoon flagged results that were allowed by policy so support teams can record a review decision without changing the public registration flow.', 'alynt-account-gateway' ); ?></p>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $items as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h5><?php echo esc_html( $item['label'] ); ?></h5>
						<p>
							<strong><?php echo esc_html( (string) $item['count'] ); ?></strong>
							<?php echo esc_html( $item['message'] ); ?>
						</p>
					</section>
				<?php endforeach; ?>
			</div>
			<?php $this->render_security_manual_review_decision_playbook(); ?>
		</div>
		<?php
	}

	/**
	 * Render manual-review decision guidance.
	 *
	 * @return void
	 */
	public function render_security_manual_review_decision_playbook() {
		$items = $this->security_manual_review_decision_items();
		?>
		<div class="alynt-ag-security-manual-review__playbook">
			<h5><?php esc_html_e( 'Manual Review Decision Playbook', 'alynt-account-gateway' ); ?></h5>
			<p class="description"><?php esc_html_e( 'Use this as a support-friendly rubric before changing the site-wide Reoon flagged-status policy.', 'alynt-account-gateway' ); ?></p>
			<table class="widefat striped alynt-ag-security-manual-review__table" aria-label="<?php esc_attr_e( 'Manual review decision playbook', 'alynt-account-gateway' ); ?>">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Result Family', 'alynt-account-gateway' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Default Decision', 'alynt-account-gateway' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Tighten When', 'alynt-account-gateway' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Review First', 'alynt-account-gateway' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $items as $item ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html( $item['result_family'] ); ?></th>
							<td><?php echo esc_html( $item['default_decision'] ); ?></td>
							<td><?php echo esc_html( $item['tighten_when'] ); ?></td>
							<td><?php echo esc_html( $item['review_first'] ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Return manual-review decision guidance rows.
	 *
	 * @return array<int,array{result_family:string,default_decision:string,tighten_when:string,review_first:string}>
	 */
	public function security_manual_review_decision_items() {
		return array(
			array(
				'result_family'    => __( 'Role account', 'alynt-account-gateway' ),
				'default_decision' => __( 'Allow and review when shared inboxes are acceptable for the site.', 'alynt-account-gateway' ),
				'tighten_when'     => __( 'Block when personal accountability, subscriptions, or fraud exposure matter more than shared access.', 'alynt-account-gateway' ),
				'review_first'     => __( 'Check whether customers commonly use support, info, billing, or team inboxes.', 'alynt-account-gateway' ),
			),
			array(
				'result_family'    => __( 'Catch-all domain', 'alynt-account-gateway' ),
				'default_decision' => __( 'Allow and monitor until repeated abuse appears from the same domain.', 'alynt-account-gateway' ),
				'tighten_when'     => __( 'Block or manually review when one domain creates repeated low-quality registrations.', 'alynt-account-gateway' ),
				'review_first'     => __( 'Compare masked activity rows with support tickets and order history before tightening.', 'alynt-account-gateway' ),
			),
			array(
				'result_family'    => __( 'Unknown or inbox full', 'alynt-account-gateway' ),
				'default_decision' => __( 'Allow and retry support contact if the customer later needs help.', 'alynt-account-gateway' ),
				'tighten_when'     => __( 'Block when failed delivery, fake-account pressure, or manual support burden rises.', 'alynt-account-gateway' ),
				'review_first'     => __( 'Confirm email delivery health and whether the address belongs to a real customer record.', 'alynt-account-gateway' ),
			),
			array(
				'result_family'    => __( 'Disposable, spamtrap, invalid, or disabled', 'alynt-account-gateway' ),
				'default_decision' => __( 'Keep blocked; these are always treated as high-risk or unusable.', 'alynt-account-gateway' ),
				'tighten_when'     => __( 'No extra tightening is needed because these statuses are already blocked.', 'alynt-account-gateway' ),
				'review_first'     => __( 'Review only when a known customer reports a false positive.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Return manual-review queue items from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	public function security_manual_review_queue_items( $logs ) {
		$allowed_flagged = $this->count_reoon_review_logs( $logs, array(), array( '_flagged' ), false, true );
		$role_accounts   = $this->count_reoon_review_logs( $logs, array( 'role_account_flagged' ), array(), false, true );
		$risky_domains   = $this->count_reoon_review_logs( $logs, array( 'catch_all_flagged', 'unknown_flagged', 'inbox_full_flagged' ), array(), false, true );
		$blocked_flagged = $this->count_reoon_review_logs( $logs, array(), array( '_flagged_blocked' ), true );

		return array(
			array(
				'label'   => __( 'Allowed Flagged Results', 'alynt-account-gateway' ),
				'status'  => $allowed_flagged > 0 ? 'warning' : 'ready',
				'count'   => $allowed_flagged,
				'message' => __( 'unreviewed Reoon flagged results allowed by policy. Record a decision on the masked rows below before changing the site-wide policy.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Role Account Reviews', 'alynt-account-gateway' ),
				'status'  => $role_accounts > 0 ? 'warning' : 'ready',
				'count'   => $role_accounts,
				'message' => __( 'unreviewed role-account emails allowed by policy. Confirm whether shared inboxes are acceptable for this site.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Catch-All And Unknown Reviews', 'alynt-account-gateway' ),
				'status'  => $risky_domains > 0 ? 'warning' : 'ready',
				'count'   => $risky_domains,
				'message' => __( 'unreviewed catch-all, unknown, or inbox-full results allowed by policy. Watch for repeated domains before tightening policy.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Blocked Flagged Results', 'alynt-account-gateway' ),
				'status'  => $blocked_flagged > 0 ? 'warning' : 'ready',
				'count'   => $blocked_flagged,
				'message' => __( 'recent Reoon flagged results blocked by strict policy. Check support tickets for legitimate customers who may need help.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Count Reoon logs that should appear in manual-review summaries.
	 *
	 * @param array<int,object> $logs            Recent verification logs.
	 * @param array<int,string> $statuses        Exact status keys.
	 * @param array<int,string> $status_suffixes Status suffixes.
	 * @param bool|null         $blocked         Required blocked state, or null for any state.
	 * @param bool              $unreviewed_only Whether to exclude rows with a recorded review decision.
	 * @return int
	 */
	public function count_reoon_review_logs( $logs, $statuses, $status_suffixes, $blocked = null, $unreviewed_only = false ) {
		$count           = 0;
		$statuses        = array_map( 'sanitize_key', $statuses );
		$status_suffixes = array_map( 'sanitize_key', $status_suffixes );

		foreach ( $logs as $log ) {
			$provider = isset( $log->provider ) ? sanitize_key( $log->provider ) : '';
			$status   = isset( $log->status ) ? sanitize_key( $log->status ) : '';

			if ( 'reoon' !== $provider || '' === $status ) {
				continue;
			}

			$log_blocked = ! empty( $log->blocked );
			if ( null !== $blocked && $log_blocked !== (bool) $blocked ) {
				continue;
			}

			if ( $unreviewed_only && ! empty( $log->review_decision ) ) {
				continue;
			}

			if ( in_array( $status, $statuses, true ) ) {
				++$count;
				continue;
			}

			foreach ( $status_suffixes as $suffix ) {
				if ( $this->status_has_suffix( $status, $suffix ) ) {
					++$count;
					break;
				}
			}
		}

		return $count;
	}
}
