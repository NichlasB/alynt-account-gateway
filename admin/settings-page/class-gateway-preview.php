<?php
/**
 * Settings page gateway-preview component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused gateway-preview behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Gateway_Preview extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Return supported gateway preview screens.
	 *
	 * @return array<string,string>
	 */
	public function gateway_preview_screens() {
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
	 * Return compact preview screen code for URLs.
	 *
	 * @param string $screen Screen key.
	 * @return string
	 */
	public function gateway_preview_screen_code( $screen ) {
		$codes = array(
			'login'                 => 'l',
			'register'              => 'r',
			'lostpassword'          => 'p',
			'setpassword'           => 's',
			'logout'                => 'o',
			'registration_disabled' => 'd',
			'invalidlink'           => 'i',
			'dashboard'             => 'b',
		);

		return isset( $codes[ $screen ] ) ? $codes[ $screen ] : 'l';
	}

	/**
	 * Render a standalone gateway preview from the settings page route.
	 *
	 * Some sites intercept or normalize admin-post.php requests. The settings
	 * page route keeps preview output behind wp-admin authentication while
	 * avoiding those front-controller collisions.
	 *
	 * @return void
	 */
	public function maybe_handle_preview_gateway_request() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce is verified by handle_preview_gateway().
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce is verified by handle_preview_gateway().
		$preview = isset( $_GET['alynt_ag_preview'] ) ? sanitize_key( wp_unslash( $_GET['alynt_ag_preview'] ) ) : '';

		if ( 'alynt-account-gateway' !== $page || '1' !== $preview ) {
			return;
		}

		$this->handle_preview_gateway();
	}

	/**
	 * Render restore-defaults control for the active tab.
	 *
	 * @param string $active_tab Active settings tab.
	 * @return void
	 */
	public function render_restore_tab_defaults( $active_tab ) {
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
		$this->print_gateway_preview_styles();
		echo '</head>';
		echo '<body class="alynt-ag-body alynt-ag-preview-body">';
		$frontend->render_preview( $screen, $settings );
		$this->print_gateway_preview_scripts();
		echo '</body></html>';
		exit;
	}

	/**
	 * Print only the assets needed by standalone gateway previews.
	 *
	 * @return void
	 */
	public function print_gateway_preview_styles() {
		if ( function_exists( 'wp_styles' ) ) {
			wp_styles()->do_items( array( 'alynt-ag-frontend' ) );
			return;
		}

		wp_print_styles( array( 'alynt-ag-frontend' ) );
	}

	/**
	 * Print only the scripts needed by standalone gateway previews.
	 *
	 * @return void
	 */
	public function print_gateway_preview_scripts() {
		if ( function_exists( 'wp_scripts' ) ) {
			wp_scripts()->do_items( array( 'alynt-ag-frontend' ) );
			return;
		}

		wp_print_scripts( array( 'alynt-ag-frontend' ) );
	}

	/**
	 * Enqueue frontend assets for a standalone admin preview.
	 *
	 * @param string              $screen   Screen key.
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	public function enqueue_gateway_preview_assets( $screen, $settings ) {
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
						'showPassword'      => __( 'Show password', 'alynt-account-gateway' ),
						'hidePassword'      => __( 'Hide password', 'alynt-account-gateway' ),
						'passwordVisible'   => __( 'Password is visible.', 'alynt-account-gateway' ),
						'passwordHidden'    => __( 'Password is hidden.', 'alynt-account-gateway' ),
						'show'              => __( 'Show', 'alynt-account-gateway' ),
						'hide'              => __( 'Hide', 'alynt-account-gateway' ),
						'requirementMet'    => __( 'Met', 'alynt-account-gateway' ),
						'requirementNotMet' => __( 'Not met', 'alynt-account-gateway' ),
						/* translators: 1: number of password requirements met, 2: total password requirements. */
						'requirementsMet'   => __( '%1$d of %2$d requirements met.', 'alynt-account-gateway' ),
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
}
