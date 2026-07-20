<?php
/**
 * Dashboard endpoint renderer.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders delegated WooCommerce endpoint content.
 */
class ALYNT_AG_Dashboard_Endpoint_Renderer {

	/**
	 * WooCommerce integration.
	 *
	 * @var ALYNT_AG_WooCommerce_Integration
	 */
	private $woocommerce;

	/**
	 * Endpoint metadata.
	 *
	 * @var ALYNT_AG_Dashboard_Endpoint_Metadata
	 */
	private $metadata;

	/**
	 * Constructor.
	 *
	 * @param ALYNT_AG_WooCommerce_Integration          $woocommerce WooCommerce integration.
	 * @param ALYNT_AG_Dashboard_Endpoint_Metadata|null $metadata Endpoint metadata.
	 */
	public function __construct( $woocommerce, $metadata = null ) {
		$this->woocommerce = $woocommerce;
		$this->metadata    = $metadata ? $metadata : new ALYNT_AG_Dashboard_Endpoint_Metadata( $woocommerce );
	}

	/**
	 * Render one non-dashboard WooCommerce endpoint.
	 *
	 * @param array<string,string> $endpoint     Endpoint data.
	 * @param array<string,mixed>  $settings     Settings.
	 * @param string               $current_path Current request path.
	 * @return void
	 */
	public function render( $endpoint, $settings, $current_path ) {
		$endpoint_key = $endpoint['endpoint'];
		$actions      = $this->metadata->actions( $endpoint_key, $settings );
		$guidance     = $this->metadata->guidance( $endpoint_key );
		$affordances  = $this->metadata->affordances( $endpoint_key, $settings );
		?>
		<section class="agw-dashboard-section agw-dashboard-section--content" aria-labelledby="agw-dashboard-content-title">
			<h2 id="agw-dashboard-content-title">
				<?php echo esc_html( $this->woocommerce->endpoint_labels()[ $endpoint_key ] ?? __( 'Account', 'alynt-account-gateway' ) ); ?>
			</h2>
			<?php if ( ! empty( $actions ) ) : ?>
				<nav class="agw-dashboard-section-actions" aria-label="<?php esc_attr_e( 'Account section shortcuts', 'alynt-account-gateway' ); ?>">
					<?php foreach ( $actions as $action ) : ?>
						<a href="<?php echo esc_url( $action['url'] ); ?>"<?php echo $this->current_attribute( $action['url'], $current_path ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by current_attribute(). ?>><?php echo esc_html( $action['label'] ); ?></a>
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
				<?php if ( ! $this->woocommerce->render_endpoint( $endpoint_key, $endpoint['value'] ) ) : ?>
					<?php $this->render_unavailable( $endpoint_key, $settings ); ?>
				<?php endif; ?>
			</div>
		</section>
		<?php
	}

	/**
	 * Render a branded fallback when WooCommerce returns no content.
	 *
	 * @param string              $endpoint Endpoint key.
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_unavailable( $endpoint, $settings ) {
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
	 * Return aria-current when a URL represents the current path.
	 *
	 * @param string $url          Link URL.
	 * @param string $current_path Current path.
	 * @return string
	 */
	private function current_attribute( $url, $current_path ) {
		$url_path     = (string) wp_parse_url( (string) $url, PHP_URL_PATH );
		$current_path = (string) wp_parse_url( (string) $current_path, PHP_URL_PATH );

		if ( '' === $url_path || '' === $current_path ) {
			return '';
		}

		$is_current = untrailingslashit( '/' . ltrim( $url_path, '/' ) )
			=== untrailingslashit( '/' . ltrim( $current_path, '/' ) );

		return $is_current ? ' aria-current="page"' : '';
	}
}
