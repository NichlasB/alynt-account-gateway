<?php
/**
 * URL, navigation, and routing test stubs.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! function_exists( 'add_query_arg' ) ) {
	function add_query_arg( $args, $value = '', $url = '' ) {
		if ( is_string( $args ) ) {
			$args = array( $args => $value );
		} else {
			$url = $value;
		}

		$separator = false === strpos( $url, '?' ) ? '?' : '&';
		return $url . $separator . http_build_query( $args );
	}
}

if ( ! function_exists( 'home_url' ) ) {
	function home_url( $path = '' ) {
		return 'https://example.test' . $path;
	}
}

if ( ! function_exists( 'admin_url' ) ) {
	function admin_url( $path = '' ) {
		return 'https://example.test/wp-admin/' . ltrim( (string) $path, '/' );
	}
}

if ( ! function_exists( 'wp_get_nav_menus' ) ) {
	function wp_get_nav_menus() {
		return isset( $GLOBALS['alynt_ag_test_nav_menus'] ) ? $GLOBALS['alynt_ag_test_nav_menus'] : array();
	}
}

if ( ! function_exists( 'wp_nav_menu' ) ) {
	function wp_nav_menu( $args = array() ) {
		$class = isset( $args['menu_class'] ) ? $args['menu_class'] : 'menu';
		$html  = '<ul class="' . esc_attr( $class ) . '">';
		$html .= '<li class="menu-item"><a href="https://example.test/shop/">Shop</a></li>';
		$html .= '<li class="menu-item"><a href="https://example.test/contact/">Contact</a></li>';
		$html .= '</ul>';

		if ( isset( $args['echo'] ) && false === $args['echo'] ) {
			return $html;
		}

		echo $html;
	}
}

if ( ! function_exists( 'trailingslashit' ) ) {
	function trailingslashit( $value ) {
		return rtrim( (string) $value, '/' ) . '/';
	}
}

if ( ! function_exists( 'untrailingslashit' ) ) {
	function untrailingslashit( $value ) {
		return rtrim( (string) $value, '/' );
	}
}

if ( ! function_exists( 'wp_logout_url' ) ) {
	function wp_logout_url( $redirect = '' ) {
		$url = 'https://example.test/wp-login.php?action=logout';

		return $redirect ? add_query_arg( 'redirect_to', $redirect, $url ) : $url;
	}
}

if ( ! function_exists( 'wp_nonce_url' ) ) {
	function wp_nonce_url( $actionurl, $action = -1, $name = '_wpnonce' ) {
		return add_query_arg( $name, 'test-nonce', $actionurl );
	}
}

if ( ! function_exists( 'wp_nonce_field' ) ) {
	function wp_nonce_field( $action = -1, $name = '_wpnonce', $referer = true, $display = true ) {
		$field = '<input type="hidden" name="' . esc_attr( $name ) . '" value="test-nonce">';
		if ( $display ) {
			echo $field;
		}

		return $field;
	}
}

if ( ! function_exists( 'wp_doing_ajax' ) ) {
	function wp_doing_ajax() {
		return ! empty( $GLOBALS['alynt_ag_test_doing_ajax'] );
	}
}

if ( ! function_exists( 'is_user_logged_in' ) ) {
	function is_user_logged_in() {
		return ! empty( $GLOBALS['alynt_ag_test_user_logged_in'] );
	}
}

if ( ! function_exists( 'is_checkout' ) ) {
	function is_checkout() {
		return ! empty( $GLOBALS['alynt_ag_test_is_checkout'] );
	}
}

if ( ! function_exists( 'is_wc_endpoint_url' ) ) {
	function is_wc_endpoint_url( $endpoint = '' ) {
		$current = $GLOBALS['alynt_ag_test_wc_endpoint'] ?? '';

		return $endpoint ? $endpoint === $current : '' !== $current;
	}
}

if ( ! function_exists( 'wc_get_checkout_url' ) ) {
	function wc_get_checkout_url() {
		return $GLOBALS['alynt_ag_test_checkout_url'] ?? 'https://example.test/checkout/';
	}
}

if ( ! function_exists( 'wc_get_endpoint_url' ) ) {
	function wc_get_endpoint_url( $endpoint, $value = '', $permalink = '' ) {
		$base = $permalink ? trailingslashit( $permalink ) : 'https://example.test/';
		$url  = $base . trim( (string) $endpoint, '/' ) . '/';

		return $value ? $url . trim( (string) $value, '/' ) . '/' : $url;
	}
}

if ( ! function_exists( 'current_user_can' ) ) {
	function current_user_can( $capability ) {
		return in_array( $capability, $GLOBALS['alynt_ag_test_user_caps'] ?? array(), true );
	}
}

if ( ! function_exists( 'get_current_user_id' ) ) {
	function get_current_user_id() {
		return isset( $GLOBALS['alynt_ag_test_current_user_id'] ) ? absint( $GLOBALS['alynt_ag_test_current_user_id'] ) : 0;
	}
}

if ( ! function_exists( 'wp_safe_redirect' ) ) {
	function wp_safe_redirect( $location, $status = 302, $x_redirect_by = 'WordPress' ) {
		$GLOBALS['alynt_ag_test_redirects'][] = array(
			'location'      => $location,
			'status'        => $status,
			'x_redirect_by' => $x_redirect_by,
		);

		if ( ! empty( $GLOBALS['alynt_ag_test_throw_on_redirect'] ) ) {
			throw new RuntimeException( 'redirect:' . $location );
		}

		return true;
	}
}
