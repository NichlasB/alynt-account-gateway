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
		$dir   = is_rtl() ? 'rtl' : 'ltr';
		?>
		<main class="alynt-ag-gateway agw-dashboard" data-agw-screen="dashboard" dir="<?php echo esc_attr( $dir ); ?>" style="<?php echo esc_attr( $style ); ?>">
			<div class="agw-dashboard__inner">
				<header class="agw-dashboard__header">
					<?php $this->branding->render_brand_block( $settings ); ?>
					<?php $this->render_dashboard_actions( $settings ); ?>
				</header>
				<?php $this->render_offcanvas_menu( $settings ); ?>
				<?php $this->render_dashboard_screen( $settings, $current_path ); ?>
				<?php $this->render_footer_menu( $settings ); ?>
			</div>
		</main>
		<?php
	}

	/**
	 * Render dashboard header icon actions.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_dashboard_actions( $settings ) {
		$logout_url        = wp_logout_url( home_url( $settings['login_path'] ) );
		$offcanvas_enabled = $this->offcanvas_enabled( $settings );
		?>
		<nav class="agw-dashboard-actions" aria-label="<?php esc_attr_e( 'Dashboard actions', 'alynt-account-gateway' ); ?>">
			<?php if ( $offcanvas_enabled ) : ?>
				<button
					type="button"
					class="agw-dashboard-action agw-dashboard-action--menu"
					aria-label="<?php esc_attr_e( 'Open account menu', 'alynt-account-gateway' ); ?>"
					aria-controls="agw-dashboard-offcanvas"
					aria-expanded="false"
					data-agw-offcanvas-open
				>
					<?php $this->render_dashboard_icon( 'menu' ); ?>
				</button>
			<?php endif; ?>
			<a class="agw-dashboard-action agw-dashboard-action--home" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Go to homepage', 'alynt-account-gateway' ); ?>">
				<?php $this->render_dashboard_icon( 'home' ); ?>
			</a>
			<a class="agw-dashboard__logout agw-dashboard-action agw-dashboard-action--logout" href="<?php echo esc_url( $logout_url ); ?>" aria-label="<?php esc_attr_e( 'Log out', 'alynt-account-gateway' ); ?>">
				<?php $this->render_dashboard_icon( 'logout' ); ?>
			</a>
		</nav>
		<?php
	}

	/**
	 * Render the optional dashboard off-canvas menu.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_offcanvas_menu( $settings ) {
		if ( ! $this->offcanvas_enabled( $settings ) ) {
			return;
		}

		$menu_id = absint( $settings['dashboard_offcanvas_menu_id'] ?? 0 );
		?>
		<div class="agw-offcanvas" id="agw-dashboard-offcanvas" aria-hidden="true" data-agw-offcanvas>
			<div class="agw-offcanvas__backdrop" data-agw-offcanvas-close></div>
			<aside class="agw-offcanvas__panel" role="dialog" aria-modal="true" aria-labelledby="agw-offcanvas-title" tabindex="-1" data-agw-offcanvas-panel>
				<div class="agw-offcanvas__header">
					<h2 id="agw-offcanvas-title"><?php esc_html_e( 'Menu', 'alynt-account-gateway' ); ?></h2>
					<button type="button" class="agw-offcanvas__close" aria-label="<?php esc_attr_e( 'Close account menu', 'alynt-account-gateway' ); ?>" data-agw-offcanvas-close>
						<?php $this->render_dashboard_icon( 'close' ); ?>
					</button>
				</div>
				<nav class="agw-offcanvas__nav" aria-label="<?php esc_attr_e( 'Account menu', 'alynt-account-gateway' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'menu'        => $menu_id,
							'container'   => false,
							'menu_class'  => 'agw-offcanvas__menu',
							'fallback_cb' => false,
							'depth'       => 2,
							'walker'      => class_exists( 'ALYNT_AG_Offcanvas_Menu_Walker' ) ? new ALYNT_AG_Offcanvas_Menu_Walker() : '',
						)
					);
					?>
				</nav>
			</aside>
		</div>
		<?php
	}

	/**
	 * Return whether the off-canvas dashboard menu is configured.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return bool
	 */
	private function offcanvas_enabled( $settings ) {
		return ! empty( $settings['dashboard_offcanvas_enabled'] )
			&& ! empty( $settings['dashboard_offcanvas_menu_id'] )
			&& function_exists( 'wp_nav_menu' );
	}

	/**
	 * Render the optional dashboard footer menu.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_footer_menu( $settings ) {
		if ( ! $this->footer_menu_enabled( $settings ) ) {
			return;
		}

		$menu_html = wp_nav_menu(
			array(
				'menu'        => absint( $settings['dashboard_footer_menu_id'] ),
				'container'   => false,
				'menu_class'  => 'agw-dashboard-footer__menu',
				'fallback_cb' => false,
				'depth'       => 1,
				'echo'        => false,
			)
		);

		if ( ! is_string( $menu_html ) || '' === trim( $menu_html ) ) {
			return;
		}
		?>
		<footer class="agw-dashboard-footer">
			<nav class="agw-dashboard-footer__nav" aria-label="<?php esc_attr_e( 'Dashboard footer navigation', 'alynt-account-gateway' ); ?>">
				<?php echo $menu_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Generated by wp_nav_menu(). ?>
			</nav>
		</footer>
		<?php
	}

	/**
	 * Return whether the dashboard footer menu is configured.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return bool
	 */
	private function footer_menu_enabled( $settings ) {
		return ! empty( $settings['dashboard_footer_menu_enabled'] )
			&& ! empty( $settings['dashboard_footer_menu_id'] )
			&& function_exists( 'wp_nav_menu' );
	}

	/**
	 * Render an inline dashboard icon.
	 *
	 * @param string $icon Icon key.
	 * @return void
	 */
	private function render_dashboard_icon( $icon ) {
		$paths = array(
			'menu'   => '<path d="M4 7h16M4 12h16M4 17h16" />',
			'home'   => '<path d="M4 11.5 12 5l8 6.5" /><path d="M6.5 10.5V20h11v-9.5" /><path d="M10 20v-5h4v5" />',
			'logout' => '<path d="M10 6H6.5A1.5 1.5 0 0 0 5 7.5v9A1.5 1.5 0 0 0 6.5 18H10" /><path d="M14 8l4 4-4 4" /><path d="M9 12h9" />',
			'close'  => '<path d="m7 7 10 10" /><path d="M17 7 7 17" />',
		);

		if ( empty( $paths[ $icon ] ) ) {
			return;
		}

		echo '<svg class="agw-dashboard-action__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $paths[ $icon ] . '</svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG paths selected by key.
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
		$name                     = trim( (string) get_user_meta( $user->ID, 'first_name', true ) );
		$name                     = $name ? $name : __( 'there', 'alynt-account-gateway' );
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
						/* translators: %s: user first name or a neutral fallback. */
						__( 'Welcome, %s', 'alynt-account-gateway' ),
						$name
					)
				);
				?>
			</h1>
			<p class="agw-dashboard-hero__meta"><?php echo esc_html( $user->user_email ); ?></p>
		</section>

		<?php if ( ! empty( $settings['woocommerce_takeover'] ) && ! $this->dashboard->woocommerce_available() ) : ?>
			<div class="agw-status agw-status--error" role="alert" aria-live="assertive" aria-atomic="true">
				<?php esc_html_e( 'WooCommerce account takeover is enabled, but WooCommerce is not active.', 'alynt-account-gateway' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $is_woocommerce_dashboard ) : ?>
			<?php $this->render_woocommerce_dashboard_overview( $settings ); ?>
			<?php $this->render_recent_orders( $user->ID, $settings ); ?>
			<?php $this->render_available_downloads( $user->ID, $settings ); ?>
			<?php $this->render_saved_addresses( $user->ID, $settings ); ?>
		<?php endif; ?>

		<section class="agw-dashboard-section" aria-labelledby="agw-dashboard-links-title">
			<h2 id="agw-dashboard-links-title"><?php esc_html_e( 'Manage Account', 'alynt-account-gateway' ); ?></h2>
			<div class="agw-dashboard-grid">
				<?php foreach ( $links as $link ) : ?>
					<?php $current_attribute = $this->dashboard_current_attribute( $link['url'], $current_path ); ?>
					<a class="agw-dashboard-link" href="<?php echo esc_url( $link['url'] ); ?>" target="<?php echo esc_attr( $link['target'] ); ?>"<?php echo '_blank' === $link['target'] ? ' rel="noopener noreferrer"' : ''; ?><?php echo $current_attribute; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by dashboard_current_attribute(). ?>>
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
			<?php $actions = $this->woocommerce_endpoint_actions( $endpoint['endpoint'], $settings ); ?>
			<?php $guidance = $this->woocommerce_endpoint_guidance( $endpoint['endpoint'] ); ?>
			<?php $affordances = $this->woocommerce_endpoint_affordances( $endpoint['endpoint'], $settings ); ?>
			<section class="agw-dashboard-section agw-dashboard-section--content" aria-labelledby="agw-dashboard-content-title">
				<h2 id="agw-dashboard-content-title">
					<?php echo esc_html( $this->woocommerce->endpoint_labels()[ $endpoint['endpoint'] ] ?? __( 'Account', 'alynt-account-gateway' ) ); ?>
				</h2>
					<?php if ( ! empty( $actions ) ) : ?>
					<nav class="agw-dashboard-section-actions" aria-label="<?php esc_attr_e( 'Account section shortcuts', 'alynt-account-gateway' ); ?>">
						<?php foreach ( $actions as $action ) : ?>
							<a href="<?php echo esc_url( $action['url'] ); ?>"<?php echo $this->dashboard_current_attribute( $action['url'], $current_path ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by dashboard_current_attribute(). ?>><?php echo esc_html( $action['label'] ); ?></a>
						<?php endforeach; ?>
					</nav>
				<?php endif; ?>
				<?php if ( ! empty( $guidance ) ) : ?>
					<div class="agw-dashboard-guidance">
						<span class="agw-dashboard-guidance__label"><?php echo esc_html( $guidance['label'] ); ?></span>
						<p><?php echo esc_html( $guidance['description'] ); ?></p>
					</div>
				<?php endif; ?>
				<?php if ( ! empty( $affordances ) ) : ?>
					<div class="agw-dashboard-affordances" aria-label="<?php esc_attr_e( 'Helpful account next steps', 'alynt-account-gateway' ); ?>">
						<?php foreach ( $affordances as $affordance ) : ?>
							<div class="agw-dashboard-affordance">
								<span class="agw-dashboard-affordance__title"><?php echo esc_html( $affordance['title'] ); ?></span>
								<p><?php echo esc_html( $affordance['description'] ); ?></p>
								<?php if ( ! empty( $affordance['url'] ) && ! empty( $affordance['label'] ) ) : ?>
									<a href="<?php echo esc_url( $affordance['url'] ); ?>"><?php echo esc_html( $affordance['label'] ); ?></a>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<div class="agw-dashboard-content">
					<?php if ( ! $this->woocommerce->render_endpoint( $endpoint['endpoint'], $endpoint['value'] ) ) : ?>
						<?php $this->render_woocommerce_endpoint_unavailable( $endpoint['endpoint'], $settings ); ?>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>
		<?php
	}

	/**
	 * Return aria-current when a dashboard URL represents the current path.
	 *
	 * @param string $url          Link URL.
	 * @param string $current_path Current relative request path.
	 * @return string
	 */
	private function dashboard_current_attribute( $url, $current_path ) {
		return $this->is_current_dashboard_url( $url, $current_path ) ? ' aria-current="page"' : '';
	}

	/**
	 * Return whether a dashboard URL points at the current path.
	 *
	 * @param string $url          Link URL.
	 * @param string $current_path Current relative request path.
	 * @return bool
	 */
	private function is_current_dashboard_url( $url, $current_path ) {
		$url_path     = (string) wp_parse_url( (string) $url, PHP_URL_PATH );
		$current_path = (string) wp_parse_url( (string) $current_path, PHP_URL_PATH );

		if ( '' === $url_path || '' === $current_path ) {
			return false;
		}

		return untrailingslashit( '/' . ltrim( $url_path, '/' ) ) === untrailingslashit( '/' . ltrim( $current_path, '/' ) );
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
	 * Return compact navigation actions for standard WooCommerce endpoints.
	 *
	 * @param string              $endpoint WooCommerce endpoint key.
	 * @param array<string,mixed> $settings Settings.
	 * @return array<int,array<string,string>>
	 */
	private function woocommerce_endpoint_actions( $endpoint, $settings ) {
		$endpoint = sanitize_key( $endpoint );
		$actions  = array(
			'orders'                     => array(
				array(
					'label' => __( 'Manage addresses', 'alynt-account-gateway' ),
					'url'   => $this->woocommerce->endpoint_url( 'edit-address', $settings ),
				),
				array(
					'label' => __( 'Account details', 'alynt-account-gateway' ),
					'url'   => $this->woocommerce->endpoint_url( 'edit-account', $settings ),
				),
			),
			'view-order'                 => array(
				array(
					'label' => __( 'Back to orders', 'alynt-account-gateway' ),
					'url'   => $this->woocommerce->endpoint_url( 'orders', $settings ),
				),
				array(
					'label' => __( 'Manage addresses', 'alynt-account-gateway' ),
					'url'   => $this->woocommerce->endpoint_url( 'edit-address', $settings ),
				),
			),
			'downloads'                  => array(
				array(
					'label' => __( 'View orders', 'alynt-account-gateway' ),
					'url'   => $this->woocommerce->endpoint_url( 'orders', $settings ),
				),
				array(
					'label' => __( 'Account details', 'alynt-account-gateway' ),
					'url'   => $this->woocommerce->endpoint_url( 'edit-account', $settings ),
				),
			),
			'edit-address'               => array(
				array(
					'label' => __( 'View orders', 'alynt-account-gateway' ),
					'url'   => $this->woocommerce->endpoint_url( 'orders', $settings ),
				),
				array(
					'label' => __( 'Account details', 'alynt-account-gateway' ),
					'url'   => $this->woocommerce->endpoint_url( 'edit-account', $settings ),
				),
			),
			'edit-account'               => array(
				array(
					'label' => __( 'Manage addresses', 'alynt-account-gateway' ),
					'url'   => $this->woocommerce->endpoint_url( 'edit-address', $settings ),
				),
				array(
					'label' => __( 'Payment methods', 'alynt-account-gateway' ),
					'url'   => $this->woocommerce->endpoint_url( 'payment-methods', $settings ),
				),
			),
			'payment-methods'            => array(
				array(
					'label' => __( 'Add payment method', 'alynt-account-gateway' ),
					'url'   => $this->woocommerce->endpoint_url( 'add-payment-method', $settings ),
				),
				array(
					'label' => __( 'Account details', 'alynt-account-gateway' ),
					'url'   => $this->woocommerce->endpoint_url( 'edit-account', $settings ),
				),
			),
			'add-payment-method'         => array(
				array(
					'label' => __( 'Saved payment methods', 'alynt-account-gateway' ),
					'url'   => $this->woocommerce->endpoint_url( 'payment-methods', $settings ),
				),
				array(
					'label' => __( 'Account details', 'alynt-account-gateway' ),
					'url'   => $this->woocommerce->endpoint_url( 'edit-account', $settings ),
				),
			),
			'delete-payment-method'      => array(
				array(
					'label' => __( 'Saved payment methods', 'alynt-account-gateway' ),
					'url'   => $this->woocommerce->endpoint_url( 'payment-methods', $settings ),
				),
			),
			'set-default-payment-method' => array(
				array(
					'label' => __( 'Saved payment methods', 'alynt-account-gateway' ),
					'url'   => $this->woocommerce->endpoint_url( 'payment-methods', $settings ),
				),
			),
		);

		return isset( $actions[ $endpoint ] ) ? $actions[ $endpoint ] : array();
	}

	/**
	 * Return contextual next-step panels for standard WooCommerce endpoints.
	 *
	 * @param string              $endpoint WooCommerce endpoint key.
	 * @param array<string,mixed> $settings Settings.
	 * @return array<int,array<string,string>>
	 */
	private function woocommerce_endpoint_affordances( $endpoint, $settings ) {
		$endpoint = sanitize_key( $endpoint );
		$items    = array(
			'orders'          => array(
				array(
					'title'       => __( 'No orders yet?', 'alynt-account-gateway' ),
					'description' => __( 'Once you place an order, its status, details, and available actions will appear here.', 'alynt-account-gateway' ),
					'label'       => __( 'Manage addresses', 'alynt-account-gateway' ),
					'url'         => $this->woocommerce->endpoint_url( 'edit-address', $settings ),
				),
			),
			'downloads'       => array(
				array(
					'title'       => __( 'No downloads available?', 'alynt-account-gateway' ),
					'description' => __( 'Downloadable files appear here after an eligible digital purchase is connected to your account.', 'alynt-account-gateway' ),
					'label'       => __( 'View orders', 'alynt-account-gateway' ),
					'url'         => $this->woocommerce->endpoint_url( 'orders', $settings ),
				),
			),
			'edit-address'    => array(
				array(
					'title'       => __( 'Keep checkout details current', 'alynt-account-gateway' ),
					'description' => __( 'Review billing and shipping information before your next checkout so totals, tax, and delivery details stay accurate.', 'alynt-account-gateway' ),
					'label'       => __( 'View orders', 'alynt-account-gateway' ),
					'url'         => $this->woocommerce->endpoint_url( 'orders', $settings ),
				),
			),
			'edit-account'    => array(
				array(
					'title'       => __( 'Account changes affect future orders', 'alynt-account-gateway' ),
					'description' => __( 'Use a current email address so order updates, password resets, and account notices can reach you.', 'alynt-account-gateway' ),
					'label'       => __( 'Manage addresses', 'alynt-account-gateway' ),
					'url'         => $this->woocommerce->endpoint_url( 'edit-address', $settings ),
				),
			),
			'payment-methods' => array(
				array(
					'title'       => __( 'No saved payment methods?', 'alynt-account-gateway' ),
					'description' => __( 'Saved methods appear here only when the store and payment provider support secure customer payment storage.', 'alynt-account-gateway' ),
					'label'       => __( 'Add payment method', 'alynt-account-gateway' ),
					'url'         => $this->woocommerce->endpoint_url( 'add-payment-method', $settings ),
				),
			),
		);

		return isset( $items[ $endpoint ] ) ? $items[ $endpoint ] : array();
	}

	/**
	 * Render a branded fallback when WooCommerce does not output endpoint content.
	 *
	 * @param string              $endpoint Endpoint key.
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_woocommerce_endpoint_unavailable( $endpoint, $settings ) {
		$labels = $this->woocommerce->endpoint_labels();
		$title  = $labels[ $endpoint ] ?? __( 'Account section', 'alynt-account-gateway' );
		?>
		<div class="agw-dashboard-empty" role="status" aria-live="polite" aria-atomic="true">
			<span class="agw-dashboard-empty__eyebrow"><?php esc_html_e( 'Account section unavailable', 'alynt-account-gateway' ); ?></span>
			<h3><?php esc_html_e( 'This area is not ready yet', 'alynt-account-gateway' ); ?></h3>
			<p>
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: WooCommerce account endpoint label. */
						__( 'WooCommerce did not return content for %s. Try another account area or come back after the store has finished configuring this section.', 'alynt-account-gateway' ),
						$title
					)
				);
				?>
			</p>
			<div class="agw-dashboard-empty__actions">
				<a class="agw-button agw-button--secondary" href="<?php echo esc_url( $this->woocommerce->endpoint_url( 'dashboard', $settings ) ); ?>">
					<?php esc_html_e( 'Back to dashboard', 'alynt-account-gateway' ); ?>
				</a>
				<a class="agw-button agw-button--primary" href="<?php echo esc_url( $this->woocommerce->endpoint_url( 'edit-account', $settings ) ); ?>">
					<?php esc_html_e( 'Manage account details', 'alynt-account-gateway' ); ?>
				</a>
			</div>
		</div>
		<?php
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
		$actions = array_values(
			array_filter(
				$actions,
				function ( $action ) use ( $settings ) {
					return $this->woocommerce->is_account_menu_item_visible( $action['endpoint'], $settings );
				}
			)
		);
		$class   = empty( $actions ) ? 'agw-dashboard-overview agw-dashboard-overview--without-actions' : 'agw-dashboard-overview';
		?>
		<section class="<?php echo esc_attr( $class ); ?>" aria-labelledby="agw-dashboard-overview-title">
			<div class="agw-dashboard-overview__copy">
				<p class="agw-dashboard-overview__eyebrow"><?php esc_html_e( 'Customer Account', 'alynt-account-gateway' ); ?></p>
				<h2 id="agw-dashboard-overview-title"><?php esc_html_e( 'Everything for your orders in one place', 'alynt-account-gateway' ); ?></h2>
				<p><?php esc_html_e( 'Review purchases, manage checkout details, and keep your account information current without leaving the branded account area.', 'alynt-account-gateway' ); ?></p>
			</div>
			<?php if ( ! empty( $actions ) ) : ?>
				<div class="agw-dashboard-overview__actions" aria-label="<?php esc_attr_e( 'Customer account shortcuts', 'alynt-account-gateway' ); ?>">
					<?php foreach ( $actions as $action ) : ?>
						<a class="agw-dashboard-overview__action" href="<?php echo esc_url( $this->woocommerce->endpoint_url( $action['endpoint'], $settings ) ); ?>">
							<span class="agw-dashboard-overview__action-label"><?php echo esc_html( $action['label'] ); ?></span>
							<span class="agw-dashboard-overview__action-description"><?php echo esc_html( $action['description'] ); ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</section>
		<?php
	}

	/**
	 * Render a read-only recent-orders module on the WooCommerce dashboard.
	 *
	 * @param int                 $user_id  WordPress user ID.
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_recent_orders( $user_id, $settings ) {
		if ( ! $this->woocommerce->is_account_menu_item_visible( 'orders', $settings ) ) {
			return;
		}

		$orders = $this->woocommerce->recent_orders( $user_id, 3 );
		?>
		<section class="agw-dashboard-section agw-dashboard-recent-orders" aria-labelledby="agw-dashboard-recent-orders-title">
			<div class="agw-dashboard-recent-orders__header">
				<h2 id="agw-dashboard-recent-orders-title"><?php esc_html_e( 'Recent Orders', 'alynt-account-gateway' ); ?></h2>
				<a href="<?php echo esc_url( $this->woocommerce->endpoint_url( 'orders', $settings ) ); ?>">
					<?php esc_html_e( 'View all orders', 'alynt-account-gateway' ); ?>
				</a>
			</div>
			<?php if ( empty( $orders ) ) : ?>
				<p class="agw-dashboard-recent-orders__empty">
					<?php esc_html_e( 'Your recent orders will appear here after your first purchase.', 'alynt-account-gateway' ); ?>
				</p>
			<?php else : ?>
				<ul class="agw-dashboard-recent-orders__list" role="list">
					<?php foreach ( $orders as $order ) : ?>
						<li>
							<a class="agw-dashboard-recent-order" href="<?php echo esc_url( $this->woocommerce->order_url( $order['id'], $settings ) ); ?>">
								<span class="agw-dashboard-recent-order__identity">
									<strong>
										<?php
										echo esc_html(
											sprintf(
												/* translators: %s: customer-facing order number. */
												__( 'Order #%s', 'alynt-account-gateway' ),
												$order['number']
											)
										);
										?>
									</strong>
									<?php if ( ! empty( $order['date'] ) ) : ?>
										<span><?php echo esc_html( $order['date'] ); ?></span>
									<?php endif; ?>
								</span>
								<span class="agw-dashboard-recent-order__summary">
									<span class="agw-dashboard-recent-order__status"><?php echo esc_html( $order['status'] ); ?></span>
									<?php if ( ! empty( $order['total'] ) ) : ?>
										<span class="agw-dashboard-recent-order__total"><?php echo esc_html( $order['total'] ); ?></span>
									<?php endif; ?>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</section>
		<?php
	}

	/**
	 * Render a read-only available-downloads module on the WooCommerce dashboard.
	 *
	 * @param int                 $user_id  WordPress user ID.
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_available_downloads( $user_id, $settings ) {
		if ( ! $this->woocommerce->is_account_menu_item_visible( 'downloads', $settings ) ) {
			return;
		}

		$downloads = $this->woocommerce->available_downloads( $user_id, 3 );
		?>
		<section class="agw-dashboard-section agw-dashboard-downloads" aria-labelledby="agw-dashboard-downloads-title">
			<div class="agw-dashboard-downloads__header">
				<h2 id="agw-dashboard-downloads-title"><?php esc_html_e( 'Available Downloads', 'alynt-account-gateway' ); ?></h2>
				<a href="<?php echo esc_url( $this->woocommerce->endpoint_url( 'downloads', $settings ) ); ?>">
					<?php esc_html_e( 'View all downloads', 'alynt-account-gateway' ); ?>
				</a>
			</div>
			<?php if ( empty( $downloads ) ) : ?>
				<p class="agw-dashboard-downloads__empty">
					<?php esc_html_e( 'Your available files will appear here after a downloadable purchase.', 'alynt-account-gateway' ); ?>
				</p>
			<?php else : ?>
				<ul class="agw-dashboard-downloads__list" role="list">
					<?php foreach ( $downloads as $download ) : ?>
						<?php
						$download_label = sprintf(
							/* translators: %s: downloadable file name. */
							__( 'Download %s', 'alynt-account-gateway' ),
							$download['name']
						);
						?>
						<li class="agw-dashboard-download">
							<span class="agw-dashboard-download__identity">
								<strong><?php echo esc_html( $download['name'] ); ?></strong>
								<?php if ( ! empty( $download['product_name'] ) && $download['product_name'] !== $download['name'] ) : ?>
									<span><?php echo esc_html( $download['product_name'] ); ?></span>
								<?php endif; ?>
							</span>
							<span class="agw-dashboard-download__meta">
								<span>
									<?php
									echo esc_html(
										null === $download['remaining']
											? __( 'Unlimited downloads', 'alynt-account-gateway' )
											: sprintf(
												/* translators: %s: number of downloads remaining. */
												__( 'Downloads remaining: %s', 'alynt-account-gateway' ),
												(string) $download['remaining']
											)
									);
									?>
								</span>
								<span>
									<?php
									echo esc_html(
										empty( $download['expires'] )
											? __( 'No expiry', 'alynt-account-gateway' )
											: sprintf(
												/* translators: %s: download expiry date. */
												__( 'Expires: %s', 'alynt-account-gateway' ),
												$download['expires']
											)
									);
									?>
								</span>
							</span>
							<a class="agw-dashboard-download__action" href="<?php echo esc_url( $download['url'] ); ?>" aria-label="<?php echo esc_attr( $download_label ); ?>">
								<?php esc_html_e( 'Download', 'alynt-account-gateway' ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</section>
		<?php
	}

	/**
	 * Render a read-only saved-addresses module on the WooCommerce dashboard.
	 *
	 * @param int                 $user_id  WordPress user ID.
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_saved_addresses( $user_id, $settings ) {
		if ( ! $this->woocommerce->is_account_menu_item_visible( 'edit-address', $settings ) ) {
			return;
		}

		$addresses = $this->woocommerce->saved_addresses( $user_id );
		$cards     = array(
			'billing'  => array(
				'title'      => __( 'Billing Address', 'alynt-account-gateway' ),
				'empty'      => __( 'No billing address is saved yet. Add one to keep checkout details ready.', 'alynt-account-gateway' ),
				'add_label'  => __( 'Add billing address', 'alynt-account-gateway' ),
				'edit_label' => __( 'Edit billing address', 'alynt-account-gateway' ),
			),
			'shipping' => array(
				'title'      => __( 'Shipping Address', 'alynt-account-gateway' ),
				'empty'      => __( 'No shipping address is saved yet. Add one to keep delivery details ready.', 'alynt-account-gateway' ),
				'add_label'  => __( 'Add shipping address', 'alynt-account-gateway' ),
				'edit_label' => __( 'Edit shipping address', 'alynt-account-gateway' ),
			),
		);
		?>
		<section class="agw-dashboard-section agw-dashboard-addresses" aria-labelledby="agw-dashboard-addresses-title">
			<div class="agw-dashboard-addresses__header">
				<h2 id="agw-dashboard-addresses-title"><?php esc_html_e( 'Saved Addresses', 'alynt-account-gateway' ); ?></h2>
				<a href="<?php echo esc_url( $this->woocommerce->endpoint_url( 'edit-address', $settings ) ); ?>">
					<?php esc_html_e( 'Manage all addresses', 'alynt-account-gateway' ); ?>
				</a>
			</div>
			<div class="agw-dashboard-addresses__grid">
				<?php foreach ( $cards as $type => $card ) : ?>
					<?php $lines = isset( $addresses[ $type ] ) && is_array( $addresses[ $type ] ) ? $addresses[ $type ] : array(); ?>
					<article class="agw-dashboard-address">
						<h3><?php echo esc_html( $card['title'] ); ?></h3>
						<?php if ( empty( $lines ) ) : ?>
							<p class="agw-dashboard-address__empty"><?php echo esc_html( $card['empty'] ); ?></p>
						<?php else : ?>
							<address class="agw-dashboard-address__details">
								<?php foreach ( $lines as $line ) : ?>
									<span><?php echo esc_html( $line ); ?></span>
								<?php endforeach; ?>
							</address>
						<?php endif; ?>
						<a class="agw-dashboard-address__action" href="<?php echo esc_url( $this->woocommerce->address_url( $type, $settings ) ); ?>">
							<?php echo esc_html( empty( $lines ) ? $card['add_label'] : $card['edit_label'] ); ?>
						</a>
					</article>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}
}
