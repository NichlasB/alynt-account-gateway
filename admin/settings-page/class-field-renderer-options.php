<?php
/**
 * Settings page field-renderer-options component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused field-renderer-options behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Field_Renderer_Options extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render the non-persistent typography preset helper.
	 *
	 * Presets populate the existing font-stack settings so custom values and
	 * import/export behavior retain one source of truth.
	 *
	 * @param array<string,mixed> $settings Current settings.
	 * @return void
	 */
	public function render_typography_preset_control( $settings ) {
		$presets        = $this->typography_presets();
		$selected       = $this->selected_typography_preset( $settings, $presets );
		$selected_label = 'custom' === $selected ? __( 'Custom', 'alynt-account-gateway' ) : $presets[ $selected ]['label'];
		?>
		<tr class="alynt-ag-typography-row">
			<th scope="row">
				<label for="alynt-ag-typography-preset">
					<?php esc_html_e( 'Typography Preset', 'alynt-account-gateway' ); ?>
				</label>
			</th>
			<td>
				<div
					class="alynt-ag-typography-control"
					data-alynt-ag-typography-presets
					data-status-prefix="<?php esc_attr_e( 'Current pairing:', 'alynt-account-gateway' ); ?>"
				>
					<select
						id="alynt-ag-typography-preset"
						aria-describedby="alynt-ag-typography-preset-help"
						data-alynt-ag-typography-select
					>
						<?php foreach ( $presets as $preset_key => $preset ) : ?>
							<option
								value="<?php echo esc_attr( $preset_key ); ?>"
								data-heading="<?php echo esc_attr( $preset['heading'] ); ?>"
								data-body="<?php echo esc_attr( $preset['body'] ); ?>"
								<?php echo $selected === $preset_key ? 'selected' : ''; ?>
							>
								<?php echo esc_html( $preset['label'] ); ?>
							</option>
						<?php endforeach; ?>
						<option value="custom" <?php echo 'custom' === $selected ? 'selected' : ''; ?>>
							<?php esc_html_e( 'Custom', 'alynt-account-gateway' ); ?>
						</option>
					</select>
					<p id="alynt-ag-typography-preset-help" class="alynt-ag-field-help">
						<?php esc_html_e( 'Choose a privacy-friendly system-font pairing, or edit either font stack below to use a custom pairing. No remote fonts are loaded.', 'alynt-account-gateway' ); ?>
					</p>
					<div class="alynt-ag-typography-preview" data-alynt-ag-typography-preview>
						<p class="alynt-ag-typography-preview__status" aria-live="polite" data-alynt-ag-typography-status>
							<?php
							echo esc_html(
								sprintf(
									/* translators: %s: typography preset name. */
									__( 'Current pairing: %s', 'alynt-account-gateway' ),
									$selected_label
								)
							);
							?>
						</p>
						<p class="alynt-ag-typography-preview__heading" data-alynt-ag-typography-heading>
							<?php esc_html_e( 'Customer account', 'alynt-account-gateway' ); ?>
						</p>
						<p class="alynt-ag-typography-preview__body" data-alynt-ag-typography-body>
							<?php esc_html_e( 'Manage your orders, details, and account preferences.', 'alynt-account-gateway' ); ?>
						</p>
					</div>
					<noscript>
						<p class="alynt-ag-field-help">
							<?php esc_html_e( 'JavaScript is required to apply a preset. The custom font-stack fields remain available below.', 'alynt-account-gateway' ); ?>
						</p>
					</noscript>
				</div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Return privacy-friendly local/system typography pairings.
	 *
	 * @return array<string,array{label:string,heading:string,body:string}>
	 */
	public function typography_presets() {
		$system_body = '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';

		return array(
			'classic'   => array(
				'label'   => __( 'Classic Contrast', 'alynt-account-gateway' ),
				'heading' => 'Georgia, serif',
				'body'    => $system_body,
			),
			'modern'    => array(
				'label'   => __( 'Modern Sans', 'alynt-account-gateway' ),
				'heading' => $system_body,
				'body'    => $system_body,
			),
			'editorial' => array(
				'label'   => __( 'Editorial Serif', 'alynt-account-gateway' ),
				'heading' => '"Palatino Linotype", "Book Antiqua", Palatino, Georgia, serif',
				'body'    => $system_body,
			),
			'humanist'  => array(
				'label'   => __( 'Clear Humanist', 'alynt-account-gateway' ),
				'heading' => '"Trebuchet MS", Arial, sans-serif',
				'body'    => '"Segoe UI", Tahoma, Arial, sans-serif',
			),
		);
	}

	/**
	 * Match current stacks to a known preset without changing saved settings.
	 *
	 * @param array<string,mixed>                                          $settings Current settings.
	 * @param array<string,array{label:string,heading:string,body:string}> $presets Presets.
	 * @return string
	 */
	public function selected_typography_preset( $settings, $presets = array() ) {
		$presets = $presets ? $presets : $this->typography_presets();
		$heading = isset( $settings['heading_font_family'] ) ? (string) $settings['heading_font_family'] : '';
		$body    = isset( $settings['body_font_family'] ) ? (string) $settings['body_font_family'] : '';

		foreach ( $presets as $preset_key => $preset ) {
			if ( $heading === $preset['heading'] && $body === $preset['body'] ) {
				return $preset_key;
			}
		}

		return 'custom';
	}

	/**
	 * Render a WordPress navigation menu selector.
	 *
	 * @param string $id    Field ID.
	 * @param string $name  Field name.
	 * @param int    $value Selected menu ID.
	 * @param string $aria  Escaped aria-describedby attribute.
	 * @return void
	 */
	public function render_nav_menu_field( $id, $name, $value, $aria = '' ) {
		$menus = function_exists( 'wp_get_nav_menus' ) ? wp_get_nav_menus() : array();
		?>
		<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>"<?php echo $aria; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by field_describedby_attribute(). ?>>
			<option value="0"><?php esc_html_e( 'Select a menu', 'alynt-account-gateway' ); ?></option>
			<?php foreach ( $menus as $menu ) : ?>
				<option value="<?php echo esc_attr( (string) $menu->term_id ); ?>" <?php selected( $value, (int) $menu->term_id ); ?>>
					<?php echo esc_html( $menu->name ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php if ( empty( $menus ) ) : ?>
			<p class="description alynt-ag-field-help">
				<?php esc_html_e( 'Create a WordPress navigation menu first, then return here to attach it to the dashboard.', 'alynt-account-gateway' ); ?>
			</p>
			<?php
		endif;
	}

	/**
	 * Render WooCommerce dashboard navigation visibility controls.
	 *
	 * @param string            $id    Field ID.
	 * @param string            $name  Field name.
	 * @param array<int,string> $value Hidden endpoint keys.
	 * @param string            $aria  Escaped aria-describedby attribute.
	 * @return void
	 */
	public function render_woocommerce_menu_visibility_field( $id, $name, $value, $aria = '' ) {
		$integration = new ALYNT_AG_WooCommerce_Integration();
		$items       = $integration->account_menu_items();
		$hidden      = is_array( $value ) ? array_map( 'sanitize_key', $value ) : array();
		$index       = 0;
		?>
		<fieldset class="alynt-ag-checkbox-list"<?php echo $aria; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by field_describedby_attribute(). ?>>
			<legend class="screen-reader-text"><?php esc_html_e( 'WooCommerce dashboard navigation visibility', 'alynt-account-gateway' ); ?></legend>
			<?php foreach ( $items as $endpoint => $label ) : ?>
				<?php
				$endpoint    = sanitize_key( $endpoint );
				$checkbox_id = 0 === $index ? $id : $id . '-item-' . $index;
				++$index;

				if ( ! $endpoint ) {
					continue;
				}
				?>
				<label for="<?php echo esc_attr( $checkbox_id ); ?>">
					<input type="hidden" name="<?php echo esc_attr( $name . '[' . $endpoint . ']' ); ?>" value="1">
					<input
						type="checkbox"
						id="<?php echo esc_attr( $checkbox_id ); ?>"
						name="<?php echo esc_attr( $name . '[' . $endpoint . ']' ); ?>"
						value="0"
						<?php checked( ! in_array( $endpoint, $hidden, true ) ); ?>
					>
					<?php
					printf(
						/* translators: %s: WooCommerce account navigation item label. */
						esc_html__( 'Show %s', 'alynt-account-gateway' ),
						esc_html( $label )
					);
					?>
				</label>
			<?php endforeach; ?>
		</fieldset>
		<?php
	}
}
