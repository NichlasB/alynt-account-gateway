<?php
/**
 * Settings page admin-notices component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused admin-notices behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Admin_Notices extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render simple admin action notices.
	 *
	 * @return void
	 */
	public function render_admin_notice() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin notice flag.
		$notice                 = isset( $_GET['alynt_ag_notice'] ) ? sanitize_key( wp_unslash( $_GET['alynt_ag_notice'] ) ) : '';
		$provider_check_notices = $this->security_provider_check_notices();

		if ( isset( $provider_check_notices[ $notice ] ) ) {
			$this->render_notice( $provider_check_notices[ $notice ] );
			return;
		}

		if ( 'settings_imported_with_ignored_keys' === $notice ) {
			$this->render_ignored_import_notice();
			return;
		}

		$notices = $this->admin_notices();
		if ( isset( $notices[ $notice ] ) ) {
			$this->render_notice( $notices[ $notice ] );
		}
	}

	/**
	 * Render one standard admin notice.
	 *
	 * @param array{type:string,message:string} $notice Notice definition.
	 * @return void
	 */
	private function render_notice( $notice ) {
		?>
		<div class="notice notice-<?php echo esc_attr( $notice['type'] ); ?> is-dismissible">
			<p><?php echo esc_html( $notice['message'] ); ?></p>
		</div>
		<?php
	}

	/**
	 * Render the import notice that includes an ignored-key count.
	 *
	 * @return void
	 */
	private function render_ignored_import_notice() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin notice flag.
		$ignored_count = isset( $_GET['alynt_ag_import_ignored'] ) ? absint( wp_unslash( $_GET['alynt_ag_import_ignored'] ) ) : 0;
		$message       = sprintf(
			/* translators: %d: ignored settings key count. */
			__( 'Settings imported successfully. Unrecognized setting keys ignored: %d.', 'alynt-account-gateway' ),
			$ignored_count
		);

		$this->render_notice(
			array(
				'type'    => 'warning',
				'message' => $message,
			)
		);
	}

	/**
	 * Return safe notices for settings-page actions.
	 *
	 * @return array<string,array{type:string,message:string}>
	 */
	private function admin_notices() {
		return array(
			'settings_imported'              => array(
				'type'    => 'success',
				'message' => __( 'Settings imported successfully.', 'alynt-account-gateway' ),
			),
			'settings_import_failed'         => array(
				'type'    => 'error',
				'message' => __( 'Settings could not be imported. Choose a valid Alynt Account Gateway JSON export.', 'alynt-account-gateway' ),
			),
			'settings_import_invalid_json'   => array(
				'type'    => 'error',
				'message' => __( 'Settings could not be imported because the selected file is not valid JSON.', 'alynt-account-gateway' ),
			),
			'settings_import_empty'          => array(
				'type'    => 'error',
				'message' => __( 'Settings could not be imported because the file does not contain recognized Alynt Account Gateway settings.', 'alynt-account-gateway' ),
			),
			'settings_import_upload_failed'  => array(
				'type'    => 'error',
				'message' => __( 'Settings could not be imported because the uploaded file could not be read.', 'alynt-account-gateway' ),
			),
			'settings_import_file_too_large' => array(
				'type'    => 'error',
				'message' => __( 'Settings could not be imported because the selected file exceeds the 1 MB limit.', 'alynt-account-gateway' ),
			),
			'tab_defaults_restored'          => array(
				'type'    => 'success',
				'message' => __( 'This settings tab was restored to its defaults.', 'alynt-account-gateway' ),
			),
			'tab_defaults_failed'            => array(
				'type'    => 'error',
				'message' => __( 'This settings tab could not be restored.', 'alynt-account-gateway' ),
			),
			'diagnostics_cleared'            => array(
				'type'    => 'success',
				'message' => __( 'Diagnostics events cleared.', 'alynt-account-gateway' ),
			),
			'diagnostics_clear_failed'       => array(
				'type'    => 'error',
				'message' => __( 'Diagnostics events could not be cleared. Please retry and check the site database if the problem continues.', 'alynt-account-gateway' ),
			),
			'email_test_sent'                => array(
				'type'    => 'success',
				'message' => __( 'Test email sent.', 'alynt-account-gateway' ),
			),
			'email_test_failed'              => array(
				'type'    => 'error',
				'message' => __( 'The test email could not be sent. Check the recipient and mail configuration.', 'alynt-account-gateway' ),
			),
			'webhook_test_sent'              => array(
				'type'    => 'success',
				'message' => __( 'Test webhook sent.', 'alynt-account-gateway' ),
			),
			'webhook_test_missing'           => array(
				'type'    => 'warning',
				'message' => __( 'Add and save an account-created webhook URL before sending a test.', 'alynt-account-gateway' ),
			),
			'webhook_test_failed'            => array(
				'type'    => 'error',
				'message' => __( 'The test webhook could not be sent. Review the recent webhook deliveries table for details.', 'alynt-account-gateway' ),
			),
			'verification_review_recorded'   => array(
				'type'    => 'success',
				'message' => __( 'The Reoon review decision was recorded.', 'alynt-account-gateway' ),
			),
			'verification_review_failed'     => array(
				'type'    => 'error',
				'message' => __( 'The review decision could not be recorded. Refresh the Security tab and try again.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Return safe admin notices for provider connection checks.
	 *
	 * @return array<string,array{type:string,message:string}>
	 */
	public function security_provider_check_notices() {
		return array(
			'turnstile_check_ready'            => array(
				'type'    => 'success',
				'message' => __( 'Turnstile reached Cloudflare and the saved secret was accepted for a deliberately invalid test token. Complete a real registration challenge to confirm the site key, hostname, widget, and secret together.', 'alynt-account-gateway' ),
			),
			'turnstile_check_missing'          => array(
				'type'    => 'warning',
				'message' => __( 'Save both the Turnstile site key and secret key before running the connection check.', 'alynt-account-gateway' ),
			),
			'turnstile_check_invalid_secret'   => array(
				'type'    => 'error',
				'message' => __( 'Cloudflare rejected the saved Turnstile secret. Confirm the secret is current and belongs to the configured widget.', 'alynt-account-gateway' ),
			),
			'turnstile_check_request_failed'   => array(
				'type'    => 'error',
				'message' => __( 'The site could not reach Cloudflare Siteverify. Check outbound HTTPS, DNS, firewall rules, and provider availability.', 'alynt-account-gateway' ),
			),
			'turnstile_check_invalid_response' => array(
				'type'    => 'error',
				'message' => __( 'Cloudflare returned an unexpected response to the Turnstile connection check. Retry later and confirm the saved secret in Cloudflare.', 'alynt-account-gateway' ),
			),
			'reoon_check_ready'                => array(
				'type'    => 'success',
				'message' => __( 'Reoon responded successfully and reports the saved API account as active. No email address was submitted during this check.', 'alynt-account-gateway' ),
			),
			'reoon_check_missing'              => array(
				'type'    => 'warning',
				'message' => __( 'Save a Reoon API key before running the account connection check.', 'alynt-account-gateway' ),
			),
			'reoon_check_inactive'             => array(
				'type'    => 'error',
				'message' => __( 'Reoon responded, but the saved API account is not active. Review the account and API key in Reoon.', 'alynt-account-gateway' ),
			),
			'reoon_check_request_failed'       => array(
				'type'    => 'error',
				'message' => __( 'The site could not reach Reoon. Check outbound HTTPS, DNS, firewall rules, provider availability, and API-key permissions.', 'alynt-account-gateway' ),
			),
			'reoon_check_invalid_response'     => array(
				'type'    => 'error',
				'message' => __( 'Reoon returned an unexpected account response. Retry later and confirm the saved API key in Reoon.', 'alynt-account-gateway' ),
			),
		);
	}
}
