<?php
/**
 * Lazy WooCommerce collaborator resolution.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates focused WooCommerce collaborators only when their feature runs.
 */
trait ALYNT_AG_WooCommerce_Collaborators {

	/**
	 * Return a focused WooCommerce collaborator on demand.
	 *
	 * @param string $key Collaborator key.
	 * @return object
	 */
	private function collaborator( $key ) {
		if ( null !== $this->collaborators[ $key ] ) {
			return $this->collaborators[ $key ];
		}

		$classes = array(
			'navigation' => 'ALYNT_AG_WooCommerce_Navigation',
			'routing'    => 'ALYNT_AG_WooCommerce_Routing',
			'renderer'   => 'ALYNT_AG_WooCommerce_Endpoint_Renderer',
			'data'       => 'ALYNT_AG_WooCommerce_Customer_Data',
		);
		$class   = $classes[ $key ];

		$this->collaborators[ $key ] = new $class( $this );

		return $this->collaborators[ $key ];
	}
}
