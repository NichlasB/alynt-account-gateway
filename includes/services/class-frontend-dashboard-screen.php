<?php
/**
 * Frontend dashboard screen helper.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the frontend account dashboard.
 */
class ALYNT_AG_Frontend_Dashboard_Screen {

	/**
	 * Dashboard service.
	 *
	 * @var ALYNT_AG_Dashboard_Service
	 */
	private $dashboard;

	/**
	 * WooCommerce integration.
	 *
	 * @var ALYNT_AG_WooCommerce_Integration
	 */
	private $woocommerce;

	/**
	 * Frontend branding helpers.
	 *
	 * @var ALYNT_AG_Frontend_Branding
	 */
	private $branding;

	/**
	 * Constructor.
	 *
	 * @param ALYNT_AG_Dashboard_Service|null       $dashboard   Dashboard service.
	 * @param ALYNT_AG_WooCommerce_Integration|null $woocommerce WooCommerce integration.
	 * @param ALYNT_AG_Frontend_Branding|null       $branding    Branding helpers.
	 */
	public function __construct( $dashboard = null, $woocommerce = null, $branding = null ) {
		$this->dashboard   = $dashboard ? $dashboard : new ALYNT_AG_Dashboard_Service();
		$this->woocommerce = $woocommerce ? $woocommerce : new ALYNT_AG_WooCommerce_Integration();
		$this->branding    = $branding ? $branding : new ALYNT_AG_Frontend_Branding();
	}

	/**
	 * Render dashboard shell.
	 *
	 * @param array<string,mixed> $settings     Settings.
	 * @param string              $current_path Current relative request path.
	 * @return void
	 */
	public function render_dashboard_shell( $settings, $current_path = '' ) {
		$style = $this->branding->style_attribute( $settings );
		?>
		<main class="alynt-ag-gateway agw-dashboard" data-agw-screen="dashboard" style="<?php echo esc_attr( $style ); ?>">
			<div class="agw-dashboard__inner">
				<header class="agw-dashboard__header">
					<?php $this->branding->render_brand_block( $settings ); ?>
					<a class="agw-dashboard__logout" href="<?php echo esc_url( wp_logout_url( home_url( $settings['login_path'] ) ) ); ?>"><?php esc_html_e( 'Log Out', 'alynt-account-gateway' ); ?></a>
				</header>
				<?php $this->render_dashboard_screen( $settings, $current_path ); ?>
			</div>
		</main>
		<?php
	}

