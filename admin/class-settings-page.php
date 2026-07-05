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
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<?php submit_button( __( 'Save Settings', 'alynt-account-gateway' ) ); ?>
			</form>

			<?php $this->render_restore_tab_defaults( $active_tab ); ?>

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

		if ( 'boolean' === $field['type'] ) {
			?>
			<label>
				<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="1" <?php checked( $value ); ?>>
				<?php esc_html_e( 'Enabled', 'alynt-account-gateway' ); ?>
			</label>
			<?php
			return;
		}

		if ( 'integer' === $field['type'] ) {
			printf(
				'<input type="number" min="0" class="small-text" id="%1$s" name="%2$s" value="%3$s">',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value )
			);
			return;
		}

		if ( 'attachment_id' === $field['type'] ) {
			$this->render_media_field( $id, $name, (int) $value );
			return;
		}

		if ( 'color' === $field['type'] ) {
			printf(
				'<input type="text" class="regular-text" id="%1$s" name="%2$s" value="%3$s" pattern="^#[a-fA-F0-9]{6}$">',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value )
			);
			return;
		}

		if ( 'textarea' === $field['type'] ) {
			printf(
				'<textarea class="large-text alynt-ag-textarea" rows="4" id="%1$s" name="%2$s">%3$s</textarea>',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_textarea( $value )
			);
			return;
		}

		if ( 'dashboard_links' === $field['type'] ) {
			$this->render_dashboard_links_field( $id, $name, $value );
			return;
		}

		if ( 'email' === $field['type'] ) {
			printf(
				'<input type="email" class="regular-text" id="%1$s" name="%2$s" value="%3$s" autocomplete="email">',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value )
			);
			return;
		}

		if ( 'select' === $field['type'] && 'diagnostics_min_level' === $key ) {
			$options = ALYNT_AG_Diagnostics_Logger::levels();
			echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">';
			foreach ( array_keys( $options ) as $option ) {
				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $option ),
					selected( $value, $option, false ),
					esc_html( ucfirst( $option ) )
				);
			}
			echo '</select>';
			return;
		}

		$type = 'secret' === $field['type'] ? 'password' : 'text';
		printf(
			'<input type="%1$s" class="regular-text" id="%2$s" name="%3$s" value="%4$s" autocomplete="off">',
			esc_attr( $type ),
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $value )
		);
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

		if ( 'settings_import_failed' === $notice ) {
			?>
			<div class="notice notice-error is-dismissible"><p><?php esc_html_e( 'Settings could not be imported. Choose a valid Alynt Account Gateway JSON export.', 'alynt-account-gateway' ); ?></p></div>
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
			<?php esc_html_e( 'Preview or send a test using the saved template settings and sample account tokens.', 'alynt-account-gateway' ); ?>
		</p>

		<form method="get" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" target="_blank" class="alynt-ag-inline-tool">
			<input type="hidden" name="action" value="alynt_ag_preview_email">
			<?php wp_nonce_field( 'alynt_ag_preview_email' ); ?>
			<label for="alynt-ag-email-preview-template"><?php esc_html_e( 'Template', 'alynt-account-gateway' ); ?></label>
			<select id="alynt-ag-email-preview-template" name="template">
				<?php foreach ( $templates as $template_key => $template_label ) : ?>
					<option value="<?php echo esc_attr( $template_key ); ?>"><?php echo esc_html( $template_label ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php submit_button( __( 'Preview Email', 'alynt-account-gateway' ), 'secondary', 'submit', false ); ?>
		</form>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="alynt-ag-inline-tool">
			<input type="hidden" name="action" value="alynt_ag_test_email">
			<?php wp_nonce_field( 'alynt_ag_test_email' ); ?>
			<label for="alynt-ag-email-test-template"><?php esc_html_e( 'Template', 'alynt-account-gateway' ); ?></label>
			<select id="alynt-ag-email-test-template" name="template">
				<?php foreach ( $templates as $template_key => $template_label ) : ?>
					<option value="<?php echo esc_attr( $template_key ); ?>"><?php echo esc_html( $template_label ); ?></option>
				<?php endforeach; ?>
			</select>
			<label for="alynt-ag-email-test-recipient"><?php esc_html_e( 'Recipient', 'alynt-account-gateway' ); ?></label>
			<input type="email" id="alynt-ag-email-test-recipient" name="recipient" class="regular-text" value="<?php echo esc_attr( $settings['email_test_recipient'] ); ?>" required>
			<?php submit_button( __( 'Send Test Email', 'alynt-account-gateway' ), 'secondary', 'submit', false ); ?>
		</form>
		<?php
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

		$status = 'settings_import_failed';
		$file   = isset( $_FILES['settings_file'] ) && is_array( $_FILES['settings_file'] ) ? $_FILES['settings_file'] : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- File metadata is validated before use.

		if ( isset( $file['tmp_name'], $file['error'] ) && is_string( $file['tmp_name'] ) && UPLOAD_ERR_OK === (int) $file['error'] && is_uploaded_file( $file['tmp_name'] ) ) {
			$json     = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading the PHP-uploaded temp file only.
			$imported = ALYNT_AG_Settings_Schema::import_package( is_string( $json ) ? $json : '' );

			if ( ! is_wp_error( $imported ) ) {
				update_option( 'alynt_ag_settings', $imported );
				ALYNT_AG_Diagnostics_Logger::log(
					'settings_imported',
					array( 'imported_keys' => array_keys( ALYNT_AG_Settings_Schema::filter_known_settings( $imported ) ) ),
					get_current_user_id()
				);
				$status = 'settings_imported';
			}
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'            => 'alynt-account-gateway',
					'tab'             => 'advanced_tools',
					'alynt_ag_notice' => $status,
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
						'showPassword' => __( 'Show password', 'alynt-account-gateway' ),
						'hidePassword' => __( 'Hide password', 'alynt-account-gateway' ),
						'show'         => __( 'Show', 'alynt-account-gateway' ),
						'hide'         => __( 'Hide', 'alynt-account-gateway' ),
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
