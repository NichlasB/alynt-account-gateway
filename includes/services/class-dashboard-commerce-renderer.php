<?php
/**
 * Dashboard commerce renderer.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders WooCommerce overview, recent orders, and downloads modules.
 */
class ALYNT_AG_Dashboard_Commerce_Renderer {

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
	 * Render commerce dashboard modules.
	 *
	 * @param int                 $user_id  User ID.
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	public function render( $user_id, $settings ) {
		$this->render_overview( $settings );
		$this->render_recent_orders( $user_id, $settings );
		$this->render_available_downloads( $user_id, $settings );
	}

	/**
	 * Render the customer account overview.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_overview( $settings ) {
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
	 * Render recent orders.
	 *
	 * @param int                 $user_id  User ID.
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
	 * Render available downloads.
	 *
	 * @param int                 $user_id  User ID.
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
											/* translators: %d: number of downloads remaining. */
											_n(
												'%d download remaining',
												'%d downloads remaining',
												absint( $download['remaining'] ),
												'alynt-account-gateway'
											),
											absint( $download['remaining'] )
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
}
