<?php
/**
 * Registration service.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public facade for pending registration flows.
 */
class ALYNT_AG_Registration_Service {
	use ALYNT_AG_Registration_Lifecycle_Facade;
	use ALYNT_AG_Registration_Protection_Facade;
	use ALYNT_AG_Registration_Credentials_Facade;

	/**
	 * Minimum password length.
	 */
	const MIN_PASSWORD_LENGTH = 12;

	/**
	 * Return destination helper.
	 *
	 * @var ALYNT_AG_Return_Destination
	 */
	private $destinations;

	/**
	 * Return path associated with the most recently completed registration.
	 *
	 * @var string
	 */
	private $last_completed_return_path = '';

	/**
	 * Focused registration collaborators.
	 *
	 * @var array<string,object>
	 */
	private $collaborators;

	/**
	 * Constructor.
	 *
	 * @param ALYNT_AG_Return_Destination|null $destinations Return destination helper.
	 * @param array<string,object>             $collaborators Optional collaborator overrides.
	 */
	public function __construct( $destinations = null, $collaborators = array() ) {
		$this->destinations  = $destinations ? $destinations : new ALYNT_AG_Return_Destination();
		$defaults            = array(
			'request'      => new ALYNT_AG_Registration_Request_Handler( $this, $this->destinations ),
			'protection'   => new ALYNT_AG_Registration_Protection( $this ),
			'activity'     => new ALYNT_AG_Registration_Activity( $this ),
			'pending'      => new ALYNT_AG_Registration_Pending_Store( $this, $this->destinations ),
			'confirmation' => new ALYNT_AG_Registration_Confirmation( $this ),
			'completion'   => new ALYNT_AG_Registration_Completion( $this, $this->destinations ),
			'delivery'     => new ALYNT_AG_Registration_Delivery( $this ),
			'credentials'  => new ALYNT_AG_Registration_Credentials( $this ),
		);
		$this->collaborators = array_merge( $defaults, is_array( $collaborators ) ? $collaborators : array() );
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'template_redirect', array( $this, 'maybe_handle_registration_request' ), 0 );
	}

	/**
	 * Handle branded registration form submissions.
	 *
	 * @return void
	 */
	public function maybe_handle_registration_request() {
		$this->collaborators['request']->run_maybe_handle_registration_request();
	}
}
