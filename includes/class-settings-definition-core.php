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
				'label'   => alynt_ag_schema_text( 'Enable Frontend Output', 'alynt-account-gateway' ),
			),
			'login_path'                         => array(
				'tab'     => 'urls',
				'type'    => 'relative_path',
				'default' => '/login',
				'label'   => alynt_ag_schema_text( 'Login URL Path', 'alynt-account-gateway' ),
			),
			'account_action_base'                => array(
				'tab'     => 'urls',
				'type'    => 'relative_path',
				'default' => '/account',
				'label'   => alynt_ag_schema_text( 'Account Action Base', 'alynt-account-gateway' ),
			),
			'after_login_redirect'               => array(
				'tab'     => 'urls',
				'type'    => 'relative_path',
				'default' => '/my-account/',
				'label'   => alynt_ag_schema_text( 'After Login Redirect', 'alynt-account-gateway' ),
			),
			'administrator_after_login_redirect' => array(
				'tab'     => 'urls',
				'type'    => 'relative_path',
				'default' => '/wp-admin/',
				'label'   => alynt_ag_schema_text( 'Administrator After Login Redirect', 'alynt-account-gateway' ),
			),
			'shop_manager_after_login_redirect'  => array(
				'tab'     => 'urls',
				'type'    => 'relative_path',
				'default' => '/wp-admin/',
				'label'   => alynt_ag_schema_text( 'Shop Manager After Login Redirect', 'alynt-account-gateway' ),
			),
			'emergency_bypass_key'               => array(
				'tab'     => 'advanced_tools',
				'type'    => 'secret',
				'default' => '',
				'label'   => alynt_ag_schema_text( 'Emergency Bypass Key', 'alynt-account-gateway' ),
			),
			'custom_css'                         => array(
				'tab'     => 'advanced_tools',
				'type'    => 'css',
				'default' => '',
				'label'   => alynt_ag_schema_text( 'Custom CSS', 'alynt-account-gateway' ),
			),
			'registration_enabled'               => array(
				'tab'     => 'registration',
				'type'    => 'boolean',
				'default' => false,
				'label'   => alynt_ag_schema_text( 'Enable Public Account Creation', 'alynt-account-gateway' ),
			),
			'registration_token_hours'           => array(
				'tab'     => 'registration',
				'type'    => 'integer',
				'default' => 24,
				'min'     => 1,
				'max'     => 168,
				'label'   => alynt_ag_schema_text( 'Pending Registration Expiry Hours', 'alynt-account-gateway' ),
			),
			'username_format'                    => array(
				'tab'     => 'registration',
				'type'    => 'string',
				'default' => '@User_{first_name}_{last_name}',
				'label'   => alynt_ag_schema_text( 'Generated Username Format', 'alynt-account-gateway' ),
			),
			'terms_path'                         => array(
				'tab'     => 'registration',
				'type'    => 'relative_path',
				'default' => '/legal/terms/',
				'label'   => alynt_ag_schema_text( 'Terms URL Path', 'alynt-account-gateway' ),
			),
			'privacy_path'                       => array(
				'tab'     => 'registration',
				'type'    => 'relative_path',
				'default' => '/legal/privacy/',
				'label'   => alynt_ag_schema_text( 'Privacy URL Path', 'alynt-account-gateway' ),
			),
			'login_intro_text'                   => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => alynt_ag_schema_text( 'Welcome back. Log in to access your account.', 'alynt-account-gateway' ),
				'label'   => alynt_ag_schema_text( 'Login Instruction Text', 'alynt-account-gateway' ),
			),
			'register_intro_text'                => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => alynt_ag_schema_text( 'Create your customer account. Fill in your details and you will receive a confirmation email. Be sure to check your spam folder if you do not see it.', 'alynt-account-gateway' ),
				'label'   => alynt_ag_schema_text( 'Registration Instruction Text', 'alynt-account-gateway' ),
			),
			'lostpassword_intro_text'            => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => alynt_ag_schema_text( 'Enter the email address associated with your account and we will send a link to reset your password.', 'alynt-account-gateway' ),
				'label'   => alynt_ag_schema_text( 'Lost Password Instruction Text', 'alynt-account-gateway' ),
			),
			'setpassword_intro_text'             => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => alynt_ag_schema_text( 'Choose a new password for your account.', 'alynt-account-gateway' ),
				'label'   => alynt_ag_schema_text( 'Set Password Instruction Text', 'alynt-account-gateway' ),
			),
			'logout_intro_text'                  => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => alynt_ag_schema_text( 'Are you sure you want to log out of your account?', 'alynt-account-gateway' ),
				'label'   => alynt_ag_schema_text( 'Logout Confirmation Instruction Text', 'alynt-account-gateway' ),
			),
			'registration_disabled_text'         => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => alynt_ag_schema_text( 'New account registration is currently unavailable. Please check back later, or log in if you already have an account.', 'alynt-account-gateway' ),
				'label'   => alynt_ag_schema_text( 'Registration Disabled Text', 'alynt-account-gateway' ),
			),
			'invalid_link_text'                  => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => alynt_ag_schema_text( 'This confirmation link is invalid or has expired. Request a new one below.', 'alynt-account-gateway' ),
				'label'   => alynt_ag_schema_text( 'Invalid Or Expired Link Text', 'alynt-account-gateway' ),
			),
			'brand_logo_id'                      => array(
				'tab'     => 'branding',
				'type'    => 'attachment_id',
				'default' => 0,
				'label'   => alynt_ag_schema_text( 'Brand Logo', 'alynt-account-gateway' ),
			),
			'brand_logo_max_width'               => array(
				'tab'     => 'branding',
				'type'    => 'integer',
				'default' => 220,
				'min'     => 80,
				'max'     => 520,
				'label'   => alynt_ag_schema_text( 'Logo Max Width', 'alynt-account-gateway' ),
			),
			'primary_color'                      => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#3B5249',
				'label'   => alynt_ag_schema_text( 'Primary Color', 'alynt-account-gateway' ),
			),
			'accent_color'                       => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#E1CDB5',
				'label'   => alynt_ag_schema_text( 'Accent Color', 'alynt-account-gateway' ),
			),
			'text_color'                         => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#281408',
				'label'   => alynt_ag_schema_text( 'Text Color', 'alynt-account-gateway' ),
			),
			'page_background_color'              => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#EAE4D6',
				'label'   => alynt_ag_schema_text( 'Page Background Color', 'alynt-account-gateway' ),
			),
			'surface_color'                      => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#FFFFFF',
				'label'   => alynt_ag_schema_text( 'Card Surface Color', 'alynt-account-gateway' ),
			),
			'error_color'                        => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#B3492E',
				'label'   => alynt_ag_schema_text( 'Error Color', 'alynt-account-gateway' ),
			),
			'button_background_color'            => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#3B5249',
				'label'   => alynt_ag_schema_text( 'Button Background Color', 'alynt-account-gateway' ),
			),
			'button_text_color'                  => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#ffffff',
				'label'   => alynt_ag_schema_text( 'Button Text Color', 'alynt-account-gateway' ),
			),
			'background_image_id'                => array(
				'tab'     => 'branding',
				'type'    => 'attachment_id',
				'default' => 0,
				'label'   => alynt_ag_schema_text( 'Gateway Background Image', 'alynt-account-gateway' ),
			),
			'heading_font_family'                => array(
				'tab'     => 'branding',
				'type'    => 'css_font_family',
				'default' => 'Georgia, serif',
				'label'   => alynt_ag_schema_text( 'Heading Font Stack', 'alynt-account-gateway' ),
			),
			'body_font_family'                   => array(
				'tab'     => 'branding',
				'type'    => 'css_font_family',
				'default' => '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
				'label'   => alynt_ag_schema_text( 'Body Font Stack', 'alynt-account-gateway' ),
			),
		);
	}
}