	/**
	 * Render custom account dashboard.
	 *
	 * @param array<string,mixed> $settings     Settings.
	 * @param string              $current_path Current relative request path.
	 * @return void
	 */
	public function render_dashboard_screen( $settings, $current_path = '' ) {
		$user                     = wp_get_current_user();
		$endpoint                 = $this->woocommerce->endpoint_from_path( $current_path, $settings );
		$links                    = $this->dashboard->links_for_user( $user, $settings );
		$name                     = $user->display_name ? $user->display_name : $user->user_email;
		$is_woocommerce_dashboard = ! empty( $settings['woocommerce_takeover'] )
			&& $this->dashboard->woocommerce_available()
			&& 'dashboard' === $endpoint['endpoint'];
		?>
		<section class="agw-dashboard-hero" aria-labelledby="agw-screen-title">
			<p class="agw-dashboard-hero__eyebrow"><?php esc_html_e( 'Account Dashboard', 'alynt-account-gateway' ); ?></p>
			<h1 id="agw-screen-title" class="agw-dashboard-hero__title">
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: user display name. */
						__( 'Welcome, %s', 'alynt-account-gateway' ),
						$name
					)
				);
				?>
			</h1>
			<p class="agw-dashboard-hero__meta"><?php echo esc_html( $user->user_email ); ?></p>
		</section>

		<?php if ( ! empty( $settings['woocommerce_takeover'] ) && ! $this->dashboard->woocommerce_available() ) : ?>
			<div class="agw-status agw-status--error" role="alert">
				<?php esc_html_e( 'WooCommerce account takeover is enabled, but WooCommerce is not active.', 'alynt-account-gateway' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $is_woocommerce_dashboard ) : ?>
			<?php $this->render_woocommerce_dashboard_overview( $settings ); ?>
		<?php endif; ?>

		<section class="agw-dashboard-section" aria-labelledby="agw-dashboard-links-title">
			<h2 id="agw-dashboard-links-title"><?php esc_html_e( 'Manage Account', 'alynt-account-gateway' ); ?></h2>
			<div class="agw-dashboard-grid">
				<?php foreach ( $links as $link ) : ?>
					<a class="agw-dashboard-link" href="<?php echo esc_url( $link['url'] ); ?>" target="<?php echo esc_attr( $link['target'] ); ?>" <?php echo '_blank' === $link['target'] ? 'rel="noopener noreferrer"' : ''; ?>>
						<span class="agw-dashboard-link__icon agw-dashboard-link__icon--<?php echo esc_attr( $link['icon'] ); ?>" aria-hidden="true"></span>
						<span class="agw-dashboard-link__label"><?php echo esc_html( $link['label'] ); ?></span>
						<?php if ( '_blank' === $link['target'] ) : ?>
							<span class="screen-reader-text"><?php esc_html_e( 'opens in a new tab', 'alynt-account-gateway' ); ?></span>
						<?php endif; ?>
					</a>
				<?php endforeach; ?>
			</div>
		</section>

		<?php if ( ! empty( $settings['woocommerce_takeover'] ) && 'dashboard' !== $endpoint['endpoint'] ) : ?>
			<?php $guidance = $this->woocommerce_endpoint_guidance( $endpoint['endpoint'] ); ?>
			<section class="agw-dashboard-section agw-dashboard-section--content" aria-labelledby="agw-dashboard-content-title">
				<h2 id="agw-dashboard-content-title">
					<?php echo esc_html( $this->woocommerce->endpoint_labels()[ $endpoint['endpoint'] ] ?? __( 'Account', 'alynt-account-gateway' ) ); ?>
				</h2>
				<?php if ( ! empty( $guidance ) ) : ?>
					<div class="agw-dashboard-guidance">
						<span class="agw-dashboard-guidance__label"><?php echo esc_html( $guidance['label'] ); ?></span>
						<p><?php echo esc_html( $guidance['description'] ); ?></p>
					</div>
				<?php endif; ?>
				<div class="agw-dashboard-content">
					<?php if ( ! $this->woocommerce->render_endpoint( $endpoint['endpoint'], $endpoint['value'] ) ) : ?>
						<p><?php esc_html_e( 'This account section is not available.', 'alynt-account-gateway' ); ?></p>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>
		<?php
	}

	/**
	 * Return endpoint guidance copy for standard WooCommerce account endpoints.
	 *
	 * @param string $endpoint WooCommerce endpoint key.
	 * @return array<string,string>
	 */
	private function woocommerce_endpoint_guidance( $endpoint ) {
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
	 * Render a customer-focused WooCommerce dashboard overview.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_woocommerce_dashboard_overview( $settings ) {
		$actions = array(
			array(
				'endpoint'    => 'orders',
				'label'       => __( 'View Orders', 'alynt-account-gateway' ),
				'description' => __( 'Check recent purchases, order status, and order details.', 'alynt-account-gateway' ),
			),
			array(
				'endpoint'    => 'edit-address',
				'label'       => __( 'Manage Addresses', 'alynt-account-gateway' ),
				'description' => __( 'Keep billing and shipping information ready for checkout.', 'alynt-account-gateway' ),
			),
			array(
				'endpoint'    => 'edit-account',
				'label'       => __( 'Account Details', 'alynt-account-gateway' ),
				'description' => __( 'Update your name, email address, and account password.', 'alynt-account-gateway' ),
			),
		);
		?>
		<section class="agw-dashboard-overview" aria-labelledby="agw-dashboard-overview-title">
			<div class="agw-dashboard-overview__copy">
				<p class="agw-dashboard-overview__eyebrow"><?php esc_html_e( 'Customer Account', 'alynt-account-gateway' ); ?></p>
				<h2 id="agw-dashboard-overview-title"><?php esc_html_e( 'Everything for your orders in one place', 'alynt-account-gateway' ); ?></h2>
				<p><?php esc_html_e( 'Review purchases, manage checkout details, and keep your account information current without leaving the branded account area.', 'alynt-account-gateway' ); ?></p>
			</div>
			<div class="agw-dashboard-overview__actions" aria-label="<?php esc_attr_e( 'Customer account shortcuts', 'alynt-account-gateway' ); ?>">
				<?php foreach ( $actions as $action ) : ?>
					<a class="agw-dashboard-overview__action" href="<?php echo esc_url( $this->woocommerce->endpoint_url( $action['endpoint'], $settings ) ); ?>">
						<span class="agw-dashboard-overview__action-label"><?php echo esc_html( $action['label'] ); ?></span>
						<span class="agw-dashboard-overview__action-description"><?php echo esc_html( $action['description'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}
}
