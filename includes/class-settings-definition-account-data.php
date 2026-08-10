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
				'label'   => alynt_ag_schema_text( 'Enable Custom Dashboard', 'alynt-account-gateway' ),
			),
			'dashboard_custom_links'              => array(
				'tab'     => 'dashboard',
				'type'    => 'dashboard_links',
				'default' => '[]',
				'label'   => alynt_ag_schema_text( 'Custom Dashboard Links', 'alynt-account-gateway' ),
			),
			'dashboard_offcanvas_enabled'         => array(
				'tab'     => 'dashboard',
				'type'    => 'boolean',
				'default' => false,
				'label'   => alynt_ag_schema_text( 'Enable Dashboard Menu Panel', 'alynt-account-gateway' ),
			),
			'dashboard_offcanvas_menu_id'         => array(
				'tab'     => 'dashboard',
				'type'    => 'nav_menu',
				'default' => 0,
				'label'   => alynt_ag_schema_text( 'Dashboard Menu Panel Menu', 'alynt-account-gateway' ),
			),
			'dashboard_footer_menu_enabled'       => array(
				'tab'     => 'dashboard',
				'type'    => 'boolean',
				'default' => false,
				'label'   => alynt_ag_schema_text( 'Enable Dashboard Footer Menu', 'alynt-account-gateway' ),
			),
			'dashboard_footer_menu_id'            => array(
				'tab'     => 'dashboard',
				'type'    => 'nav_menu',
				'default' => 0,
				'label'   => alynt_ag_schema_text( 'Dashboard Footer Menu', 'alynt-account-gateway' ),
			),
			'woocommerce_takeover'                => array(
				'tab'     => 'woocommerce',
				'type'    => 'boolean',
				'default' => false,
				'label'   => alynt_ag_schema_text( 'Take Over WooCommerce My Account', 'alynt-account-gateway' ),
			),
			'woocommerce_require_login_checkout'  => array(
				'tab'     => 'woocommerce',
				'type'    => 'boolean',
				'default' => false,
				'label'   => alynt_ag_schema_text( 'Require Login Before Checkout', 'alynt-account-gateway' ),
			),
			'woocommerce_require_login_order_pay' => array(
				'tab'     => 'woocommerce',
				'type'    => 'boolean',
				'default' => false,
				'label'   => alynt_ag_schema_text( 'Require Login For Order Payment Links', 'alynt-account-gateway' ),
			),
			'woocommerce_saved_methods_enabled'   => array(
				'tab'     => 'woocommerce',
				'type'    => 'boolean',
				'default' => false,
				'label'   => alynt_ag_schema_text( 'Enable Saved Payment Methods', 'alynt-account-gateway' ),
			),
			'woocommerce_hidden_menu_items'       => array(
				'tab'     => 'woocommerce',
				'type'    => 'woocommerce_menu_visibility',
				'default' => array(),
				'label'   => alynt_ag_schema_text( 'Dashboard Navigation Items', 'alynt-account-gateway' ),
			),
			'account_created_webhook'             => array(
				'tab'     => 'webhooks',
				'type'    => 'url',
				'default' => '',
				'label'   => alynt_ag_schema_text( 'Account Created Webhook URL', 'alynt-account-gateway' ),
			),
			'webhook_signing_secret'              => array(
				'tab'     => 'webhooks',
				'type'    => 'secret',
				'default' => '',
				'label'   => alynt_ag_schema_text( 'Webhook Signing Secret', 'alynt-account-gateway' ),
			),
			'debug_payload_logging'               => array(
				'tab'     => 'webhooks',
				'type'    => 'boolean',
				'default' => false,
				'label'   => alynt_ag_schema_text( 'Debug Payload Logging', 'alynt-account-gateway' ),
			),
			'diagnostics_enabled'                 => array(
				'tab'     => 'advanced_tools',
				'type'    => 'boolean',
				'default' => false,
				'label'   => alynt_ag_schema_text( 'Enable Diagnostics', 'alynt-account-gateway' ),
			),
			'diagnostics_min_level'               => array(
				'tab'     => 'advanced_tools',
				'type'    => 'select',
				'default' => 'warning',
				'label'   => alynt_ag_schema_text( 'Diagnostics Minimum Level', 'alynt-account-gateway' ),
			),
			'diagnostics_retention'               => array(
				'tab'     => 'advanced_tools',
				'type'    => 'integer',
				'default' => 30,
				'min'     => 1,
				'max'     => 3650,
				'label'   => alynt_ag_schema_text( 'Diagnostics Retention Days', 'alynt-account-gateway' ),
			),
			'success_log_retention'               => array(
				'tab'     => 'privacy',
				'type'    => 'integer',
				'default' => 7,
				'min'     => 1,
				'max'     => 3650,
				'label'   => alynt_ag_schema_text( 'Successful Webhook Log Retention Days', 'alynt-account-gateway' ),
			),
			'failed_log_retention'                => array(
				'tab'     => 'privacy',
				'type'    => 'integer',
				'default' => 30,
				'min'     => 1,
				'max'     => 3650,
				'label'   => alynt_ag_schema_text( 'Failed Webhook Log Retention Days', 'alynt-account-gateway' ),
			),
			'verification_log_retention'          => array(
				'tab'     => 'privacy',
				'type'    => 'integer',
				'default' => 30,
				'min'     => 1,
				'max'     => 3650,
				'label'   => alynt_ag_schema_text( 'Verification Log Retention Days', 'alynt-account-gateway' ),
			),
			'consent_record_retention'            => array(
				'tab'     => 'privacy',
				'type'    => 'integer',
				'default' => 365,
				'min'     => 1,
				'max'     => 3650,
				'label'   => alynt_ag_schema_text( 'Consent Record Retention Days', 'alynt-account-gateway' ),
			),
			'audit_log_retention'                 => array(
				'tab'     => 'privacy',
				'type'    => 'integer',
				'default' => 180,
				'min'     => 1,
				'max'     => 3650,
				'label'   => alynt_ag_schema_text( 'Audit Log Retention Days', 'alynt-account-gateway' ),
			),
			'public_author_name_format'           => array(
				'tab'     => 'privacy',
				'type'    => 'select',
				'default' => 'first_last_initial',
				'label'   => alynt_ag_schema_text( 'Public Comment/Review Name Format', 'alynt-account-gateway' ),
				'options' => array(
					'wordpress_default'  => alynt_ag_schema_text( 'WordPress default', 'alynt-account-gateway' ),
					'first_last_initial' => alynt_ag_schema_text( 'First name + last initial, e.g. Anna M.', 'alynt-account-gateway' ),
					'first_name'         => alynt_ag_schema_text( 'First name only', 'alynt-account-gateway' ),
					'customer'           => alynt_ag_schema_text( 'Generic customer label', 'alynt-account-gateway' ),
				),
			),
		);
	}
}
