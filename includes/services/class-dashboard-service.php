<?php
/**
 * Dashboard service placeholder.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provides account dashboard metadata.
 */
class ALYNT_AG_Dashboard_Service {

	/**
	 * Return default dashboard links.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public function default_links() {
		return array(
			array(
				'label'  => __( 'Account Details', 'alynt-account-gateway' ),
				'url'    => '/my-account/edit-account/',
				'icon'   => 'user',
				'target' => '_self',
				'roles'  => array( 'customer', 'subscriber' ),
			),
		);
	}
}
