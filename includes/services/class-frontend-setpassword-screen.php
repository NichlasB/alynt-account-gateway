<?php
/**
 * Frontend set-password screen helper.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the frontend set-password screen.
 */
class ALYNT_AG_Frontend_Setpassword_Screen {

	/**
	 * Auth service.
	 *
	 * @var ALYNT_AG_Auth_Service
	 */
	private $auth;

	/**
	 * Registration service.
	 *
	 * @var ALYNT_AG_Registration_Service
	 */
	private $registration;

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
	 * Lost-password screen helper.
	 *
	 * @var ALYNT_AG_Frontend_Lostpassword_Screen
	 */
	private $lostpassword_screen;

	/**
	 * Auth state screen helpers.
	 *
	 * @var ALYNT_AG_Frontend_State_Screens
	 */
	private $state_screens;

	/**
	 * Constructor.
	 *
	 * @param ALYNT_AG_Auth_Service|null                 $auth                Auth service.
	 * @param ALYNT_AG_Registration_Service|null         $registration        Registration service.
	 * @param ALYNT_AG_Frontend_Components|null          $components          Component helpers.
	 * @param ALYNT_AG_Frontend_Messages|null            $messages            Message helpers.
	 * @param ALYNT_AG_Frontend_Lostpassword_Screen|null $lostpassword_screen Lost-password screen helper.
	 * @param ALYNT_AG_Frontend_State_Screens|null       $state_screens       State screen helpers.
	 */
	public function __construct( $auth = null, $registration = null, $components = null, $messages = null, $lostpassword_screen = null, $state_screens = null ) {
		$this->auth                = $auth ? $auth : new ALYNT_AG_Auth_Service();
		$this->registration        = $registration ? $registration : new ALYNT_AG_Registration_Service();
		$this->components          = $components ? $components : new ALYNT_AG_Frontend_Components();
		$this->messages            = $messages ? $messages : new ALYNT_AG_Frontend_Messages();
		$this->lostpassword_screen = $lostpassword_screen ? $lostpassword_screen : new ALYNT_AG_Frontend_Lostpassword_Screen();
		$this->state_screens       = $state_screens ? $state_screens : new ALYNT_AG_Frontend_State_Screens();
	}

