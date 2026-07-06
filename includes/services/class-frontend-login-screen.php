<?php
/**
 * Frontend login screen helper.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the frontend login screen.
 */
class ALYNT_AG_Frontend_Login_Screen {

	/**
	 * Auth service.
	 *
	 * @var ALYNT_AG_Auth_Service
	 */
	private $auth;

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
	 * Constructor.
	 *
	 * @param ALYNT_AG_Auth_Service|null        $auth       Auth service.
	 * @param ALYNT_AG_Frontend_Components|null $components Component helpers.
	 * @param ALYNT_AG_Frontend_Routes|null     $routes     Route helpers.
	 */
	public function __construct( $auth = null, $components = null, $routes = null ) {
		$this->auth       = $auth ? $auth : new ALYNT_AG_Auth_Service();
		$this->components = $components ? $components : new ALYNT_AG_Frontend_Components();
		$this->routes     = $routes ? $routes : new ALYNT_AG_Frontend_Routes();
	}

	/**
	 * Render login screen.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	public function render_login_screen( $settings ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only status display.
		$registration_complete = ! empty( $_GET['registration_complete'] );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only status display.
		$password_reset = ! empty( $_GET['password_reset'] );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only error display.
		$error_code = isset( $_GET['login_error'] ) ? sanitize_key( wp_unslash( $_GET['login_error'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Optional redirect target for a login attempt.
		$redirect_to = isset( $_GET['redirect_to'] ) ? esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ) : '';
		?>
		<h1 id="agw-screen-title" class="agw-title"><?php esc_html_e( 'Log In', 'alynt-account-gateway' ); ?></h1>
		<?php $this->components->render_notice( $settings['login_intro_text'] ); ?>
		<?php if ( $registration_complete ) : ?>
			<div class="agw-status agw-status--success" role="status" aria-live="polite">
				<?php esc_html_e( 'Your account has been created. You can log in now.', 'alynt-account-gateway' ); ?>
			</div>
		<?php endif; ?>
		<?php if ( $password_reset ) : ?>
			<div class="agw-status agw-status--success" role="status" aria-live="polite">
				<?php esc_html_e( 'Your password has been updated. You can log in now.', 'alynt-account-gateway' ); ?>
			</div>
		<?php endif; ?>
		<?php if ( $error_code ) : ?>
			<div id="agw-login-error" class="agw-status agw-status--error" role="alert"><?php echo esc_html( $this->auth->get_login_error_message( $error_code ) ); ?></div>
		<?php endif; ?>
		<form class="agw-form" method="post" action="<?php echo esc_url( home_url( $settings['login_path'] ) ); ?>" <?php echo $error_code ? 'aria-describedby="agw-login-error"' : ''; ?>>
			<input type="hidden" name="alynt_ag_action" value="login">
			<?php wp_nonce_field( 'alynt_ag_login', 'alynt_ag_auth_nonce' ); ?>
			<?php if ( $redirect_to ) : ?>
				<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>">
			<?php endif; ?>
			<div class="agw-field">
				<label for="agw-login-email"><?php esc_html_e( 'Email Address', 'alynt-account-gateway' ); ?></label>
				<input id="agw-login-email" name="email" type="email" autocomplete="email" dir="ltr" required <?php echo $error_code ? 'aria-invalid="true" aria-describedby="agw-login-error"' : ''; ?>>
			</div>
			<div class="agw-field agw-field--password">
				<label for="agw-login-password"><?php esc_html_e( 'Password', 'alynt-account-gateway' ); ?></label>
				<div class="agw-password">
					<input id="agw-login-password" name="pwd" type="password" autocomplete="current-password" required <?php echo $error_code ? 'aria-invalid="true" aria-describedby="agw-login-error"' : ''; ?>>
					<button type="button" class="agw-password__toggle" data-agw-password-toggle aria-controls="agw-login-password" aria-label="<?php esc_attr_e( 'Show password', 'alynt-account-gateway' ); ?>" aria-pressed="false"><?php esc_html_e( 'Show', 'alynt-account-gateway' ); ?></button>
				</div>
			</div>
			<label class="agw-checkbox">
				<input name="rememberme" type="checkbox" value="forever">
				<span><?php esc_html_e( 'Remember Me', 'alynt-account-gateway' ); ?></span>
			</label>
			<button class="agw-button agw-button--primary" type="submit"><?php esc_html_e( 'Log In', 'alynt-account-gateway' ); ?></button>
			<div class="agw-links">
				<a href="<?php echo esc_url( $this->routes->action_url( 'register', $settings ) ); ?>"><?php esc_html_e( 'Create Account', 'alynt-account-gateway' ); ?></a>
				<a href="<?php echo esc_url( $this->routes->action_url( 'lostpassword', $settings ) ); ?>"><?php esc_html_e( 'Forgot Password?', 'alynt-account-gateway' ); ?></a>
			</div>
		</form>
		<?php
	}
}
