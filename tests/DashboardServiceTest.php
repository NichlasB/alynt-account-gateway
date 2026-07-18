<?php
/**
 * Dashboard service tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests dashboard link metadata.
 */
class DashboardServiceTest extends TestCase {

	public function test_default_links_include_account_details_and_logout() {
		$service = new ALYNT_AG_Dashboard_Service();
		$links   = $service->default_links(
			array(
				'after_login_redirect' => '/account/',
				'login_path'           => '/login',
			)
		);

		$labels = array_column( $links, 'label' );

		$this->assertContains( 'Account Details', $labels );
		$this->assertContains( 'Log Out', $labels );
		$this->assertSame( '/account/edit-account/', $links[0]['url'] );
	}

	public function test_custom_links_are_parsed_normalized_filtered_and_ordered() {
		$service = new ALYNT_AG_Dashboard_Service();
		$user    = new WP_User( 'customer@example.test' );
		$user->roles = array( 'customer' );
		$links   = $service->links_for_user(
			$user,
			array(
				'after_login_redirect'  => '/account/',
				'login_path'            => '/login',
				'dashboard_custom_links' => wp_json_encode(
					array(
						array(
							'label'  => 'VIP Area',
							'url'    => '/vip/',
							'icon'   => 'star',
							'order'  => 5,
							'target' => '_blank',
							'roles'  => array( 'vip' ),
						),
						array(
							'label'  => 'Documentation',
							'url'    => 'https://docs.example.test/account/',
							'icon'   => 'book',
							'order'  => 10,
							'target' => '_blank',
							'roles'  => array( 'customer' ),
						),
						array(
							'label' => 'Support',
							'url'   => '/support/',
							'icon'  => 'help',
							'order' => 15,
							'roles' => array( 'customer' ),
						),
					)
				),
			)
		);

		$labels = array_column( $links, 'label' );

		$this->assertNotContains( 'VIP Area', $labels );
		$this->assertSame( array( 'Documentation', 'Support', 'Account Details', 'Log Out' ), $labels );
		$this->assertSame( 'https://docs.example.test/account/', $links[0]['url'] );
		$this->assertSame( 'book', $links[0]['icon'] );
		$this->assertSame( '_blank', $links[0]['target'] );
		$this->assertSame( 'https://example.test/support/', $links[1]['url'] );
		$this->assertSame( 'help', $links[1]['icon'] );
		$this->assertSame( '_self', $links[1]['target'] );
	}

	public function test_woocommerce_available_is_false_without_woocommerce() {
		$service = new ALYNT_AG_Dashboard_Service();

		$this->assertFalse( $service->woocommerce_available() );
	}
}
