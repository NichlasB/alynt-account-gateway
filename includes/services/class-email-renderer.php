<?php
/**
 * Email template renderer.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders branded HTML and plain-text account emails.
 */
class ALYNT_AG_Email_Renderer extends ALYNT_AG_Service_Collaborator {

	/**
	 * Token provider.
	 *
	 * @var ALYNT_AG_Email_Tokens
	 */
	private $tokens;

	/**
	 * Constructor.
	 *
	 * @param object                $service Public service facade.
	 * @param ALYNT_AG_Email_Tokens $tokens  Token provider.
	 */
	public function __construct( $service, $tokens ) {
		parent::__construct( $service );
		$this->tokens = $tokens;
	}

	/**
	 * Render a template into subject, HTML, and plain text.
	 *
	 * @param string              $template Template key.
	 * @param array<string,mixed> $tokens   Token values.
	 * @param array<string,mixed> $settings Settings.
	 * @return array<string,string>|WP_Error
	 */
	public function render( $template, $tokens, $settings ) {
		if ( ! isset( $this->service->templates()[ $template ] ) ) {
			return new WP_Error( 'alynt_ag_unknown_email_template', __( 'Unknown email template.', 'alynt-account-gateway' ) );
		}

		$tokens        = $this->tokens->normalize( $tokens );
		$button        = $this->tokens->button( $template, $tokens );
		$prefix        = $this->tokens->settings_prefix( $template );
		$subject       = $this->service->replace_tokens( $settings[ "{$prefix}_subject" ] ?? '', $tokens );
		$preheader     = $this->service->replace_tokens( $settings[ "{$prefix}_preheader" ] ?? '', $tokens );
		$body_template = $settings[ "{$prefix}_body" ] ?? '';
		$body          = $this->service->replace_tokens( $body_template, $tokens );
		$body_html     = $this->replace_html_tokens( $body_template, $tokens );

		return array(
			'subject'   => $subject,
			'preheader' => $preheader,
			'html'      => $this->render_html( $template, $subject, $preheader, $body_html, $button, $settings ),
			'plain'     => $this->render_plain( $body, $button ),
		);
	}

	/**
	 * Replace known template tokens.
	 *
	 * @param string              $content Content.
	 * @param array<string,mixed> $tokens  Token values.
	 * @return string
	 */
	public function replace_tokens( $content, $tokens ) {
		$normalized = $this->tokens->normalize( $tokens );
		$replace    = array();

		foreach ( $normalized as $key => $value ) {
			$replace[ '{{' . $key . '}}' ] = $value;
		}

		return strtr( (string) $content, $replace );
	}

	/**
	 * Replace HTML-body tokens without allowing token markup.
	 *
	 * @param string              $content Content.
	 * @param array<string,mixed> $tokens  Token values.
	 * @return string
	 */
	private function replace_html_tokens( $content, $tokens ) {
		$normalized = $this->tokens->normalize( $tokens );
		$replace    = array();
		$url_tokens = array( 'confirmation_url', 'reset_url', 'change_email_url', 'dashboard_url' );

		foreach ( $normalized as $key => $value ) {
			$replace[ '{{' . $key . '}}' ] = in_array( $key, $url_tokens, true ) ? esc_url( $value ) : esc_html( $value );
		}

		return strtr( (string) $content, $replace );
	}

