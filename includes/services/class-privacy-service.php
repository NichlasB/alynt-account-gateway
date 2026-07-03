<?php
/**
 * Privacy service placeholder.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers privacy hooks.
 */
class ALYNT_AG_Privacy_Service {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_init', array( $this, 'add_privacy_policy_content' ) );
	}

	/**
	 * Add privacy policy helper content.
	 *
	 * @return void
	 */
	public function add_privacy_policy_content() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}

		wp_add_privacy_policy_content(
			__( 'Alynt Account Gateway', 'alynt-account-gateway' ),
			wp_kses_post(
				__( 'Alynt Account Gateway may process account registration data, email verification results, webhook delivery metadata, and consent records. Site owners should disclose configured third-party services such as Cloudflare Turnstile, Reoon Email Verifier, and outgoing webhooks.', 'alynt-account-gateway' )
			)
		);
	}
}
