<?php
/**
 * Frontend gateway shell renderer.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the branded auth shell and dispatches auth screen content.
 */
class ALYNT_AG_Frontend_Gateway_Shell {

	/**
	 * Branding helper.
	 *
	 * @var ALYNT_AG_Frontend_Branding|null
	 */
	private $branding;

	/**
	 * Login screen helper.
	 *
	 * @var ALYNT_AG_Frontend_Login_Screen|null
	 */
	private $login_screen;

	/**
	 * Registration screen helper.
	 *
	 * @var ALYNT_AG_Frontend_Register_Screen|null
	 */
	private $register_screen;

	/**
	 * Lost-password screen helper.
	 *
	 * @var ALYNT_AG_Frontend_Lostpassword_Screen|null
	 */
	private $lostpassword_screen;

	/**
	 * Set-password screen helper.
	 *
	 * @var ALYNT_AG_Frontend_Setpassword_Screen|null
	 */
	private $setpassword_screen;

	/**
	 * Logout screen helper.
	 *
	 * @var ALYNT_AG_Frontend_Logout_Screen|null
	 */
	private $logout_screen;

	/**
	 * State screen helpers.
	 *
	 * @var ALYNT_AG_Frontend_State_Screens|null
	 */
	private $state_screens;

	/**
	 * Constructor.
	 *
	 * @param ALYNT_AG_Frontend_Branding|null            $branding            Branding helper.
	 * @param ALYNT_AG_Frontend_Login_Screen|null        $login_screen        Login screen helper.
	 * @param ALYNT_AG_Frontend_Register_Screen|null     $register_screen     Registration screen helper.
	 * @param ALYNT_AG_Frontend_Lostpassword_Screen|null $lostpassword_screen Lost-password screen helper.
	 * @param ALYNT_AG_Frontend_Setpassword_Screen|null  $setpassword_screen  Set-password screen helper.
	 * @param ALYNT_AG_Frontend_Logout_Screen|null       $logout_screen       Logout screen helper.
	 * @param ALYNT_AG_Frontend_State_Screens|null       $state_screens       State screen helpers.
	 */
	public function __construct(
		$branding = null,
		$login_screen = null,
		$register_screen = null,
		$lostpassword_screen = null,
		$setpassword_screen = null,
		$logout_screen = null,
		$state_screens = null
	) {
		$this->branding            = $branding;
		$this->login_screen        = $login_screen;
		$this->register_screen     = $register_screen;
		$this->lostpassword_screen = $lostpassword_screen;
		$this->setpassword_screen  = $setpassword_screen;
		$this->logout_screen       = $logout_screen;
		$this->state_screens       = $state_screens;
	}

