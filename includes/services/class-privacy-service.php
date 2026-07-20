<?php
/**
 * Privacy service.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers privacy hooks.
 */
class ALYNT_AG_Privacy_Service {

	/**
	 * Personal data exporter.
	 *
	 * @var ALYNT_AG_Privacy_Exporter|null
	 */
	private $exporter;

	/**
	 * Personal data eraser.
	 *
	 * @var ALYNT_AG_Privacy_Eraser|null
	 */
	private $eraser;

	/**
	 * Constructor.
	 *
	 * @param ALYNT_AG_Privacy_Exporter|null $exporter Personal data exporter.
	 * @param ALYNT_AG_Privacy_Eraser|null   $eraser   Personal data eraser.
	 */
	public function __construct( $exporter = null, $eraser = null ) {
		$this->exporter = $exporter;
		$this->eraser   = $eraser;
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_init', array( $this, 'add_privacy_policy_content' ) );
		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporter' ) );
		add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ) );
	}

	/**
	 * Add privacy policy helper content.
	 *
	 * @return void
	 */
	public function add_privacy_policy_content() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}

		wp_add_privacy_policy_content(
			__( 'Alynt Account Gateway', 'alynt-account-gateway' ),
			wp_kses_post(
				__( 'Alynt Account Gateway may process account registration data, email verification results, webhook delivery metadata, and consent records. Site owners should disclose configured third-party services such as Cloudflare Turnstile, Reoon Email Verifier, and outgoing webhooks.', 'alynt-account-gateway' )
			)
		);
	}

	/**
	 * Register personal data exporter.
	 *
	 * @param array<string,mixed> $exporters Exporters.
	 * @return array<string,mixed>
	 */
	public function register_exporter( $exporters ) {
		$exporters['alynt-account-gateway'] = array(
			'exporter_friendly_name' => __( 'Alynt Account Gateway', 'alynt-account-gateway' ),
			'callback'               => array( $this, 'export_personal_data' ),
		);

		return $exporters;
	}

	/**
	 * Register personal data eraser.
	 *
	 * @param array<string,mixed> $erasers Erasers.
	 * @return array<string,mixed>
	 */
	public function register_eraser( $erasers ) {
		$erasers['alynt-account-gateway'] = array(
			'eraser_friendly_name' => __( 'Alynt Account Gateway', 'alynt-account-gateway' ),
			'callback'             => array( $this, 'erase_personal_data' ),
		);

		return $erasers;
	}

	/**
	 * Record registration consent.
	 *
	 * @param string              $email    Email address.
	 * @param array<string,mixed> $settings Settings.
	 * @param int                 $user_id  User ID.
	 * @return bool
	 */
	public function record_registration_consent( $email, $settings, $user_id = 0 ) {
		global $wpdb;

		$email = sanitize_email( $email );
		if ( ! is_email( $email ) ) {
			return false;
		}

		$terms_path    = isset( $settings['terms_path'] ) ? sanitize_text_field( $settings['terms_path'] ) : '';
		$privacy_path  = isset( $settings['privacy_path'] ) ? sanitize_text_field( $settings['privacy_path'] ) : '';
		$tables        = ALYNT_AG_Database::tables();
		$settings_hash = hash(
			'sha256',
			wp_json_encode(
				array(
					'terms_path'   => $terms_path,
					'privacy_path' => $privacy_path,
				)
			)
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Plugin-owned consent table.
		return (bool) $wpdb->insert(
			$tables['consent_records'],
			array(
				'user_id'         => absint( $user_id ),
				'email'           => $email,
				'terms_path'      => $terms_path,
				'privacy_path'    => $privacy_path,
				'context'         => 'registration',
				'consent_version' => ALYNT_AG_VERSION,
				'settings_hash'   => $settings_hash,
				'created_at'      => current_time( 'mysql', true ),
			),
			array( '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);
	}

	/**
	 * Attach pending consent records to a created user.
	 *
	 * @param string $email   Email address.
	 * @param int    $user_id User ID.
	 * @return bool
	 */
	public function attach_registration_consent_to_user( $email, $user_id ) {
		global $wpdb;

		$tables = ALYNT_AG_Database::tables();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Plugin-owned consent table.
		return false !== $wpdb->update(
			$tables['consent_records'],
			array( 'user_id' => absint( $user_id ) ),
			array(
				'email'   => sanitize_email( $email ),
				'user_id' => 0,
				'context' => 'registration',
			),
			array( '%d' ),
			array( '%s', '%d', '%s' )
		);
	}

	/**
	 * Export personal data stored by the plugin.
	 *
	 * @param string $email_address Email address.
	 * @param int    $page          Page number.
	 * @return array<string,mixed>
	 */
	public function export_personal_data( $email_address, $page = 1 ) {
		return $this->get_exporter()->export_personal_data( $email_address, $page );
	}

	/**
	 * Erase personal data stored by the plugin.
	 *
	 * @param string $email_address Email address.
	 * @param int    $page          Page number.
	 * @return array<string,mixed>
	 */
	public function erase_personal_data( $email_address, $page = 1 ) {
		return $this->get_eraser()->erase_personal_data( $email_address, $page );
	}

	/**
	 * Return the personal data exporter.
	 *
	 * @return ALYNT_AG_Privacy_Exporter
	 */
	private function get_exporter() {
		if ( null === $this->exporter ) {
			$this->exporter = new ALYNT_AG_Privacy_Exporter();
		}

		return $this->exporter;
	}

	/**
	 * Return the personal data eraser.
	 *
	 * @return ALYNT_AG_Privacy_Eraser
	 */
	private function get_eraser() {
		if ( null === $this->eraser ) {
			$this->eraser = new ALYNT_AG_Privacy_Eraser();
		}

		return $this->eraser;
	}
}
