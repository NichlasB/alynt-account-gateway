<?php
/**
 * Frontend logout screen helper.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the frontend logout confirmation screen.
 */
class ALYNT_AG_Frontend_Logout_Screen {

	/**
	 * Shared component helpers.
	 *
	 * @var ALYNT_AG_Frontend_Components
	 */
	private $components;

	/**
	 * Constructor.
	 *
	 * @param ALYNT_AG_Frontend_Components|null $components Component helpers.
	 */
	public function __construct( $components = null ) {
		$this->components = $components ? $components : new ALYNT_AG_Frontend_Components();
	}

	/**
	 * Render logout confirmation screen.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	public function render_logout_screen( $settings ) {
		$logout_url = wp_nonce_url(
			add_query_arg(
				array(
					'action'  => 'logout',
					'confirm' => '1',
				),
				home_url( $settings['account_action_base'] )
			),
			'log-out'
		);
		?>
		<h1 id="agw-screen-title" class="agw-title"><?php esc_html_e( 'Confirm Logout', 'alynt-account-gateway' ); ?></h1>
		<?php $this->components->render_notice( $settings['logout_intro_text'], 'agw-logout-instructions' ); ?>
		<div class="agw-actions">
			<a class="agw-button agw-button--primary" href="<?php echo esc_url( $logout_url ); ?>"><?php esc_html_e( 'Log Out', 'alynt-account-gateway' ); ?></a>
			<a class="agw-button agw-button--secondary" href="<?php echo esc_url( home_url( $settings['after_login_redirect'] ) ); ?>"><?php esc_html_e( 'Cancel', 'alynt-account-gateway' ); ?></a>
		</div>
		<?php
	}
}
