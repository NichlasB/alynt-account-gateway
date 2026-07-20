<?php
/**
 * Email template service.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coordinates branded account emails.
 */
class ALYNT_AG_Email_Template_Service {

	/**
	 * Focused collaborators.
	 *
	 * @var array<string,object>
	 */
	private $collaborators;

	/**
	 * Constructor.
	 *
	 * @param array<string,object> $collaborators Optional test collaborators.
	 */
	public function __construct( $collaborators = array() ) {
		$tokens = $collaborators['tokens'] ?? new ALYNT_AG_Email_Tokens( $this );

		$this->collaborators = array(
			'tokens'   => $tokens,
			'renderer' => $collaborators['renderer'] ?? new ALYNT_AG_Email_Renderer( $this, $tokens ),
			'sender'   => $collaborators['sender'] ?? new ALYNT_AG_Email_Sender( $this ),
			'filters'  => $collaborators['filters'] ?? new ALYNT_AG_Email_WordPress_Filters( $this, $tokens ),
		);
	}

	/**
	 * Register WordPress email filters.
	 *
	 * @return void
	 */
	public function register() {
		add_filter( 'retrieve_password_notification_email', array( $this, 'filter_retrieve_password_notification_email' ), 10, 4 );
		add_filter( 'retrieve_password_title', array( $this, 'filter_retrieve_password_title' ), 10, 3 );
		add_filter( 'retrieve_password_message', array( $this, 'filter_retrieve_password_message' ), 10, 4 );
		add_filter( 'send_password_change_email', array( $this, 'filter_send_password_change_email' ), 10, 3 );
		add_filter( 'password_change_email', array( $this, 'filter_password_change_email' ), 10, 3 );
		add_filter( 'send_email_change_email', array( $this, 'filter_send_email_change_email' ), 10, 3 );
		add_filter( 'email_change_email', array( $this, 'filter_email_change_email' ), 10, 3 );
		add_filter( 'new_user_email_content', array( $this, 'filter_new_user_email_content' ), 10, 2 );
		add_filter( 'pre_wp_mail', array( $this, 'filter_pre_wp_mail_for_profile_email_change' ), 10, 2 );
	}

	/**
	 * Return supported templates.
	 *
	 * @return array<string,string>
	 */
	public function templates() {
		return $this->collaborators['tokens']->templates();
	}

	/**
	 * Return preview tokens.
	 *
	 * @return array<string,string>
	 */
	public function preview_tokens() {
		return $this->collaborators['tokens']->preview_tokens();
	}

	/**
	 * Return documented template tokens.
	 *
	 * @return array<string,array<string,string>>
	 */
	public function token_reference() {
		return $this->collaborators['tokens']->token_reference();
	}

	/**
	 * Render a branded email template.
	 *
	 * @param string              $template Template key.
	 * @param array<string,mixed> $tokens   Token values.
	 * @param array<string,mixed> $settings Settings.
	 * @return array<string,string>|WP_Error
	 */
	public function render( $template, $tokens, $settings ) {
		return $this->collaborators['renderer']->render( $template, $tokens, $settings );
	}

	/**
	 * Send a branded email template.
	 *
	 * @param string              $template Template key.
	 * @param string              $to       Recipient email.
	 * @param array<string,mixed> $tokens   Token values.
	 * @param array<string,mixed> $settings Settings.
	 * @return true|WP_Error
	 */
	public function send( $template, $to, $tokens, $settings ) {
		return $this->collaborators['sender']->send( $template, $to, $tokens, $settings );
	}

	/**
	 * Filter the password-reset notification.
	 *
	 * @param array<string,mixed> $email Email data.
	 * @param string              $key Reset key.
	 * @param string              $user_login User login.
	 * @param WP_User             $user_data User object.
	 * @return array<string,mixed>
	 */
	public function filter_retrieve_password_notification_email( $email, $key, $user_login, $user_data ) {
		return $this->collaborators['filters']->filter_retrieve_password_notification_email( $email, $key, $user_login, $user_data );
	}

