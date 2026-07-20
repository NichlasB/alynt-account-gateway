<?php
/**
 * Settings page security-review-ui component.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Owns focused security-review-ui behavior for the settings page.
 */
class ALYNT_AG_Settings_Page_Security_Review_Ui extends ALYNT_AG_Settings_Page_Component {

	/**
	 * Render an admin review action or recorded decision for a verification row.
	 *
	 * @param object $log Verification log row.
	 * @return void
	 */
	public function render_security_review_action( $log ) {
		if ( ! $this->is_security_reoon_reviewable( $log ) ) {
			echo esc_html__( 'Not required', 'alynt-account-gateway' );
			return;
		}

		$decision = isset( $log->review_decision ) ? sanitize_key( $log->review_decision ) : '';
		if ( in_array( $decision, array( 'legitimate', 'monitor' ), true ) ) {
			?>
			<span class="alynt-ag-security-review__recorded">
				<?php echo esc_html( $this->security_review_decision_label( $decision ) ); ?>
			</span>
			<?php if ( ! empty( $log->reviewed_at ) ) : ?>
				<small><?php echo esc_html( $log->reviewed_at ); ?></small>
			<?php endif; ?>
			<?php
			return;
		}

		$log_id = isset( $log->id ) ? absint( $log->id ) : 0;
		if ( ! $log_id ) {
			echo esc_html__( 'Unavailable', 'alynt-account-gateway' );
			return;
		}
		?>
		<form
			method="post"
			action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
			class="alynt-ag-security-review__form"
			data-alynt-ag-action-form
			data-alynt-ag-confirm="<?php esc_attr_e( 'Record this review decision for the verification event?', 'alynt-account-gateway' ); ?>"
		>
			<input type="hidden" name="action" value="alynt_ag_review_verification">
			<input type="hidden" name="log_id" value="<?php echo esc_attr( (string) $log_id ); ?>">
			<?php wp_nonce_field( 'alynt_ag_review_verification_' . $log_id ); ?>
			<label class="screen-reader-text" for="alynt-ag-review-decision-<?php echo esc_attr( (string) $log_id ); ?>">
				<?php esc_html_e( 'Review decision', 'alynt-account-gateway' ); ?>
			</label>
			<select id="alynt-ag-review-decision-<?php echo esc_attr( (string) $log_id ); ?>" name="decision">
				<option value="legitimate"><?php esc_html_e( 'Legitimate signup', 'alynt-account-gateway' ); ?></option>
				<option value="monitor"><?php esc_html_e( 'Monitor pattern', 'alynt-account-gateway' ); ?></option>
			</select>
			<button type="submit" class="button button-small"><?php esc_html_e( 'Record review', 'alynt-account-gateway' ); ?></button>
		</form>
		<?php
	}

	/**
	 * Return whether a verification row supports a manual review decision.
	 *
	 * @param object $log Verification log row.
	 * @return bool
	 */
	public function is_security_reoon_reviewable( $log ) {
		$provider = isset( $log->provider ) ? sanitize_key( $log->provider ) : '';
		$status   = isset( $log->status ) ? sanitize_key( $log->status ) : '';

		return 'reoon' === $provider && empty( $log->blocked ) && $this->status_has_suffix( $status, '_flagged' );
	}

	/**
	 * Return the label for a stored review decision.
	 *
	 * @param string $decision Review decision key.
	 * @return string
	 */
	public function security_review_decision_label( $decision ) {
		return 'monitor' === sanitize_key( $decision )
			? __( 'Monitor pattern', 'alynt-account-gateway' )
			: __( 'Legitimate signup', 'alynt-account-gateway' );
	}

	/**
	 * Return whether a status string ends with a suffix.
	 *
	 * @param string $status Status string.
	 * @param string $suffix Suffix to test.
	 * @return bool
	 */
	public function status_has_suffix( $status, $suffix ) {
		if ( '' === $suffix ) {
			return true;
		}

		return substr( $status, -strlen( $suffix ) ) === $suffix;
	}

