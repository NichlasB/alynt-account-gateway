<?php
/**
 * Production class loader for unit tests.
 *
 * @package Alynt_Account_Gateway
 */

require_once ALYNT_AG_PLUGIN_DIR . 'includes/class-settings-schema.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/class-database.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/class-deactivator.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/class-diagnostics-logger.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/class-retention-cleanup.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/class-i18n.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-client-ip.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-rate-limiter.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-return-destination.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-email-template-service.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-auth-service.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-registration-service.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-reoon-client.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-turnstile-client.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-webhook-dispatcher.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-dashboard-service.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-woocommerce-integration.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-frontend-routes.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-woocommerce-checkout-gate.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-frontend-assets.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-frontend-branding.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-frontend-components.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-frontend-messages.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-dashboard-navigation-renderer.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-dashboard-endpoint-metadata.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-dashboard-endpoint-renderer.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-dashboard-commerce-renderer.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-dashboard-account-renderer.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-frontend-dashboard-screen.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-frontend-register-screen.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-frontend-login-screen.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-frontend-lostpassword-screen.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-frontend-setpassword-screen.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-frontend-logout-screen.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-frontend-state-screens.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-frontend-gateway-shell.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-frontend-document-renderer.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-compatibility-warnings.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-privacy-exporter.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-privacy-eraser.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/services/class-privacy-service.php';
require_once ALYNT_AG_PLUGIN_DIR . 'public/class-frontend.php';
require_once ALYNT_AG_PLUGIN_DIR . 'includes/class-plugin.php';
