<?php
/**
 * WordPress email template filters.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Applies branded templates to WordPress account emails.
 */
class ALYNT_AG_Email_WordPress_Filters extends ALYNT_AG_Service_Collaborator {

	/**
	 * Token provider.
	 *
	 * @var ALYNT_AG_Email_Tokens
	 */
	private $tokens;

	/**
	 * Whether the current profile email-change request should be suppressed.
	 *
	 * @var bool
	 */
	private $suppress_profile_email_change_request = false;

	/**
	 * Constructor.
	 *
	 * @param object                $service Public service facade.
	 * @param ALYNT_AG_Email_Tokens $tokens  Token provider.
	 */
	public function __construct( $service, $tokens ) {
		parent::__construct( $service );
		$this->tokens = $tokens;
	}

	/**
	 * Replace the native password-reset notification email.
	 *
	 * @param array<string,mixed> $email      Email data.
	 * @param string              $key        Password reset key.
	 * @param string              $user_login User login.
	 * @param WP_User             $user_data  User object.
	 * @return array<string,mixed>
	 */
	public function filter_retrieve_password_notification_email( $email, $key, $user_login, $user_data ) {
		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$tokens   = $this->tokens->for_user(
			$user_data,
			array(
				'reset_url' => $this->service->build_reset_url( $key, $user_login, $settings ),
			)
		);
		$rendered = $this->service->render( 'password_reset', $tokens, $settings );

		if ( is_wp_error( $rendered ) ) {
			return $email;
		}

		$email['subject'] = $rendered['subject'];
		$email['message'] = $rendered['html'];
		$email['headers'] = $this->service->html_headers();

		return $email;
	}

	/**
	 * Replace the native password-reset title fallback.
	 *
	 * @param string  $title      Email title.
	 * @param string  $user_login User login.
	 * @param WP_User $user_data  User object.
	 * @return string
	 */
	public function filter_retrieve_password_title( $title, $user_login, $user_data ) {
		unset( $user_login );
		$rendered = $this->service->render( 'password_reset', $this->tokens->for_user( $user_data ), ALYNT_AG_Settings_Schema::get_settings() );

		return is_wp_error( $rendered ) ? $title : $rendered['subject'];
	}

	/**
	 * Replace the native password-reset message fallback.
	 *
	 * @param string  $message    Email message.
	 * @param string  $key        Password reset key.
	 * @param string  $user_login User login.
	 * @param WP_User $user_data  User object.
	 * @return string
	 */
	public function filter_retrieve_password_message( $message, $key, $user_login, $user_data ) {
		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$tokens   = $this->tokens->for_user(
			$user_data,
			array(
				'reset_url' => $this->service->build_reset_url( $key, $user_login, $settings ),
			)
		);
		$rendered = $this->service->render( 'password_reset', $tokens, $settings );

		return is_wp_error( $rendered ) ? $message : $rendered['html'];
	}

	/**
	 * Optionally suppress the native password-changed email.
	 *
	 * @param bool                $send     Whether to send.
	 * @param WP_User             $user     User object.
	 * @param array<string,mixed> $userdata Updated user data.
	 * @return bool
	 */
	public function filter_send_password_change_email( $send, $user, $userdata ) {
		unset( $user, $userdata );
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		return empty( $settings['email_password_changed_disabled'] ) ? $send : false;
	}

	/**
	 * Replace the native password-changed email.
	 *
	 * @param array<string,mixed> $email    Email data.
	 * @param WP_User             $user     User object.
	 * @param array<string,mixed> $userdata Updated user data.
	 * @return array<string,mixed>
	 */
	public function filter_password_change_email( $email, $user, $userdata ) {
		unset( $userdata );
		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$rendered = $this->service->render( 'password_changed', $this->tokens->for_user( $user ), $settings );

		if ( is_wp_error( $rendered ) ) {
			return $email;
		}

		$email['subject'] = $rendered['subject'];
		$email['message'] = $rendered['html'];
		$email['headers'] = $this->service->html_headers();

		return $email;
	}

