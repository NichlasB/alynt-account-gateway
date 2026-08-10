<?php
/**
 * Public author privacy tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests public comment and review author privacy formatting.
 */
class PublicAuthorPrivacyTest extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		$GLOBALS['alynt_ag_test_options']   = array();
		$GLOBALS['alynt_ag_test_user_meta'] = array(
			'first_name' => 'Anna',
			'last_name'  => 'Miller',
		);
		$GLOBALS['alynt_ag_test_is_admin']  = false;
	}

	protected function tearDown(): void {
		unset(
			$GLOBALS['alynt_ag_test_options'],
			$GLOBALS['alynt_ag_test_user_meta'],
			$GLOBALS['alynt_ag_test_is_admin']
		);
		parent::tearDown();
	}

	public function test_default_format_outputs_first_name_and_last_initial() {
		$privacy = new ALYNT_AG_Public_Author_Privacy();
		$comment = (object) array( 'user_id' => 123 );

		$this->assertSame( 'Anna M.', $privacy->filter_comment_author( 'Anna Miller', 7, $comment ) );
	}

	public function test_wordpress_default_format_preserves_original_author() {
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'public_author_name_format' => 'wordpress_default',
		);

		$privacy = new ALYNT_AG_Public_Author_Privacy();
		$comment = (object) array( 'user_id' => 123 );

		$this->assertSame( 'Anna Miller', $privacy->filter_comment_author( 'Anna Miller', 7, $comment ) );
	}

	public function test_first_name_format_outputs_first_name_only() {
		$GLOBALS['alynt_ag_test_options']['alynt_ag_settings'] = array(
			'public_author_name_format' => 'first_name',
		);

		$privacy = new ALYNT_AG_Public_Author_Privacy();
		$comment = (object) array( 'user_id' => 123 );

		$this->assertSame( 'Anna', $privacy->filter_comment_author( 'Anna Miller', 7, $comment ) );
	}

	public function test_frontend_insert_data_uses_privacy_safe_author_for_logged_in_user() {
		$privacy = new ALYNT_AG_Public_Author_Privacy();

		$data = $privacy->filter_insert_comment_data(
			array( 'comment_author' => 'Anna Miller' ),
			array( 'user_id' => 123 )
		);

		$this->assertSame( 'Anna M.', $data['comment_author'] );
	}

	public function test_admin_context_preserves_original_author() {
		$GLOBALS['alynt_ag_test_is_admin'] = true;

		$privacy = new ALYNT_AG_Public_Author_Privacy();
		$comment = (object) array( 'user_id' => 123 );

		$this->assertSame( 'Anna Miller', $privacy->filter_comment_author( 'Anna Miller', 7, $comment ) );
	}
}
