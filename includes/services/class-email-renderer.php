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
	 * Branded HTML renderer.
	 *
	 * @var ALYNT_AG_Email_Html_Renderer
	 */
	private $html_renderer;

	/**
	 * Constructor.
	 *
	 * @param object                       $service       Public service facade.
	 * @param ALYNT_AG_Email_Tokens        $tokens        Token provider.
	 * @param ALYNT_AG_Email_Html_Renderer $html_renderer Optional HTML renderer.
	 */
	public function __construct( $service, $tokens, $html_renderer = null ) {
		parent::__construct( $service );
		$this->tokens        = $tokens;
		$this->html_renderer = $html_renderer ? $html_renderer : new ALYNT_AG_Email_Html_Renderer();
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
			'html'      => $this->html_renderer->render(
				array(
					'template'  => $template,
					'subject'   => $subject,
					'preheader' => $preheader,
					'body'      => $body_html,
					'button'    => $button,
					'settings'  => $settings,
				)
			),
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
