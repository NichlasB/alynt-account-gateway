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
 * Coordinates the frontend account dashboard renderers.
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
	 * Navigation renderer.
	 *
	 * @var ALYNT_AG_Dashboard_Navigation_Renderer
	 */
	private $navigation;

	/**
	 * Endpoint renderer.
	 *
	 * @var ALYNT_AG_Dashboard_Endpoint_Renderer
	 */
	private $endpoint_renderer;

	/**
	 * Commerce renderer.
	 *
	 * @var ALYNT_AG_Dashboard_Commerce_Renderer
	 */
	private $commerce_renderer;

	/**
	 * Account renderer.
	 *
	 * @var ALYNT_AG_Dashboard_Account_Renderer
	 */
	private $account_renderer;

	/**
	 * Constructor.
	 *
	 * The first three arguments preserve the established public contract.
	 *
	 * @param ALYNT_AG_Dashboard_Service|null             $dashboard         Dashboard service.
	 * @param ALYNT_AG_WooCommerce_Integration|null       $woocommerce       WooCommerce integration.
	 * @param ALYNT_AG_Frontend_Branding|null             $branding          Branding helpers.
	 * @param ALYNT_AG_Dashboard_Navigation_Renderer|null $navigation        Navigation renderer.
	 * @param ALYNT_AG_Dashboard_Endpoint_Renderer|null   $endpoint_renderer Endpoint renderer.
	 * @param ALYNT_AG_Dashboard_Commerce_Renderer|null   $commerce_renderer Commerce renderer.
	 * @param ALYNT_AG_Dashboard_Account_Renderer|null    $account_renderer  Account renderer.
	 */
	public function __construct(
		$dashboard = null,
		$woocommerce = null,
		$branding = null,
		$navigation = null,
		$endpoint_renderer = null,
		$commerce_renderer = null,
		$account_renderer = null
	) {
		$this->dashboard         = $dashboard ? $dashboard : new ALYNT_AG_Dashboard_Service();
		$this->woocommerce       = $woocommerce ? $woocommerce : new ALYNT_AG_WooCommerce_Integration();
		$this->branding          = $branding ? $branding : new ALYNT_AG_Frontend_Branding();
		$this->navigation        = $navigation ? $navigation : new ALYNT_AG_Dashboard_Navigation_Renderer();
		$this->endpoint_renderer = $endpoint_renderer ? $endpoint_renderer : new ALYNT_AG_Dashboard_Endpoint_Renderer( $this->woocommerce );
		$this->commerce_renderer = $commerce_renderer ? $commerce_renderer : new ALYNT_AG_Dashboard_Commerce_Renderer( $this->woocommerce );
		$this->account_renderer  = $account_renderer ? $account_renderer : new ALYNT_AG_Dashboard_Account_Renderer( $this->woocommerce );
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
					<?php $this->navigation->render_actions( $settings ); ?>
				</header>
				<?php $this->navigation->render_offcanvas_menu( $settings ); ?>
				<?php $this->render_dashboard_screen( $settings, $current_path ); ?>
				<?php $this->navigation->render_footer_menu( $settings ); ?>
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
			<?php $this->commerce_renderer->render( $user->ID, $settings ); ?>
			<?php $this->account_renderer->render( $user->ID, $settings ); ?>
		<?php endif; ?>

		<section class="agw-dashboard-section" aria-labelledby="agw-dashboard-links-title">
			<h2 id="agw-dashboard-links-title"><?php esc_html_e( 'Manage Account', 'alynt-account-gateway' ); ?></h2>
			<div class="agw-dashboard-grid">
				<?php foreach ( $links as $link ) : ?>
					<?php $current_attribute = $this->current_attribute( $link['url'], $current_path ); ?>
					<a class="agw-dashboard-link" href="<?php echo esc_url( $link['url'] ); ?>" target="<?php echo esc_attr( $link['target'] ); ?>"<?php echo '_blank' === $link['target'] ? ' rel="noopener noreferrer"' : ''; ?><?php echo $current_attribute; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by current_attribute(). ?>>
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
			<?php $this->endpoint_renderer->render( $endpoint, $settings, $current_path ); ?>
		<?php endif; ?>
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
