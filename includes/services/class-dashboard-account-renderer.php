<?php
/**
 * Dashboard account renderer.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders saved addresses, account details, and payment methods.
 */
class ALYNT_AG_Dashboard_Account_Renderer {

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
	 * Render account summary modules.
	 *
	 * @param int                 $user_id  User ID.
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	public function render( $user_id, $settings ) {
		$this->render_saved_addresses( $user_id, $settings );
		$this->render_account_details( $user_id, $settings );
		$this->render_saved_payment_methods( $user_id, $settings );
	}

	/**
	 * Render saved addresses.
	 *
	 * @param int                 $user_id  User ID.
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

	/**
	 * Render account details.
	 *
	 * @param int                 $user_id  User ID.
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_account_details( $user_id, $settings ) {
		if ( ! $this->woocommerce->is_account_menu_item_visible( 'edit-account', $settings ) ) {
			return;
		}

		$details = $this->woocommerce->account_details( $user_id );
		if ( empty( $details ) ) {
			return;
		}

		$is_complete  = ! empty( $details['is_complete'] );
		$status       = $is_complete ? __( 'Details ready', 'alynt-account-gateway' ) : __( 'Needs review', 'alynt-account-gateway' );
		$guidance     = $is_complete
			? __( 'Your name and email are ready for account notices and future order updates.', 'alynt-account-gateway' )
			: __( 'Add your first and last name so account notices and future orders use the right details.', 'alynt-account-gateway' );
		$status_class = 'agw-dashboard-account-details__status';
		if ( $is_complete ) {
			$status_class .= ' agw-dashboard-account-details__status--ready';
		}
		?>
		<section class="agw-dashboard-section agw-dashboard-account-details" aria-labelledby="agw-dashboard-account-details-title">
			<div class="agw-dashboard-account-details__header">
				<h2 id="agw-dashboard-account-details-title"><?php esc_html_e( 'Account Details', 'alynt-account-gateway' ); ?></h2>
				<div class="agw-dashboard-account-details__actions">
					<span class="<?php echo esc_attr( $status_class ); ?>"><?php echo esc_html( $status ); ?></span>
					<a href="<?php echo esc_url( $this->woocommerce->endpoint_url( 'edit-account', $settings ) ); ?>">
						<?php esc_html_e( 'Edit account details', 'alynt-account-gateway' ); ?>
					</a>
				</div>
			</div>
			<dl class="agw-dashboard-account-details__grid">
				<div class="agw-dashboard-account-detail">
					<dt><?php esc_html_e( 'Name', 'alynt-account-gateway' ); ?></dt>
					<dd><?php echo esc_html( ! empty( $details['name'] ) ? $details['name'] : __( 'Not added yet', 'alynt-account-gateway' ) ); ?></dd>
				</div>
				<div class="agw-dashboard-account-detail">
					<dt><?php esc_html_e( 'Email address', 'alynt-account-gateway' ); ?></dt>
					<dd><?php echo esc_html( ! empty( $details['email'] ) ? $details['email'] : __( 'Not available', 'alynt-account-gateway' ) ); ?></dd>
				</div>
				<div class="agw-dashboard-account-detail">
					<dt><?php esc_html_e( 'Customer since', 'alynt-account-gateway' ); ?></dt>
					<dd><?php echo esc_html( ! empty( $details['member_since'] ) ? $details['member_since'] : __( 'Not available', 'alynt-account-gateway' ) ); ?></dd>
				</div>
			</dl>
			<p class="agw-dashboard-account-details__guidance"><?php echo esc_html( $guidance ); ?></p>
		</section>
		<?php
	}

	/**
	 * Render saved payment methods.
	 *
	 * @param int                 $user_id  User ID.
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_saved_payment_methods( $user_id, $settings ) {
		if ( ! $this->woocommerce->is_account_menu_item_visible( 'payment-methods', $settings ) ) {
			return;
		}

		$methods = $this->woocommerce->saved_payment_methods( $user_id, 3 );
		?>
		<section class="agw-dashboard-section agw-dashboard-payment-methods" aria-labelledby="agw-dashboard-payment-methods-title">
			<div class="agw-dashboard-payment-methods__header">
				<h2 id="agw-dashboard-payment-methods-title"><?php esc_html_e( 'Saved Payment Methods', 'alynt-account-gateway' ); ?></h2>
				<a href="<?php echo esc_url( $this->woocommerce->endpoint_url( 'payment-methods', $settings ) ); ?>">
					<?php esc_html_e( 'Manage payment methods', 'alynt-account-gateway' ); ?>
				</a>
			</div>
			<?php if ( empty( $methods ) ) : ?>
				<p class="agw-dashboard-payment-methods__empty">
					<?php esc_html_e( 'Saved payment methods will appear here when your payment provider supports secure account storage.', 'alynt-account-gateway' ); ?>
				</p>
			<?php else : ?>
				<ul class="agw-dashboard-payment-methods__list" role="list">
					<?php foreach ( $methods as $method ) : ?>
						<li class="agw-dashboard-payment-method">
							<span class="agw-dashboard-payment-method__name"><?php echo esc_html( $method['display_name'] ); ?></span>
							<?php if ( ! empty( $method['is_default'] ) ) : ?>
								<span class="agw-dashboard-payment-method__default"><?php esc_html_e( 'Default', 'alynt-account-gateway' ); ?></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</section>
		<?php
	}
}
