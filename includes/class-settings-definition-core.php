<?php
/**
 * Core, URL, registration, copy, and branding settings definitions.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Core, URL, registration, copy, and branding settings definitions.
 */
class ALYNT_AG_Settings_Definition_Core {

	/**
	 * Return this provider's ordered settings fields.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function fields() {
		return array(
			'frontend_enabled'                   => array(
				'tab'     => 'general',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Enable Frontend Output', 'alynt-account-gateway' ),
			),
			'login_path'                         => array(
				'tab'     => 'urls',
				'type'    => 'relative_path',
				'default' => '/login',
				'label'   => __( 'Login URL Path', 'alynt-account-gateway' ),
			),
			'account_action_base'                => array(
				'tab'     => 'urls',
				'type'    => 'relative_path',
				'default' => '/account',
				'label'   => __( 'Account Action Base', 'alynt-account-gateway' ),
			),
			'after_login_redirect'               => array(
				'tab'     => 'urls',
				'type'    => 'relative_path',
				'default' => '/my-account/',
				'label'   => __( 'After Login Redirect', 'alynt-account-gateway' ),
			),
			'administrator_after_login_redirect' => array(
				'tab'     => 'urls',
				'type'    => 'relative_path',
				'default' => '/wp-admin/',
				'label'   => __( 'Administrator After Login Redirect', 'alynt-account-gateway' ),
			),
			'shop_manager_after_login_redirect'  => array(
				'tab'     => 'urls',
				'type'    => 'relative_path',
				'default' => '/wp-admin/',
				'label'   => __( 'Shop Manager After Login Redirect', 'alynt-account-gateway' ),
			),
			'emergency_bypass_key'               => array(
				'tab'     => 'advanced_tools',
				'type'    => 'secret',
				'default' => '',
				'label'   => __( 'Emergency Bypass Key', 'alynt-account-gateway' ),
			),
			'registration_enabled'               => array(
				'tab'     => 'registration',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Enable Public Account Creation', 'alynt-account-gateway' ),
			),
			'registration_token_hours'           => array(
				'tab'     => 'registration',
				'type'    => 'integer',
				'default' => 24,
				'label'   => __( 'Pending Registration Expiry Hours', 'alynt-account-gateway' ),
			),
			'username_format'                    => array(
				'tab'     => 'registration',
				'type'    => 'string',
				'default' => '@User_{first_name}_{last_name}',
				'label'   => __( 'Generated Username Format', 'alynt-account-gateway' ),
			),
			'terms_path'                         => array(
				'tab'     => 'registration',
				'type'    => 'relative_path',
				'default' => '/legal/terms/',
				'label'   => __( 'Terms URL Path', 'alynt-account-gateway' ),
			),
			'privacy_path'                       => array(
				'tab'     => 'registration',
				'type'    => 'relative_path',
				'default' => '/legal/privacy/',
				'label'   => __( 'Privacy URL Path', 'alynt-account-gateway' ),
			),
			'login_intro_text'                   => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'Welcome back. Log in to manage your orders and account details.', 'alynt-account-gateway' ),
				'label'   => __( 'Login Instruction Text', 'alynt-account-gateway' ),
			),
			'register_intro_text'                => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'Create your customer account. Fill in your details and you will receive a confirmation email. Be sure to check your spam folder if you do not see it.', 'alynt-account-gateway' ),
				'label'   => __( 'Registration Instruction Text', 'alynt-account-gateway' ),
			),
			'lostpassword_intro_text'            => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'Enter the email address associated with your account and we will send a link to reset your password.', 'alynt-account-gateway' ),
				'label'   => __( 'Lost Password Instruction Text', 'alynt-account-gateway' ),
			),
			'setpassword_intro_text'             => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'Choose a new password for your account.', 'alynt-account-gateway' ),
				'label'   => __( 'Set Password Instruction Text', 'alynt-account-gateway' ),
			),
			'logout_intro_text'                  => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'Are you sure you want to log out of your account?', 'alynt-account-gateway' ),
				'label'   => __( 'Logout Confirmation Instruction Text', 'alynt-account-gateway' ),
			),
			'registration_disabled_text'         => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'New account registration is currently unavailable. Please check back later, or log in if you already have an account.', 'alynt-account-gateway' ),
				'label'   => __( 'Registration Disabled Text', 'alynt-account-gateway' ),
			),
			'invalid_link_text'                  => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'This confirmation link is invalid or has expired. Request a new one below.', 'alynt-account-gateway' ),
				'label'   => __( 'Invalid Or Expired Link Text', 'alynt-account-gateway' ),
			),
			'brand_logo_id'                      => array(
				'tab'     => 'branding',
				'type'    => 'attachment_id',
				'default' => 0,
				'label'   => __( 'Brand Logo', 'alynt-account-gateway' ),
			),
			'brand_logo_max_width'               => array(
				'tab'     => 'branding',
				'type'    => 'integer',
				'default' => 220,
				'label'   => __( 'Logo Max Width', 'alynt-account-gateway' ),
			),
			'primary_color'                      => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#3B5249',
				'label'   => __( 'Primary Color', 'alynt-account-gateway' ),
			),
			'accent_color'                       => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#E1CDB5',
				'label'   => __( 'Accent Color', 'alynt-account-gateway' ),
			),
			'text_color'                         => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#281408',
				'label'   => __( 'Text Color', 'alynt-account-gateway' ),
			),
			'page_background_color'              => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#EAE4D6',
				'label'   => __( 'Page Background Color', 'alynt-account-gateway' ),
			),
			'surface_color'                      => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#FFFFFF',
				'label'   => __( 'Card Surface Color', 'alynt-account-gateway' ),
			),
			'error_color'                        => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#B3492E',
				'label'   => __( 'Error Color', 'alynt-account-gateway' ),
			),
			'button_background_color'            => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#3B5249',
				'label'   => __( 'Button Background Color', 'alynt-account-gateway' ),
			),
			'button_text_color'                  => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#ffffff',
				'label'   => __( 'Button Text Color', 'alynt-account-gateway' ),
			),
			'background_image_id'                => array(
				'tab'     => 'branding',
				'type'    => 'attachment_id',
				'default' => 0,
				'label'   => __( 'Gateway Background Image', 'alynt-account-gateway' ),
			),
			'heading_font_family'                => array(
				'tab'     => 'branding',
				'type'    => 'css_font_family',
				'default' => 'Georgia, serif',
				'label'   => __( 'Heading Font Stack', 'alynt-account-gateway' ),
			),
			'body_font_family'                   => array(
				'tab'     => 'branding',
				'type'    => 'css_font_family',
				'default' => '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
				'label'   => __( 'Body Font Stack', 'alynt-account-gateway' ),
			),
		);
	}
}
