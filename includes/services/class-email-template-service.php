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
 * Renders branded account emails.
 */
class ALYNT_AG_Email_Template_Service {

	/**
	 * Whether the current request should suppress the pending profile email change request.
	 *
	 * @var bool
	 */
	private $suppress_profile_email_change_request = false;

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
	 * Return supported template keys.
	 *
	 * @return array<string,string>
	 */
	public function templates() {
		return array(
			'registration_confirmation' => __( 'Registration Confirmation', 'alynt-account-gateway' ),
			'password_reset'            => __( 'Password Reset', 'alynt-account-gateway' ),
			'password_changed'          => __( 'Password Changed', 'alynt-account-gateway' ),
			'new_user_welcome'          => __( 'Account Created Welcome', 'alynt-account-gateway' ),
			'email_change_confirmation' => __( 'Email Change Confirmation', 'alynt-account-gateway' ),
		);
	}

	/**
	 * Return default preview tokens.
	 *
	 * @return array<string,string>
	 */
	public function preview_tokens() {
		return array(
			'first_name'       => __( 'Damon', 'alynt-account-gateway' ),
			'last_name'        => __( 'Paulo', 'alynt-account-gateway' ),
			'user_email'       => 'customer@example.com',
			'site_name'        => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
			'confirmation_url' => home_url( '/account?action=setpassword&alynt_ag_token=sample-token' ),
			'reset_url'        => home_url( '/account?action=setpassword&key=sample-key&login=customer%40example.com' ),
			'change_email_url' => home_url( '/account?action=confirm-email-change&key=sample-key' ),
			'dashboard_url'    => home_url( '/my-account/' ),
			'expiry_hours'     => '24',
		);
	}

	/**
	 * Render a template into subject, HTML, and plain text.
	 *
	 * @param string              $template Template key.
	 * @param array<string,mixed> $tokens   Token values.
	 * @param array<string,mixed> $settings Settings.
	 * @return array<string,string>|WP_Error
	 */
	public function render( $template, $tokens, $settings ) {
		if ( ! isset( $this->templates()[ $template ] ) ) {
			return new WP_Error( 'alynt_ag_unknown_email_template', __( 'Unknown email template.', 'alynt-account-gateway' ) );
		}

		$tokens    = $this->normalize_tokens( $tokens );
		$button    = $this->get_button_for_template( $template, $tokens );
		$prefix    = $this->get_settings_prefix( $template );
		$subject   = $this->replace_tokens( $settings[ "{$prefix}_subject" ] ?? '', $tokens );
		$preheader = $this->replace_tokens( $settings[ "{$prefix}_preheader" ] ?? '', $tokens );
		$body      = $this->replace_tokens( $settings[ "{$prefix}_body" ] ?? '', $tokens );

		return array(
			'subject'   => $subject,
			'preheader' => $preheader,
			'html'      => $this->render_html( $template, $subject, $preheader, $body, $button, $settings ),
			'plain'     => $this->render_plain( $body, $button ),
		);
	}

	/**
	 * Send a rendered template.
	 *
	 * @param string              $template Template key.
	 * @param string              $to       Recipient email.
	 * @param array<string,mixed> $tokens   Token values.
	 * @param array<string,mixed> $settings Settings.
	 * @return true|WP_Error
	 */
	public function send( $template, $to, $tokens, $settings ) {
		$to = sanitize_email( $to );
		if ( ! is_email( $to ) ) {
			return new WP_Error( 'alynt_ag_invalid_email_recipient', __( 'The email recipient is invalid.', 'alynt-account-gateway' ) );
		}

		$rendered = $this->render( $template, $tokens, $settings );
		if ( is_wp_error( $rendered ) ) {
			return $rendered;
		}

		$sent = wp_mail(
			$to,
			$rendered['subject'],
			$rendered['html'],
			array( 'Content-Type: text/html; charset=UTF-8' )
		);

		if ( ! $sent ) {
			return new WP_Error( 'alynt_ag_email_send_failed', __( 'The email could not be sent. Please try again.', 'alynt-account-gateway' ) );
		}

		return true;
	}

