<?php
/**
 * Frontend document renderer.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders full gateway documents and admin preview output.
 */
class ALYNT_AG_Frontend_Document_Renderer {

	/**
	 * Gateway shell renderer.
	 *
	 * @var ALYNT_AG_Frontend_Gateway_Shell
	 */
	private $gateway_shell;

	/**
	 * Dashboard screen renderer.
	 *
	 * @var ALYNT_AG_Frontend_Dashboard_Screen
	 */
	private $dashboard_screen;

	/**
	 * Frontend messages.
	 *
	 * @var ALYNT_AG_Frontend_Messages
	 */
	private $messages;

	/**
	 * Constructor.
	 *
	 * @param ALYNT_AG_Frontend_Gateway_Shell|null    $gateway_shell    Gateway shell renderer.
	 * @param ALYNT_AG_Frontend_Dashboard_Screen|null $dashboard_screen Dashboard screen renderer.
	 * @param ALYNT_AG_Frontend_Messages|null         $messages         Frontend messages.
	 */
	public function __construct( $gateway_shell = null, $dashboard_screen = null, $messages = null ) {
		$this->gateway_shell    = $gateway_shell ? $gateway_shell : new ALYNT_AG_Frontend_Gateway_Shell();
		$this->dashboard_screen = $dashboard_screen ? $dashboard_screen : new ALYNT_AG_Frontend_Dashboard_Screen();
		$this->messages         = $messages ? $messages : new ALYNT_AG_Frontend_Messages();
	}

	/**
	 * Render a full gateway document.
	 *
	 * @param string              $screen                Screen key.
	 * @param array<string,mixed> $settings              Settings.
	 * @param string              $current_relative_path Current relative request path.
	 * @return void
	 */
	public function render_gateway_document( $screen, $settings, $current_relative_path ) {
		status_header( 200 );
		nocache_headers();

		echo '<!doctype html>';
		echo '<html ';
		language_attributes();
		echo '>';
		echo '<head>';
		echo '<meta charset="' . esc_attr( get_bloginfo( 'charset' ) ) . '">';
		echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
		echo '<title>' . esc_html( $this->get_screen_title( $screen ) ) . '</title>';
		wp_head();
		echo '</head>';
		echo '<body class="alynt-ag-body">';
		$this->render_gateway_body( $screen, $settings, $current_relative_path );
		wp_footer();
		echo '</body></html>';
	}

	/**
	 * Render one gateway screen for admin preview.
	 *
	 * @param string              $screen                Screen key.
	 * @param array<string,mixed> $settings              Settings.
	 * @param string              $current_relative_path Current relative request path.
	 * @return void
	 */
	public function render_preview( $screen, $settings, $current_relative_path = '' ) {
		$screen = $this->normalize_preview_screen( $screen );

		if ( 'setpassword' === $screen ) {
			$this->gateway_shell->render_gateway_shell_with_password_preview( $settings );
			return;
		}

		$this->render_gateway_body( $screen, $settings, $current_relative_path );
	}

	/**
	 * Get title for the document title tag.
	 *
	 * @param string $screen Screen key.
	 * @return string
	 */
	public function get_screen_title( $screen ) {
		return $this->messages->screen_title( $screen );
	}

	/**
	 * Render the dashboard or auth gateway body.
	 *
	 * @param string              $screen                Screen key.
	 * @param array<string,mixed> $settings              Settings.
	 * @param string              $current_relative_path Current relative request path.
	 * @return void
	 */
	private function render_gateway_body( $screen, $settings, $current_relative_path ) {
		if ( 'dashboard' === $screen ) {
			$this->dashboard_screen->render_dashboard_shell( $settings, $current_relative_path );
			return;
		}

		$this->gateway_shell->render_gateway_shell( $screen, $settings );
	}

	/**
	 * Normalize preview screen input.
	 *
	 * @param string $screen Screen key.
	 * @return string
	 */
	private function normalize_preview_screen( $screen ) {
		return in_array(
			$screen,
			array( 'dashboard', 'login', 'register', 'lostpassword', 'setpassword', 'logout', 'registration_disabled', 'invalidlink' ),
			true
		) ? $screen : 'login';
	}
}
