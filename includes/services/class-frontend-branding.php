<?php
/**
 * Frontend branding helpers.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds frontend branding styles and visual blocks.
 */
class ALYNT_AG_Frontend_Branding {

	/**
	 * Return inline CSS custom properties for configured branding.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	public function style_attribute( $settings ) {
		$properties = array(
			'--agw-color-text'        => $settings['text_color'],
			'--agw-color-primary'     => $settings['primary_color'],
			'--agw-color-background'  => $settings['page_background_color'],
			'--agw-color-notice'      => $settings['accent_color'],
			'--agw-color-error'       => $settings['error_color'],
			'--agw-color-surface'     => $settings['surface_color'],
			'--agw-button-background' => $settings['button_background_color'],
			'--agw-button-text'       => $settings['button_text_color'],
			'--agw-font-heading'      => $settings['heading_font_family'],
			'--agw-font-body'         => $settings['body_font_family'],
		);

		$style = '';
		foreach ( $properties as $property => $value ) {
			if ( '' === $value ) {
				continue;
			}
			$style .= sprintf( '%s:%s;', $property, $value );
		}

		return $style;
	}

	/**
	 * Render the media panel.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	public function render_media_panel( $settings ) {
		$image_url = $settings['background_image_id'] ? wp_get_attachment_image_url( (int) $settings['background_image_id'], 'full' ) : '';

		if ( $image_url ) {
			printf(
				'<div class="agw-media__image" style="background-image:url(%s);"></div>',
				esc_url( $image_url )
			);
			return;
		}
		?>
		<div class="agw-media__pattern">
			<span class="agw-leaf agw-leaf--one"></span>
			<span class="agw-leaf agw-leaf--two"></span>
			<span class="agw-dot agw-dot--one"></span>
			<span class="agw-dot agw-dot--two"></span>
			<span class="agw-dot agw-dot--three"></span>
		</div>
		<?php
	}

	/**
	 * Render logo or store name.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	public function render_brand_block( $settings ) {
		$logo_url  = $settings['brand_logo_id'] ? wp_get_attachment_image_url( (int) $settings['brand_logo_id'], 'full' ) : '';
		$max_width = max( 80, min( 520, (int) $settings['brand_logo_max_width'] ) );
		?>
		<div class="agw-brand">
			<?php if ( $logo_url ) : ?>
				<img class="agw-brand__logo" src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" style="max-width:<?php echo esc_attr( (string) $max_width ); ?>px;">
			<?php else : ?>
				<div class="agw-brand__name"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></div>
			<?php endif; ?>
		</div>
		<?php
	}
}
