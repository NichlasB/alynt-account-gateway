<?php
/**
 * Compatibility warning service.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Detects likely account-gateway compatibility overlaps.
 */
class ALYNT_AG_Compatibility_Warnings {

	/**
	 * Compatibility metadata.
	 *
	 * @var ALYNT_AG_Compatibility_Registry
	 */
	private $registry;

	/**
	 * Hook callback inspector.
	 *
	 * @var ALYNT_AG_Compatibility_Hook_Inspector
	 */
	private $hook_inspector;

	/**
	 * Constructor.
	 *
	 * @param ALYNT_AG_Compatibility_Registry|null       $registry       Compatibility metadata.
	 * @param ALYNT_AG_Compatibility_Hook_Inspector|null $hook_inspector Hook callback inspector.
	 */
	public function __construct( $registry = null, $hook_inspector = null ) {
		$this->registry       = $registry ? $registry : new ALYNT_AG_Compatibility_Registry();
		$this->hook_inspector = $hook_inspector ? $hook_inspector : new ALYNT_AG_Compatibility_Hook_Inspector();
	}

	/**
	 * Return active compatibility warnings.
	 *
	 * @param array<string,mixed>|null $settings Optional settings.
	 * @return array<int,array<string,string>>
	 */
	public function warnings( $settings = null ) {
		$settings = is_array( $settings ) ? $settings : ALYNT_AG_Settings_Schema::get_settings();
		$warnings = array_merge(
			$this->known_plugin_warnings( $this->active_plugin_basenames(), $settings ),
			$this->hook_warnings( $settings ),
			$this->woocommerce_checkout_warnings( $settings )
		);

		return $this->deduplicate_warnings( $warnings );
	}

	/**
	 * Return checkout-setting compatibility warnings.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return array<int,array<string,string>>
	 */
	public function woocommerce_checkout_warnings( $settings ) {
		if (
			empty( $settings['woocommerce_require_login_checkout'] )
			|| 'yes' !== get_option( 'woocommerce_enable_guest_checkout', 'no' )
		) {
			return array();
		}

		return array(
			array(
				'id'       => 'woocommerce_guest_checkout',
				'category' => 'woocommerce_checkout',
				'title'    => __( 'WooCommerce guest checkout is still enabled', 'alynt-account-gateway' ),
				'message'  => __( 'Alynt Account Gateway will require login before the main checkout, but WooCommerce still permits guest checkout. Review both settings and test every checkout entry point before launch.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Return warnings for known plugin overlap.
	 *
	 * @param array<int,string>        $active_plugins Active plugin basenames.
	 * @param array<string,mixed>|null $settings       Optional settings.
	 * @return array<int,array<string,string>>
	 */
	public function known_plugin_warnings( $active_plugins, $settings = null ) {
		$settings = is_array( $settings ) ? $settings : ALYNT_AG_Settings_Schema::get_settings();
		$warnings = array();
		$known    = $this->registry->known_plugins();

		foreach ( $active_plugins as $plugin ) {
			if ( ! isset( $known[ $plugin ] ) ) {
				continue;
			}

			$entry = $known[ $plugin ];
			if ( ! $this->registry->category_enabled( $entry['category'], $settings ) ) {
				continue;
			}

			$warnings[] = array(
				'id'       => 'plugin_' . sanitize_key( str_replace( array( '/', '.' ), '_', $plugin ) ),
				'category' => $entry['category'],
				'title'    => $entry['title'],
				'message'  => $entry['message'],
			);
		}

		return $warnings;
	}

	/**
	 * Return warnings for account-related hook overlap.
	 *
	 * @param array<string,mixed>|null $settings Optional settings.
	 * @return array<int,array<string,string>>
	 */
	public function hook_warnings( $settings = null ) {
		$settings = is_array( $settings ) ? $settings : ALYNT_AG_Settings_Schema::get_settings();
		$warnings = array();

		foreach ( $this->registry->hook_categories() as $category => $hooks ) {
			if ( ! $this->registry->category_enabled( $category, $settings ) ) {
				continue;
			}

			$callbacks = $this->hook_inspector->third_party_callbacks_for_hooks( $hooks );
			if ( empty( $callbacks ) ) {
				continue;
			}

			$warnings[] = array(
				'id'       => 'hook_' . sanitize_key( $category ),
				'category' => $category,
				'title'    => $this->registry->category_title( $category ),
				'message'  => sprintf(
					/* translators: %s: callback summary. */
					__( 'Other code is attached to related account hooks: %s. Review these integrations if gateway behavior looks unexpected.', 'alynt-account-gateway' ),
					implode( ', ', array_slice( $callbacks, 0, 6 ) )
				),
			);
		}

		return $warnings;
	}

	/**
	 * Return known active plugin basenames.
	 *
	 * @return array<int,string>
	 */
	private function active_plugin_basenames() {
		$active = get_option( 'active_plugins', array() );

		if ( ! is_array( $active ) ) {
			$active = array();
		}

		if ( is_multisite() ) {
			$network_active = get_site_option( 'active_sitewide_plugins', array() );
			if ( is_array( $network_active ) ) {
				$active = array_merge( $active, array_keys( $network_active ) );
			}
		}

		return array_values( array_unique( array_map( 'sanitize_text_field', $active ) ) );
	}

	/**
	 * Remove duplicate warnings.
	 *
	 * @param array<int,array<string,string>> $warnings Warnings.
	 * @return array<int,array<string,string>>
	 */
	private function deduplicate_warnings( $warnings ) {
		$seen = array();
		$out  = array();

		foreach ( $warnings as $warning ) {
			$id = $warning['id'] ?? md5( wp_json_encode( $warning ) );
			if ( isset( $seen[ $id ] ) ) {
				continue;
			}

			$seen[ $id ] = true;
			$out[]       = $warning;
		}

		return $out;
	}
}
