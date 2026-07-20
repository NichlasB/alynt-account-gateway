<?php
/**
 * Dashboard endpoint metadata.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds guidance, shortcuts, and next steps for WooCommerce endpoints.
 */
class ALYNT_AG_Dashboard_Endpoint_Metadata {

	/**
	 * WooCommerce integration.
	 *
	 * @var ALYNT_AG_WooCommerce_Integration
	 */
	private $woocommerce;

	/**
	 * Constructor.
	 *
	 * @param ALYNT_AG_WooCommerce_Integration $woocommerce WooCommerce integration.
	 */
	public function __construct( $woocommerce ) {
		$this->woocommerce = $woocommerce;
	}

	/**
	 * Return endpoint guidance.
	 *
	 * @param string $endpoint Endpoint key.
	 * @return array<string,string>
	 */
	public function guidance( $endpoint ) {
		$guidance = array(
			'orders'                     => array(
				'label'       => __( 'Order History', 'alynt-account-gateway' ),
				'description' => __( 'Track purchase status, review order details, and open individual orders for more information.', 'alynt-account-gateway' ),
			),
			'view-order'                 => array(
				'label'       => __( 'Order Details', 'alynt-account-gateway' ),
				'description' => __( 'Review the selected order, including status, line items, totals, and available order actions.', 'alynt-account-gateway' ),
			),
			'downloads'                  => array(
				'label'       => __( 'Downloads', 'alynt-account-gateway' ),
				'description' => __( 'Access available digital purchases and download files connected to your account.', 'alynt-account-gateway' ),
			),
			'edit-address'               => array(
				'label'       => __( 'Billing and Shipping Addresses', 'alynt-account-gateway' ),
				'description' => __( 'Keep your billing and shipping details current so future checkouts stay quick and accurate.', 'alynt-account-gateway' ),
			),
			'edit-account'               => array(
				'label'       => __( 'Account Details', 'alynt-account-gateway' ),
				'description' => __( 'Update your name, email address, and password using WooCommerce account controls.', 'alynt-account-gateway' ),
			),
			'payment-methods'            => array(
				'label'       => __( 'Saved Payment Methods', 'alynt-account-gateway' ),
				'description' => __( 'Manage saved payment methods when your store supports secure payment method storage.', 'alynt-account-gateway' ),
			),
			'add-payment-method'         => array(
				'label'       => __( 'Add Payment Method', 'alynt-account-gateway' ),
				'description' => __( 'Add a new saved payment method through WooCommerce and the connected payment provider.', 'alynt-account-gateway' ),
			),
			'delete-payment-method'      => array(
				'label'       => __( 'Delete Payment Method', 'alynt-account-gateway' ),
				'description' => __( 'Confirm removal of a saved payment method from your customer account.', 'alynt-account-gateway' ),
			),
			'set-default-payment-method' => array(
				'label'       => __( 'Default Payment Method', 'alynt-account-gateway' ),
				'description' => __( 'Choose which saved payment method should be used first when the store supports defaults.', 'alynt-account-gateway' ),
			),
		);

		return isset( $guidance[ $endpoint ] ) ? $guidance[ $endpoint ] : array();
	}

	/**
	 * Return endpoint shortcut links.
	 *
	 * @param string              $endpoint Endpoint key.
	 * @param array<string,mixed> $settings Settings.
	 * @return array<int,array<string,string>>
	 */
	public function actions( $endpoint, $settings ) {
		$endpoint = sanitize_key( $endpoint );
		$actions  = array(
			'orders'                     => array(
				$this->action( __( 'Manage addresses', 'alynt-account-gateway' ), 'edit-address', $settings ),
				$this->action( __( 'Account details', 'alynt-account-gateway' ), 'edit-account', $settings ),
			),
			'view-order'                 => array(
				$this->action( __( 'Back to orders', 'alynt-account-gateway' ), 'orders', $settings ),
				$this->action( __( 'Manage addresses', 'alynt-account-gateway' ), 'edit-address', $settings ),
			),
			'downloads'                  => array(
				$this->action( __( 'View orders', 'alynt-account-gateway' ), 'orders', $settings ),
				$this->action( __( 'Account details', 'alynt-account-gateway' ), 'edit-account', $settings ),
			),
			'edit-address'               => array(
				$this->action( __( 'View orders', 'alynt-account-gateway' ), 'orders', $settings ),
				$this->action( __( 'Account details', 'alynt-account-gateway' ), 'edit-account', $settings ),
			),
			'edit-account'               => array(
				$this->action( __( 'Manage addresses', 'alynt-account-gateway' ), 'edit-address', $settings ),
				$this->action( __( 'Payment methods', 'alynt-account-gateway' ), 'payment-methods', $settings ),
			),
			'payment-methods'            => array(
				$this->action( __( 'Add payment method', 'alynt-account-gateway' ), 'add-payment-method', $settings ),
				$this->action( __( 'Account details', 'alynt-account-gateway' ), 'edit-account', $settings ),
			),
			'add-payment-method'         => array(
				$this->action( __( 'Saved payment methods', 'alynt-account-gateway' ), 'payment-methods', $settings ),
				$this->action( __( 'Account details', 'alynt-account-gateway' ), 'edit-account', $settings ),
			),
			'delete-payment-method'      => array(
				$this->action( __( 'Saved payment methods', 'alynt-account-gateway' ), 'payment-methods', $settings ),
			),
			'set-default-payment-method' => array(
				$this->action( __( 'Saved payment methods', 'alynt-account-gateway' ), 'payment-methods', $settings ),
			),
		);

		return isset( $actions[ $endpoint ] ) ? $actions[ $endpoint ] : array();
	}

