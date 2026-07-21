<?php
/**
 * Settings page complex-fields component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused complex-fields behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Complex_Fields extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render a WordPress media-library backed image field.
	 *
	 * @param string $id    Field ID.
	 * @param string $name  Field name.
	 * @param int    $value Attachment ID.
	 * @param string $label Field label.
	 * @return void
	 */
	public function render_media_field( $id, $name, $value, $label = '' ) {
		$image_url = $value ? wp_get_attachment_image_url( $value, 'medium' ) : '';
		$label     = $label ? $label : __( 'Image', 'alynt-account-gateway' );
		$select    = sprintf(
			/* translators: %s: image field label. */
			__( 'Select %s', 'alynt-account-gateway' ),
			$label
		);
		$remove = sprintf(
			/* translators: %s: image field label. */
			__( 'Remove %s', 'alynt-account-gateway' ),
			$label
		);
		?>
		<div class="alynt-ag-media-field" data-alynt-ag-media-field>
			<input type="hidden" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( (string) $value ); ?>" data-alynt-ag-media-input>
			<div class="alynt-ag-media-field__preview" data-alynt-ag-media-preview>
				<?php if ( $image_url ) : ?>
					<img src="<?php echo esc_url( $image_url ); ?>" alt="">
				<?php endif; ?>
			</div>
			<p>
				<button type="button" class="button" aria-label="<?php echo esc_attr( $select ); ?>" data-alynt-ag-media-select><?php esc_html_e( 'Select Image', 'alynt-account-gateway' ); ?></button>
				<button type="button" class="button" aria-label="<?php echo esc_attr( $remove ); ?>" aria-disabled="<?php echo $value ? 'false' : 'true'; ?>" data-alynt-ag-media-remove <?php disabled( ! $value ); ?>><?php esc_html_e( 'Remove', 'alynt-account-gateway' ); ?></button>
			</p>
			<p class="screen-reader-text" role="status" aria-live="polite" aria-atomic="true" data-alynt-ag-media-status></p>
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
	public function render_dashboard_links_field( $id, $name, $value ) {
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
			<p class="screen-reader-text" role="status" aria-live="polite" aria-atomic="true" data-alynt-ag-dashboard-link-status></p>

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
	public function render_dashboard_link_row( $index, $link, $icons, $roles ) {
		$label  = isset( $link['label'] ) ? (string) $link['label'] : '';
		$url    = isset( $link['url'] ) ? (string) $link['url'] : '';
		$icon   = isset( $link['icon'] ) ? sanitize_key( $link['icon'] ) : 'link';
		$order  = isset( $link['order'] ) ? absint( $link['order'] ) : 100;
		$target = isset( $link['target'] ) ? (string) $link['target'] : '_self';
		$chosen = isset( $link['roles'] ) && is_array( $link['roles'] ) ? array_map( 'sanitize_key', $link['roles'] ) : array();
		?>
		<div class="alynt-ag-dashboard-link-row" role="group" aria-labelledby="alynt-ag-dashboard-link-<?php echo esc_attr( $index ); ?>-title" data-alynt-ag-dashboard-link-row>
			<div class="alynt-ag-dashboard-link-row__header">
				<strong id="alynt-ag-dashboard-link-<?php echo esc_attr( $index ); ?>-title"><?php esc_html_e( 'Dashboard Link', 'alynt-account-gateway' ); ?></strong>
				<button type="button" class="button-link-delete" aria-label="<?php esc_attr_e( 'Remove dashboard link', 'alynt-account-gateway' ); ?>" data-alynt-ag-dashboard-link-remove>
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
	public function dashboard_link_icon_options() {
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
	public function dashboard_link_role_options() {
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
}
