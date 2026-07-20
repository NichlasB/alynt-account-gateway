<?php
/**
 * Settings page page-shell component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused page-shell behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Page_Shell extends ALYNT_AG_Settings_Page_Component {

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

			<form
				method="post"
				action="options.php"
				data-alynt-ag-settings-form
				data-alynt-ag-action-form
				<?php if ( 'emails' === $active_tab ) : ?>
					data-alynt-ag-email-settings
				<?php endif; ?>
			>
				<?php settings_fields( 'alynt_ag_settings' ); ?>

				<table class="form-table" role="presentation">
					<tbody>
						<?php if ( 'branding' === $active_tab ) : ?>
							<?php $this->render_typography_preset_control( $settings ); ?>
						<?php endif; ?>
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
}
