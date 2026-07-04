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
			$this->gateway_shell()->render_gateway_shell( $screen, $settings );
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
			$this->gateway_shell()->render_gateway_shell_with_password_preview( $settings );
			return;
		}

		if ( 'dashboard' === $screen ) {
			$this->render_dashboard_shell( $settings );
			return;
		}

		$this->gateway_shell()->render_gateway_shell( $screen, $settings );
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
	 * Frontend gateway shell renderer.
	 *
	 * @return ALYNT_AG_Frontend_Gateway_Shell
	 */
	private function gateway_shell() {
		return new ALYNT_AG_Frontend_Gateway_Shell();
	}

	/**
	 * Frontend dashboard screen helper.
	 *
	 * @return ALYNT_AG_Frontend_Dashboard_Screen
	 */
	private function dashboard_screen() {
		return new ALYNT_AG_Frontend_Dashboard_Screen();
	}

	/**
	 * Render dashboard shell.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_dashboard_shell( $settings ) {
		$this->dashboard_screen()->render_dashboard_shell( $settings, $this->get_current_relative_path() );
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
	 * Get title for the document title tag.
	 *
	 * @param string $screen Screen key.
	 * @return string
	 */
	public function get_screen_title( $screen ) {
		$messages = new ALYNT_AG_Frontend_Messages();
		return $messages->screen_title( $screen );
	}
}
