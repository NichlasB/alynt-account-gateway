<?php
/**
 * Dashboard, WooCommerce, webhook, diagnostics, and privacy definitions.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dashboard, WooCommerce, webhook, diagnostics, and privacy definitions.
 */
class ALYNT_AG_Settings_Definition_Account_Data {

	/**
	 * Return this provider's ordered settings fields.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function fields() {
		return array(
			'dashboard_enabled'                   => array(
				'tab'     => 'dashboard',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Enable Custom Dashboard', 'alynt-account-gateway' ),
			),
			'dashboard_custom_links'              => array(
				'tab'     => 'dashboard',
				'type'    => 'dashboard_links',
				'default' => '[]',
				'label'   => __( 'Custom Dashboard Links', 'alynt-account-gateway' ),
			),
			'dashboard_offcanvas_enabled'         => array(
				'tab'     => 'dashboard',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Enable Dashboard Menu Panel', 'alynt-account-gateway' ),
			),
			'dashboard_offcanvas_menu_id'         => array(
				'tab'     => 'dashboard',
				'type'    => 'nav_menu',
				'default' => 0,
				'label'   => __( 'Dashboard Menu Panel Menu', 'alynt-account-gateway' ),
			),
			'dashboard_footer_menu_enabled'       => array(
				'tab'     => 'dashboard',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Enable Dashboard Footer Menu', 'alynt-account-gateway' ),
			),
			'dashboard_footer_menu_id'            => array(
				'tab'     => 'dashboard',
				'type'    => 'nav_menu',
				'default' => 0,
				'label'   => __( 'Dashboard Footer Menu', 'alynt-account-gateway' ),
			),
			'woocommerce_takeover'                => array(
				'tab'     => 'woocommerce',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Take Over WooCommerce My Account', 'alynt-account-gateway' ),
			),
			'woocommerce_require_login_checkout'  => array(
				'tab'     => 'woocommerce',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Require Login Before Checkout', 'alynt-account-gateway' ),
			),
			'woocommerce_require_login_order_pay' => array(
				'tab'     => 'woocommerce',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Require Login For Order Payment Links', 'alynt-account-gateway' ),
			),
			'woocommerce_hidden_menu_items'       => array(
				'tab'     => 'woocommerce',
				'type'    => 'woocommerce_menu_visibility',
				'default' => array(),
				'label'   => __( 'Dashboard Navigation Items', 'alynt-account-gateway' ),
			),
			'account_created_webhook'             => array(
				'tab'     => 'webhooks',
				'type'    => 'url',
				'default' => '',
				'label'   => __( 'Account Created Webhook URL', 'alynt-account-gateway' ),
			),
			'webhook_signing_secret'              => array(
				'tab'     => 'webhooks',
				'type'    => 'secret',
				'default' => '',
				'label'   => __( 'Webhook Signing Secret', 'alynt-account-gateway' ),
			),
			'debug_payload_logging'               => array(
				'tab'     => 'webhooks',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Debug Payload Logging', 'alynt-account-gateway' ),
			),
			'diagnostics_enabled'                 => array(
				'tab'     => 'advanced_tools',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Enable Diagnostics', 'alynt-account-gateway' ),
			),
			'diagnostics_min_level'               => array(
				'tab'     => 'advanced_tools',
				'type'    => 'select',
				'default' => 'warning',
				'label'   => __( 'Diagnostics Minimum Level', 'alynt-account-gateway' ),
			),
			'diagnostics_retention'               => array(
				'tab'     => 'advanced_tools',
				'type'    => 'integer',
				'default' => 30,
				'label'   => __( 'Diagnostics Retention Days', 'alynt-account-gateway' ),
			),
			'success_log_retention'               => array(
				'tab'     => 'privacy',
				'type'    => 'integer',
				'default' => 7,
				'label'   => __( 'Successful Webhook Log Retention Days', 'alynt-account-gateway' ),
			),
			'failed_log_retention'                => array(
				'tab'     => 'privacy',
				'type'    => 'integer',
				'default' => 30,
				'label'   => __( 'Failed Webhook Log Retention Days', 'alynt-account-gateway' ),
			),
			'verification_log_retention'          => array(
				'tab'     => 'privacy',
				'type'    => 'integer',
				'default' => 30,
				'label'   => __( 'Verification Log Retention Days', 'alynt-account-gateway' ),
			),
			'consent_record_retention'            => array(
				'tab'     => 'privacy',
				'type'    => 'integer',
				'default' => 365,
				'label'   => __( 'Consent Record Retention Days', 'alynt-account-gateway' ),
			),
			'audit_log_retention'                 => array(
				'tab'     => 'privacy',
				'type'    => 'integer',
				'default' => 180,
				'label'   => __( 'Audit Log Retention Days', 'alynt-account-gateway' ),
			),
		);
	}
}
