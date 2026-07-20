<?php
/**
 * Frontend gateway facade.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers public hooks and delegates frontend behavior.
 */
class ALYNT_AG_Frontend {

	/**
	 * Frontend routes.
	 *
	 * @var ALYNT_AG_Frontend_Routes
	 */
	private $routes;

	/**
	 * Frontend assets.
	 *
	 * @var ALYNT_AG_Frontend_Assets
	 */
	private $assets;

	/**
	 * Access controller.
	 *
	 * @var ALYNT_AG_Frontend_Access_Controller
	 */
	private $access;

	/**
	 * URL adapter.
	 *
	 * @var ALYNT_AG_Frontend_Url_Adapter
	 */
	private $urls;

	/**
	 * Gateway controller.
	 *
	 * @var ALYNT_AG_Frontend_Gateway_Controller
	 */
	private $gateway;

	/**
	 * Constructor.
	 *
	 * @param array<string,object> $collaborators Optional collaborator overrides.
	 */
	public function __construct( $collaborators = array() ) {
		$collaborators = is_array( $collaborators ) ? $collaborators : array();
		$routes        = $collaborators['routes'] ?? new ALYNT_AG_Frontend_Routes();
		$context       = $collaborators['context'] ?? new ALYNT_AG_Frontend_Request_Context();
		$renderer      = $collaborators['renderer'] ?? new ALYNT_AG_Frontend_Document_Renderer();

		$this->routes  = $routes;
		$this->assets  = $collaborators['assets'] ?? new ALYNT_AG_Frontend_Assets();
		$this->access  = $collaborators['access'] ?? new ALYNT_AG_Frontend_Access_Controller( $routes, $context );
		$this->urls    = $collaborators['urls'] ?? new ALYNT_AG_Frontend_Url_Adapter( $routes );
		$this->gateway = $collaborators['gateway'] ?? new ALYNT_AG_Frontend_Gateway_Controller( $routes, $this->assets, $renderer );
	}

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
		add_action( 'template_redirect', array( $this, 'maybe_render_gateway_preview' ), 0 );
		add_action( 'template_redirect', array( $this, 'maybe_render_gateway' ), 1 );
		add_filter( 'login_url', array( $this, 'filter_login_url' ), 10, 3 );
		add_filter( 'lostpassword_url', array( $this, 'filter_lostpassword_url' ), 10, 2 );
		add_filter( 'register_url', array( $this, 'filter_register_url' ) );
		add_filter( 'logout_url', array( $this, 'filter_logout_url' ), 10, 2 );
		add_filter( 'v_forcelogin_bypass', array( $this, 'filter_force_login_bypass' ), 10, 2 );
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$screen   = $this->routes->screen( $settings );

		$this->assets->enqueue( $settings, $screen );
	}

	/**
	 * Enqueue assets for an authenticated gateway preview.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @param string              $screen   Gateway screen.
	 * @return void
	 */
	public function enqueue_preview_assets( $settings, $screen ) {
		$this->assets->enqueue_preview( $settings, $screen );
	}

	/**
	 * Restrict the admin toolbar.
	 *
	 * @param bool $show Whether to show toolbar.
	 * @return bool
	 */
	public function filter_admin_bar( $show ) {
		return $this->access->filter_admin_bar( $show );
	}

	/**
	 * Block wp-admin access when required.
	 *
	 * @return void
	 */
	public function maybe_block_wp_admin() {
		$this->access->maybe_block_wp_admin();
	}

	/**
	 * Redirect native login requests.
	 *
	 * @return void
	 */
	public function maybe_redirect_native_login() {
		$this->access->maybe_redirect_native_login();
	}

	/**
	 * Render the branded gateway.
	 *
	 * @return void
	 */
	public function maybe_render_gateway() {
		$this->gateway->maybe_render_gateway();
	}

	/**
	 * Render an authenticated gateway preview.
	 *
	 * @return void
	 */
	public function maybe_render_gateway_preview() {
		$this->gateway->maybe_render_gateway_preview();
	}

	/**
	 * Render one gateway screen for admin preview.
	 *
	 * @param string              $screen   Screen key.
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	public function render_preview( $screen, $settings ) {
		$this->gateway->render_preview( $screen, $settings );
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
		return $this->urls->filter_login_url( $login_url, $redirect, $force_reauth );
	}

	/**
	 * Filter the lost-password URL.
	 *
	 * @param string $lostpassword_url Native URL.
	 * @param string $redirect         Redirect URL.
	 * @return string
	 */
	public function filter_lostpassword_url( $lostpassword_url, $redirect ) {
		return $this->urls->filter_lostpassword_url( $lostpassword_url, $redirect );
	}

	/**
	 * Filter the registration URL.
	 *
	 * @param string $register_url Native URL.
	 * @return string
	 */
	public function filter_register_url( $register_url ) {
		return $this->urls->filter_register_url( $register_url );
	}

	/**
	 * Filter the logout URL.
	 *
	 * @param string $logout_url Native URL.
	 * @param string $redirect   Redirect URL.
	 * @return string
	 */
	public function filter_logout_url( $logout_url, $redirect ) {
		return $this->urls->filter_logout_url( $logout_url, $redirect );
	}

	/**
	 * Let Force Login pass through public gateway routes.
	 *
	 * @param bool   $bypass Whether Force Login already intends to bypass.
	 * @param string $url    Visited URL.
	 * @return bool
	 */
	public function filter_force_login_bypass( $bypass, $url ) {
		return $this->access->filter_force_login_bypass( $bypass, $url );
	}

	/**
	 * Get title for the document title tag.
	 *
	 * @param string $screen Screen key.
	 * @return string
	 */
	public function get_screen_title( $screen ) {
		return $this->gateway->get_screen_title( $screen );
	}
}
