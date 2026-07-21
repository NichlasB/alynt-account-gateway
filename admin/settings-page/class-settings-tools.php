<?php
/**
 * Settings page settings-tools component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused settings-tools behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Settings_Tools extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render settings import/export tools.
	 *
	 * @return void
	 */
	public function render_settings_tools() {
		?>
		<h2><?php esc_html_e( 'Settings Import / Export', 'alynt-account-gateway' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Export portable plugin settings as JSON, or import a JSON settings package from another site. Imported values are sanitized through the active settings schema.', 'alynt-account-gateway' ); ?>
		</p>
		<div class="notice notice-info inline">
			<p><strong><?php esc_html_e( 'Configuration portability notes', 'alynt-account-gateway' ); ?></strong></p>
			<ul class="ul-disc">
				<li><?php esc_html_e( 'Exports include saved plugin settings only. Media-library files, pending registrations, diagnostics, webhook delivery logs, and WordPress users are not included.', 'alynt-account-gateway' ); ?></li>
				<li><?php esc_html_e( 'Secret credentials, the test email recipient, and site-specific media selections are omitted. Configure those values separately on the destination site.', 'alynt-account-gateway' ); ?></li>
				<li><?php esc_html_e( 'Imports validate JSON before saving, keep recognized settings, sanitize each value, and ignore settings that do not belong to the current schema.', 'alynt-account-gateway' ); ?></li>
				<li><?php esc_html_e( 'Use the restore button at the bottom of each tab when you only want to reset that tab instead of replacing the full configuration.', 'alynt-account-gateway' ); ?></li>
			</ul>
		</div>

		<p>
			<a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=alynt_ag_export_settings' ), 'alynt_ag_export_settings' ) ); ?>">
				<?php esc_html_e( 'Export Settings JSON', 'alynt-account-gateway' ); ?>
			</a>
		</p>

		<form
			method="post"
			action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
			enctype="multipart/form-data"
			class="alynt-ag-inline-tool"
			data-alynt-ag-action-form
			data-alynt-ag-confirm="<?php esc_attr_e( 'Importing will replace recognized plugin settings with values from this file. Continue?', 'alynt-account-gateway' ); ?>"
		>
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
	public function render_gateway_preview_tools() {
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
						'alynt_ag_preview_gateway' => '1',
						'alynt_ag_preview_screen'  => $this->gateway_preview_screen_code( $screen ),
					),
					home_url( '/' )
				);
				$preview_url = wp_nonce_url( $preview_url, 'alynt_ag_preview_gateway_' . $screen, 'alynt_ag_preview_nonce' );
				?>
				<a class="button" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( $preview_url ); ?>">
					<?php echo esc_html( $label ); ?>
					<span class="screen-reader-text"><?php esc_html_e( 'opens in a new tab', 'alynt-account-gateway' ); ?></span>
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
	public function render_compatibility_warnings() {
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
		<table class="widefat striped" aria-label="<?php esc_attr_e( 'Potential compatibility overlaps', 'alynt-account-gateway' ); ?>">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Area', 'alynt-account-gateway' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Warning', 'alynt-account-gateway' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Details', 'alynt-account-gateway' ); ?></th>
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
}
