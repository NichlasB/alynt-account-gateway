<?php
/**
 * Settings page messaging-actions component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused messaging-actions behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Messaging_Actions extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render an email template preview.
	 *
	 * @return void
	 */
	public function handle_preview_email() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to preview emails.', 'alynt-account-gateway' ) );
		}

		check_admin_referer( 'alynt_ag_preview_email' );

		$email_service = new ALYNT_AG_Email_Template_Service();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verified above.
		$template = isset( $_GET['template'] ) ? sanitize_key( wp_unslash( $_GET['template'] ) ) : 'registration_confirmation';
		$rendered = $email_service->render( $template, $email_service->preview_tokens(), ALYNT_AG_Settings_Schema::get_settings() );

		if ( is_wp_error( $rendered ) ) {
			wp_die( esc_html( $rendered->get_error_message() ) );
		}

		header( 'Content-Type: text/html; charset=utf-8' );
		echo $rendered['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by email renderer.
		exit;
	}

	/**
	 * Send a test email.
	 *
	 * @return void
	 */
	public function handle_test_email() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to send test emails.', 'alynt-account-gateway' ) );
		}

		check_admin_referer( 'alynt_ag_test_email' );

		$email_service = new ALYNT_AG_Email_Template_Service();
		$template      = isset( $_POST['template'] ) ? sanitize_key( wp_unslash( $_POST['template'] ) ) : 'registration_confirmation';
		$recipient     = isset( $_POST['recipient'] ) ? sanitize_email( wp_unslash( $_POST['recipient'] ) ) : '';
		$result        = $email_service->send( $template, $recipient, $email_service->preview_tokens(), ALYNT_AG_Settings_Schema::get_settings() );
		$status        = is_wp_error( $result ) ? 'email_test_failed' : 'email_test_sent';

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'            => 'alynt-account-gateway',
					'tab'             => 'emails',
					'alynt_ag_notice' => $status,
				),
				admin_url( 'options-general.php' )
			)
		);
		exit;
	}

	/**
	 * Send a test webhook.
	 *
	 * @return void
	 */
	public function handle_test_webhook() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to send test webhooks.', 'alynt-account-gateway' ) );
		}

		check_admin_referer( 'alynt_ag_test_webhook' );

		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$status   = 'webhook_test_failed';

		if ( empty( $settings['account_created_webhook'] ) ) {
			$status = 'webhook_test_missing';
		} else {
			$dispatcher = new ALYNT_AG_Webhook_Dispatcher();
			$result     = $dispatcher->dispatch_account_created_test( get_current_user_id(), $settings );
			$status     = is_wp_error( $result ) ? 'webhook_test_failed' : 'webhook_test_sent';
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'            => 'alynt-account-gateway',
					'tab'             => 'webhooks',
					'alynt_ag_notice' => $status,
				),
				admin_url( 'options-general.php' )
			)
		);
		exit;
	}

	/**
	 * Record settings changes in the audit log.
	 *
	 * @param array<string,mixed> $old_value Previous settings.
	 * @param array<string,mixed> $value     New settings.
	 * @return void
	 */
	public function log_settings_change( $old_value, $value ) {
		$changed_keys = array();

		foreach ( (array) $value as $key => $new_value ) {
			$old_setting = is_array( $old_value ) && array_key_exists( $key, $old_value ) ? $old_value[ $key ] : null;
			if ( $old_setting !== $new_value ) {
				$changed_keys[] = $key;
			}
		}

		if ( empty( $changed_keys ) ) {
			return;
		}

		ALYNT_AG_Diagnostics_Logger::log(
			'settings_changed',
			array( 'changed_keys' => $changed_keys ),
			get_current_user_id()
		);
	}
}