	/**
	 * Filter the password-reset title.
	 *
	 * @param string  $title Title.
	 * @param string  $user_login User login.
	 * @param WP_User $user_data User object.
	 * @return string
	 */
	public function filter_retrieve_password_title( $title, $user_login, $user_data ) {
		return $this->collaborators['filters']->filter_retrieve_password_title( $title, $user_login, $user_data );
	}

	/**
	 * Filter the password-reset message.
	 *
	 * @param string  $message Message.
	 * @param string  $key Reset key.
	 * @param string  $user_login User login.
	 * @param WP_User $user_data User object.
	 * @return string
	 */
	public function filter_retrieve_password_message( $message, $key, $user_login, $user_data ) {
		return $this->collaborators['filters']->filter_retrieve_password_message( $message, $key, $user_login, $user_data );
	}

	/**
	 * Filter password-change delivery.
	 *
	 * @param bool                $send Whether to send.
	 * @param WP_User             $user User object.
	 * @param array<string,mixed> $userdata User data.
	 * @return bool
	 */
	public function filter_send_password_change_email( $send, $user, $userdata ) {
		return $this->collaborators['filters']->filter_send_password_change_email( $send, $user, $userdata );
	}

	/**
	 * Filter the password-changed email.
	 *
	 * @param array<string,mixed> $email Email data.
	 * @param WP_User             $user User object.
	 * @param array<string,mixed> $userdata User data.
	 * @return array<string,mixed>
	 */
	public function filter_password_change_email( $email, $user, $userdata ) {
		return $this->collaborators['filters']->filter_password_change_email( $email, $user, $userdata );
	}

	/**
	 * Filter email-change delivery.
	 *
	 * @param bool                $send Whether to send.
	 * @param WP_User             $user User object.
	 * @param array<string,mixed> $userdata User data.
	 * @return bool
	 */
	public function filter_send_email_change_email( $send, $user, $userdata ) {
		return $this->collaborators['filters']->filter_send_email_change_email( $send, $user, $userdata );
	}

	/**
	 * Filter the email-change email.
	 *
	 * @param array<string,mixed> $email Email data.
	 * @param WP_User             $user User object.
	 * @param array<string,mixed> $userdata User data.
	 * @return array<string,mixed>
	 */
	public function filter_email_change_email( $email, $user, $userdata ) {
		return $this->collaborators['filters']->filter_email_change_email( $email, $user, $userdata );
	}

	/**
	 * Filter the pending profile email-change body.
	 *
	 * @param string              $content Email content.
	 * @param array<string,mixed> $new_user_email Pending email data.
	 * @return string
	 */
	public function filter_new_user_email_content( $content, $new_user_email ) {
		return $this->collaborators['filters']->filter_new_user_email_content( $content, $new_user_email );
	}

	/**
	 * Filter pending profile email-change delivery.
	 *
	 * @param null|bool           $pre Short-circuit value.
	 * @param array<string,mixed> $atts Mail arguments.
	 * @return null|bool
	 */
	public function filter_pre_wp_mail_for_profile_email_change( $pre, $atts ) {
		return $this->collaborators['filters']->filter_pre_wp_mail_for_profile_email_change( $pre, $atts );
	}

	/**
	 * Replace known template tokens.
	 *
	 * @param string              $content Content.
	 * @param array<string,mixed> $tokens  Token values.
	 * @return string
	 */
	public function replace_tokens( $content, $tokens ) {
		return $this->collaborators['renderer']->replace_tokens( $content, $tokens );
	}

	/**
	 * Build a branded reset URL.
	 *
	 * @param string              $key        Password reset key.
	 * @param string              $user_login User login.
	 * @param array<string,mixed> $settings   Settings.
	 * @return string
	 */
	public function build_reset_url( $key, $user_login, $settings ) {
		return $this->collaborators['tokens']->build_reset_url( $key, $user_login, $settings );
	}

	/**
	 * Return standard HTML mail headers.
	 *
	 * @return array<int,string>
	 */
	public function html_headers() {
		return $this->collaborators['tokens']->html_headers();
	}
}
