<?php
/**
 * Frontend URL adapter.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adapts WordPress authentication URLs to branded gateway routes.
 */
class ALYNT_AG_Frontend_Url_Adapter {

	/**
	 * Route helper.
	 *
	 * @var ALYNT_AG_Frontend_Routes
	 */
	private $routes;

	/**
	 * Constructor.
	 *
	 * @param ALYNT_AG_Frontend_Routes $routes Route helper.
	 */
	public function __construct( $routes ) {
		$this->routes = $routes;
	}

	/**
	 * Filter the WordPress login URL.
	 *
	 * @param string $login_url    Native login URL.
	 * @param string $redirect     Redirect URL.
	 * @param bool   $force_reauth Whether to force reauthentication.
	 * @return string
	 */
	public function filter_login_url( $login_url, $redirect, $force_reauth ) {
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		return empty( $settings['frontend_enabled'] )
			? $login_url
			: $this->routes->login_url( $settings, $redirect, $force_reauth );
	}

	/**
	 * Filter the lost password URL.
	 *
	 * @param string $lostpassword_url Native URL.
	 * @param string $redirect         Redirect URL.
	 * @return string
	 */
	public function filter_lostpassword_url( $lostpassword_url, $redirect ) {
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		return empty( $settings['frontend_enabled'] )
			? $lostpassword_url
			: $this->routes->lostpassword_url( $settings, $redirect );
	}

	/**
	 * Filter the registration URL.
	 *
	 * @param string $register_url Native URL.
	 * @return string
	 */
	public function filter_register_url( $register_url ) {
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		return empty( $settings['frontend_enabled'] )
			? $register_url
			: $this->routes->register_url( $settings );
	}

	/**
	 * Filter the logout URL.
	 *
	 * @param string $logout_url Native URL.
	 * @param string $redirect   Redirect URL.
	 * @return string
	 */
	public function filter_logout_url( $logout_url, $redirect ) {
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		return empty( $settings['frontend_enabled'] )
			? $logout_url
			: $this->routes->logout_url( $settings, $redirect );
	}
}
