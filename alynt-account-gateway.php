<?php
/**
 * Plugin Name:       Alynt Account Gateway
 * Description:       Branded account gateway for WordPress login, registration, password flows, emails, dashboards, WooCommerce account handling, and integrations.
 * Version:           0.1.82
 * Author:            Alynt
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       alynt-account-gateway
 * Domain Path:       /languages
 * GitHub Plugin URI: NichlasB/alynt-account-gateway
 * Requires at least: 6.0
 * Requires PHP:      7.4
 *
 * @package Alynt_Account_Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ALYNT_AG_VERSION', '0.1.82' );
define( 'ALYNT_AG_PLUGIN_FILE', __FILE__ );
define( 'ALYNT_AG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ALYNT_AG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ALYNT_AG_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'ALYNT_AG_TEXT_DOMAIN', 'alynt-account-gateway' );

require_once ALYNT_AG_PLUGIN_DIR . 'includes/class-loader.php';

register_activation_hook( __FILE__, array( 'ALYNT_AG_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'ALYNT_AG_Deactivator', 'deactivate' ) );

/**
 * Start the plugin.
 *
 * @return void
 */
function alynt_ag_run() {
	$plugin = new ALYNT_AG_Plugin();
	$plugin->run();
}

alynt_ag_run();
