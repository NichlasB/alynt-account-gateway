<?php
/**
 * Settings definition catalog.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Aggregates ordered settings definition providers.
 */
class ALYNT_AG_Settings_Definition {


	/**
	 * Return settings tabs.
	 *
	 * @return array<string,string>
	 */
	public static function tabs() {

		return array(
			'general'        => __( 'General', 'alynt-account-gateway' ),
			'urls'           => __( 'URLs & Redirects', 'alynt-account-gateway' ),
			'branding'       => __( 'Branding & Layout', 'alynt-account-gateway' ),
			'copy'           => __( 'Screen Copy', 'alynt-account-gateway' ),
			'registration'   => __( 'Registration', 'alynt-account-gateway' ),
			'security'       => __( 'Security & Spam', 'alynt-account-gateway' ),
			'emails'         => __( 'Emails', 'alynt-account-gateway' ),
			'dashboard'      => __( 'Dashboard', 'alynt-account-gateway' ),
			'woocommerce'    => __( 'WooCommerce', 'alynt-account-gateway' ),
			'webhooks'       => __( 'Webhooks', 'alynt-account-gateway' ),
			'privacy'        => __( 'Privacy & Data', 'alynt-account-gateway' ),
			'advanced_tools' => __( 'Advanced / Tools', 'alynt-account-gateway' ),
		);
	}

	/**
	 * Return the complete ordered settings schema.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function schema() {
		return array_merge(
			ALYNT_AG_Settings_Definition_Core::fields(),
			ALYNT_AG_Settings_Definition_Security_Email::fields(),
			ALYNT_AG_Settings_Definition_Account_Data::fields()
		);
	}
}