	/**
	 * Replace the native password-reset notification email.
	 *
	 * @param array<string,mixed> $email     Email data.
	 * @param string              $key       Password reset key.
	 * @param string              $user_login User login.
	 * @param WP_User             $user_data User object.
	 * @return array<string,mixed>
	 */
	public function filter_retrieve_password_notification_email( $email, $key, $user_login, $user_data ) {
		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$tokens   = $this->user_tokens(
			$user_data,
			array(
				'reset_url' => $this->build_reset_url( $key, $user_login, $settings ),
			)
		);
		$rendered = $this->render( 'password_reset', $tokens, $settings );

		if ( is_wp_error( $rendered ) ) {
			return $email;
		}

		$email['subject'] = $rendered['subject'];
		$email['message'] = $rendered['html'];
		$email['headers'] = $this->html_headers();

		return $email;
	}

	/**
	 * Replace the native password-reset title fallback.
	 *
	 * @param string  $title     Email title.
	 * @param string  $user_login User login.
	 * @param WP_User $user_data User object.
	 * @return string
	 */
	public function filter_retrieve_password_title( $title, $user_login, $user_data ) {
		$rendered = $this->render( 'password_reset', $this->user_tokens( $user_data ), ALYNT_AG_Settings_Schema::get_settings() );

		return is_wp_error( $rendered ) ? $title : $rendered['subject'];
	}

	/**
	 * Replace the native password-reset message fallback.
	 *
	 * @param string  $message   Email message.
	 * @param string  $key       Password reset key.
	 * @param string  $user_login User login.
	 * @param WP_User $user_data User object.
	 * @return string
	 */
	public function filter_retrieve_password_message( $message, $key, $user_login, $user_data ) {
		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$tokens   = $this->user_tokens(
			$user_data,
			array(
				'reset_url' => $this->build_reset_url( $key, $user_login, $settings ),
			)
		);
		$rendered = $this->render( 'password_reset', $tokens, $settings );

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
		$rendered = $this->render( 'password_changed', $this->user_tokens( $user ), $settings );

		if ( is_wp_error( $rendered ) ) {
			return $email;
		}

		$email['subject'] = $rendered['subject'];
		$email['message'] = $rendered['html'];
		$email['headers'] = $this->html_headers();

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
		$rendered = $this->render( 'email_change_confirmation', $this->user_tokens( $user ), $settings );

		if ( is_wp_error( $rendered ) ) {
			return $email;
		}

		$email['subject'] = $rendered['subject'];
		$email['message'] = $rendered['html'];
		$email['headers'] = $this->html_headers();

		return $email;
	}

	/**
	 * Replace the pending profile email-change confirmation body.
	 *
	 * WordPress exposes only the body for this core email, then replaces the
	 * ###ADMIN_URL### and related placeholders after the filter runs.
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
		$tokens = is_object( $user ) ? $this->user_tokens( $user ) : array();

		$tokens['user_email']       = $new_user_email['newemail'] ?? ( $tokens['user_email'] ?? '' );
		$tokens['change_email_url'] = '###ADMIN_URL###';

		$rendered = $this->render( 'email_change_confirmation', $tokens, $settings );

		return is_wp_error( $rendered ) ? $content : $rendered['plain'];
	}

	/**
	 * Suppress the pending profile email-change request when configured.
	 *
	 * WordPress exposes only the pending profile email-change body through
	 * `new_user_email_content`. The actual sender is a direct wp_mail() call, so
	 * the disable toggle must short-circuit that specific mail after the body
	 * filter marks the current request.
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

		$settings = ALYNT_AG_Settings_Schema::get_settings();
		if ( empty( $settings['email_change_confirmation_disabled'] ) ) {
			return $pre;
		}

		$this->delete_pending_profile_email_change( $atts );

		return false;
	}

	/**
	 * Replace known template tokens.
	 *
	 * @param string              $content Content.
	 * @param array<string,mixed> $tokens  Token values.
	 * @return string
	 */
	public function replace_tokens( $content, $tokens ) {
		$normalized = $this->normalize_tokens( $tokens );
		$replace    = array();

		foreach ( $normalized as $key => $value ) {
			$replace[ '{{' . $key . '}}' ] = $value;
		}

		return strtr( (string) $content, $replace );
	}

