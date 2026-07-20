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

	/**
	 * Render sanitized admin notices for failed plugin-data reads.
	 *
	 * @param array<int,WP_Error> $errors Read errors.
	 * @return void
	 */
	protected function render_admin_data_read_errors( $errors ) {
		foreach ( $errors as $error ) {
			if ( ! is_wp_error( $error ) ) {
				continue;
			}
			?>
			<div class="notice notice-error inline">
				<p><?php echo esc_html( $error->get_error_message() ); ?></p>
			</div>
			<?php
		}
	}
}
