<?php
/**
 * Settings page.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the plugin settings page.
 */
class ALYNT_AG_Settings_Page {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_post_alynt_ag_export_settings', array( $this, 'handle_export_settings' ) );
		add_action( 'admin_post_alynt_ag_import_settings', array( $this, 'handle_import_settings' ) );
		add_action( 'admin_post_alynt_ag_restore_tab_defaults', array( $this, 'handle_restore_tab_defaults' ) );
		add_action( 'admin_post_alynt_ag_preview_gateway', array( $this, 'handle_preview_gateway' ) );
		add_action( 'admin_post_alynt_ag_export_diagnostics', array( $this, 'handle_export_diagnostics' ) );
		add_action( 'admin_post_alynt_ag_clear_diagnostics', array( $this, 'handle_clear_diagnostics' ) );
		add_action( 'admin_post_alynt_ag_preview_email', array( $this, 'handle_preview_email' ) );
		add_action( 'admin_post_alynt_ag_test_email', array( $this, 'handle_test_email' ) );
		add_action( 'admin_post_alynt_ag_test_webhook', array( $this, 'handle_test_webhook' ) );
		add_action( 'update_option_alynt_ag_settings', array( $this, 'log_settings_change' ), 10, 2 );
	}

	/**
	 * Add settings page.
	 *
	 * @return void
	 */
	public function add_menu_page() {
		add_options_page(
			__( 'Alynt Account Gateway', 'alynt-account-gateway' ),
			__( 'Account Gateway', 'alynt-account-gateway' ),
			'manage_options',
			'alynt-account-gateway',
			array( $this, 'render' )
		);
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'alynt_ag_settings',
			'alynt_ag_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( 'ALYNT_AG_Settings_Schema', 'sanitize' ),
				'default'           => ALYNT_AG_Settings_Schema::defaults(),
			)
		);
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$tabs = ALYNT_AG_Settings_Schema::tabs();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only tab navigation.
		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general';
		$active_tab = isset( $tabs[ $active_tab ] ) ? $active_tab : 'general';
		$settings   = ALYNT_AG_Settings_Schema::get_settings();
		$schema     = ALYNT_AG_Settings_Schema::schema();
		$tab_fields = array_filter(
			$schema,
			static function ( $field ) use ( $active_tab ) {
				return isset( $field['tab'] ) && $field['tab'] === $active_tab;
			}
		);
		?>
		<div class="wrap alynt-ag-admin">
			<h1><?php esc_html_e( 'Alynt Account Gateway', 'alynt-account-gateway' ); ?></h1>
			<hr class="wp-header-end">
			<?php $this->render_admin_notice(); ?>

			<nav class="nav-tab-wrapper" aria-label="<?php esc_attr_e( 'Settings tabs', 'alynt-account-gateway' ); ?>">
				<?php foreach ( $tabs as $tab_key => $tab_label ) : ?>
					<a
						class="nav-tab <?php echo $active_tab === $tab_key ? 'nav-tab-active' : ''; ?>"
						href="
						<?php
						echo esc_url(
							add_query_arg(
								array(
									'page' => 'alynt-account-gateway',
									'tab'  => $tab_key,
								),
								admin_url( 'options-general.php' )
							)
						);
						?>
								"
					>
						<?php echo esc_html( $tab_label ); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<?php $this->render_tab_guidance( $active_tab ); ?>

			<?php if ( 'general' === $active_tab ) : ?>
				<?php $this->render_setup_readiness_panel( $settings ); ?>
			<?php endif; ?>

			<form method="post" action="options.php">
				<?php settings_fields( 'alynt_ag_settings' ); ?>

				<table class="form-table" role="presentation">
					<tbody>
						<?php foreach ( $tab_fields as $key => $field ) : ?>
							<tr>
								<th scope="row">
									<label for="alynt-ag-<?php echo esc_attr( $key ); ?>">
										<?php echo esc_html( $field['label'] ); ?>
									</label>
								</th>
								<td>
									<?php $this->render_field( $key, $field, $settings[ $key ] ?? $field['default'] ); ?>
									<?php $this->render_field_help( $key ); ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<?php submit_button( __( 'Save Settings', 'alynt-account-gateway' ) ); ?>
			</form>

			<?php $this->render_restore_tab_defaults( $active_tab ); ?>

			<?php if ( 'security' === $active_tab ) : ?>
				<?php $this->render_security_status_panel( $settings ); ?>
			<?php endif; ?>

			<?php if ( 'advanced_tools' === $active_tab ) : ?>
				<?php $this->render_diagnostics_tools(); ?>
			<?php endif; ?>

			<?php if ( 'emails' === $active_tab ) : ?>
				<?php $this->render_email_tools(); ?>
			<?php endif; ?>

			<?php if ( 'webhooks' === $active_tab ) : ?>
				<?php $this->render_webhook_tools(); ?>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render one settings field.
	 *
	 * @param string              $key   Field key.
	 * @param array<string,mixed> $field Field schema.
	 * @param mixed               $value Current value.
	 * @return void
	 */
	private function render_field( $key, $field, $value ) {
		$name = sprintf( 'alynt_ag_settings[%s]', $key );
		$id   = sprintf( 'alynt-ag-%s', $key );
		$aria = $this->field_describedby_attribute( $key );

		if ( 'boolean' === $field['type'] ) {
			?>
			<label>
				<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="1" <?php checked( $value ); ?><?php echo $aria; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by field_describedby_attribute(). ?>>
				<?php esc_html_e( 'Enabled', 'alynt-account-gateway' ); ?>
			</label>
			<?php
			return;
		}

		if ( 'integer' === $field['type'] ) {
			printf(
				'<input type="number" min="0" class="small-text" id="%1$s" name="%2$s" value="%3$s"%4$s>',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value ),
				$aria // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by field_describedby_attribute().
			);
			return;
		}

		if ( 'attachment_id' === $field['type'] ) {
			$this->render_media_field( $id, $name, (int) $value );
			return;
		}

		if ( 'color' === $field['type'] ) {
			printf(
				'<input type="text" class="regular-text" id="%1$s" name="%2$s" value="%3$s" pattern="^#[a-fA-F0-9]{6}$"%4$s>',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value ),
				$aria // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by field_describedby_attribute().
			);
			return;
		}

		if ( 'textarea' === $field['type'] ) {
			printf(
				'<textarea class="large-text alynt-ag-textarea" rows="4" id="%1$s" name="%2$s"%4$s>%3$s</textarea>',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_textarea( $value ),
				$aria // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by field_describedby_attribute().
			);
			return;
		}

		if ( 'dashboard_links' === $field['type'] ) {
			$this->render_dashboard_links_field( $id, $name, $value );
			return;
		}

		if ( 'email' === $field['type'] ) {
			printf(
				'<input type="email" class="regular-text" id="%1$s" name="%2$s" value="%3$s" autocomplete="email"%4$s>',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value ),
				$aria // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by field_describedby_attribute().
			);
			return;
		}

		if ( 'select' === $field['type'] ) {
			$options = $this->field_select_options( $key, $field );

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $aria is escaped by field_describedby_attribute().
			echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '"' . $aria . '>';
			foreach ( $options as $option => $label ) {
				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $option ),
					selected( $value, $option, false ),
					esc_html( $label )
				);
			}
			echo '</select>';
			return;
		}

		$type      = 'secret' === $field['type'] ? 'password' : 'text';
		$direction = $this->field_direction_attribute( $key, $field );
		printf(
			'<input type="%1$s" class="regular-text" id="%2$s" name="%3$s" value="%4$s" autocomplete="off"%5$s%6$s>',
			esc_attr( $type ),
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $value ),
			$aria, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by field_describedby_attribute().
			$direction // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static attribute from field_direction_attribute().
		);
	}

	/**
	 * Return text direction for machine-readable settings fields.
	 *
	 * @param string              $key   Field key.
	 * @param array<string,mixed> $field Field schema.
	 * @return string
	 */
	private function field_direction_attribute( $key, $field ) {
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
	private function field_select_options( $key, $field ) {
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
	private function render_field_help( $key ) {
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
	private function field_describedby_attribute( $key ) {
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
	private function field_help_id( $key ) {
		return sprintf( 'alynt-ag-%s-help', sanitize_key( $key ) );
	}

	/**
	 * Return field-level setup help text.
	 *
	 * @param string $key Field key.
	 * @return string
	 */
	private function settings_field_help_text( $key ) {
		$help = array(
			'frontend_enabled'                   => __( 'Leave disabled until URLs, branding, registration, email, dashboard, privacy, and recovery settings have been reviewed.', 'alynt-account-gateway' ),
			'login_path'                         => __( 'Use a relative path such as /login. This path becomes the clean public login URL.', 'alynt-account-gateway' ),
			'account_action_base'                => __( 'Use a relative path such as /account. WordPress login actions are served from this base with action parameters.', 'alynt-account-gateway' ),
			'after_login_redirect'               => __( 'Use the destination users should see after login, usually /my-account/ when the dashboard or WooCommerce account area is enabled.', 'alynt-account-gateway' ),
			'emergency_bypass_key'               => __( 'Store this privately. It lets administrators reach the native wp-login.php screen if custom routing causes a lockout.', 'alynt-account-gateway' ),
			'registration_enabled'               => __( 'Public account creation is disabled by default. Enable it only after terms, privacy, email confirmation, and anti-spam settings are ready.', 'alynt-account-gateway' ),
			'registration_token_hours'           => __( 'Pending registrations expire after this many hours. The default 24-hour window gives customers time to find the email without leaving stale invitations open too long.', 'alynt-account-gateway' ),
			'username_format'                    => __( 'Use tokens such as {first_name} and {last_name}. Customers log in by email, but WordPress still needs a generated username.', 'alynt-account-gateway' ),
			'terms_path'                         => __( 'Use a relative URL path to the Terms page. Registration should not launch until this page exists.', 'alynt-account-gateway' ),
			'privacy_path'                       => __( 'Use a relative URL path to the Privacy Policy page. Registration should not launch until this page exists.', 'alynt-account-gateway' ),
			'brand_logo_id'                      => __( 'Shown on account gateway screens and the custom dashboard. Use a clear logo that remains readable at smaller sizes.', 'alynt-account-gateway' ),
			'brand_logo_max_width'               => __( 'Controls the displayed logo width in pixels. Keep this modest so forms remain visible on small screens.', 'alynt-account-gateway' ),
			'primary_color'                      => __( 'Used for primary accents, focus states, and branded UI elements. Check contrast against surface and background colors.', 'alynt-account-gateway' ),
			'accent_color'                       => __( 'Used for soft panels and supporting accents. Choose a color that supports the primary color without overpowering form content.', 'alynt-account-gateway' ),
			'button_background_color'            => __( 'Check this together with the button text color so primary actions remain readable and accessible.', 'alynt-account-gateway' ),
			'button_text_color'                  => __( 'Use enough contrast against the button background color for readable primary actions.', 'alynt-account-gateway' ),
			'background_image_id'                => __( 'Used in the desktop two-column gateway layout. Choose a wide image that can crop gracefully as the viewport changes.', 'alynt-account-gateway' ),
			'heading_font_family'                => __( 'Use a CSS font stack that is available on the site. Keep heading fonts readable at form-card sizes.', 'alynt-account-gateway' ),
			'body_font_family'                   => __( 'Use a CSS font stack suitable for forms, dashboard content, and small helper text.', 'alynt-account-gateway' ),
			'protection_mode'                    => __( 'Controls how configured anti-spam providers are evaluated during registration.', 'alynt-account-gateway' ),
			'turnstile_site_key'                 => __( 'Pair this with the matching secret key. The widget is only trustworthy when the server-side token check succeeds.', 'alynt-account-gateway' ),
			'turnstile_secret_key'               => __( 'Keep this private. The plugin uses it server-side to verify Turnstile tokens during registration.', 'alynt-account-gateway' ),
			'reoon_api_key'                      => __( 'Used to verify registration email quality. Treat uncertain results according to the selected protection policy.', 'alynt-account-gateway' ),
			'reoon_mode'                         => __( 'Quick mode is faster for most sites; stricter modes may take longer but can provide deeper mailbox checks.', 'alynt-account-gateway' ),
			'reoon_flagged_policy'               => __( 'Default keeps uncertain Reoon statuses usable while logging them for review. Use blocking only when the site prefers fewer signups over more false positives.', 'alynt-account-gateway' ),
			'registration_rate_limit_count'      => __( 'Maximum registration attempts allowed from the same source during the rate-limit window.', 'alynt-account-gateway' ),
			'registration_rate_limit_window'     => __( 'Length of the registration rate-limit window in minutes.', 'alynt-account-gateway' ),
			'login_rate_limit_count'             => __( 'Maximum login attempts allowed from the same source during the rate-limit window.', 'alynt-account-gateway' ),
			'login_rate_limit_window'            => __( 'Length of the login rate-limit window in minutes.', 'alynt-account-gateway' ),
			'lostpassword_rate_limit_count'      => __( 'Maximum password reset requests allowed from the same source during the rate-limit window.', 'alynt-account-gateway' ),
			'lostpassword_rate_limit_window'     => __( 'Length of the password reset rate-limit window in minutes.', 'alynt-account-gateway' ),
			'email_test_recipient'               => __( 'Use an address you control, then preview and send representative account emails before launch.', 'alynt-account-gateway' ),
			'email_password_changed_disabled'    => __( 'Disable only when another trusted system sends an equivalent password-change notification.', 'alynt-account-gateway' ),
			'email_new_user_welcome_disabled'    => __( 'Disable only when another onboarding or CRM system sends an equivalent welcome message.', 'alynt-account-gateway' ),
			'email_change_confirmation_disabled' => __( 'Disable only when another trusted system handles email-change confirmation.', 'alynt-account-gateway' ),
			'dashboard_enabled'                  => __( 'Enable this when logged-in users should see the branded full-page account dashboard.', 'alynt-account-gateway' ),
			'dashboard_custom_links'             => __( 'Add only links that are useful to the selected roles. Ordering and icons help repeated account tasks stay scannable.', 'alynt-account-gateway' ),
			'woocommerce_takeover'               => __( 'Requires the custom dashboard. WooCommerce still handles account forms and endpoint actions inside the branded shell.', 'alynt-account-gateway' ),
			'account_created_webhook'            => __( 'Receives account-created events after the user confirms email and sets a password.', 'alynt-account-gateway' ),
			'webhook_signing_secret'             => __( 'Add this when the receiving system can verify timestamped HMAC headers.', 'alynt-account-gateway' ),
			'debug_payload_logging'              => __( 'Enable only while debugging. Payload logging may store personal account data in webhook logs.', 'alynt-account-gateway' ),
			'diagnostics_enabled'                => __( 'Enable temporarily when setup or support needs additional event evidence.', 'alynt-account-gateway' ),
			'diagnostics_min_level'              => __( 'Controls the lowest event severity stored when diagnostics are enabled.', 'alynt-account-gateway' ),
			'diagnostics_retention'              => __( 'Number of days diagnostics events are retained before cleanup.', 'alynt-account-gateway' ),
			'success_log_retention'              => __( 'Successful webhook logs usually need shorter retention than failed delivery evidence.', 'alynt-account-gateway' ),
			'failed_log_retention'               => __( 'Failed webhook logs can be retained longer to support troubleshooting and resend decisions.', 'alynt-account-gateway' ),
			'verification_log_retention'         => __( 'Controls how long anti-spam and email verification records are kept.', 'alynt-account-gateway' ),
			'consent_record_retention'           => __( 'Controls how long registration consent evidence is retained.', 'alynt-account-gateway' ),
			'audit_log_retention'                => __( 'Controls how long plugin audit events are retained for operational review.', 'alynt-account-gateway' ),
		);

		return isset( $help[ $key ] ) ? $help[ $key ] : '';
	}

	/**
	 * Render simple admin action notices.
	 *
	 * @return void
	 */
	private function render_admin_notice() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin notice flag.
		$notice = isset( $_GET['alynt_ag_notice'] ) ? sanitize_key( wp_unslash( $_GET['alynt_ag_notice'] ) ) : '';

		if ( 'settings_imported' === $notice ) {
			?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings imported successfully.', 'alynt-account-gateway' ); ?></p></div>
			<?php
			return;
		}

		if ( 'settings_imported_with_ignored_keys' === $notice ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin notice flag.
			$ignored_count = isset( $_GET['alynt_ag_import_ignored'] ) ? absint( wp_unslash( $_GET['alynt_ag_import_ignored'] ) ) : 0;
			?>
			<div class="notice notice-warning is-dismissible">
				<p>
					<?php
					printf(
						/* translators: %d: ignored settings key count. */
						esc_html__( 'Settings imported successfully. Unrecognized setting keys ignored: %d.', 'alynt-account-gateway' ),
						esc_html( (string) $ignored_count )
					);
					?>
				</p>
			</div>
			<?php
			return;
		}

		if ( 'settings_import_failed' === $notice ) {
			?>
			<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'Settings could not be imported. Choose a valid Alynt Account Gateway JSON export.', 'alynt-account-gateway' ); ?></p></div>
			<?php
			return;
		}

		if ( 'settings_import_invalid_json' === $notice ) {
			?>
			<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'Settings could not be imported because the selected file is not valid JSON.', 'alynt-account-gateway' ); ?></p></div>
			<?php
			return;
		}

		if ( 'settings_import_empty' === $notice ) {
			?>
			<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'Settings could not be imported because the file does not contain recognized Alynt Account Gateway settings.', 'alynt-account-gateway' ); ?></p></div>
			<?php
			return;
		}

		if ( 'settings_import_upload_failed' === $notice ) {
			?>
			<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'Settings could not be imported because the uploaded file could not be read.', 'alynt-account-gateway' ); ?></p></div>
			<?php
			return;
		}

		if ( 'tab_defaults_restored' === $notice ) {
			?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'This settings tab was restored to its defaults.', 'alynt-account-gateway' ); ?></p></div>
			<?php
			return;
		}

		if ( 'tab_defaults_failed' === $notice ) {
			?>
			<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'This settings tab could not be restored.', 'alynt-account-gateway' ); ?></p></div>
			<?php
			return;
		}

		if ( 'email_test_sent' === $notice ) {
			?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Test email sent.', 'alynt-account-gateway' ); ?></p></div>
			<?php
			return;
		}

		if ( 'email_test_failed' === $notice ) {
			?>
			<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'The test email could not be sent. Check the recipient and mail configuration.', 'alynt-account-gateway' ); ?></p></div>
			<?php
			return;
		}

		if ( 'webhook_test_sent' === $notice ) {
			?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Test webhook sent.', 'alynt-account-gateway' ); ?></p></div>
			<?php
			return;
		}

		if ( 'webhook_test_missing' === $notice ) {
			?>
			<div class="notice notice-warning is-dismissible"><p><?php esc_html_e( 'Add and save an account-created webhook URL before sending a test.', 'alynt-account-gateway' ); ?></p></div>
			<?php
			return;
		}

		if ( 'webhook_test_failed' === $notice ) {
			?>
			<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'The test webhook could not be sent. Review the recent webhook deliveries table for details.', 'alynt-account-gateway' ); ?></p></div>
			<?php
		}
	}

	/**
	 * Render tab-level setup guidance.
	 *
	 * @param string $active_tab Active settings tab.
	 * @return void
	 */
	private function render_tab_guidance( $active_tab ) {
		$guidance = $this->settings_tab_guidance();
		$tab      = isset( $guidance[ $active_tab ] ) ? $active_tab : 'general';
		$item     = $guidance[ $tab ];
		?>
		<section class="alynt-ag-tab-guidance" aria-labelledby="alynt-ag-tab-guidance-title">
			<div class="alynt-ag-tab-guidance__copy">
				<p class="alynt-ag-tab-guidance__eyebrow"><?php esc_html_e( 'Tab Guidance', 'alynt-account-gateway' ); ?></p>
				<h2 id="alynt-ag-tab-guidance-title"><?php echo esc_html( $item['title'] ); ?></h2>
				<p><?php echo esc_html( $item['description'] ); ?></p>
			</div>
			<ul class="alynt-ag-tab-guidance__steps">
				<?php foreach ( $item['steps'] as $step ) : ?>
					<li><?php echo esc_html( $step ); ?></li>
				<?php endforeach; ?>
			</ul>
			<?php if ( ! empty( $item['related_tab'] ) && ! empty( $item['related_label'] ) ) : ?>
				<a class="button button-secondary" href="<?php echo esc_url( $this->settings_tab_url( $item['related_tab'] ) ); ?>">
					<?php echo esc_html( $item['related_label'] ); ?>
				</a>
			<?php endif; ?>
		</section>
		<?php
	}

	/**
	 * Return tab-level setup guidance.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	private function settings_tab_guidance() {
		return array(
			'general'        => array(
				'title'         => __( 'Start safely before changing public account screens.', 'alynt-account-gateway' ),
				'description'   => __( 'Keep frontend output disabled while you configure and preview the gateway. Use readiness checks here as the launch gate.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Confirm URLs, branding, registration, email, dashboard, and privacy settings before enabling public output.', 'alynt-account-gateway' ),
					__( 'Use previews and test sends before sending real users to the gateway.', 'alynt-account-gateway' ),
					__( 'Save the emergency bypass key somewhere private before replacing public login screens.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'urls',
				'related_label' => __( 'Review URLs', 'alynt-account-gateway' ),
			),
			'urls'           => array(
				'title'         => __( 'Set the public account paths first.', 'alynt-account-gateway' ),
				'description'   => __( 'These paths decide where login, account actions, and post-login redirects send users.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Keep the login path separate from the account action base when you want clean login URLs.', 'alynt-account-gateway' ),
					__( 'Use relative paths so settings can move cleanly between staging and production domains.', 'alynt-account-gateway' ),
					__( 'Confirm the after-login redirect matches the dashboard or WooCommerce account destination.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'dashboard',
				'related_label' => __( 'Review Dashboard', 'alynt-account-gateway' ),
			),
			'branding'       => array(
				'title'         => __( 'Make the default gateway feel site-owned.', 'alynt-account-gateway' ),
				'description'   => __( 'Logo, color, layout, and font settings control the front-facing gateway shell and account dashboard.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Upload a brand logo and set a max width that works on mobile and desktop.', 'alynt-account-gateway' ),
					__( 'Check button background and text colors together for readable contrast.', 'alynt-account-gateway' ),
					__( 'Use one global background image that scales well in a two-column layout.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'copy',
				'related_label' => __( 'Review Screen Copy', 'alynt-account-gateway' ),
			),
			'copy'           => array(
				'title'         => __( 'Tune the words users see at sensitive account moments.', 'alynt-account-gateway' ),
				'description'   => __( 'Screen copy appears above login, registration, password, logout, disabled-registration, and invalid-link states.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Keep instructions short and reassuring so forms stay easy to scan.', 'alynt-account-gateway' ),
					__( 'Mention spam-folder checks on registration and password reset flows where appropriate.', 'alynt-account-gateway' ),
					__( 'Avoid brand-specific claims in reusable defaults; save those for site-specific configuration.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'branding',
				'related_label' => __( 'Review Branding', 'alynt-account-gateway' ),
			),
			'registration'   => array(
				'title'         => __( 'Keep account creation intentional.', 'alynt-account-gateway' ),
				'description'   => __( 'Registration stays disabled by default. Enable it only after terms, privacy, confirmation, and protection settings are ready.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Confirm the Terms and Privacy paths point to real public pages.', 'alynt-account-gateway' ),
					__( 'Keep the 24-hour pending registration window unless the site has a reason to shorten it.', 'alynt-account-gateway' ),
					__( 'Review username generation before inviting customers so generated usernames stay consistent.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'security',
				'related_label' => __( 'Review Security', 'alynt-account-gateway' ),
			),
			'security'       => array(
				'title'         => __( 'Layer protection before opening registration.', 'alynt-account-gateway' ),
				'description'   => __( 'Turnstile, Reoon, and rate limits reduce spam signups, repeated login attempts, and password-reset abuse.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Configure at least one provider before enabling public registration.', 'alynt-account-gateway' ),
					__( 'Use server-side Turnstile verification keys, not the client widget alone.', 'alynt-account-gateway' ),
					__( 'Keep rate limits conservative until real traffic patterns are known.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'registration',
				'related_label' => __( 'Review Registration', 'alynt-account-gateway' ),
			),
			'emails'         => array(
				'title'         => __( 'Preview account emails before users rely on them.', 'alynt-account-gateway' ),
				'description'   => __( 'Email settings control confirmation, password reset, password changed, welcome, and email-change confirmation messages.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Set a test recipient before using preview and test-send tools.', 'alynt-account-gateway' ),
					__( 'Keep required confirmation and reset tokens in the message body.', 'alynt-account-gateway' ),
					__( 'Only disable account emails when another system reliably covers the same notification.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'registration',
				'related_label' => __( 'Review Registration', 'alynt-account-gateway' ),
			),
			'dashboard'      => array(
				'title'         => __( 'Decide where logged-in users land.', 'alynt-account-gateway' ),
				'description'   => __( 'Dashboard settings control the branded account dashboard and custom links users can access after login.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Enable the custom dashboard before enabling WooCommerce takeover.', 'alynt-account-gateway' ),
					__( 'Add custom links only when they are useful to the roles that can see them.', 'alynt-account-gateway' ),
					__( 'Use ordering and icons to keep repeated account tasks easy to scan.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'woocommerce',
				'related_label' => __( 'Review WooCommerce', 'alynt-account-gateway' ),
			),
			'woocommerce'    => array(
				'title'         => __( 'Take over WooCommerce account screens carefully.', 'alynt-account-gateway' ),
				'description'   => __( 'WooCommerce takeover wraps native account endpoints in the branded dashboard while WooCommerce keeps handling account actions.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Keep the custom dashboard enabled before switching on WooCommerce takeover.', 'alynt-account-gateway' ),
					__( 'Smoke orders, addresses, account details, downloads, and payment methods after changes.', 'alynt-account-gateway' ),
					__( 'Leave sensitive form handling delegated to WooCommerce.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'dashboard',
				'related_label' => __( 'Review Dashboard', 'alynt-account-gateway' ),
			),
			'webhooks'       => array(
				'title'         => __( 'Send account-created data only where it belongs.', 'alynt-account-gateway' ),
				'description'   => __( 'Webhook settings dispatch account-created events to external tools and can include signing headers for receiver verification.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Add a signing secret when the receiver can verify HMAC headers.', 'alynt-account-gateway' ),
					__( 'Use the test webhook tool before relying on automation downstream.', 'alynt-account-gateway' ),
					__( 'Enable debug payload logging only while diagnosing webhook payloads.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'privacy',
				'related_label' => __( 'Review Retention', 'alynt-account-gateway' ),
			),
			'privacy'        => array(
				'title'         => __( 'Set retention before collecting account evidence.', 'alynt-account-gateway' ),
				'description'   => __( 'Privacy settings control how long plugin-owned verification, webhook, consent, and audit records are kept.', 'alynt-account-gateway' ),
				'steps'         => array(
					__( 'Keep successful webhook logs shorter than failed logs unless the site needs longer audit evidence.', 'alynt-account-gateway' ),
					__( 'Retain consent and audit records long enough for operational review.', 'alynt-account-gateway' ),
					__( 'Confirm exporter and eraser behavior during privacy QA.', 'alynt-account-gateway' ),
				),
				'related_tab'   => 'advanced_tools',
				'related_label' => __( 'Review Tools', 'alynt-account-gateway' ),
			),
			'advanced_tools' => array(
				'title'       => __( 'Use advanced tools for recovery and diagnostics.', 'alynt-account-gateway' ),
				'description' => __( 'Advanced tools include emergency access, import/export, diagnostics, and cleanup controls for setup and support.', 'alynt-account-gateway' ),
				'steps'       => array(
					__( 'Store the emergency bypass key securely before replacing public login routes.', 'alynt-account-gateway' ),
					__( 'Export settings before larger configuration changes.', 'alynt-account-gateway' ),
					__( 'Enable diagnostics only when setup or support needs extra evidence.', 'alynt-account-gateway' ),
				),
			),
		);
	}

	/**
	 * Render setup readiness checks.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return void
	 */
	private function render_setup_readiness_panel( $settings ) {
		$checks = $this->setup_readiness_checks( $settings );
		$counts = $this->setup_readiness_counts( $checks );
		?>
		<section class="alynt-ag-readiness" aria-labelledby="alynt-ag-readiness-title">
			<div class="alynt-ag-readiness__header">
				<div>
					<h2 id="alynt-ag-readiness-title"><?php esc_html_e( 'Setup Readiness', 'alynt-account-gateway' ); ?></h2>
					<p><?php esc_html_e( 'Review these checks before enabling public account gateway output.', 'alynt-account-gateway' ); ?></p>
				</div>
				<div class="alynt-ag-readiness__summary" aria-label="<?php esc_attr_e( 'Setup readiness summary', 'alynt-account-gateway' ); ?>">
					<span><strong><?php echo esc_html( (string) $counts['action'] ); ?></strong> <?php esc_html_e( 'Action Needed', 'alynt-account-gateway' ); ?></span>
					<span><strong><?php echo esc_html( (string) $counts['warning'] ); ?></strong> <?php esc_html_e( 'Review', 'alynt-account-gateway' ); ?></span>
					<span><strong><?php echo esc_html( (string) $counts['ready'] ); ?></strong> <?php esc_html_e( 'Ready', 'alynt-account-gateway' ); ?></span>
				</div>
			</div>
			<ul class="alynt-ag-readiness__list">
				<?php foreach ( $checks as $check ) : ?>
					<li class="alynt-ag-readiness__item alynt-ag-readiness__item--<?php echo esc_attr( $check['status'] ); ?>">
						<span class="alynt-ag-readiness__badge"><?php echo esc_html( $this->readiness_status_label( $check['status'] ) ); ?></span>
						<div>
							<strong><?php echo esc_html( $check['label'] ); ?></strong>
							<p><?php echo esc_html( $check['message'] ); ?></p>
							<?php if ( ! empty( $check['tab'] ) ) : ?>
								<a href="<?php echo esc_url( $this->settings_tab_url( $check['tab'] ) ); ?>"><?php esc_html_e( 'Open Setting', 'alynt-account-gateway' ); ?></a>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
		<?php
	}

	/**
	 * Return setup readiness checks.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return array<int,array{label:string,status:string,message:string,tab:string}>
	 */
	private function setup_readiness_checks( $settings ) {
		$has_login_path       = ! empty( $settings['login_path'] );
		$has_action_base      = ! empty( $settings['account_action_base'] );
		$has_after_login      = ! empty( $settings['after_login_redirect'] );
		$registration_enabled = ! empty( $settings['registration_enabled'] );
		$has_turnstile        = ! empty( $settings['turnstile_site_key'] ) && ! empty( $settings['turnstile_secret_key'] );
		$has_reoon            = ! empty( $settings['reoon_api_key'] );
		$dashboard_enabled    = ! empty( $settings['dashboard_enabled'] );
		$woocommerce_takeover = ! empty( $settings['woocommerce_takeover'] );
		$webhook_enabled      = ! empty( $settings['account_created_webhook'] );

		$checks = array();

		$checks[] = array(
			'label'   => __( 'Frontend Output', 'alynt-account-gateway' ),
			'status'  => ! empty( $settings['frontend_enabled'] ) ? 'ready' : 'warning',
			'message' => ! empty( $settings['frontend_enabled'] )
				? __( 'Frontend output is enabled. Keep the remaining checks ready before sending users to the gateway.', 'alynt-account-gateway' )
				: __( 'Frontend output is disabled, which is safest while setup is in progress.', 'alynt-account-gateway' ),
			'tab'     => 'general',
		);

		$checks[] = array(
			'label'   => __( 'Gateway URLs', 'alynt-account-gateway' ),
			'status'  => $has_login_path && $has_action_base && $has_after_login ? 'ready' : 'action',
			'message' => $has_login_path && $has_action_base && $has_after_login
				? __( 'Login, account action, and after-login paths are configured.', 'alynt-account-gateway' )
				: __( 'Set the login path, account action base, and after-login redirect before enabling frontend output.', 'alynt-account-gateway' ),
			'tab'     => 'urls',
		);

		$checks[] = array(
			'label'   => __( 'Emergency Access', 'alynt-account-gateway' ),
			'status'  => ! empty( $settings['emergency_bypass_key'] ) ? 'ready' : 'action',
			'message' => ! empty( $settings['emergency_bypass_key'] )
				? __( 'An emergency bypass key exists for restoring access to the native login screen.', 'alynt-account-gateway' )
				: __( 'Generate and save an emergency bypass key before replacing public login screens.', 'alynt-account-gateway' ),
			'tab'     => 'advanced_tools',
		);

		$checks[] = array(
			'label'   => __( 'Branding', 'alynt-account-gateway' ),
			'status'  => ! empty( $settings['brand_logo_id'] ) ? 'ready' : 'warning',
			'message' => ! empty( $settings['brand_logo_id'] )
				? __( 'A brand logo is configured for gateway screens and the dashboard.', 'alynt-account-gateway' )
				: __( 'No brand logo is configured yet. The gateway can still run, but branded output will feel less complete.', 'alynt-account-gateway' ),
			'tab'     => 'branding',
		);

		$checks[] = $this->registration_readiness_check( $registration_enabled, $has_turnstile, $has_reoon, $settings );

		$checks[] = array(
			'label'   => __( 'Email Testing', 'alynt-account-gateway' ),
			'status'  => ! empty( $settings['email_test_recipient'] ) ? 'ready' : 'warning',
			'message' => ! empty( $settings['email_test_recipient'] )
				? __( 'A test recipient is configured for email preview and test-send checks.', 'alynt-account-gateway' )
				: __( 'Add a test recipient and send representative account emails before inviting users.', 'alynt-account-gateway' ),
			'tab'     => 'emails',
		);

		$checks[] = array(
			'label'   => __( 'Dashboard', 'alynt-account-gateway' ),
			'status'  => $dashboard_enabled ? 'ready' : 'warning',
			'message' => $dashboard_enabled
				? __( 'The branded account dashboard is enabled.', 'alynt-account-gateway' )
				: __( 'The branded account dashboard is disabled. Users may be redirected to the configured account destination without the custom dashboard.', 'alynt-account-gateway' ),
			'tab'     => 'dashboard',
		);

		$checks[] = $this->woocommerce_readiness_check( $dashboard_enabled, $woocommerce_takeover );

		$checks[] = array(
			'label'   => __( 'Webhook Signing', 'alynt-account-gateway' ),
			'status'  => ! $webhook_enabled || ! empty( $settings['webhook_signing_secret'] ) ? 'ready' : 'warning',
			'message' => $this->webhook_signing_readiness_message( $webhook_enabled, ! empty( $settings['webhook_signing_secret'] ) ),
			'tab'     => 'webhooks',
		);

		$checks[] = array(
			'label'   => __( 'Privacy Retention', 'alynt-account-gateway' ),
			'status'  => $this->privacy_retention_ready( $settings ) ? 'ready' : 'action',
			'message' => $this->privacy_retention_ready( $settings )
				? __( 'Plugin-owned privacy, verification, webhook, consent, and audit retention windows are configured.', 'alynt-account-gateway' )
				: __( 'Set retention windows above zero so plugin-owned logs and records can be cleaned up predictably.', 'alynt-account-gateway' ),
			'tab'     => 'privacy',
		);

		return $checks;
	}

	/**
	 * Return registration readiness.
	 *
	 * @param bool                $registration_enabled Whether public registration is enabled.
	 * @param bool                $has_turnstile        Whether Turnstile is configured.
	 * @param bool                $has_reoon            Whether Reoon is configured.
	 * @param array<string,mixed> $settings             Current settings.
	 * @return array{label:string,status:string,message:string,tab:string}
	 */
	private function registration_readiness_check( $registration_enabled, $has_turnstile, $has_reoon, $settings ) {
		if ( ! $registration_enabled ) {
			return array(
				'label'   => __( 'Public Registration', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'message' => __( 'Public registration is disabled by default. Enable it only after terms, privacy, and protection settings are reviewed.', 'alynt-account-gateway' ),
				'tab'     => 'registration',
			);
		}

		if ( empty( $settings['terms_path'] ) || empty( $settings['privacy_path'] ) ) {
			return array(
				'label'   => __( 'Public Registration', 'alynt-account-gateway' ),
				'status'  => 'action',
				'message' => __( 'Public registration is enabled, but Terms and Privacy paths must both be configured.', 'alynt-account-gateway' ),
				'tab'     => 'registration',
			);
		}

		if ( ! $has_turnstile && ! $has_reoon ) {
			return array(
				'label'   => __( 'Public Registration', 'alynt-account-gateway' ),
				'status'  => 'warning',
				'message' => __( 'Public registration is enabled without Turnstile or Reoon. Add at least one protection provider before public launch.', 'alynt-account-gateway' ),
				'tab'     => 'security',
			);
		}

		return array(
			'label'   => __( 'Public Registration', 'alynt-account-gateway' ),
			'status'  => 'ready',
			'message' => __( 'Public registration has Terms, Privacy, and at least one protection provider configured.', 'alynt-account-gateway' ),
			'tab'     => 'registration',
		);
	}

	/**
	 * Return WooCommerce readiness.
	 *
	 * @param bool $dashboard_enabled    Whether the custom dashboard is enabled.
	 * @param bool $woocommerce_takeover Whether WooCommerce takeover is enabled.
	 * @return array{label:string,status:string,message:string,tab:string}
	 */
	private function woocommerce_readiness_check( $dashboard_enabled, $woocommerce_takeover ) {
		if ( ! $woocommerce_takeover ) {
			return array(
				'label'   => __( 'WooCommerce Takeover', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'message' => __( 'WooCommerce account takeover is disabled.', 'alynt-account-gateway' ),
				'tab'     => 'woocommerce',
			);
		}

		if ( ! $dashboard_enabled ) {
			return array(
				'label'   => __( 'WooCommerce Takeover', 'alynt-account-gateway' ),
				'status'  => 'action',
				'message' => __( 'WooCommerce takeover requires the custom dashboard to be enabled.', 'alynt-account-gateway' ),
				'tab'     => 'dashboard',
			);
		}

		if ( ! class_exists( 'WooCommerce' ) && ! function_exists( 'WC' ) ) {
			return array(
				'label'   => __( 'WooCommerce Takeover', 'alynt-account-gateway' ),
				'status'  => 'warning',
				'message' => __( 'WooCommerce takeover is enabled, but WooCommerce does not appear to be active.', 'alynt-account-gateway' ),
				'tab'     => 'woocommerce',
			);
		}

		return array(
			'label'   => __( 'WooCommerce Takeover', 'alynt-account-gateway' ),
			'status'  => 'ready',
			'message' => __( 'WooCommerce takeover is enabled and WooCommerce appears to be active.', 'alynt-account-gateway' ),
			'tab'     => 'woocommerce',
		);
	}

	/**
	 * Return webhook signing readiness message.
	 *
	 * @param bool $webhook_enabled Whether a webhook URL is configured.
	 * @param bool $signing_enabled Whether signing is configured.
	 * @return string
	 */
	private function webhook_signing_readiness_message( $webhook_enabled, $signing_enabled ) {
		if ( ! $webhook_enabled ) {
			return __( 'No account-created webhook URL is configured.', 'alynt-account-gateway' );
		}

		if ( $signing_enabled ) {
			return __( 'Account-created webhooks include HMAC signing headers.', 'alynt-account-gateway' );
		}

		return __( 'A webhook URL is configured without a signing secret. Add signing before connecting sensitive automations.', 'alynt-account-gateway' );
	}

	/**
	 * Check whether retention windows are usable.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return bool
	 */
	private function privacy_retention_ready( $settings ) {
		foreach ( array( 'success_log_retention', 'failed_log_retention', 'verification_log_retention', 'consent_record_retention', 'audit_log_retention' ) as $key ) {
			if ( empty( $settings[ $key ] ) || 0 >= (int) $settings[ $key ] ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Count readiness check statuses.
	 *
	 * @param array<int,array{status:string}> $checks Readiness checks.
	 * @return array{action:int,warning:int,ready:int}
	 */
	private function setup_readiness_counts( $checks ) {
		$counts = array(
			'action'  => 0,
			'warning' => 0,
			'ready'   => 0,
		);

		foreach ( $checks as $check ) {
			if ( isset( $counts[ $check['status'] ] ) ) {
				++$counts[ $check['status'] ];
			}
		}

		return $counts;
	}

	/**
	 * Return a readiness status label.
	 *
	 * @param string $status Status key.
	 * @return string
	 */
	private function readiness_status_label( $status ) {
		if ( 'action' === $status ) {
			return __( 'Action Needed', 'alynt-account-gateway' );
		}

		if ( 'warning' === $status ) {
			return __( 'Review', 'alynt-account-gateway' );
		}

		return __( 'Ready', 'alynt-account-gateway' );
	}

	/**
	 * Build an admin URL to a settings tab.
	 *
	 * @param string $tab Tab key.
	 * @return string
	 */
	private function settings_tab_url( $tab ) {
		return add_query_arg(
			array(
				'page' => 'alynt-account-gateway',
				'tab'  => sanitize_key( $tab ),
			),
			admin_url( 'options-general.php' )
		);
	}

	/**
	 * Render security provider and rate-limit status guidance.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return void
	 */
	private function render_security_status_panel( $settings ) {
		$providers       = $this->security_provider_status_items( $settings );
		$launch_items    = $this->security_launch_decision_items( $settings );
		$rate_limits     = $this->security_rate_limit_items( $settings );
		$provider_counts = $this->setup_readiness_counts( $providers );
		?>
		<section class="alynt-ag-security-status" aria-labelledby="alynt-ag-security-status-title">
			<div class="alynt-ag-security-status__header">
				<div>
					<h2 id="alynt-ag-security-status-title"><?php esc_html_e( 'Security And Spam Status', 'alynt-account-gateway' ); ?></h2>
					<p><?php esc_html_e( 'Review configured anti-spam providers, Reoon policy handling, and rate limits before enabling public registration.', 'alynt-account-gateway' ); ?></p>
				</div>
				<div class="alynt-ag-readiness__summary" aria-label="<?php esc_attr_e( 'Security provider summary', 'alynt-account-gateway' ); ?>">
					<span><strong><?php echo esc_html( (string) $provider_counts['action'] ); ?></strong> <?php esc_html_e( 'Action Needed', 'alynt-account-gateway' ); ?></span>
					<span><strong><?php echo esc_html( (string) $provider_counts['warning'] ); ?></strong> <?php esc_html_e( 'Review', 'alynt-account-gateway' ); ?></span>
					<span><strong><?php echo esc_html( (string) $provider_counts['ready'] ); ?></strong> <?php esc_html_e( 'Ready', 'alynt-account-gateway' ); ?></span>
				</div>
			</div>

			<?php if ( 0 === $this->security_configured_provider_count( $settings ) ) : ?>
				<p class="alynt-ag-security-status__notice">
					<?php esc_html_e( 'No anti-spam provider is fully configured. Keep registration disabled or configure Turnstile or Reoon before going public.', 'alynt-account-gateway' ); ?>
				</p>
			<?php endif; ?>

			<div class="alynt-ag-security-launch" aria-label="<?php esc_attr_e( 'Security launch decision summary', 'alynt-account-gateway' ); ?>">
				<h3><?php esc_html_e( 'Launch Decision Summary', 'alynt-account-gateway' ); ?></h3>
				<p class="description"><?php esc_html_e( 'Use this quick checklist before making public registration available. It summarizes configuration choices that affect spam resistance, customer support, and launch evidence.', 'alynt-account-gateway' ); ?></p>
				<div class="alynt-ag-security-status__grid">
					<?php foreach ( $launch_items as $item ) : ?>
						<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
							<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
							<h4><?php echo esc_html( $item['label'] ); ?></h4>
							<p><?php echo esc_html( $item['message'] ); ?></p>
						</section>
					<?php endforeach; ?>
				</div>
			</div>

			<h3><?php esc_html_e( 'Provider Readiness', 'alynt-account-gateway' ); ?></h3>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $providers as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h4><?php echo esc_html( $item['label'] ); ?></h4>
						<p><?php echo esc_html( $item['message'] ); ?></p>
					</section>
				<?php endforeach; ?>
			</div>

			<?php $this->render_security_reoon_policy_guide( $settings ); ?>

			<h3><?php esc_html_e( 'Rate Limit Posture', 'alynt-account-gateway' ); ?></h3>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $rate_limits as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h4><?php echo esc_html( $item['label'] ); ?></h4>
						<p><?php echo esc_html( $item['message'] ); ?></p>
					</section>
				<?php endforeach; ?>
			</div>

			<?php $this->render_security_verification_activity(); ?>
			<?php $this->render_security_pending_registrations(); ?>
		</section>
		<?php
	}

	/**
	 * Return security launch decision items.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return array<int,array{label:string,status:string,message:string}>
	 */
	private function security_launch_decision_items( $settings ) {
		$registration_enabled = ! empty( $settings['registration_enabled'] );
		$provider_count       = $this->security_configured_provider_count( $settings );
		$has_terms            = ! empty( $settings['terms_path'] );
		$has_privacy          = ! empty( $settings['privacy_path'] );
		$has_reoon            = ! empty( $settings['reoon_api_key'] );
		$flagged_policy       = ! empty( $settings['reoon_flagged_policy'] ) ? sanitize_key( $settings['reoon_flagged_policy'] ) : 'allow';
		$diagnostics_enabled  = ! empty( $settings['diagnostics_enabled'] );

		return array(
			array(
				'label'   => __( 'Public Registration', 'alynt-account-gateway' ),
				'status'  => $registration_enabled ? 'ready' : 'action',
				'message' => $registration_enabled
					? __( 'Public account creation is enabled. Confirm the remaining checks before sending customers to registration.', 'alynt-account-gateway' )
					: __( 'Public account creation is disabled. Keep it disabled while configuring the gateway, then enable it only after provider and email checks are ready.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Anti-Spam Coverage', 'alynt-account-gateway' ),
				'status'  => $provider_count > 0 ? 'ready' : 'action',
				'message' => $provider_count > 0
					? __( 'At least one anti-spam provider is fully configured for registration verification.', 'alynt-account-gateway' )
					: __( 'No fully configured anti-spam provider is available. Configure Turnstile or Reoon before public registration receives traffic.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Consent Links', 'alynt-account-gateway' ),
				'status'  => $has_terms && $has_privacy ? 'ready' : 'action',
				'message' => $has_terms && $has_privacy
					? __( 'Terms and privacy links are configured for the registration consent checkbox.', 'alynt-account-gateway' )
					: __( 'Configure both Terms and Privacy relative URL paths before public registration.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Flagged Email Policy', 'alynt-account-gateway' ),
				'status'  => $has_reoon && 'block' === $flagged_policy ? 'ready' : 'warning',
				'message' => $has_reoon
					? $this->security_reoon_flagged_policy_message( $flagged_policy )
					: __( 'Reoon is not configured, so flagged email policy decisions are inactive. Use Turnstile alone or add Reoon before relying on email-quality review.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Launch Evidence', 'alynt-account-gateway' ),
				'status'  => $diagnostics_enabled ? 'ready' : 'warning',
				'message' => $diagnostics_enabled
					? __( 'Diagnostics are enabled, so launch and support signals can be collected during registration rollout.', 'alynt-account-gateway' )
					: __( 'Diagnostics are disabled. Enable them temporarily during launch or support windows if you need fuller evidence for redirects, emails, and webhook outcomes.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Return security provider status items.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return array<int,array{label:string,status:string,message:string}>
	 */
	private function security_provider_status_items( $settings ) {
		$has_turnstile_site   = ! empty( $settings['turnstile_site_key'] );
		$has_turnstile_secret = ! empty( $settings['turnstile_secret_key'] );
		$has_turnstile        = $has_turnstile_site && $has_turnstile_secret;
		$has_reoon            = ! empty( $settings['reoon_api_key'] );

		$turnstile_status  = $has_turnstile ? 'ready' : 'action';
		$turnstile_message = __( 'Turnstile is not configured. Add both keys or use Reoon before enabling public registration.', 'alynt-account-gateway' );

		if ( $has_turnstile ) {
			$turnstile_message = __( 'Server-side verification can run when the registration form sends a Turnstile token.', 'alynt-account-gateway' );
		} elseif ( $has_turnstile_site || $has_turnstile_secret ) {
			$turnstile_status  = 'warning';
			$turnstile_message = __( 'Turnstile is partially configured. Add both the site key and secret key before relying on it.', 'alynt-account-gateway' );
		}

		$mode                 = ! empty( $settings['protection_mode'] ) ? sanitize_key( $settings['protection_mode'] ) : 'turnstile_or_reoon';
		$reoon_flagged_policy = ! empty( $settings['reoon_flagged_policy'] ) ? sanitize_key( $settings['reoon_flagged_policy'] ) : 'allow';

		return array(
			array(
				'label'   => __( 'Protection Mode', 'alynt-account-gateway' ),
				'status'  => $this->security_configured_provider_count( $settings ) > 0 ? 'ready' : 'warning',
				'message' => $this->security_protection_mode_message( $mode ),
			),
			array(
				'label'   => __( 'Turnstile', 'alynt-account-gateway' ),
				'status'  => $turnstile_status,
				'message' => $turnstile_message,
			),
			array(
				'label'   => __( 'Reoon Email Verifier', 'alynt-account-gateway' ),
				'status'  => $has_reoon ? 'ready' : 'action',
				'message' => $has_reoon
					? __( 'Email quality verification can run using the configured Reoon API key.', 'alynt-account-gateway' )
					: __( 'Reoon is not configured. Add an API key or use Turnstile before enabling public registration.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Reoon Blocked Statuses', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'message' => __( 'Always blocks invalid, disabled, disposable, and spamtrap statuses.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Reoon Flagged Statuses', 'alynt-account-gateway' ),
				'status'  => 'block' === $reoon_flagged_policy ? 'ready' : 'warning',
				'message' => $this->security_reoon_flagged_policy_message( $reoon_flagged_policy ),
			),
		);
	}

	/**
	 * Render Reoon policy guidance for flagged email-quality statuses.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return void
	 */
	private function render_security_reoon_policy_guide( $settings ) {
		$policy       = ! empty( $settings['reoon_flagged_policy'] ) ? sanitize_key( $settings['reoon_flagged_policy'] ) : 'allow';
		$policy_label = 'block' === $policy
			? __( 'Block flagged statuses', 'alynt-account-gateway' )
			: __( 'Allow and log flagged statuses', 'alynt-account-gateway' );
		$policy_items = $this->security_reoon_policy_visibility_items( $policy );
		?>
		<div class="alynt-ag-reoon-policy-guide">
			<div>
				<h3><?php esc_html_e( 'Reoon Flagged Status Guidance', 'alynt-account-gateway' ); ?></h3>
				<p>
					<?php
					echo esc_html(
						sprintf(
							/* translators: %s: selected Reoon flagged-status policy label. */
							__( 'Current policy: %s.', 'alynt-account-gateway' ),
							$policy_label
						)
					);
					?>
				</p>
			</div>
			<table class="widefat striped alynt-ag-reoon-policy-guide__table">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Reoon Result Group', 'alynt-account-gateway' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Statuses', 'alynt-account-gateway' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Registration Treatment', 'alynt-account-gateway' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $policy_items as $item ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html( $item['group'] ); ?></th>
							<td><?php echo esc_html( $item['statuses'] ); ?></td>
							<td><?php echo esc_html( $item['treatment'] ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<div class="alynt-ag-reoon-policy-guide__grid">
				<section>
					<h4><?php esc_html_e( 'Recommended default', 'alynt-account-gateway' ); ?></h4>
					<p><?php esc_html_e( 'For most stores, allow and log flagged statuses first. Catch-all domains, role accounts, unknown results, and full inboxes can include legitimate customers, so reviewing activity before blocking reduces false positives.', 'alynt-account-gateway' ); ?></p>
				</section>
				<section>
					<h4><?php esc_html_e( 'When to block', 'alynt-account-gateway' ); ?></h4>
					<p><?php esc_html_e( 'Switch to blocking when support volume, spam pressure, or fraud risk matters more than occasional manual recovery for legitimate customers.', 'alynt-account-gateway' ); ?></p>
				</section>
				<section>
					<h4><?php esc_html_e( 'Where to review', 'alynt-account-gateway' ); ?></h4>
					<p><?php esc_html_e( 'Use Recent Registration Verification Activity below to review allowed flagged results and blocked Reoon decisions with masked email addresses.', 'alynt-account-gateway' ); ?></p>
				</section>
			</div>
		</div>
		<?php
	}

	/**
	 * Return Reoon policy visibility rows for the Security tab.
	 *
	 * @param string $policy Reoon flagged-status policy.
	 * @return array<int,array{group:string,statuses:string,treatment:string}>
	 */
	private function security_reoon_policy_visibility_items( $policy ) {
		$flagged_treatment = 'block' === $policy
			? __( 'Blocked before account creation.', 'alynt-account-gateway' )
			: __( 'Allowed, logged, and shown for admin review.', 'alynt-account-gateway' );

		return array(
			array(
				'group'     => __( 'Always blocked', 'alynt-account-gateway' ),
				'statuses'  => __( 'invalid, disabled, disposable, spamtrap', 'alynt-account-gateway' ),
				'treatment' => __( 'Blocked before account creation.', 'alynt-account-gateway' ),
			),
			array(
				'group'     => __( 'Configurable flagged statuses', 'alynt-account-gateway' ),
				'statuses'  => __( 'catch_all, role_account, unknown, inbox_full', 'alynt-account-gateway' ),
				'treatment' => $flagged_treatment,
			),
		);
	}

	/**
	 * Return the human-readable Reoon flagged-status policy message.
	 *
	 * @param string $policy Reoon flagged-status policy.
	 * @return string
	 */
	private function security_reoon_flagged_policy_message( $policy ) {
		if ( 'block' === $policy ) {
			return __( 'Blocks catch-all, role account, unknown, and inbox-full statuses before account creation.', 'alynt-account-gateway' );
		}

		return __( 'Allows but logs catch-all, role account, unknown, and inbox-full statuses for admin review.', 'alynt-account-gateway' );
	}

	/**
	 * Return configured security provider count.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return int
	 */
	private function security_configured_provider_count( $settings ) {
		$count = 0;

		if ( ! empty( $settings['turnstile_site_key'] ) && ! empty( $settings['turnstile_secret_key'] ) ) {
			++$count;
		}

		if ( ! empty( $settings['reoon_api_key'] ) ) {
			++$count;
		}

		return $count;
	}

	/**
	 * Return the human-readable protection mode message.
	 *
	 * @param string $mode Protection mode.
	 * @return string
	 */
	private function security_protection_mode_message( $mode ) {
		if ( 'turnstile_and_reoon' === $mode ) {
			return __( 'Every configured provider must pass registration. Configure both Turnstile and Reoon when the site needs two independent checks.', 'alynt-account-gateway' );
		}

		return __( 'Either configured provider can pass registration. This is the recommended default for most sites.', 'alynt-account-gateway' );
	}

	/**
	 * Return security rate-limit status items.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return array<int,array{label:string,status:string,message:string}>
	 */
	private function security_rate_limit_items( $settings ) {
		return array(
			array(
				'label'   => __( 'Registration Attempts', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'message' => $this->security_rate_limit_message( $settings, 'registration_rate_limit_count', 'registration_rate_limit_window' ),
			),
			array(
				'label'   => __( 'Confirmation Resend Attempts', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'message' => $this->security_rate_limit_message( $settings, 'resend_confirmation_rate_limit_count', 'resend_confirmation_rate_limit_window' ),
			),
			array(
				'label'   => __( 'Login Attempts', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'message' => $this->security_rate_limit_message( $settings, 'login_rate_limit_count', 'login_rate_limit_window' ),
			),
			array(
				'label'   => __( 'Password Reset Attempts', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'message' => $this->security_rate_limit_message( $settings, 'lostpassword_rate_limit_count', 'lostpassword_rate_limit_window' ),
			),
		);
	}

	/**
	 * Return a rate-limit message.
	 *
	 * @param array<string,mixed> $settings   Current settings.
	 * @param string              $count_key  Count setting key.
	 * @param string              $window_key Window setting key.
	 * @return string
	 */
	private function security_rate_limit_message( $settings, $count_key, $window_key ) {
		$count  = isset( $settings[ $count_key ] ) ? max( 1, absint( $settings[ $count_key ] ) ) : 1;
		$window = isset( $settings[ $window_key ] ) ? max( 1, absint( $settings[ $window_key ] ) ) : 1;

		return sprintf(
			/* translators: 1: attempt count, 2: window length in minutes. */
			__( 'Limit: %1$d attempts in a %2$d-minute window.', 'alynt-account-gateway' ),
			$count,
			$window
		);
	}

	/**
	 * Render recent registration verification activity.
	 *
	 * @return void
	 */
	private function render_security_verification_activity() {
		$logs              = $this->security_recent_verification_logs( 10 );
		$diagnostic_events = $this->security_recent_diagnostics_events( 25 );
		$external_events   = $this->security_recent_external_diagnostics_events( 25 );
		$webhook_logs      = $this->recent_webhook_logs();
		$settings          = ALYNT_AG_Settings_Schema::get_settings();
		?>
		<div class="alynt-ag-security-activity">
			<h3><?php esc_html_e( 'Recent Registration Verification Activity', 'alynt-account-gateway' ); ?></h3>
			<p class="description">
				<?php esc_html_e( 'Shows recent Turnstile and Reoon outcomes stored in the plugin verification log. Email addresses are masked in this admin view.', 'alynt-account-gateway' ); ?>
			</p>

			<?php $this->render_security_provider_health_signals( $logs ); ?>
			<?php $this->render_security_manual_review_queue( $logs ); ?>
			<?php $this->render_security_provider_failure_triage( $logs ); ?>
			<?php $this->render_security_rate_limit_pressure( $logs ); ?>
			<?php $this->render_security_registration_abuse_signals( $logs ); ?>
			<?php $this->render_security_diagnostics_dependency_notice( $settings ); ?>
			<?php $this->render_security_access_control_signals( $logs, $diagnostic_events ); ?>
			<?php $this->render_security_auth_redirect_signals( $diagnostic_events ); ?>
			<?php $this->render_security_branded_auth_signals( $diagnostic_events ); ?>
			<?php $this->render_security_registration_flow_signals( $logs ); ?>
			<?php $this->render_security_delivery_signals( $external_events, $webhook_logs ); ?>

			<?php if ( empty( $logs ) ) : ?>
				<p class="alynt-ag-security-status__notice">
					<?php esc_html_e( 'No verification activity has been logged yet.', 'alynt-account-gateway' ); ?>
				</p>
			<?php else : ?>
				<table class="widefat striped alynt-ag-security-activity__table">
					<thead>
						<tr>
							<th scope="col"><?php esc_html_e( 'Email', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Provider', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Outcome', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Decision', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Guidance', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Next Step', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Logged At', 'alynt-account-gateway' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $logs as $log ) : ?>
							<tr>
								<td><?php echo esc_html( $this->mask_email_for_display( $log->email ?? '' ) ); ?></td>
								<td><?php echo esc_html( $this->security_provider_label( $log->provider ?? '' ) ); ?></td>
								<td><code><?php echo esc_html( $log->status ?? '' ); ?></code></td>
								<td>
									<span class="alynt-ag-security-decision alynt-ag-security-decision--<?php echo ! empty( $log->blocked ) ? 'blocked' : 'passed'; ?>">
										<?php echo ! empty( $log->blocked ) ? esc_html__( 'Blocked', 'alynt-account-gateway' ) : esc_html__( 'Passed', 'alynt-account-gateway' ); ?>
									</span>
								</td>
								<td class="alynt-ag-security-guidance"><?php echo esc_html( $this->security_verification_guidance( $log ) ); ?></td>
								<td class="alynt-ag-security-next-step"><?php echo esc_html( $this->security_verification_next_step( $log ) ); ?></td>
								<td><?php echo esc_html( $log->created_at ?? '' ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render notice for security signals that depend on diagnostics logs.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_security_diagnostics_dependency_notice( $settings ) {
		if ( ! empty( $settings['diagnostics_enabled'] ) ) {
			return;
		}

		?>
		<div class="alynt-ag-security-diagnostics-note" role="note">
			<strong><?php esc_html_e( 'Diagnostics are disabled.', 'alynt-account-gateway' ); ?></strong>
			<?php esc_html_e( 'Access control, gateway routing, welcome-email failure, and webhook-dispatch signals only show complete evidence while diagnostics are enabled in Advanced Tools.', 'alynt-account-gateway' ); ?>
		</div>
		<?php
	}

	/**
	 * Render registration abuse summary from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return void
	 */
	private function render_security_registration_abuse_signals( $logs ) {
		$items = $this->security_registration_abuse_signal_items( $logs );
		?>
		<div class="alynt-ag-security-abuse" aria-label="<?php esc_attr_e( 'Recent registration abuse signals', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Registration Abuse Signals', 'alynt-account-gateway' ); ?></h4>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $items as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h5><?php echo esc_html( $item['label'] ); ?></h5>
						<p>
							<strong><?php echo esc_html( (string) $item['count'] ); ?></strong>
							<?php echo esc_html( $item['message'] ); ?>
						</p>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Return registration abuse signal items from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	private function security_registration_abuse_signal_items( $logs ) {
		$registration_limits = $this->count_security_logs_by_provider_statuses(
			$logs,
			'rate_limit',
			array( 'registration_rate_limited' )
		);
		$resend_limits       = $this->count_security_logs_by_provider_statuses(
			$logs,
			'rate_limit',
			array( 'resend_confirmation_rate_limited' )
		);
		$flagged_blocks      = $this->count_security_logs_by_provider_statuses(
			$logs,
			'reoon',
			array( 'alynt_ag_reoon_blocked' ),
			array( '_flagged_blocked' )
		);
		$setup_friction      = $this->count_security_logs_by_provider_statuses(
			$logs,
			'registration_flow',
			array( 'password_mismatch', 'alynt_ag_password_length', 'alynt_ag_password_complexity', 'email_unavailable' )
		);

		return array(
			array(
				'label'   => __( 'Registration Rate Limits', 'alynt-account-gateway' ),
				'status'  => $registration_limits > 0 ? 'warning' : 'ready',
				'count'   => $registration_limits,
				'message' => __( 'recent registration attempts blocked before verification. Watch for bursts from the same campaign or customer support reports.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Resend Rate Limits', 'alynt-account-gateway' ),
				'status'  => $resend_limits > 0 ? 'warning' : 'ready',
				'count'   => $resend_limits,
				'message' => __( 'recent confirmation resend attempts blocked by throttling. Repeated blocks can indicate inbox delivery delays or automated retries.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Flagged Email Blocks', 'alynt-account-gateway' ),
				'status'  => $flagged_blocks > 0 ? 'warning' : 'ready',
				'count'   => $flagged_blocks,
				'message' => __( 'recent Reoon policy blocks for low-quality or flagged addresses. Review if legitimate business domains appear in support tickets.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Setup Friction Blocks', 'alynt-account-gateway' ),
				'status'  => $setup_friction > 0 ? 'warning' : 'ready',
				'count'   => $setup_friction,
				'message' => __( 'recent password or email-availability blocks during account setup. Improve form guidance if legitimate customers abandon setup here.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Render access-control summary from recent verification and diagnostics logs.
	 *
	 * @param array<int,object> $logs              Recent verification logs.
	 * @param array<int,object> $diagnostic_events Recent diagnostics events.
	 * @return void
	 */
	private function render_security_access_control_signals( $logs, $diagnostic_events ) {
		$items = $this->security_access_control_signal_items( $logs, $diagnostic_events );
		?>
		<div class="alynt-ag-security-access" aria-label="<?php esc_attr_e( 'Recent access control signals', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Access Control Signals', 'alynt-account-gateway' ); ?></h4>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $items as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h5><?php echo esc_html( $item['label'] ); ?></h5>
						<p>
							<strong><?php echo esc_html( (string) $item['count'] ); ?></strong>
							<?php echo esc_html( $item['message'] ); ?>
						</p>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Return access-control signal items from recent verification and diagnostics logs.
	 *
	 * @param array<int,object> $logs              Recent verification logs.
	 * @param array<int,object> $diagnostic_events Recent diagnostics events.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	private function security_access_control_signal_items( $logs, $diagnostic_events ) {
		$login_lockouts          = $this->count_security_logs_by_provider_statuses(
			$logs,
			'rate_limit',
			array( 'login_rate_limited' )
		);
		$password_reset_lockouts = $this->count_security_logs_by_provider_statuses(
			$logs,
			'rate_limit',
			array( 'lostpassword_rate_limited' )
		);
		$admin_blocks            = $this->count_diagnostics_events_by_code( $diagnostic_events, 'wp_admin_access_blocked' );
		$admin_block_detail      = $this->latest_wp_admin_block_detail( $diagnostic_events );
		$admin_block_message     = __( 'recent wp-admin redirects recorded by diagnostics. Repeated blocks can mean customers are following admin links or a role rule needs review.', 'alynt-account-gateway' );
		if ( '' !== $admin_block_detail ) {
			$admin_block_message .= ' ' . $admin_block_detail;
		}

		return array(
			array(
				'label'   => __( 'Login Lockouts', 'alynt-account-gateway' ),
				'status'  => $login_lockouts > 0 ? 'warning' : 'ready',
				'count'   => $login_lockouts,
				'message' => __( 'recent login rate-limit blocks. Review for credential stuffing or customers stuck at login.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Password Reset Lockouts', 'alynt-account-gateway' ),
				'status'  => $password_reset_lockouts > 0 ? 'warning' : 'ready',
				'count'   => $password_reset_lockouts,
				'message' => __( 'recent password-reset rate-limit blocks. Watch for repeated reset requests against the same account.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Blocked Admin Access', 'alynt-account-gateway' ),
				'status'  => $admin_blocks > 0 ? 'warning' : 'ready',
				'count'   => $admin_blocks,
				'message' => $admin_block_message,
			),
		);
	}

	/**
	 * Return safe detail from the most recent blocked wp-admin event.
	 *
	 * @param array<int,object> $diagnostic_events Recent diagnostics events.
	 * @return string
	 */
	private function latest_wp_admin_block_detail( $diagnostic_events ) {
		foreach ( $diagnostic_events as $event ) {
			$code = isset( $event->event_code ) ? sanitize_key( $event->event_code ) : '';
			if ( 'wp_admin_access_blocked' !== $code ) {
				continue;
			}

			$context          = $this->diagnostics_event_context( $event );
			$request_path     = isset( $context['request_path'] ) && is_scalar( $context['request_path'] ) ? sanitize_text_field( (string) $context['request_path'] ) : '';
			$destination_path = isset( $context['destination_path'] ) && is_scalar( $context['destination_path'] ) ? sanitize_text_field( (string) $context['destination_path'] ) : '';
			$query_keys       = $this->diagnostics_context_query_keys( $context );

			if ( '' === $request_path && isset( $context['path'] ) && is_scalar( $context['path'] ) ) {
				$request_path = sanitize_text_field( (string) $context['path'] );
			}

			if ( '' === $request_path && '' === $destination_path && empty( $query_keys ) ) {
				return '';
			}

			$detail = array();

			if ( '' !== $request_path && '' !== $destination_path ) {
				$detail[] = sprintf(
					/* translators: 1: blocked request path, 2: redirect destination path. */
					__( 'Latest blocked path: %1$s -> %2$s.', 'alynt-account-gateway' ),
					$request_path,
					$destination_path
				);
			} elseif ( '' !== $request_path ) {
				$detail[] = sprintf(
					/* translators: %s: blocked request path. */
					__( 'Latest blocked path: %s.', 'alynt-account-gateway' ),
					$request_path
				);
			}

			if ( ! empty( $query_keys ) ) {
				$detail[] = sprintf(
					/* translators: %s: comma-separated query argument names. */
					__( 'Query keys: %s.', 'alynt-account-gateway' ),
					implode( ', ', $query_keys )
				);
			}

			return implode( ' ', $detail );
		}

		return '';
	}

	/**
	 * Render auth redirect summary from recent diagnostics logs.
	 *
	 * @param array<int,object> $diagnostic_events Recent diagnostics events.
	 * @return void
	 */
	private function render_security_auth_redirect_signals( $diagnostic_events ) {
		$items = $this->security_auth_redirect_signal_items( $diagnostic_events );
		?>
		<div class="alynt-ag-security-routing" aria-label="<?php esc_attr_e( 'Recent gateway routing signals', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Gateway Routing Signals', 'alynt-account-gateway' ); ?></h4>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $items as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h5><?php echo esc_html( $item['label'] ); ?></h5>
						<p>
							<strong><?php echo esc_html( (string) $item['count'] ); ?></strong>
							<?php echo esc_html( $item['message'] ); ?>
						</p>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Return auth redirect signal items from recent diagnostics logs.
	 *
	 * @param array<int,object> $diagnostic_events Recent diagnostics events.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	private function security_auth_redirect_signal_items( $diagnostic_events ) {
		$native_redirects = $this->count_native_login_redirects_with_preserved_keys( $diagnostic_events );
		$reset_redirects  = $this->count_native_login_redirects_with_preserved_keys( $diagnostic_events, array( 'key', 'login' ) );
		$target_redirects = $this->count_native_login_redirects_with_preserved_keys( $diagnostic_events, array( 'redirect_to' ) );

		return array(
			array(
				'label'   => __( 'Native Login Redirects', 'alynt-account-gateway' ),
				'status'  => $native_redirects > 0 ? 'warning' : 'ready',
				'count'   => $native_redirects,
				'message' => __( 'recent native wp-login.php redirects. If this rises, update menus, emails, and third-party links to use branded account routes.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Reset Link Redirects', 'alynt-account-gateway' ),
				'status'  => $reset_redirects > 0 ? 'warning' : 'ready',
				'count'   => $reset_redirects,
				'message' => __( 'recent reset-link redirects preserved password setup keys. Confirm branded set-password handling stays healthy.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Redirect-To Preserved', 'alynt-account-gateway' ),
				'status'  => $target_redirects > 0 ? 'warning' : 'ready',
				'count'   => $target_redirects,
				'message' => __( 'recent login redirects preserved a destination. Review protected-page links if customers seem bounced through login often.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Render branded authentication summary from recent diagnostics logs.
	 *
	 * @param array<int,object> $diagnostic_events Recent diagnostics events.
	 * @return void
	 */
	private function render_security_branded_auth_signals( $diagnostic_events ) {
		$items = $this->security_branded_auth_signal_items( $diagnostic_events );
		?>
		<div class="alynt-ag-security-auth" aria-label="<?php esc_attr_e( 'Recent branded authentication signals', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Gateway Auth Signals', 'alynt-account-gateway' ); ?></h4>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $items as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h5><?php echo esc_html( $item['label'] ); ?></h5>
						<p>
							<strong><?php echo esc_html( (string) $item['count'] ); ?></strong>
							<?php echo esc_html( $item['message'] ); ?>
						</p>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Return branded authentication signal items from recent diagnostics logs.
	 *
	 * @param array<int,object> $diagnostic_events Recent diagnostics events.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	private function security_branded_auth_signal_items( $diagnostic_events ) {
		$login_failures  = $this->count_diagnostics_events_by_codes(
			$diagnostic_events,
			array( 'branded_login_failed', 'branded_login_rate_limited' )
		);
		$login_successes = $this->count_diagnostics_events_by_code( $diagnostic_events, 'branded_login_succeeded' );
		$reset_requests  = $this->count_diagnostics_events_by_code( $diagnostic_events, 'branded_password_reset_requested' );
		$reset_issues    = $this->count_diagnostics_events_by_codes(
			$diagnostic_events,
			array( 'branded_password_reset_failed', 'branded_password_reset_email_failed', 'branded_password_reset_rate_limited' )
		);
		$reset_completed = $this->count_diagnostics_events_by_code( $diagnostic_events, 'branded_password_reset_completed' );

		return array(
			array(
				'label'   => __( 'Gateway Login Failures', 'alynt-account-gateway' ),
				'status'  => $login_failures > 0 ? 'warning' : 'ready',
				'count'   => $login_failures,
				'message' => __( 'recent branded login failures or rate-limit blocks. Review if customers report login trouble or if the count rises suddenly.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Gateway Login Successes', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'count'   => $login_successes,
				'message' => __( 'recent successful branded login completions recorded by diagnostics.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Password Reset Requests', 'alynt-account-gateway' ),
				'status'  => $reset_requests > 0 ? 'warning' : 'ready',
				'count'   => $reset_requests,
				'message' => __( 'recent neutral branded password-reset requests. Watch for spikes against customer accounts or delivery support reports.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Password Reset Issues', 'alynt-account-gateway' ),
				'status'  => $reset_issues > 0 ? 'action' : 'ready',
				'count'   => $reset_issues,
				'message' => __( 'recent reset completion, email delivery, or rate-limit issues. Check reset email delivery and customer password guidance.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Password Reset Completions', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'count'   => $reset_completed,
				'message' => __( 'recent successful branded password-reset completions recorded by diagnostics.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Render registration flow summary from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return void
	 */
	private function render_security_registration_flow_signals( $logs ) {
		$items = $this->security_registration_flow_signal_items( $logs );
		?>
		<div class="alynt-ag-security-flow" aria-label="<?php esc_attr_e( 'Recent registration flow signals', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Registration Flow Signals', 'alynt-account-gateway' ); ?></h4>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $items as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h5><?php echo esc_html( $item['label'] ); ?></h5>
						<p>
							<strong><?php echo esc_html( (string) $item['count'] ); ?></strong>
							<?php echo esc_html( $item['message'] ); ?>
						</p>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Return registration flow signal items from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	private function security_registration_flow_signal_items( $logs ) {
		$consent_blocks  = $this->count_security_logs_by_provider_statuses(
			$logs,
			'registration_flow',
			array( 'terms_required', 'consent_record_failed' )
		);
		$system_failures = $this->count_security_logs_by_provider_statuses(
			$logs,
			'registration_flow',
			array( 'pending_registration_failed', 'confirmation_email_failed' )
		);
		$password_blocks = $this->count_security_logs_by_provider_statuses(
			$logs,
			'registration_flow',
			array( 'password_mismatch', 'alynt_ag_password_length', 'alynt_ag_password_complexity', 'email_unavailable' )
		);
		$resends         = $this->count_security_logs_by_provider_statuses(
			$logs,
			'registration_flow',
			array( 'confirmation_resent' )
		);

		return array(
			array(
				'label'   => __( 'Consent Blocks', 'alynt-account-gateway' ),
				'status'  => $consent_blocks > 0 ? 'warning' : 'ready',
				'count'   => $consent_blocks,
				'message' => __( 'recent consent-related blocks. Check Terms and Privacy copy if legitimate customers are stopping here.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Registration System Failures', 'alynt-account-gateway' ),
				'status'  => $system_failures > 0 ? 'action' : 'ready',
				'count'   => $system_failures,
				'message' => __( 'recent pending-record or confirmation-email failures. Review database writes and email delivery before public launch.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Password Setup Blocks', 'alynt-account-gateway' ),
				'status'  => $password_blocks > 0 ? 'warning' : 'ready',
				'count'   => $password_blocks,
				'message' => __( 'recent password or email-availability blocks. Review password guidance if customers struggle to complete setup.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Confirmation Resends Sent', 'alynt-account-gateway' ),
				'status'  => $resends > 0 ? 'warning' : 'ready',
				'count'   => $resends,
				'message' => __( 'recent successful resends. Repeated resends can point to delivery delays or unclear confirmation instructions.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Render account delivery summary from recent diagnostics and webhook logs.
	 *
	 * @param array<int,object> $external_events Recent external diagnostics events.
	 * @param array<int,object> $webhook_logs    Recent webhook logs.
	 * @return void
	 */
	private function render_security_delivery_signals( $external_events, $webhook_logs ) {
		$items = $this->security_delivery_signal_items( $external_events, $webhook_logs );
		?>
		<div class="alynt-ag-security-delivery" aria-label="<?php esc_attr_e( 'Recent account delivery signals', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Account Delivery Signals', 'alynt-account-gateway' ); ?></h4>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $items as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h5><?php echo esc_html( $item['label'] ); ?></h5>
						<p>
							<strong><?php echo esc_html( (string) $item['count'] ); ?></strong>
							<?php echo esc_html( $item['message'] ); ?>
						</p>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Return account delivery signal items from recent diagnostics and webhook logs.
	 *
	 * @param array<int,object> $external_events Recent external diagnostics events.
	 * @param array<int,object> $webhook_logs    Recent webhook logs.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	private function security_delivery_signal_items( $external_events, $webhook_logs ) {
		$welcome_failures  = $this->count_diagnostics_events_by_code( $external_events, 'account_created_welcome_failed' );
		$webhook_failures  = $this->count_diagnostics_events_by_code( $external_events, 'account_created_webhook_failed' );
		$failed_deliveries = $this->count_failed_webhook_logs( $webhook_logs );

		return array(
			array(
				'label'   => __( 'Welcome Email Failures', 'alynt-account-gateway' ),
				'status'  => $welcome_failures > 0 ? 'action' : 'ready',
				'count'   => $welcome_failures,
				'message' => __( 'recent account-created welcome email failures. Check mail delivery before relying on account onboarding.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Account Webhook Failures', 'alynt-account-gateway' ),
				'status'  => $webhook_failures > 0 ? 'action' : 'ready',
				'count'   => $webhook_failures,
				'message' => __( 'recent account-created webhook dispatch failures. Review endpoint configuration and signing before relying on automation.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Failed Webhook Deliveries', 'alynt-account-gateway' ),
				'status'  => $failed_deliveries > 0 ? 'action' : 'ready',
				'count'   => $failed_deliveries,
				'message' => __( 'recent failed webhook delivery rows. Open the Webhooks tab to review destinations, HTTP status, and error messages.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Render provider health summary from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return void
	 */
	private function render_security_provider_health_signals( $logs ) {
		$items = $this->security_provider_health_signal_items( $logs );
		?>
		<div class="alynt-ag-security-signal" aria-label="<?php esc_attr_e( 'Recent provider health signals', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Provider Health Signals', 'alynt-account-gateway' ); ?></h4>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $items as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h5><?php echo esc_html( $item['label'] ); ?></h5>
						<p>
							<strong><?php echo esc_html( (string) $item['count'] ); ?></strong>
							<?php echo esc_html( $item['message'] ); ?>
						</p>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Return provider health signal items from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	private function security_provider_health_signal_items( $logs ) {
		$turnstile_challenges = $this->count_security_logs_by_provider_statuses(
			$logs,
			'turnstile',
			array( 'alynt_ag_turnstile_failed' )
		);
		$turnstile_failures   = $this->count_security_logs_by_provider_statuses(
			$logs,
			'turnstile',
			array( 'alynt_ag_turnstile_missing', 'alynt_ag_turnstile_request_failed' )
		);
		$reoon_blocks         = $this->count_security_logs_by_provider_statuses(
			$logs,
			'reoon',
			array( 'alynt_ag_reoon_blocked' ),
			array( '_flagged_blocked' )
		);
		$reoon_failures       = $this->count_security_logs_by_provider_statuses(
			$logs,
			'reoon',
			array( 'alynt_ag_reoon_missing', 'alynt_ag_reoon_request_failed', 'alynt_ag_reoon_invalid_response' )
		);

		return array(
			array(
				'label'   => __( 'Turnstile Challenges', 'alynt-account-gateway' ),
				'status'  => $turnstile_challenges > 0 ? 'warning' : 'ready',
				'count'   => $turnstile_challenges,
				'message' => __( 'recent challenge rejections. Confirm the site key matches the secret key and watch for bot traffic if this rises.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Turnstile Connectivity', 'alynt-account-gateway' ),
				'status'  => $turnstile_failures > 0 ? 'action' : 'ready',
				'count'   => $turnstile_failures,
				'message' => __( 'recent configuration or network failures. Check both Turnstile keys and outbound HTTP connectivity.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Reoon Email Blocks', 'alynt-account-gateway' ),
				'status'  => $reoon_blocks > 0 ? 'warning' : 'ready',
				'count'   => $reoon_blocks,
				'message' => __( 'recent email-quality blocks. Review the policy if legitimate customers are affected.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Reoon Provider Failures', 'alynt-account-gateway' ),
				'status'  => $reoon_failures > 0 ? 'action' : 'ready',
				'count'   => $reoon_failures,
				'message' => __( 'recent configuration, connectivity, or response failures. Test the API key and outbound HTTP connectivity.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Render manual-review queue guidance from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return void
	 */
	private function render_security_manual_review_queue( $logs ) {
		$items = $this->security_manual_review_queue_items( $logs );
		?>
		<div class="alynt-ag-security-manual-review" aria-label="<?php esc_attr_e( 'Manual review queue', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Manual Review Queue', 'alynt-account-gateway' ); ?></h4>
			<p class="description"><?php esc_html_e( 'Highlights Reoon flagged results that were allowed by policy so support teams can review legitimate-risk signups without changing the public registration flow.', 'alynt-account-gateway' ); ?></p>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $items as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h5><?php echo esc_html( $item['label'] ); ?></h5>
						<p>
							<strong><?php echo esc_html( (string) $item['count'] ); ?></strong>
							<?php echo esc_html( $item['message'] ); ?>
						</p>
					</section>
				<?php endforeach; ?>
			</div>
			<?php $this->render_security_manual_review_decision_playbook(); ?>
		</div>
		<?php
	}

	/**
	 * Render manual-review decision guidance.
	 *
	 * @return void
	 */
	private function render_security_manual_review_decision_playbook() {
		$items = $this->security_manual_review_decision_items();
		?>
		<div class="alynt-ag-security-manual-review__playbook">
			<h5><?php esc_html_e( 'Manual Review Decision Playbook', 'alynt-account-gateway' ); ?></h5>
			<p class="description"><?php esc_html_e( 'Use this as a support-friendly rubric before changing the site-wide Reoon flagged-status policy.', 'alynt-account-gateway' ); ?></p>
			<table class="widefat striped alynt-ag-security-manual-review__table">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Result Family', 'alynt-account-gateway' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Default Decision', 'alynt-account-gateway' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Tighten When', 'alynt-account-gateway' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Review First', 'alynt-account-gateway' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $items as $item ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html( $item['result_family'] ); ?></th>
							<td><?php echo esc_html( $item['default_decision'] ); ?></td>
							<td><?php echo esc_html( $item['tighten_when'] ); ?></td>
							<td><?php echo esc_html( $item['review_first'] ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Return manual-review decision guidance rows.
	 *
	 * @return array<int,array{result_family:string,default_decision:string,tighten_when:string,review_first:string}>
	 */
	private function security_manual_review_decision_items() {
		return array(
			array(
				'result_family'    => __( 'Role account', 'alynt-account-gateway' ),
				'default_decision' => __( 'Allow and review when shared inboxes are acceptable for the site.', 'alynt-account-gateway' ),
				'tighten_when'     => __( 'Block when personal accountability, subscriptions, or fraud exposure matter more than shared access.', 'alynt-account-gateway' ),
				'review_first'     => __( 'Check whether customers commonly use support, info, billing, or team inboxes.', 'alynt-account-gateway' ),
			),
			array(
				'result_family'    => __( 'Catch-all domain', 'alynt-account-gateway' ),
				'default_decision' => __( 'Allow and monitor until repeated abuse appears from the same domain.', 'alynt-account-gateway' ),
				'tighten_when'     => __( 'Block or manually review when one domain creates repeated low-quality registrations.', 'alynt-account-gateway' ),
				'review_first'     => __( 'Compare masked activity rows with support tickets and order history before tightening.', 'alynt-account-gateway' ),
			),
			array(
				'result_family'    => __( 'Unknown or inbox full', 'alynt-account-gateway' ),
				'default_decision' => __( 'Allow and retry support contact if the customer later needs help.', 'alynt-account-gateway' ),
				'tighten_when'     => __( 'Block when failed delivery, fake-account pressure, or manual support burden rises.', 'alynt-account-gateway' ),
				'review_first'     => __( 'Confirm email delivery health and whether the address belongs to a real customer record.', 'alynt-account-gateway' ),
			),
			array(
				'result_family'    => __( 'Disposable, spamtrap, invalid, or disabled', 'alynt-account-gateway' ),
				'default_decision' => __( 'Keep blocked; these are always treated as high-risk or unusable.', 'alynt-account-gateway' ),
				'tighten_when'     => __( 'No extra tightening is needed because these statuses are already blocked.', 'alynt-account-gateway' ),
				'review_first'     => __( 'Review only when a known customer reports a false positive.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Return manual-review queue items from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	private function security_manual_review_queue_items( $logs ) {
		$allowed_flagged = $this->count_reoon_review_logs( $logs, array(), array( '_flagged' ), false );
		$role_accounts   = $this->count_reoon_review_logs( $logs, array( 'role_account_flagged' ), array(), false );
		$risky_domains   = $this->count_reoon_review_logs( $logs, array( 'catch_all_flagged', 'unknown_flagged', 'inbox_full_flagged' ), array(), false );
		$blocked_flagged = $this->count_reoon_review_logs( $logs, array(), array( '_flagged_blocked' ), true );

		return array(
			array(
				'label'   => __( 'Allowed Flagged Results', 'alynt-account-gateway' ),
				'status'  => $allowed_flagged > 0 ? 'warning' : 'ready',
				'count'   => $allowed_flagged,
				'message' => __( 'recent Reoon flagged results allowed by policy. Review the masked rows below before deciding whether to block flagged statuses.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Role Account Reviews', 'alynt-account-gateway' ),
				'status'  => $role_accounts > 0 ? 'warning' : 'ready',
				'count'   => $role_accounts,
				'message' => __( 'recent role-account emails allowed for review. Confirm whether shared inboxes are acceptable for this site.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Catch-All And Unknown Reviews', 'alynt-account-gateway' ),
				'status'  => $risky_domains > 0 ? 'warning' : 'ready',
				'count'   => $risky_domains,
				'message' => __( 'recent catch-all, unknown, or inbox-full results allowed for review. Watch for repeated domains before tightening policy.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Blocked Flagged Results', 'alynt-account-gateway' ),
				'status'  => $blocked_flagged > 0 ? 'warning' : 'ready',
				'count'   => $blocked_flagged,
				'message' => __( 'recent Reoon flagged results blocked by strict policy. Check support tickets for legitimate customers who may need help.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Count Reoon logs that should appear in manual-review summaries.
	 *
	 * @param array<int,object> $logs            Recent verification logs.
	 * @param array<int,string> $statuses        Exact status keys.
	 * @param array<int,string> $status_suffixes Status suffixes.
	 * @param bool|null         $blocked         Required blocked state, or null for any state.
	 * @return int
	 */
	private function count_reoon_review_logs( $logs, $statuses, $status_suffixes, $blocked = null ) {
		$count           = 0;
		$statuses        = array_map( 'sanitize_key', $statuses );
		$status_suffixes = array_map( 'sanitize_key', $status_suffixes );

		foreach ( $logs as $log ) {
			$provider = isset( $log->provider ) ? sanitize_key( $log->provider ) : '';
			$status   = isset( $log->status ) ? sanitize_key( $log->status ) : '';

			if ( 'reoon' !== $provider || '' === $status ) {
				continue;
			}

			$log_blocked = ! empty( $log->blocked );
			if ( null !== $blocked && $log_blocked !== (bool) $blocked ) {
				continue;
			}

			if ( in_array( $status, $statuses, true ) ) {
				++$count;
				continue;
			}

			foreach ( $status_suffixes as $suffix ) {
				if ( $this->status_has_suffix( $status, $suffix ) ) {
					++$count;
					break;
				}
			}
		}

		return $count;
	}

	/**
	 * Render provider failure triage guidance from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return void
	 */
	private function render_security_provider_failure_triage( $logs ) {
		$items = $this->security_provider_failure_triage_items( $logs );
		?>
		<div class="alynt-ag-security-triage" aria-label="<?php esc_attr_e( 'Provider failure triage', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Provider Failure Triage', 'alynt-account-gateway' ); ?></h4>
			<p class="description"><?php esc_html_e( 'Use these focused checks when provider errors appear. They separate configuration gaps from connectivity problems and policy decisions.', 'alynt-account-gateway' ); ?></p>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $items as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h5><?php echo esc_html( $item['label'] ); ?></h5>
						<p>
							<strong><?php echo esc_html( (string) $item['count'] ); ?></strong>
							<?php echo esc_html( $item['message'] ); ?>
						</p>
						<?php if ( ! empty( $item['latest'] ) ) : ?>
							<p class="description alynt-ag-security-card__meta">
								<?php
								printf(
									/* translators: %s: latest provider failure timestamp. */
									esc_html__( 'Latest seen: %s.', 'alynt-account-gateway' ),
									esc_html( $item['latest'] )
								);
								?>
							</p>
						<?php endif; ?>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Return provider failure triage items from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return array<int,array{label:string,status:string,count:int,message:string,latest:string}>
	 */
	private function security_provider_failure_triage_items( $logs ) {
		$turnstile_missing         = $this->count_security_logs_by_provider_statuses(
			$logs,
			'turnstile',
			array( 'alynt_ag_turnstile_missing' )
		);
		$turnstile_network         = $this->count_security_logs_by_provider_statuses(
			$logs,
			'turnstile',
			array( 'alynt_ag_turnstile_request_failed' )
		);
		$turnstile_rejected        = $this->count_security_logs_by_provider_statuses(
			$logs,
			'turnstile',
			array( 'alynt_ag_turnstile_failed' )
		);
		$turnstile_missing_latest  = $this->latest_security_log_time_by_provider_statuses(
			$logs,
			'turnstile',
			array( 'alynt_ag_turnstile_missing' )
		);
		$turnstile_network_latest  = $this->latest_security_log_time_by_provider_statuses(
			$logs,
			'turnstile',
			array( 'alynt_ag_turnstile_request_failed' )
		);
		$turnstile_rejected_latest = $this->latest_security_log_time_by_provider_statuses(
			$logs,
			'turnstile',
			array( 'alynt_ag_turnstile_failed' )
		);
		$reoon_missing             = $this->count_security_logs_by_provider_statuses(
			$logs,
			'reoon',
			array( 'alynt_ag_reoon_missing' )
		);
		$reoon_network             = $this->count_security_logs_by_provider_statuses(
			$logs,
			'reoon',
			array( 'alynt_ag_reoon_request_failed' )
		);
		$reoon_unexpected          = $this->count_security_logs_by_provider_statuses(
			$logs,
			'reoon',
			array( 'alynt_ag_reoon_invalid_response' )
		);
		$reoon_missing_latest      = $this->latest_security_log_time_by_provider_statuses(
			$logs,
			'reoon',
			array( 'alynt_ag_reoon_missing' )
		);
		$reoon_network_latest      = $this->latest_security_log_time_by_provider_statuses(
			$logs,
			'reoon',
			array( 'alynt_ag_reoon_request_failed' )
		);
		$reoon_unexpected_latest   = $this->latest_security_log_time_by_provider_statuses(
			$logs,
			'reoon',
			array( 'alynt_ag_reoon_invalid_response' )
		);

		return array(
			array(
				'label'   => __( 'Turnstile Configuration', 'alynt-account-gateway' ),
				'status'  => $turnstile_missing > 0 ? 'action' : 'ready',
				'count'   => $turnstile_missing,
				'message' => __( 'recent missing-token or key configuration failures. Confirm both keys are saved and belong to the same Cloudflare Turnstile site.', 'alynt-account-gateway' ),
				'latest'  => $turnstile_missing_latest,
			),
			array(
				'label'   => __( 'Turnstile Connectivity', 'alynt-account-gateway' ),
				'status'  => $turnstile_network > 0 ? 'action' : 'ready',
				'count'   => $turnstile_network,
				'message' => __( 'recent Cloudflare Siteverify connection failures. Check outbound HTTP, DNS, firewall rules, and the saved secret key.', 'alynt-account-gateway' ),
				'latest'  => $turnstile_network_latest,
			),
			array(
				'label'   => __( 'Turnstile Challenge Rejections', 'alynt-account-gateway' ),
				'status'  => $turnstile_rejected > 0 ? 'warning' : 'ready',
				'count'   => $turnstile_rejected,
				'message' => __( 'recent rejected challenges. Confirm the registration domain is allowed in Cloudflare and compare with bot traffic before changing policy.', 'alynt-account-gateway' ),
				'latest'  => $turnstile_rejected_latest,
			),
			array(
				'label'   => __( 'Reoon Configuration', 'alynt-account-gateway' ),
				'status'  => $reoon_missing > 0 ? 'action' : 'ready',
				'count'   => $reoon_missing,
				'message' => __( 'recent missing API-key failures. Confirm the Reoon key is saved before registration relies on email verification.', 'alynt-account-gateway' ),
				'latest'  => $reoon_missing_latest,
			),
			array(
				'label'   => __( 'Reoon Connectivity', 'alynt-account-gateway' ),
				'status'  => $reoon_network > 0 ? 'action' : 'ready',
				'count'   => $reoon_network,
				'message' => __( 'recent Reoon API connection failures. Check outbound HTTP, DNS, provider availability, and key permissions.', 'alynt-account-gateway' ),
				'latest'  => $reoon_network_latest,
			),
			array(
				'label'   => __( 'Reoon Unexpected Responses', 'alynt-account-gateway' ),
				'status'  => $reoon_unexpected > 0 ? 'action' : 'ready',
				'count'   => $reoon_unexpected,
				'message' => __( 'recent malformed or unexpected Reoon responses. Test the key in Reoon and review provider availability before enabling stricter blocking.', 'alynt-account-gateway' ),
				'latest'  => $reoon_unexpected_latest,
			),
		);
	}

	/**
	 * Return the latest matching security log timestamp.
	 *
	 * @param array<int,object> $logs            Recent verification logs.
	 * @param string            $provider        Provider key.
	 * @param array<int,string> $statuses        Exact status keys.
	 * @param array<int,string> $status_suffixes Status suffixes.
	 * @return string
	 */
	private function latest_security_log_time_by_provider_statuses( $logs, $provider, $statuses, $status_suffixes = array() ) {
		$latest          = 0;
		$provider        = sanitize_key( $provider );
		$statuses        = array_map( 'sanitize_key', $statuses );
		$status_suffixes = array_map( 'sanitize_key', $status_suffixes );

		foreach ( $logs as $log ) {
			$log_provider = isset( $log->provider ) ? sanitize_key( $log->provider ) : '';
			$status       = isset( $log->status ) ? sanitize_key( $log->status ) : '';

			if ( $provider !== $log_provider || '' === $status || empty( $log->created_at ) ) {
				continue;
			}

			if ( ! in_array( $status, $statuses, true ) ) {
				$matches_suffix = false;
				foreach ( $status_suffixes as $suffix ) {
					if ( $this->status_has_suffix( $status, $suffix ) ) {
						$matches_suffix = true;
						break;
					}
				}

				if ( ! $matches_suffix ) {
					continue;
				}
			}

			$timestamp = strtotime( (string) $log->created_at );
			if ( $timestamp && $timestamp > $latest ) {
				$latest = $timestamp;
			}
		}

		if ( ! $latest ) {
			return '';
		}

		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );

		return date_i18n( $date_format . ' ' . $time_format, $latest, true );
	}

	/**
	 * Count matching security log rows.
	 *
	 * @param array<int,object> $logs            Recent verification logs.
	 * @param string            $provider        Provider key.
	 * @param array<int,string> $statuses        Exact status keys.
	 * @param array<int,string> $status_suffixes Status suffixes.
	 * @return int
	 */
	private function count_security_logs_by_provider_statuses( $logs, $provider, $statuses, $status_suffixes = array() ) {
		$count           = 0;
		$provider        = sanitize_key( $provider );
		$statuses        = array_map( 'sanitize_key', $statuses );
		$status_suffixes = array_map( 'sanitize_key', $status_suffixes );

		foreach ( $logs as $log ) {
			$log_provider = isset( $log->provider ) ? sanitize_key( $log->provider ) : '';
			$status       = isset( $log->status ) ? sanitize_key( $log->status ) : '';

			if ( $provider !== $log_provider || '' === $status ) {
				continue;
			}

			if ( in_array( $status, $statuses, true ) ) {
				++$count;
				continue;
			}

			foreach ( $status_suffixes as $suffix ) {
				if ( $this->status_has_suffix( $status, $suffix ) ) {
					++$count;
					break;
				}
			}
		}

		return $count;
	}

	/**
	 * Count matching diagnostics event rows.
	 *
	 * @param array<int,object> $events     Recent diagnostics events.
	 * @param string            $event_code Event code.
	 * @return int
	 */
	private function count_diagnostics_events_by_code( $events, $event_code ) {
		$count      = 0;
		$event_code = sanitize_key( $event_code );

		foreach ( $events as $event ) {
			$code = isset( $event->event_code ) ? sanitize_key( $event->event_code ) : '';

			if ( $event_code === $code ) {
				++$count;
			}
		}

		return $count;
	}

	/**
	 * Count matching diagnostics event rows across multiple event codes.
	 *
	 * @param array<int,object> $events      Recent diagnostics events.
	 * @param array<int,string> $event_codes Event codes.
	 * @return int
	 */
	private function count_diagnostics_events_by_codes( $events, $event_codes ) {
		$count       = 0;
		$event_codes = array_values( array_filter( array_map( 'sanitize_key', $event_codes ) ) );

		foreach ( $events as $event ) {
			$code = isset( $event->event_code ) ? sanitize_key( $event->event_code ) : '';

			if ( in_array( $code, $event_codes, true ) ) {
				++$count;
			}
		}

		return $count;
	}

	/**
	 * Count failed webhook log rows.
	 *
	 * @param array<int,object> $logs Recent webhook logs.
	 * @return int
	 */
	private function count_failed_webhook_logs( $logs ) {
		$count = 0;

		foreach ( $logs as $log ) {
			if ( empty( $log->success ) ) {
				++$count;
			}
		}

		return $count;
	}

	/**
	 * Count native login redirects by preserved query keys.
	 *
	 * @param array<int,object> $events        Recent diagnostics events.
	 * @param array<int,string> $required_keys Required preserved query keys.
	 * @return int
	 */
	private function count_native_login_redirects_with_preserved_keys( $events, $required_keys = array() ) {
		$count         = 0;
		$required_keys = array_values( array_filter( array_map( 'sanitize_key', $required_keys ) ) );

		foreach ( $events as $event ) {
			$code = isset( $event->event_code ) ? sanitize_key( $event->event_code ) : '';

			if ( 'native_login_redirected' !== $code ) {
				continue;
			}

			if ( empty( $required_keys ) || $this->diagnostics_event_has_preserved_query_keys( $event, $required_keys ) ) {
				++$count;
			}
		}

		return $count;
	}

	/**
	 * Determine whether a diagnostics event preserved all requested query keys.
	 *
	 * @param object            $event         Diagnostics event row.
	 * @param array<int,string> $required_keys Required preserved query keys.
	 * @return bool
	 */
	private function diagnostics_event_has_preserved_query_keys( $event, $required_keys ) {
		$context        = $this->diagnostics_event_context( $event );
		$preserved_keys = array();

		if ( isset( $context['preserved_query_keys'] ) && is_array( $context['preserved_query_keys'] ) ) {
			foreach ( $context['preserved_query_keys'] as $key ) {
				if ( is_scalar( $key ) ) {
					$preserved_keys[] = sanitize_key( (string) $key );
				}
			}
		}

		foreach ( $required_keys as $required_key ) {
			if ( ! in_array( sanitize_key( $required_key ), $preserved_keys, true ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Return a decoded diagnostics event context.
	 *
	 * @param object $event Diagnostics event row.
	 * @return array<string,mixed>
	 */
	private function diagnostics_event_context( $event ) {
		if ( ! isset( $event->context ) ) {
			return array();
		}

		if ( is_array( $event->context ) ) {
			return $event->context;
		}

		if ( ! is_string( $event->context ) || '' === $event->context ) {
			return array();
		}

		$context = json_decode( $event->context, true );

		return is_array( $context ) ? $context : array();
	}

	/**
	 * Return sanitized query keys from diagnostics context.
	 *
	 * @param array<string,mixed> $context Diagnostics context.
	 * @return array<int,string>
	 */
	private function diagnostics_context_query_keys( $context ) {
		$keys = array();

		if ( ! isset( $context['request_query_keys'] ) || ! is_array( $context['request_query_keys'] ) ) {
			return $keys;
		}

		foreach ( $context['request_query_keys'] as $key ) {
			if ( is_scalar( $key ) ) {
				$keys[] = sanitize_key( (string) $key );
			}
		}

		return array_values( array_filter( array_unique( $keys ) ) );
	}

	/**
	 * Render rate-limit pressure summary from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return void
	 */
	private function render_security_rate_limit_pressure( $logs ) {
		$items          = $this->security_rate_limit_pressure_items( $logs );
		$active_buckets = $this->security_active_rate_limit_bucket_items();
		?>
		<div class="alynt-ag-security-pressure" aria-label="<?php esc_attr_e( 'Recent rate limit pressure', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Rate Limit Pressure', 'alynt-account-gateway' ); ?></h4>
			<p class="description">
				<?php esc_html_e( 'Recent blocks come from verification logs. Active buckets show privacy-preserving lockout pressure that is still inside the configured rate-limit window.', 'alynt-account-gateway' ); ?>
			</p>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $items as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h5><?php echo esc_html( $item['label'] ); ?></h5>
						<p>
							<strong><?php echo esc_html( (string) $item['count'] ); ?></strong>
							<?php echo esc_html( $item['message'] ); ?>
						</p>
					</section>
				<?php endforeach; ?>
			</div>
			<h5><?php esc_html_e( 'Active Rate Limit Buckets', 'alynt-account-gateway' ); ?></h5>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $active_buckets as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h6><?php echo esc_html( $item['label'] ); ?></h6>
						<p>
							<strong><?php echo esc_html( (string) $item['count'] ); ?></strong>
							<?php echo esc_html( $item['message'] ); ?>
						</p>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Return rate-limit pressure summary items from recent verification logs.
	 *
	 * @param array<int,object> $logs Recent verification logs.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	private function security_rate_limit_pressure_items( $logs ) {
		$counts = array(
			'registration_rate_limited'        => 0,
			'resend_confirmation_rate_limited' => 0,
			'login_rate_limited'               => 0,
			'lostpassword_rate_limited'        => 0,
		);

		foreach ( $logs as $log ) {
			$provider = isset( $log->provider ) ? sanitize_key( $log->provider ) : '';
			$status   = isset( $log->status ) ? sanitize_key( $log->status ) : '';

			if ( 'rate_limit' === $provider && array_key_exists( $status, $counts ) ) {
				++$counts[ $status ];
			}
		}

		return array(
			array(
				'label'   => __( 'Registration', 'alynt-account-gateway' ),
				'status'  => $counts['registration_rate_limited'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['registration_rate_limited'],
				'message' => __( 'recent registration blocks. Review the limit if legitimate customers are affected.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Confirmation Resends', 'alynt-account-gateway' ),
				'status'  => $counts['resend_confirmation_rate_limited'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['resend_confirmation_rate_limited'],
				'message' => __( 'recent resend blocks. Repeated resends can indicate confused customers or automated retries.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Login', 'alynt-account-gateway' ),
				'status'  => $counts['login_rate_limited'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['login_rate_limited'],
				'message' => __( 'recent login blocks. Repeated login blocks can indicate credential stuffing or customers stuck at login.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Password Reset', 'alynt-account-gateway' ),
				'status'  => $counts['lostpassword_rate_limited'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['lostpassword_rate_limited'],
				'message' => __( 'recent password-reset blocks. Check for repeated reset requests against the same account.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Return active rate-limit bucket summary items.
	 *
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	private function security_active_rate_limit_bucket_items() {
		$counts = array(
			'registration'        => array(
				'active' => 0,
				'locked' => 0,
			),
			'resend_confirmation' => array(
				'active' => 0,
				'locked' => 0,
			),
			'login'               => array(
				'active' => 0,
				'locked' => 0,
			),
			'lostpassword'        => array(
				'active' => 0,
				'locked' => 0,
			),
		);

		foreach ( $this->security_active_rate_limit_bucket_rows() as $row ) {
			$meta = isset( $row->option_value ) ? maybe_unserialize( $row->option_value ) : null;

			if ( ! is_array( $meta ) || empty( $meta['action'] ) ) {
				continue;
			}

			$action = sanitize_key( $meta['action'] );
			if ( ! isset( $counts[ $action ] ) ) {
				continue;
			}

			if ( ! empty( $meta['expires_at'] ) && absint( $meta['expires_at'] ) < time() ) {
				continue;
			}

			++$counts[ $action ]['active'];
			if ( ! empty( $meta['locked'] ) ) {
				++$counts[ $action ]['locked'];
			}
		}

		return array(
			array(
				'label'   => __( 'Registration', 'alynt-account-gateway' ),
				'status'  => $counts['registration']['locked'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['registration']['locked'],
				'message' => sprintf(
					/* translators: %d: active bucket count. */
					__( 'active lockouts from %d current registration buckets.', 'alynt-account-gateway' ),
					$counts['registration']['active']
				),
			),
			array(
				'label'   => __( 'Confirmation Resends', 'alynt-account-gateway' ),
				'status'  => $counts['resend_confirmation']['locked'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['resend_confirmation']['locked'],
				'message' => sprintf(
					/* translators: %d: active bucket count. */
					__( 'active lockouts from %d current resend buckets.', 'alynt-account-gateway' ),
					$counts['resend_confirmation']['active']
				),
			),
			array(
				'label'   => __( 'Login', 'alynt-account-gateway' ),
				'status'  => $counts['login']['locked'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['login']['locked'],
				'message' => sprintf(
					/* translators: %d: active bucket count. */
					__( 'active lockouts from %d current login buckets.', 'alynt-account-gateway' ),
					$counts['login']['active']
				),
			),
			array(
				'label'   => __( 'Password Reset', 'alynt-account-gateway' ),
				'status'  => $counts['lostpassword']['locked'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['lostpassword']['locked'],
				'message' => sprintf(
					/* translators: %d: active bucket count. */
					__( 'active lockouts from %d current password-reset buckets.', 'alynt-account-gateway' ),
					$counts['lostpassword']['active']
				),
			),
		);
	}

	/**
	 * Fetch active rate-limit metadata transient rows.
	 *
	 * @return array<int,object>
	 */
	private function security_active_rate_limit_bucket_rows() {
		global $wpdb;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Admin-only aggregate observability for plugin-owned transient rows.
		return (array) $wpdb->get_results(
			$wpdb->prepare(
				"SELECT option_value FROM {$wpdb->options} WHERE option_name LIKE %s",
				$wpdb->esc_like( '_transient_alynt_ag_rl_meta_' ) . '%'
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	}

	/**
	 * Render recent pending registration activity.
	 *
	 * @return void
	 */
	private function render_security_pending_registrations() {
		$registrations = $this->security_recent_pending_registrations( 10 );
		?>
		<div class="alynt-ag-security-activity">
			<h3><?php esc_html_e( 'Recent Pending Registrations', 'alynt-account-gateway' ); ?></h3>
			<p class="description">
				<?php esc_html_e( 'Shows recent email-confirmation registration records stored by the plugin. Email addresses are masked in this admin view.', 'alynt-account-gateway' ); ?>
			</p>

			<?php $this->render_security_pending_registration_lifecycle_signals( $registrations ); ?>

			<?php if ( empty( $registrations ) ) : ?>
				<p class="alynt-ag-security-status__notice">
					<?php esc_html_e( 'No pending registration records have been created yet.', 'alynt-account-gateway' ); ?>
				</p>
			<?php else : ?>
				<table class="widefat striped alynt-ag-security-activity__table">
					<thead>
						<tr>
							<th scope="col"><?php esc_html_e( 'Email', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Status', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'User ID', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Created At', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Confirmed At', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Expires At', 'alynt-account-gateway' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Next Step', 'alynt-account-gateway' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $registrations as $registration ) : ?>
							<?php $status = $this->security_pending_registration_status( $registration ); ?>
							<tr>
								<td><?php echo esc_html( $this->mask_email_for_display( $registration->email ?? '' ) ); ?></td>
								<td>
									<span class="alynt-ag-security-registration-status alynt-ag-security-registration-status--<?php echo esc_attr( $status['key'] ); ?>">
										<?php echo esc_html( $status['label'] ); ?>
									</span>
								</td>
								<td><?php echo ! empty( $registration->user_id ) ? esc_html( (string) absint( $registration->user_id ) ) : esc_html__( 'None', 'alynt-account-gateway' ); ?></td>
								<td><?php echo esc_html( $registration->created_at ?? '' ); ?></td>
								<td><?php echo esc_html( $registration->confirmed_at ?? '' ); ?></td>
								<td><?php echo esc_html( $registration->expires_at ?? '' ); ?></td>
								<td class="alynt-ag-security-guidance"><?php echo esc_html( $this->security_pending_registration_guidance( $status['key'] ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render pending registration lifecycle summary.
	 *
	 * @param array<int,object> $registrations Recent pending registration rows.
	 * @return void
	 */
	private function render_security_pending_registration_lifecycle_signals( $registrations ) {
		$items = $this->security_pending_registration_lifecycle_signal_items( $registrations );
		?>
		<div class="alynt-ag-security-lifecycle" aria-label="<?php esc_attr_e( 'Recent pending registration lifecycle signals', 'alynt-account-gateway' ); ?>">
			<h4><?php esc_html_e( 'Pending Registration Lifecycle Signals', 'alynt-account-gateway' ); ?></h4>
			<div class="alynt-ag-security-status__grid">
				<?php foreach ( $items as $item ) : ?>
					<section class="alynt-ag-security-card alynt-ag-security-card--<?php echo esc_attr( $item['status'] ); ?>">
						<span class="alynt-ag-security-card__badge"><?php echo esc_html( $this->readiness_status_label( $item['status'] ) ); ?></span>
						<h5><?php echo esc_html( $item['label'] ); ?></h5>
						<p>
							<strong><?php echo esc_html( (string) $item['count'] ); ?></strong>
							<?php echo esc_html( $item['message'] ); ?>
						</p>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Return pending registration lifecycle signal items.
	 *
	 * @param array<int,object> $registrations Recent pending registration rows.
	 * @return array<int,array{label:string,status:string,count:int,message:string}>
	 */
	private function security_pending_registration_lifecycle_signal_items( $registrations ) {
		$counts = array(
			'pending'         => 0,
			'email-confirmed' => 0,
			'expired'         => 0,
			'completed'       => 0,
		);

		foreach ( $registrations as $registration ) {
			$status = $this->security_pending_registration_status( $registration );
			$key    = isset( $status['key'] ) ? sanitize_key( $status['key'] ) : 'pending';

			if ( isset( $counts[ $key ] ) ) {
				++$counts[ $key ];
			}
		}

		return array(
			array(
				'label'   => __( 'Waiting For Confirmation', 'alynt-account-gateway' ),
				'status'  => $counts['pending'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['pending'],
				'message' => __( 'recent pending records still waiting for email confirmation. Watch resend activity and inbox-delivery support requests.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Confirmed, Not Completed', 'alynt-account-gateway' ),
				'status'  => $counts['email-confirmed'] > 0 ? 'warning' : 'ready',
				'count'   => $counts['email-confirmed'],
				'message' => __( 'recent records where email is confirmed but password setup is unfinished. Customers may need clearer next-step copy.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Expired Pending Records', 'alynt-account-gateway' ),
				'status'  => $counts['expired'] > 0 ? 'action' : 'ready',
				'count'   => $counts['expired'],
				'message' => __( 'recent pending records past their confirmation window. High counts can indicate missed emails or confusing confirmation instructions.', 'alynt-account-gateway' ),
			),
			array(
				'label'   => __( 'Completed Pending Records', 'alynt-account-gateway' ),
				'status'  => 'ready',
				'count'   => $counts['completed'],
				'message' => __( 'recent pending records that reached account creation. This helps compare completed registrations against stalled ones.', 'alynt-account-gateway' ),
			),
		);
	}

	/**
	 * Return recent registration verification logs.
	 *
	 * @param int $limit Maximum records.
	 * @return array<int,object>
	 */
	private function security_recent_verification_logs( $limit = 10 ) {
		global $wpdb;

		$tables = ALYNT_AG_Database::tables();
		$limit  = min( 25, max( 1, absint( $limit ) ) );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Admin security viewer reads plugin-owned verification log table.
		$logs = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT email, provider, status, blocked, created_at FROM {$tables['verification_logs']} ORDER BY created_at DESC, id DESC LIMIT %d",
				$limit
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		return is_array( $logs ) ? $logs : array();
	}

	/**
	 * Return recent security diagnostics events.
	 *
	 * @param int $limit Maximum records.
	 * @return array<int,object>
	 */
	private function security_recent_diagnostics_events( $limit = 25 ) {
		global $wpdb;

		$tables = ALYNT_AG_Database::tables();
		$limit  = min( 50, max( 1, absint( $limit ) ) );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Admin security viewer reads plugin-owned diagnostics log table.
		$events = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT event_code, context, created_at FROM {$tables['diagnostics_logs']} WHERE category = %s ORDER BY created_at DESC, id DESC LIMIT %d",
				'security',
				$limit
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		return is_array( $events ) ? $events : array();
	}

	/**
	 * Return recent external diagnostics events.
	 *
	 * @param int $limit Maximum records.
	 * @return array<int,object>
	 */
	private function security_recent_external_diagnostics_events( $limit = 25 ) {
		global $wpdb;

		$tables = ALYNT_AG_Database::tables();
		$limit  = min( 50, max( 1, absint( $limit ) ) );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Admin security viewer reads plugin-owned diagnostics log table.
		$events = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT event_code, context, created_at FROM {$tables['diagnostics_logs']} WHERE category = %s ORDER BY created_at DESC, id DESC LIMIT %d",
				'external_api',
				$limit
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		return is_array( $events ) ? $events : array();
	}

	/**
	 * Return admin guidance for a verification log row.
	 *
	 * @param object $log Verification log row.
	 * @return string
	 */
	private function security_verification_guidance( $log ) {
		$provider = isset( $log->provider ) ? sanitize_key( $log->provider ) : '';
		$status   = isset( $log->status ) ? sanitize_key( $log->status ) : '';
		$blocked  = ! empty( $log->blocked );

		if ( 'rate_limit' === $provider ) {
			if ( 'registration_rate_limited' === $status ) {
				return __( 'Registration attempt was blocked by the rate limit.', 'alynt-account-gateway' );
			}

			if ( 'resend_confirmation_rate_limited' === $status ) {
				return __( 'Confirmation resend was blocked by the rate limit. Ask the customer to wait for the configured resend window before trying again.', 'alynt-account-gateway' );
			}

			if ( 'login_rate_limited' === $status ) {
				return __( 'Login attempt was blocked by the rate limit.', 'alynt-account-gateway' );
			}

			if ( 'lostpassword_rate_limited' === $status ) {
				return __( 'Password reset request was blocked by the rate limit.', 'alynt-account-gateway' );
			}

			return __( 'Account action was blocked by a rate limit.', 'alynt-account-gateway' );
		}

		if ( 'reoon' === $provider ) {
			if ( $this->status_has_suffix( $status, '_flagged_blocked' ) ) {
				return __( 'Reoon blocked this flagged email because the flagged-status policy is set to block.', 'alynt-account-gateway' );
			}

			if ( $this->status_has_suffix( $status, '_flagged' ) ) {
				return __( 'Reoon allowed this email, but the status should be reviewed.', 'alynt-account-gateway' );
			}

			if ( 'alynt_ag_reoon_blocked' === $status ) {
				return __( 'Reoon blocked this email by policy.', 'alynt-account-gateway' );
			}

			if ( 'alynt_ag_reoon_missing' === $status ) {
				return __( 'Reoon was not configured when verification ran. Confirm the API key before enabling public registration.', 'alynt-account-gateway' );
			}

			if ( 'alynt_ag_reoon_request_failed' === $status ) {
				return __( 'Reoon could not be reached. Check outbound HTTP connectivity, API availability, and the saved API key.', 'alynt-account-gateway' );
			}

			if ( 'alynt_ag_reoon_invalid_response' === $status ) {
				return __( 'Reoon returned an unexpected response. Review provider availability and test the saved API key.', 'alynt-account-gateway' );
			}

			return $blocked
				? __( 'Reoon blocked this registration.', 'alynt-account-gateway' )
				: __( 'Reoon accepted this email.', 'alynt-account-gateway' );
		}

		if ( 'turnstile' === $provider ) {
			if ( 'alynt_ag_turnstile_failed' === $status ) {
				return __( 'Turnstile rejected the challenge response. Ask the customer to retry and confirm the site key matches the secret key.', 'alynt-account-gateway' );
			}

			if ( 'alynt_ag_turnstile_missing' === $status ) {
				return __( 'Turnstile was not configured when verification ran. Confirm both the site key and secret key before launch.', 'alynt-account-gateway' );
			}

			if ( 'alynt_ag_turnstile_request_failed' === $status ) {
				return __( 'Turnstile verification could not reach Cloudflare. Check outbound HTTP connectivity and the saved secret key.', 'alynt-account-gateway' );
			}

			return $blocked
				? __( 'Turnstile blocked this registration.', 'alynt-account-gateway' )
				: __( 'Turnstile challenge passed.', 'alynt-account-gateway' );
		}

		if ( 'registration_flow' === $provider ) {
			if ( 'terms_required' === $status ) {
				return __( 'Registration was blocked because terms and privacy consent was not accepted.', 'alynt-account-gateway' );
			}

			if ( 'pending_registration_failed' === $status ) {
				return __( 'The pending registration record could not be stored.', 'alynt-account-gateway' );
			}

			if ( 'consent_record_failed' === $status ) {
				return __( 'Registration consent evidence could not be stored.', 'alynt-account-gateway' );
			}

			if ( 'confirmation_email_failed' === $status ) {
				return __( 'The registration confirmation email could not be sent.', 'alynt-account-gateway' );
			}

			if ( 'confirmation_resent' === $status ) {
				return __( 'A fresh confirmation email was sent for an existing pending registration.', 'alynt-account-gateway' );
			}

			if ( 'password_mismatch' === $status ) {
				return __( 'Account creation was blocked because the password confirmation did not match.', 'alynt-account-gateway' );
			}

			if ( in_array( $status, array( 'alynt_ag_password_length', 'alynt_ag_password_complexity' ), true ) ) {
				return __( 'Account creation was blocked because the password did not meet the strength rules.', 'alynt-account-gateway' );
			}

			if ( 'email_unavailable' === $status ) {
				return __( 'Account creation was blocked because the email address became unavailable.', 'alynt-account-gateway' );
			}

			return $blocked
				? __( 'Registration flow blocked this account action.', 'alynt-account-gateway' )
				: __( 'Registration flow recorded this account action.', 'alynt-account-gateway' );
		}

		if ( $this->status_has_suffix( $status, '_flagged' ) ) {
			return __( 'Verification passed, but the status should be reviewed.', 'alynt-account-gateway' );
		}

		return $blocked
			? __( 'Verification blocked this registration.', 'alynt-account-gateway' )
			: __( 'Verification passed.', 'alynt-account-gateway' );
	}

	/**
	 * Return the recommended next step for a verification log row.
	 *
	 * @param object $log Verification log row.
	 * @return string
	 */
	private function security_verification_next_step( $log ) {
		$provider = isset( $log->provider ) ? sanitize_key( $log->provider ) : '';
		$status   = isset( $log->status ) ? sanitize_key( $log->status ) : '';
		$blocked  = ! empty( $log->blocked );

		if ( 'rate_limit' === $provider ) {
			if ( 'resend_confirmation_rate_limited' === $status ) {
				return __( 'Ask the customer to wait for the resend window; check email delivery if resend blocks repeat.', 'alynt-account-gateway' );
			}

			if ( 'login_rate_limited' === $status ) {
				return __( 'Review login lockout pressure before changing limits or support guidance.', 'alynt-account-gateway' );
			}

			if ( 'lostpassword_rate_limited' === $status ) {
				return __( 'Review reset-request pressure and delivery reports before changing limits.', 'alynt-account-gateway' );
			}

			return __( 'Review active rate-limit buckets and support reports before loosening limits.', 'alynt-account-gateway' );
		}

		if ( 'reoon' === $provider ) {
			if ( $this->status_has_suffix( $status, '_flagged_blocked' ) ) {
				return __( 'Check support tickets for false positives before keeping strict flagged-status blocking.', 'alynt-account-gateway' );
			}

			if ( $this->status_has_suffix( $status, '_flagged' ) ) {
				return __( 'Review masked email and domain patterns before changing the flagged-status policy.', 'alynt-account-gateway' );
			}

			if ( 'alynt_ag_reoon_missing' === $status || 'alynt_ag_reoon_request_failed' === $status || 'alynt_ag_reoon_invalid_response' === $status ) {
				return __( 'Test the Reoon API key and outbound HTTP path before relying on email verification.', 'alynt-account-gateway' );
			}

			return $blocked
				? __( 'Review support reports before manually recovering a blocked registrant.', 'alynt-account-gateway' )
				: __( 'No action needed unless this status pattern changes.', 'alynt-account-gateway' );
		}

		if ( 'turnstile' === $provider ) {
			if ( 'alynt_ag_turnstile_failed' === $status ) {
				return __( 'Confirm domain and key pairing, then watch for bot traffic if challenge failures rise.', 'alynt-account-gateway' );
			}

			if ( 'alynt_ag_turnstile_missing' === $status || 'alynt_ag_turnstile_request_failed' === $status ) {
				return __( 'Confirm Turnstile keys and outbound HTTP connectivity before public launch.', 'alynt-account-gateway' );
			}

			return $blocked
				? __( 'Review challenge failures before changing Turnstile settings.', 'alynt-account-gateway' )
				: __( 'No action needed unless challenge failures rise.', 'alynt-account-gateway' );
		}

		if ( 'registration_flow' === $provider ) {
			if ( 'terms_required' === $status ) {
				return __( 'Review Terms and Privacy copy if consent blocks repeat.', 'alynt-account-gateway' );
			}

			if ( in_array( $status, array( 'pending_registration_failed', 'consent_record_failed', 'confirmation_email_failed' ), true ) ) {
				return __( 'Check database writes and email delivery before public launch.', 'alynt-account-gateway' );
			}

			if ( 'confirmation_resent' === $status ) {
				return __( 'Watch resend volume and confirmation-email instructions for customer confusion.', 'alynt-account-gateway' );
			}

			if ( in_array( $status, array( 'password_mismatch', 'alynt_ag_password_length', 'alynt_ag_password_complexity' ), true ) ) {
				return __( 'Review password guidance if account setup blocks repeat.', 'alynt-account-gateway' );
			}

			if ( 'email_unavailable' === $status ) {
				return __( 'No action needed unless email-unavailable blocks repeat.', 'alynt-account-gateway' );
			}

			return $blocked
				? __( 'Review the related registration setting or support reports.', 'alynt-account-gateway' )
				: __( 'No action needed unless this registration-flow pattern rises.', 'alynt-account-gateway' );
		}

		return $blocked
			? __( 'Review this blocked verification before changing policy.', 'alynt-account-gateway' )
			: __( 'No action needed unless this verification pattern changes.', 'alynt-account-gateway' );
	}

	/**
	 * Return whether a status string ends with a suffix.
	 *
	 * @param string $status Status string.
	 * @param string $suffix Suffix to test.
	 * @return bool
	 */
	private function status_has_suffix( $status, $suffix ) {
		if ( '' === $suffix ) {
			return true;
		}

		return substr( $status, -strlen( $suffix ) ) === $suffix;
	}

	/**
	 * Return recent pending registration records.
	 *
	 * @param int $limit Maximum records.
	 * @return array<int,object>
	 */
	private function security_recent_pending_registrations( $limit = 10 ) {
		global $wpdb;

		$tables = ALYNT_AG_Database::tables();
		$limit  = min( 25, max( 1, absint( $limit ) ) );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Admin security viewer reads plugin-owned pending registration table.
		$registrations = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT email, user_id, status, expires_at, created_at, confirmed_at FROM {$tables['pending_registrations']} ORDER BY created_at DESC, id DESC LIMIT %d",
				$limit
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		return is_array( $registrations ) ? $registrations : array();
	}

	/**
	 * Return a pending registration status descriptor.
	 *
	 * @param object $registration Pending registration row.
	 * @return array{key:string,label:string}
	 */
	private function security_pending_registration_status( $registration ) {
		$status     = isset( $registration->status ) ? sanitize_key( $registration->status ) : 'pending';
		$expires_at = isset( $registration->expires_at ) ? strtotime( (string) $registration->expires_at ) : false;
		$now        = strtotime( current_time( 'mysql' ) );

		if ( in_array( $status, array( 'pending', 'email_confirmed' ), true ) && $expires_at && $now && $expires_at < $now ) {
			return array(
				'key'   => 'expired',
				'label' => __( 'Expired', 'alynt-account-gateway' ),
			);
		}

		if ( 'email_confirmed' === $status ) {
			return array(
				'key'   => 'email-confirmed',
				'label' => __( 'Email Confirmed', 'alynt-account-gateway' ),
			);
		}

		if ( 'completed' === $status ) {
			return array(
				'key'   => 'completed',
				'label' => __( 'Completed', 'alynt-account-gateway' ),
			);
		}

		return array(
			'key'   => 'pending',
			'label' => __( 'Pending', 'alynt-account-gateway' ),
		);
	}

	/**
	 * Return admin guidance for a pending registration status.
	 *
	 * @param string $status_key Pending registration status key.
	 * @return string
	 */
	private function security_pending_registration_guidance( $status_key ) {
		$status_key = sanitize_key( $status_key );

		if ( 'expired' === $status_key ) {
			return __( 'The confirmation window has expired. The customer can request a fresh confirmation email from the invalid-link screen.', 'alynt-account-gateway' );
		}

		if ( 'email-confirmed' === $status_key ) {
			return __( 'Email is confirmed. The customer still needs to set a password before the record expires.', 'alynt-account-gateway' );
		}

		if ( 'completed' === $status_key ) {
			return __( 'Account creation is complete. No resend action is needed.', 'alynt-account-gateway' );
		}

		return __( 'Waiting for email confirmation. Resend requests are throttled by the configured resend-confirmation limit.', 'alynt-account-gateway' );
	}

	/**
	 * Return a masked email for admin table display.
	 *
	 * @param string $email Email address.
	 * @return string
	 */
	private function mask_email_for_display( $email ) {
		$email = sanitize_email( $email );

		if ( ! $email || false === strpos( $email, '@' ) ) {
			return '';
		}

		list( $local, $domain ) = explode( '@', $email, 2 );
		$first                  = '' !== $local ? substr( $local, 0, 1 ) : '*';

		return $first . '***@' . $domain;
	}

	/**
	 * Return a readable provider label.
	 *
	 * @param string $provider Provider key.
	 * @return string
	 */
	private function security_provider_label( $provider ) {
		$provider = sanitize_key( $provider );

		if ( 'turnstile' === $provider ) {
			return __( 'Turnstile', 'alynt-account-gateway' );
		}

		if ( 'reoon' === $provider ) {
			return __( 'Reoon Email Verifier', 'alynt-account-gateway' );
		}

		if ( 'rate_limit' === $provider ) {
			return __( 'Rate Limit', 'alynt-account-gateway' );
		}

		if ( 'registration_flow' === $provider ) {
			return __( 'Registration Flow', 'alynt-account-gateway' );
		}

		return $provider;
	}

	/**
	 * Render email preview and test-send tools.
	 *
	 * @return void
	 */
	private function render_email_tools() {
		$email_service = new ALYNT_AG_Email_Template_Service();
		$templates     = $email_service->templates();
		$settings      = ALYNT_AG_Settings_Schema::get_settings();
		?>
		<h2><?php esc_html_e( 'Email Preview And Test Send', 'alynt-account-gateway' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Use the saved template settings with sample account tokens before sending real account emails.', 'alynt-account-gateway' ); ?>
		</p>

		<div class="alynt-ag-email-tools">
			<?php $this->render_email_template_reference( $templates ); ?>
			<?php $this->render_email_token_reference( $email_service ); ?>

			<div class="alynt-ag-email-actions">
				<form method="get" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" target="_blank" class="alynt-ag-inline-tool alynt-ag-email-action">
					<input type="hidden" name="action" value="alynt_ag_preview_email">
					<?php wp_nonce_field( 'alynt_ag_preview_email' ); ?>
					<label for="alynt-ag-email-preview-template"><?php esc_html_e( 'Preview Template', 'alynt-account-gateway' ); ?></label>
					<select id="alynt-ag-email-preview-template" name="template" aria-describedby="alynt-ag-email-preview-help">
						<?php foreach ( $templates as $template_key => $template_label ) : ?>
							<option value="<?php echo esc_attr( $template_key ); ?>"><?php echo esc_html( $template_label ); ?></option>
						<?php endforeach; ?>
					</select>
					<p id="alynt-ag-email-preview-help" class="description">
						<?php esc_html_e( 'Opens the selected email in a new tab using saved settings and sample token values.', 'alynt-account-gateway' ); ?>
					</p>
					<?php submit_button( __( 'Preview Email', 'alynt-account-gateway' ), 'secondary', 'submit', false ); ?>
				</form>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="alynt-ag-inline-tool alynt-ag-email-action">
					<input type="hidden" name="action" value="alynt_ag_test_email">
					<?php wp_nonce_field( 'alynt_ag_test_email' ); ?>
					<label for="alynt-ag-email-test-template"><?php esc_html_e( 'Test Template', 'alynt-account-gateway' ); ?></label>
					<select id="alynt-ag-email-test-template" name="template" aria-describedby="alynt-ag-email-test-help">
						<?php foreach ( $templates as $template_key => $template_label ) : ?>
							<option value="<?php echo esc_attr( $template_key ); ?>"><?php echo esc_html( $template_label ); ?></option>
						<?php endforeach; ?>
					</select>
					<label for="alynt-ag-email-test-recipient"><?php esc_html_e( 'Recipient', 'alynt-account-gateway' ); ?></label>
					<input type="email" id="alynt-ag-email-test-recipient" name="recipient" class="regular-text" value="<?php echo esc_attr( $settings['email_test_recipient'] ); ?>" aria-describedby="alynt-ag-email-test-help" required>
					<p id="alynt-ag-email-test-help" class="description">
						<?php esc_html_e( 'Sends one real test email to this recipient. Editing this field here does not save the default test recipient setting above.', 'alynt-account-gateway' ); ?>
					</p>
					<?php submit_button( __( 'Send Test Email', 'alynt-account-gateway' ), 'secondary', 'submit', false ); ?>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Render email template guidance.
	 *
	 * @param array<string,string> $templates Template labels.
	 * @return void
	 */
	private function render_email_template_reference( $templates ) {
		$reference = $this->email_template_reference();
		?>
		<div class="alynt-ag-email-reference">
			<h3><?php esc_html_e( 'Template Reference', 'alynt-account-gateway' ); ?></h3>
			<p class="description">
				<?php esc_html_e( 'Use these notes when editing subjects, preheaders, and body copy above.', 'alynt-account-gateway' ); ?>
			</p>
			<div class="alynt-ag-email-reference__grid">
				<?php foreach ( $templates as $template_key => $template_label ) : ?>
					<?php $item = $reference[ $template_key ] ?? array(); ?>
					<section class="alynt-ag-email-reference__item">
						<h4><?php echo esc_html( $template_label ); ?></h4>
						<p><?php echo esc_html( $item['description'] ?? '' ); ?></p>
						<?php if ( ! empty( $item['tokens'] ) ) : ?>
							<p class="alynt-ag-email-reference__tokens">
								<strong><?php esc_html_e( 'Action tokens:', 'alynt-account-gateway' ); ?></strong>
								<?php foreach ( $item['tokens'] as $token ) : ?>
									<code>{{<?php echo esc_html( $token ); ?>}}</code>
								<?php endforeach; ?>
							</p>
						<?php else : ?>
							<p class="alynt-ag-email-reference__tokens">
								<strong><?php esc_html_e( 'Action tokens:', 'alynt-account-gateway' ); ?></strong>
								<?php esc_html_e( 'None required.', 'alynt-account-gateway' ); ?>
							</p>
						<?php endif; ?>
					</section>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render email token guidance.
	 *
	 * @param ALYNT_AG_Email_Template_Service $email_service Email service.
	 * @return void
	 */
	private function render_email_token_reference( $email_service ) {
		$tokens         = $email_service->token_reference();
		$preview_tokens = $email_service->preview_tokens();
		?>
		<details class="alynt-ag-email-tokens" open>
			<summary><?php esc_html_e( 'Available Template Tokens', 'alynt-account-gateway' ); ?></summary>
			<p class="description">
				<?php esc_html_e( 'Tokens can be used in email subjects, preheaders, and body fields. Action URL tokens also power branded buttons and the plain-text fallback.', 'alynt-account-gateway' ); ?>
			</p>
			<div class="alynt-ag-email-tokens__grid">
				<?php foreach ( $tokens as $token => $item ) : ?>
					<div class="alynt-ag-email-token">
						<code>{{<?php echo esc_html( $token ); ?>}}</code>
						<strong><?php echo esc_html( $item['label'] ); ?></strong>
						<span><?php echo esc_html( $item['description'] ); ?></span>
						<small>
							<?php
							printf(
								/* translators: %s: sample token value. */
								esc_html__( 'Sample: %s', 'alynt-account-gateway' ),
								esc_html( $preview_tokens[ $token ] ?? '' )
							);
							?>
						</small>
					</div>
				<?php endforeach; ?>
			</div>
			<p class="description">
				<?php esc_html_e( 'Core profile email-change requests may use a plain-text body because WordPress exposes only the message body for that specific email.', 'alynt-account-gateway' ); ?>
			</p>
		</details>
		<?php
	}

	/**
	 * Return email template guidance metadata.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	private function email_template_reference() {
		return array(
			'registration_confirmation' => array(
				'description' => __( 'Sent after the registration form is submitted. The customer must confirm email before setting a password.', 'alynt-account-gateway' ),
				'tokens'      => array( 'confirmation_url', 'expiry_hours' ),
			),
			'password_reset'            => array(
				'description' => __( 'Sent when a customer requests a password reset from the branded gateway or WordPress reset flow.', 'alynt-account-gateway' ),
				'tokens'      => array( 'reset_url' ),
			),
			'password_changed'          => array(
				'description' => __( 'Sent after an account password changes, unless this notification is disabled.', 'alynt-account-gateway' ),
				'tokens'      => array(),
			),
			'new_user_welcome'          => array(
				'description' => __( 'Sent after the confirmed customer account is created, unless the welcome email is disabled.', 'alynt-account-gateway' ),
				'tokens'      => array( 'dashboard_url' ),
			),
			'email_change_confirmation' => array(
				'description' => __( 'Sent when an account email address change requires confirmation, unless this notification is disabled.', 'alynt-account-gateway' ),
				'tokens'      => array( 'change_email_url' ),
			),
		);
	}

	/**
	 * Render webhook test and recent delivery tools.
	 *
	 * @return void
	 */
	private function render_webhook_tools() {
		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$logs     = $this->recent_webhook_logs();
		?>
		<h2><?php esc_html_e( 'Webhook Tools', 'alynt-account-gateway' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Send a sample account-created test event to the saved webhook URL and review recent delivery metadata.', 'alynt-account-gateway' ); ?>
		</p>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="alynt-ag-inline-tool">
			<input type="hidden" name="action" value="alynt_ag_test_webhook">
			<?php wp_nonce_field( 'alynt_ag_test_webhook' ); ?>
			<?php
			submit_button(
				__( 'Send Test Webhook', 'alynt-account-gateway' ),
				'secondary',
				'submit',
				false,
				array( 'disabled' => empty( $settings['account_created_webhook'] ) )
			);
			?>
		</form>

		<?php if ( empty( $settings['account_created_webhook'] ) ) : ?>
			<p class="description"><?php esc_html_e( 'Save an account-created webhook URL to enable test sends.', 'alynt-account-gateway' ); ?></p>
		<?php endif; ?>

		<?php $this->render_webhook_delivery_summary( $logs, $settings ); ?>

		<h3><?php esc_html_e( 'Recent Webhook Deliveries', 'alynt-account-gateway' ); ?></h3>
		<?php if ( empty( $logs ) ) : ?>
			<p><?php esc_html_e( 'No webhook deliveries have been recorded.', 'alynt-account-gateway' ); ?></p>
		<?php else : ?>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Time', 'alynt-account-gateway' ); ?></th>
						<th><?php esc_html_e( 'Event', 'alynt-account-gateway' ); ?></th>
						<th><?php esc_html_e( 'Destination', 'alynt-account-gateway' ); ?></th>
						<th><?php esc_html_e( 'HTTP', 'alynt-account-gateway' ); ?></th>
						<th><?php esc_html_e( 'Result', 'alynt-account-gateway' ); ?></th>
						<th><?php esc_html_e( 'Error', 'alynt-account-gateway' ); ?></th>
						<th><?php esc_html_e( 'Details', 'alynt-account-gateway' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $logs as $log ) : ?>
						<tr>
							<td><?php echo esc_html( $this->format_webhook_log_time( $log->created_at ) ); ?></td>
							<td><?php echo esc_html( $log->event_name ); ?></td>
							<td><?php echo esc_html( $log->destination_host ); ?></td>
							<td><?php echo esc_html( (string) absint( $log->http_status ) ); ?></td>
							<td>
								<?php echo esc_html( $this->webhook_result_label( $log->success ) ); ?>
							</td>
							<td><?php echo esc_html( $log->error_message ); ?></td>
							<td><?php $this->render_webhook_log_details( $log ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render a summary of webhook delivery status and signing setup.
	 *
	 * @param array<int,object>   $logs     Recent webhook logs.
	 * @param array<string,mixed> $settings Current settings.
	 * @return void
	 */
	private function render_webhook_delivery_summary( $logs, $settings ) {
		$latest          = ! empty( $logs ) ? $logs[0] : null;
		$signing_enabled = ! empty( $settings['webhook_signing_secret'] );
		?>
		<div class="notice notice-info inline">
			<p>
				<strong><?php esc_html_e( 'Delivery Status:', 'alynt-account-gateway' ); ?></strong>
				<?php if ( $latest ) : ?>
					<?php
					printf(
						/* translators: 1: result label, 2: event name, 3: destination host, 4: HTTP status, 5: created date. */
						esc_html__( '%1$s for %2$s to %3$s with HTTP %4$s at %5$s.', 'alynt-account-gateway' ),
						esc_html( $this->webhook_result_label( $latest->success ) ),
						esc_html( $latest->event_name ),
						esc_html( $latest->destination_host ),
						esc_html( (string) absint( $latest->http_status ) ),
						esc_html( $this->format_webhook_log_time( $latest->created_at ) )
					);
					?>
				<?php else : ?>
					<?php esc_html_e( 'No deliveries have been logged yet.', 'alynt-account-gateway' ); ?>
				<?php endif; ?>
			</p>
			<p>
				<strong><?php esc_html_e( 'Signing:', 'alynt-account-gateway' ); ?></strong>
				<?php
				echo $signing_enabled
					? esc_html__( 'Enabled. Outgoing webhooks include timestamped HMAC verification headers.', 'alynt-account-gateway' )
					: esc_html__( 'Disabled. Add a webhook signing secret to send verification headers.', 'alynt-account-gateway' );
				?>
			</p>
			<details>
				<summary><?php esc_html_e( 'Signature Verification Reference', 'alynt-account-gateway' ); ?></summary>
				<p><?php esc_html_e( 'Verify the X-Alynt-AG-Signature header by computing HMAC-SHA256 over the timestamp, event name, and exact JSON body.', 'alynt-account-gateway' ); ?></p>
				<p><code><?php echo esc_html( 'sha256=HMAC_SHA256({X-Alynt-AG-Time}.{X-Alynt-AG-Event}.{raw_json_body}, signing_secret)' ); ?></code></p>
				<p><?php esc_html_e( 'Reject requests with an unexpected event name, an old timestamp, or a signature that does not match.', 'alynt-account-gateway' ); ?></p>
			</details>
		</div>
		<?php
	}

	/**
	 * Render expanded webhook log metadata.
	 *
	 * @param object $log Webhook log row.
	 * @return void
	 */
	private function render_webhook_log_details( $log ) {
		?>
		<details>
			<summary><?php esc_html_e( 'View', 'alynt-account-gateway' ); ?></summary>
			<ul>
				<li><strong><?php esc_html_e( 'Event:', 'alynt-account-gateway' ); ?></strong> <?php echo esc_html( $log->event_name ); ?></li>
				<li><strong><?php esc_html_e( 'Destination:', 'alynt-account-gateway' ); ?></strong> <?php echo esc_html( $log->destination_host ); ?></li>
				<li><strong><?php esc_html_e( 'HTTP Status:', 'alynt-account-gateway' ); ?></strong> <?php echo esc_html( (string) absint( $log->http_status ) ); ?></li>
				<li><strong><?php esc_html_e( 'Result:', 'alynt-account-gateway' ); ?></strong> <?php echo esc_html( $this->webhook_result_label( $log->success ) ); ?></li>
				<li><strong><?php esc_html_e( 'Created:', 'alynt-account-gateway' ); ?></strong> <?php echo esc_html( $this->format_webhook_log_time( $log->created_at ) ); ?></li>
				<?php if ( ! empty( $log->error_message ) ) : ?>
					<li><strong><?php esc_html_e( 'Error:', 'alynt-account-gateway' ); ?></strong> <?php echo esc_html( $log->error_message ); ?></li>
				<?php endif; ?>
			</ul>
		</details>
		<?php
	}

	/**
	 * Return a translated webhook result label.
	 *
	 * @param mixed $success Success flag.
	 * @return string
	 */
	private function webhook_result_label( $success ) {
		return (int) $success ? __( 'Success', 'alynt-account-gateway' ) : __( 'Failed', 'alynt-account-gateway' );
	}

	/**
	 * Format a stored UTC webhook log timestamp for display.
	 *
	 * @param string $created_at Stored timestamp.
	 * @return string
	 */
	private function format_webhook_log_time( $created_at ) {
		$timestamp = strtotime( (string) $created_at );

		if ( ! $timestamp ) {
			return (string) $created_at;
		}

		$date_format = (string) get_option( 'date_format', 'Y-m-d' );
		$time_format = (string) get_option( 'time_format', 'H:i' );

		return date_i18n( $date_format . ' ' . $time_format, $timestamp, true );
	}

	/**
	 * Return recent webhook log rows.
	 *
	 * @return array<int,object>
	 */
	private function recent_webhook_logs() {
		global $wpdb;

		$tables = ALYNT_AG_Database::tables();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Admin viewer reads plugin-owned webhook log table.
		return (array) $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, event_name, destination_host, http_status, success, error_message, created_at FROM {$tables['webhook_logs']} ORDER BY created_at DESC, id DESC LIMIT %d",
				10
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Render a WordPress media-library backed image field.
	 *
	 * @param string $id    Field ID.
	 * @param string $name  Field name.
	 * @param int    $value Attachment ID.
	 * @return void
	 */
	private function render_media_field( $id, $name, $value ) {
		$image_url = $value ? wp_get_attachment_image_url( $value, 'medium' ) : '';
		?>
		<div class="alynt-ag-media-field" data-alynt-ag-media-field>
			<input type="hidden" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" data-alynt-ag-media-input>
			<div class="alynt-ag-media-field__preview" data-alynt-ag-media-preview>
				<?php if ( $image_url ) : ?>
					<img src="<?php echo esc_url( $image_url ); ?>" alt="">
				<?php endif; ?>
			</div>
			<p>
				<button type="button" class="button" data-alynt-ag-media-select><?php esc_html_e( 'Select Image', 'alynt-account-gateway' ); ?></button>
				<button type="button" class="button" data-alynt-ag-media-remove <?php disabled( ! $value ); ?>><?php esc_html_e( 'Remove', 'alynt-account-gateway' ); ?></button>
			</p>
		</div>
		<?php
	}

	/**
	 * Render the custom dashboard links editor.
	 *
	 * @param string $id    Field ID.
	 * @param string $name  Field name.
	 * @param mixed  $value Stored dashboard link JSON.
	 * @return void
	 */
	private function render_dashboard_links_field( $id, $name, $value ) {
		$dashboard = new ALYNT_AG_Dashboard_Service();
		$links     = $dashboard->custom_links( $value );
		$icons     = $this->dashboard_link_icon_options();
		$roles     = $this->dashboard_link_role_options();
		?>
		<div class="alynt-ag-dashboard-links" data-alynt-ag-dashboard-links>
			<p class="description">
				<?php esc_html_e( 'Add optional dashboard links. Leave role visibility empty to show a link to every logged-in user.', 'alynt-account-gateway' ); ?>
			</p>
			<div class="alynt-ag-dashboard-links__rows" data-alynt-ag-dashboard-link-rows>
				<?php foreach ( $links as $index => $link ) : ?>
					<?php $this->render_dashboard_link_row( (string) $index, $link, $icons, $roles ); ?>
				<?php endforeach; ?>
			</div>

			<p>
				<button type="button" class="button button-secondary" data-alynt-ag-dashboard-link-add>
					<?php esc_html_e( 'Add Dashboard Link', 'alynt-account-gateway' ); ?>
				</button>
			</p>

			<details class="alynt-ag-dashboard-links__json">
				<summary><?php esc_html_e( 'Raw JSON', 'alynt-account-gateway' ); ?></summary>
				<textarea class="large-text code alynt-ag-textarea" rows="6" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" data-alynt-ag-dashboard-link-json><?php echo esc_textarea( $value ); ?></textarea>
			</details>

			<template data-alynt-ag-dashboard-link-template>
				<?php $this->render_dashboard_link_row( '__index__', array(), $icons, $roles ); ?>
			</template>
		</div>
		<?php
	}

	/**
	 * Render one custom dashboard link row.
	 *
	 * @param string               $index Row index.
	 * @param array<string,mixed>  $link  Link data.
	 * @param array<string,string> $icons  Icon options.
	 * @param array<string,string> $roles  Role options.
	 * @return void
	 */
	private function render_dashboard_link_row( $index, $link, $icons, $roles ) {
		$label  = isset( $link['label'] ) ? (string) $link['label'] : '';
		$url    = isset( $link['url'] ) ? (string) $link['url'] : '';
		$icon   = isset( $link['icon'] ) ? sanitize_key( $link['icon'] ) : 'link';
		$order  = isset( $link['order'] ) ? absint( $link['order'] ) : 100;
		$target = isset( $link['target'] ) ? (string) $link['target'] : '_self';
		$chosen = isset( $link['roles'] ) && is_array( $link['roles'] ) ? array_map( 'sanitize_key', $link['roles'] ) : array();
		?>
		<div class="alynt-ag-dashboard-link-row" data-alynt-ag-dashboard-link-row>
			<div class="alynt-ag-dashboard-link-row__header">
				<strong><?php esc_html_e( 'Dashboard Link', 'alynt-account-gateway' ); ?></strong>
				<button type="button" class="button-link-delete" data-alynt-ag-dashboard-link-remove>
					<?php esc_html_e( 'Remove', 'alynt-account-gateway' ); ?>
				</button>
			</div>
			<div class="alynt-ag-dashboard-link-row__grid">
				<label for="alynt-ag-dashboard-link-<?php echo esc_attr( $index ); ?>-label">
					<?php esc_html_e( 'Label', 'alynt-account-gateway' ); ?>
					<input type="text" id="alynt-ag-dashboard-link-<?php echo esc_attr( $index ); ?>-label" value="<?php echo esc_attr( $label ); ?>" data-alynt-ag-dashboard-link-label>
				</label>
				<label for="alynt-ag-dashboard-link-<?php echo esc_attr( $index ); ?>-url">
					<?php esc_html_e( 'URL', 'alynt-account-gateway' ); ?>
					<input type="text" id="alynt-ag-dashboard-link-<?php echo esc_attr( $index ); ?>-url" value="<?php echo esc_attr( $url ); ?>" placeholder="/support/" data-alynt-ag-dashboard-link-url>
				</label>
				<label for="alynt-ag-dashboard-link-<?php echo esc_attr( $index ); ?>-icon">
					<?php esc_html_e( 'Icon', 'alynt-account-gateway' ); ?>
					<select id="alynt-ag-dashboard-link-<?php echo esc_attr( $index ); ?>-icon" data-alynt-ag-dashboard-link-icon>
						<?php foreach ( $icons as $icon_key => $icon_label ) : ?>
							<option value="<?php echo esc_attr( $icon_key ); ?>" <?php selected( $icon, $icon_key ); ?>><?php echo esc_html( $icon_label ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<label for="alynt-ag-dashboard-link-<?php echo esc_attr( $index ); ?>-order">
					<?php esc_html_e( 'Order', 'alynt-account-gateway' ); ?>
					<input type="number" min="0" class="small-text" id="alynt-ag-dashboard-link-<?php echo esc_attr( $index ); ?>-order" value="<?php echo esc_attr( (string) $order ); ?>" data-alynt-ag-dashboard-link-order>
				</label>
			</div>
			<label class="alynt-ag-dashboard-link-row__toggle">
				<input type="checkbox" value="_blank" <?php checked( '_blank', $target ); ?> data-alynt-ag-dashboard-link-new-tab>
				<?php esc_html_e( 'Open in a new tab', 'alynt-account-gateway' ); ?>
			</label>
			<fieldset class="alynt-ag-dashboard-link-row__roles">
				<legend><?php esc_html_e( 'Role Visibility', 'alynt-account-gateway' ); ?></legend>
				<?php foreach ( $roles as $role_key => $role_label ) : ?>
					<label>
						<input type="checkbox" value="<?php echo esc_attr( $role_key ); ?>" <?php checked( in_array( $role_key, $chosen, true ) ); ?> data-alynt-ag-dashboard-link-role>
						<?php echo esc_html( $role_label ); ?>
					</label>
				<?php endforeach; ?>
			</fieldset>
		</div>
		<?php
	}

	/**
	 * Return dashboard link icon choices.
	 *
	 * @return array<string,string>
	 */
	private function dashboard_link_icon_options() {
		return array(
			'link'      => __( 'Link', 'alynt-account-gateway' ),
			'user'      => __( 'User', 'alynt-account-gateway' ),
			'orders'    => __( 'Orders', 'alynt-account-gateway' ),
			'downloads' => __( 'Downloads', 'alynt-account-gateway' ),
			'address'   => __( 'Address', 'alynt-account-gateway' ),
			'payment'   => __( 'Payment', 'alynt-account-gateway' ),
			'star'      => __( 'Star', 'alynt-account-gateway' ),
			'help'      => __( 'Help', 'alynt-account-gateway' ),
			'logout'    => __( 'Logout', 'alynt-account-gateway' ),
		);
	}

	/**
	 * Return dashboard link role visibility choices.
	 *
	 * @return array<string,string>
	 */
	private function dashboard_link_role_options() {
		if ( function_exists( 'get_editable_roles' ) ) {
			$editable_roles = get_editable_roles();
			$roles          = array();

			foreach ( $editable_roles as $role_key => $role ) {
				$roles[ sanitize_key( $role_key ) ] = translate_user_role( $role['name'] );
			}

			if ( ! empty( $roles ) ) {
				return $roles;
			}
		}

		return array(
			'administrator' => __( 'Administrator', 'alynt-account-gateway' ),
			'shop_manager'  => __( 'Shop Manager', 'alynt-account-gateway' ),
			'customer'      => __( 'Customer', 'alynt-account-gateway' ),
			'subscriber'    => __( 'Subscriber', 'alynt-account-gateway' ),
		);
	}

	/**
	 * Render settings import/export tools.
	 *
	 * @return void
	 */
	private function render_settings_tools() {
		?>
		<h2><?php esc_html_e( 'Settings Import / Export', 'alynt-account-gateway' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Export all plugin-owned settings as JSON, or import a JSON settings package from another site. Imported values are sanitized through the active settings schema.', 'alynt-account-gateway' ); ?>
		</p>
		<div class="notice notice-info inline">
			<p><strong><?php esc_html_e( 'Configuration portability notes', 'alynt-account-gateway' ); ?></strong></p>
			<ul class="ul-disc">
				<li><?php esc_html_e( 'Exports include saved plugin settings only. Media-library files, pending registrations, diagnostics, webhook delivery logs, and WordPress users are not included.', 'alynt-account-gateway' ); ?></li>
				<li><?php esc_html_e( 'Imports validate JSON before saving, keep recognized settings, sanitize each value, and ignore settings that do not belong to the current schema.', 'alynt-account-gateway' ); ?></li>
				<li><?php esc_html_e( 'Use the restore button at the bottom of each tab when you only want to reset that tab instead of replacing the full configuration.', 'alynt-account-gateway' ); ?></li>
			</ul>
		</div>

		<p>
			<a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=alynt_ag_export_settings' ), 'alynt_ag_export_settings' ) ); ?>">
				<?php esc_html_e( 'Export Settings JSON', 'alynt-account-gateway' ); ?>
			</a>
		</p>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" class="alynt-ag-inline-tool">
			<input type="hidden" name="action" value="alynt_ag_import_settings">
			<?php wp_nonce_field( 'alynt_ag_import_settings' ); ?>
			<label for="alynt-ag-settings-import"><?php esc_html_e( 'Settings JSON file', 'alynt-account-gateway' ); ?></label>
			<input type="file" id="alynt-ag-settings-import" name="settings_file" accept="application/json,.json" required>
			<?php submit_button( __( 'Import Settings', 'alynt-account-gateway' ), 'secondary', 'submit', false ); ?>
		</form>
		<?php
	}

	/**
	 * Render gateway preview tools.
	 *
	 * @return void
	 */
	private function render_gateway_preview_tools() {
		$screens = $this->gateway_preview_screens();
		?>
		<h2><?php esc_html_e( 'Gateway Screen Preview', 'alynt-account-gateway' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Preview branded gateway screens using the saved settings, even while frontend output is disabled.', 'alynt-account-gateway' ); ?>
		</p>
		<div class="alynt-ag-preview-links">
			<?php foreach ( $screens as $screen => $label ) : ?>
				<?php
				$preview_url = add_query_arg(
					array(
						'action' => 'alynt_ag_preview_gateway',
						'screen' => $screen,
					),
					admin_url( 'admin-post.php' )
				);
				$preview_url = wp_nonce_url( $preview_url, 'alynt_ag_preview_gateway_' . $screen );
				?>
				<a class="button" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $preview_url ); ?>">
					<?php echo esc_html( $label ); ?>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Render compatibility warning summary.
	 *
	 * @return void
	 */
	private function render_compatibility_warnings() {
		$service  = new ALYNT_AG_Compatibility_Warnings();
		$warnings = $service->warnings();
		?>
		<h2><?php esc_html_e( 'Compatibility Warnings', 'alynt-account-gateway' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'These checks flag active plugins and hooks that may also control login, registration, redirects, account pages, or WooCommerce account endpoints.', 'alynt-account-gateway' ); ?>
		</p>

		<?php if ( empty( $warnings ) ) : ?>
			<div class="notice notice-success inline">
				<p><?php esc_html_e( 'No common account-gateway compatibility overlaps were detected for the current settings.', 'alynt-account-gateway' ); ?></p>
			</div>
			<?php
			return;
		endif;
		?>

		<div class="notice notice-warning inline">
			<p><?php esc_html_e( 'Potential compatibility overlaps were detected. Review these before enabling or troubleshooting frontend output.', 'alynt-account-gateway' ); ?></p>
		</div>
		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Area', 'alynt-account-gateway' ); ?></th>
					<th><?php esc_html_e( 'Warning', 'alynt-account-gateway' ); ?></th>
					<th><?php esc_html_e( 'Details', 'alynt-account-gateway' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $warnings as $warning ) : ?>
					<tr>
						<td><?php echo esc_html( ucwords( str_replace( '_', ' ', $warning['category'] ) ) ); ?></td>
						<td><?php echo esc_html( $warning['title'] ); ?></td>
						<td><?php echo esc_html( $warning['message'] ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Return supported gateway preview screens.
	 *
	 * @return array<string,string>
	 */
	private function gateway_preview_screens() {
		return array(
			'login'                 => __( 'Login', 'alynt-account-gateway' ),
			'register'              => __( 'Registration', 'alynt-account-gateway' ),
			'lostpassword'          => __( 'Lost Password', 'alynt-account-gateway' ),
			'setpassword'           => __( 'Set Password', 'alynt-account-gateway' ),
			'logout'                => __( 'Logout Confirmation', 'alynt-account-gateway' ),
			'registration_disabled' => __( 'Registration Disabled', 'alynt-account-gateway' ),
			'invalidlink'           => __( 'Invalid Link', 'alynt-account-gateway' ),
			'dashboard'             => __( 'Dashboard', 'alynt-account-gateway' ),
		);
	}

	/**
	 * Render restore-defaults control for the active tab.
	 *
	 * @param string $active_tab Active settings tab.
	 * @return void
	 */
	private function render_restore_tab_defaults( $active_tab ) {
		$tabs = ALYNT_AG_Settings_Schema::tabs();

		if ( ! isset( $tabs[ $active_tab ] ) || empty( ALYNT_AG_Settings_Schema::keys_for_tab( $active_tab ) ) ) {
			return;
		}

		$confirm = sprintf(
			/* translators: %s: settings tab label. */
			__( 'Restore the %s tab to its default settings? This cannot be undone automatically.', 'alynt-account-gateway' ),
			$tabs[ $active_tab ]
		);
		?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="alynt-ag-restore-tab-defaults">
			<input type="hidden" name="action" value="alynt_ag_restore_tab_defaults">
			<input type="hidden" name="tab" value="<?php echo esc_attr( $active_tab ); ?>">
			<?php wp_nonce_field( 'alynt_ag_restore_tab_defaults_' . $active_tab ); ?>
			<?php submit_button( __( 'Restore This Tab To Defaults', 'alynt-account-gateway' ), 'secondary', 'submit', false, array( 'onclick' => 'return confirm(' . wp_json_encode( $confirm ) . ');' ) ); ?>
		</form>
		<?php
	}

	/**
	 * Render diagnostics tools.
	 *
	 * @return void
	 */
	private function render_diagnostics_tools() {
		$health = ALYNT_AG_Diagnostics_Logger::health_summary();
		$events = ALYNT_AG_Diagnostics_Logger::recent_events( 20 );
		?>
		<?php $this->render_compatibility_warnings(); ?>

		<?php $this->render_gateway_preview_tools(); ?>

		<?php $this->render_settings_tools(); ?>

		<h2><?php esc_html_e( 'Diagnostics', 'alynt-account-gateway' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Diagnostics are disabled by default. When enabled, structured events are stored in a plugin-owned table with sensitive fields redacted before persistence.', 'alynt-account-gateway' ); ?>
		</p>

		<table class="widefat striped" role="presentation">
			<tbody>
				<?php foreach ( $health as $label => $value ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( ucwords( str_replace( '_', ' ', $label ) ) ); ?></th>
						<td>
							<?php
							if ( is_bool( $value ) ) {
								echo esc_html( $value ? __( 'Yes', 'alynt-account-gateway' ) : __( 'No', 'alynt-account-gateway' ) );
							} else {
								echo esc_html( (string) $value );
							}
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<p>
			<a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=alynt_ag_export_diagnostics' ), 'alynt_ag_export_diagnostics' ) ); ?>">
				<?php esc_html_e( 'Export Diagnostics CSV', 'alynt-account-gateway' ); ?>
			</a>
		</p>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="alynt_ag_clear_diagnostics">
			<?php wp_nonce_field( 'alynt_ag_clear_diagnostics' ); ?>
			<?php submit_button( __( 'Clear Diagnostics Events', 'alynt-account-gateway' ), 'delete', 'submit', false ); ?>
		</form>

		<h3><?php esc_html_e( 'Recent Events', 'alynt-account-gateway' ); ?></h3>
		<?php if ( empty( $events ) ) : ?>
			<p><?php esc_html_e( 'No diagnostics events have been recorded.', 'alynt-account-gateway' ); ?></p>
		<?php else : ?>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Time', 'alynt-account-gateway' ); ?></th>
						<th><?php esc_html_e( 'Level', 'alynt-account-gateway' ); ?></th>
						<th><?php esc_html_e( 'Category', 'alynt-account-gateway' ); ?></th>
						<th><?php esc_html_e( 'Event', 'alynt-account-gateway' ); ?></th>
						<th><?php esc_html_e( 'Message', 'alynt-account-gateway' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $events as $event ) : ?>
						<tr>
							<td><?php echo esc_html( $event->created_at ); ?></td>
							<td><?php echo esc_html( $event->level ); ?></td>
							<td><?php echo esc_html( $event->category ); ?></td>
							<td><?php echo esc_html( $event->event_code ); ?></td>
							<td><?php echo esc_html( $event->message ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
		<?php
	}

	/**
	 * Export plugin settings as JSON.
	 *
	 * @return void
	 */
	public function handle_export_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to export settings.', 'alynt-account-gateway' ) );
		}

		check_admin_referer( 'alynt_ag_export_settings' );

		$package = ALYNT_AG_Settings_Schema::export_package();
		$json    = wp_json_encode( $package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

		if ( ! is_string( $json ) ) {
			wp_die( esc_html__( 'Settings could not be encoded for export.', 'alynt-account-gateway' ) );
		}

		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=alynt-account-gateway-settings.json' );

		echo $json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON download generated from sanitized settings.
		exit;
	}

	/**
	 * Import plugin settings from JSON.
	 *
	 * @return void
	 */
	public function handle_import_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to import settings.', 'alynt-account-gateway' ) );
		}

		check_admin_referer( 'alynt_ag_import_settings' );

		$status        = 'settings_import_upload_failed';
		$ignored_count = 0;
		$file          = isset( $_FILES['settings_file'] ) && is_array( $_FILES['settings_file'] ) ? $_FILES['settings_file'] : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- File metadata is validated before use.

		if ( isset( $file['tmp_name'], $file['error'] ) && is_string( $file['tmp_name'] ) && UPLOAD_ERR_OK === (int) $file['error'] && is_uploaded_file( $file['tmp_name'] ) ) {
			$json       = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading the PHP-uploaded temp file only.
			$json       = is_string( $json ) ? $json : '';
			$inspection = ALYNT_AG_Settings_Schema::inspect_import_package( $json );
			$imported   = is_wp_error( $inspection ) ? $inspection : ALYNT_AG_Settings_Schema::import_package( $json );

			if ( ! is_wp_error( $imported ) ) {
				$ignored_count = isset( $inspection['unknown_count'] ) ? absint( $inspection['unknown_count'] ) : 0;
				update_option( 'alynt_ag_settings', $imported );
				ALYNT_AG_Diagnostics_Logger::log(
					'settings_imported',
					array(
						'imported_keys'  => isset( $inspection['known_keys'] ) ? $inspection['known_keys'] : array_keys( ALYNT_AG_Settings_Schema::filter_known_settings( $imported ) ),
						'ignored_keys'   => isset( $inspection['unknown_keys'] ) ? $inspection['unknown_keys'] : array(),
						'source_plugin'  => isset( $inspection['plugin'] ) ? $inspection['plugin'] : '',
						'source_version' => isset( $inspection['version'] ) ? $inspection['version'] : '',
						'exported_at'    => isset( $inspection['exported_at'] ) ? $inspection['exported_at'] : '',
					),
					get_current_user_id()
				);
				$status = $ignored_count > 0 ? 'settings_imported_with_ignored_keys' : 'settings_imported';
			} elseif ( 'alynt_ag_invalid_settings_import' === $imported->get_error_code() ) {
				$status = 'settings_import_invalid_json';
			} elseif ( 'alynt_ag_empty_settings_import' === $imported->get_error_code() ) {
				$status = 'settings_import_empty';
			} else {
				$status = 'settings_import_failed';
			}
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'                    => 'alynt-account-gateway',
					'tab'                     => 'advanced_tools',
					'alynt_ag_notice'         => $status,
					'alynt_ag_import_ignored' => $ignored_count,
				),
				admin_url( 'options-general.php' )
			)
		);
		exit;
	}

	/**
	 * Restore one settings tab to defaults.
	 *
	 * @return void
	 */
	public function handle_restore_tab_defaults() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to restore settings.', 'alynt-account-gateway' ) );
		}

		$tabs = ALYNT_AG_Settings_Schema::tabs();
		$tab  = isset( $_POST['tab'] ) ? sanitize_key( wp_unslash( $_POST['tab'] ) ) : 'general';
		$tab  = isset( $tabs[ $tab ] ) ? $tab : 'general';

		check_admin_referer( 'alynt_ag_restore_tab_defaults_' . $tab );

		$restored = ALYNT_AG_Settings_Schema::restore_tab_defaults( $tab );
		$status   = 'tab_defaults_failed';

		if ( ! is_wp_error( $restored ) ) {
			update_option( 'alynt_ag_settings', $restored );
			ALYNT_AG_Diagnostics_Logger::log(
				'tab_defaults_restored',
				array(
					'tab'           => $tab,
					'restored_keys' => ALYNT_AG_Settings_Schema::keys_for_tab( $tab ),
				),
				get_current_user_id()
			);
			$status = 'tab_defaults_restored';
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'            => 'alynt-account-gateway',
					'tab'             => $tab,
					'alynt_ag_notice' => $status,
				),
				admin_url( 'options-general.php' )
			)
		);
		exit;
	}

	/**
	 * Render a standalone gateway screen preview.
	 *
	 * @return void
	 */
	public function handle_preview_gateway() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to preview gateway screens.', 'alynt-account-gateway' ) );
		}

		$screens = $this->gateway_preview_screens();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verified immediately below.
		$screen = isset( $_GET['screen'] ) ? sanitize_key( wp_unslash( $_GET['screen'] ) ) : 'login';
		$screen = isset( $screens[ $screen ] ) ? $screen : 'login';

		check_admin_referer( 'alynt_ag_preview_gateway_' . $screen );

		$frontend = new ALYNT_AG_Frontend();
		$settings = ALYNT_AG_Settings_Schema::get_settings();

		$this->enqueue_gateway_preview_assets( $screen, $settings );
		show_admin_bar( false );
		add_filter( 'show_admin_bar', '__return_false', PHP_INT_MAX );
		remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
		remove_action( 'admin_footer', 'wp_admin_bar_render', 1000 );

		status_header( 200 );
		nocache_headers();

		echo '<!doctype html>';
		echo '<html ';
		language_attributes();
		echo '>';
		echo '<head>';
		echo '<meta charset="' . esc_attr( get_bloginfo( 'charset' ) ) . '">';
		echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
		echo '<title>' . esc_html( $frontend->get_screen_title( $screen ) ) . '</title>';
		wp_head();
		echo '</head>';
		echo '<body class="alynt-ag-body alynt-ag-preview-body">';
		$frontend->render_preview( $screen, $settings );
		wp_footer();
		echo '</body></html>';
		exit;
	}

	/**
	 * Enqueue frontend assets for a standalone admin preview.
	 *
	 * @param string              $screen   Screen key.
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function enqueue_gateway_preview_assets( $screen, $settings ) {
		$style_path = ALYNT_AG_PLUGIN_DIR . 'assets/dist/frontend/index.css';
		if ( file_exists( $style_path ) ) {
			wp_enqueue_style(
				'alynt-ag-frontend',
				ALYNT_AG_PLUGIN_URL . 'assets/dist/frontend/index.css',
				array(),
				filemtime( $style_path )
			);
		}

		$script_path = ALYNT_AG_PLUGIN_DIR . 'assets/dist/frontend/index.js';
		if ( file_exists( $script_path ) ) {
			wp_enqueue_script(
				'alynt-ag-frontend',
				ALYNT_AG_PLUGIN_URL . 'assets/dist/frontend/index.js',
				array(),
				filemtime( $script_path ),
				true
			);

			wp_localize_script(
				'alynt-ag-frontend',
				'alyntAgFrontend',
				array(
					'labels' => array(
						'showPassword'    => __( 'Show password', 'alynt-account-gateway' ),
						'hidePassword'    => __( 'Hide password', 'alynt-account-gateway' ),
						'passwordVisible' => __( 'Password is visible.', 'alynt-account-gateway' ),
						'passwordHidden'  => __( 'Password is hidden.', 'alynt-account-gateway' ),
						'show'            => __( 'Show', 'alynt-account-gateway' ),
						'hide'            => __( 'Hide', 'alynt-account-gateway' ),
					),
				)
			);
		}

		if ( ! empty( $settings['turnstile_site_key'] ) && 'register' === $screen ) {
			wp_enqueue_script(
				'alynt-ag-turnstile',
				'https://challenges.cloudflare.com/turnstile/v0/api.js',
				array(),
				ALYNT_AG_VERSION,
				true
			);
		}
	}

	/**
	 * Export diagnostics events.
	 *
	 * @return void
	 */
	public function handle_export_diagnostics() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to export diagnostics.', 'alynt-account-gateway' ) );
		}

		check_admin_referer( 'alynt_ag_export_diagnostics' );

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=alynt-account-gateway-diagnostics.csv' );

		ALYNT_AG_Diagnostics_Logger::export_csv();
		exit;
	}

	/**
	 * Clear diagnostics events.
	 *
	 * @return void
	 */
	public function handle_clear_diagnostics() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to clear diagnostics.', 'alynt-account-gateway' ) );
		}

		check_admin_referer( 'alynt_ag_clear_diagnostics' );
		ALYNT_AG_Diagnostics_Logger::clear_events();

		wp_safe_redirect(
			add_query_arg(
				array(
					'page' => 'alynt-account-gateway',
					'tab'  => 'advanced_tools',
				),
				admin_url( 'options-general.php' )
			)
		);
		exit;
	}

	/**
	 * Render an email template preview.
	 *
	 * @return void
	 */
	public function handle_preview_email() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to preview emails.', 'alynt-account-gateway' ) );
		}

		check_admin_referer( 'alynt_ag_preview_email' );

		$email_service = new ALYNT_AG_Email_Template_Service();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verified above.
		$template = isset( $_GET['template'] ) ? sanitize_key( wp_unslash( $_GET['template'] ) ) : 'registration_confirmation';
		$rendered = $email_service->render( $template, $email_service->preview_tokens(), ALYNT_AG_Settings_Schema::get_settings() );

		if ( is_wp_error( $rendered ) ) {
			wp_die( esc_html( $rendered->get_error_message() ) );
		}

		header( 'Content-Type: text/html; charset=utf-8' );
		echo $rendered['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by email renderer.
		exit;
	}

	/**
	 * Send a test email.
	 *
	 * @return void
	 */
	public function handle_test_email() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to send test emails.', 'alynt-account-gateway' ) );
		}

		check_admin_referer( 'alynt_ag_test_email' );

		$email_service = new ALYNT_AG_Email_Template_Service();
		$template      = isset( $_POST['template'] ) ? sanitize_key( wp_unslash( $_POST['template'] ) ) : 'registration_confirmation';
		$recipient     = isset( $_POST['recipient'] ) ? sanitize_email( wp_unslash( $_POST['recipient'] ) ) : '';
		$result        = $email_service->send( $template, $recipient, $email_service->preview_tokens(), ALYNT_AG_Settings_Schema::get_settings() );
		$status        = is_wp_error( $result ) ? 'email_test_failed' : 'email_test_sent';

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'            => 'alynt-account-gateway',
					'tab'             => 'emails',
					'alynt_ag_notice' => $status,
				),
				admin_url( 'options-general.php' )
			)
		);
		exit;
	}

	/**
	 * Send a test webhook.
	 *
	 * @return void
	 */
	public function handle_test_webhook() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to send test webhooks.', 'alynt-account-gateway' ) );
		}

		check_admin_referer( 'alynt_ag_test_webhook' );

		$settings = ALYNT_AG_Settings_Schema::get_settings();
		$status   = 'webhook_test_failed';

		if ( empty( $settings['account_created_webhook'] ) ) {
			$status = 'webhook_test_missing';
		} else {
			$dispatcher = new ALYNT_AG_Webhook_Dispatcher();
			$result     = $dispatcher->dispatch_account_created_test( get_current_user_id(), $settings );
			$status     = is_wp_error( $result ) ? 'webhook_test_failed' : 'webhook_test_sent';
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'            => 'alynt-account-gateway',
					'tab'             => 'webhooks',
					'alynt_ag_notice' => $status,
				),
				admin_url( 'options-general.php' )
			)
		);
		exit;
	}

	/**
	 * Record settings changes in the audit log.
	 *
	 * @param array<string,mixed> $old_value Previous settings.
	 * @param array<string,mixed> $value     New settings.
	 * @return void
	 */
	public function log_settings_change( $old_value, $value ) {
		$changed_keys = array();

		foreach ( (array) $value as $key => $new_value ) {
			$old_setting = is_array( $old_value ) && array_key_exists( $key, $old_value ) ? $old_value[ $key ] : null;
			if ( $old_setting !== $new_value ) {
				$changed_keys[] = $key;
			}
		}

		if ( empty( $changed_keys ) ) {
			return;
		}

		ALYNT_AG_Diagnostics_Logger::log(
			'settings_changed',
			array( 'changed_keys' => $changed_keys ),
			get_current_user_id()
		);
	}
}
