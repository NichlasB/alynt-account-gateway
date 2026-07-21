<?php
/**
 * Settings page security-policy component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused security-policy behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Security_Policy extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render Reoon policy guidance for flagged email-quality statuses.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return void
	 */
	public function render_security_reoon_policy_guide( $settings ) {
		$policy       = ! empty( $settings['reoon_flagged_policy'] ) ? sanitize_key( $settings['reoon_flagged_policy'] ) : 'allow';
		$policy_label = 'block' === $policy
			? __( 'Block flagged statuses', 'alynt-account-gateway' )
			: __( 'Allow and log flagged statuses', 'alynt-account-gateway' );
		$policy_items = $this->security_reoon_policy_visibility_items( $policy );
		?>
		<div class="alynt-ag-reoon-policy-guide">
			<div>
				<h3><?php esc_html_e( 'Reoon Flagged Status Guidance', 'alynt-account-gateway' ); ?></h3>
				<p>
					<?php
					echo esc_html(
						sprintf(
							/* translators: %s: selected Reoon flagged-status policy label. */
							__( 'Current policy: %s.', 'alynt-account-gateway' ),
							$policy_label
						)
					);
					?>
				</p>
			</div>
			<table class="widefat striped alynt-ag-reoon-policy-guide__table">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Reoon Result Group', 'alynt-account-gateway' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Statuses', 'alynt-account-gateway' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Registration Treatment', 'alynt-account-gateway' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $policy_items as $item ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html( $item['group'] ); ?></th>
							<td><?php echo esc_html( $item['statuses'] ); ?></td>
							<td><?php echo esc_html( $item['treatment'] ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<div class="alynt-ag-reoon-policy-guide__grid">
				<section>
					<h4><?php esc_html_e( 'Recommended default', 'alynt-account-gateway' ); ?></h4>
					<p><?php esc_html_e( 'For most stores, allow and log flagged statuses first. Catch-all domains, role accounts, unknown results, and full inboxes can include legitimate customers, so reviewing activity before blocking reduces false positives.', 'alynt-account-gateway' ); ?></p>
				</section>
				<section>
					<h4><?php esc_html_e( 'When to block', 'alynt-account-gateway' ); ?></h4>
					<p><?php esc_html_e( 'Switch to blocking when support volume, spam pressure, or fraud risk matters more than occasional manual recovery for legitimate customers.', 'alynt-account-gateway' ); ?></p>
				</section>
				<section>
					<h4><?php esc_html_e( 'Where to review', 'alynt-account-gateway' ); ?></h4>
					<p><?php esc_html_e( 'Use Recent Registration Verification Activity below to review allowed flagged results and blocked Reoon decisions with masked email addresses.', 'alynt-account-gateway' ); ?></p>
				</section>
			</div>
		</div>
		<?php
	}

	/**
	 * Return Reoon policy visibility rows for the Security tab.
	 *
	 * @param string $policy Reoon flagged-status policy.
	 * @return array<int,array{group:string,statuses:string,treatment:string}>
	 */
	public function security_reoon_policy_visibility_items( $policy ) {
		$flagged_treatment = 'block' === $policy
			? __( 'Blocked before account creation.', 'alynt-account-gateway' )
			: __( 'Allowed, logged, and shown for admin review.', 'alynt-account-gateway' );

		return array(
			array(
				'group'     => __( 'Always blocked', 'alynt-account-gateway' ),
				'statuses'  => __( 'invalid, disabled, disposable, spamtrap', 'alynt-account-gateway' ),
				'treatment' => __( 'Blocked before account creation.', 'alynt-account-gateway' ),
			),
			array(
				'group'     => __( 'Configurable flagged statuses', 'alynt-account-gateway' ),
				'statuses'  => __( 'catch_all, role_account, unknown, inbox_full', 'alynt-account-gateway' ),
				'treatment' => $flagged_treatment,
			),
		);
	}

	/**
	 * Return the human-readable Reoon flagged-status policy message.
	 *
	 * @param string $policy Reoon flagged-status policy.
	 * @return string
	 */
	public function security_reoon_flagged_policy_message( $policy ) {
		if ( 'block' === $policy ) {
			return __( 'Blocks catch-all, role account, unknown, and inbox-full statuses before account creation.', 'alynt-account-gateway' );
		}

		return __( 'Allows but logs catch-all, role account, unknown, and inbox-full statuses for admin review.', 'alynt-account-gateway' );
	}

	/**
	 * Return configured security provider count.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return int
	 */
	public function security_configured_provider_count( $settings ) {
		$count = 0;

		if ( ! empty( $settings['turnstile_site_key'] ) && ! empty( $settings['turnstile_secret_key'] ) ) {
			++$count;
		}

		if ( ! empty( $settings['reoon_api_key'] ) ) {
			++$count;
		}

		return $count;
	}

	/**
	 * Return the human-readable protection mode message.
	 *
	 * @param string $mode Protection mode.
	 * @return string
	 */
	public function security_protection_mode_message( $mode ) {
		if ( 'turnstile_and_reoon' === $mode ) {
			return __( 'Every configured provider must pass registration. Configure both Turnstile and Reoon when the site needs two independent checks.', 'alynt-account-gateway' );
		}

		return __( 'Either configured provider can pass registration. This is the recommended default for most sites.', 'alynt-account-gateway' );
	}

	/**
	 * Return security rate-limit status items.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return array<int,array{label:string,status:string,message:string}>
	 */
	public function security_rate_limit_items( $settings ) {
		return array(
			array(
				'label'   => __( 'Registration Attempts', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'message' => $this->security_rate_limit_message( $settings, 'registration_rate_limit_count', 'registration_rate_limit_window' ),
			),
			array(
				'label'   => __( 'Confirmation Resend Attempts', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'message' => $this->security_rate_limit_message( $settings, 'resend_confirmation_rate_limit_count', 'resend_confirmation_rate_limit_window' ),
			),
			array(
				'label'   => __( 'Login Attempts', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'message' => $this->security_rate_limit_message( $settings, 'login_rate_limit_count', 'login_rate_limit_window' ),
			),
			array(
				'label'   => __( 'Password Reset Attempts', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'message' => $this->security_rate_limit_message( $settings, 'lostpassword_rate_limit_count', 'lostpassword_rate_limit_window' ),
			),
		);
	}

	/**
	 * Return a rate-limit message.
	 *
	 * @param array<string,mixed> $settings   Current settings.
	 * @param string              $count_key  Count setting key.
	 * @param string              $window_key Window setting key.
	 * @return string
	 */
	public function security_rate_limit_message( $settings, $count_key, $window_key ) {
		$count  = isset( $settings[ $count_key ] ) ? max( 1, absint( $settings[ $count_key ] ) ) : 1;
		$window = isset( $settings[ $window_key ] ) ? max( 1, absint( $settings[ $window_key ] ) ) : 1;

		$attempts = sprintf(
			/* translators: %d: configured attempt count. */
			_n( '%d attempt', '%d attempts', $count, 'alynt-account-gateway' ),
			$count
		);
		$minutes = sprintf(
			/* translators: %d: configured window length in minutes. */
			_n( '%d minute', '%d minutes', $window, 'alynt-account-gateway' ),
			$window
		);

		return sprintf(
			/* translators: 1: localized attempt count, 2: localized rate-limit window. */
			__( 'Limit: %1$s within %2$s.', 'alynt-account-gateway' ),
			$attempts,
			$minutes
		);
	}
}
