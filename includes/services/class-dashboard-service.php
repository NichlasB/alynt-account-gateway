<?php
/**
 * Dashboard service.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides account dashboard metadata.
 */
class ALYNT_AG_Dashboard_Service {

	/**
	 * Return default dashboard links.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return array<int,array<string,mixed>>
	 */
	public function default_links( $settings = array() ) {
		$woocommerce = new ALYNT_AG_WooCommerce_Integration();
		if ( $this->woocommerce_available() ) {
			return $woocommerce->account_menu_links( $settings );
		}

		$base  = ! empty( $settings['after_login_redirect'] ) ? $settings['after_login_redirect'] : '/my-account/';
		$links = array(
			array(
				'label'  => __( 'Account Details', 'alynt-account-gateway' ),
				'url'    => trailingslashit( $base ) . 'edit-account/',
				'icon'   => 'user',
				'order'  => 20,
				'target' => '_self',
				'roles'  => array(),
			),
			array(
				'label'  => __( 'Log Out', 'alynt-account-gateway' ),
				'url'    => wp_logout_url( home_url( $settings['login_path'] ?? '/login' ) ),
				'icon'   => 'logout',
				'order'  => 900,
				'target' => '_self',
				'roles'  => array(),
			),
		);

		return $links;
	}

	/**
	 * Return dashboard links for a user.
	 *
	 * @param WP_User             $user     User object.
	 * @param array<string,mixed> $settings Settings.
	 * @return array<int,array<string,mixed>>
	 */
	public function links_for_user( $user, $settings ) {
		$links = array_merge(
			$this->default_links( $settings ),
			$this->custom_links( $settings['dashboard_custom_links'] ?? '[]' )
		);
		$links = array_map( array( $this, 'normalize_link' ), $links );
		$links = array_filter(
			$links,
			function ( $link ) use ( $user ) {
				return ! empty( $link['label'] ) && ! empty( $link['url'] ) && $this->user_can_see_link( $user, $link );
			}
		);

		usort(
			$links,
			static function ( $left, $right ) {
				if ( $left['order'] === $right['order'] ) {
					return strcmp( $left['label'], $right['label'] );
				}

				return $left['order'] <=> $right['order'];
			}
		);

		return array_values( $links );
	}

	/**
	 * Parse custom dashboard links from settings.
	 *
	 * @param mixed $raw_links Raw custom links JSON or array.
	 * @return array<int,array<string,mixed>>
	 */
	public function custom_links( $raw_links ) {
		if ( is_string( $raw_links ) ) {
			$decoded   = json_decode( $raw_links, true );
			$raw_links = is_array( $decoded ) ? $decoded : array();
		}

		if ( ! is_array( $raw_links ) ) {
			return array();
		}

		return array_values(
			array_filter(
				$raw_links,
				static function ( $link ) {
					return is_array( $link );
				}
			)
		);
	}

	/**
	 * Normalize one link.
	 *
	 * @param array<string,mixed> $link Link data.
	 * @return array<string,mixed>
	 */
	public function normalize_link( $link ) {
		$roles = isset( $link['roles'] ) && is_array( $link['roles'] ) ? $link['roles'] : array();

		return array(
			'label'  => sanitize_text_field( $link['label'] ?? '' ),
			'url'    => $this->normalize_url( $link['url'] ?? '' ),
			'icon'   => sanitize_key( $link['icon'] ?? 'link' ),
			'order'  => isset( $link['order'] ) ? (int) $link['order'] : 100,
			'target' => '_blank' === ( $link['target'] ?? '' ) ? '_blank' : '_self',
			'roles'  => array_values( array_filter( array_map( 'sanitize_key', $roles ) ) ),
		);
	}

	/**
	 * Normalize internal paths and absolute URLs.
	 *
	 * @param mixed $url URL or path.
	 * @return string
	 */
	public function normalize_url( $url ) {
		$url = trim( (string) $url );

		if ( '' === $url ) {
			return '';
		}

		if ( 0 === strpos( $url, '/' ) ) {
			return home_url( $url );
		}

		return esc_url_raw( $url );
	}

	/**
	 * Determine whether a user can see a link.
	 *
	 * @param WP_User             $user User object.
	 * @param array<string,mixed> $link Link data.
	 * @return bool
	 */
	public function user_can_see_link( $user, $link ) {
		if ( empty( $link['roles'] ) ) {
			return true;
		}

		$user_roles = isset( $user->roles ) && is_array( $user->roles ) ? $user->roles : array();

		return (bool) array_intersect( $link['roles'], $user_roles );
	}

	/**
	 * Whether WooCommerce is available.
	 *
	 * @return bool
	 */
	public function woocommerce_available() {
		return class_exists( 'WooCommerce' ) || function_exists( 'wc_get_account_menu_items' );
	}
}
