<?php
/**
 * Core WordPress test stubs.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! function_exists( '__' ) ) {
	function __( $text, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( 'esc_html' ) ) {
	function esc_html( $value ) {
		return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_attr' ) ) {
	function esc_attr( $value ) {
		return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_textarea' ) ) {
	function esc_textarea( $value ) {
		return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'selected' ) ) {
	function selected( $selected, $current = true, $display = true ) {
		$result = (string) $selected === (string) $current ? ' selected="selected"' : '';

		if ( $display ) {
			echo $result; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static test stub output.
		}

		return $result;
	}
}

if ( ! function_exists( 'esc_html_e' ) ) {
	function esc_html_e( $text, $domain = 'default' ) {
		echo esc_html( $text );
	}
}

if ( ! function_exists( 'esc_html__' ) ) {
	function esc_html__( $text, $domain = 'default' ) {
		return esc_html( $text );
	}
}

if ( ! function_exists( 'esc_attr_e' ) ) {
	function esc_attr_e( $text, $domain = 'default' ) {
		echo esc_attr( $text );
	}
}

if ( ! function_exists( 'esc_attr__' ) ) {
	function esc_attr__( $text, $domain = 'default' ) {
		return esc_attr( $text );
	}
}

if ( ! function_exists( 'esc_url' ) ) {
	function esc_url( $value ) {
		return filter_var( $value, FILTER_SANITIZE_URL );
	}
}

if ( ! function_exists( 'is_wp_error' ) ) {
	function is_wp_error( $thing ) {
		return $thing instanceof WP_Error;
	}
}

if ( ! function_exists( 'absint' ) ) {
	function absint( $value ) {
		return abs( (int) $value );
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $value ) {
		return trim( (string) $value );
	}
}

if ( ! function_exists( 'sanitize_key' ) ) {
	function sanitize_key( $value ) {
		return strtolower( preg_replace( '/[^a-zA-Z0-9_-]/', '', (string) $value ) );
	}
}

if ( ! function_exists( 'sanitize_email' ) ) {
	function sanitize_email( $value ) {
		return trim( strtolower( (string) $value ) );
	}
}

if ( ! function_exists( 'sanitize_user' ) ) {
	function sanitize_user( $username, $strict = false ) {
		$username = preg_replace( '/\s+/', '_', (string) $username );
		return preg_replace( '/[^A-Za-z0-9_@.-]/', '', $username );
	}
}

if ( ! function_exists( 'is_email' ) ) {
	function is_email( $value ) {
		return false !== filter_var( $value, FILTER_VALIDATE_EMAIL );
	}
}

if ( ! function_exists( 'is_rtl' ) ) {
	function is_rtl() {
		return ! empty( $GLOBALS['alynt_ag_test_is_rtl'] );
	}
}

if ( ! function_exists( 'wp_salt' ) ) {
	function wp_salt( $scheme = 'auth' ) {
		return 'test-salt-' . $scheme;
	}
}

if ( ! function_exists( 'wp_generate_password' ) ) {
	function wp_generate_password( $length = 12, $special_chars = true, $extra_special_chars = false ) {
		return substr( str_repeat( 'Abc12345', 8 ), 0, $length );
	}
}

if ( ! function_exists( 'current_time' ) ) {
	function current_time( $type, $gmt = false ) {
		if ( $gmt && isset( $GLOBALS['alynt_ag_test_current_time_utc'] ) ) {
			return $GLOBALS['alynt_ag_test_current_time_utc'];
		}

		if ( ! $gmt && isset( $GLOBALS['alynt_ag_test_current_time_local'] ) ) {
			return $GLOBALS['alynt_ag_test_current_time_local'];
		}

		return '2026-07-03 12:00:00';
	}
}

if ( ! function_exists( 'date_i18n' ) ) {
	function date_i18n( $format, $timestamp = false, $gmt = false ) {
		return gmdate( (string) $format, $timestamp ? (int) $timestamp : time() );
	}
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $value, $flags = 0, $depth = 512 ) {
		return json_encode( $value, $flags, $depth );
	}
}

if ( ! function_exists( 'maybe_unserialize' ) ) {
	function maybe_unserialize( $value ) {
		if ( ! is_string( $value ) ) {
			return $value;
		}

		$trimmed = trim( $value );
		if ( 'N;' === $trimmed ) {
			return null;
		}

		if ( ! preg_match( '/^(a|O|s|i|b|d):/', $trimmed ) ) {
			return $value;
		}

		$data = unserialize( $value );

		return false !== $data || 'b:0;' === $value ? $data : $value;
	}
}

if ( ! function_exists( 'wp_enqueue_style' ) ) {
	function wp_enqueue_style( $handle, $src = '', $deps = array(), $ver = false, $media = 'all' ) {
		$GLOBALS['alynt_ag_test_enqueued_styles'][] = array(
			'handle' => $handle,
			'src'    => $src,
			'deps'   => $deps,
			'ver'    => $ver,
			'media'  => $media,
		);
	}
}

if ( ! function_exists( 'wp_enqueue_script' ) ) {
	function wp_enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $in_footer = false ) {
		$GLOBALS['alynt_ag_test_enqueued_scripts'][] = array(
			'handle'    => $handle,
			'src'       => $src,
			'deps'      => $deps,
			'ver'       => $ver,
			'in_footer' => $in_footer,
		);
	}
}

if ( ! function_exists( 'wp_localize_script' ) ) {
	function wp_localize_script( $handle, $object_name, $l10n ) {
		$GLOBALS['alynt_ag_test_localized_scripts'][] = array(
			'handle'      => $handle,
			'object_name' => $object_name,
			'l10n'        => $l10n,
		);

		return true;
	}
}

if ( ! function_exists( 'get_bloginfo' ) ) {
	function get_bloginfo( $show = '' ) {
		if ( 'name' === $show ) {
			return 'Example Store';
		}

		return 'UTF-8';
	}
}

if ( ! function_exists( 'status_header' ) ) {
	function status_header( $code ) {
		$GLOBALS['alynt_ag_test_status_header'] = (int) $code;
	}
}

if ( ! function_exists( 'nocache_headers' ) ) {
	function nocache_headers() {
		$GLOBALS['alynt_ag_test_nocache_headers'] = true;
	}
}

if ( ! function_exists( 'language_attributes' ) ) {
	function language_attributes() {
		echo 'lang="en-US"';
	}
}

if ( ! function_exists( 'wp_head' ) ) {
	function wp_head() {
		echo '<!-- wp_head -->';
	}
}

if ( ! function_exists( 'wp_footer' ) ) {
	function wp_footer() {
		echo '<!-- wp_footer -->';
	}
}

if ( ! function_exists( 'wp_editor' ) ) {
	function wp_editor( $content, $editor_id, $settings = array() ) {
		$GLOBALS['alynt_ag_test_editors'][] = array(
			'content'   => $content,
			'editor_id' => $editor_id,
			'settings'  => $settings,
		);

		$name = isset( $settings['textarea_name'] ) ? $settings['textarea_name'] : $editor_id;
		echo '<div class="wp-editor-wrap"><textarea id="' . esc_attr( $editor_id ) . '" name="' . esc_attr( $name ) . '">' . esc_html( $content ) . '</textarea></div>';
	}
}

if ( ! function_exists( 'wp_specialchars_decode' ) ) {
	function wp_specialchars_decode( $value, $quote_style = ENT_QUOTES ) {
		return html_entity_decode( (string) $value, $quote_style, 'UTF-8' );
	}
}

if ( ! function_exists( 'wpautop' ) ) {
	function wpautop( $value ) {
		$paragraphs = preg_split( "/\n\s*\n/", trim( (string) $value ) );
		$paragraphs = array_map(
			static function ( $paragraph ) {
				return '<p>' . nl2br( $paragraph ) . '</p>';
			},
			$paragraphs ? $paragraphs : array()
		);

		return implode( "\n", $paragraphs );
	}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	function wp_strip_all_tags( $value ) {
		return strip_tags( (string) $value );
	}
}
