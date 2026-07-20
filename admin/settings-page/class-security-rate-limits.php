<?php
/**
 * Settings page security-rate-limits component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused security-rate-limits behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Security_Rate_Limits extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Maximum active transient rows summarized in one admin request.
	 */
	const MAX_ACTIVE_BUCKETS = 1000;

	/**
	 * Render rate-limit pressure summary from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return void
	 */
	public function render_security_rate_limit_pressure( $logs ) {
		$items          = $this->security_rate_limit_pressure_items( $logs );
		$active_buckets = $this->security_active_rate_limit_bucket_items();
		$read_error     = is_wp_error( $active_buckets ) ? $active_buckets : null;
		$active_buckets = $read_error ? array() : $active_buckets;
		?>
		<div class="alynt-ag-security-pressure" aria-label="<?php esc_attr_e( 'Recent rate limit pressure', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Rate Limit Pressure', 'alynt-account-gateway' ); ?></h4>
			<p class="description">
				<?php esc_html_e( 'Recent blocks come from verification logs. Active buckets show privacy-preserving lockout pressure that is still inside the configured rate-limit window.', 'alynt-account-gateway' ); ?>
			</p>
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
			<h5><?php esc_html_e( 'Active Rate Limit Buckets', 'alynt-account-gateway' ); ?></h5>
			<?php $this->render_admin_data_read_errors( array( $read_error ) ); ?>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $active_buckets as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h6><?php echo esc_html( $item['label'] ); ?></h6>
						<p>
							<strong><?php echo esc_html( (string) $item['count'] ); ?></strong>
							<?php echo esc_html( $item['message'] ); ?>
						</p>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Return rate-limit pressure summary items from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	public function security_rate_limit_pressure_items( $logs ) {
		$counts = array(
			'registration_rate_limited'        => 0,
			'resend_confirmation_rate_limited' => 0,
			'login_rate_limited'               => 0,
			'lostpassword_rate_limited'        => 0,
		);

		foreach ( $logs as $log ) {
			$provider = isset( $log->provider ) ? sanitize_key( $log->provider ) : '';
			$status   = isset( $log->status ) ? sanitize_key( $log->status ) : '';

			if ( 'rate_limit' === $provider && array_key_exists( $status, $counts ) ) {
				++$counts[ $status ];
			}
		}

		return array(
			array(
				'label'   => __( 'Registration', 'alynt-account-gateway' ),
				'status'  => $counts['registration_rate_limited'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['registration_rate_limited'],
				'message' => __( 'recent registration blocks. Review the limit if legitimate customers are affected.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Confirmation Resends', 'alynt-account-gateway' ),
				'status'  => $counts['resend_confirmation_rate_limited'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['resend_confirmation_rate_limited'],
				'message' => __( 'recent resend blocks. Repeated resends can indicate confused customers or automated retries.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Login', 'alynt-account-gateway' ),
				'status'  => $counts['login_rate_limited'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['login_rate_limited'],
				'message' => __( 'recent login blocks. Repeated login blocks can indicate credential stuffing or customers stuck at login.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Password Reset', 'alynt-account-gateway' ),
				'status'  => $counts['lostpassword_rate_limited'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['lostpassword_rate_limited'],
				'message' => __( 'recent password-reset blocks. Check for repeated reset requests against the same account.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Return active rate-limit bucket summary items.
	 *
	 * @return array<int,array{label:string,status:string,count:int,message:string}>|WP_Error
	 */
	public function security_active_rate_limit_bucket_items() {
		$counts = array(
			'registration'        => array(
				'active' => 0,
				'locked' => 0,
			),
			'resend_confirmation' => array(
				'active' => 0,
				'locked' => 0,
			),
			'login'               => array(
				'active' => 0,
				'locked' => 0,
			),
			'lostpassword'        => array(
				'active' => 0,
				'locked' => 0,
			),
		);

		$rows = $this->security_active_rate_limit_bucket_rows();
		if ( is_wp_error( $rows ) ) {
			return $rows;
		}

		foreach ( $rows as $row ) {
			$meta = isset( $row->option_value ) ? maybe_unserialize( $row->option_value ) : null;

			if ( ! is_array( $meta ) || empty( $meta['action'] ) ) {
				continue;
			}

			$action = sanitize_key( $meta['action'] );
			if ( ! isset( $counts[ $action ] ) ) {
				continue;
			}

			if ( ! empty( $meta['expires_at'] ) && absint( $meta['expires_at'] ) < time() ) {
				continue;
			}

			++$counts[ $action ]['active'];
			if ( ! empty( $meta['locked'] ) ) {
				++$counts[ $action ]['locked'];
			}
		}

		return array(
			array(
				'label'   => __( 'Registration', 'alynt-account-gateway' ),
				'status'  => $counts['registration']['locked'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['registration']['locked'],
				'message' => sprintf(
					/* translators: %d: active bucket count. */
					__( 'active lockouts from %d current registration buckets.', 'alynt-account-gateway' ),
					$counts['registration']['active']
				),
			),
			array(
				'label'   => __( 'Confirmation Resends', 'alynt-account-gateway' ),
				'status'  => $counts['resend_confirmation']['locked'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['resend_confirmation']['locked'],
				'message' => sprintf(
					/* translators: %d: active bucket count. */
					__( 'active lockouts from %d current resend buckets.', 'alynt-account-gateway' ),
					$counts['resend_confirmation']['active']
				),
			),
			array(
				'label'   => __( 'Login', 'alynt-account-gateway' ),
				'status'  => $counts['login']['locked'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['login']['locked'],
				'message' => sprintf(
					/* translators: %d: active bucket count. */
					__( 'active lockouts from %d current login buckets.', 'alynt-account-gateway' ),
					$counts['login']['active']
				),
			),
			array(
				'label'   => __( 'Password Reset', 'alynt-account-gateway' ),
				'status'  => $counts['lostpassword']['locked'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['lostpassword']['locked'],
				'message' => sprintf(
					/* translators: %d: active bucket count. */
					__( 'active lockouts from %d current password-reset buckets.', 'alynt-account-gateway' ),
					$counts['lostpassword']['active']
				),
			),
		);
	}

	/**
	 * Fetch active rate-limit metadata transient rows.
	 *
	 * @return array<int,object>|WP_Error
	 */
	public function security_active_rate_limit_bucket_rows() {
		global $wpdb;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Admin-only aggregate observability for plugin-owned transient rows.
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT option_value FROM {$wpdb->options} WHERE option_name LIKE %s LIMIT %d",
				$wpdb->esc_like( '_transient_alynt_ag_rl_meta_' ) . '%',
				self::MAX_ACTIVE_BUCKETS + 1
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		if ( ! is_array( $rows ) ) {
			return new WP_Error(
				'alynt_ag_rate_limit_buckets_read_failed',
				__( 'Active rate-limit buckets could not be loaded. Refresh the page and check the database connection if the problem continues.', 'alynt-account-gateway' )
			);
		}

		if ( count( $rows ) > self::MAX_ACTIVE_BUCKETS ) {
			return new WP_Error(
				'alynt_ag_rate_limit_buckets_too_many',
				__( 'Active rate-limit pressure is too high to summarize safely in one request. Wait for current rate-limit windows to expire, then refresh this screen.', 'alynt-account-gateway' )
			);
		}

		return $rows;
	}
}