	/**
	 * Render gateway shell.
	 *
	 * @param string              $screen   Screen key.
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	public function render_gateway_shell( $screen, $settings ) {
		$style = $this->branding()->style_attribute( $settings );
		$dir   = is_rtl() ? 'rtl' : 'ltr';
		?>
		<main class="alynt-ag-gateway" data-agw-screen="<?php echo esc_attr( $screen ); ?>" dir="<?php echo esc_attr( $dir ); ?>" style="<?php echo esc_attr( $style ); ?>">
			<section class="agw-shell" aria-labelledby="agw-screen-title">
				<div class="agw-media" aria-hidden="true">
					<?php $this->branding()->render_media_panel( $settings ); ?>
				</div>
				<div class="agw-panel">
					<div class="agw-card">
						<?php $this->branding()->render_brand_block( $settings ); ?>
						<?php $this->render_screen( $screen, $settings ); ?>
					</div>
				</div>
			</section>
		</main>
		<?php
	}

	/**
	 * Render the set-password shell for admin preview without requiring a live token.
	 *
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	public function render_gateway_shell_with_password_preview( $settings ) {
		$style = $this->branding()->style_attribute( $settings );
		$dir   = is_rtl() ? 'rtl' : 'ltr';
		?>
		<main class="alynt-ag-gateway" data-agw-screen="setpassword" dir="<?php echo esc_attr( $dir ); ?>" style="<?php echo esc_attr( $style ); ?>">
			<section class="agw-shell" aria-labelledby="agw-screen-title">
				<div class="agw-media" aria-hidden="true">
					<?php $this->branding()->render_media_panel( $settings ); ?>
				</div>
				<div class="agw-panel">
					<div class="agw-card">
						<?php $this->branding()->render_brand_block( $settings ); ?>
						<?php
						$this->setpassword_screen()->render_password_form(
							$settings,
							home_url( $settings['account_action_base'] ),
							'reset_password',
							'alynt_ag_reset_password',
							'alynt_ag_auth_nonce',
							array(
								'key'   => 'preview-key',
								'login' => 'preview@example.test',
							),
							''
						);
						?>
					</div>
				</div>
			</section>
		</main>
		<?php
	}

	/**
	 * Render one screen inside the gateway shell.
	 *
	 * @param string              $screen   Screen key.
	 * @param array<string,mixed> $settings Settings.
	 * @return void
	 */
	private function render_screen( $screen, $settings ) {
		switch ( $screen ) {
			case 'register':
				$this->register_screen()->render_register_screen( $settings );
				break;
			case 'lostpassword':
				$this->lostpassword_screen()->render_lostpassword_screen( $settings );
				break;
			case 'setpassword':
				$this->setpassword_screen()->render_setpassword_screen( $settings );
				break;
			case 'logout':
				$this->logout_screen()->render_logout_screen( $settings );
				break;
			case 'registration_disabled':
				$this->state_screens()->render_registration_disabled_screen( $settings );
				break;
			case 'invalidlink':
				$this->state_screens()->render_invalid_link_screen( $settings );
				break;
			case 'login':
			default:
				$this->login_screen()->render_login_screen( $settings );
				break;
		}
	}

	/**
	 * Return a shell collaborator, creating it only for the active screen.
	 *
	 * @param string $property   Property name.
	 * @param string $class_name Class name.
	 * @return object
	 */
	private function collaborator( $property, $class_name ) {
		if ( null === $this->{$property} ) {
			$this->{$property} = new $class_name();
		}

		return $this->{$property};
	}

	/**
	 * Return the branding collaborator.
	 *
	 * @return ALYNT_AG_Frontend_Branding
	 */
	private function branding() {
		return $this->collaborator( 'branding', 'ALYNT_AG_Frontend_Branding' );
	}

	/**
	 * Return the login screen collaborator.
	 *
	 * @return ALYNT_AG_Frontend_Login_Screen
	 */
	private function login_screen() {
		return $this->collaborator( 'login_screen', 'ALYNT_AG_Frontend_Login_Screen' );
	}

	/**
	 * Return the registration screen collaborator.
	 *
	 * @return ALYNT_AG_Frontend_Register_Screen
	 */
	private function register_screen() {
		return $this->collaborator( 'register_screen', 'ALYNT_AG_Frontend_Register_Screen' );
	}

	/**
	 * Return the lost-password screen collaborator.
	 *
	 * @return ALYNT_AG_Frontend_Lostpassword_Screen
	 */
	private function lostpassword_screen() {
		return $this->collaborator( 'lostpassword_screen', 'ALYNT_AG_Frontend_Lostpassword_Screen' );
	}

	/**
	 * Return the set-password screen collaborator.
	 *
	 * @return ALYNT_AG_Frontend_Setpassword_Screen
	 */
	private function setpassword_screen() {
		return $this->collaborator( 'setpassword_screen', 'ALYNT_AG_Frontend_Setpassword_Screen' );
	}

	/**
	 * Return the logout screen collaborator.
	 *
	 * @return ALYNT_AG_Frontend_Logout_Screen
	 */
	private function logout_screen() {
		return $this->collaborator( 'logout_screen', 'ALYNT_AG_Frontend_Logout_Screen' );
	}

	/**
	 * Return the state-screen collaborator.
	 *
	 * @return ALYNT_AG_Frontend_State_Screens
	 */
	private function state_screens() {
		return $this->collaborator( 'state_screens', 'ALYNT_AG_Frontend_State_Screens' );
	}
}