	/**
	 * Render branded HTML.
	 *
	 * @param string              $template  Template key.
	 * @param string              $subject   Subject.
	 * @param string              $preheader Preheader.
	 * @param string              $body      Body.
	 * @param array<string,mixed> $button    Button metadata.
	 * @param array<string,mixed> $settings  Settings.
	 * @return string
	 */
	private function render_html( $template, $subject, $preheader, $body, $button, $settings ) {
		$site_name    = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$logo_url     = ! empty( $settings['brand_logo_id'] ) ? wp_get_attachment_image_url( (int) $settings['brand_logo_id'], 'full' ) : '';
		$logo_width   = ! empty( $settings['brand_logo_max_width'] ) ? absint( $settings['brand_logo_max_width'] ) : 180;
		$logo_width   = max( 80, min( 220, $logo_width ) );
		$primary      = $settings['button_background_color'] ?? '#3B5249';
		$button_text  = $settings['button_text_color'] ?? '#ffffff';
		$text_color   = $settings['text_color'] ?? '#281408';
		$background   = $settings['page_background_color'] ?? '#EAE4D6';
		$surface      = $settings['surface_color'] ?? '#FFFFFF';
		$body_html    = $this->style_email_body_html( wpautop( wp_kses_post( $body ) ) );
		$button_url   = ! empty( $button['url'] ) ? esc_url( $button['url'] ) : '';
		$button_label = ! empty( $button['label'] ) ? $button['label'] : '';

		ob_start();
		?>
		<!doctype html>
		<html>
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title><?php echo esc_html( $subject ); ?></title>
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
		<body style="margin:0;padding:0;background:<?php echo esc_attr( $background ); ?>;color:<?php echo esc_attr( $text_color ); ?>;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">
			<div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;"><?php echo esc_html( $preheader ); ?></div>
			<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:<?php echo esc_attr( $background ); ?>;padding:32px 16px;">
				<tr>
					<td align="center">
						<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background:<?php echo esc_attr( $surface ); ?>;border-radius:8px;overflow:hidden;">
							<tr>
								<td style="padding:32px 32px 16px;text-align:center;">
									<?php if ( $logo_url ) : ?>
										<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $site_name ); ?>" width="<?php echo esc_attr( (string) $logo_width ); ?>" style="display:block;margin:0 auto;width:<?php echo esc_attr( (string) $logo_width ); ?>px;max-width:100%;height:auto;border:0;outline:none;text-decoration:none;">
									<?php else : ?>
										<div style="font-family:Georgia,serif;font-size:24px;font-weight:600;"><?php echo esc_html( $site_name ); ?></div>
									<?php endif; ?>
								</td>
							</tr>
							<tr>
								<td style="padding:16px 32px 8px;">
									<h1 style="margin:0 0 16px;font-family:Georgia,serif;font-size:26px;line-height:1.25;color:<?php echo esc_attr( $text_color ); ?>;"><?php echo esc_html( $subject ); ?></h1>
									<div class="agw-email-body" style="font-size:20px;line-height:1.6;color:<?php echo esc_attr( $text_color ); ?>;"><?php echo wp_kses_post( $body_html ); ?></div>
									<?php if ( $button_url && $button_label ) : ?>
										<p style="margin:28px 0;">
											<a href="<?php echo esc_url( $button_url ); ?>" style="display:inline-block;padding:14px 22px;border-radius:6px;background:<?php echo esc_attr( $primary ); ?>;color:<?php echo esc_attr( $button_text ); ?>;font-weight:600;text-decoration:none;"><?php echo esc_html( $button_label ); ?></a>
										</p>
										<p style="font-size:13px;line-height:1.5;color:<?php echo esc_attr( $text_color ); ?>;opacity:.78;word-break:break-all;overflow-wrap:anywhere;"><?php echo esc_html( $button_url ); ?></p>
									<?php endif; ?>
								</td>
							</tr>
							<tr>
								<td style="padding:16px 32px 32px;font-size:12px;line-height:1.5;color:<?php echo esc_attr( $text_color ); ?>;opacity:.72;">
									<?php
									echo esc_html(
										sprintf(
											/* translators: %s: site name. */
											__( 'This email was sent by %s.', 'alynt-account-gateway' ),
											$site_name
										)
									);
									?>
									<span style="display:none;"><?php echo esc_html( $template ); ?></span>
								</td>
							</tr>
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
	 * Add email-client-safe inline sizing to generated body copy.
	 *
	 * @param string $body_html Sanitized body HTML.
	 * @return string
	 */
	private function style_email_body_html( $body_html ) {
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

	/**
	 * Render plain text fallback content.
	 *
	 * @param string              $body   Body content.
	 * @param array<string,mixed> $button Button metadata.
	 * @return string
	 */
	private function render_plain( $body, $button ) {
		$plain = trim( wp_strip_all_tags( $body ) );

		if ( ! empty( $button['url'] ) ) {
			$plain .= "\n\n" . $button['url'];
		}

		return $plain;
	}
}
