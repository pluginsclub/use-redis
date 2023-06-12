<?php

if (defined('WP_CLI') && WP_CLI) {


    /**
     * Display Redis server information.
     */
    class Redis_Status_Command extends WP_CLI_Command
    {
        /**
         * Display Redis server information.
         *
         * ## OPTIONS
         *
         * [<server>]
         * : Optional Redis server and port in the format <hostname>:<port>.
         *
         * ## EXAMPLES
         *
         *     wp redis-info connect
         *
         *     wp redis-info connect 127.0.0.1:6379
         *
         *     wp redis-info connect localhost
         * 
         *     wp redis-info connect :6379
         *
         * @when after_wp_load
         */
        public function __invoke($args, $assoc_args)
        {

// Redis server and port configuration
    $redis_server = get_option('object_cacher_redis_server', '127.0.0.1');
    $redis_port = get_option('object_cacher_redis_port', '6379');

if (!empty($args[0])) {
    $server_parts = explode(':', $args[0]);
    if (count($server_parts) === 2) {
        // Check if server IP is provided or use the default value
        $redis_server = $server_parts[0] !== '' ? $server_parts[0] : $redis_server;
        $redis_port = $server_parts[1];
    } elseif (count($server_parts) === 1) {
        if ($server_parts[0] !== '') {
            // Only hostname is provided, use default port
            $redis_server = $server_parts[0];
        } else {
            // No hostname or port provided, use default server IP and port
            $redis_server = get_option('object_cacher_redis_server', '127.0.0.1');
            $redis_port = get_option('object_cacher_redis_port', '6379');
        }
    }
}



            try {
                // Connect to Redis
                $redis = new Redis();
                $redis->connect($redis_server, $redis_port);
                // Get Redis server port
                $redis_port = $redis->getPort();
                // Get Redis server information
                $server_info = $redis->info();

                 // Output server information
WP_CLI::log("               _._");
WP_CLI::log("          _.-``__ ''-._");
WP_CLI::log("     _.-``    `.  `_.  ''-._           Redis {$server_info['redis_version']}");
WP_CLI::log(" .-`` .-```.  ```\\/    _.,_ ''-._");
WP_CLI::log("(    '      ,       .-`  | `,    )     ");
WP_CLI::log("|`-._`-...-` __...-.``-._|'` _.-'|     Port: {$redis_port}");
WP_CLI::log("|    `-._   `._    /     _.-'    |     PID: {$server_info['process_id']}");
WP_CLI::log(" `-._    `-._  `-./  _.-'    _.-'");
WP_CLI::log("|`-._`-._    `-.__.-'    _.-'_.-'|");
WP_CLI::log("|    `-._`-._        _.-'_.-'    |           <https://redis.plugins.club/>");
WP_CLI::log(" `-._    `-._`-.__.-'_.-'    _.-'");
WP_CLI::log("|`-._`-._    `-.__.-'    _.-'_.-'|");
WP_CLI::log("|    `-._`-._        _.-'_.-'    |");
WP_CLI::log(" `-._    `-._`-.__.-'_.-'    _.-'");
WP_CLI::log("     `-._    `-.__.-'    _.-'");
WP_CLI::log("         `-._        _.-'");
WP_CLI::log("             `-.__.-'");

                // Disconnect from Redis
                $redis->close();
            } catch (RedisException $e) {
                WP_CLI::error('Failed to connect to Redis server on ' . $redis_server . ':' . $redis_port . '. Make sure REDIS is running!');
            }
        }
    }
    
    
    /**
     * Display Redis server information.
     */
    class Redis_Info_Command extends WP_CLI_Command
    {
        /**
         * Display Redis server information.
         *
         * ## OPTIONS
         *
         * [<server>]
         * : Optional Redis server and port in the format <hostname>:<port>.
         *
         * ## EXAMPLES
         *
         *     wp redis-info connect
         *
         *     wp redis-info connect 127.0.0.1:6379
         *
         *     wp redis-info connect localhost
         * 
         *     wp redis-info connect :6379
         *
         * @when after_wp_load
         */
        public function __invoke($args, $assoc_args)
        {

// Redis server and port configuration
    $redis_server = get_option('object_cacher_redis_server', '127.0.0.1');
    $redis_port = get_option('object_cacher_redis_port', '6379');

if (!empty($args[0])) {
    $server_parts = explode(':', $args[0]);
    if (count($server_parts) === 2) {
        // Check if server IP is provided or use the default value
        $redis_server = $server_parts[0] !== '' ? $server_parts[0] : $redis_server;
        $redis_port = $server_parts[1];
    } elseif (count($server_parts) === 1) {
        if ($server_parts[0] !== '') {
            // Only hostname is provided, use default port
            $redis_server = $server_parts[0];
        } else {
            // No hostname or port provided, use default server IP and port
            $redis_server = get_option('object_cacher_redis_server', '127.0.0.1');
            $redis_port = get_option('object_cacher_redis_port', '6379');
        }
    }
}



            try {
                // Connect to Redis
                $redis = new Redis();
                $redis->connect($redis_server, $redis_port);

                // Get Redis server information
                $server_info = $redis->info();

                // Format uptime
                $uptimeInSeconds = $server_info['uptime_in_seconds'];
                $uptimeReadable = format_uptime($uptimeInSeconds);

                // Output server information
                WP_CLI::log('Redis Server Information');
                WP_CLI::log('------------------------');
                WP_CLI::log("Process ID: {$server_info['process_id']}");
                WP_CLI::log("Uptime: $uptimeReadable");
                WP_CLI::log("Memory Usage: {$server_info['used_memory_human']} / {$server_info['maxmemory_human']}");
                WP_CLI::log("Connected Clients: {$server_info['connected_clients']}");
                WP_CLI::log("Total Connections Received: {$server_info['total_connections_received']}");
                WP_CLI::log("Total Commands Processed: {$server_info['total_commands_processed']}");
                WP_CLI::log("Expired Keys: {$server_info['expired_keys']}");

                // Disconnect from Redis
                $redis->close();
            } catch (RedisException $e) {
                WP_CLI::error('Failed to connect to Redis server on ' . $redis_server . ':' . $redis_port . '. Make sure REDIS is running!');
            }
        }
    }
    
    
    
    /**
     * Display Redis keys.
     */
    class Redis_Keys_Command extends WP_CLI_Command
    {
        /**
         * Display Redis keys.
         *
         * ## OPTIONS
         *
         * [<server>]
         * : Optional Redis server and port in the format <hostname>:<port>.
         *
         * ## EXAMPLES
         *
         *     wp redis-info keys
         *
         *     wp redis-info keys 127.0.0.1:6379
         *
         *     wp redis-info keys localhost
         * 
         *     wp redis-info keys :6379
         *
         * @when after_wp_load
         */
        public function __invoke($args, $assoc_args)
        {
// Redis server and port configuration
    $redis_server = get_option('object_cacher_redis_server', '127.0.0.1');
    $redis_port = get_option('object_cacher_redis_port', '6379');

if (!empty($args[0])) {
    $server_parts = explode(':', $args[0]);
    if (count($server_parts) === 2) {
        // Check if server IP is provided or use the default value
        $redis_server = $server_parts[0] !== '' ? $server_parts[0] : $redis_server;
        $redis_port = $server_parts[1];
    } elseif (count($server_parts) === 1) {
        if ($server_parts[0] !== '') {
            // Only hostname is provided, use default port
            $redis_server = $server_parts[0];
        } else {
            // No hostname or port provided, use default server IP and port
    $redis_server = get_option('object_cacher_redis_server', '127.0.0.1');
    $redis_port = get_option('object_cacher_redis_port', '6379');
        }
    }
}



            try {
                // Connect to Redis
                $redis = new Redis();
                $redis->connect($redis_server, $redis_port);

                // Get all keys
                $keys = $redis->keys('*');

                // Output key information
                WP_CLI::log('Redis Data');
                WP_CLI::log('------------------------');

                // Display data for each key
                foreach ($keys as $key) {
                    WP_CLI::log("Key: $key");

                    $type = $redis->type($key);
                    WP_CLI::log("Type: $type");

                    WP_CLI::log('------------------------');
                }

                // Disconnect from Redis
                $redis->close();
            } catch (RedisException $e) {
                WP_CLI::error('Failed to connect to Redis server on ' . $redis_server . ':' . $redis_port . '. You can specify a custom IP and Port: wp redis-info keys 127.0.0.1:6379');
            }
        }
    }
    
    /**
     * Display Redis value for a specific key.
     */
    class Redis_Value_Command
    {
        /**
         * Display Redis value for a specific key.
         *
         * ## OPTIONS
         *
         * <key>
         * : Redis key for which to display the value.
         *
         * [<server>]
         * : Optional Redis server and port in the format <hostname>:<port>.
         *
         * ## EXAMPLES
         *
         *     wp redis-info value 05446category_relationships.6376
         *
         *     wp redis-info value 05446category_relationships.6376 127.0.0.1:6379
         *
         * @when after_wp_load
         */
        public function __invoke($args, $assoc_args)
        {
            if (empty($args[0])) {
                WP_CLI::error('Please provide a Redis key.');
            }

            $redis_key = $args[0];

            // Redis server and port configuration
    $redis_server = get_option('object_cacher_redis_server', '127.0.0.1');
    $redis_port = get_option('object_cacher_redis_port', '6379');

if (!empty($args[0])) {
    $server_parts = explode(':', $args[0]);
    if (count($server_parts) === 2) {
        // Check if server IP is provided or use the default value
        $redis_server = $server_parts[0] !== '' ? $server_parts[0] : $redis_server;
        $redis_port = $server_parts[1];
    } elseif (count($server_parts) === 1) {
        if ($server_parts[0] !== '') {
            // Only hostname is provided, use default port
            $redis_server = $server_parts[0];
        } else {
            // No hostname or port provided, use default server IP and port
    $redis_server = get_option('object_cacher_redis_server', '127.0.0.1');
    $redis_port = get_option('object_cacher_redis_port', '6379');
        }
    }
}

            try {
                // Connect to Redis
                $redis = new Redis();
                $redis->connect($redis_server, $redis_port);

                // Check if the key exists
                if (!$redis->exists($redis_key)) {
                    WP_CLI::error("Key '{$redis_key}' does not exist in Redis.");
                }

                WP_CLI::log("Key: $redis_key");

                $type = $redis->type($redis_key);
                WP_CLI::log("Type: $type");

                switch ($type) {
                    case Redis::REDIS_STRING:
                        $value = $redis->get($redis_key);
                        WP_CLI::log("Value: $value");
                        break;
                    case Redis::REDIS_HASH:
                        $hash = $redis->hGetAll($redis_key);
                        WP_CLI::log('Value: ' . print_r($hash, true));
                        break;
                    case Redis::REDIS_LIST:
                        $list = $redis->lRange($redis_key, 0, -1);
                        WP_CLI::log('Value: ' . print_r($list, true));
                        break;
                    case Redis::REDIS_SET:
                        $set = $redis->sMembers($redis_key);
                        WP_CLI::log('Value: ' . print_r($set, true));
                        break;
                    case Redis::REDIS_ZSET:
                        $zset = $redis->zRange($redis_key, 0, -1, true);
                        WP_CLI::log('Value: ' . print_r($zset, true));
                        break;
                    default:
                        WP_CLI::log('Value: Unknown type');
                        break;
                }

                // Disconnect from Redis
                $redis->close();
            } catch (RedisException $e) {
                WP_CLI::error('Failed to connect to Redis server on ' . $redis_server . ':' . $redis_port . '. You can specify a custom IP and Port: wp redis-info 127.0.0.1:6379');
            }
        }
    }
    
    /**
     * Flush Redis cache.
     */
    class Redis_Flush_Command {

        /**
         * Flush Redis cache.
         *
         * ## EXAMPLES
         *
         *     wp redis-info flush
         *
         * @when after_wp_load
         */
        public function __invoke($args, $assoc_args){
            
// Redis server and port configuration
    $redis_server = get_option('object_cacher_redis_server', '127.0.0.1');
    $redis_port = get_option('object_cacher_redis_port', '6379');

if (!empty($args[0])) {
    $server_parts = explode(':', $args[0]);
    if (count($server_parts) === 2) {
        // Check if server IP is provided or use the default value
        $redis_server = $server_parts[0] !== '' ? $server_parts[0] : $redis_server;
        $redis_port = $server_parts[1];
    } elseif (count($server_parts) === 1) {
        if ($server_parts[0] !== '') {
            // Only hostname is provided, use default port
            $redis_server = $server_parts[0];
        } else {
            // No hostname or port provided, use default server IP and port
    $redis_server = get_option('object_cacher_redis_server', '127.0.0.1');
    $redis_port = get_option('object_cacher_redis_port', '6379');
        }
    }
}

            try {
                // Connect to Redis
                $redis = new Redis();
                $redis->connect($redis_server, $redis_port);
                $redis->flushAll();
                WP_CLI::success( 'Redis cache flushed.' );
               // Disconnect from Redis
                $redis->close();
            } catch (RedisException $e) {
                WP_CLI::error('Failed to connect to Redis server on ' . $redis_server . ':' . $redis_port . '. You can specify a custom IP and Port: wp redis-info flush 127.0.0.1:6379');
            }
        }
        
        
    }
    
    
    // Register WP-CLI commands
    WP_CLI::add_command('redis-info status', 'Redis_Status_Command');
    WP_CLI::add_command('redis-info connect', 'Redis_Info_Command');
    WP_CLI::add_command('redis-info keys', 'Redis_Keys_Command');
    WP_CLI::add_command('redis-info value', 'Redis_Value_Command');
    WP_CLI::add_command('redis-info flush', 'Redis_Flush_Command');
}

