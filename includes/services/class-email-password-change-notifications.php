<?php
/**
 * Password-change notification suppression.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Keeps the password-changed disable setting aligned with native WordPress mail.
 */
class ALYNT_AG_Email_Password_Change_Notifications {

	/**
	 * Register suppression hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'init', array( $this, 'maybe_suppress_admin_notification' ), 0 );
		add_filter( 'pre_wp_mail', array( $this, 'filter_pre_wp_mail' ), 9, 2 );
	}

	/**
	 * Remove WordPress's native admin password-change notification when disabled.
	 *
	 * @return void
	 */
	public function maybe_suppress_admin_notification() {
		if ( ! $this->password_changed_email_disabled() || ! function_exists( 'remove_action' ) ) {
			return;
		}

		remove_action( 'after_password_reset', 'wp_password_change_notification' );
	}

	/**
	 * Safety-net suppression for the native admin password-change email.
	 *
	 * @param null|bool           $pre  Short-circuit value.
	 * @param array<string,mixed> $atts Mail arguments.
	 * @return null|bool
	 */
	public function filter_pre_wp_mail( $pre, $atts ) {
		if ( null !== $pre || ! $this->password_changed_email_disabled() ) {
			return $pre;
		}

		return $this->is_native_admin_password_changed_email( $atts ) ? false : $pre;
	}

	/**
	 * Determine whether the disable setting is enabled.
	 *
	 * @return bool
	 */
	private function password_changed_email_disabled() {
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		return ! empty( $settings['email_password_changed_disabled'] );
	}

	/**
	 * Detect WordPress's native admin password-change notification.
	 *
	 * @param array<string,mixed> $atts Mail arguments.
	 * @return bool
	 */
	private function is_native_admin_password_changed_email( $atts ) {
		$subject = isset( $atts['subject'] ) ? (string) $atts['subject'] : '';
		$message = isset( $atts['message'] ) ? (string) $atts['message'] : '';

		return false !== strpos( $subject, 'Password Changed' )
			&& 0 === strpos( $message, 'Password changed for user:' );
	}
}
