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
		add_action( 'admin_post_alynt_ag_export_diagnostics', array( $this, 'handle_export_diagnostics' ) );
		add_action( 'admin_post_alynt_ag_clear_diagnostics', array( $this, 'handle_clear_diagnostics' ) );
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

			<?php if ( 'advanced_tools' === $active_tab ) : ?>
				<?php $this->render_diagnostics_tools(); ?>
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
	 * Render diagnostics tools.
	 *
	 * @return void
	 */
	private function render_diagnostics_tools() {
		$health = ALYNT_AG_Diagnostics_Logger::health_summary();
		$events = ALYNT_AG_Diagnostics_Logger::recent_events( 20 );
		?>
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
