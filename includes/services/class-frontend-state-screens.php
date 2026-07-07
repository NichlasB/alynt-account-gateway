<?php
/**
 * Frontend auth state screen helpers.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders low-interaction frontend auth state screens.
 */
class ALYNT_AG_Frontend_State_Screens {

	/**
	 * Shared component helpers.
	 *
	 * @var ALYNT_AG_Frontend_Components
	 */
	private $components;

	/**
	 * Route helpers.
	 *
	 * @var ALYNT_AG_Frontend_Routes
	 */
	private $routes;

	/**
	 * Message helpers.
	 *
	 * @var ALYNT_AG_Frontend_Messages
	 */
	private $messages;

	/**
	 * Constructor.
	 *
	 * @param ALYNT_AG_Frontend_Components|null $components Component helpers.
	 * @param ALYNT_AG_Frontend_Routes|null     $routes     Route helpers.
	 * @param ALYNT_AG_Frontend_Messages|null   $messages   Message helpers.
	 */
	public function __construct( $components = null, $routes = null, $messages = null ) {
		$this->components = $components ? $components : new ALYNT_AG_Frontend_Components();
		$this->routes     = $routes ? $routes : new ALYNT_AG_Frontend_Routes();
		$this->messages   = $messages ? $messages : new ALYNT_AG_Frontend_Messages();
	}

	/**
	 * Render registration disabled screen.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	public function render_registration_disabled_screen( $settings ) {
		?>
		<h1 id="agw-screen-title" class="agw-title"><?php esc_html_e( 'Registration Unavailable', 'alynt-account-gateway' ); ?></h1>
		<?php $this->components->render_notice( $settings['registration_disabled_text'] ); ?>
		<a class="agw-button agw-button--primary" href="<?php echo esc_url( home_url( $settings['login_path'] ) ); ?>"><?php esc_html_e( 'Back to Login', 'alynt-account-gateway' ); ?></a>
		<?php
	}

	/**
	 * Render invalid link screen.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	public function render_invalid_link_screen( $settings ) {
		$resend_action = $this->routes->action_url( 'invalidlink', $settings );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only status display.
		$confirmation_resent = ! empty( $_GET['confirmation_resent'] );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only error display.
		$error_code      = isset( $_GET['resend_error'] ) ? sanitize_key( wp_unslash( $_GET['resend_error'] ) ) : '';
		$is_rate_limited = 'alynt_ag_rate_limited' === $error_code;
		$describedby     = $error_code ? 'agw-resend-error' : '';
		if ( $is_rate_limited ) {
			$describedby = trim( $describedby . ' agw-resend-guidance' );
		}
		?>
		<h1 id="agw-screen-title" class="agw-title"><?php esc_html_e( 'Link Expired', 'alynt-account-gateway' ); ?></h1>
		<?php $this->components->render_notice( $settings['invalid_link_text'] ); ?>
		<?php if ( $confirmation_resent ) : ?>
			<div class="agw-status agw-status--success" role="status" aria-live="polite">
				<?php esc_html_e( 'If a pending registration can be found, a new confirmation email has been sent.', 'alynt-account-gateway' ); ?>
			</div>
		<?php endif; ?>
		<?php if ( $error_code ) : ?>
			<div id="agw-resend-error" class="agw-status agw-status--error" role="alert"><?php echo esc_html( $this->messages->resend_error( $error_code ) ); ?></div>
		<?php endif; ?>
		<?php if ( $is_rate_limited ) : ?>
			<?php $this->render_resend_throttle_guidance( $settings ); ?>
		<?php endif; ?>
		<form class="agw-form" method="post" action="<?php echo esc_url( $resend_action ); ?>" <?php echo $describedby ? 'aria-describedby="' . esc_attr( $describedby ) . '"' : ''; ?>>
			<input type="hidden" name="alynt_ag_action" value="resend_confirmation">
			<?php wp_nonce_field( 'alynt_ag_resend_confirmation', 'alynt_ag_registration_nonce' ); ?>
			<div class="agw-field">
				<label for="agw-invalid-email"><?php esc_html_e( 'Email Address', 'alynt-account-gateway' ); ?></label>
				<input id="agw-invalid-email" name="email" type="email" autocomplete="email" dir="ltr" required <?php echo $error_code ? 'aria-invalid="true" aria-describedby="' . esc_attr( $describedby ) . '"' : ''; ?>>
			</div>
			<button class="agw-button agw-button--primary" type="submit"><?php esc_html_e( 'Send New Link', 'alynt-account-gateway' ); ?></button>
			<a class="agw-back-link" href="<?php echo esc_url( home_url( $settings['login_path'] ) ); ?>"><?php esc_html_e( 'Back to Login', 'alynt-account-gateway' ); ?></a>
		</form>
		<?php
	}

	/**
	 * Render resend cooldown guidance.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_resend_throttle_guidance( $settings ) {
		$window_mins = isset( $settings['resend_confirmation_rate_limit_window'] ) ? max( 1, absint( $settings['resend_confirmation_rate_limit_window'] ) ) : 60;
		?>
		<div id="agw-resend-guidance" class="agw-resend-guidance" aria-labelledby="agw-resend-guidance-title">
			<h2 id="agw-resend-guidance-title"><?php esc_html_e( 'Before requesting another link', 'alynt-account-gateway' ); ?></h2>
			<ul>
				<li>
					<?php
					printf(
						/* translators: %d: configured resend cooldown window in minutes. */
						esc_html__( 'Wait %d minutes before requesting another confirmation email.', 'alynt-account-gateway' ),
						(int) $window_mins
					);
					?>
				</li>
				<li><?php esc_html_e( 'Use the newest confirmation email only. Older links may stop working after a resend.', 'alynt-account-gateway' ); ?></li>
				<li><?php esc_html_e( 'Check spam, promotions, and filtered inbox folders if the email is not in your inbox.', 'alynt-account-gateway' ); ?></li>
			</ul>
		</div>
		<?php
	}
}
