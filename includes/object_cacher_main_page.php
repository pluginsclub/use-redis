<?php

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

// Redis Configuration Page
function object_cacher_main_page() {
    if (isset($_POST['submit'])) {
        // Retrieve Redis server and port values from form submission
        $redis_server = isset($_POST['redis_server']) && !empty($_POST['redis_server']) ? sanitize_text_field($_POST['redis_server']) : get_option('object_cacher_redis_server', '127.0.0.1');
        $redis_port = isset($_POST['redis_port']) && !empty($_POST['redis_port']) ? sanitize_text_field($_POST['redis_port']) : get_option('object_cacher_redis_port', '6379');

        // Save Redis server and port to database
        update_option('object_cacher_redis_server', $redis_server);
        update_option('object_cacher_redis_port', $redis_port);
    }

    // Redis server and port configuration
    $redis_server = get_option('object_cacher_redis_server', '127.0.0.1');
    $redis_port = get_option('object_cacher_redis_port', '6379');

    ?>

<div class="wrap">

	<div id="icon-options-general" class="icon32"></div>
	<h1><img class="logo-slika" src="<?php echo plugin_dir_url( __FILE__ ) . '/assets/images/redis.svg';?>"></img> Redis Object Cacher</h1>
	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2">

			<!-- main content -->
			<div id="post-body-content">


							
<?php
// Redis server and port configuration
    $redis_server = get_option('object_cacher_redis_server', '127.0.0.1');
    $redis_port = get_option('object_cacher_redis_port', '6379');

try {    
// Connect to Redis
$redis = new Redis();
$redis->connect($redis_server, $redis_port);


// Get Redis server information
$serverInfo = $redis->info();

// Initialize the $serverHtml variable
$serverHtml = '';

// Show different color depending on memory usage
$usedMemory = $serverInfo['used_memory'];
$maxMemory = $serverInfo['maxmemory'];
$usedMemoryHuman = $serverInfo['used_memory_human'];
$maxMemoryHuman = $serverInfo['maxmemory_human'];

$usagePercentage = ($usedMemory / $maxMemory) * 100;

if ($usagePercentage > 90) {
    $color = 'red';
} elseif ($usagePercentage > 70) {
    $color = 'orange';
} else {
    $color = 'green';
}



$serverHtml .= '<table style="width: 100%;text-align: center;border: 0 none"><tbody><tr>';
$serverHtml .= "<td style='width:26%; background: #191E23;color: #fff;border: 2px solid #fff;padding: 2em 0'>Memory Usage <h3 style='color: #fff;padding: 0'><b><span style='color: {$color};'>{$usedMemoryHuman}</span></b>/<b> {$maxMemoryHuman}</b></h3></td>";

if (!empty($serverInfo['uptime_in_days'])) {
        // If uptime in days is available, display it and skip displaying uptime in seconds
        $serverHtml .= "<td style='width:26%; background: #191E23;color: #fff;border: 2px solid #fff;padding: 2em 0'>Uptime (Days) <h3 style='color: #fff;padding: 0'><b>{$serverInfo['uptime_in_days']}</b></h3></td>";

    } else {
        // If uptime in days is not available, display uptime in seconds rounded to hours, minutes, seconds
        $uptimeSeconds = $serverInfo['uptime_in_seconds'];
        $uptimeFormatted = formatSecondsToTime($uptimeSeconds);
        $serverHtml .= "<td style='width:26%; background: #191E23;color: #fff;border: 2px solid #fff;padding: 2em 0'>Uptime <h3 style='color: #fff;padding: 0'><b>{$uptimeFormatted}</b></h3></td>";
    }
$serverHtml .= "<td style='width:26%; background: #191E23;color: #fff;border: 2px solid #fff;padding: 2em 0'>Connected Clients <h3 style='color: #fff;padding: 0'><b>{$serverInfo['connected_clients']}</b></h3></td>";
$serverHtml .= "</tr></tr>";  
$serverHtml .= "<td style='width:26%; background: #191E23;color: #fff;border: 2px solid #fff;padding: 2em 0'>Total Connections Received </br><b> {$serverInfo['total_connections_received']}</b></td>";
$serverHtml .= "<td style='width:26%; background: #191E23;color: #fff;border: 2px solid #fff;padding: 2em 0'>Total Commands Processed </br><b>{$serverInfo['total_commands_processed']}</b></td>";
$serverHtml .= "<td style='width:26%; background: #191E23;color: #fff;border: 2px solid #fff;padding: 2em 0'>Expired Keys </br><b>{$serverInfo['expired_keys']}</b></td>";
$serverHtml .= "</tr></tbody></table>";    
$serverHtml .= "</br>"; 
echo $serverHtml;
}
catch (Exception $e) {
}
    
    
?>    

				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">

						<h2><span><?php esc_attr_e( 'Redis Configuration', 'WpAdminStyle' ); ?></span></h2>

						<div class="inside">
 <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="redis_server">Redis Server</label></th>
                    <td><input type="text" name="redis_server" id="redis_server" value="<?php echo esc_attr($redis_server); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="redis_port">Redis Port</label></th>
                    <td><input type="number" name="redis_port" id="redis_port" value="<?php echo esc_attr($redis_port); ?>"></td>
                </tr>
            </table>
                        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
        </form>

						</div>
						<!-- .inside -->

					</div>
					<!-- .postbox -->

				</div>
				<!-- .meta-box-sortables .ui-sortable -->

			</div>
			<!-- post-body-content -->

			<!-- sidebar -->
			<div id="postbox-container-1" class="postbox-container">

				<div class="meta-box-sortables">

					<div class="postbox">

						<h2><span><?php esc_attr_e(
									'WP-CLI Commands', 'WpAdminStyle'
								); ?></span></h2>

						<div class="inside">
							<p>Test connection to Redis server and monitor memory usage via WP-CLI.</p>
<p>Available commands:</p>
<ul>
<li><a href="https://plugins.club/free-wordpress-plugins/redis-object-cacher/#wp-redis-info-status" target="_blank">wp redis-info status</a></li>
<li><a href="https://plugins.club/free-wordpress-plugins/redis-object-cacher/#wp-redis-info-connect" target="_blank">wp redis-info connect</a></li>
<li><a href="https://plugins.club/free-wordpress-plugins/redis-object-cacher/#wp-redis-info-flush" target="_blank">wp redis-info flush</a></li>
<li><a href="https://plugins.club/free-wordpress-plugins/redis-object-cacher/#wp-redis-info-keys" target="_blank">wp redis-info keys</a></li>
<li><a href="https://plugins.club/free-wordpress-plugins/redis-object-cacher/#wp-redis-info-value" target="_blank">wp redis-info value</a></li>
</ul>
						</div>
						<!-- .inside -->

					</div>
					<!-- .postbox -->

				</div>
				<!-- .meta-box-sortables -->

			</div>
			<!-- #postbox-container-1 .postbox-container -->

		</div>
		<!-- #post-body .metabox-holder .columns-2 -->

		<br class="clear">
	</div>
	<!-- #poststuff -->

</div> <!-- .wrap -->
    
<?php


// Redis server and port configuration
    $redis_server = get_option('object_cacher_redis_server', '127.0.0.1');
    $redis_port = get_option('object_cacher_redis_port', '6379');

    try {
        // Test Redis connection
        $redis_connect = new Redis();
        $redis_connect->connect($redis_server, $redis_port);

        // Get Redis server information
        $serverInfo = $redis_connect->info();
        // Display success message
        echo '<div class="notice notice-success"><p>&#128994; Successfully connected to Redis server on <span style="color:green; font-weight: bold">' . $redis_server . ':' . $redis_port . '</span></p></div>';

    } catch (RedisException $e) {
        echo '<div class="notice notice-error"><p>&#128308; Failed to connect to Redis server on <span style="color:red; font-weight: bold">' . $redis_server . ':' . $redis_port . '</span>. Please check your Redis server configuration.</p></div>';

    } 

    try {
        // Test Redis connection
        $redis_connect = new Redis();
        $redis_connect->connect($redis_server, $redis_port);

        // Get Redis server information
        $serverInfo = $redis_connect->info();

// Update wp-config.php file
        $config_file = ABSPATH . 'wp-config.php';

        // Read the current contents of wp-config.php
        $config_content = file_get_contents($config_file);

        // Define the replacement constants
        $replacement = "
define('OBJECT_CACHER_REDIS_SERVER', '{$redis_server}');
define('OBJECT_CACHER_REDIS_PORT', '{$redis_port}');";

        // Check if the constants already exist in the file
        $existing_constants = "define('OBJECT_CACHER_REDIS_SERVER',";
        if (strpos($config_content, $existing_constants) !== false) {
            // Replace the existing constant values with the new values
            $updated_content = preg_replace("/define\('OBJECT_CACHER_REDIS_SERVER',\s*'.*?'\);/", "define('OBJECT_CACHER_REDIS_SERVER', '{$redis_server}');", $config_content);
            $updated_content = preg_replace("/define\('OBJECT_CACHER_REDIS_PORT',\s*'.*?'\);/", "define('OBJECT_CACHER_REDIS_PORT', '{$redis_port}');", $updated_content);
        } else {
            // Insert the new constants just after the opening <?php tag
            $updated_content = str_replace('<?php', '<?php' . $replacement, $config_content);
        }

        // Write the updated contents back to wp-config.php
        file_put_contents($config_file, $updated_content);


    // Perform custom cache actions
    // If connection is successfully ONLY THEN enable caching in wp-config.php and symlink the advanced-cache.php file
    // Add to wp-config.php
    if (get_option('custom_cache_added_to_wp_config')) {
        return; // Already added, so no need to do anything
    }

    $wp_config_path = ABSPATH . 'wp-config.php';
    $config_contents = file_get_contents($wp_config_path);

    if (strpos($config_contents, "define('WP_CACHE', true);") === false) {
        $config_contents = preg_replace('/<\?php\s*(\R)/', "<?php\ndefine( 'WP_CACHE', true );\n$1", $config_contents, 1);
        file_put_contents($wp_config_path, $config_contents);
    }

    update_option('custom_cache_added_to_wp_config', true);


// Create symbolic link
    if (get_option('custom_cache_symbolic_link_created')) {
        return; // Already created, so no need to do anything
    }

    $plugin_dir = plugin_dir_path(__FILE__);
    $target_file = $plugin_dir . 'instance/advanced-cache.php';
    $symbolic_link = WP_CONTENT_DIR . '/advanced-cache.php';

    if (!is_link($symbolic_link) && file_exists($target_file)) {
        symlink($target_file, $symbolic_link);
    }

    update_option('custom_cache_symbolic_link_created', true);
    
    } catch (RedisException $e) {
    }    



?>


<?php


}