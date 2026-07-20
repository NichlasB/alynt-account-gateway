<?php
/**
 * Settings schema tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-settings-schema-test-case.php';

/**
 * Tests dashboard and WooCommerce schema values.
 */
class SettingsSchemaDashboardTest extends SettingsSchemaTestCase {

	public function test_dashboard_offcanvas_settings_are_sanitized() {
		$sanitized = ALYNT_AG_Settings_Schema::sanitize(
			array(
				'dashboard_offcanvas_enabled' => '1',
				'dashboard_offcanvas_menu_id' => '-42',
				'dashboard_footer_menu_enabled' => '1',
				'dashboard_footer_menu_id' => '-84',
			)
		);

		$this->assertTrue( $sanitized['dashboard_offcanvas_enabled'] );
		$this->assertSame( 42, $sanitized['dashboard_offcanvas_menu_id'] );
		$this->assertTrue( $sanitized['dashboard_footer_menu_enabled'] );
		$this->assertSame( 84, $sanitized['dashboard_footer_menu_id'] );
	}

	public function test_woocommerce_dashboard_visibility_is_sanitized_to_hidden_endpoint_keys() {
		$sanitized = ALYNT_AG_Settings_Schema::sanitize(
			array(
				'woocommerce_hidden_menu_items' => array(
					'orders'          => '1',
					'downloads'       => '0',
					'loyalty_points'  => '1',
					'<script>'        => '1',
				),
			)
		);

		$this->assertSame(
			array( 'orders', 'loyalty_points', 'script' ),
			$sanitized['woocommerce_hidden_menu_items']
		);
	}

	public function test_woocommerce_dashboard_visibility_accepts_indexed_import_values() {
		$sanitized = ALYNT_AG_Settings_Schema::sanitize(
			array(
				'woocommerce_hidden_menu_items' => array( 'orders', 'loyalty-points', 'orders' ),
			)
		);

		$this->assertSame(
			array( 'orders', 'loyalty-points' ),
			$sanitized['woocommerce_hidden_menu_items']
		);
	}

	public function test_dashboard_custom_links_are_sanitized_to_json() {
		$sanitized = ALYNT_AG_Settings_Schema::sanitize(
			array(
				'dashboard_custom_links' => wp_json_encode(
					array(
						array(
							'label'   => '<strong>Support</strong>',
							'url'     => '/support/',
							'icon'    => 'help<script>',
							'order'   => '-10',
							'target'  => '_blank',
							'roles'   => array( 'customer', '<bad>' ),
							'unknown' => 'discarded',
						),
						array(
							'label' => 'Missing URL',
							'url'   => '',
						),
					)
				),
			)
		);

		$links = json_decode( $sanitized['dashboard_custom_links'], true );

		$this->assertCount( 1, $links );
		$this->assertSame( 'Support', $links[0]['label'] );
		$this->assertSame( '/support/', $links[0]['url'] );
		$this->assertSame( 'helpscript', $links[0]['icon'] );
		$this->assertSame( 0, $links[0]['order'] );
		$this->assertSame( '_blank', $links[0]['target'] );
		$this->assertSame( array( 'customer', 'bad' ), $links[0]['roles'] );
		$this->assertArrayNotHasKey( 'unknown', $links[0] );
	}

	public function test_invalid_dashboard_custom_links_json_preserves_saved_links() {
		$saved = wp_json_encode(
			array(
				array(
					'label' => 'Support',
					'url'   => '/support/',
				),
			)
		);
		update_option(
			'alynt_ag_settings',
			array( 'dashboard_custom_links' => $saved )
		);

		$sanitized = ALYNT_AG_Settings_Schema::sanitize(
			array( 'dashboard_custom_links' => '{invalid-json' )
		);

		$this->assertSame( $saved, $sanitized['dashboard_custom_links'] );
	}
}
