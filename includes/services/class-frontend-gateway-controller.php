<?php
/**
 * Frontend gateway controller.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dispatches branded gateway documents and authenticated previews.
 */
class ALYNT_AG_Frontend_Gateway_Controller {

	/**
	 * Route helper.
	 *
	 * @var ALYNT_AG_Frontend_Routes
	 */
	private $routes;

	/**
	 * Asset helper.
	 *
	 * @var ALYNT_AG_Frontend_Assets
	 */
	private $assets;

	/**
	 * Document renderer.
	 *
	 * @var ALYNT_AG_Frontend_Document_Renderer
	 */
	private $renderer;

	/**
	 * Constructor.
	 *
	 * @param ALYNT_AG_Frontend_Routes            $routes   Route helper.
	 * @param ALYNT_AG_Frontend_Assets            $assets   Asset helper.
	 * @param ALYNT_AG_Frontend_Document_Renderer $renderer Document renderer.
	 */
	public function __construct( $routes, $assets, $renderer ) {
		$this->routes   = $routes;
		$this->assets   = $assets;
		$this->renderer = $renderer;
	}

	/**
	 * Render the branded gateway for a configured route.
	 *
	 * @return void
	 */
	public function maybe_render_gateway() {
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		if ( empty( $settings['frontend_enabled'] ) ) {
			return;
		}

		$screen = $this->routes->screen( $settings );
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

		$this->renderer->render_gateway_document( $screen, $settings, $this->routes->current_relative_path() );
		exit;
	}

	/**
	 * Render a nonce-protected gateway preview outside wp-admin.
	 *
	 * @return void
	 */
	public function maybe_render_gateway_preview() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verified after screen normalization.
		$preview = isset( $_GET['alynt_ag_preview_gateway'] ) ? sanitize_key( wp_unslash( $_GET['alynt_ag_preview_gateway'] ) ) : '';
		if ( '1' !== $preview ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verified after screen normalization.
		$screen_code = isset( $_GET['alynt_ag_preview_screen'] ) ? sanitize_key( wp_unslash( $_GET['alynt_ag_preview_screen'] ) ) : 'l';
		$screens     = $this->preview_screen_codes();
		$screen      = isset( $screens[ $screen_code ] ) ? $screens[ $screen_code ] : 'login';

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to preview gateway screens.', 'alynt-account-gateway' ) );
		}

		check_admin_referer( 'alynt_ag_preview_gateway_' . $screen, 'alynt_ag_preview_nonce' );

		$settings = ALYNT_AG_Settings_Schema::get_settings();

		$this->assets->enqueue_preview( $settings, $screen );
		show_admin_bar( false );
		add_filter( 'show_admin_bar', '__return_false', PHP_INT_MAX );

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
		$this->print_preview_styles();
		echo '</head>';
		echo '<body class="alynt-ag-body alynt-ag-preview-body">';
		$this->render_preview( $screen, $settings );
		$this->print_preview_scripts();
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
		$this->renderer->render_preview( $screen, $settings, $this->routes->current_relative_path() );
	}

	/**
	 * Get title for the document title tag.
	 *
	 * @param string $screen Screen key.
	 * @return string
	 */
	public function get_screen_title( $screen ) {
		return $this->renderer->get_screen_title( $screen );
	}

	/**
	 * Return supported preview screens.
	 *
	 * @return array<string,bool>
	 */
	private function preview_screen_codes() {
		return array(
			'b' => 'dashboard',
			'l' => 'login',
			'r' => 'register',
			'p' => 'lostpassword',
			's' => 'setpassword',
			'o' => 'logout',
			'd' => 'registration_disabled',
			'i' => 'invalidlink',
		);
	}

	/**
	 * Print standalone gateway preview styles.
	 *
	 * @return void
	 */
	private function print_preview_styles() {
		if ( function_exists( 'wp_styles' ) ) {
			wp_styles()->do_items( array( 'alynt-ag-frontend' ) );
			return;
		}

		wp_print_styles( array( 'alynt-ag-frontend' ) );
	}

	/**
	 * Print standalone gateway preview scripts.
	 *
	 * @return void
	 */
	private function print_preview_scripts() {
		if ( function_exists( 'wp_scripts' ) ) {
			wp_scripts()->do_items( array( 'alynt-ag-frontend' ) );
			return;
		}

		wp_print_scripts( array( 'alynt-ag-frontend' ) );
	}

	/**
	 * Handle confirmed logout requests.
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
}
