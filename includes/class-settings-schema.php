<?php
/**
 * Settings schema.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Central settings definition.
 */
class ALYNT_AG_Settings_Schema {

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
	 * Return schema.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function schema() {
		return array(
			'frontend_enabled'                      => array(
				'tab'     => 'general',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Enable Frontend Output', 'alynt-account-gateway' ),
			),
			'login_path'                            => array(
				'tab'     => 'urls',
				'type'    => 'relative_path',
				'default' => '/login',
				'label'   => __( 'Login URL Path', 'alynt-account-gateway' ),
			),
			'account_action_base'                   => array(
				'tab'     => 'urls',
				'type'    => 'relative_path',
				'default' => '/account',
				'label'   => __( 'Account Action Base', 'alynt-account-gateway' ),
			),
			'after_login_redirect'                  => array(
				'tab'     => 'urls',
				'type'    => 'relative_path',
				'default' => '/my-account/',
				'label'   => __( 'After Login Redirect', 'alynt-account-gateway' ),
			),
			'emergency_bypass_key'                  => array(
				'tab'     => 'advanced_tools',
				'type'    => 'secret',
				'default' => '',
				'label'   => __( 'Emergency Bypass Key', 'alynt-account-gateway' ),
			),
			'registration_enabled'                  => array(
				'tab'     => 'registration',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Enable Public Account Creation', 'alynt-account-gateway' ),
			),
			'registration_token_hours'              => array(
				'tab'     => 'registration',
				'type'    => 'integer',
				'default' => 24,
				'label'   => __( 'Pending Registration Expiry Hours', 'alynt-account-gateway' ),
			),
			'username_format'                       => array(
				'tab'     => 'registration',
				'type'    => 'string',
				'default' => '@User_{first_name}_{last_name}',
				'label'   => __( 'Generated Username Format', 'alynt-account-gateway' ),
			),
			'terms_path'                            => array(
				'tab'     => 'registration',
				'type'    => 'relative_path',
				'default' => '/terms/',
				'label'   => __( 'Terms URL Path', 'alynt-account-gateway' ),
			),
			'privacy_path'                          => array(
				'tab'     => 'registration',
				'type'    => 'relative_path',
				'default' => '/legal/privacy/',
				'label'   => __( 'Privacy URL Path', 'alynt-account-gateway' ),
			),
			'login_intro_text'                      => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'Welcome back. Log in to manage your orders, downloads, and account details.', 'alynt-account-gateway' ),
				'label'   => __( 'Login Instruction Text', 'alynt-account-gateway' ),
			),
			'register_intro_text'                   => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'Create your customer account. Fill in your details and you will receive a confirmation email. Be sure to check your spam folder if you do not see it.', 'alynt-account-gateway' ),
				'label'   => __( 'Registration Instruction Text', 'alynt-account-gateway' ),
			),
			'lostpassword_intro_text'               => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'Enter the email address associated with your account and we will send a link to reset your password.', 'alynt-account-gateway' ),
				'label'   => __( 'Lost Password Instruction Text', 'alynt-account-gateway' ),
			),
			'setpassword_intro_text'                => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'Choose a new password for your account.', 'alynt-account-gateway' ),
				'label'   => __( 'Set Password Instruction Text', 'alynt-account-gateway' ),
			),
			'logout_intro_text'                     => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'Are you sure you want to log out of your account?', 'alynt-account-gateway' ),
				'label'   => __( 'Logout Confirmation Instruction Text', 'alynt-account-gateway' ),
			),
			'registration_disabled_text'            => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'New account registration is currently unavailable. Please check back later, or log in if you already have an account.', 'alynt-account-gateway' ),
				'label'   => __( 'Registration Disabled Text', 'alynt-account-gateway' ),
			),
			'invalid_link_text'                     => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'This confirmation link is invalid or has expired. Request a new one below.', 'alynt-account-gateway' ),
				'label'   => __( 'Invalid Or Expired Link Text', 'alynt-account-gateway' ),
			),
			'brand_logo_id'                         => array(
				'tab'     => 'branding',
				'type'    => 'attachment_id',
				'default' => 0,
				'label'   => __( 'Brand Logo', 'alynt-account-gateway' ),
			),
			'brand_logo_max_width'                  => array(
				'tab'     => 'branding',
				'type'    => 'integer',
				'default' => 220,
				'label'   => __( 'Logo Max Width', 'alynt-account-gateway' ),
			),
			'primary_color'                         => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#3B5249',
				'label'   => __( 'Primary Color', 'alynt-account-gateway' ),
			),
			'accent_color'                          => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#E1CDB5',
				'label'   => __( 'Accent Color', 'alynt-account-gateway' ),
			),
			'text_color'                            => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#281408',
				'label'   => __( 'Text Color', 'alynt-account-gateway' ),
			),
			'page_background_color'                 => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#EAE4D6',
				'label'   => __( 'Page Background Color', 'alynt-account-gateway' ),
			),
			'surface_color'                         => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#FFFFFF',
				'label'   => __( 'Card Surface Color', 'alynt-account-gateway' ),
			),
			'error_color'                           => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#B3492E',
				'label'   => __( 'Error Color', 'alynt-account-gateway' ),
			),
			'button_background_color'               => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#3B5249',
				'label'   => __( 'Button Background Color', 'alynt-account-gateway' ),
			),
			'button_text_color'                     => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#ffffff',
				'label'   => __( 'Button Text Color', 'alynt-account-gateway' ),
			),
			'background_image_id'                   => array(
				'tab'     => 'branding',
				'type'    => 'attachment_id',
				'default' => 0,
				'label'   => __( 'Gateway Background Image', 'alynt-account-gateway' ),
			),
			'heading_font_family'                   => array(
				'tab'     => 'branding',
				'type'    => 'css_font_family',
				'default' => 'Georgia, serif',
				'label'   => __( 'Heading Font Stack', 'alynt-account-gateway' ),
			),
			'body_font_family'                      => array(
				'tab'     => 'branding',
				'type'    => 'css_font_family',
				'default' => '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
				'label'   => __( 'Body Font Stack', 'alynt-account-gateway' ),
			),
			'protection_mode'                       => array(
				'tab'     => 'security',
				'type'    => 'select',
				'default' => 'turnstile_or_reoon',
				'label'   => __( 'Registration Protection Mode', 'alynt-account-gateway' ),
			),
			'turnstile_site_key'                    => array(
				'tab'     => 'security',
				'type'    => 'string',
				'default' => '',
				'label'   => __( 'Turnstile Site Key', 'alynt-account-gateway' ),
			),
			'turnstile_secret_key'                  => array(
				'tab'     => 'security',
				'type'    => 'secret',
				'default' => '',
				'label'   => __( 'Turnstile Secret Key', 'alynt-account-gateway' ),
			),
			'reoon_api_key'                         => array(
				'tab'     => 'security',
				'type'    => 'secret',
				'default' => '',
				'label'   => __( 'Reoon API Key', 'alynt-account-gateway' ),
			),
			'reoon_mode'                            => array(
				'tab'     => 'security',
				'type'    => 'select',
				'default' => 'quick',
				'label'   => __( 'Reoon Verification Mode', 'alynt-account-gateway' ),
			),
			'registration_rate_limit_count'         => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 5,
				'label'   => __( 'Registration Attempts Per Window', 'alynt-account-gateway' ),
			),
			'registration_rate_limit_window'        => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 60,
				'label'   => __( 'Registration Rate Limit Window Minutes', 'alynt-account-gateway' ),
			),
			'resend_confirmation_rate_limit_count'  => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 5,
				'label'   => __( 'Confirmation Resend Attempts Per Window', 'alynt-account-gateway' ),
			),
			'resend_confirmation_rate_limit_window' => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 60,
				'label'   => __( 'Confirmation Resend Rate Limit Window Minutes', 'alynt-account-gateway' ),
			),
			'login_rate_limit_count'                => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 10,
				'label'   => __( 'Login Attempts Per Window', 'alynt-account-gateway' ),
			),
			'login_rate_limit_window'               => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 15,
				'label'   => __( 'Login Rate Limit Window Minutes', 'alynt-account-gateway' ),
			),
			'lostpassword_rate_limit_count'         => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 5,
				'label'   => __( 'Password Reset Attempts Per Window', 'alynt-account-gateway' ),
			),
			'lostpassword_rate_limit_window'        => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 60,
				'label'   => __( 'Password Reset Rate Limit Window Minutes', 'alynt-account-gateway' ),
			),
			'dashboard_enabled'                     => array(
				'tab'     => 'dashboard',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Enable Custom Dashboard', 'alynt-account-gateway' ),
			),
			'woocommerce_takeover'                  => array(
				'tab'     => 'woocommerce',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Take Over WooCommerce My Account', 'alynt-account-gateway' ),
			),
			'account_created_webhook'               => array(
				'tab'     => 'webhooks',
				'type'    => 'url',
				'default' => '',
				'label'   => __( 'Account Created Webhook URL', 'alynt-account-gateway' ),
			),
			'debug_payload_logging'                 => array(
				'tab'     => 'webhooks',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Debug Payload Logging', 'alynt-account-gateway' ),
			),
			'diagnostics_enabled'                   => array(
				'tab'     => 'advanced_tools',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Enable Diagnostics', 'alynt-account-gateway' ),
			),
			'diagnostics_min_level'                 => array(
				'tab'     => 'advanced_tools',
				'type'    => 'select',
				'default' => 'warning',
				'label'   => __( 'Diagnostics Minimum Level', 'alynt-account-gateway' ),
			),
			'diagnostics_retention'                 => array(
				'tab'     => 'advanced_tools',
				'type'    => 'integer',
				'default' => 30,
				'label'   => __( 'Diagnostics Retention Days', 'alynt-account-gateway' ),
			),
			'success_log_retention'                 => array(
				'tab'     => 'privacy',
				'type'    => 'integer',
				'default' => 7,
				'label'   => __( 'Successful Webhook Log Retention Days', 'alynt-account-gateway' ),
			),
			'failed_log_retention'                  => array(
				'tab'     => 'privacy',
				'type'    => 'integer',
				'default' => 30,
				'label'   => __( 'Failed Webhook Log Retention Days', 'alynt-account-gateway' ),
			),
			'verification_log_retention'            => array(
				'tab'     => 'privacy',
				'type'    => 'integer',
				'default' => 30,
				'label'   => __( 'Verification Log Retention Days', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Return defaults.
	 *
	 * @return array<string,mixed>
	 */
	public static function defaults() {
		$defaults = array();

		foreach ( self::schema() as $key => $field ) {
			$defaults[ $key ] = $field['default'];
		}

		if ( empty( $defaults['emergency_bypass_key'] ) && function_exists( 'wp_generate_password' ) ) {
			$defaults['emergency_bypass_key'] = wp_generate_password( 32, false, false );
		}

		return $defaults;
	}

	/**
	 * Return settings.
	 *
	 * @return array<string,mixed>
	 */
	public static function get_settings() {
		$saved = get_option( 'alynt_ag_settings', array() );

		if ( ! is_array( $saved ) ) {
			$saved = array();
		}

		return array_merge( self::defaults(), $saved );
	}

	/**
	 * Sanitize settings.
	 *
	 * @param array<string,mixed> $input Raw settings.
	 * @return array<string,mixed>
	 */
	public static function sanitize( $input ) {
		$current   = self::get_settings();
		$schema    = self::schema();
		$sanitized = $current;

		if ( ! is_array( $input ) ) {
			return $sanitized;
		}

		foreach ( $schema as $key => $field ) {
			if ( ! array_key_exists( $key, $input ) ) {
				continue;
			}

			$sanitized[ $key ] = self::sanitize_value( $input[ $key ], $field['type'] );
		}

		return $sanitized;
	}

	/**
	 * Sanitize one value by schema type.
	 *
	 * @param mixed  $value Raw value.
	 * @param string $type  Field type.
	 * @return mixed
	 */
	private static function sanitize_value( $value, $type ) {
		switch ( $type ) {
			case 'boolean':
				return (bool) $value;
			case 'integer':
			case 'attachment_id':
				return max( 0, absint( $value ) );
			case 'relative_path':
				$path = '/' . ltrim( sanitize_text_field( wp_unslash( $value ) ), '/' );
				return strtok( $path, '?' );
			case 'color':
				$color = sanitize_hex_color( $value );
				return $color ? $color : '';
			case 'url':
				return esc_url_raw( $value );
			case 'css_font_family':
				$font_stack = sanitize_text_field( wp_unslash( $value ) );
				$font_stack = preg_replace( '/[^a-zA-Z0-9\\s,_"\'\\-]/', '', $font_stack );
				return $font_stack ? $font_stack : '';
			case 'textarea':
				return wp_kses_post( wp_unslash( $value ) );
			case 'secret':
			case 'select':
			case 'string':
			default:
				return sanitize_text_field( wp_unslash( $value ) );
		}
	}
}
