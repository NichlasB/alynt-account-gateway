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
	 * Gateway title supplied to WordPress while its document head renders.
	 *
	 * @var string
	 */
	private $gateway_document_title = '';

	/**
	 * Gateway shell renderer.
	 *
	 * @var ALYNT_AG_Frontend_Gateway_Shell|null
	 */
	private $gateway_shell;

	/**
	 * Dashboard screen renderer.
	 *
	 * @var ALYNT_AG_Frontend_Dashboard_Screen|null
	 */
	private $dashboard_screen;

	/**
	 * Frontend messages.
	 *
	 * @var ALYNT_AG_Frontend_Messages|null
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
		$this->gateway_shell    = $gateway_shell;
		$this->dashboard_screen = $dashboard_screen;
		$this->messages         = $messages;
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
		$this->prepare_gateway_document_title( $screen );

		status_header( 200 );
		nocache_headers();

		echo '<!doctype html>';
		echo '<html ';
		language_attributes();
		echo '>';
		echo '<head>';
		echo '<meta charset="' . esc_attr( get_bloginfo( 'charset' ) ) . '">';
		echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
		wp_head();
		$this->release_gateway_document_title();
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
			$this->gateway_shell()->render_gateway_shell_with_password_preview( $settings );
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
		return $this->messages()->screen_title( $screen );
	}

	/**
	 * Supply the branded gateway title through WordPress's title pipeline.
	 *
	 * @param string $title Existing document title.
	 * @return string
	 */
	public function filter_gateway_document_title( $title ) {
		return '' !== $this->gateway_document_title ? $this->gateway_document_title : $title;
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
			$this->dashboard_screen()->render_dashboard_shell( $settings, $current_relative_path );
			return;
		}

		$this->gateway_shell()->render_gateway_shell( $screen, $settings );
	}

	/**
	 * Set gateway document metadata before WordPress renders its head.
	 *
	 * Branded routes are intentionally handled before a matching WordPress page
	 * exists. Clear that false 404 state so WordPress and SEO integrations do not
	 * emit not-found metadata alongside the gateway document.
	 *
	 * @param string $screen Screen key.
	 * @return void
	 */
	private function prepare_gateway_document_title( $screen ) {
		global $wp_query;

		$this->gateway_document_title = $this->get_screen_title( $screen );

		if ( isset( $wp_query ) && is_object( $wp_query ) && isset( $wp_query->is_404 ) ) {
			$wp_query->is_404 = false;
		}

		add_filter( 'pre_get_document_title', array( $this, 'filter_gateway_document_title' ), PHP_INT_MAX );
	}

	/**
	 * Remove the temporary title filter after the document head has rendered.
	 *
	 * @return void
	 */
	private function release_gateway_document_title() {
		remove_filter( 'pre_get_document_title', array( $this, 'filter_gateway_document_title' ), PHP_INT_MAX );
		$this->gateway_document_title = '';
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

	/**
	 * Return the authentication gateway shell on demand.
	 *
	 * @return ALYNT_AG_Frontend_Gateway_Shell
	 */
	private function gateway_shell() {
		if ( null === $this->gateway_shell ) {
			$this->gateway_shell = new ALYNT_AG_Frontend_Gateway_Shell();
		}

		return $this->gateway_shell;
	}

	/**
	 * Return the dashboard renderer on demand.
	 *
	 * @return ALYNT_AG_Frontend_Dashboard_Screen
	 */
	private function dashboard_screen() {
		if ( null === $this->dashboard_screen ) {
			$this->dashboard_screen = new ALYNT_AG_Frontend_Dashboard_Screen();
		}

		return $this->dashboard_screen;
	}

	/**
	 * Return frontend messages on demand.
	 *
	 * @return ALYNT_AG_Frontend_Messages
	 */
	private function messages() {
		if ( null === $this->messages ) {
			$this->messages = new ALYNT_AG_Frontend_Messages();
		}

		return $this->messages;
	}
}