	/**
	 * Return recent pending registration records.
	 *
	 * @param int $limit Maximum records.
	 * @return array<int,object>|WP_Error
	 */
	public function security_recent_pending_registrations( $limit = 10 ) {
		global $wpdb;

		$tables = ALYNT_AG_Database::tables();
		$limit  = min( 25, max( 1, absint( $limit ) ) );

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Admin security viewer reads plugin-owned pending registration table.
		$registrations = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT email, user_id, status, expires_at, created_at, confirmed_at FROM {$tables['pending_registrations']} ORDER BY created_at DESC, id DESC LIMIT %d",
				$limit
			)
		);
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		return is_array( $registrations )
			? $registrations
			: new WP_Error(
				'alynt_ag_pending_registrations_read_failed',
				__( 'Recent pending registrations could not be loaded. Refresh the page and check the database connection if the problem continues.', 'alynt-account-gateway' )
			);
	}

	/**
	 * Return a pending registration status descriptor.
	 *
	 * @param object $registration Pending registration row.
	 * @return array{key:string,label:string}
	 */
	public function security_pending_registration_status( $registration ) {
		$status     = isset( $registration->status ) ? sanitize_key( $registration->status ) : 'pending';
		$expires_at = isset( $registration->expires_at ) ? strtotime( (string) $registration->expires_at ) : false;
		$now        = strtotime( current_time( 'mysql' ) );

		if ( in_array( $status, array( 'pending', 'email_confirmed' ), true ) && $expires_at && $now && $expires_at < $now ) {
			return array(
				'key'   => 'expired',
				'label' => __( 'Expired', 'alynt-account-gateway' ),
			);
		}

		if ( 'email_confirmed' === $status ) {
			return array(
				'key'   => 'email-confirmed',
				'label' => __( 'Email Confirmed', 'alynt-account-gateway' ),
			);
		}

		if ( 'completed' === $status ) {
			return array(
				'key'   => 'completed',
				'label' => __( 'Completed', 'alynt-account-gateway' ),
			);
		}

		return array(
			'key'   => 'pending',
			'label' => __( 'Pending', 'alynt-account-gateway' ),
		);
	}

	/**
	 * Return admin guidance for a pending registration status.
	 *
	 * @param string $status_key Pending registration status key.
	 * @return string
	 */
	public function security_pending_registration_guidance( $status_key ) {
		$status_key = sanitize_key( $status_key );

		if ( 'expired' === $status_key ) {
			return __( 'The confirmation window has expired. The customer can request a fresh confirmation email from the invalid-link screen.', 'alynt-account-gateway' );
		}

		if ( 'email-confirmed' === $status_key ) {
			return __( 'Email is confirmed. The customer still needs to set a password before the record expires.', 'alynt-account-gateway' );
		}

		if ( 'completed' === $status_key ) {
			return __( 'Account creation is complete. No resend action is needed.', 'alynt-account-gateway' );
		}

		return __( 'Waiting for email confirmation. Resend requests are throttled by the configured resend-confirmation limit.', 'alynt-account-gateway' );
	}

	/**
	 * Return a masked email for admin table display.
	 *
	 * @param string $email Email address.
	 * @return string
	 */
	public function mask_email_for_display( $email ) {
		$email = sanitize_email( $email );

		if ( ! $email || false === strpos( $email, '@' ) ) {
			return '';
		}

		list( $local, $domain ) = explode( '@', $email, 2 );
		$first                  = '' !== $local ? substr( $local, 0, 1 ) : '*';

		return $first . '***@' . $domain;
	}

	/**
	 * Return a readable provider label.
	 *
	 * @param string $provider Provider key.
	 * @return string
	 */
	public function security_provider_label( $provider ) {
		$provider = sanitize_key( $provider );

		if ( 'turnstile' === $provider ) {
			return __( 'Turnstile', 'alynt-account-gateway' );
		}

		if ( 'reoon' === $provider ) {
			return __( 'Reoon Email Verifier', 'alynt-account-gateway' );
		}

		if ( 'rate_limit' === $provider ) {
			return __( 'Rate Limit', 'alynt-account-gateway' );
		}

		if ( 'registration_flow' === $provider ) {
			return __( 'Registration Flow', 'alynt-account-gateway' );
		}

		return $provider;
	}
}
