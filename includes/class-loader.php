<?php
/**
 * Loads plugin classes.
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$alynt_ag_files = array(
	'includes/class-settings-schema.php',
	'includes/class-database.php',
	'includes/class-diagnostics-logger.php',
	'includes/class-retention-cleanup.php',
	'includes/class-i18n.php',
	'includes/class-activator.php',
	'includes/class-deactivator.php',
	'includes/services/class-rate-limiter.php',
	'includes/services/class-auth-service.php',
	'includes/services/class-registration-service.php',
	'includes/services/class-email-template-service.php',
	'includes/services/class-turnstile-client.php',
	'includes/services/class-reoon-client.php',
	'includes/services/class-webhook-dispatcher.php',
	'includes/services/class-dashboard-service.php',
	'includes/services/class-woocommerce-integration.php',
	'includes/services/class-frontend-routes.php',
	'includes/services/class-frontend-assets.php',
	'includes/services/class-frontend-branding.php',
	'includes/services/class-compatibility-warnings.php',
	'includes/services/class-privacy-service.php',
	'includes/services/class-frontend-messages.php',
	'admin/class-admin.php',
	'admin/class-settings-page.php',
	'public/class-frontend.php',
	'includes/class-plugin.php',
);

foreach ( $alynt_ag_files as $alynt_ag_file ) {
	require_once ALYNT_AG_PLUGIN_DIR . $alynt_ag_file;
}
