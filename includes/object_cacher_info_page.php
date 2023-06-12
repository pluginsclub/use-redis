<?php

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

// Redis Information page
function object_cacher_info_page() {
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

// Display success message
        echo '<div class="notice notice-success"><p>&#128994; Successfully connected to Redis server on <span style="color:green; font-weight: bold">' . $redis_server . ':' . $redis_port . '</span></p></div>';

// Initialize the $serverHtml variable
$serverHtml = '';
$serverHtml .= '<table style="width: 100%;text-align: center;border: 0 none"><tbody><tr>';
$serverHtml .= "<td style='width:26%; background: #191E23;color: #fff;border: 2px solid #fff;padding: 2em 0'>Redis Server <h3 style='color: #fff;padding: 0'><b>" . $redis_server . "</b></h3></td>";
$serverHtml .= "<td style='width:26%; background: #191E23;color: #fff;border: 2px solid #fff;padding: 2em 0'>TCP Port <h3 style='color: #fff;padding: 0'><b>{$serverInfo['tcp_port']}</b></h3></td>";
$serverHtml .= "<td style='width:26%; background: #191E23;color: #fff;border: 2px solid #fff;padding: 2em 0'>Process ID <h3 style='color: #fff;padding: 0'><b>{$serverInfo['process_id']}</b></h3></td>";
$serverHtml .= "</tr></tbody></table>";    
$serverHtml .= "</br>"; 
echo $serverHtml;
    
?> 
  				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">  
    						<div class="inside">
    
    
    <?php

// Get Redis server information
$serverInfo = $redis->info();

// Get all keys
$keys = $redis->keys('*');

// Generate HTML table
$dataHtml = '<h2>Redis Data</h2>';
$dataHtml .= '<table class="wp-list-table  widefat striped posts" id="keys">';
$dataHtml .= '<tr><th>Key</th><th>Type</th><th>Value</th><th>Page</th></tr>';

// Display data for each key
foreach ($keys as $key) {
    $dataHtml .= '<tr>';
    $dataHtml .= "<td>$key</td>";

    $type = $redis->type($key);
    $typeText = getTypeText($type);
    $dataHtml .= "<td>$typeText</td>";
    switch ($type) {
        case Redis::REDIS_STRING:
            $value = $redis->get($key);
            if (strlen($value) > 150) {
                $dataHtml .= "<td><span class='toggle-link' onclick='toggleContent(this)'>Display</span><span class='content-hidden'>" . htmlspecialchars($value) . "</span></td>";
            } else {
                $dataHtml .= "<td><pre>" . htmlspecialchars($value) . "</pre></td>";
            }

            // Extract page URL from value
            $pageUrl = "";
            if (preg_match('/Location: (.*?)["\';\s]/', $value, $matches)) {
                $pageUrl = $matches[1];
            }
            $dataHtml .= "<td><a href='$pageUrl'>$pageUrl</a></td>";
            break;
        case Redis::REDIS_HASH:
            $hash = $redis->hGetAll($key);
            $dataHtml .= "<td><pre>" . htmlspecialchars(json_encode($hash, JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_QUOT)) . "</pre></td>";
            $dataHtml .= "<td></td>"; // Empty column for hashes
            break;
        case Redis::REDIS_LIST:
            $list = $redis->lRange($key, 0, -1);
            $dataHtml .= "<td><pre>" . htmlspecialchars(json_encode($list, JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_QUOT)) . "</pre></td>";
            $dataHtml .= "<td></td>"; // Empty column for lists
            break;
        case Redis::REDIS_SET:
            $set = $redis->sMembers($key);
            $dataHtml .= "<td><pre>" . htmlspecialchars(json_encode($set, JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_QUOT)) . "</pre></td>";
            $dataHtml .= "<td></td>"; // Empty column for sets
            break;
        case Redis::REDIS_ZSET:
            $zset = $redis->zRange($key, 0, -1, true);
            $dataHtml .= "<td><pre>" . htmlspecialchars(json_encode($zset, JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_QUOT)) . "</pre></td>";
            $dataHtml .= "<td></td>"; // Empty column for sorted sets
            break;
        default:
            $dataHtml .= "<td>Unknown type</td>";
            $dataHtml .= "<td></td>"; // Empty column for unknown types
            break;
    }

    $dataHtml .= '</tr>';
}

$dataHtml .= '</table>';



// Disconnect from Redis
$redis->close();

// Combine server information and data HTML
$html = $dataHtml;

// Display HTML
echo $html;

////////

}
catch (Exception $e) {
            echo '<div class="notice notice-error"><p>&#128308; Failed to connect to Redis server on <span style="color:red; font-weight: bold">' . $redis_server . ':' . $redis_port . '</span>. Please check your Redis server configuration.</p></div>';

}
   


?>


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
    



<script>
    function toggleContent(link) {
        var content = link.parentNode.querySelector('.content-hidden');
        if (content.style.display === 'none' || content.style.display === '') {
            content.style.display = 'inline';
            link.innerHTML = 'Hide </br>';
        } else {
            content.style.display = 'none';
            link.innerHTML = 'Display';
        }
    }
</script>


<style>
    .toggle-link {
        color: blue;
        cursor: pointer;
        text-decoration: underline;
    }
    .content-hidden {
        display: none;
    }
</style>
<?php
    
    // TODO: Display Redis server information
}



// Function to get the type as text
function getTypeText($type)
{
    switch ($type) {
        case Redis::REDIS_STRING:
            return 'String';
        case Redis::REDIS_LIST:
            return 'List';
        case Redis::REDIS_SET:
            return 'Set';
        case Redis::REDIS_ZSET:
            return 'Sorted Set';
        case Redis::REDIS_HASH:
            return 'Hash';
        case Redis::REDIS_STREAM:
            return 'Stream';
        case Redis::REDIS_MODULE:
            return 'Module';
        default:
            return 'Unknown';
    }
}