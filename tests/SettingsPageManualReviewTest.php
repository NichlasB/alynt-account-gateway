<?php
/**
 * Focused structural test suite.
 *
 * @package Alynt_Account_Gateway
 */

require_once __DIR__ . '/support/class-settings-page-security-status-test-case.php';

/**
 * Tests manual security review queues and decisions.
 */
class SettingsPageManualReviewTest extends SettingsPageSecurityStatusTestCase {

	public function test_security_manual_review_queue_counts_allowed_and_blocked_reoon_flags() {
		$settings_page = new ALYNT_AG_Settings_Page();
		$items         = $this->invoke_helper(
			$settings_page,
			'security_manual_review_queue_items',
			array(
				array(
					(object) array(
						'provider' => 'reoon',
						'status'   => 'role_account_flagged',
						'blocked'  => 0,
					),
					(object) array(
						'provider' => 'reoon',
						'status'   => 'catch_all_flagged',
						'blocked'  => 0,
					),
					(object) array(
						'provider' => 'reoon',
						'status'   => 'unknown_flagged',
						'blocked'  => 0,
					),
					(object) array(
						'provider'        => 'reoon',
						'status'          => 'inbox_full_flagged',
						'blocked'         => 0,
						'review_decision' => 'monitor',
					),
					(object) array(
						'provider' => 'reoon',
						'status'   => 'role_account_flagged_blocked',
						'blocked'  => 1,
					),
					(object) array(
						'provider' => 'reoon',
						'status'   => 'alynt_ag_reoon_blocked',
						'blocked'  => 1,
					),
					(object) array(
						'provider' => 'rate_limit',
						'status'   => 'registration_rate_limited',
						'blocked'  => 1,
					),
				),
			)
		);

		$this->assertSame( 'Allowed Flagged Results', $items[0]['label'] );
		$this->assertSame( 3, $items[0]['count'] );
		$this->assertSame( 'warning', $items[0]['status'] );
		$this->assertSame( 'Role Account Reviews', $items[1]['label'] );
		$this->assertSame( 1, $items[1]['count'] );
		$this->assertSame( 'Catch-All And Unknown Reviews', $items[2]['label'] );
		$this->assertSame( 2, $items[2]['count'] );
		$this->assertSame( 'Blocked Flagged Results', $items[3]['label'] );
		$this->assertSame( 1, $items[3]['count'] );
		$this->assertSame( 'warning', $items[3]['status'] );
	}

	public function test_security_review_action_renders_form_and_recorded_decision() {
		$settings_page = new ALYNT_AG_Settings_Page();

		ob_start();
		$this->invoke_helper(
			$settings_page,
			'render_security_review_action',
			array(
				(object) array(
					'id'              => 41,
					'provider'        => 'reoon',
					'status'          => 'catch_all_flagged',
					'blocked'         => 0,
					'review_decision' => '',
				),
			)
		);
		$pending_output = ob_get_clean();

		$this->assertStringContainsString( 'alynt_ag_review_verification', $pending_output );
		$this->assertStringContainsString( 'Legitimate signup', $pending_output );
		$this->assertStringContainsString( 'Monitor pattern', $pending_output );
		$this->assertStringContainsString( 'Record review', $pending_output );

		ob_start();
		$this->invoke_helper(
			$settings_page,
			'render_security_review_action',
			array(
				(object) array(
					'id'              => 41,
					'provider'        => 'reoon',
					'status'          => 'catch_all_flagged',
					'blocked'         => 0,
					'review_decision' => 'monitor',
					'reviewed_at'     => '2026-07-12 15:00:00',
				),
			)
		);
		$reviewed_output = ob_get_clean();

		$this->assertStringContainsString( 'Monitor pattern', $reviewed_output );
		$this->assertStringContainsString( '2026-07-12 15:00:00', $reviewed_output );
		$this->assertStringNotContainsString( 'Record review', $reviewed_output );
	}

	public function test_review_verification_handler_records_eligible_decision_and_audit_event() {
		$tables = ALYNT_AG_Database::tables();
		$GLOBALS['alynt_ag_test_db_results'][ $tables['verification_logs'] ] = array(
			(object) array(
				'id'       => 41,
				'provider' => 'reoon',
				'status'   => 'role_account_flagged',
				'blocked'  => 0,
			),
		);
		$GLOBALS['alynt_ag_test_user_caps']          = array( 'manage_options' );
		$GLOBALS['alynt_ag_test_current_user_id']    = 7;
		$GLOBALS['alynt_ag_test_throw_on_redirect']  = true;
		$_POST = array(
			'log_id'   => '41',
			'decision' => 'legitimate',
		);

		$settings_page = new ALYNT_AG_Settings_Page();

		try {
			$settings_page->handle_review_verification();
			$this->fail( 'Expected redirect exception.' );
		} catch ( RuntimeException $exception ) {
			$this->assertStringContainsString( 'verification_review_recorded', $exception->getMessage() );
		}

		$this->assertSame( $tables['verification_logs'], $GLOBALS['alynt_ag_test_db_updates'][0]['table'] );
		$this->assertSame( 'legitimate', $GLOBALS['alynt_ag_test_db_updates'][0]['data']['review_decision'] );
		$this->assertSame( 7, $GLOBALS['alynt_ag_test_db_updates'][0]['data']['reviewed_by'] );
		$this->assertSame( 41, $GLOBALS['alynt_ag_test_db_updates'][0]['where']['id'] );
		$this->assertSame( '', $GLOBALS['alynt_ag_test_db_updates'][0]['where']['review_decision'] );
		$this->assertSame( $tables['audit_logs'], $GLOBALS['alynt_ag_test_db_inserts'][0]['table'] );
		$this->assertSame( 'reoon_review_recorded', $GLOBALS['alynt_ag_test_db_inserts'][0]['data']['action'] );
	}

	public function test_record_security_review_decision_rejects_blocked_or_unknown_rows() {
		$tables = ALYNT_AG_Database::tables();
		$GLOBALS['alynt_ag_test_db_results'][ $tables['verification_logs'] ] = array(
			(object) array(
				'id'       => 52,
				'provider' => 'reoon',
				'status'   => 'role_account_flagged_blocked',
				'blocked'  => 1,
			),
		);

		$settings_page = new ALYNT_AG_Settings_Page();

		$this->assertFalse( $this->invoke_helper( $settings_page, 'record_security_review_decision', array( 52, 'legitimate', 7 ) ) );
		$this->assertFalse( $this->invoke_helper( $settings_page, 'record_security_review_decision', array( 52, 'unknown', 7 ) ) );

		$GLOBALS['alynt_ag_test_db_results'][ $tables['verification_logs'] ] = array(
			(object) array(
				'id'              => 53,
				'provider'        => 'reoon',
				'status'          => 'catch_all_flagged',
				'blocked'         => 0,
				'review_decision' => 'monitor',
			),
		);
		$this->assertFalse( $this->invoke_helper( $settings_page, 'record_security_review_decision', array( 53, 'legitimate', 7 ) ) );
		$this->assertSame( array(), $GLOBALS['alynt_ag_test_db_updates'] );
	}
}
