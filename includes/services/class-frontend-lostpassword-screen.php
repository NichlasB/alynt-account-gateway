<?php
/**
 * Frontend lost-password screen helper.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the frontend lost-password screen.
 */
class ALYNT_AG_Frontend_Lostpassword_Screen {

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
	 * Render lost password screen.
	 *
	 * @param array<string,mixed> $settings          Settings.
	 * @param string              $forced_error_code Optional forced error code.
	 * @return void
	 */
	public function render_lostpassword_screen( $settings, $forced_error_code = '' ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only status display.
		$reset_sent = ! empty( $_GET['reset_sent'] );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only error display.
		$error_code = $forced_error_code ? sanitize_key( $forced_error_code ) : ( isset( $_GET['reset_error'] ) ? sanitize_key( wp_unslash( $_GET['reset_error'] ) ) : '' );
		$notice_id  = $this->components->has_notice( $settings['lostpassword_intro_text'] ) ? 'agw-lostpassword-instructions' : '';
		$form_desc  = array_filter( array( $notice_id, $error_code ? 'agw-lostpassword-error' : '' ) );

		if ( $reset_sent ) {
			?>
			<h1 id="agw-screen-title" class="agw-title"><?php esc_html_e( 'Check Your Email', 'alynt-account-gateway' ); ?></h1>
			<div id="agw-lostpassword-sent" class="agw-status agw-status--success" role="status" aria-live="polite" aria-atomic="true">
				<?php echo esc_html( $this->auth->get_lostpassword_sent_message() ); ?>
			</div>
			<a class="agw-back-link" href="<?php echo esc_url( home_url( $settings['login_path'] ) ); ?>"><?php esc_html_e( 'Back to Login', 'alynt-account-gateway' ); ?></a>
			<?php
			return;
		}
		?>
		<h1 id="agw-screen-title" class="agw-title"><?php esc_html_e( 'Reset Password', 'alynt-account-gateway' ); ?></h1>
		<?php $this->components->render_notice( $settings['lostpassword_intro_text'], $notice_id ); ?>
		<?php if ( $error_code ) : ?>
			<div id="agw-lostpassword-error" class="agw-status agw-status--error" role="alert" aria-live="assertive" aria-atomic="true"><?php echo esc_html( $this->auth->get_lostpassword_error_message( $error_code ) ); ?></div>
		<?php endif; ?>
		<form class="agw-form" method="post" action="<?php echo esc_url( $this->routes->action_url( 'lostpassword', $settings ) ); ?>" data-agw-retain-fields<?php echo $this->components->describedby_attribute( $form_desc ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by describedby_attribute(). ?>>
			<input type="hidden" name="alynt_ag_action" value="lostpassword">
			<?php wp_nonce_field( 'alynt_ag_lostpassword', 'alynt_ag_auth_nonce' ); ?>
			<div class="agw-field">
				<label for="agw-lost-email"><?php esc_html_e( 'Email Address', 'alynt-account-gateway' ); ?></label>
				<input id="agw-lost-email" name="user_login" type="email" autocomplete="email" dir="ltr" required data-agw-retain <?php echo $error_code ? 'aria-invalid="true" aria-describedby="agw-lostpassword-error"' : ''; ?>>
			</div>
			<button class="agw-button agw-button--primary" type="submit"><?php esc_html_e( 'Reset Password', 'alynt-account-gateway' ); ?></button>
			<a class="agw-back-link" href="<?php echo esc_url( home_url( $settings['login_path'] ) ); ?>"><?php esc_html_e( 'Back to Login', 'alynt-account-gateway' ); ?></a>
		</form>
		<?php
	}
}
