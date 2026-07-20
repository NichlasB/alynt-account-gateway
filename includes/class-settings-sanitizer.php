<?php
/**
 * Settings sanitization.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitizes schema-owned settings values.
 */
class ALYNT_AG_Settings_Sanitizer {

	/**
	 * Keep only values owned by the supplied schema.
	 *
	 * @param array<string,mixed>               $settings Candidate settings.
	 * @param array<string,array<string,mixed>> $schema   Settings schema.
	 * @return array<string,mixed>
	 */
	public static function filter_known_settings( $settings, $schema ) {
		$known = array();

		foreach ( $settings as $key => $value ) {
			if ( array_key_exists( $key, $schema ) ) {
				$known[ $key ] = $value;
			}
		}

		return $known;
	}

	/**
	 * Sanitize supplied values over the current settings.
	 *
	 * @param array<string,mixed>               $input    Raw settings.
	 * @param array<string,array<string,mixed>> $schema   Settings schema.
	 * @param array<string,mixed>               $current  Current settings.
	 * @return array<string,mixed>
	 */
	public static function sanitize( $input, $schema, $current ) {
		$sanitized = $current;

		if ( ! is_array( $input ) ) {
			return $sanitized;
		}

		foreach ( $schema as $key => $field ) {
			if ( ! array_key_exists( $key, $input ) ) {
				continue;
			}

			if ( 'dashboard_links' === ( $field['type'] ?? '' ) && ! self::is_dashboard_links_json_valid( $input[ $key ] ) ) {
				self::add_dashboard_links_error();
				continue;
			}

			$sanitized[ $key ] = self::sanitize_value( $input[ $key ], $field );
		}

		return $sanitized;
	}

	/**
	 * Sanitize one value according to its field definition.
	 *
	 * @param mixed               $value Raw value.
	 * @param array<string,mixed> $field Field definition.
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
		 * Return whether a raw dashboard links value can be safely imported.
		 *
		 * @param mixed $value Raw dashboard links value.
		 * @return bool
		 */
	private static function is_dashboard_links_json_valid( $value ) {
		if ( ! is_string( $value ) ) {
			return is_array( $value );
		}

		return is_array( json_decode( wp_unslash( $value ), true ) );
	}

		/**
		 * Register an admin-facing error for invalid dashboard links JSON.
		 *
		 * @return void
		 */
	private static function add_dashboard_links_error() {
		if ( function_exists( 'add_settings_error' ) ) {
			add_settings_error(
				'alynt_ag_settings',
				'alynt_ag_invalid_dashboard_links',
				__( 'Dashboard custom links were not saved because the raw JSON is invalid. The previously saved links were preserved.', 'alynt-account-gateway' ),
				'error'
			);
		}
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
