<?php
/**
 * Settings page security-actions component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused security-actions behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Security_Actions extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Record an admin decision for an allowed flagged Reoon result.
	 *
	 * @return void
	 */
	public function handle_review_verification() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to review verification results.', 'alynt-account-gateway' ) );
		}

		$log_id = isset( $_POST['log_id'] ) ? absint( wp_unslash( $_POST['log_id'] ) ) : 0;
		check_admin_referer( 'alynt_ag_review_verification_' . $log_id );

		$decision = isset( $_POST['decision'] ) ? sanitize_key( wp_unslash( $_POST['decision'] ) ) : '';
		$recorded = $this->record_security_review_decision( $log_id, $decision, get_current_user_id() );
		$status   = $recorded ? 'verification_review_recorded' : 'verification_review_failed';

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'            => 'alynt-account-gateway',
					'tab'             => 'security',
					'alynt_ag_notice' => $status,
				),
				admin_url( 'options-general.php' )
			)
		);
		exit;
	}

	/**
	 * Run a safe provider connection check using saved credentials.
	 *
	 * @return void
	 */
	public function handle_test_security_provider() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to test security providers.', 'alynt-account-gateway' ) );
		}

		$provider = isset( $_POST['provider'] ) ? sanitize_key( wp_unslash( $_POST['provider'] ) ) : '';
		if ( ! in_array( $provider, array( 'turnstile', 'reoon' ), true ) ) {
			wp_die( esc_html__( 'Choose a supported security provider.', 'alynt-account-gateway' ) );
		}

		check_admin_referer( 'alynt_ag_test_security_provider_' . $provider );
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		if ( 'turnstile' === $provider ) {
			$result = empty( $settings['turnstile_site_key'] ) || empty( $settings['turnstile_secret_key'] )
				? new WP_Error( 'alynt_ag_turnstile_missing', __( 'Turnstile verification is not configured.', 'alynt-account-gateway' ) )
				: ( new ALYNT_AG_Turnstile_Client() )->check_configuration( $settings['turnstile_secret_key'] );
		} else {
			$result = ( new ALYNT_AG_Reoon_Client() )->check_account( $settings['reoon_api_key'] );
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'            => 'alynt-account-gateway',
					'tab'             => 'security',
					'alynt_ag_notice' => $this->security_provider_check_notice_key( $provider, $result ),
				),
				admin_url( 'options-general.php' )
			)
		);
		exit;
	}

	/**
	 * Persist a review decision for one eligible verification row.
	 *
	 * @param int    $log_id   Verification log ID.
	 * @param string $decision Review decision key.
	 * @param int    $user_id  Reviewing administrator ID.
	 * @return bool
	 */
	public function record_security_review_decision( $log_id, $decision, $user_id ) {
		global $wpdb;

		$log_id   = absint( $log_id );
		$decision = sanitize_key( $decision );
		$user_id  = absint( $user_id );

		if ( ! $log_id || ! in_array( $decision, array( 'legitimate', 'monitor' ), true ) ) {
			return false;
		}

		$tables = ALYNT_AG_Database::tables();
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Admin action validates a plugin-owned verification row before updating it.
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, provider, status, blocked, review_decision FROM {$tables['verification_logs']} WHERE id = %d LIMIT 1",
				$log_id
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		$log = is_array( $rows ) && ! empty( $rows ) ? $rows[0] : null;
		if ( ! is_object( $log ) || ! $this->is_security_reoon_reviewable( $log ) || ! empty( $log->review_decision ) ) {
			return false;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Admin action updates one validated plugin-owned verification row.
		$updated = $wpdb->update(
			$tables['verification_logs'],
			array(
				'review_decision' => $decision,
				'reviewed_by'     => $user_id,
				'reviewed_at'     => current_time( 'mysql', true ),
			),
			array(
				'id'              => $log_id,
				'review_decision' => '',
			),
			array( '%s', '%d', '%s' ),
			array( '%d', '%s' )
		);

		if ( false === $updated || 0 === $updated ) {
			return false;
		}

		ALYNT_AG_Diagnostics_Logger::log(
			'reoon_review_recorded',
			array(
				'verification_log_id' => $log_id,
				'decision'            => $decision,
			),
			$user_id
		);

		return true;
	}
}
