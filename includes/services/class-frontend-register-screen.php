<?php
/**
 * Frontend registration screen helper.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the frontend registration screen.
 */
class ALYNT_AG_Frontend_Register_Screen {

	/**
	 * Shared component helpers.
	 *
	 * @var ALYNT_AG_Frontend_Components
	 */
	private $components;

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
	 * @param ALYNT_AG_Frontend_Messages|null   $messages   Message helpers.
	 */
	public function __construct( $components = null, $messages = null ) {
		$this->components = $components ? $components : new ALYNT_AG_Frontend_Components();
		$this->messages   = $messages ? $messages : new ALYNT_AG_Frontend_Messages();
	}

	/**
	 * Render registration screen.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	public function render_register_screen( $settings ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only status display.
		$registration_sent = ! empty( $_GET['registration_sent'] );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only error display.
		$error_code = isset( $_GET['registration_error'] ) ? sanitize_key( wp_unslash( $_GET['registration_error'] ) ) : '';
		$notice_id  = $this->components->has_notice( $settings['register_intro_text'] ) ? 'agw-register-instructions' : '';
		$form_desc  = array_filter( array( $notice_id, $error_code ? 'agw-register-error' : '' ) );

		if ( $registration_sent ) {
			?>
			<h1 id="agw-screen-title" class="agw-title"><?php esc_html_e( 'Check Your Email', 'alynt-account-gateway' ); ?></h1>
			<div id="agw-registration-sent" class="agw-status agw-status--success" role="status" aria-live="polite" aria-atomic="true">
				<?php esc_html_e( 'If the details can be used, a confirmation email has been sent. Please check your inbox and spam folder.', 'alynt-account-gateway' ); ?>
			</div>
			<a class="agw-back-link" href="<?php echo esc_url( home_url( $settings['login_path'] ) ); ?>"><?php esc_html_e( 'Back to Login', 'alynt-account-gateway' ); ?></a>
			<?php
			return;
		}
		?>
		<h1 id="agw-screen-title" class="agw-title"><?php esc_html_e( 'Create Account', 'alynt-account-gateway' ); ?></h1>
		<?php $this->components->render_notice( $settings['register_intro_text'], $notice_id ); ?>
		<?php if ( $error_code ) : ?>
			<div id="agw-register-error" class="agw-status agw-status--error" role="alert" aria-live="assertive" aria-atomic="true"><?php echo esc_html( $this->messages->registration_error( $error_code ) ); ?></div>
		<?php endif; ?>
		<form class="agw-form" method="post" action="<?php echo esc_url( home_url( $settings['account_action_base'] ) ); ?>" data-agw-registration-form<?php echo $this->components->describedby_attribute( $form_desc ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by describedby_attribute(). ?>>
			<input type="hidden" name="alynt_ag_action" value="start_registration">
			<?php wp_nonce_field( 'alynt_ag_start_registration', 'alynt_ag_registration_nonce' ); ?>
			<div class="agw-grid agw-grid--two">
				<div class="agw-field">
					<label for="agw-register-first"><?php esc_html_e( 'First Name', 'alynt-account-gateway' ); ?></label>
					<input id="agw-register-first" name="first_name" type="text" autocomplete="given-name" required data-agw-registration-required <?php echo in_array( $error_code, array( 'missing_required_fields' ), true ) ? 'aria-invalid="true" aria-describedby="agw-register-error"' : ''; ?>>
				</div>
				<div class="agw-field">
					<label for="agw-register-last"><?php esc_html_e( 'Last Name', 'alynt-account-gateway' ); ?></label>
					<input id="agw-register-last" name="last_name" type="text" autocomplete="family-name" required data-agw-registration-required <?php echo in_array( $error_code, array( 'missing_required_fields' ), true ) ? 'aria-invalid="true" aria-describedby="agw-register-error"' : ''; ?>>
				</div>
			</div>
			<div class="agw-field">
				<label for="agw-register-email"><?php esc_html_e( 'Email Address', 'alynt-account-gateway' ); ?></label>
				<input id="agw-register-email" name="email" type="email" autocomplete="email" dir="ltr" required data-agw-registration-required <?php echo in_array( $error_code, array( 'missing_required_fields', 'invalid_email', 'email_unavailable' ), true ) ? 'aria-invalid="true" aria-describedby="agw-register-error"' : ''; ?>>
			</div>
			<label class="agw-checkbox">
				<input id="agw-register-terms" name="terms" type="checkbox" required data-agw-registration-terms <?php echo 'terms_required' === $error_code ? 'aria-invalid="true" aria-describedby="agw-register-error"' : ''; ?>>
				<span>
					<?php esc_html_e( 'By creating an account, you agree to our', 'alynt-account-gateway' ); ?>
					<a href="<?php echo esc_url( home_url( $settings['terms_path'] ) ); ?>"><?php esc_html_e( 'Terms', 'alynt-account-gateway' ); ?></a>
					<?php esc_html_e( 'and', 'alynt-account-gateway' ); ?>
					<a href="<?php echo esc_url( home_url( $settings['privacy_path'] ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'alynt-account-gateway' ); ?></a>
				</span>
			</label>
			<?php $this->components->render_verification_slot( $settings ); ?>
			<button class="agw-button agw-button--primary" type="submit" data-agw-registration-submit disabled aria-disabled="true"><?php esc_html_e( 'Create Account', 'alynt-account-gateway' ); ?></button>
			<a class="agw-back-link" href="<?php echo esc_url( home_url( $settings['login_path'] ) ); ?>"><?php esc_html_e( 'Back to Login', 'alynt-account-gateway' ); ?></a>
		</form>
		<?php
	}
}
