<?php
/**
 * Frontend JavaScript source tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests important frontend JavaScript behavior markers.
 */
class FrontendJsSourceTest extends TestCase {

	/**
	 * Reads the frontend source JavaScript.
	 *
	 * @return string
	 */
	private function get_frontend_js() {
		$source_dir = dirname( __DIR__ ) . '/assets/src/frontend';
		$files      = glob( $source_dir . '/modules/*.js' );

		$this->assertIsArray( $files );
		sort( $files );
		array_unshift( $files, $source_dir . '/index.js' );

		$js = '';
		foreach ( $files as $file ) {
			$module = file_get_contents( $file );

			$this->assertIsString( $module );
			$js .= "\n" . $module;
		}

		return $js;
	}

	public function test_frontend_javascript_uses_focused_modules() {
		$source_dir = dirname( __DIR__ ) . '/assets/src/frontend';
		$entry      = file_get_contents( $source_dir . '/index.js' );
		$modules    = glob( $source_dir . '/modules/*.js' );
		$source     = $this->get_frontend_js();

		$this->assertIsString( $entry );
		$this->assertIsArray( $modules );
		$this->assertCount( 7, $modules );

		foreach ( $modules as $module ) {
			$relative_path = './modules/' . basename( $module );
			$import_path   = 'labels.js' === basename( $module ) ? './labels.js' : $relative_path;
			$lines         = file( $module );

			$this->assertStringContainsString( $import_path, $source );
			$this->assertIsArray( $lines );
			$this->assertLessThanOrEqual( 250, count( $lines ), $relative_path );
		}
	}

	public function test_redirected_errors_restore_non_secret_fields_and_focus_invalid_input() {
		$js = $this->get_frontend_js();

		$this->assertStringContainsString( "form.querySelectorAll( '[data-agw-retain][name]' )", $js );
		$this->assertStringContainsString( 'window.sessionStorage.setItem', $js );
		$this->assertStringContainsString( "document.querySelector( '.agw-form [aria-invalid=\"true\"]' )", $js );
		$this->assertStringNotContainsString( "name=\"pwd\"", $js );
		$this->assertStringNotContainsString( "name=\"password\"", $js );
	}

	public function test_password_submit_aria_disabled_tracks_validity() {
		$js = $this->get_frontend_js();

		$this->assertStringContainsString( 'Array.from( password ).length >= 12', $js );
		$this->assertStringContainsString( 'submit.disabled = ! isValid;', $js );
		$this->assertStringContainsString( "submit.setAttribute( 'aria-disabled', isValid ? 'false' : 'true' );", $js );
	}

	public function test_gateway_forms_prevent_duplicate_submissions_and_report_busy_state() {
		$js = $this->get_frontend_js();

		$this->assertStringContainsString( "document.querySelectorAll( '.agw-form' )", $js );
		$this->assertStringContainsString( "form.dataset.agwSubmitting === '1'", $js );
		$this->assertStringContainsString( "form.setAttribute( 'aria-busy', 'true' )", $js );
		$this->assertStringContainsString( "submit.setAttribute( 'aria-disabled', 'true' )", $js );
		$this->assertStringContainsString( 'event.defaultPrevented || ! form.checkValidity()', $js );
	}

	public function test_password_requirements_use_readable_accessibility_labels() {
		$js = $this->get_frontend_js();

		$this->assertStringContainsString( "item.getAttribute( 'data-agw-requirement-label' )", $js );
		$this->assertStringContainsString( "item.setAttribute( 'aria-label', `\${ requirementState }: \${ requirementLabel }` );", $js );
		$this->assertStringContainsString( "alyntAgLabels.requirementMet || ''", $js );
		$this->assertStringContainsString( "alyntAgLabels.requirementNotMet || ''", $js );
		$this->assertStringContainsString( "state.metRequirements === 1", $js );
		$this->assertStringContainsString( "alyntAgLabels.requirementMetSummary || ''", $js );
		$this->assertStringContainsString( "alyntAgLabels.requirementsMetSummary || ''", $js );
		$this->assertStringNotContainsString( "'Met'", $js );
		$this->assertStringNotContainsString( "'Not met'", $js );
		$this->assertStringNotContainsString( 'requirements met.', $js );
		$this->assertStringNotContainsString( "item.setAttribute( 'aria-checked'", $js );
		$this->assertStringNotContainsString( "item.setAttribute( 'aria-current'", $js );
	}

	public function test_password_toggle_updates_hidden_visibility_status() {
		$js = $this->get_frontend_js();

		$this->assertStringContainsString( "wrapper ? wrapper.querySelector( '[data-agw-password-visibility-status]' ) : null;", $js );
		$this->assertStringContainsString( "alyntAgLabels.passwordVisible || ''", $js );
		$this->assertStringContainsString( "alyntAgLabels.passwordHidden || ''", $js );
		$this->assertStringNotContainsString( "'Password is visible.'", $js );
		$this->assertStringNotContainsString( "'Password is hidden.'", $js );
		$this->assertStringContainsString( 'status.textContent = statusText;', $js );
	}
}
