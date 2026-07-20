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

	/**
	 * Verify a submitted frontend nonce without invoking the core error screen.
	 *
	 * @param string $action Nonce action.
	 * @param string $field  Request field.
	 * @return bool
	 */
	protected function request_nonce_is_valid( $action, $field ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- This method performs the nonce verification.
		$nonce = isset( $_REQUEST[ $field ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $field ] ) ) : '';

		return (bool) wp_verify_nonce( $nonce, $action );
	}
}
