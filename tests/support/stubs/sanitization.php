<?php
/**
 * Sanitization and redirect-validation test stubs.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! function_exists( 'sanitize_hex_color' ) ) {
	function sanitize_hex_color( $value ) {
		return preg_match( '/^#[a-fA-F0-9]{6}$/', (string) $value ) ? $value : '';
	}
}

if ( ! function_exists( 'wp_unslash' ) ) {
	function wp_unslash( $value ) {
		return $value;
	}
}

if ( ! function_exists( 'esc_url_raw' ) ) {
	function esc_url_raw( $value ) {
		return filter_var( $value, FILTER_SANITIZE_URL );
	}
}

if ( ! function_exists( 'wp_parse_url' ) ) {
	function wp_parse_url( $url, $component = -1 ) {
		return parse_url( $url, $component );
	}
}

if ( ! function_exists( 'wp_validate_redirect' ) ) {
	function wp_validate_redirect( $location, $fallback_url = '' ) {
		if ( '' === (string) $location ) {
			return $fallback_url;
		}

		$host = parse_url( $location, PHP_URL_HOST );

		return in_array( $host, array( 'example.test', null ), true ) ? $location : $fallback_url;
	}
}

if ( ! function_exists( 'wp_kses_post' ) ) {
	function wp_kses_post( $value ) {
		$value = preg_replace( '#<(script|style|iframe|form)\b[^>]*>.*?</\1>#is', '', (string) $value );
		$value = strip_tags( (string) $value, '<a><abbr><b><blockquote><br><cite><code><del><em><h1><h2><h3><h4><h5><h6><hr><i><ins><li><ol><p><pre><q><s><small><span><strong><sub><sup><u><ul>' );
		$value = preg_replace_callback(
			'/<[^>]+>/',
			static function ( $matches ) {
				$tag = preg_replace( '/\s+on[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $matches[0] );
				return preg_replace( '/\s+(href|src)\s*=\s*(["\'])\s*javascript:[^"\']*\2/i', '', (string) $tag );
			},
			(string) $value
		);

		return (string) $value;
	}
}
