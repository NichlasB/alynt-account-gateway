<?php
/**
 * Frontend gateway foundation.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers frontend hooks.
 */
class ALYNT_AG_Frontend {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'show_admin_bar', array( $this, 'filter_admin_bar' ) );
		add_action( 'admin_init', array( $this, 'maybe_block_wp_admin' ) );
		add_action( 'login_init', array( $this, 'maybe_redirect_native_login' ) );
		add_action( 'template_redirect', array( $this, 'maybe_render_gateway' ) );
		add_filter( 'login_url', array( $this, 'filter_login_url' ), 10, 3 );
		add_filter( 'lostpassword_url', array( $this, 'filter_lostpassword_url' ), 10, 2 );
		add_filter( 'register_url', array( $this, 'filter_register_url' ) );
		add_filter( 'logout_url', array( $this, 'filter_logout_url' ), 10, 2 );
	}

	/**
	 * Enqueue frontend assets only when frontend output is enabled.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$screen   = $this->get_gateway_screen( $settings );

		$this->assets()->enqueue( $settings, $screen );
	}

	/**
	 * Restrict admin toolbar to administrators and shop managers.
	 *
	 * @param bool $show Whether to show toolbar.
	 * @return bool
	 */
	public function filter_admin_bar( $show ) {
		if ( ! is_user_logged_in() ) {
			return $show;
		}

		// phpcs:ignore WordPress.WP.Capabilities.Unknown -- WooCommerce registers this capability for shop managers.
		return current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' );
	}

	/**
	 * Block wp-admin access for customers and other non-admin roles.
	 *
	 * @return void
	 */
	public function maybe_block_wp_admin() {
		if ( wp_doing_ajax() || ! is_user_logged_in() ) {
			return;
		}

		// phpcs:ignore WordPress.WP.Capabilities.Unknown -- WooCommerce registers this capability for shop managers.
		if ( current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$settings = ALYNT_AG_Settings_Schema::get_settings();
		wp_safe_redirect( home_url( $settings['after_login_redirect'] ) );
		exit;
	}

	/**
	 * Redirect native wp-login.php requests to branded routes unless the bypass key is present.
	 *
	 * @return void
	 */
	public function maybe_redirect_native_login() {
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		if ( empty( $settings['frontend_enabled'] ) || $this->is_emergency_bypass( $settings ) ) {
			return;
		}

		$request_method = isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '';
		if ( 'POST' === strtoupper( $request_method ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only action routing.
		$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : 'login';
		$url    = $this->get_url_for_action( $action, $settings );

		foreach ( array( 'key', 'login', 'redirect_to' ) as $param ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Preserving core login query arguments.
			if ( isset( $_GET[ $param ] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Preserving core login query arguments.
				$value = sanitize_text_field( wp_unslash( $_GET[ $param ] ) );
				$url   = add_query_arg( $param, rawurlencode( $value ), $url );
			}
		}

		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Render the branded gateway when the current request matches a configured route.
	 *
	 * @return void
	 */
	public function maybe_render_gateway() {
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		if ( empty( $settings['frontend_enabled'] ) ) {
			return;
		}

		$screen = $this->get_gateway_screen( $settings );
		if ( ! $screen ) {
			return;
		}

		if ( 'dashboard' === $screen && ! is_user_logged_in() ) {
			wp_safe_redirect( add_query_arg( 'redirect_to', rawurlencode( home_url( $settings['after_login_redirect'] ) ), home_url( $settings['login_path'] ) ) );
			exit;
		}

		if ( 'logout' === $screen && $this->maybe_handle_confirmed_logout( $settings ) ) {
			return;
		}

		status_header( 200 );
		nocache_headers();

		echo '<!doctype html>';
		echo '<html ';
		language_attributes();
		echo '>';
		echo '<head>';
		echo '<meta charset="' . esc_attr( get_bloginfo( 'charset' ) ) . '">';
		echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
		echo '<title>' . esc_html( $this->get_screen_title( $screen ) ) . '</title>';
		wp_head();
		echo '</head>';
		echo '<body class="alynt-ag-body">';
		if ( 'dashboard' === $screen ) {
			$this->render_dashboard_shell( $settings );
		} else {
			$this->render_gateway_shell( $screen, $settings );
		}
		wp_footer();
		echo '</body></html>';
		exit;
	}

	/**
	 * Render one gateway screen for admin preview.
	 *
	 * @param string              $screen   Screen key.
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	public function render_preview( $screen, $settings ) {
		$screen = in_array(
			$screen,
			array( 'dashboard', 'login', 'register', 'lostpassword', 'setpassword', 'logout', 'registration_disabled', 'invalidlink' ),
			true
		) ? $screen : 'login';

		if ( 'setpassword' === $screen ) {
			$this->render_gateway_shell_with_password_preview( $settings );
			return;
		}

		if ( 'dashboard' === $screen ) {
			$this->render_dashboard_shell( $settings );
			return;
		}

		$this->render_gateway_shell( $screen, $settings );
	}

	/**
	 * Filter the WordPress login URL.
	 *
	 * @param string $login_url    Native login URL.
	 * @param string $redirect     Redirect URL.
	 * @param bool   $force_reauth Whether to force reauthentication.
	 * @return string
	 */
	public function filter_login_url( $login_url, $redirect, $force_reauth ) {
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		if ( empty( $settings['frontend_enabled'] ) ) {
			return $login_url;
		}

		return $this->routes()->login_url( $settings, $redirect, $force_reauth );
	}

	/**
	 * Filter the lost password URL.
	 *
	 * @param string $lostpassword_url Native URL.
	 * @param string $redirect         Redirect URL.
	 * @return string
	 */
	public function filter_lostpassword_url( $lostpassword_url, $redirect ) {
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		if ( empty( $settings['frontend_enabled'] ) ) {
			return $lostpassword_url;
		}

		return $this->routes()->lostpassword_url( $settings, $redirect );
	}

	/**
	 * Filter the registration URL.
	 *
	 * @param string $register_url Native URL.
	 * @return string
	 */
	public function filter_register_url( $register_url ) {
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		return empty( $settings['frontend_enabled'] ) ? $register_url : $this->routes()->register_url( $settings );
	}

	/**
	 * Filter the logout URL.
	 *
	 * @param string $logout_url Native URL.
	 * @param string $redirect   Redirect URL.
	 * @return string
	 */
	public function filter_logout_url( $logout_url, $redirect ) {
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		if ( empty( $settings['frontend_enabled'] ) ) {
			return $logout_url;
		}

		return $this->routes()->logout_url( $settings, $redirect );
	}

	/**
	 * Determine whether the current request is the emergency native-login bypass.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return bool
	 */
	private function is_emergency_bypass( $settings ) {
		if ( empty( $settings['emergency_bypass_key'] ) ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Emergency bypass is a read-only routing check.
		$provided = isset( $_GET['alynt_ag_bypass'] ) ? sanitize_text_field( wp_unslash( $_GET['alynt_ag_bypass'] ) ) : '';

		return $provided && hash_equals( (string) $settings['emergency_bypass_key'], $provided );
	}

	/**
	 * Return the gateway screen for the current request.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	private function get_gateway_screen( $settings ) {
		return $this->routes()->screen( $settings );
	}

	/**
	 * Build a branded gateway URL for a login action.
	 *
	 * @param string              $action   Login action.
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	private function get_url_for_action( $action, $settings ) {
		return $this->routes()->action_url( $action, $settings );
	}

	/**
	 * Get current request path relative to home URL.
	 *
	 * @return string
	 */
	private function get_current_relative_path() {
		return $this->routes()->current_relative_path();
	}

	/**
	 * Compare relative paths without trailing slash sensitivity.
	 *
	 * @param string $left  First path.
	 * @param string $right Second path.
	 * @return bool
	 */
	private function paths_match( $left, $right ) {
		return $this->routes()->paths_match( $left, $right );
	}

	/**
	 * Frontend route helpers.
	 *
	 * @return ALYNT_AG_Frontend_Routes
	 */
	private function routes() {
		return new ALYNT_AG_Frontend_Routes();
	}

	/**
	 * Frontend asset helpers.
	 *
	 * @return ALYNT_AG_Frontend_Assets
	 */
	private function assets() {
		return new ALYNT_AG_Frontend_Assets();
	}

	/**
	 * Frontend branding helpers.
	 *
	 * @return ALYNT_AG_Frontend_Branding
	 */
	private function branding() {
		return new ALYNT_AG_Frontend_Branding();
	}

	/**
	 * Render gateway shell.
	 *
	 * @param string              $screen   Screen key.
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_gateway_shell( $screen, $settings ) {
		$style = $this->get_gateway_style_attribute( $settings );
		?>
		<main class="alynt-ag-gateway" data-agw-screen="<?php echo esc_attr( $screen ); ?>" style="<?php echo esc_attr( $style ); ?>">
			<section class="agw-shell" aria-labelledby="agw-screen-title">
				<div class="agw-media" aria-hidden="true">
					<?php $this->render_media_panel( $settings ); ?>
				</div>
				<div class="agw-panel">
					<div class="agw-card">
						<?php $this->render_brand_block( $settings ); ?>
						<?php $this->render_screen( $screen, $settings ); ?>
					</div>
				</div>
			</section>
		</main>
		<?php
	}

	/**
	 * Render the set-password shell for admin preview without requiring a live token.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_gateway_shell_with_password_preview( $settings ) {
		$style = $this->get_gateway_style_attribute( $settings );
		?>
		<main class="alynt-ag-gateway" data-agw-screen="setpassword" style="<?php echo esc_attr( $style ); ?>">
			<section class="agw-shell" aria-labelledby="agw-screen-title">
				<div class="agw-media" aria-hidden="true">
					<?php $this->render_media_panel( $settings ); ?>
				</div>
				<div class="agw-panel">
					<div class="agw-card">
						<?php $this->render_brand_block( $settings ); ?>
						<?php
						$this->render_password_form(
							$settings,
							home_url( $settings['account_action_base'] ),
							'reset_password',
							'alynt_ag_reset_password',
							'alynt_ag_auth_nonce',
							array(
								'key'   => 'preview-key',
								'login' => 'preview@example.test',
							),
							''
						);
						?>
					</div>
				</div>
			</section>
		</main>
		<?php
	}

	/**
	 * Render dashboard shell.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_dashboard_shell( $settings ) {
		$style = $this->get_gateway_style_attribute( $settings );
		?>
		<main class="alynt-ag-gateway agw-dashboard" data-agw-screen="dashboard" style="<?php echo esc_attr( $style ); ?>">
			<div class="agw-dashboard__inner">
				<header class="agw-dashboard__header">
					<?php $this->render_brand_block( $settings ); ?>
					<a class="agw-dashboard__logout" href="<?php echo esc_url( wp_logout_url( home_url( $settings['login_path'] ) ) ); ?>"><?php esc_html_e( 'Log Out', 'alynt-account-gateway' ); ?></a>
				</header>
				<?php $this->render_dashboard_screen( $settings ); ?>
			</div>
		</main>
		<?php
	}

	/**
	 * Return inline CSS custom properties for configured branding.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	private function get_gateway_style_attribute( $settings ) {
		return $this->branding()->style_attribute( $settings );
	}

	/**
	 * Render the media panel.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_media_panel( $settings ) {
		$this->branding()->render_media_panel( $settings );
	}

	/**
	 * Render logo or store name.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_brand_block( $settings ) {
		$this->branding()->render_brand_block( $settings );
	}

	/**
	 * Render one screen inside the gateway shell.
	 *
	 * @param string              $screen   Screen key.
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_screen( $screen, $settings ) {
		switch ( $screen ) {
			case 'register':
				$this->render_register_screen( $settings );
				break;
			case 'lostpassword':
				$this->render_lostpassword_screen( $settings );
				break;
			case 'setpassword':
				$this->render_setpassword_screen( $settings );
				break;
			case 'logout':
				$this->render_logout_screen( $settings );
				break;
			case 'registration_disabled':
				$this->render_registration_disabled_screen( $settings );
				break;
			case 'invalidlink':
				$this->render_invalid_link_screen( $settings );
				break;
			case 'login':
			default:
				$this->render_login_screen( $settings );
				break;
		}
	}

	/**
	 * Render login screen.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_login_screen( $settings ) {
		$auth = new ALYNT_AG_Auth_Service();
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
		<?php $this->render_notice( $settings['login_intro_text'] ); ?>
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
			<div id="agw-login-error" class="agw-status agw-status--error" role="alert"><?php echo esc_html( $auth->get_login_error_message( $error_code ) ); ?></div>
		<?php endif; ?>
		<form class="agw-form" method="post" action="<?php echo esc_url( home_url( $settings['login_path'] ) ); ?>" <?php echo $error_code ? 'aria-describedby="agw-login-error"' : ''; ?>>
			<input type="hidden" name="alynt_ag_action" value="login">
			<?php wp_nonce_field( 'alynt_ag_login', 'alynt_ag_auth_nonce' ); ?>
			<?php if ( $redirect_to ) : ?>
				<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>">
			<?php endif; ?>
			<div class="agw-field">
				<label for="agw-login-email"><?php esc_html_e( 'Email Address', 'alynt-account-gateway' ); ?></label>
				<input id="agw-login-email" name="email" type="email" autocomplete="email" required <?php echo $error_code ? 'aria-invalid="true" aria-describedby="agw-login-error"' : ''; ?>>
			</div>
			<div class="agw-field agw-field--password">
				<label for="agw-login-password"><?php esc_html_e( 'Password', 'alynt-account-gateway' ); ?></label>
				<div class="agw-password">
					<input id="agw-login-password" name="pwd" type="password" autocomplete="current-password" required <?php echo $error_code ? 'aria-invalid="true" aria-describedby="agw-login-error"' : ''; ?>>
					<button type="button" class="agw-password__toggle" data-agw-password-toggle aria-label="<?php esc_attr_e( 'Show password', 'alynt-account-gateway' ); ?>" aria-pressed="false"><?php esc_html_e( 'Show', 'alynt-account-gateway' ); ?></button>
				</div>
			</div>
			<label class="agw-checkbox">
				<input name="rememberme" type="checkbox" value="forever">
				<span><?php esc_html_e( 'Remember Me', 'alynt-account-gateway' ); ?></span>
			</label>
			<button class="agw-button agw-button--primary" type="submit"><?php esc_html_e( 'Log In', 'alynt-account-gateway' ); ?></button>
			<div class="agw-links">
				<a href="<?php echo esc_url( $this->get_url_for_action( 'register', $settings ) ); ?>"><?php esc_html_e( 'Create Account', 'alynt-account-gateway' ); ?></a>
				<a href="<?php echo esc_url( $this->get_url_for_action( 'lostpassword', $settings ) ); ?>"><?php esc_html_e( 'Forgot Password?', 'alynt-account-gateway' ); ?></a>
			</div>
		</form>
		<?php
	}

	/**
	 * Render registration screen.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_register_screen( $settings ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only status display.
		$registration_sent = ! empty( $_GET['registration_sent'] );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only error display.
		$error_code = isset( $_GET['registration_error'] ) ? sanitize_key( wp_unslash( $_GET['registration_error'] ) ) : '';

		if ( $registration_sent ) {
			?>
			<h1 id="agw-screen-title" class="agw-title"><?php esc_html_e( 'Check Your Email', 'alynt-account-gateway' ); ?></h1>
			<div class="agw-status agw-status--success" role="status" aria-live="polite">
				<?php esc_html_e( 'If the details can be used, a confirmation email has been sent. Please check your inbox and spam folder.', 'alynt-account-gateway' ); ?>
			</div>
			<a class="agw-back-link" href="<?php echo esc_url( home_url( $settings['login_path'] ) ); ?>"><?php esc_html_e( 'Back to Login', 'alynt-account-gateway' ); ?></a>
			<?php
			return;
		}
		?>
		<h1 id="agw-screen-title" class="agw-title"><?php esc_html_e( 'Create Account', 'alynt-account-gateway' ); ?></h1>
		<?php $this->render_notice( $settings['register_intro_text'] ); ?>
		<?php if ( $error_code ) : ?>
			<div id="agw-register-error" class="agw-status agw-status--error" role="alert"><?php echo esc_html( $this->get_registration_error_message( $error_code ) ); ?></div>
		<?php endif; ?>
		<form class="agw-form" method="post" action="<?php echo esc_url( home_url( $settings['account_action_base'] ) ); ?>" data-agw-registration-form <?php echo $error_code ? 'aria-describedby="agw-register-error"' : ''; ?>>
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
				<input id="agw-register-email" name="email" type="email" autocomplete="email" required data-agw-registration-required <?php echo in_array( $error_code, array( 'missing_required_fields', 'invalid_email', 'email_unavailable' ), true ) ? 'aria-invalid="true" aria-describedby="agw-register-error"' : ''; ?>>
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
			<?php $this->render_verification_slot( $settings ); ?>
			<button class="agw-button agw-button--primary" type="submit" data-agw-registration-submit disabled aria-disabled="true"><?php esc_html_e( 'Create Account', 'alynt-account-gateway' ); ?></button>
			<a class="agw-back-link" href="<?php echo esc_url( home_url( $settings['login_path'] ) ); ?>"><?php esc_html_e( 'Back to Login', 'alynt-account-gateway' ); ?></a>
		</form>
		<?php
	}

	/**
	 * Render lost password screen.
	 *
	 * @param array<string,mixed> $settings          Settings.
	 * @param string              $forced_error_code Optional forced error code.
	 * @return void
	 */
	private function render_lostpassword_screen( $settings, $forced_error_code = '' ) {
		$auth = new ALYNT_AG_Auth_Service();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only status display.
		$reset_sent = ! empty( $_GET['reset_sent'] );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only error display.
		$error_code = $forced_error_code ? sanitize_key( $forced_error_code ) : ( isset( $_GET['reset_error'] ) ? sanitize_key( wp_unslash( $_GET['reset_error'] ) ) : '' );

		if ( $reset_sent ) {
			?>
			<h1 id="agw-screen-title" class="agw-title"><?php esc_html_e( 'Check Your Email', 'alynt-account-gateway' ); ?></h1>
			<div class="agw-status agw-status--success" role="status" aria-live="polite">
				<?php echo esc_html( $auth->get_lostpassword_sent_message() ); ?>
			</div>
			<a class="agw-back-link" href="<?php echo esc_url( home_url( $settings['login_path'] ) ); ?>"><?php esc_html_e( 'Back to Login', 'alynt-account-gateway' ); ?></a>
			<?php
			return;
		}
		?>
		<h1 id="agw-screen-title" class="agw-title"><?php esc_html_e( 'Reset Password', 'alynt-account-gateway' ); ?></h1>
		<?php $this->render_notice( $settings['lostpassword_intro_text'] ); ?>
		<?php if ( $error_code ) : ?>
			<div id="agw-lostpassword-error" class="agw-status agw-status--error" role="alert"><?php echo esc_html( $auth->get_lostpassword_error_message( $error_code ) ); ?></div>
		<?php endif; ?>
		<form class="agw-form" method="post" action="<?php echo esc_url( $this->get_url_for_action( 'lostpassword', $settings ) ); ?>" <?php echo $error_code ? 'aria-describedby="agw-lostpassword-error"' : ''; ?>>
			<input type="hidden" name="alynt_ag_action" value="lostpassword">
			<?php wp_nonce_field( 'alynt_ag_lostpassword', 'alynt_ag_auth_nonce' ); ?>
			<div class="agw-field">
				<label for="agw-lost-email"><?php esc_html_e( 'Email Address', 'alynt-account-gateway' ); ?></label>
				<input id="agw-lost-email" name="user_login" type="email" autocomplete="email" required <?php echo $error_code ? 'aria-invalid="true" aria-describedby="agw-lostpassword-error"' : ''; ?>>
			</div>
			<button class="agw-button agw-button--primary" type="submit"><?php esc_html_e( 'Reset Password', 'alynt-account-gateway' ); ?></button>
			<a class="agw-back-link" href="<?php echo esc_url( home_url( $settings['login_path'] ) ); ?>"><?php esc_html_e( 'Back to Login', 'alynt-account-gateway' ); ?></a>
		</form>
		<?php
	}

	/**
	 * Render set password screen.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_setpassword_screen( $settings ) {
		$auth         = new ALYNT_AG_Auth_Service();
		$registration = new ALYNT_AG_Registration_Service();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Token is validated against plugin-owned pending registration storage.
		$token = isset( $_GET['alynt_ag_token'] ) ? sanitize_text_field( wp_unslash( $_GET['alynt_ag_token'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Native reset key is validated by WordPress.
		$key = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Native reset login is validated by WordPress.
		$login = isset( $_GET['login'] ) ? sanitize_user( wp_unslash( $_GET['login'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only error display.
		$error_code = isset( $_GET['password_error'] ) ? sanitize_key( wp_unslash( $_GET['password_error'] ) ) : '';

		if ( $token ) {
			if ( is_wp_error( $registration->confirm_pending_token( $token ) ) ) {
				$this->render_invalid_link_screen( $settings );
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
			if ( is_wp_error( $auth->validate_password_reset_key( $key, $login ) ) ) {
				$this->render_lostpassword_screen( $settings, 'invalid_or_expired_token' );
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

		$this->render_invalid_link_screen( $settings );
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
	private function render_password_form( $settings, $action_url, $action, $nonce_action, $nonce_name, $hidden, $error_code ) {
		?>
		<h1 id="agw-screen-title" class="agw-title"><?php esc_html_e( 'Set New Password', 'alynt-account-gateway' ); ?></h1>
		<?php $this->render_notice( $settings['setpassword_intro_text'] ); ?>
		<?php if ( $error_code ) : ?>
			<div id="agw-password-error" class="agw-status agw-status--error" role="alert"><?php echo esc_html( $this->get_password_error_message( $error_code ) ); ?></div>
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

	/**
	 * Render logout confirmation.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_logout_screen( $settings ) {
		$logout_url = wp_nonce_url(
			add_query_arg(
				array(
					'action'  => 'logout',
					'confirm' => '1',
				),
				home_url( $settings['account_action_base'] )
			),
			'log-out'
		);
		?>
		<h1 id="agw-screen-title" class="agw-title"><?php esc_html_e( 'Log Out', 'alynt-account-gateway' ); ?></h1>
		<?php $this->render_notice( $settings['logout_intro_text'] ); ?>
		<div class="agw-actions">
			<a class="agw-button agw-button--primary" href="<?php echo esc_url( $logout_url ); ?>"><?php esc_html_e( 'Log Out', 'alynt-account-gateway' ); ?></a>
			<a class="agw-button agw-button--secondary" href="<?php echo esc_url( home_url( $settings['after_login_redirect'] ) ); ?>"><?php esc_html_e( 'Cancel', 'alynt-account-gateway' ); ?></a>
		</div>
		<?php
	}

	/**
	 * Handle confirmed logout requests from the branded logout screen.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return bool
	 */
	private function maybe_handle_confirmed_logout( $settings ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Presence check before nonce verification.
		if ( empty( $_GET['confirm'] ) ) {
			return false;
		}

		check_admin_referer( 'log-out' );
		wp_logout();

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Optional redirect target after verified logout.
		$redirect_to = isset( $_GET['redirect_to'] ) ? esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ) : home_url( $settings['login_path'] );

		wp_safe_redirect( $redirect_to );
		exit;
	}

	/**
	 * Render registration disabled screen.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_registration_disabled_screen( $settings ) {
		?>
		<h1 id="agw-screen-title" class="agw-title"><?php esc_html_e( 'Registration Unavailable', 'alynt-account-gateway' ); ?></h1>
		<?php $this->render_notice( $settings['registration_disabled_text'] ); ?>
		<a class="agw-button agw-button--primary" href="<?php echo esc_url( home_url( $settings['login_path'] ) ); ?>"><?php esc_html_e( 'Back to Login', 'alynt-account-gateway' ); ?></a>
		<?php
	}

	/**
	 * Render invalid link screen.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_invalid_link_screen( $settings ) {
		$resend_action = $this->get_url_for_action( 'invalidlink', $settings );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only status display.
		$confirmation_resent = ! empty( $_GET['confirmation_resent'] );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only error display.
		$error_code = isset( $_GET['resend_error'] ) ? sanitize_key( wp_unslash( $_GET['resend_error'] ) ) : '';
		?>
		<h1 id="agw-screen-title" class="agw-title"><?php esc_html_e( 'Link Expired', 'alynt-account-gateway' ); ?></h1>
		<?php $this->render_notice( $settings['invalid_link_text'] ); ?>
		<?php if ( $confirmation_resent ) : ?>
			<div class="agw-status agw-status--success" role="status" aria-live="polite">
				<?php esc_html_e( 'If a pending registration can be found, a new confirmation email has been sent.', 'alynt-account-gateway' ); ?>
			</div>
		<?php endif; ?>
		<?php if ( $error_code ) : ?>
			<div id="agw-resend-error" class="agw-status agw-status--error" role="alert"><?php echo esc_html( $this->get_resend_error_message( $error_code ) ); ?></div>
		<?php endif; ?>
		<form class="agw-form" method="post" action="<?php echo esc_url( $resend_action ); ?>" <?php echo $error_code ? 'aria-describedby="agw-resend-error"' : ''; ?>>
			<input type="hidden" name="alynt_ag_action" value="resend_confirmation">
			<?php wp_nonce_field( 'alynt_ag_resend_confirmation', 'alynt_ag_registration_nonce' ); ?>
			<div class="agw-field">
				<label for="agw-invalid-email"><?php esc_html_e( 'Email Address', 'alynt-account-gateway' ); ?></label>
				<input id="agw-invalid-email" name="email" type="email" autocomplete="email" required <?php echo $error_code ? 'aria-invalid="true" aria-describedby="agw-resend-error"' : ''; ?>>
			</div>
			<button class="agw-button agw-button--primary" type="submit"><?php esc_html_e( 'Send New Link', 'alynt-account-gateway' ); ?></button>
			<a class="agw-back-link" href="<?php echo esc_url( home_url( $settings['login_path'] ) ); ?>"><?php esc_html_e( 'Back to Login', 'alynt-account-gateway' ); ?></a>
		</form>
		<?php
	}

	/**
	 * Render custom account dashboard.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_dashboard_screen( $settings ) {
		$user        = wp_get_current_user();
		$dashboard   = new ALYNT_AG_Dashboard_Service();
		$woocommerce = new ALYNT_AG_WooCommerce_Integration();
		$endpoint    = $woocommerce->endpoint_from_path( $this->get_current_relative_path(), $settings );
		$links       = $dashboard->links_for_user( $user, $settings );
		$name        = $user->display_name ? $user->display_name : $user->user_email;
		?>
		<section class="agw-dashboard-hero" aria-labelledby="agw-screen-title">
			<p class="agw-dashboard-hero__eyebrow"><?php esc_html_e( 'Account Dashboard', 'alynt-account-gateway' ); ?></p>
			<h1 id="agw-screen-title" class="agw-dashboard-hero__title">
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: user display name. */
						__( 'Welcome, %s', 'alynt-account-gateway' ),
						$name
					)
				);
				?>
			</h1>
			<p class="agw-dashboard-hero__meta"><?php echo esc_html( $user->user_email ); ?></p>
		</section>

		<?php if ( ! empty( $settings['woocommerce_takeover'] ) && ! $dashboard->woocommerce_available() ) : ?>
			<div class="agw-status agw-status--error" role="alert">
				<?php esc_html_e( 'WooCommerce account takeover is enabled, but WooCommerce is not active.', 'alynt-account-gateway' ); ?>
			</div>
		<?php endif; ?>

		<section class="agw-dashboard-section" aria-labelledby="agw-dashboard-links-title">
			<h2 id="agw-dashboard-links-title"><?php esc_html_e( 'Manage Account', 'alynt-account-gateway' ); ?></h2>
			<div class="agw-dashboard-grid">
				<?php foreach ( $links as $link ) : ?>
					<a class="agw-dashboard-link" href="<?php echo esc_url( $link['url'] ); ?>" target="<?php echo esc_attr( $link['target'] ); ?>" <?php echo '_blank' === $link['target'] ? 'rel="noopener noreferrer"' : ''; ?>>
						<span class="agw-dashboard-link__icon agw-dashboard-link__icon--<?php echo esc_attr( $link['icon'] ); ?>" aria-hidden="true"></span>
						<span class="agw-dashboard-link__label"><?php echo esc_html( $link['label'] ); ?></span>
						<?php if ( '_blank' === $link['target'] ) : ?>
							<span class="screen-reader-text"><?php esc_html_e( 'opens in a new tab', 'alynt-account-gateway' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endforeach; ?>
			</div>
		</section>

		<?php if ( ! empty( $settings['woocommerce_takeover'] ) && 'dashboard' !== $endpoint['endpoint'] ) : ?>
			<section class="agw-dashboard-section agw-dashboard-section--content" aria-labelledby="agw-dashboard-content-title">
				<h2 id="agw-dashboard-content-title">
					<?php echo esc_html( $woocommerce->endpoint_labels()[ $endpoint['endpoint'] ] ?? __( 'Account', 'alynt-account-gateway' ) ); ?>
				</h2>
				<div class="agw-dashboard-content">
					<?php if ( ! $woocommerce->render_endpoint( $endpoint['endpoint'], $endpoint['value'] ) ) : ?>
						<p><?php esc_html_e( 'This account section is not available.', 'alynt-account-gateway' ); ?></p>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render configured registration verification area.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_verification_slot( $settings ) {
		if ( ! empty( $settings['turnstile_site_key'] ) ) {
			?>
			<div class="agw-verification-slot" aria-label="<?php esc_attr_e( 'Account verification', 'alynt-account-gateway' ); ?>">
				<div class="cf-turnstile" data-sitekey="<?php echo esc_attr( $settings['turnstile_site_key'] ); ?>"></div>
			</div>
			<?php
			return;
		}
		?>
		<div class="agw-verification-slot" role="status"><?php esc_html_e( 'Verification will appear here when enabled.', 'alynt-account-gateway' ); ?></div>
		<?php
	}

	/**
	 * Get title for the document title tag.
	 *
	 * @param string $screen Screen key.
	 * @return string
	 */
	public function get_screen_title( $screen ) {
		$messages = new ALYNT_AG_Frontend_Messages();
		return $messages->screen_title( $screen );
	}

	/**
	 * Get public registration error message.
	 *
	 * @param string $error_code Error code.
	 * @return string
	 */
	private function get_registration_error_message( $error_code ) {
		$messages = new ALYNT_AG_Frontend_Messages();
		return $messages->registration_error( $error_code );
	}

	/**
	 * Get public resend-confirmation error message.
	 *
	 * @param string $error_code Error code.
	 * @return string
	 */
	private function get_resend_error_message( $error_code ) {
		$messages = new ALYNT_AG_Frontend_Messages();
		return $messages->resend_error( $error_code );
	}

	/**
	 * Get public set-password error message.
	 *
	 * @param string $error_code Error code.
	 * @return string
	 */
	private function get_password_error_message( $error_code ) {
		$messages = new ALYNT_AG_Frontend_Messages();
		return $messages->password_error( $error_code );
	}

	/**
	 * Render configurable screen instruction text.
	 *
	 * @param string $copy Notice copy.
	 * @return void
	 */
	private function render_notice( $copy ) {
		if ( '' === trim( wp_strip_all_tags( (string) $copy ) ) ) {
			return;
		}
		?>
		<div class="agw-notice"><?php echo wp_kses_post( wpautop( $copy ) ); ?></div>
		<?php
	}
}
