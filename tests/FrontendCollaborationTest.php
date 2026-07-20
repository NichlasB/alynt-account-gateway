<?php
/**
 * Frontend facade collaboration tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Records frontend facade collaborator calls.
 */
class ALYNT_AG_Test_Frontend_Collaborator_Spy {

	/**
	 * Recorded calls.
	 *
	 * @var array<int,array<string,mixed>>
	 */
	public $calls = array();

	/**
	 * Record arbitrary collaborator calls.
	 *
	 * @param string           $name      Method name.
	 * @param array<int,mixed> $arguments Method arguments.
	 * @return mixed
	 */
	public function __call( $name, $arguments ) {
		$this->calls[] = compact( 'name', 'arguments' );

		if ( 0 === strpos( $name, 'filter_' ) ) {
			return 'filtered-' . $name;
		}

		return 'get_screen_title' === $name ? 'Injected title' : null;
	}
}

/**
 * Provides an injected screen for asset delegation.
 */
class ALYNT_AG_Test_Frontend_Routes_Spy {

	public function screen( $settings ) {
		return 'injected-screen';
	}
}

/**
 * Locks the frontend facade and collaborator boundaries.
 */
class FrontendCollaborationTest extends TestCase {

	public function test_default_facade_defers_gateway_document_graph() {
		$facade     = new ALYNT_AG_Frontend();
		$reflection = new ReflectionClass( $facade );

		foreach ( array( 'renderer', 'gateway' ) as $property_name ) {
			$property = $reflection->getProperty( $property_name );
			if ( PHP_VERSION_ID < 80100 ) {
				$property->setAccessible( true );
			}

			$this->assertNull( $property->getValue( $facade ) );
		}
	}

	public function test_facade_delegates_to_injected_collaborators() {
		$routes  = new ALYNT_AG_Test_Frontend_Routes_Spy();
		$assets  = new ALYNT_AG_Test_Frontend_Collaborator_Spy();
		$access  = new ALYNT_AG_Test_Frontend_Collaborator_Spy();
		$urls    = new ALYNT_AG_Test_Frontend_Collaborator_Spy();
		$gateway = new ALYNT_AG_Test_Frontend_Collaborator_Spy();
		$facade  = new ALYNT_AG_Frontend(
			array(
				'routes'  => $routes,
				'assets'  => $assets,
				'access'  => $access,
				'urls'    => $urls,
				'gateway' => $gateway,
			)
		);

		$facade->enqueue_assets();
		$facade->enqueue_preview_assets( array(), 'login' );
		$this->assertSame( 'filtered-filter_admin_bar', $facade->filter_admin_bar( true ) );
		$facade->maybe_block_wp_admin();
		$facade->maybe_redirect_native_login();
		$facade->maybe_render_gateway();
		$facade->maybe_render_gateway_preview();
		$facade->render_preview( 'login', array() );
		$this->assertSame( 'filtered-filter_login_url', $facade->filter_login_url( 'native', '', false ) );
		$this->assertSame( 'filtered-filter_lostpassword_url', $facade->filter_lostpassword_url( 'native', '' ) );
		$this->assertSame( 'filtered-filter_register_url', $facade->filter_register_url( 'native' ) );
		$this->assertSame( 'filtered-filter_logout_url', $facade->filter_logout_url( 'native', '' ) );
		$this->assertSame( 'filtered-filter_force_login_bypass', $facade->filter_force_login_bypass( false, 'https://example.test/login' ) );
		$this->assertSame( 'Injected title', $facade->get_screen_title( 'login' ) );

		$this->assertSame( 'injected-screen', $assets->calls[0]['arguments'][1] );
		$this->assertCount( 2, $assets->calls );
		$this->assertCount( 4, $access->calls );
		$this->assertCount( 4, $urls->calls );
		$this->assertCount( 4, $gateway->calls );
	}

	public function test_frontend_files_and_loader_order_stay_structurally_bounded() {
		$files = array(
			'public/class-frontend.php',
			'includes/services/class-frontend-request-context.php',
			'includes/services/class-frontend-url-adapter.php',
			'includes/services/class-frontend-access-controller.php',
			'includes/services/class-frontend-gateway-controller.php',
		);

		foreach ( $files as $file ) {
			$this->assertLessThanOrEqual( 300, count( file( ALYNT_AG_PLUGIN_DIR . $file ) ), $file );
		}

		$loader = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'includes/class-loader.php' );
		$facade = strpos( $loader, 'public/class-frontend.php' );

		$this->assertStringContainsString( 'if ( is_admin() )', $loader );
		$this->assertStringContainsString( '$alynt_ag_files = array_merge( $alynt_ag_files, $alynt_ag_admin_files )', $loader );

		foreach ( array_slice( $files, 1 ) as $file ) {
			$this->assertLessThan( $facade, strpos( $loader, basename( $file ) ) );
		}
	}
}
