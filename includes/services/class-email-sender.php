<?php
/**
 * Email template sender.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sends rendered account emails.
 */
class ALYNT_AG_Email_Sender extends ALYNT_AG_Service_Collaborator {

	/**
	 * Send a rendered template.
	 *
	 * @param string              $template Template key.
	 * @param string              $to       Recipient email.
	 * @param array<string,mixed> $tokens   Token values.
	 * @param array<string,mixed> $settings Settings.
	 * @return true|WP_Error
	 */
	public function send( $template, $to, $tokens, $settings ) {
		$to = sanitize_email( $to );
		if ( ! is_email( $to ) ) {
			return new WP_Error( 'alynt_ag_invalid_email_recipient', __( 'The email recipient is invalid.', 'alynt-account-gateway' ) );
		}

		$rendered = $this->service->render( $template, $tokens, $settings );
		if ( is_wp_error( $rendered ) ) {
			return $rendered;
		}

		$sent = wp_mail(
			$to,
			$rendered['subject'],
			$rendered['html'],
			array( 'Content-Type: text/html; charset=UTF-8' )
		);

		if ( ! $sent ) {
			return new WP_Error( 'alynt_ag_email_send_failed', __( 'The email could not be sent. Please try again.', 'alynt-account-gateway' ) );
		}

		return true;
	}
}
