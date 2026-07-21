<?php
/**
 * WooCommerce integration.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coordinates WooCommerce account integration.
 */
class ALYNT_AG_WooCommerce_Integration {
	use ALYNT_AG_WooCommerce_Collaborators;

	/**
	 * Focused collaborators.
	 *
	 * @var array<string,object|null>
	 */
	private $collaborators;

	/**
	 * Constructor.
	 *
	 * @param array<string,object> $collaborators Optional test collaborators.
	 */
	public function __construct( $collaborators = array() ) {
		$this->collaborators = array(
			'navigation' => $collaborators['navigation'] ?? null,
			'routing'    => $collaborators['routing'] ?? null,
			'renderer'   => $collaborators['renderer'] ?? null,
			'data'       => $collaborators['data'] ?? null,
		);
	}

	/**
	 * Standard account endpoint labels.
	 *
	 * @return array<string,string>
	 */
	public function standard_endpoint_labels() {
		return $this->collaborator( 'navigation' )->standard_endpoint_labels();
	}

	/**
	 * Account endpoint labels.
	 *
	 * @return array<string,string>
	 */
	public function endpoint_labels() {
		return $this->collaborator( 'navigation' )->endpoint_labels();
	}

	/**
	 * Return WooCommerce account menu items.
	 *
	 * @return array<string,string>
	 */
	public function account_menu_items() {
		return $this->collaborator( 'navigation' )->account_menu_items();
	}

	/**
	 * Return standard WooCommerce account menu items.
	 *
	 * @return array<string,string>
	 */
	public function standard_account_menu_items() {
		return $this->collaborator( 'navigation' )->standard_account_menu_items();
	}

	/**
	 * Return hidden dashboard endpoint keys.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return array<int,string>
	 */
	public function hidden_account_menu_items( $settings ) {
		return $this->collaborator( 'navigation' )->hidden_account_menu_items( $settings );
	}

	/**
	 * Return whether an endpoint is visible.
	 *
	 * @param string              $endpoint Endpoint key.
	 * @param array<string,mixed> $settings Settings.
	 * @return bool
	 */
	public function is_account_menu_item_visible( $endpoint, $settings ) {
		return $this->collaborator( 'navigation' )->is_account_menu_item_visible( $endpoint, $settings );
	}

	/**
	 * Return visible account menu items.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return array<string,string>
	 */
	public function visible_account_menu_items( $settings ) {
		return $this->collaborator( 'navigation' )->visible_account_menu_items( $settings );
	}

	/**
	 * Build dashboard account links.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return array<int,array<string,mixed>>
	 */
	public function account_menu_links( $settings ) {
		return $this->collaborator( 'navigation' )->account_menu_links( $settings );
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'plugins_loaded', array( $this, 'detect' ), 20 );
		add_action( 'template_redirect', array( $this, 'maybe_handle_account_form_post' ), 0 );
	}

	/**
	 * Detect WooCommerce availability.
	 *
	 * @return bool
	 */
	public function detect() {
		return class_exists( 'WooCommerce' );
	}

	/**
	 * Whether WooCommerce account takeover can run.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return bool
	 */
	public function takeover_enabled( $settings ) {
		return ! empty( $settings['dashboard_enabled'] ) && ! empty( $settings['woocommerce_takeover'] ) && $this->detect();
	}

	/**
	 * Process native account form POSTs.
	 *
	 * @return void
	 */
	public function maybe_handle_account_form_post() {
		$this->collaborator( 'routing' )->maybe_handle_account_form_post();
	}

	/**
	 * Return endpoint data for a dashboard path.
	 *
	 * @param string              $path     Current relative path.
	 * @param array<string,mixed> $settings Settings.
	 * @return array<string,mixed>
	 */
	public function endpoint_from_path( $path, $settings ) {
		return $this->collaborator( 'routing' )->endpoint_from_path( $path, $settings );
	}

	/**
	 * Render native endpoint content.
	 *
	 * @param string $endpoint Endpoint key.
	 * @param string $value    Endpoint value.
	 * @return bool
	 */
	public function render_endpoint( $endpoint, $value = '' ) {
		return $this->collaborator( 'renderer' )->render_endpoint( $endpoint, $value );
	}

	/**
	 * Return normalized recent orders.
	 *
	 * @param int $user_id WordPress user ID.
	 * @param int $limit   Maximum rows.
	 * @return array<int,array<string,mixed>>
	 */
	public function recent_orders( $user_id, $limit = 3 ) {
		return $this->collaborator( 'data' )->recent_orders( $user_id, $limit );
	}

	/**
	 * Return normalized downloads.
	 *
	 * @param int $user_id WordPress user ID.
	 * @param int $limit   Maximum rows.
	 * @return array<int,array<string,mixed>>
	 */
	public function available_downloads( $user_id, $limit = 3 ) {
		return $this->collaborator( 'data' )->available_downloads( $user_id, $limit );
	}

	/**
	 * Return normalized account details.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return array<string,mixed>
	 */
	public function account_details( $user_id ) {
		return $this->collaborator( 'data' )->account_details( $user_id );
	}

	/**
	 * Return normalized payment methods.
	 *
	 * @param int $user_id WordPress user ID.
	 * @param int $limit   Maximum rows.
	 * @return array<int,array<string,mixed>>
	 */
	public function saved_payment_methods( $user_id, $limit = 3 ) {
		return $this->collaborator( 'data' )->saved_payment_methods( $user_id, $limit );
	}

	/**
	 * Return normalized saved addresses.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return array<string,array<int,string>>
	 */
	public function saved_addresses( $user_id ) {
		return $this->collaborator( 'data' )->saved_addresses( $user_id );
	}

	/**
	 * Build an order-details URL.
	 *
	 * @param int                 $order_id Order ID.
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	public function order_url( $order_id, $settings ) {
		return $this->collaborator( 'navigation' )->order_url( $order_id, $settings );
	}

	/**
	 * Build an address-editor URL.
	 *
	 * @param string              $type     Address type.
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	public function address_url( $type, $settings ) {
		return $this->collaborator( 'navigation' )->address_url( $type, $settings );
	}

	/**
	 * Build an account endpoint URL.
	 *
	 * @param string              $endpoint Endpoint key.
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	public function endpoint_url( $endpoint, $settings ) {
		return $this->collaborator( 'navigation' )->endpoint_url( $endpoint, $settings );
	}

	/**
	 * Merge required standard account items into WooCommerce-provided items.
	 *
	 * Retained as a compatibility shim for the established service contract.
	 *
	 * @param array<string,string> $items          WooCommerce menu items.
	 * @param array<string,string> $standard_items Standard required menu items.
	 * @return array<string,string>
	 */
	private function merge_standard_account_menu_items( $items, $standard_items ) {
		return $this->collaborator( 'navigation' )->merge_standard_account_menu_items( $items, $standard_items );
	}
}
