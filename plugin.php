<?php
/**
 * Plugin Name: WP Primary Category
 * Description: Select the primary category
 * Author: Aires Gonçalves
 * Author URI: http://github.com/airesvsg
 * Version: 1.1.0
 * Plugin URI: http://github.com/airesvsg/wp-primary-category
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WP_PC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_PC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

require_once WP_PC_PLUGIN_PATH . 'includes/classes/class-wp-primary-category.php';

add_action( 'plugins_loaded', array( 'WP_Primary_Category', 'init' ) );
