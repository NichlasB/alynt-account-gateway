<?php
/**
 * Settings page email-tools component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused email-tools behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Email_Tools extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render email preview and test-send tools.
	 *
	 * @return void
	 */
	public function render_email_tools() {
		$email_service = new ALYNT_AG_Email_Template_Service();
		$templates     = $email_service->templates();
		$settings      = ALYNT_AG_Settings_Schema::get_settings();
		?>
		<h2><?php esc_html_e( 'Email Preview And Test Send', 'alynt-account-gateway' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Use the saved template settings with sample account tokens before sending real account emails.', 'alynt-account-gateway' ); ?>
		</p>
		<div class="notice notice-warning inline alynt-ag-email-save-state" data-alynt-ag-email-save-state role="status" aria-live="polite" hidden>
			<p>
				<strong><?php esc_html_e( 'You have unsaved email changes.', 'alynt-account-gateway' ); ?></strong>
				<?php esc_html_e( 'Save Settings before previewing, sending a test email, or leaving this page.', 'alynt-account-gateway' ); ?>
			</p>
		</div>

		<div class="alynt-ag-email-tools">
			<?php $this->render_email_template_reference( $templates ); ?>
			<?php $this->render_email_token_reference( $email_service ); ?>

			<div class="alynt-ag-email-actions">
				<form method="get" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" target="_blank" class="alynt-ag-inline-tool alynt-ag-email-action" data-alynt-ag-requires-saved-email-settings data-alynt-ag-action-form>
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

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="alynt-ag-inline-tool alynt-ag-email-action" data-alynt-ag-requires-saved-email-settings data-alynt-ag-action-form>
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
	public function render_email_template_reference( $templates ) {
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
	public function render_email_token_reference( $email_service ) {
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
	public function email_template_reference() {
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
}
