<?php
/**
 * Test bootstrap.
 *
 * @package Alynt_Account_Gateway
 */

define( 'ALYNT_AG_TESTS', true );
define( 'ABSPATH', dirname( __DIR__ ) . '/' );
define( 'ALYNT_AG_PLUGIN_DIR', dirname( __DIR__ ) . '/' );
define( 'ALYNT_AG_PLUGIN_URL', 'https://example.test/wp-content/plugins/alynt-account-gateway/' );
define( 'ALYNT_AG_PLUGIN_BASENAME', 'alynt-account-gateway/alynt-account-gateway.php' );
define( 'ALYNT_AG_TEXT_DOMAIN', 'alynt-account-gateway' );
define( 'ALYNT_AG_VERSION', '0.1.0' );
define( 'HOUR_IN_SECONDS', 3600 );
define( 'MINUTE_IN_SECONDS', 60 );

$GLOBALS['alynt_ag_test_transients'] = array();
$GLOBALS['alynt_ag_test_mail'] = array();
$GLOBALS['alynt_ag_test_options'] = array();
$GLOBALS['alynt_ag_test_remote_posts'] = array();
$GLOBALS['alynt_ag_test_db_inserts'] = array();
$GLOBALS['alynt_ag_test_db_updates'] = array();
$GLOBALS['alynt_ag_test_db_deletes'] = array();
$GLOBALS['alynt_ag_test_db_rows'] = array();
$GLOBALS['alynt_ag_test_db_results'] = array();
$GLOBALS['alynt_ag_test_db_queries'] = array();
$GLOBALS['alynt_ag_test_filters'] = array();
$GLOBALS['alynt_ag_test_deleted_options'] = array();
$GLOBALS['alynt_ag_test_scheduled_hooks'] = array();
$GLOBALS['alynt_ag_test_unscheduled_events'] = array();
$GLOBALS['alynt_ag_test_cleared_hooks'] = array();
$GLOBALS['alynt_ag_test_redirects'] = array();
$GLOBALS['alynt_ag_test_signons'] = array();
$GLOBALS['alynt_ag_test_deleted_user_meta'] = array();
$GLOBALS['alynt_ag_test_created_users'] = array();
$GLOBALS['alynt_ag_test_user_updates'] = array();
$GLOBALS['alynt_ag_test_enqueued_styles'] = array();
$GLOBALS['alynt_ag_test_enqueued_scripts'] = array();
$GLOBALS['alynt_ag_test_localized_scripts'] = array();
$GLOBALS['alynt_ag_test_attachment_urls'] = array();

require_once __DIR__ . '/support/class-test-wpdb.php';

$GLOBALS['wpdb'] = new ALYNT_AG_Test_WPDB();

$autoload = ALYNT_AG_PLUGIN_DIR . 'vendor/autoload.php';
if ( file_exists( $autoload ) ) {
	require_once $autoload;
}

require_once __DIR__ . '/support/stubs/core.php';
require_once __DIR__ . '/support/stubs/woocommerce.php';
require_once __DIR__ . '/support/stubs/media.php';
require_once __DIR__ . '/support/stubs/http.php';
require_once __DIR__ . '/support/stubs/routing.php';
require_once __DIR__ . '/support/stubs/authentication.php';
require_once __DIR__ . '/support/stubs/sanitization.php';
require_once __DIR__ . '/support/stubs/options-hooks.php';
require_once __DIR__ . '/support/load-plugin.php';
require_once __DIR__ . '/support/settings-page-test-access.php';
