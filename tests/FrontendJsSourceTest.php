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
		$js = file_get_contents( dirname( __DIR__ ) . '/assets/src/frontend/index.js' );

		$this->assertIsString( $js );

		return $js;
	}

	public function test_password_submit_aria_disabled_tracks_validity() {
		$js = $this->get_frontend_js();

		$this->assertStringContainsString( 'submit.disabled = ! isValid;', $js );
		$this->assertStringContainsString( "submit.setAttribute( 'aria-disabled', isValid ? 'false' : 'true' );", $js );
	}
	public function test_password_requirements_use_readable_accessibility_labels() {
		$js = $this->get_frontend_js();

		$this->assertStringContainsString( "item.getAttribute( 'data-agw-requirement-label' )", $js );
		$this->assertStringContainsString( "item.setAttribute( 'aria-label', `\${ requirementState }: \${ requirementLabel }` );", $js );
		$this->assertStringContainsString( "alyntAgLabels.requirementMet || ''", $js );
		$this->assertStringContainsString( "alyntAgLabels.requirementNotMet || ''", $js );
		$this->assertStringContainsString( "( alyntAgLabels.requirementsMet || '' )", $js );
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
