<?php
/**
 * Settings page readiness-rules component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused readiness-rules behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Readiness_Rules extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Return registration readiness.
	 *
	 * @param bool                $registration_enabled Whether public registration is enabled.
	 * @param bool                $has_turnstile        Whether Turnstile is configured.
	 * @param bool                $has_reoon            Whether Reoon is configured.
	 * @param array<string,mixed> $settings             Current settings.
	 * @return array{label:string,status:string,message:string,tab:string}
	 */
	public function registration_readiness_check( $registration_enabled, $has_turnstile, $has_reoon, $settings ) {
		if ( ! $registration_enabled ) {
			return array(
				'label'   => __( 'Public Registration', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'message' => __( 'Public registration is disabled by default. Enable it only after terms, privacy, and protection settings are reviewed.', 'alynt-account-gateway' ),
				'tab'     => 'registration',
			);
		}

		if ( empty( $settings['terms_path'] ) || empty( $settings['privacy_path'] ) ) {
			return array(
				'label'   => __( 'Public Registration', 'alynt-account-gateway' ),
				'status'  => 'action',
				'message' => __( 'Public registration is enabled, but Terms and Privacy paths must both be configured.', 'alynt-account-gateway' ),
				'tab'     => 'registration',
			);
		}

		if ( ! $has_turnstile && ! $has_reoon ) {
			return array(
				'label'   => __( 'Public Registration', 'alynt-account-gateway' ),
				'status'  => 'warning',
				'message' => __( 'Public registration is enabled without Turnstile or Reoon. Add at least one protection provider before public launch.', 'alynt-account-gateway' ),
				'tab'     => 'security',
			);
		}

		return array(
			'label'   => __( 'Public Registration', 'alynt-account-gateway' ),
			'status'  => 'ready',
			'message' => __( 'Public registration has Terms, Privacy, and at least one protection provider configured.', 'alynt-account-gateway' ),
			'tab'     => 'registration',
		);
	}

	/**
	 * Return WooCommerce readiness.
	 *
	 * @param bool $dashboard_enabled    Whether the custom dashboard is enabled.
	 * @param bool $woocommerce_takeover Whether WooCommerce takeover is enabled.
	 * @return array{label:string,status:string,message:string,tab:string}
	 */
	public function woocommerce_readiness_check( $dashboard_enabled, $woocommerce_takeover ) {
		if ( ! $woocommerce_takeover ) {
			return array(
				'label'   => __( 'WooCommerce Takeover', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'message' => __( 'WooCommerce account takeover is disabled.', 'alynt-account-gateway' ),
				'tab'     => 'woocommerce',
			);
		}

		if ( ! $dashboard_enabled ) {
			return array(
				'label'   => __( 'WooCommerce Takeover', 'alynt-account-gateway' ),
				'status'  => 'action',
				'message' => __( 'WooCommerce takeover requires the custom dashboard to be enabled.', 'alynt-account-gateway' ),
				'tab'     => 'dashboard',
			);
		}

		if ( ! class_exists( 'WooCommerce' ) && ! function_exists( 'WC' ) ) {
			return array(
				'label'   => __( 'WooCommerce Takeover', 'alynt-account-gateway' ),
				'status'  => 'warning',
				'message' => __( 'WooCommerce takeover is enabled, but WooCommerce does not appear to be active.', 'alynt-account-gateway' ),
				'tab'     => 'woocommerce',
			);
		}

		return array(
			'label'   => __( 'WooCommerce Takeover', 'alynt-account-gateway' ),
			'status'  => 'ready',
			'message' => __( 'WooCommerce takeover is enabled and WooCommerce appears to be active.', 'alynt-account-gateway' ),
			'tab'     => 'woocommerce',
		);
	}

	/**
	 * Return webhook signing readiness message.
	 *
	 * @param bool $webhook_enabled Whether a webhook URL is configured.
	 * @param bool $signing_enabled Whether signing is configured.
	 * @return string
	 */
	public function webhook_signing_readiness_message( $webhook_enabled, $signing_enabled ) {
		if ( ! $webhook_enabled ) {
			return __( 'No account-created webhook URL is configured.', 'alynt-account-gateway' );
		}

		if ( $signing_enabled ) {
			return __( 'Account-created webhooks include HMAC signing headers.', 'alynt-account-gateway' );
		}

		return __( 'A webhook URL is configured without a signing secret. Add signing before connecting sensitive automations.', 'alynt-account-gateway' );
	}

	/**
	 * Check whether retention windows are usable.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return bool
	 */
	public function privacy_retention_ready( $settings ) {
		foreach ( array( 'success_log_retention', 'failed_log_retention', 'verification_log_retention', 'consent_record_retention', 'audit_log_retention' ) as $key ) {
			if ( empty( $settings[ $key ] ) || 0 >= (int) $settings[ $key ] ) {
				return false;
			}
		}

		return true;
	}
}
