<?php
/**
 * Settings page component registry.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds and routes the focused settings-page collaborators.
 */
class ALYNT_AG_Settings_Page_Components {

	/**
	 * Components keyed by method name.
	 *
	 * @var array<string,ALYNT_AG_Settings_Page_Component>
	 */
	private $methods = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$classes = array(
			'ALYNT_AG_Settings_Page_Page_Shell',
			'ALYNT_AG_Settings_Page_Field_Renderer_Core',
			'ALYNT_AG_Settings_Page_Field_Renderer_Options',
			'ALYNT_AG_Settings_Page_Field_Help',
			'ALYNT_AG_Settings_Page_Admin_Notices',
			'ALYNT_AG_Settings_Page_Guidance',
			'ALYNT_AG_Settings_Page_Readiness_Summary',
			'ALYNT_AG_Settings_Page_Readiness_Rules',
			'ALYNT_AG_Settings_Page_Security_Overview',
			'ALYNT_AG_Settings_Page_Security_Policy',
			'ALYNT_AG_Settings_Page_Security_Signal_Renderer_A',
			'ALYNT_AG_Settings_Page_Security_Signal_Renderer_B',
			'ALYNT_AG_Settings_Page_Security_Signal_Data_A',
			'ALYNT_AG_Settings_Page_Security_Signal_Data_B',
			'ALYNT_AG_Settings_Page_Security_Review_Queue',
			'ALYNT_AG_Settings_Page_Security_Failure_Triage',
			'ALYNT_AG_Settings_Page_Security_Log_Metrics',
			'ALYNT_AG_Settings_Page_Security_Rate_Limits',
			'ALYNT_AG_Settings_Page_Security_Pending',
			'ALYNT_AG_Settings_Page_Security_Verification_Guidance',
			'ALYNT_AG_Settings_Page_Security_Review_Ui',
			'ALYNT_AG_Settings_Page_Email_Tools',
			'ALYNT_AG_Settings_Page_Webhook_Tools',
			'ALYNT_AG_Settings_Page_Complex_Fields',
			'ALYNT_AG_Settings_Page_Settings_Tools',
			'ALYNT_AG_Settings_Page_Gateway_Preview',
			'ALYNT_AG_Settings_Page_Diagnostics_Tools',
			'ALYNT_AG_Settings_Page_Settings_Transfer',
			'ALYNT_AG_Settings_Page_Security_Actions',
			'ALYNT_AG_Settings_Page_Messaging_Actions',
		);

		foreach ( $classes as $class_name ) {
			$this->register_component( new $class_name( $this ) );
		}
	}

	/**
	 * Determine whether one internal operation is registered.
	 *
	 * @param string $method Method name.
	 * @return bool
	 */
	public function has( $method ) {
		return isset( $this->methods[ $method ] );
	}

	/**
	 * Call one internal operation.
	 *
	 * @param string           $method    Method name.
	 * @param array<int,mixed> $arguments Method arguments.
	 * @return mixed
	 *
	 * @throws BadMethodCallException When no component owns the method.
	 */
	public function call( $method, $arguments = array() ) {
		if ( ! $this->has( $method ) ) {
			throw new BadMethodCallException(
				sprintf( 'Unknown settings-page operation: %s', esc_html( $method ) )
			);
		}

		return call_user_func_array(
			array( $this->methods[ $method ], $method ),
			$arguments
		);
	}

	/**
	 * Register the public methods declared by one component.
	 *
	 * @param ALYNT_AG_Settings_Page_Component $component Component instance.
	 * @return void
	 *
	 * @throws LogicException When two components claim the same method.
	 */
	private function register_component( $component ) {
		$reflection = new ReflectionClass( $component );

		foreach ( $reflection->getMethods( ReflectionMethod::IS_PUBLIC ) as $method ) {
			if ( $method->getDeclaringClass()->getName() !== $reflection->getName() ) {
				continue;
			}

			$method_name = $method->getName();
			if ( isset( $this->methods[ $method_name ] ) ) {
				throw new LogicException(
					sprintf( 'Duplicate settings-page operation: %s', esc_html( $method_name ) )
				);
			}

			$this->methods[ $method_name ] = $component;
		}
	}
}
