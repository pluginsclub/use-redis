<?php

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}


// Display the SERVER RESOURCES widget
function pluginsclub_redis_widget_display() {
    
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

// Generate HTML for server information
$serverHtml .= '<table class="wp-list-table widefat striped" id="server">';
$serverHtml .= "<tr><td>Memory Usage</td><td><b><span style='color: {$color};'>{$usedMemoryHuman}</span></b>/<b> {$maxMemoryHuman}</b></td></tr>";

if (!empty($serverInfo['uptime_in_days'])) {
        // If uptime in days is available, display it and skip displaying uptime in seconds
        $serverHtml .= "<tr><td>Uptime (Days)</td><td><b>{$serverInfo['uptime_in_days']}</b></td></tr>";
    } else {
        // If uptime in days is not available, display uptime in seconds rounded to hours, minutes, seconds
        $uptimeSeconds = $serverInfo['uptime_in_seconds'];
        $uptimeFormatted = formatSecondsToTime($uptimeSeconds);
        $serverHtml .= "<tr><td>Uptime</td><td><b>{$uptimeFormatted}</b></td></tr>";
    }
$serverHtml .= "<tr><td>Connected Clients</td><td>{$serverInfo['connected_clients']}</td></tr>";
$serverHtml .= "<tr><td>Total Connections Received</td><td>{$serverInfo['total_connections_received']}</td></tr>";
$serverHtml .= "<tr><td>Total Commands Processed</td><td>{$serverInfo['total_commands_processed']}</td></tr>";
$serverHtml .= "<tr><td>Expired Keys</td><td>{$serverInfo['expired_keys']}</td></tr>";
$serverHtml .= '</table>';

echo $serverHtml;
}
catch (Exception $e) {
    // Display error message
    echo "Redis: " . $e->getMessage() . "</br>";
    echo "Please check the hostname and port setting <a href='/wp-admin/admin.php?page=object-cacher'>here</a>";
}

}


// Helper function to format bytes into a human-readable format
function formatBytes($bytes) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }

    return round($bytes, 2) . ' ' . $units[$i];
}



// Helper function to format seconds into hours, minutes, and seconds
function formatSecondsToTime($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;

    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}