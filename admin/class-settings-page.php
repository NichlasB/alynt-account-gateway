<?php
/**
 * Settings page.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers WordPress hooks and delegates settings-page behavior.
 */
class ALYNT_AG_Settings_Page {

	/**
	 * Settings-page component registry.
	 *
	 * @var ALYNT_AG_Settings_Page_Components|null
	 */
	private $components;

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'maybe_handle_preview_gateway_request' ), 1 );
		add_action( 'admin_post_alynt_ag_export_settings', array( $this, 'handle_export_settings' ) );
		add_action( 'admin_post_alynt_ag_import_settings', array( $this, 'handle_import_settings' ) );
		add_action( 'admin_post_alynt_ag_restore_tab_defaults', array( $this, 'handle_restore_tab_defaults' ) );
		add_action( 'admin_post_alynt_ag_preview_gateway', array( $this, 'handle_preview_gateway' ) );
		add_action( 'wp_ajax_alynt_ag_preview_gateway', array( $this, 'handle_preview_gateway' ) );
		add_action( 'admin_post_alynt_ag_export_diagnostics', array( $this, 'handle_export_diagnostics' ) );
		add_action( 'admin_post_alynt_ag_clear_diagnostics', array( $this, 'handle_clear_diagnostics' ) );
		add_action( 'admin_post_alynt_ag_review_verification', array( $this, 'handle_review_verification' ) );
		add_action( 'admin_post_alynt_ag_test_security_provider', array( $this, 'handle_test_security_provider' ) );
		add_action( 'admin_post_alynt_ag_preview_email', array( $this, 'handle_preview_email' ) );
		add_action( 'admin_post_alynt_ag_test_email', array( $this, 'handle_test_email' ) );
		add_action( 'admin_post_alynt_ag_test_webhook', array( $this, 'handle_test_webhook' ) );
		add_action( 'update_option_alynt_ag_settings', array( $this, 'log_settings_change' ), 10, 2 );
	}

	/**
	 * Add settings page.
	 *
	 * @return void
	 */
	public function add_menu_page() {
		add_options_page(
			__( 'Alynt Account Gateway', 'alynt-account-gateway' ),
			__( 'Account Gateway', 'alynt-account-gateway' ),
			'manage_options',
			'alynt-account-gateway',
			array( $this, 'render' )
		);
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'alynt_ag_settings',
			'alynt_ag_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( 'ALYNT_AG_Settings_Schema', 'sanitize' ),
				'default'           => ALYNT_AG_Settings_Schema::defaults(),
			)
		);
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render() {
		$this->call_component( 'render' );
	}

	/**
	 * Render a standalone gateway preview from the settings page route.
	 *
	 * Some sites intercept or normalize admin-post.php requests. The settings
	 * page route keeps preview output behind wp-admin authentication while
	 * avoiding those front-controller collisions.
	 *
	 * @return void
	 */
	public function maybe_handle_preview_gateway_request() {
		$this->call_component( 'maybe_handle_preview_gateway_request' );
	}

	/**
	 * Export plugin settings as JSON.
	 *
	 * @return void
	 */
	public function handle_export_settings() {
		$this->call_component( 'handle_export_settings' );
	}

	/**
	 * Import plugin settings from JSON.
	 *
	 * @return void
	 */
	public function handle_import_settings() {
		$this->call_component( 'handle_import_settings' );
	}

	/**
	 * Restore one settings tab to defaults.
	 *
	 * @return void
	 */
	public function handle_restore_tab_defaults() {
		$this->call_component( 'handle_restore_tab_defaults' );
	}

	/**
	 * Render a standalone gateway screen preview.
	 *
	 * @return void
	 */
	public function handle_preview_gateway() {
		$this->call_component( 'handle_preview_gateway' );
	}

	/**
	 * Export diagnostics events.
	 *
	 * @return void
	 */
	public function handle_export_diagnostics() {
		$this->call_component( 'handle_export_diagnostics' );
	}

	/**
	 * Clear diagnostics events.
	 *
	 * @return void
	 */
	public function handle_clear_diagnostics() {
		$this->call_component( 'handle_clear_diagnostics' );
	}

	/**
	 * Record an admin decision for an allowed flagged Reoon result.
	 *
	 * @return void
	 */
	public function handle_review_verification() {
		$this->call_component( 'handle_review_verification' );
	}

	/**
	 * Run a safe provider connection check using saved credentials.
	 *
	 * @return void
	 */
	public function handle_test_security_provider() {
		$this->call_component( 'handle_test_security_provider' );
	}

	/**
	 * Render an email template preview.
	 *
	 * @return void
	 */
	public function handle_preview_email() {
		$this->call_component( 'handle_preview_email' );
	}

	/**
	 * Send a test email.
	 *
	 * @return void
	 */
	public function handle_test_email() {
		$this->call_component( 'handle_test_email' );
	}

	/**
	 * Send a test webhook.
	 *
	 * @return void
	 */
	public function handle_test_webhook() {
		$this->call_component( 'handle_test_webhook' );
	}

	/**
	 * Record settings changes in the audit log.
	 *
	 * @param array<string,mixed> $old_value Previous settings.
	 * @param array<string,mixed> $value     New settings.
	 * @return void
	 */
	public function log_settings_change( $old_value, $value ) {
		$this->call_component(
			'log_settings_change',
			array( $old_value, $value )
		);
	}

	/**
	 * Call one internal settings-page operation.
	 *
	 * @param string           $method    Operation name.
	 * @param array<int,mixed> $arguments Operation arguments.
	 * @return mixed
	 */
	private function call_component( $method, $arguments = array() ) {
		return $this->components()->call( $method, $arguments );
	}

	/**
	 * Return the lazily-created component registry.
	 *
	 * @return ALYNT_AG_Settings_Page_Components
	 */
	private function components() {
		if ( null === $this->components ) {
			$this->components = new ALYNT_AG_Settings_Page_Components();
		}

		return $this->components;
	}
}
