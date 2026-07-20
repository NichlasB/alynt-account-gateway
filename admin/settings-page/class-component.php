<?php
/**
 * Settings page component base.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Routes cross-component method calls through the shared registry.
 */
abstract class ALYNT_AG_Settings_Page_Component {

	/**
	 * Component registry.
	 *
	 * @var ALYNT_AG_Settings_Page_Components
	 */
	private $components;

	/**
	 * Constructor.
	 *
	 * @param ALYNT_AG_Settings_Page_Components $components Component registry.
	 */
	public function __construct( $components ) {
		$this->components = $components;
	}

	/**
	 * Route a call to the component that owns it.
	 *
	 * @param string           $method    Method name.
	 * @param array<int,mixed> $arguments Method arguments.
	 * @return mixed
	 */
	public function __call( $method, $arguments ) {
		return $this->components->call( $method, $arguments );
	}
}