	/**
	 * Return endpoint next-step panels.
	 *
	 * @param string              $endpoint Endpoint key.
	 * @param array<string,mixed> $settings Settings.
	 * @return array<int,array<string,string>>
	 */
	public function affordances( $endpoint, $settings ) {
		$endpoint = sanitize_key( $endpoint );
		$items    = array(
			'orders'          => array(
				$this->affordance(
					__( 'No orders yet?', 'alynt-account-gateway' ),
					__( 'Once you place an order, its status, details, and available actions will appear here.', 'alynt-account-gateway' ),
					__( 'Manage addresses', 'alynt-account-gateway' ),
					'edit-address',
					$settings
				),
			),
			'downloads'       => array(
				$this->affordance(
					__( 'No downloads available?', 'alynt-account-gateway' ),
					__( 'Downloadable files appear here after an eligible digital purchase is connected to your account.', 'alynt-account-gateway' ),
					__( 'View orders', 'alynt-account-gateway' ),
					'orders',
					$settings
				),
			),
			'edit-address'    => array(
				$this->affordance(
					__( 'Keep checkout details current', 'alynt-account-gateway' ),
					__( 'Review billing and shipping information before your next checkout so totals, tax, and delivery details stay accurate.', 'alynt-account-gateway' ),
					__( 'View orders', 'alynt-account-gateway' ),
					'orders',
					$settings
				),
			),
			'edit-account'    => array(
				$this->affordance(
					__( 'Account changes affect future orders', 'alynt-account-gateway' ),
					__( 'Use a current email address so order updates, password resets, and account notices can reach you.', 'alynt-account-gateway' ),
					__( 'Manage addresses', 'alynt-account-gateway' ),
					'edit-address',
					$settings
				),
			),
			'payment-methods' => array(
				$this->affordance(
					__( 'No saved payment methods?', 'alynt-account-gateway' ),
					__( 'Saved methods appear here only when the store and payment provider support secure customer payment storage.', 'alynt-account-gateway' ),
					__( 'Add payment method', 'alynt-account-gateway' ),
					'add-payment-method',
					$settings
				),
			),
		);

		return isset( $items[ $endpoint ] ) ? $items[ $endpoint ] : array();
	}

	/**
	 * Build one translated shortcut.
	 *
	 * @param string              $label    Label source.
	 * @param string              $endpoint Endpoint key.
	 * @param array<string,mixed> $settings Settings.
	 * @return array<string,string>
	 */
	private function action( $label, $endpoint, $settings ) {
		return array(
			'label' => $label,
			'url'   => $this->woocommerce->endpoint_url( $endpoint, $settings ),
		);
	}

	/**
	 * Build one next-step panel.
	 *
	 * @param string              $title       Title.
	 * @param string              $description Description.
	 * @param string              $label       Link label.
	 * @param string              $endpoint    Endpoint key.
	 * @param array<string,mixed> $settings    Settings.
	 * @return array<string,string>
	 */
	private function affordance( $title, $description, $label, $endpoint, $settings ) {
		return array(
			'title'       => $title,
			'description' => $description,
			'label'       => $label,
			'url'         => $this->woocommerce->endpoint_url( $endpoint, $settings ),
		);
	}
}
