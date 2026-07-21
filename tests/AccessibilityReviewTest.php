<?php
/**
 * Accessibility review regression tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Locks cross-cutting accessibility semantics identified during pre-release.
 */
class AccessibilityReviewTest extends TestCase {

	/**
	 * Return concatenated settings-page component source.
	 *
	 * @return string
	 */
	private function settings_source() {
		$files  = glob( ALYNT_AG_PLUGIN_DIR . 'admin/settings-page/*.php' );
		$source = '';

		$this->assertIsArray( $files );
		foreach ( $files as $file ) {
			$content = file_get_contents( $file );
			$this->assertIsString( $content );
			$source .= "\n" . $content;
		}

		return $source;
	}

	public function test_admin_data_tables_have_accessible_names() {
		$source = $this->settings_source();
		preg_match_all( '/<table\b([^>]*)>/', $source, $matches );

		$this->assertNotEmpty( $matches[1] );
		foreach ( $matches[1] as $attributes ) {
			if ( false !== strpos( $attributes, 'role="presentation"' ) ) {
				continue;
			}

			$this->assertStringContainsString( 'aria-label=', $attributes, 'Every admin data table needs an accessible name.' );
		}
	}

	public function test_admin_data_table_column_headers_declare_scope() {
		$source = $this->settings_source();
		preg_match_all( '/<thead>(.*?)<\/thead>/s', $source, $headers );

		$this->assertNotEmpty( $headers[1] );
		foreach ( $headers[1] as $header ) {
			preg_match_all( '/<th\b([^>]*)>/', $header, $cells );
			foreach ( $cells[1] as $attributes ) {
				$this->assertStringContainsString( 'scope="col"', $attributes );
			}
		}
	}

	public function test_current_settings_tab_is_programmatically_identified() {
		$source = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'admin/settings-page/class-page-shell.php' );

		$this->assertIsString( $source );
		$this->assertStringContainsString( 'aria-current="page"', $source );
	}

	public function test_gateway_preview_links_announce_new_tabs() {
		$source = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'admin/settings-page/class-settings-tools.php' );

		$this->assertIsString( $source );
		$this->assertStringContainsString( 'target="_blank" rel="noopener noreferrer"', $source );
		$this->assertStringContainsString( "esc_html_e( 'opens in a new tab'", $source );
	}
}
