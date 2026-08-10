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
			'--agw-color-primary-rgb' => $this->hex_to_rgb_channels( $settings['primary_color'] ),
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
	 * Return gateway shell classes for configured page-level states.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return string
	 */
	public function shell_class_attribute( $settings ) {
		$classes = array( 'agw-shell' );

		if ( $this->is_white_color( $settings['page_background_color'] ?? '' ) ) {
			$classes[] = 'agw-shell--white-page-bg';
		}

		return implode( ' ', $classes );
	}

	/**
	 * Convert a hex color to space-separated RGB channels for alpha-ready CSS.
	 *
	 * @param string $color Hex color.
	 * @return string
	 */
	private function hex_to_rgb_channels( $color ) {
		$color = ltrim( trim( (string) $color ), '#' );
		if ( 3 === strlen( $color ) ) {
			$color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
		}

		$color = sanitize_hex_color( '#' . $color );

		if ( ! $color ) {
			$color = '#3B5249';
		}

		$color = ltrim( $color, '#' );

		if ( 6 !== strlen( $color ) || ! ctype_xdigit( $color ) ) {
			return '59 82 73';
		}

		return sprintf(
			'%d %d %d',
			hexdec( substr( $color, 0, 2 ) ),
			hexdec( substr( $color, 2, 2 ) ),
			hexdec( substr( $color, 4, 2 ) )
		);
	}

	/**
	 * Determine whether a configured color is white.
	 *
	 * @param string $color Color value.
	 * @return bool
	 */
	private function is_white_color( $color ) {
		$color = strtolower( trim( (string) $color ) );

		return in_array(
			$color,
			array( '#fff', 'fff', '#ffffff', 'ffffff' ),
			true
		);
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
	 * @param string              $link_url Optional brand block URL.
	 * @return void
	 */
	public function render_brand_block( $settings, $link_url = '' ) {
		$logo_url  = $settings['brand_logo_id'] ? wp_get_attachment_image_url( (int) $settings['brand_logo_id'], 'full' ) : '';
		$max_width = max( 80, min( 520, (int) $settings['brand_logo_max_width'] ) );
		$content   = '';

		if ( $logo_url ) {
			$content = sprintf(
				'<img class="agw-brand__logo" src="%s" alt="%s" style="max-width:%spx;">',
				esc_url( $logo_url ),
				esc_attr( get_bloginfo( 'name' ) ),
				esc_attr( (string) $max_width )
			);
		} else {
			$content = sprintf(
				'<div class="agw-brand__name">%s</div>',
				esc_html( get_bloginfo( 'name' ) )
			);
		}
		?>
		<div class="agw-brand">
			<?php if ( $link_url ) : ?>
				<a class="agw-brand__link" href="<?php echo esc_url( $link_url ); ?>" aria-label="<?php esc_attr_e( 'Go to homepage', 'alynt-account-gateway' ); ?>">
					<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Content is assembled from escaped values above. ?>
				</a>
			<?php else : ?>
				<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Content is assembled from escaped values above. ?>
			<?php endif; ?>
		</div>
		<?php
	}
}
