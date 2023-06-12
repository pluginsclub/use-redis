<?php
/**
 * Plugin Name:       REDIS Object Cacher
 * Plugin URI:        http://redis.plugins.club/
 * Description:       Simpler WordPress plugin for Redis object caching.
 * Version:           1.0.0
 * Author:            ♣️ plugins.club
 * Author URI:        https://plugins.club
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least: 5.0
 * Tested up to: 	  6.2
*/

// Include plugin Settings pages
require_once plugin_dir_path( __FILE__ ) . 'includes/object_cacher_main_page.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/object_cacher_info_page.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/object_cacher_warmer_page.php';

// Include the Widgets
require_once plugin_dir_path( __FILE__ ) . 'includes/widget.php';

// Include WP-CLI commands
require_once plugin_dir_path( __FILE__ ) . 'includes/wpcli.php';

// Add the widget to the admin dashboard
    add_action('wp_dashboard_setup', 'pluginsclub_redis_widget_add_dashboard_widget');
    function pluginsclub_redis_widget_add_dashboard_widget() {
        wp_add_dashboard_widget('pluginsclub_redis_widget', 'Redis Server Information', 'pluginsclub_redis_widget_display');
}



/**
 * Enqueue a script in the WordPress admin on admin.php.
 *
 * @param int $hook Hook suffix for the current admin pagees.
 */
function pluginsclub_redis_menu_page() {
    $screen = get_current_screen();
    if ( $screen->id === 'toplevel_page_object-cacher' || $screen->id === 'redis-cacher_page_object-cacher-info' || $screen->id === 'redis-cacher_page_object-cacher-warmer') {
    }
}
add_action( 'admin_enqueue_scripts', 'pluginsclub_redis_menu_page' );


function pluginsclub_redis_menu_icon() {
//wp_enqueue_script( 'pluginsclub_cpanel', plugin_dir_url( __FILE__ ) . 'includes/assets/js/settings-page.js', array(), '1.0.0', true );
wp_enqueue_style( 'pluginsclub_cpanel', plugin_dir_url( __FILE__ ) . 'includes/assets/css/settings-page.css', array(), '1.0.0' );
}
add_action( 'admin_enqueue_scripts', 'pluginsclub_redis_menu_icon' );

// Register menu pages
add_action('admin_menu', 'object_cacher_register_menu_pages');
function object_cacher_register_menu_pages() {
    $icon_url = plugin_dir_url( __FILE__ ) . 'includes/assets/images/redis.svg';

    add_menu_page(
        'Redis Object Cacher',
        'Redis',
        'manage_options',
        'object-cacher',
        'object_cacher_main_page',
        $icon_url,
        20
    );

    // Set the icon size
    global $menu;
    $menu[20][6] = $icon_url;
    $menu[20][7] = 'dashicons-database-view'; // Fallback dashicon class
    $menu[20][1] = '20px'; // Set the width of the icon
    $menu[20][2] = '20px'; // Set the height of the icon
}


// Redis server and port configuration
    $redis_server = get_option('object_cacher_redis_server', '127.0.0.1');
    $redis_port = get_option('object_cacher_redis_port', '6379');
// Connect to Redis and ONLY THEN load the other two admin pages
try {    
// Connect to Redis
$redis = new Redis();
$redis->connect($redis_server, $redis_port);

// Get Redis server information
$serverInfo = $redis->info();

// Register menu pages
add_action('admin_menu', 'object_cacher_register_redis_submenu_pages');
function object_cacher_register_redis_submenu_pages() {

    add_submenu_page(
        'object-cacher',
        'Redis Information',
        'Redis Information',
        'manage_options',
        'object-cacher-info',
        'object_cacher_info_page'
    );
    
    add_submenu_page(
        'object-cacher',
        'Cache Warmer',
        'Cache Warmer',
        'manage_options',
        'object-cacher-warmer',
        'object_cacher_warmer_page'
    );
}
    
// Disconnect from Redis
$redis->close();


}
catch (Exception $e) {
}
