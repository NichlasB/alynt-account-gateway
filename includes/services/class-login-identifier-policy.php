<?php
/**
 * Login identifier policy.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalizes and validates submitted branded-login identifiers.
 */
class ALYNT_AG_Login_Identifier_Policy {

	/**
	 * Normalize the submitted login identifier according to the configured mode.
	 *
	 * @param mixed               $value    Raw submitted identifier.
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	public static function normalize( $value, $settings ) {
		$value = trim( (string) $value );

		if ( is_email( $value ) ) {
			return sanitize_email( $value );
		}

		if ( self::usernames_are_allowed( $settings ) ) {
			return sanitize_user( $value );
		}

		return sanitize_text_field( $value );
	}

	/**
	 * Return whether the submitted identifier is allowed for branded login.
	 *
	 * @param string              $identifier Submitted identifier.
	 * @param array<string,mixed> $settings   Settings.
	 * @return bool
	 */
	public static function is_allowed( $identifier, $settings ) {
		if ( '' === $identifier ) {
			return false;
		}

		if ( is_email( $identifier ) ) {
			return true;
		}

		return self::usernames_are_allowed( $settings ) && '' !== sanitize_user( $identifier );
	}

	/**
	 * Return whether username login is enabled.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return bool
	 */
	public static function usernames_are_allowed( $settings ) {
		return ! empty( $settings['login_identifier_mode'] ) && 'email_or_username' === $settings['login_identifier_mode'];
	}
}
