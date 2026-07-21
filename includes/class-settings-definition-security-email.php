<?php
/**
 * Security, provider, rate-limit, and email settings definitions.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Security, provider, rate-limit, and email settings definitions.
 */
class ALYNT_AG_Settings_Definition_Security_Email {

	/**
	 * Return this provider's ordered settings fields.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function fields() {
		return array(
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
				'min'     => 1,
				'max'     => 1000,
				'label'   => __( 'Registration Attempts Per Window', 'alynt-account-gateway' ),
			),
			'registration_rate_limit_window'            => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 60,
				'min'     => 1,
				'max'     => 10080,
				'label'   => __( 'Registration Rate Limit Window Minutes', 'alynt-account-gateway' ),
			),
			'resend_confirmation_rate_limit_count'      => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 5,
				'min'     => 1,
				'max'     => 1000,
				'label'   => __( 'Confirmation Resend Attempts Per Window', 'alynt-account-gateway' ),
			),
			'resend_confirmation_rate_limit_window'     => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 60,
				'min'     => 1,
				'max'     => 10080,
				'label'   => __( 'Confirmation Resend Rate Limit Window Minutes', 'alynt-account-gateway' ),
			),
			'login_rate_limit_count'                    => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 10,
				'min'     => 1,
				'max'     => 1000,
				'label'   => __( 'Login Attempts Per Window', 'alynt-account-gateway' ),
			),
			'login_rate_limit_window'                   => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 15,
				'min'     => 1,
				'max'     => 10080,
				'label'   => __( 'Login Rate Limit Window Minutes', 'alynt-account-gateway' ),
			),
			'lostpassword_rate_limit_count'             => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 5,
				'min'     => 1,
				'max'     => 1000,
				'label'   => __( 'Password Reset Attempts Per Window', 'alynt-account-gateway' ),
			),
			'lostpassword_rate_limit_window'            => array(
				'tab'     => 'security',
				'type'    => 'integer',
				'default' => 60,
				'min'     => 1,
				'max'     => 10080,
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
		);
	}
}