/**
 * Format the uptime into a readable string.
 *
 * @param int $uptimeInSeconds The uptime in seconds.
 * @return string Formatted uptime.
 */
function format_uptime($uptimeInSeconds)
{
    $years = floor($uptimeInSeconds / (365 * 24 * 60 * 60));
    $months = floor(($uptimeInSeconds % (365 * 24 * 60 * 60)) / (30 * 24 * 60 * 60));
    $weeks = floor((($uptimeInSeconds % (365 * 24 * 60 * 60)) % (30 * 24 * 60 * 60)) / (7 * 24 * 60 * 60));
    $days = floor(((($uptimeInSeconds % (365 * 24 * 60 * 60)) % (30 * 24 * 60 * 60)) % (7 * 24 * 60 * 60)) / (24 * 60 * 60));
    $hours = floor((((($uptimeInSeconds % (365 * 24 * 60 * 60)) % (30 * 24 * 60 * 60)) % (7 * 24 * 60 * 60)) % (24 * 60 * 60)) / (60 * 60));
    $minutes = floor(((((($uptimeInSeconds % (365 * 24 * 60 * 60)) % (30 * 24 * 60 * 60)) % (7 * 24 * 60 * 60)) % (24 * 60 * 60)) % (60 * 60)) / 60);
    $seconds = floor((((($uptimeInSeconds % (365 * 24 * 60 * 60)) % (30 * 24 * 60 * 60)) % (7 * 24 * 60 * 60)) % (24 * 60 * 60)) % 60);

    $uptimeReadable = '';
    if ($years > 0) {
        $uptimeReadable .= $years . ' year' . ($years > 1 ? 's' : '') . ', ';
    }
    if ($months > 0) {
        $uptimeReadable .= $months . ' month' . ($months > 1 ? 's' : '') . ', ';
    }
    if ($weeks > 0) {
        $uptimeReadable .= $weeks . ' week' . ($weeks > 1 ? 's' : '') . ', ';
    }
    if ($days > 0) {
        $uptimeReadable .= $days . ' day' . ($days > 1 ? 's' : '') . ', ';
    }
    if ($hours > 0) {
        $uptimeReadable .= $hours . ' hour' . ($hours > 1 ? 's' : '') . ', ';
    }
    if ($minutes > 0) {
        $uptimeReadable .= $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ', ';
    }
    if ($seconds > 0) {
        $uptimeReadable .= $seconds . ' second' . ($seconds > 1 ? 's' : '');
    }

    $uptimeReadable = rtrim($uptimeReadable, ', ');

    return $uptimeReadable;
}
