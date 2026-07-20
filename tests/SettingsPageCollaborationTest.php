<?php
/**
 * Settings page collaboration tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Locks the settings-page facade and component boundaries.
 */
class SettingsPageCollaborationTest extends TestCase {

	/**
	 * Preserve the established WordPress-facing public API.
	 */
	public function test_facade_preserves_public_methods() {
		$reflection = new ReflectionClass( 'ALYNT_AG_Settings_Page' );
		$methods    = array();

		foreach ( $reflection->getMethods( ReflectionMethod::IS_PUBLIC ) as $method ) {
			if ( 'ALYNT_AG_Settings_Page' === $method->getDeclaringClass()->getName() ) {
				$methods[] = $method->getName();
			}
		}

		sort( $methods );

		$this->assertSame(
			array(
				'add_menu_page',
				'handle_clear_diagnostics',
				'handle_export_diagnostics',
				'handle_export_settings',
				'handle_import_settings',
				'handle_preview_email',
				'handle_preview_gateway',
				'handle_restore_tab_defaults',
				'handle_review_verification',
				'handle_test_email',
				'handle_test_security_provider',
				'handle_test_webhook',
				'log_settings_change',
				'maybe_handle_preview_gateway_request',
				'register',
				'register_settings',
				'render',
			),
			$methods
		);
	}

	/**
	 * Keep the facade and every focused production component under 300 lines.
	 */
	public function test_settings_page_production_files_stay_within_line_limit() {
		$files = array_merge(
			array( ALYNT_AG_PLUGIN_DIR . 'admin/class-settings-page.php' ),
			glob( ALYNT_AG_PLUGIN_DIR . 'admin/settings-page/*.php' )
		);

		$this->assertNotEmpty( $files );

		foreach ( $files as $file ) {
			$line_count = count( file( $file ) );

			$this->assertLessThanOrEqual(
				300,
				$line_count,
				basename( $file ) . ' exceeds the 300-line structural limit.'
			);
		}
	}

	/**
	 * Ensure every extracted method has one component owner.
	 */
	public function test_registry_owns_every_extracted_operation() {
		$components = new ALYNT_AG_Settings_Page_Components();
		$classes    = array_filter(
			get_declared_classes(),
			static function ( $class_name ) {
				return 0 === strpos( $class_name, 'ALYNT_AG_Settings_Page_' )
					&& is_subclass_of( $class_name, 'ALYNT_AG_Settings_Page_Component' );
			}
		);
		$methods    = array();

		foreach ( $classes as $class_name ) {
			$reflection = new ReflectionClass( $class_name );

			foreach ( $reflection->getMethods( ReflectionMethod::IS_PUBLIC ) as $method ) {
				if ( $class_name !== $method->getDeclaringClass()->getName() ) {
					continue;
				}

				$methods[] = $method->getName();
				$this->assertTrue( $components->has( $method->getName() ) );
			}
		}

		$this->assertCount( 133, array_unique( $methods ) );
		$this->assertCount( 133, $methods );
	}

	/**
	 * Load the base, components, registry, and facade in dependency order.
	 */
	public function test_production_loader_preserves_component_order() {
		$loader = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'includes/class-loader.php' );

		$base_position     = strpos( $loader, 'admin/settings-page/class-component.php' );
		$field_position    = strpos( $loader, 'admin/settings-page/class-field-renderer-core.php' );
		$registry_position = strpos( $loader, 'admin/settings-page/class-components.php' );
		$facade_position   = strpos( $loader, 'admin/class-settings-page.php' );

		$this->assertIsInt( $base_position );
		$this->assertIsInt( $field_position );
		$this->assertIsInt( $registry_position );
		$this->assertIsInt( $facade_position );
		$this->assertLessThan( $field_position, $base_position );
		$this->assertLessThan( $registry_position, $field_position );
		$this->assertLessThan( $facade_position, $registry_position );
	}
}
