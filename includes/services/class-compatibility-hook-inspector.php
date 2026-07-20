<?php
/**
 * Compatibility hook inspector.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Finds third-party callbacks attached to account-related hooks.
 */
class ALYNT_AG_Compatibility_Hook_Inspector {

	/**
	 * Return third-party callback summaries for hooks.
	 *
	 * @param array<int,string> $hooks Hook names.
	 * @return array<int,string>
	 */
	public function third_party_callbacks_for_hooks( $hooks ) {
		global $wp_filter;

		if ( empty( $wp_filter ) || ! is_array( $wp_filter ) ) {
			return array();
		}

		$callbacks = array();

		foreach ( $hooks as $hook ) {
			if ( empty( $wp_filter[ $hook ] ) ) {
				continue;
			}

			foreach ( $this->extract_callbacks( $wp_filter[ $hook ] ) as $callback ) {
				if ( $this->is_own_callback( $callback ) || $this->is_platform_callback( $callback ) ) {
					continue;
				}

				$callbacks[] = $hook . ':' . $this->callback_label( $callback );
			}
		}

		return array_values( array_unique( $callbacks ) );
	}

	/**
	 * Extract callbacks from WP_Hook or array-shaped hook storage.
	 *
	 * @param mixed $hook Hook storage.
	 * @return array<int,mixed>
	 */
	private function extract_callbacks( $hook ) {
		$callbacks = array();
		$groups    = array();

		if ( is_object( $hook ) && isset( $hook->callbacks ) && is_array( $hook->callbacks ) ) {
			$groups = $hook->callbacks;
		} elseif ( is_array( $hook ) ) {
			$groups = $hook;
		}

		foreach ( $groups as $priority_callbacks ) {
			if ( ! is_array( $priority_callbacks ) ) {
				continue;
			}

			foreach ( $priority_callbacks as $callback ) {
				$callbacks[] = is_array( $callback ) && array_key_exists( 'function', $callback )
					? $callback['function']
					: $callback;
			}
		}

		return $callbacks;
	}

	/**
	 * Whether callback belongs to this plugin.
	 *
	 * @param mixed $callback Callback.
	 * @return bool
	 */
	private function is_own_callback( $callback ) {
		if ( is_array( $callback ) && isset( $callback[0] ) ) {
			$target = is_object( $callback[0] ) ? get_class( $callback[0] ) : (string) $callback[0];
			return 0 === strpos( $target, 'ALYNT_AG_' );
		}

		if ( is_string( $callback ) ) {
			return 0 === strpos( $callback, 'alynt_ag_' ) || 0 === strpos( $callback, 'ALYNT_AG_' );
		}

		return false;
	}

	/**
	 * Whether callback is expected WordPress or WooCommerce platform behavior.
	 *
	 * @param mixed $callback Callback.
	 * @return bool
	 */
	private function is_platform_callback( $callback ) {
		if ( is_array( $callback ) && isset( $callback[0] ) ) {
			$target = is_object( $callback[0] ) ? get_class( $callback[0] ) : (string) $callback[0];

			return 0 === strpos( $target, 'WP_' )
				|| 0 === strpos( $target, 'Automattic\\WooCommerce\\' )
				|| 0 === strpos( $target, 'WC_' );
		}

		if ( ! is_string( $callback ) ) {
			return false;
		}

		if (
			0 === strpos( $callback, 'wp_' )
			|| 0 === strpos( $callback, '_wp_' )
			|| 0 === strpos( $callback, '_maybe_update_' )
			|| 0 === strpos( $callback, 'rest_' )
			|| 0 === strpos( $callback, 'wc_' )
			|| 0 === strpos( $callback, 'woocommerce_' )
		) {
			return true;
		}

		return in_array(
			$callback,
			array(
				'_wp_admin_bar_init',
				'redirect_canonical',
				'send_frame_options_header',
				'wp_admin_headers',
				'wp_maybe_update_user_counts',
				'wp_old_slug_redirect',
			),
			true
		);
	}

	/**
	 * Return a compact callback label.
	 *
	 * @param mixed $callback Callback.
	 * @return string
	 */
	private function callback_label( $callback ) {
		if ( is_string( $callback ) ) {
			return $callback;
		}

		if ( is_array( $callback ) && isset( $callback[0], $callback[1] ) ) {
			$target = is_object( $callback[0] ) ? get_class( $callback[0] ) : (string) $callback[0];
			return $target . '::' . (string) $callback[1];
		}

		return $callback instanceof Closure ? 'closure' : 'callback';
	}
}