	/**
	 * Optionally suppress the native email-change notification.
	 *
	 * @param bool                $send     Whether to send.
	 * @param WP_User             $user     User object.
	 * @param array<string,mixed> $userdata Updated user data.
	 * @return bool
	 */
	public function filter_send_email_change_email( $send, $user, $userdata ) {
		unset( $user, $userdata );
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		return empty( $settings['email_change_confirmation_disabled'] ) ? $send : false;
	}

	/**
	 * Replace the native email-change notification.
	 *
	 * @param array<string,mixed> $email    Email data.
	 * @param WP_User             $user     User object.
	 * @param array<string,mixed> $userdata Updated user data.
	 * @return array<string,mixed>
	 */
	public function filter_email_change_email( $email, $user, $userdata ) {
		unset( $userdata );
		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$rendered = $this->service->render( 'email_change_confirmation', $this->tokens->for_user( $user ), $settings );

		if ( is_wp_error( $rendered ) ) {
			return $email;
		}

		$email['subject'] = $rendered['subject'];
		$email['message'] = $rendered['html'];
		$email['headers'] = $this->service->html_headers();

		return $email;
	}

	/**
	 * Replace the pending profile email-change confirmation body.
	 *
	 * @param string              $content        Email content.
	 * @param array<string,mixed> $new_user_email Pending email data.
	 * @return string
	 */
	public function filter_new_user_email_content( $content, $new_user_email ) {
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		if ( ! empty( $settings['email_change_confirmation_disabled'] ) ) {
			$this->suppress_profile_email_change_request = true;
			return $content;
		}

		$user   = function_exists( 'wp_get_current_user' ) ? wp_get_current_user() : null;
		$tokens = is_object( $user ) ? $this->tokens->for_user( $user ) : array();

		$tokens['user_email']       = $new_user_email['newemail'] ?? ( $tokens['user_email'] ?? '' );
		$tokens['change_email_url'] = '###ADMIN_URL###';
		$rendered                   = $this->service->render( 'email_change_confirmation', $tokens, $settings );

		return is_wp_error( $rendered ) ? $content : $rendered['plain'];
	}

	/**
	 * Suppress a configured pending profile email-change request.
	 *
	 * @param null|bool           $pre  Short-circuit value.
	 * @param array<string,mixed> $atts Mail arguments.
	 * @return null|bool
	 */
	public function filter_pre_wp_mail_for_profile_email_change( $pre, $atts ) {
		if ( null !== $pre || ! $this->suppress_profile_email_change_request ) {
			return $pre;
		}

		$this->suppress_profile_email_change_request = false;
		$settings                                    = ALYNT_AG_Settings_Schema::get_settings();
		if ( empty( $settings['email_change_confirmation_disabled'] ) ) {
			return $pre;
		}

		$this->delete_pending_profile_email_change( $atts );

		return false;
	}

	/**
	 * Remove the pending email-change marker created before core sends mail.
	 *
	 * @param array<string,mixed> $atts Mail arguments.
	 * @return void
	 */
	private function delete_pending_profile_email_change( $atts ) {
		if ( ! function_exists( 'wp_get_current_user' ) || ! function_exists( 'delete_user_meta' ) ) {
			return;
		}

		$current_user = wp_get_current_user();
		if ( ! is_object( $current_user ) || empty( $current_user->ID ) ) {
			return;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Mirrors core profile update context after WordPress nonce validation.
		$posted_user_id = isset( $_POST['user_id'] ) ? absint( wp_unslash( $_POST['user_id'] ) ) : 0;
		$posted_email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Missing
		$mail_to = isset( $atts['to'] ) ? sanitize_email( is_array( $atts['to'] ) ? reset( $atts['to'] ) : $atts['to'] ) : '';

		if ( (int) $current_user->ID !== $posted_user_id || '' === $posted_email || $posted_email !== $mail_to ) {
			return;
		}

		delete_user_meta( (int) $current_user->ID, '_new_email' );
	}
}
