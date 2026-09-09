<?php
/**
 * Plugin Name:       Splendid Core
 * Plugin URI:        https://splendidglazing.co.uk/
 * Description:       Business data, enquiry handling, custom blocks, SEO output and the content importer for the Splendid Double Glazing website. Keeps durable data out of the theme so the design can be updated without losing enquiries or settings.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Author:            LaunchFlow
 * Author URI:        https://launchflow.uk/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       splendid-core
 *
 * @package Splendid_Core
 */

defined( 'ABSPATH' ) || exit;

define( 'SPLENDID_CORE_VERSION', '1.0.0' );
define( 'SPLENDID_CORE_FILE', __FILE__ );
define( 'SPLENDID_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'SPLENDID_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once SPLENDID_CORE_DIR . 'includes/data.php';
require_once SPLENDID_CORE_DIR . 'includes/fallbacks.php';
require_once SPLENDID_CORE_DIR . 'includes/settings.php';
require_once SPLENDID_CORE_DIR . 'includes/enquiry.php';
require_once SPLENDID_CORE_DIR . 'includes/leads.php';
require_once SPLENDID_CORE_DIR . 'includes/blocks.php';
require_once SPLENDID_CORE_DIR . 'includes/blocks-content.php';
require_once SPLENDID_CORE_DIR . 'includes/seo.php';
require_once SPLENDID_CORE_DIR . 'includes/importer.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once SPLENDID_CORE_DIR . 'includes/cli.php';
}

/**
 * Flush rewrite rules once on activation so imported page paths resolve.
 */
function splendid_core_activate() {
	splendid_leads_register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'splendid_core_activate' );

/**
 * Clean up scheduled work on deactivation. Enquiry records are left in place.
 */
function splendid_core_deactivate() {
	wp_clear_scheduled_hook( 'splendid_retry_queue' );
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'splendid_core_deactivate' );