	/**
	 * Remove the pending email-change marker created immediately before core sends mail.
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

	/**
	 * Build a branded reset URL.
	 *
	 * @param string              $key        Password reset key.
	 * @param string              $user_login User login.
	 * @param array<string,mixed> $settings   Settings.
	 * @return string
	 */
	public function build_reset_url( $key, $user_login, $settings ) {
		return add_query_arg(
			array(
				'action' => 'setpassword',
				'key'    => rawurlencode( $key ),
				'login'  => rawurlencode( $user_login ),
			),
			home_url( $settings['account_action_base'] )
		);
	}

	/**
	 * Return standard HTML mail headers.
	 *
	 * @return array<int,string>
	 */
	public function html_headers() {
		return array( 'Content-Type: text/html; charset=UTF-8' );
	}

	/**
	 * Return the settings prefix for a template key.
	 *
	 * @param string $template Template key.
	 * @return string
	 */
	private function get_settings_prefix( $template ) {
		if ( 'email_change_confirmation' === $template ) {
			return 'email_change_confirmation';
		}

		if ( 'new_user_welcome' === $template ) {
			return 'email_new_user_welcome';
		}

		return 'email_' . $template;
	}

	/**
	 * Normalize tokens and provide common defaults.
	 *
	 * @param array<string,mixed> $tokens Token values.
	 * @return array<string,string>
	 */
	private function normalize_tokens( $tokens ) {
		$defaults = $this->preview_tokens();
		$tokens   = array_merge( $defaults, (array) $tokens );

		foreach ( $tokens as $key => $value ) {
			$tokens[ $key ] = (string) $value;
		}

		return $tokens;
	}

	/**
	 * Build common tokens from a WordPress user object.
	 *
	 * @param WP_User             $user  User object.
	 * @param array<string,mixed> $extra Extra tokens.
	 * @return array<string,string>
	 */
	private function user_tokens( $user, $extra = array() ) {
		$user_id    = isset( $user->ID ) ? (int) $user->ID : 0;
		$first_name = $user_id ? get_user_meta( $user_id, 'first_name', true ) : '';
		$last_name  = $user_id ? get_user_meta( $user_id, 'last_name', true ) : '';
		$email      = isset( $user->user_email ) ? $user->user_email : '';

		return array_merge(
			array(
				'first_name' => $first_name ? $first_name : __( 'there', 'alynt-account-gateway' ),
				'last_name'  => $last_name,
				'user_email' => $email,
			),
			$extra
		);
	}

	/**
	 * Return button metadata for a template.
	 *
	 * @param string              $template Template key.
	 * @param array<string,mixed> $tokens   Token values.
	 * @return array<string,string>
	 */
	private function get_button_for_template( $template, $tokens ) {
		$map = array(
			'registration_confirmation' => array(
				'label' => __( 'Confirm Account', 'alynt-account-gateway' ),
				'url'   => $tokens['confirmation_url'] ?? '',
			),
			'password_reset'            => array(
				'label' => __( 'Reset Password', 'alynt-account-gateway' ),
				'url'   => $tokens['reset_url'] ?? '',
			),
			'email_change_confirmation' => array(
				'label' => __( 'Confirm Email Address', 'alynt-account-gateway' ),
				'url'   => $tokens['change_email_url'] ?? '',
			),
			'new_user_welcome'          => array(
				'label' => __( 'View Account', 'alynt-account-gateway' ),
				'url'   => $tokens['dashboard_url'] ?? '',
			),
		);

		return $map[ $template ] ?? array(
			'label' => '',
			'url'   => '',
		);
	}

