<?php
/**
 * Main plugin orchestrator.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coordinates plugin services.
 */
class ALYNT_AG_Plugin {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function run() {
		add_action( 'admin_init', array( 'ALYNT_AG_Database', 'maybe_upgrade' ) );

		$i18n = new ALYNT_AG_I18n();
		$i18n->register();

		$privacy = new ALYNT_AG_Privacy_Service();
		$privacy->register();

		$cleanup = new ALYNT_AG_Retention_Cleanup();
		$cleanup->register();

		$rate_limiter = new ALYNT_AG_Rate_Limiter();
		$rate_limiter->register();

		$email_templates = new ALYNT_AG_Email_Template_Service();
		$email_templates->register();

		$auth = new ALYNT_AG_Auth_Service();
		$auth->register();

		$registration = new ALYNT_AG_Registration_Service();
		$registration->register();

		if ( is_admin() ) {
			$admin = new ALYNT_AG_Admin();
			$admin->register();
		}

		$frontend = new ALYNT_AG_Frontend();
		$frontend->register();

		$woocommerce = new ALYNT_AG_WooCommerce_Integration();
		$woocommerce->register();
	}
}
