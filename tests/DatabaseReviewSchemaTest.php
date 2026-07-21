<?php
/**
 * Verification review schema tests.
 *
 * @package Alynt_Account_Gateway
 */

use PHPUnit\Framework\TestCase;

require_once ALYNT_AG_PLUGIN_DIR . 'includes/class-database.php';

/**
 * Covers the persistent review metadata added to verification logs.
 */
class DatabaseReviewSchemaTest extends TestCase {

	public function test_verification_log_schema_includes_review_metadata() {
		$source = file_get_contents( ALYNT_AG_PLUGIN_DIR . 'includes/class-database.php' );

		$this->assertSame( '0.1.8', ALYNT_AG_Database::DB_VERSION );
		$this->assertStringContainsString( 'KEY token_hash (token_hash)', $source );
		$this->assertStringContainsString( 'return_path text NULL', $source );
		$this->assertStringContainsString( "review_decision varchar(40) NOT NULL DEFAULT ''", $source );
		$this->assertStringContainsString( 'reviewed_by bigint(20) unsigned NOT NULL DEFAULT 0', $source );
		$this->assertStringContainsString( 'reviewed_at datetime NULL', $source );
		$this->assertStringContainsString( 'KEY review_decision (review_decision)', $source );
		$this->assertStringContainsString( 'KEY reviewed_at (reviewed_at)', $source );
		$this->assertStringContainsString( 'KEY success_created_at (success, created_at)', $source );
		$this->assertStringContainsString( 'KEY created_at_id (created_at, id)', $source );
		$this->assertStringContainsString( 'KEY category_created_at (category, created_at, id)', $source );
		$this->assertStringContainsString( 'SHOW TABLES LIKE %s', $source );
		$this->assertStringContainsString( "update_option( 'alynt_ag_db_version', self::DB_VERSION )", $source );
	}
}
