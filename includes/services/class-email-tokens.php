<?php
/**
 * Email template tokens.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides email template metadata and normalized token values.
 */
class ALYNT_AG_Email_Tokens extends ALYNT_AG_Service_Collaborator {

	/**
	 * Return supported template keys.
	 *
	 * @return array<string,string>
	 */
	public function templates() {
		return array(
			'registration_confirmation' => __( 'Registration Confirmation', 'alynt-account-gateway' ),
			'password_reset'            => __( 'Password Reset', 'alynt-account-gateway' ),
			'password_changed'          => __( 'Password Changed', 'alynt-account-gateway' ),
			'new_user_welcome'          => __( 'Account Created Welcome', 'alynt-account-gateway' ),
			'email_change_confirmation' => __( 'Email Change Confirmation', 'alynt-account-gateway' ),
		);
	}

	/**
	 * Return default preview tokens.
	 *
	 * @return array<string,string>
	 */
	public function preview_tokens() {
		return array(
			'first_name'       => __( 'Damon', 'alynt-account-gateway' ),
			'last_name'        => __( 'Paulo', 'alynt-account-gateway' ),
			'user_email'       => 'customer@example.com',
			'site_name'        => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
			'confirmation_url' => home_url( '/account?action=setpassword&alynt_ag_token=sample-token' ),
			'reset_url'        => home_url( '/account?action=setpassword&key=sample-key&login=customer%40example.com' ),
			'change_email_url' => home_url( '/account?action=confirm-email-change&key=sample-key' ),
			'dashboard_url'    => home_url( '/my-account/' ),
			'expiry_hours'     => '24',
		);
	}

	/**
	 * Return documented template tokens for admin reference.
	 *
	 * @return array<string,array<string,string>>
	 */
	public function token_reference() {
		return array(
			'first_name'       => array(
				'label'       => __( 'First name', 'alynt-account-gateway' ),
				'description' => __( 'Customer first name, with a friendly fallback when profile data is missing.', 'alynt-account-gateway' ),
			),
			'last_name'        => array(
				'label'       => __( 'Last name', 'alynt-account-gateway' ),
				'description' => __( 'Customer last name when available.', 'alynt-account-gateway' ),
			),
			'user_email'       => array(
				'label'       => __( 'Email address', 'alynt-account-gateway' ),
				'description' => __( 'Customer email address for account and confirmation context.', 'alynt-account-gateway' ),
			),
			'site_name'        => array(
				'label'       => __( 'Site name', 'alynt-account-gateway' ),
				'description' => __( 'Current WordPress site name.', 'alynt-account-gateway' ),
			),
			'confirmation_url' => array(
				'label'       => __( 'Registration confirmation URL', 'alynt-account-gateway' ),
				'description' => __( 'Email-confirmation link used before the customer sets a password.', 'alynt-account-gateway' ),
			),
			'reset_url'        => array(
				'label'       => __( 'Password reset URL', 'alynt-account-gateway' ),
				'description' => __( 'Password reset link for the branded account gateway.', 'alynt-account-gateway' ),
			),
			'change_email_url' => array(
				'label'       => __( 'Email-change confirmation URL', 'alynt-account-gateway' ),
				'description' => __( 'Confirmation link for account email address changes.', 'alynt-account-gateway' ),
			),
			'dashboard_url'    => array(
				'label'       => __( 'Dashboard URL', 'alynt-account-gateway' ),
				'description' => __( 'Destination for account dashboard or WooCommerce customer account access.', 'alynt-account-gateway' ),
			),
			'expiry_hours'     => array(
				'label'       => __( 'Confirmation expiry', 'alynt-account-gateway' ),
				'description' => __( 'Number of hours before a pending registration confirmation expires.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Build a branded reset URL.
	 *
	 * @param string              $key        Password reset key.
	 * @param string              $user_login User login.
	 * @param array<string,mixed> $settings   Settings.
	 * @return string
	 */
	public function build_reset_url( $key, $user_login, $settings ) {
		return add_query_arg(
			array(
				'action' => 'setpassword',
				'key'    => rawurlencode( $key ),
				'login'  => rawurlencode( $user_login ),
			),
			home_url( $settings['account_action_base'] )
		);
	}

	/**
	 * Return standard HTML mail headers.
	 *
	 * @return array<int,string>
	 */
	public function html_headers() {
		return array( 'Content-Type: text/html; charset=UTF-8' );
	}

	/**
	 * Return the settings prefix for a template key.
	 *
	 * @param string $template Template key.
	 * @return string
	 */
	public function settings_prefix( $template ) {
		if ( 'email_change_confirmation' === $template ) {
			return 'email_change_confirmation';
		}

		if ( 'new_user_welcome' === $template ) {
			return 'email_new_user_welcome';
		}

		return 'email_' . $template;
	}

	/**
	 * Normalize tokens and provide common defaults.
	 *
	 * @param array<string,mixed> $tokens Token values.
	 * @return array<string,string>
	 */
	public function normalize( $tokens ) {
		$tokens = array_merge( $this->service->preview_tokens(), (array) $tokens );

		foreach ( $tokens as $key => $value ) {
			$tokens[ $key ] = (string) $value;
		}

		return $tokens;
	}

	/**
	 * Build common tokens from a WordPress user object.
	 *
	 * @param WP_User             $user  User object.
	 * @param array<string,mixed> $extra Extra tokens.
	 * @return array<string,string>
	 */
	public function for_user( $user, $extra = array() ) {
		$user_id    = isset( $user->ID ) ? (int) $user->ID : 0;
		$first_name = $user_id ? get_user_meta( $user_id, 'first_name', true ) : '';
		$last_name  = $user_id ? get_user_meta( $user_id, 'last_name', true ) : '';
		$email      = isset( $user->user_email ) ? $user->user_email : '';

		return array_merge(
			array(
				'first_name' => $first_name ? $first_name : __( 'there', 'alynt-account-gateway' ),
				'last_name'  => $last_name,
				'user_email' => $email,
			),
			$extra
		);
	}

	/**
	 * Return button metadata for a template.
	 *
	 * @param string              $template Template key.
	 * @param array<string,mixed> $tokens   Token values.
	 * @return array<string,string>
	 */
	public function button( $template, $tokens ) {
		$map = array(
			'registration_confirmation' => array(
				'label' => __( 'Confirm Account', 'alynt-account-gateway' ),
				'url'   => $tokens['confirmation_url'] ?? '',
			),
			'password_reset'            => array(
				'label' => __( 'Reset Password', 'alynt-account-gateway' ),
				'url'   => $tokens['reset_url'] ?? '',
			),
			'email_change_confirmation' => array(
				'label' => __( 'Confirm Email Address', 'alynt-account-gateway' ),
				'url'   => $tokens['change_email_url'] ?? '',
			),
			'new_user_welcome'          => array(
				'label' => __( 'View Account', 'alynt-account-gateway' ),
				'url'   => $tokens['dashboard_url'] ?? '',
			),
		);

		return $map[ $template ] ?? array(
			'label' => '',
			'url'   => '',
		);
	}
}
