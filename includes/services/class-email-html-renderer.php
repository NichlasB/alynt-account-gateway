<?php
/**
 * Branded email HTML renderer.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the client-safe branded HTML email shell.
 */
class ALYNT_AG_Email_Html_Renderer {

	/**
	 * Render branded HTML.
	 *
	 * @param array<string,mixed> $input Template, content, action, and settings input.
	 * @return string
	 */
	public function render( $input ) {
		$context = $this->context( $input );

		ob_start();
		?>
		<!doctype html>
		<html>
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title><?php echo esc_html( $context['subject'] ); ?></title>
			<style>
				@media screen and (max-width: 599px) {
					.agw-email-body,
					.agw-email-body p,
					.agw-email-body li {
						font-size: 16px !important;
					}
				}

				@media screen and (min-width: 600px) and (max-width: 959px) {
					.agw-email-body,
					.agw-email-body p,
					.agw-email-body li {
						font-size: 18px !important;
					}
				}
			</style>
		</head>
		<body style="margin:0;padding:0;background:<?php echo esc_attr( $context['background'] ); ?>;color:<?php echo esc_attr( $context['text_color'] ); ?>;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">
			<div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;"><?php echo esc_html( $context['preheader'] ); ?></div>
			<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:<?php echo esc_attr( $context['background'] ); ?>;padding:32px 16px;">
				<tr>
					<td align="center">
						<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:<?php echo esc_attr( $context['surface'] ); ?>;border-radius:8px;overflow:hidden;">
							<?php $this->render_header( $context ); ?>
							<?php $this->render_content( $context ); ?>
							<?php $this->render_footer( $context ); ?>
						</table>
					</td>
				</tr>
			</table>
		</body>
		</html>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Build the branded HTML render context.
	 *
	 * @param array<string,mixed> $input Template, content, action, and settings input.
	 * @return array<string,mixed>
	 */
	private function context( $input ) {
		$settings   = $input['settings'];
		$button     = $input['button'];
		$site_name  = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$logo_url   = ! empty( $settings['brand_logo_id'] ) ? wp_get_attachment_image_url( (int) $settings['brand_logo_id'], 'full' ) : '';
		$logo_width = ! empty( $settings['brand_logo_max_width'] ) ? absint( $settings['brand_logo_max_width'] ) : 180;

		return array(
			'template'     => $input['template'],
			'subject'      => $input['subject'],
			'preheader'    => $input['preheader'],
			'site_name'    => $site_name,
			'logo_url'     => $logo_url,
			'logo_width'   => max( 80, min( 220, $logo_width ) ),
			'primary'      => $settings['button_background_color'] ?? '#3B5249',
			'button_text'  => $settings['button_text_color'] ?? '#ffffff',
			'text_color'   => $settings['text_color'] ?? '#281408',
			'background'   => $settings['page_background_color'] ?? '#EAE4D6',
			'surface'      => $settings['surface_color'] ?? '#FFFFFF',
			'body_html'    => $this->style_body_html( wpautop( wp_kses_post( $input['body'] ) ) ),
			'button_url'   => ! empty( $button['url'] ) ? esc_url( $button['url'] ) : '',
			'button_label' => ! empty( $button['label'] ) ? $button['label'] : '',
		);
	}

	/**
	 * Render the email brand header.
	 *
	 * @param array<string,mixed> $context Render context.
	 * @return void
	 */
	private function render_header( $context ) {
		?>
		<tr>
			<td style="padding:32px 32px 16px;text-align:center;">
				<?php if ( $context['logo_url'] ) : ?>
					<img src="<?php echo esc_url( $context['logo_url'] ); ?>" alt="<?php echo esc_attr( $context['site_name'] ); ?>" width="<?php echo esc_attr( (string) $context['logo_width'] ); ?>" style="display:block;margin:0 auto;width:<?php echo esc_attr( (string) $context['logo_width'] ); ?>px;max-width:100%;height:auto;border:0;outline:none;text-decoration:none;">
				<?php else : ?>
					<div style="font-family:Georgia,serif;font-size:24px;font-weight:600;"><?php echo esc_html( $context['site_name'] ); ?></div>
				<?php endif; ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Render the email message and optional action.
	 *
	 * @param array<string,mixed> $context Render context.
	 * @return void
	 */
	private function render_content( $context ) {
		?>
		<tr>
			<td style="padding:16px 32px 8px;">
				<h1 style="margin:0 0 16px;font-family:Georgia,serif;font-size:26px;line-height:1.25;color:<?php echo esc_attr( $context['text_color'] ); ?>;"><?php echo esc_html( $context['subject'] ); ?></h1>
				<div class="agw-email-body" style="font-size:20px;line-height:1.6;color:<?php echo esc_attr( $context['text_color'] ); ?>;"><?php echo wp_kses_post( $context['body_html'] ); ?></div>
				<?php if ( $context['button_url'] && $context['button_label'] ) : ?>
					<p style="margin:28px 0;">
						<a href="<?php echo esc_url( $context['button_url'] ); ?>" style="display:inline-block;padding:14px 22px;border-radius:6px;background:<?php echo esc_attr( $context['primary'] ); ?>;color:<?php echo esc_attr( $context['button_text'] ); ?>;font-weight:600;text-decoration:none;"><?php echo esc_html( $context['button_label'] ); ?></a>
					</p>
					<p style="font-size:13px;line-height:1.5;color:<?php echo esc_attr( $context['text_color'] ); ?>;opacity:.78;word-break:break-all;overflow-wrap:anywhere;"><?php echo esc_html( $context['button_url'] ); ?></p>
				<?php endif; ?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Render the email footer.
	 *
	 * @param array<string,mixed> $context Render context.
	 * @return void
	 */
	private function render_footer( $context ) {
		?>
		<tr>
			<td style="padding:16px 32px 32px;font-size:12px;line-height:1.5;color:<?php echo esc_attr( $context['text_color'] ); ?>;opacity:.72;">
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: site name. */
						__( 'This email was sent by %s.', 'alynt-account-gateway' ),
						$context['site_name']
					)
				);
				?>
				<span style="display:none;"><?php echo esc_html( $context['template'] ); ?></span>
			</td>
		</tr>
		<?php
	}

	/**
	 * Add email-client-safe inline sizing to generated body copy.
	 *
	 * @param string $body_html Sanitized body HTML.
	 * @return string
	 */
	private function style_body_html( $body_html ) {
		return (string) preg_replace_callback(
			'/<(p|li)(\s[^>]*)?>/i',
			function ( $matches ) {
				$tag       = strtolower( $matches[1] );
				$attrs     = isset( $matches[2] ) ? (string) $matches[2] : '';
				$copy_css  = 'font-size:20px;line-height:1.6;';
				$copy_css .= 'p' === $tag ? 'margin:0 0 16px;' : 'margin:0 0 8px;';

				if ( preg_match( '/\sstyle=(["\'])(.*?)\1/i', $attrs ) ) {
					$attrs = (string) preg_replace_callback(
						'/\sstyle=(["\'])(.*?)\1/i',
						function ( $style_matches ) use ( $copy_css ) {
							$style = rtrim( (string) $style_matches[2] );
							if ( '' !== $style && ';' !== substr( $style, -1 ) ) {
								$style .= ';';
							}

							return ' style=' . $style_matches[1] . $style . $copy_css . $style_matches[1];
						},
						$attrs,
						1
					);
				} else {
					$attrs .= ' style="' . $copy_css . '"';
				}

				return '<' . $tag . $attrs . '>';
			},
			$body_html
		);
	}
}
