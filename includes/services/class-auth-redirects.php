<?php
/**
 * Resolves role-aware authentication redirects.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolves role-aware authentication redirects.
 */
class ALYNT_AG_Auth_Redirects extends ALYNT_AG_Service_Collaborator {

	/**
	 * Return destination helper.
	 *
	 * @var ALYNT_AG_Return_Destination
	 */
	private $destinations;

	/**
	 * Constructor.
	 *
	 * @param object                      $service      Public service facade.
	 * @param ALYNT_AG_Return_Destination $destinations Return destination helper.
	 */
	public function __construct( $service, $destinations ) {
		parent::__construct( $service );
		$this->destinations = $destinations;
	}

	/**
	 * Return a safe login redirect URL.
	 *
	 * @param string              $redirect_to Submitted redirect URL.
	 * @param array<string,mixed> $settings    Settings.
	 * @param WP_User|null        $user        Authenticated user, when available.
	 * @return string
	 */
	public function run_get_login_redirect_url( $redirect_to, $settings, $user = null ) {
		$default = home_url( $this->get_default_login_redirect_path( $settings, $user ) );

		if ( '' === (string) $redirect_to ) {
			return $default;
		}

		return $this->destinations->absolute_url( $redirect_to, $settings, $default );
	}

	/**
	 * Return the configured role-aware default login redirect path.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @param WP_User|null        $user     Authenticated user, when available.
	 * @return string
	 */
	private function get_default_login_redirect_path( $settings, $user = null ) {
		$roles = $user instanceof WP_User && is_array( $user->roles ) ? $user->roles : array();

		if ( in_array( 'administrator', $roles, true ) ) {
			return $settings['administrator_after_login_redirect'] ?? '/wp-admin/';
		}

		if ( in_array( 'shop_manager', $roles, true ) ) {
			return $settings['shop_manager_after_login_redirect'] ?? '/wp-admin/';
		}

		return $settings['after_login_redirect'] ?? '/my-account/';
	}
}
