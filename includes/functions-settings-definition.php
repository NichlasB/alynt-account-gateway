<?php
/**
 * Settings definition helper functions.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'alynt_ag_schema_text' ) ) {
	/**
	 * Translate settings-schema text only after WordPress is ready for i18n.
	 *
	 * Settings can be read by early authentication, routing, and CLI paths. Calling
	 * gettext before `init` triggers WordPress's just-in-time translation warning,
	 * so schema strings stay in their source language until the i18n layer is ready.
	 *
	 * @param string $text   Text to translate.
	 * @param string $domain Text domain.
	 * @return string
	 */
	function alynt_ag_schema_text( $text, $domain = 'alynt-account-gateway' ) {
		if ( did_action( 'init' ) ) {
			// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText,WordPress.WP.I18n.NonSingularStringLiteralDomain -- Settings schema passes literal source strings through this delayed i18n helper.
			return __( $text, $domain );
		}

		return $text;
	}
}
