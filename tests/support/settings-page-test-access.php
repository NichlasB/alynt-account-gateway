<?php
/**
 * Test-only access to settings-page collaborator methods.
 *
 * @package Alynt_Account_Gateway
 */

/**
 * Invoke a facade method or one internal component operation.
 *
 * @param ALYNT_AG_Settings_Page $settings_page Settings page instance.
 * @param string                 $method        Method name.
 * @param array<int,mixed>       $arguments     Method arguments.
 * @return mixed
 */
function alynt_ag_test_invoke_settings_page_method( $settings_page, $method, $arguments = array() ) {
	if ( method_exists( $settings_page, $method ) ) {
		$reflection = new ReflectionMethod( $settings_page, $method );
		if ( PHP_VERSION_ID < 80100 ) {
			$reflection->setAccessible( true );
		}

		return $reflection->invokeArgs( $settings_page, $arguments );
	}

	$reflection = new ReflectionMethod( $settings_page, 'call_component' );
	if ( PHP_VERSION_ID < 80100 ) {
		$reflection->setAccessible( true );
	}

	return $reflection->invokeArgs(
		$settings_page,
		array( $method, $arguments )
	);
}