	/**
	 * Render branded HTML.
	 *
	 * @param string              $template  Template key.
	 * @param string              $subject   Subject.
	 * @param string              $preheader Preheader.
	 * @param string              $body      Body.
	 * @param array<string,mixed> $button    Button metadata.
	 * @param array<string,mixed> $settings  Settings.
	 * @return string
	 */
	private function render_html( $template, $subject, $preheader, $body, $button, $settings ) {
		$site_name    = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$logo_url     = ! empty( $settings['brand_logo_id'] ) ? wp_get_attachment_image_url( (int) $settings['brand_logo_id'], 'full' ) : '';
		$primary      = $settings['button_background_color'] ?? '#3B5249';
		$button_text  = $settings['button_text_color'] ?? '#ffffff';
		$text_color   = $settings['text_color'] ?? '#281408';
		$background   = $settings['page_background_color'] ?? '#EAE4D6';
		$surface      = $settings['surface_color'] ?? '#FFFFFF';
		$body_html    = wpautop( esc_html( $body ) );
		$button_url   = ! empty( $button['url'] ) ? esc_url( $button['url'] ) : '';
		$button_label = ! empty( $button['label'] ) ? $button['label'] : '';

		ob_start();
		?>
		<!doctype html>
		<html>
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title><?php echo esc_html( $subject ); ?></title>
		</head>
		<body style="margin:0;padding:0;background:<?php echo esc_attr( $background ); ?>;color:<?php echo esc_attr( $text_color ); ?>;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">
			<div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;"><?php echo esc_html( $preheader ); ?></div>
			<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:<?php echo esc_attr( $background ); ?>;padding:32px 16px;">
				<tr>
					<td align="center">
						<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:<?php echo esc_attr( $surface ); ?>;border-radius:8px;overflow:hidden;">
							<tr>
								<td style="padding:32px 32px 16px;text-align:center;">
									<?php if ( $logo_url ) : ?>
										<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $site_name ); ?>" style="max-width:220px;height:auto;">
									<?php else : ?>
										<div style="font-family:Georgia,serif;font-size:24px;font-weight:600;"><?php echo esc_html( $site_name ); ?></div>
									<?php endif; ?>
								</td>
							</tr>
							<tr>
								<td style="padding:16px 32px 8px;">
									<h1 style="margin:0 0 16px;font-family:Georgia,serif;font-size:26px;line-height:1.25;color:<?php echo esc_attr( $text_color ); ?>;"><?php echo esc_html( $subject ); ?></h1>
									<div style="font-size:16px;line-height:1.6;color:<?php echo esc_attr( $text_color ); ?>;"><?php echo wp_kses_post( $body_html ); ?></div>
									<?php if ( $button_url && $button_label ) : ?>
										<p style="margin:28px 0;">
											<a href="<?php echo esc_url( $button_url ); ?>" style="display:inline-block;padding:14px 22px;border-radius:6px;background:<?php echo esc_attr( $primary ); ?>;color:<?php echo esc_attr( $button_text ); ?>;font-weight:600;text-decoration:none;"><?php echo esc_html( $button_label ); ?></a>
										</p>
										<p style="font-size:13px;line-height:1.5;color:<?php echo esc_attr( $text_color ); ?>;opacity:.78;"><?php echo esc_html( $button_url ); ?></p>
									<?php endif; ?>
								</td>
							</tr>
							<tr>
								<td style="padding:16px 32px 32px;font-size:12px;line-height:1.5;color:<?php echo esc_attr( $text_color ); ?>;opacity:.72;">
									<?php
									echo esc_html(
										sprintf(
											/* translators: %s: site name. */
											__( 'This email was sent by %s.', 'alynt-account-gateway' ),
											$site_name
										)
									);
									?>
									<span style="display:none;"><?php echo esc_html( $template ); ?></span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</body>
		</html>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Render plain text fallback content.
	 *
	 * @param string              $body   Body content.
	 * @param array<string,mixed> $button Button metadata.
	 * @return string
	 */
	private function render_plain( $body, $button ) {
		$plain = trim( wp_strip_all_tags( $body ) );

		if ( ! empty( $button['url'] ) ) {
			$plain .= "\n\n" . $button['url'];
		}

		return $plain;
	}
}
