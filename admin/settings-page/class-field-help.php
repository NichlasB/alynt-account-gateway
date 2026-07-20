<?php
/**
 * Settings page field-help component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused field-help behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Field_Help extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Return text direction for machine-readable settings fields.
	 *
	 * @param string              $key   Field key.
	 * @param array<string,mixed> $field Field schema.
	 * @return string
	 */
	public function field_direction_attribute( $key, $field ) {
		$type = isset( $field['type'] ) ? (string) $field['type'] : '';

		if ( in_array( $type, array( 'relative_path', 'url', 'secret', 'css_font_family' ), true ) ) {
			return ' dir="ltr"';
		}

		if ( in_array( $key, array( 'turnstile_site_key', 'username_format' ), true ) ) {
			return ' dir="ltr"';
		}

		return '';
	}

	/**
	 * Return select options for a field.
	 *
	 * @param string              $key   Field key.
	 * @param array<string,mixed> $field Field schema.
	 * @return array<string,string>
	 */
	public function field_select_options( $key, $field ) {
		if ( 'diagnostics_min_level' === $key ) {
			$options = array();

			foreach ( array_keys( ALYNT_AG_Diagnostics_Logger::levels() ) as $level ) {
				$options[ $level ] = ucfirst( $level );
			}

			return $options;
		}

		return ! empty( $field['options'] ) && is_array( $field['options'] ) ? $field['options'] : array();
	}

	/**
	 * Render help text for a settings field.
	 *
	 * @param string $key Field key.
	 * @return void
	 */
	public function render_field_help( $key ) {
		$help = $this->settings_field_help_text( $key );

		if ( '' === $help ) {
			return;
		}

		printf(
			'<p class="description alynt-ag-field-help" id="%1$s">%2$s</p>',
			esc_attr( $this->field_help_id( $key ) ),
			esc_html( $help )
		);
	}

	/**
	 * Return an aria-describedby attribute for fields with help text.
	 *
	 * @param string $key Field key.
	 * @return string
	 */
	public function field_describedby_attribute( $key ) {
		if ( '' === $this->settings_field_help_text( $key ) ) {
			return '';
		}

		return ' aria-describedby="' . esc_attr( $this->field_help_id( $key ) ) . '"';
	}

	/**
	 * Return the help text element ID for a settings field.
	 *
	 * @param string $key Field key.
	 * @return string
	 */
	public function field_help_id( $key ) {
		return sprintf( 'alynt-ag-%s-help', sanitize_key( $key ) );
	}

	/**
	 * Return field-level setup help text.
	 *
	 * @param string $key Field key.
	 * @return string
	 */
	public function settings_field_help_text( $key ) {
		$email_body_keys = array(
			'email_registration_confirmation_body',
			'email_password_reset_body',
			'email_password_changed_body',
			'email_new_user_welcome_body',
			'email_change_confirmation_body',
		);

		if ( in_array( $key, $email_body_keys, true ) ) {
			return __( 'Use the Visual editor for headings, emphasis, links, blockquotes, and lists. Tokens remain available in Visual and Text modes.', 'alynt-account-gateway' );
		}

		$help = array(
			'frontend_enabled'                    => __( 'Leave disabled until URLs, branding, registration, email, dashboard, privacy, and recovery settings have been reviewed.', 'alynt-account-gateway' ),
			'login_path'                          => __( 'Use a relative path such as /login. This path becomes the clean public login URL.', 'alynt-account-gateway' ),
			'account_action_base'                 => __( 'Use a relative path such as /account. WordPress login actions are served from this base with action parameters.', 'alynt-account-gateway' ),
			'after_login_redirect'                => __( 'Use the destination users should see after login, usually /my-account/ when the dashboard or WooCommerce account area is enabled.', 'alynt-account-gateway' ),
			'administrator_after_login_redirect'  => __( 'Used when an administrator logs in without a safe requested destination. The default is /wp-admin/.', 'alynt-account-gateway' ),
			'shop_manager_after_login_redirect'   => __( 'Used when a WooCommerce shop manager logs in without a safe requested destination. The default is /wp-admin/.', 'alynt-account-gateway' ),
			'emergency_bypass_key'                => __( 'Store this privately. It lets administrators reach the native wp-login.php screen if custom routing causes a lockout.', 'alynt-account-gateway' ),
			'registration_enabled'                => __( 'Public account creation is disabled by default. Enable it only after terms, privacy, email confirmation, and anti-spam settings are ready.', 'alynt-account-gateway' ),
			'registration_token_hours'            => __( 'Pending registrations expire after this many hours. The default 24-hour window gives customers time to find the email without leaving stale invitations open too long.', 'alynt-account-gateway' ),
			'username_format'                     => __( 'Use tokens such as {first_name} and {last_name}. Customers log in by email, but WordPress still needs a generated username.', 'alynt-account-gateway' ),
			'terms_path'                          => __( 'Use a relative URL path to the Terms page. Registration should not launch until this page exists.', 'alynt-account-gateway' ),
			'privacy_path'                        => __( 'Use a relative URL path to the Privacy Policy page. Registration should not launch until this page exists.', 'alynt-account-gateway' ),
			'brand_logo_id'                       => __( 'Shown on account gateway screens and the custom dashboard. Use a clear logo that remains readable at smaller sizes.', 'alynt-account-gateway' ),
			'brand_logo_max_width'                => __( 'Controls the displayed logo width in pixels. Keep this modest so forms remain visible on small screens.', 'alynt-account-gateway' ),
			'primary_color'                       => __( 'Use the color swatch to open the picker, or enter a six-digit hex value such as #3B5249. Used for primary accents and focus states; check contrast against surface and background colors.', 'alynt-account-gateway' ),
			'accent_color'                        => __( 'Used for soft panels and supporting accents. Choose a color that supports the primary color without overpowering form content.', 'alynt-account-gateway' ),
			'button_background_color'             => __( 'Check this together with the button text color so primary actions remain readable and accessible.', 'alynt-account-gateway' ),
			'button_text_color'                   => __( 'Use enough contrast against the button background color for readable primary actions.', 'alynt-account-gateway' ),
			'background_image_id'                 => __( 'Used in the desktop two-column gateway layout. Choose a tall portrait image that can crop gracefully as the viewport changes. Recommended size: about 1280 x 1920px or similar 2:3 portrait ratio.', 'alynt-account-gateway' ),
			'heading_font_family'                 => __( 'Enter a comma-separated CSS font stack. For example, if Blocksy already loads the Google Font Poppins, use "Poppins", Arial, sans-serif. This plugin does not load fonts itself.', 'alynt-account-gateway' ),
			'body_font_family'                    => __( 'Enter the preferred font first, followed by fallbacks. For example, if Blocksy already loads Inter, use "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif.', 'alynt-account-gateway' ),
			'protection_mode'                     => __( 'Controls how configured anti-spam providers are evaluated during registration.', 'alynt-account-gateway' ),
			'turnstile_site_key'                  => __( 'Pair this with the matching secret key. The widget is only trustworthy when the server-side token check succeeds.', 'alynt-account-gateway' ),
			'turnstile_secret_key'                => __( 'Keep this private. The plugin uses it server-side to verify Turnstile tokens during registration.', 'alynt-account-gateway' ),
			'reoon_api_key'                       => __( 'Used to verify registration email quality. Treat uncertain results according to the selected protection policy.', 'alynt-account-gateway' ),
			'reoon_mode'                          => __( 'Quick mode is faster for most sites; stricter modes may take longer but can provide deeper mailbox checks.', 'alynt-account-gateway' ),
			'reoon_flagged_policy'                => __( 'Default keeps uncertain Reoon statuses usable while logging them for review. Use blocking only when the site prefers fewer signups over more false positives.', 'alynt-account-gateway' ),
			'registration_rate_limit_count'       => __( 'Maximum registration attempts allowed from the same source during the rate-limit window.', 'alynt-account-gateway' ),
			'registration_rate_limit_window'      => __( 'Length of the registration rate-limit window in minutes.', 'alynt-account-gateway' ),
			'login_rate_limit_count'              => __( 'Maximum login attempts allowed from the same source during the rate-limit window.', 'alynt-account-gateway' ),
			'login_rate_limit_window'             => __( 'Length of the login rate-limit window in minutes.', 'alynt-account-gateway' ),
			'lostpassword_rate_limit_count'       => __( 'Maximum password reset requests allowed from the same source during the rate-limit window.', 'alynt-account-gateway' ),
			'lostpassword_rate_limit_window'      => __( 'Length of the password reset rate-limit window in minutes.', 'alynt-account-gateway' ),
			'email_test_recipient'                => __( 'Use an address you control, then preview and send representative account emails before launch.', 'alynt-account-gateway' ),
			'email_password_changed_disabled'     => __( 'Disable only when another trusted system sends an equivalent password-change notification.', 'alynt-account-gateway' ),
			'email_new_user_welcome_disabled'     => __( 'Disable only when another onboarding or CRM system sends an equivalent welcome message.', 'alynt-account-gateway' ),
			'email_change_confirmation_disabled'  => __( 'Disable only when another trusted system handles email-change confirmation.', 'alynt-account-gateway' ),
			'dashboard_enabled'                   => __( 'Enable this when logged-in users should see the branded full-page account dashboard.', 'alynt-account-gateway' ),
			'dashboard_custom_links'              => __( 'Add only links that are useful to the selected roles. Ordering and icons help repeated account tasks stay scannable.', 'alynt-account-gateway' ),
			'dashboard_offcanvas_enabled'         => __( 'Optional. When enabled, the dashboard header can open a right-side menu panel using a selected WordPress navigation menu.', 'alynt-account-gateway' ),
			'dashboard_offcanvas_menu_id'         => __( 'Select a menu created under Appearance > Menus. The hamburger icon appears only when the menu panel is enabled and a menu is selected.', 'alynt-account-gateway' ),
			'dashboard_footer_menu_enabled'       => __( 'Optional. Display a compact navigation menu below the dashboard for legal, privacy, cookie, and policy links.', 'alynt-account-gateway' ),
			'dashboard_footer_menu_id'            => __( 'Select a menu created under Appearance > Menus. This assignment is independent from the dashboard menu panel, but both may use the same menu.', 'alynt-account-gateway' ),
			'woocommerce_takeover'                => __( 'Requires the custom dashboard. WooCommerce still handles account forms and endpoint actions inside the branded shell.', 'alynt-account-gateway' ),
			'woocommerce_require_login_checkout'  => __( 'Optional. Redirect logged-out visitors from the main WooCommerce checkout to the branded login screen, then return them to checkout after login. This does not change WooCommerce guest-checkout settings.', 'alynt-account-gateway' ),
			'woocommerce_require_login_order_pay' => __( 'Leave disabled unless customers must authenticate before using order-payment links. Guest payment links remain available by default.', 'alynt-account-gateway' ),
			'woocommerce_hidden_menu_items'       => __( 'All detected items are shown by default. Clear an item to remove its dashboard card and overview shortcut without disabling the WooCommerce endpoint or direct URL.', 'alynt-account-gateway' ),
			'account_created_webhook'             => __( 'Receives account-created events after the user confirms email and sets a password.', 'alynt-account-gateway' ),
			'webhook_signing_secret'              => __( 'Add this when the receiving system can verify timestamped HMAC headers.', 'alynt-account-gateway' ),
			'debug_payload_logging'               => __( 'Enable only while debugging. Payload logging may store personal account data in webhook logs.', 'alynt-account-gateway' ),
			'diagnostics_enabled'                 => __( 'Enable temporarily when setup or support needs additional event evidence.', 'alynt-account-gateway' ),
			'diagnostics_min_level'               => __( 'Controls the lowest event severity stored when diagnostics are enabled.', 'alynt-account-gateway' ),
			'diagnostics_retention'               => __( 'Number of days diagnostics events are retained before cleanup.', 'alynt-account-gateway' ),
			'success_log_retention'               => __( 'Successful webhook logs usually need shorter retention than failed delivery evidence.', 'alynt-account-gateway' ),
			'failed_log_retention'                => __( 'Failed webhook logs can be retained longer to support troubleshooting and resend decisions.', 'alynt-account-gateway' ),
			'verification_log_retention'          => __( 'Controls how long anti-spam and email verification records are kept.', 'alynt-account-gateway' ),
			'consent_record_retention'            => __( 'Controls how long registration consent evidence is retained.', 'alynt-account-gateway' ),
			'audit_log_retention'                 => __( 'Controls how long plugin audit events are retained for operational review.', 'alynt-account-gateway' ),
		);

		return isset( $help[ $key ] ) ? $help[ $key ] : '';
	}
}