	/**
	 * Render set-password screen.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	public function render_setpassword_screen( $settings ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Token is validated against plugin-owned pending registration storage.
		$token = isset( $_GET['alynt_ag_token'] ) ? sanitize_text_field( wp_unslash( $_GET['alynt_ag_token'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Native reset key is validated by WordPress.
		$key = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Native reset login is validated by WordPress.
		$login = isset( $_GET['login'] ) ? sanitize_user( wp_unslash( $_GET['login'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only error display.
		$error_code = isset( $_GET['password_error'] ) ? sanitize_key( wp_unslash( $_GET['password_error'] ) ) : '';

		if ( $token ) {
			if ( is_wp_error( $this->registration->confirm_pending_token( $token ) ) ) {
				$this->state_screens->render_invalid_link_screen( $settings );
				return;
			}

			$set_password_action = add_query_arg(
				array(
					'action'         => 'setpassword',
					'alynt_ag_token' => rawurlencode( $token ),
				),
				home_url( $settings['account_action_base'] )
			);

			$this->render_password_form(
				$settings,
				$set_password_action,
				'complete_registration',
				'alynt_ag_complete_registration',
				'alynt_ag_registration_nonce',
				array(
					'alynt_ag_token' => $token,
				),
				$error_code
			);
			return;
		}

		if ( $key && $login ) {
			if ( is_wp_error( $this->auth->validate_password_reset_key( $key, $login ) ) ) {
				$this->lostpassword_screen->render_lostpassword_screen( $settings, 'invalid_or_expired_token' );
				return;
			}

			$set_password_action = add_query_arg(
				array(
					'action' => 'setpassword',
					'key'    => rawurlencode( $key ),
					'login'  => rawurlencode( $login ),
				),
				home_url( $settings['account_action_base'] )
			);

			$this->render_password_form(
				$settings,
				$set_password_action,
				'reset_password',
				'alynt_ag_reset_password',
				'alynt_ag_auth_nonce',
				array(
					'key'   => $key,
					'login' => $login,
				),
				$error_code
			);
			return;
		}

		$this->state_screens->render_invalid_link_screen( $settings );
	}

	/**
	 * Render the shared set-password form.
	 *
	 * @param array<string,mixed>  $settings     Settings.
	 * @param string               $action_url   Form action URL.
	 * @param string               $action       Auth action.
	 * @param string               $nonce_action Nonce action.
	 * @param string               $nonce_name   Nonce field name.
	 * @param array<string,string> $hidden       Hidden form fields.
	 * @param string               $error_code   Error code.
	 * @return void
	 */
	public function render_password_form( $settings, $action_url, $action, $nonce_action, $nonce_name, $hidden, $error_code ) {
		?>
		<h1 id="agw-screen-title" class="agw-title"><?php esc_html_e( 'Set New Password', 'alynt-account-gateway' ); ?></h1>
		<?php $this->components->render_notice( $settings['setpassword_intro_text'] ); ?>
		<?php if ( $error_code ) : ?>
			<div id="agw-password-error" class="agw-status agw-status--error" role="alert"><?php echo esc_html( $this->messages->password_error( $error_code ) ); ?></div>
		<?php endif; ?>
		<form class="agw-form" method="post" action="<?php echo esc_url( $action_url ); ?>" data-agw-password-form <?php echo $error_code ? 'aria-describedby="agw-password-error"' : ''; ?>>
			<input type="hidden" name="alynt_ag_action" value="<?php echo esc_attr( $action ); ?>">
			<?php wp_nonce_field( $nonce_action, $nonce_name ); ?>
			<?php foreach ( $hidden as $name => $value ) : ?>
				<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
			<?php endforeach; ?>
			<div class="agw-field agw-field--password">
				<label for="agw-set-password"><?php esc_html_e( 'New Password', 'alynt-account-gateway' ); ?></label>
				<input id="agw-set-password" name="password" type="password" autocomplete="new-password" aria-describedby="<?php echo esc_attr( $error_code ? 'agw-password-error agw-password-status agw-password-requirements' : 'agw-password-status agw-password-requirements' ); ?>" <?php echo $error_code ? 'aria-invalid="true"' : ''; ?> data-agw-password-input required>
			</div>
			<div class="agw-field agw-field--password">
				<label for="agw-set-confirm"><?php esc_html_e( 'Confirm Password', 'alynt-account-gateway' ); ?></label>
				<input id="agw-set-confirm" name="password_confirm" type="password" autocomplete="new-password" aria-describedby="<?php echo esc_attr( $error_code ? 'agw-password-error agw-password-status agw-password-requirements' : 'agw-password-status agw-password-requirements' ); ?>" <?php echo $error_code ? 'aria-invalid="true"' : ''; ?> data-agw-password-confirm required>
			</div>
			<div class="agw-strength" data-agw-strength data-agw-strength-score="0" data-agw-message-empty="<?php esc_attr_e( 'Enter a password to begin.', 'alynt-account-gateway' ); ?>" data-agw-message-weak="<?php esc_attr_e( 'Keep going.', 'alynt-account-gateway' ); ?>" data-agw-message-good="<?php esc_attr_e( 'Almost there.', 'alynt-account-gateway' ); ?>" data-agw-message-ready="<?php esc_attr_e( 'Password is ready.', 'alynt-account-gateway' ); ?>" aria-live="polite">
				<span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span>
				<strong id="agw-password-status" data-agw-strength-label><?php esc_html_e( 'Enter a password to begin.', 'alynt-account-gateway' ); ?></strong>
			</div>
			<ul id="agw-password-requirements" class="agw-requirements" data-agw-password-requirements>
				<li data-agw-requirement="length"><?php esc_html_e( 'At least 12 characters', 'alynt-account-gateway' ); ?></li>
				<li data-agw-requirement="uppercase"><?php esc_html_e( 'At least one uppercase letter', 'alynt-account-gateway' ); ?></li>
				<li data-agw-requirement="lowercase"><?php esc_html_e( 'At least one lowercase letter', 'alynt-account-gateway' ); ?></li>
				<li data-agw-requirement="number"><?php esc_html_e( 'At least one number', 'alynt-account-gateway' ); ?></li>
				<li data-agw-requirement="symbol"><?php esc_html_e( 'At least one special symbol', 'alynt-account-gateway' ); ?></li>
				<li data-agw-requirement="match"><?php esc_html_e( 'Passwords match', 'alynt-account-gateway' ); ?></li>
			</ul>
			<button class="agw-button agw-button--primary" type="submit" data-agw-password-submit disabled><?php esc_html_e( 'Save Password', 'alynt-account-gateway' ); ?></button>
		</form>
		<?php
	}
}
