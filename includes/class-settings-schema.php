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
			'frontend_enabled'                          => array(
				'tab'     => 'general',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Enable Frontend Output', 'alynt-account-gateway' ),
			),
			'login_path'                                => array(
				'tab'     => 'urls',
				'type'    => 'relative_path',
				'default' => '/login',
				'label'   => __( 'Login URL Path', 'alynt-account-gateway' ),
			),
			'account_action_base'                       => array(
				'tab'     => 'urls',
				'type'    => 'relative_path',
				'default' => '/account',
				'label'   => __( 'Account Action Base', 'alynt-account-gateway' ),
			),
			'after_login_redirect'                      => array(
				'tab'     => 'urls',
				'type'    => 'relative_path',
				'default' => '/my-account/',
				'label'   => __( 'After Login Redirect', 'alynt-account-gateway' ),
			),
			'administrator_after_login_redirect'        => array(
				'tab'     => 'urls',
				'type'    => 'relative_path',
				'default' => '/wp-admin/',
				'label'   => __( 'Administrator After Login Redirect', 'alynt-account-gateway' ),
			),
			'shop_manager_after_login_redirect'         => array(
				'tab'     => 'urls',
				'type'    => 'relative_path',
				'default' => '/wp-admin/',
				'label'   => __( 'Shop Manager After Login Redirect', 'alynt-account-gateway' ),
			),
			'emergency_bypass_key'                      => array(
				'tab'     => 'advanced_tools',
				'type'    => 'secret',
				'default' => '',
				'label'   => __( 'Emergency Bypass Key', 'alynt-account-gateway' ),
			),
			'registration_enabled'                      => array(
				'tab'     => 'registration',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Enable Public Account Creation', 'alynt-account-gateway' ),
			),
			'registration_token_hours'                  => array(
				'tab'     => 'registration',
				'type'    => 'integer',
				'default' => 24,
				'label'   => __( 'Pending Registration Expiry Hours', 'alynt-account-gateway' ),
			),
			'username_format'                           => array(
				'tab'     => 'registration',
				'type'    => 'string',
				'default' => '@User_{first_name}_{last_name}',
				'label'   => __( 'Generated Username Format', 'alynt-account-gateway' ),
			),
			'terms_path'                                => array(
				'tab'     => 'registration',
				'type'    => 'relative_path',
				'default' => '/legal/terms/',
				'label'   => __( 'Terms URL Path', 'alynt-account-gateway' ),
			),
			'privacy_path'                              => array(
				'tab'     => 'registration',
				'type'    => 'relative_path',
				'default' => '/legal/privacy/',
				'label'   => __( 'Privacy URL Path', 'alynt-account-gateway' ),
			),
			'login_intro_text'                          => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'Welcome back. Log in to manage your orders and account details.', 'alynt-account-gateway' ),
				'label'   => __( 'Login Instruction Text', 'alynt-account-gateway' ),
			),
			'register_intro_text'                       => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'Create your customer account. Fill in your details and you will receive a confirmation email. Be sure to check your spam folder if you do not see it.', 'alynt-account-gateway' ),
				'label'   => __( 'Registration Instruction Text', 'alynt-account-gateway' ),
			),
			'lostpassword_intro_text'                   => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'Enter the email address associated with your account and we will send a link to reset your password.', 'alynt-account-gateway' ),
				'label'   => __( 'Lost Password Instruction Text', 'alynt-account-gateway' ),
			),
			'setpassword_intro_text'                    => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'Choose a new password for your account.', 'alynt-account-gateway' ),
				'label'   => __( 'Set Password Instruction Text', 'alynt-account-gateway' ),
			),
			'logout_intro_text'                         => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'Are you sure you want to log out of your account?', 'alynt-account-gateway' ),
				'label'   => __( 'Logout Confirmation Instruction Text', 'alynt-account-gateway' ),
			),
			'registration_disabled_text'                => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'New account registration is currently unavailable. Please check back later, or log in if you already have an account.', 'alynt-account-gateway' ),
				'label'   => __( 'Registration Disabled Text', 'alynt-account-gateway' ),
			),
			'invalid_link_text'                         => array(
				'tab'     => 'copy',
				'type'    => 'textarea',
				'default' => __( 'This confirmation link is invalid or has expired. Request a new one below.', 'alynt-account-gateway' ),
				'label'   => __( 'Invalid Or Expired Link Text', 'alynt-account-gateway' ),
			),
			'brand_logo_id'                             => array(
				'tab'     => 'branding',
				'type'    => 'attachment_id',
				'default' => 0,
				'label'   => __( 'Brand Logo', 'alynt-account-gateway' ),
			),
			'brand_logo_max_width'                      => array(
				'tab'     => 'branding',
				'type'    => 'integer',
				'default' => 220,
				'label'   => __( 'Logo Max Width', 'alynt-account-gateway' ),
			),
			'primary_color'                             => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#3B5249',
				'label'   => __( 'Primary Color', 'alynt-account-gateway' ),
			),
			'accent_color'                              => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#E1CDB5',
				'label'   => __( 'Accent Color', 'alynt-account-gateway' ),
			),
			'text_color'                                => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#281408',
				'label'   => __( 'Text Color', 'alynt-account-gateway' ),
			),
			'page_background_color'                     => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#EAE4D6',
				'label'   => __( 'Page Background Color', 'alynt-account-gateway' ),
			),
			'surface_color'                             => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#FFFFFF',
				'label'   => __( 'Card Surface Color', 'alynt-account-gateway' ),
			),
			'error_color'                               => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#B3492E',
				'label'   => __( 'Error Color', 'alynt-account-gateway' ),
			),
			'button_background_color'                   => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#3B5249',
				'label'   => __( 'Button Background Color', 'alynt-account-gateway' ),
			),
			'button_text_color'                         => array(
				'tab'     => 'branding',
				'type'    => 'color',
				'default' => '#ffffff',
				'label'   => __( 'Button Text Color', 'alynt-account-gateway' ),
			),
			'background_image_id'                       => array(
				'tab'     => 'branding',
				'type'    => 'attachment_id',
				'default' => 0,
				'label'   => __( 'Gateway Background Image', 'alynt-account-gateway' ),
			),
			'heading_font_family'                       => array(
				'tab'     => 'branding',
				'type'    => 'css_font_family',
				'default' => 'Georgia, serif',
				'label'   => __( 'Heading Font Stack', 'alynt-account-gateway' ),
			),
			'body_font_family'                          => array(
				'tab'     => 'branding',
				'type'    => 'css_font_family',
				'default' => '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
				'label'   => __( 'Body Font Stack', 'alynt-account-gateway' ),
			),
			'protection_mode'                           => array(
				'tab'     => 'security',
				'type'    => 'select',
				'default' => 'turnstile_or_reoon',
				'label'   => __( 'Registration Protection Mode', 'alynt-account-gateway' ),
				'options' => array(
					'turnstile_or_reoon'  => __( 'Either configured provider can pass', 'alynt-account-gateway' ),
					'turnstile_and_reoon' => __( 'Every configured provider must pass', 'alynt-account-gateway' ),
				),
			),
			'turnstile_site_key'                        => array(
				'tab'     => 'security',
				'type'    => 'string',
				'default' => '',
				'label'   => __( 'Turnstile Site Key', 'alynt-account-gateway' ),
			),
			'turnstile_secret_key'                      => array(
				'tab'     => 'security',
				'type'    => 'secret',
				'default' => '',
				'label'   => __( 'Turnstile Secret Key', 'alynt-account-gateway' ),
			),
			'reoon_api_key'                             => array(
				'tab'     => 'security',
				'type'    => 'secret',
				'default' => '',
				'label'   => __( 'Reoon API Key', 'alynt-account-gateway' ),
			),
			'reoon_mode'                                => array(
				'tab'     => 'security',
				'type'    => 'select',
				'default' => 'quick',
				'label'   => __( 'Reoon Verification Mode', 'alynt-account-gateway' ),
				'options' => array(
					'quick' => __( 'Quick', 'alynt-account-gateway' ),
					'power' => __( 'Power', 'alynt-account-gateway' ),
				),
			),
			'reoon_flagged_policy'                      => array(
				'tab'     => 'security',
				'type'    => 'select',
				'default' => 'allow',
				'label'   => __( 'Reoon Flagged Status Policy', 'alynt-account-gateway' ),
				'options' => array(
					'allow' => __( 'Allow and log flagged statuses', 'alynt-account-gateway' ),
					'block' => __( 'Block flagged statuses', 'alynt-account-gateway' ),
				),
			),
			'registration_rate_limit_count'             => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 5,
				'label'   => __( 'Registration Attempts Per Window', 'alynt-account-gateway' ),
			),
			'registration_rate_limit_window'            => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 60,
				'label'   => __( 'Registration Rate Limit Window Minutes', 'alynt-account-gateway' ),
			),
			'resend_confirmation_rate_limit_count'      => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 5,
				'label'   => __( 'Confirmation Resend Attempts Per Window', 'alynt-account-gateway' ),
			),
			'resend_confirmation_rate_limit_window'     => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 60,
				'label'   => __( 'Confirmation Resend Rate Limit Window Minutes', 'alynt-account-gateway' ),
			),
			'login_rate_limit_count'                    => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 10,
				'label'   => __( 'Login Attempts Per Window', 'alynt-account-gateway' ),
			),
			'login_rate_limit_window'                   => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 15,
				'label'   => __( 'Login Rate Limit Window Minutes', 'alynt-account-gateway' ),
			),
			'lostpassword_rate_limit_count'             => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 5,
				'label'   => __( 'Password Reset Attempts Per Window', 'alynt-account-gateway' ),
			),
			'lostpassword_rate_limit_window'            => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 60,
				'label'   => __( 'Password Reset Rate Limit Window Minutes', 'alynt-account-gateway' ),
			),
			'email_registration_confirmation_subject'   => array(
				'tab'     => 'emails',
				'type'    => 'string',
				'default' => __( 'Confirm your account for {{site_name}}', 'alynt-account-gateway' ),
				'label'   => __( 'Registration Confirmation Subject', 'alynt-account-gateway' ),
			),
			'email_registration_confirmation_preheader' => array(
				'tab'     => 'emails',
				'type'    => 'string',
				'default' => __( 'Confirm your email address and choose a password.', 'alynt-account-gateway' ),
				'label'   => __( 'Registration Confirmation Preheader', 'alynt-account-gateway' ),
			),
			'email_registration_confirmation_body'      => array(
				'tab'     => 'emails',
				'type'    => 'rich_text',
				'default' => __( "Hi {{first_name}},\n\nWelcome to {{site_name}}. Confirm your email address and choose a password using the button below.\n\nThis link expires in {{expiry_hours}} hours.", 'alynt-account-gateway' ),
				'label'   => __( 'Registration Confirmation Body', 'alynt-account-gateway' ),
			),
			'email_password_reset_subject'              => array(
				'tab'     => 'emails',
				'type'    => 'string',
				'default' => __( 'Reset your password for {{site_name}}', 'alynt-account-gateway' ),
				'label'   => __( 'Password Reset Subject', 'alynt-account-gateway' ),
			),
			'email_password_reset_preheader'            => array(
				'tab'     => 'emails',
				'type'    => 'string',
				'default' => __( 'Use this secure link to choose a new password.', 'alynt-account-gateway' ),
				'label'   => __( 'Password Reset Preheader', 'alynt-account-gateway' ),
			),
			'email_password_reset_body'                 => array(
				'tab'     => 'emails',
				'type'    => 'rich_text',
				'default' => __( "Hi {{first_name}},\n\nWe received a request to reset the password for your {{site_name}} account. Use the button below to choose a new password.\n\nIf you did not request this, you can ignore this email.", 'alynt-account-gateway' ),
				'label'   => __( 'Password Reset Body', 'alynt-account-gateway' ),
			),
			'email_password_changed_disabled'           => array(
				'tab'     => 'emails',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Disable Password Changed Email', 'alynt-account-gateway' ),
			),
			'email_password_changed_subject'            => array(
				'tab'     => 'emails',
				'type'    => 'string',
				'default' => __( 'Your {{site_name}} password was changed', 'alynt-account-gateway' ),
				'label'   => __( 'Password Changed Subject', 'alynt-account-gateway' ),
			),
			'email_password_changed_preheader'          => array(
				'tab'     => 'emails',
				'type'    => 'string',
				'default' => __( 'This confirms that your account password was updated.', 'alynt-account-gateway' ),
				'label'   => __( 'Password Changed Preheader', 'alynt-account-gateway' ),
			),
			'email_password_changed_body'               => array(
				'tab'     => 'emails',
				'type'    => 'rich_text',
				'default' => __( "Hi {{first_name}},\n\nThis is a confirmation that the password for your {{site_name}} account was changed.\n\nIf you did not make this change, please contact the site owner right away.", 'alynt-account-gateway' ),
				'label'   => __( 'Password Changed Body', 'alynt-account-gateway' ),
			),
			'email_new_user_welcome_disabled'           => array(
				'tab'     => 'emails',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Disable Account Created Welcome Email', 'alynt-account-gateway' ),
			),
			'email_new_user_welcome_subject'            => array(
				'tab'     => 'emails',
				'type'    => 'string',
				'default' => __( 'Welcome to {{site_name}}', 'alynt-account-gateway' ),
				'label'   => __( 'Account Created Welcome Subject', 'alynt-account-gateway' ),
			),
			'email_new_user_welcome_preheader'          => array(
				'tab'     => 'emails',
				'type'    => 'string',
				'default' => __( 'Your account is ready.', 'alynt-account-gateway' ),
				'label'   => __( 'Account Created Welcome Preheader', 'alynt-account-gateway' ),
			),
			'email_new_user_welcome_body'               => array(
				'tab'     => 'emails',
				'type'    => 'rich_text',
				'default' => __( "Hi {{first_name}},\n\nYour {{site_name}} account has been created successfully. You can now log in, manage your details, and access your customer dashboard.\n\nUse the button below to visit your account.", 'alynt-account-gateway' ),
				'label'   => __( 'Account Created Welcome Body', 'alynt-account-gateway' ),
			),
			'email_change_confirmation_disabled'        => array(
				'tab'     => 'emails',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Disable Email Change Confirmation Email', 'alynt-account-gateway' ),
			),
			'email_change_confirmation_subject'         => array(
				'tab'     => 'emails',
				'type'    => 'string',
				'default' => __( 'Confirm your email address for {{site_name}}', 'alynt-account-gateway' ),
				'label'   => __( 'Email Change Confirmation Subject', 'alynt-account-gateway' ),
			),
			'email_change_confirmation_preheader'       => array(
				'tab'     => 'emails',
				'type'    => 'string',
				'default' => __( 'Confirm this email address to finish updating your account.', 'alynt-account-gateway' ),
				'label'   => __( 'Email Change Confirmation Preheader', 'alynt-account-gateway' ),
			),
			'email_change_confirmation_body'            => array(
				'tab'     => 'emails',
				'type'    => 'rich_text',
				'default' => __( "Hi {{first_name}},\n\nConfirm this email address for your {{site_name}} account using the button below.\n\nIf you did not request this change, you can ignore this email.", 'alynt-account-gateway' ),
				'label'   => __( 'Email Change Confirmation Body', 'alynt-account-gateway' ),
			),
			'email_test_recipient'                      => array(
				'tab'     => 'emails',
				'type'    => 'email',
				'default' => '',
				'label'   => __( 'Test Email Recipient', 'alynt-account-gateway' ),
			),
			'dashboard_enabled'                         => array(
				'tab'     => 'dashboard',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Enable Custom Dashboard', 'alynt-account-gateway' ),
			),
			'dashboard_custom_links'                    => array(
				'tab'     => 'dashboard',
				'type'    => 'dashboard_links',
				'default' => '[]',
				'label'   => __( 'Custom Dashboard Links', 'alynt-account-gateway' ),
			),
			'dashboard_offcanvas_enabled'               => array(
				'tab'     => 'dashboard',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Enable Dashboard Menu Panel', 'alynt-account-gateway' ),
			),
			'dashboard_offcanvas_menu_id'               => array(
				'tab'     => 'dashboard',
				'type'    => 'nav_menu',
				'default' => 0,
				'label'   => __( 'Dashboard Menu Panel Menu', 'alynt-account-gateway' ),
			),
			'dashboard_footer_menu_enabled'             => array(
				'tab'     => 'dashboard',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Enable Dashboard Footer Menu', 'alynt-account-gateway' ),
			),
			'dashboard_footer_menu_id'                  => array(
				'tab'     => 'dashboard',
				'type'    => 'nav_menu',
				'default' => 0,
				'label'   => __( 'Dashboard Footer Menu', 'alynt-account-gateway' ),
			),
			'woocommerce_takeover'                      => array(
				'tab'     => 'woocommerce',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Take Over WooCommerce My Account', 'alynt-account-gateway' ),
			),
			'woocommerce_require_login_checkout'        => array(
				'tab'     => 'woocommerce',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Require Login Before Checkout', 'alynt-account-gateway' ),
			),
			'woocommerce_require_login_order_pay'       => array(
				'tab'     => 'woocommerce',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Require Login For Order Payment Links', 'alynt-account-gateway' ),
			),
			'woocommerce_hidden_menu_items'             => array(
				'tab'     => 'woocommerce',
				'type'    => 'woocommerce_menu_visibility',
				'default' => array(),
				'label'   => __( 'Dashboard Navigation Items', 'alynt-account-gateway' ),
			),
			'account_created_webhook'                   => array(
				'tab'     => 'webhooks',
				'type'    => 'url',
				'default' => '',
				'label'   => __( 'Account Created Webhook URL', 'alynt-account-gateway' ),
			),
			'webhook_signing_secret'                    => array(
				'tab'     => 'webhooks',
				'type'    => 'secret',
				'default' => '',
				'label'   => __( 'Webhook Signing Secret', 'alynt-account-gateway' ),
			),
			'debug_payload_logging'                     => array(
				'tab'     => 'webhooks',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Debug Payload Logging', 'alynt-account-gateway' ),
			),
			'diagnostics_enabled'                       => array(
				'tab'     => 'advanced_tools',
				'type'    => 'boolean',
				'default' => false,
				'label'   => __( 'Enable Diagnostics', 'alynt-account-gateway' ),
			),
			'diagnostics_min_level'                     => array(
				'tab'     => 'advanced_tools',
				'type'    => 'select',
				'default' => 'warning',
				'label'   => __( 'Diagnostics Minimum Level', 'alynt-account-gateway' ),
			),
			'diagnostics_retention'                     => array(
				'tab'     => 'advanced_tools',
				'type'    => 'integer',
				'default' => 30,
				'label'   => __( 'Diagnostics Retention Days', 'alynt-account-gateway' ),
			),
			'success_log_retention'                     => array(
				'tab'     => 'privacy',
				'type'    => 'integer',
				'default' => 7,
				'label'   => __( 'Successful Webhook Log Retention Days', 'alynt-account-gateway' ),
			),
			'failed_log_retention'                      => array(
				'tab'     => 'privacy',
				'type'    => 'integer',
				'default' => 30,
				'label'   => __( 'Failed Webhook Log Retention Days', 'alynt-account-gateway' ),
			),
			'verification_log_retention'                => array(
				'tab'     => 'privacy',
				'type'    => 'integer',
				'default' => 30,
				'label'   => __( 'Verification Log Retention Days', 'alynt-account-gateway' ),
			),
			'consent_record_retention'                  => array(
				'tab'     => 'privacy',
				'type'    => 'integer',
				'default' => 365,
				'label'   => __( 'Consent Record Retention Days', 'alynt-account-gateway' ),
			),
			'audit_log_retention'                       => array(
				'tab'     => 'privacy',
				'type'    => 'integer',
				'default' => 180,
				'label'   => __( 'Audit Log Retention Days', 'alynt-account-gateway' ),
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
	 * Return schema keys for one settings tab.
	 *
	 * @param string $tab Settings tab key.
	 * @return array<int,string>
	 */
	public static function keys_for_tab( $tab ) {
		$keys = array();

		foreach ( self::schema() as $key => $field ) {
			if ( isset( $field['tab'] ) && $field['tab'] === $tab ) {
				$keys[] = $key;
			}
		}

		return $keys;
	}

	/**
	 * Return default values for one settings tab.
	 *
	 * @param string $tab Settings tab key.
	 * @return array<string,mixed>
	 */
	public static function defaults_for_tab( $tab ) {
		$defaults     = self::defaults();
		$tab_defaults = array();

		foreach ( self::keys_for_tab( $tab ) as $key ) {
			$tab_defaults[ $key ] = $defaults[ $key ];
		}

		return $tab_defaults;
	}

	/**
	 * Restore one settings tab to its schema defaults.
	 *
	 * @param string $tab Settings tab key.
	 * @return array<string,mixed>|WP_Error
	 */
	public static function restore_tab_defaults( $tab ) {
		$tabs = self::tabs();

		if ( ! isset( $tabs[ $tab ] ) ) {
			return new WP_Error(
				'alynt_ag_invalid_settings_tab',
				__( 'The selected settings tab is invalid.', 'alynt-account-gateway' )
			);
		}

		$tab_defaults = self::defaults_for_tab( $tab );

		if ( empty( $tab_defaults ) ) {
			return new WP_Error(
				'alynt_ag_empty_settings_tab',
				__( 'The selected settings tab does not contain restorable settings.', 'alynt-account-gateway' )
			);
		}

		return array_merge( self::get_settings(), $tab_defaults );
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
	 * Create a portable settings export package.
	 *
	 * @return array<string,mixed>
	 */
	public static function export_package() {
		return array(
			'plugin'     => 'alynt-account-gateway',
			'version'    => defined( 'ALYNT_AG_VERSION' ) ? ALYNT_AG_VERSION : '',
			'exportedAt' => gmdate( 'c' ),
			'settings'   => self::portable_settings(),
		);
	}

	/**
	 * Return settings that are safe and useful to move between sites.
	 *
	 * @return array<string,mixed>
	 */
	private static function portable_settings() {
		$settings = self::get_settings();

		foreach ( self::schema() as $key => $field ) {
			$type = isset( $field['type'] ) ? (string) $field['type'] : '';

			if ( in_array( $type, array( 'secret', 'email', 'attachment_id', 'nav_menu' ), true ) ) {
				unset( $settings[ $key ] );
			}
		}

		return $settings;
	}

	/**
	 * Inspect a settings import package without saving it.
	 *
	 * @param string $json Raw JSON package.
	 * @return array<string,mixed>|WP_Error
	 */
	public static function inspect_import_package( $json ) {
		$package = json_decode( (string) $json, true );

		if ( ! is_array( $package ) ) {
			return new WP_Error(
				'alynt_ag_invalid_settings_import',
				__( 'The selected settings file is not valid JSON.', 'alynt-account-gateway' )
			);
		}

		$settings = isset( $package['settings'] ) && is_array( $package['settings'] ) ? $package['settings'] : $package;
		$known    = self::filter_known_settings( $settings );
		$unknown  = array();

		foreach ( $settings as $key => $value ) {
			unset( $value );

			if ( ! array_key_exists( $key, $known ) ) {
				$unknown[] = (string) $key;
			}
		}

		if ( empty( $known ) ) {
			return new WP_Error(
				'alynt_ag_empty_settings_import',
				__( 'The selected settings file does not contain any recognized plugin settings.', 'alynt-account-gateway' )
			);
		}

		return array(
			'plugin'        => isset( $package['plugin'] ) && is_scalar( $package['plugin'] ) ? sanitize_text_field( (string) $package['plugin'] ) : '',
			'version'       => isset( $package['version'] ) && is_scalar( $package['version'] ) ? sanitize_text_field( (string) $package['version'] ) : '',
			'exported_at'   => isset( $package['exportedAt'] ) && is_scalar( $package['exportedAt'] ) ? sanitize_text_field( (string) $package['exportedAt'] ) : '',
			'known_keys'    => array_keys( $known ),
			'unknown_keys'  => $unknown,
			'known_count'   => count( $known ),
			'unknown_count' => count( $unknown ),
		);
	}

	/**
	 * Parse and sanitize a settings import package.
	 *
	 * @param string $json Raw JSON package.
	 * @return array<string,mixed>|WP_Error
	 */
	public static function import_package( $json ) {
		$inspection = self::inspect_import_package( $json );

		if ( is_wp_error( $inspection ) ) {
			return $inspection;
		}

		$package = json_decode( (string) $json, true );

		$settings = isset( $package['settings'] ) && is_array( $package['settings'] ) ? $package['settings'] : $package;
		$settings = self::filter_known_settings( $settings );

		return self::sanitize( $settings );
	}

	/**
	 * Keep only settings that belong to this plugin schema.
	 *
	 * @param array<string,mixed> $settings Candidate settings.
	 * @return array<string,mixed>
	 */
	public static function filter_known_settings( $settings ) {
		$schema = self::schema();
		$known  = array();

		foreach ( $settings as $key => $value ) {
			if ( array_key_exists( $key, $schema ) ) {
				$known[ $key ] = $value;
			}
		}

		return $known;
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

			$sanitized[ $key ] = self::sanitize_value( $input[ $key ], $field );
		}

		return $sanitized;
	}

	/**
	 * Sanitize one value by schema type.
	 *
	 * @param mixed               $value Raw value.
	 * @param array<string,mixed> $field Field schema.
	 * @return mixed
	 */
	private static function sanitize_value( $value, $field ) {
		$type = isset( $field['type'] ) ? (string) $field['type'] : 'string';

		switch ( $type ) {
			case 'boolean':
				return (bool) $value;
			case 'integer':
			case 'attachment_id':
			case 'nav_menu':
				return max( 0, absint( $value ) );
			case 'relative_path':
				$path = '/' . ltrim( sanitize_text_field( wp_unslash( $value ) ), '/' );
				return strtok( $path, '?' );
			case 'color':
				$color = sanitize_hex_color( $value );
				return $color ? $color : '';
			case 'url':
				return esc_url_raw( $value );
			case 'email':
				return sanitize_email( $value );
			case 'css_font_family':
				$font_stack = sanitize_text_field( wp_unslash( $value ) );
				$font_stack = preg_replace( '/[^a-zA-Z0-9\\s,_"\'\\-]/', '', $font_stack );
				return $font_stack ? $font_stack : '';
			case 'dashboard_links':
				return self::sanitize_dashboard_links( $value );
			case 'woocommerce_menu_visibility':
				return self::sanitize_woocommerce_hidden_menu_items( $value );
			case 'rich_text':
			case 'textarea':
				return wp_kses_post( wp_unslash( $value ) );
			case 'select':
				$value = sanitize_key( wp_unslash( $value ) );

				if ( ! empty( $field['options'] ) && is_array( $field['options'] ) ) {
					return array_key_exists( $value, $field['options'] ) ? $value : $field['default'];
				}

				return $value;
			case 'secret':
			case 'string':
			default:
				return sanitize_text_field( wp_unslash( $value ) );
		}
	}

	/**
	 * Sanitize dashboard custom links into the stored JSON format.
	 *
	 * @param mixed $value Raw dashboard links JSON or array.
	 * @return string
	 */
	private static function sanitize_dashboard_links( $value ) {
		if ( is_string( $value ) ) {
			$decoded = json_decode( wp_unslash( $value ), true );
			$value   = is_array( $decoded ) ? $decoded : array();
		}

		if ( ! is_array( $value ) ) {
			$value = array();
		}

		$links = array();

		foreach ( $value as $link ) {
			if ( ! is_array( $link ) ) {
				continue;
			}

			$label = sanitize_text_field( wp_strip_all_tags( wp_unslash( $link['label'] ?? '' ) ) );
			$url   = esc_url_raw( trim( (string) wp_unslash( $link['url'] ?? '' ) ) );

			if ( '' === $label || '' === $url ) {
				continue;
			}

			$roles = isset( $link['roles'] ) && is_array( $link['roles'] ) ? $link['roles'] : array();
			$roles = array_values( array_filter( array_map( 'sanitize_key', $roles ) ) );

			$links[] = array(
				'label'  => $label,
				'url'    => $url,
				'icon'   => sanitize_key( $link['icon'] ?? 'link' ),
				'order'  => isset( $link['order'] ) ? max( 0, (int) $link['order'] ) : 100,
				'target' => '_blank' === ( $link['target'] ?? '' ) ? '_blank' : '_self',
				'roles'  => $roles,
			);
		}

		$json = wp_json_encode( $links, JSON_UNESCAPED_SLASHES );

		return is_string( $json ) ? $json : '[]';
	}

	/**
	 * Sanitize WooCommerce endpoint visibility into a list of hidden keys.
	 *
	 * Associative checkbox input uses a truthy value for hidden items. Indexed
	 * input is also accepted so portable imports remain straightforward.
	 *
	 * @param mixed $value Raw endpoint visibility input.
	 * @return array<int,string>
	 */
	private static function sanitize_woocommerce_hidden_menu_items( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}

		$hidden = array();

		foreach ( $value as $key => $flag ) {
			if ( is_int( $key ) ) {
				$endpoint  = sanitize_key( wp_unslash( $flag ) );
				$is_hidden = true;
			} else {
				$endpoint  = sanitize_key( wp_unslash( $key ) );
				$is_hidden = ! empty( $flag );
			}

			if ( $endpoint && $is_hidden ) {
				$hidden[] = $endpoint;
			}
		}

		return array_values( array_unique( $hidden ) );
	}
}
