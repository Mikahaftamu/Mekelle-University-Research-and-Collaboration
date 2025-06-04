<?php
/**
 * Plugin Name:  Activity Feed Anywhere For BuddyBoss
 * Description:  Place a native BuddyBoss activity post box and/or feed on any page.
 * Author:       Digitalera AB
 * Author URI:	 https://digitalera.se
 * Version:      1.2.5
 * Requires PHP: 7.0
 * License:      GNU General Public License v2 or later
 * Text Domain:  activity-feed-anywhere-for-buddyboss
 * Domain Path:  /languages/
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// definitions to use throughout application
define( 'BB_AFA_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'BB_AFA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BB_AFA_TEMPLATE_DIR', dirname(__FILE__) . '/templates/' );
define( 'BB_AFA_PAGE_TEMPLATE_FILENAME', 'page-custom-template-with-activity-feed' );
define( 'BB_AFA_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Load text domain
load_plugin_textdomain( 'activity-feed-anywhere-for-buddyboss', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

require( BB_AFA_PLUGIN_PATH . 'includes/activity-feed-anywhere-for-buddyboss-functions.php' );

/**
 * Start the engines, captain...
 *
 * @return void
 */
function bb_afa_init() {
	// autoload Activity Feed Anywhere classes
	require BB_AFA_PLUGIN_PATH . '/classes/autoload.php';

	if ( ! BB_Activity_Feed_Anywhere_Dependency_Checker::check_dependencies() ) {
		return;
	} else {
		new BB_Activity_Feed_Anywhere();
	}
}
add_action( 'init', 'bb_afa_init' );


/**
 * Action hook to execute after Activity Feed Anywhere For BuddyBoss plugin init.
 *
 * Use this hook to init addons.
 */
do_action( 'activity_feed_anywhere_for_buddyboss_init' );


