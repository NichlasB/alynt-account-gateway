<?php
/**
 * Shared service collaborator.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Forwards override-sensitive calls through a public service facade.
 */
abstract class ALYNT_AG_Service_Collaborator {

	/**
	 * Public service facade.
	 *
	 * @var object
	 */
	protected $service;

	/**
	 * Constructor.
	 *
	 * @param object $service Public service facade.
	 */
	public function __construct( $service ) {
		$this->service = $service;
	}

	/**
	 * Forward an override-sensitive method call to the public facade.
	 *
	 * @param string           $name      Method name.
	 * @param array<int,mixed> $arguments Method arguments.
	 * @return mixed
	 */
	public function __call( $name, $arguments ) {
		return call_user_func_array( array( $this->service, $name ), $arguments );
	}
}
