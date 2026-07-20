<?php
/**
 * Settings page field-renderer-core component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused field-renderer-core behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Field_Renderer_Core extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render one settings field.
	 *
	 * @param string              $key   Field key.
	 * @param array<string,mixed> $field Field schema.
	 * @param mixed               $value Current value.
	 * @return void
	 */
	public function render_field( $key, $field, $value ) {
		$name    = sprintf( 'alynt_ag_settings[%s]', $key );
		$id      = sprintf( 'alynt-ag-%s', $key );
		$aria    = $this->field_describedby_attribute( $key );
		$type    = $field['type'];
		$context = compact( 'key', 'field', 'value', 'name', 'id', 'aria', 'type' );

		if ( $this->render_simple_field( $context ) ) {
			return;
		}

		if ( 'attachment_id' === $type ) {
			$this->render_media_field( $id, $name, (int) $value );
			return;
		}

		if ( 'color' === $type ) {
			$this->render_color_field( $context );
			return;
		}

		if ( 'rich_text' === $type ) {
			$this->render_rich_text_field( $context );
			return;
		}

		if ( 'dashboard_links' === $type ) {
			$this->render_dashboard_links_field( $id, $name, $value );
			return;
		}

		if ( 'woocommerce_menu_visibility' === $type ) {
			$this->render_woocommerce_menu_visibility_field( $id, $name, $value, $aria );
			return;
		}

		if ( 'nav_menu' === $type ) {
			$this->render_nav_menu_field( $id, $name, (int) $value, $aria );
			return;
		}

		if ( 'select' === $type ) {
			$this->render_select_field( $context );
			return;
		}

		$this->render_text_field( $context );
	}

	/**
	 * Render a simple scalar field when the type is supported.
	 *
	 * @param array<string,mixed> $context Field render context.
	 * @return bool Whether the field was rendered.
	 */
	private function render_simple_field( $context ) {
		$type  = $context['type'];
		$id    = $context['id'];
		$name  = $context['name'];
		$value = $context['value'];
		$aria  = $context['aria'];

		if ( 'boolean' === $type ) {
			?>
			<label>
				<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="0">
				<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="1" <?php checked( $value ); ?><?php echo $aria; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by field_describedby_attribute(). ?>>
				<?php esc_html_e( 'Enabled', 'alynt-account-gateway' ); ?>
			</label>
			<?php
			return true;
		}

		if ( 'integer' === $type ) {
			$minimum = isset( $context['field']['min'] ) ? (int) $context['field']['min'] : 0;
			$maximum = isset( $context['field']['max'] ) ? ' max="' . esc_attr( (string) (int) $context['field']['max'] ) . '"' : '';
			printf(
				'<input type="number" min="%1$s"%2$s class="small-text" id="%3$s" name="%4$s" value="%5$s"%6$s>',
				esc_attr( (string) $minimum ),
				$maximum, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Attribute value escaped above.
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value ),
				$aria // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by field_describedby_attribute().
			);
			return true;
		}

		$formats = array(
			'email'    => '<input type="email" class="regular-text" id="%1$s" name="%2$s" value="%3$s" autocomplete="email"%4$s>',
			'textarea' => '<textarea class="large-text alynt-ag-textarea" rows="4" id="%1$s" name="%2$s"%4$s>%3$s</textarea>',
		);
		if ( ! isset( $formats[ $type ] ) ) {
			return false;
		}

		printf(
			$formats[ $type ], // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static formats defined above.
			esc_attr( $id ),
			esc_attr( $name ),
			'textarea' === $type ? esc_textarea( $value ) : esc_attr( $value ),
			$aria // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by field_describedby_attribute().
		);
		return true;
	}

	/**
	 * Render a synchronized color field.
	 *
	 * @param array<string,mixed> $context Field render context.
	 * @return void
	 */
	private function render_color_field( $context ) {
		$id           = $context['id'];
		$name         = $context['name'];
		$value        = $context['value'];
		$field        = $context['field'];
		$aria         = $context['aria'];
		$picker_value = sanitize_hex_color( (string) $value );
		$picker_value = $picker_value ? $picker_value : sanitize_hex_color( (string) $field['default'] );
		$picker_label = sprintf(
			/* translators: %s: settings field label. */
			__( 'Choose %s', 'alynt-account-gateway' ),
			$field['label']
		);
		?>
		<div class="alynt-ag-color-control" data-alynt-ag-color-control>
			<input type="color" class="alynt-ag-color-control__picker" value="<?php echo esc_attr( $picker_value ); ?>" aria-label="<?php echo esc_attr( $picker_label ); ?>" title="<?php echo esc_attr( $picker_label ); ?>" data-alynt-ag-color-picker>
			<input type="text" class="regular-text alynt-ag-color-control__text" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" pattern="^#[a-fA-F0-9]{6}$" placeholder="#3B5249" autocomplete="off" spellcheck="false" dir="ltr" data-alynt-ag-color-text <?php echo $aria; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by field_describedby_attribute(). ?>>
		</div>
		<?php
	}

	/**
	 * Render a rich text editor.
	 *
	 * @param array<string,mixed> $context Field render context.
	 * @return void
	 */
	private function render_rich_text_field( $context ) {
		wp_editor(
			(string) $context['value'],
			$context['id'],
			array(
				'textarea_name'    => $context['name'],
				'editor_class'     => 'alynt-ag-rich-text',
				'editor_height'    => 280,
				'media_buttons'    => false,
				'teeny'            => false,
				'drag_drop_upload' => false,
				'tinymce'          => array(
					'toolbar1' => 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,undo,redo',
					'toolbar2' => '',
				),
				'quicktags'        => array(
					'buttons' => 'strong,em,link,block,ul,ol,li,close',
				),
			)
		);
	}

	/**
	 * Render a select field.
	 *
	 * @param array<string,mixed> $context Field render context.
	 * @return void
	 */
	private function render_select_field( $context ) {
		$key     = $context['key'];
		$field   = $context['field'];
		$id      = $context['id'];
		$name    = $context['name'];
		$value   = $context['value'];
		$aria    = $context['aria'];
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
	}

	/**
	 * Render a text or secret field.
	 *
	 * @param array<string,mixed> $context Field render context.
	 * @return void
	 */
	private function render_text_field( $context ) {
		$key       = $context['key'];
		$field     = $context['field'];
		$id        = $context['id'];
		$name      = $context['name'];
		$value     = $context['value'];
		$aria      = $context['aria'];
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
}
