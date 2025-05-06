<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Redis {
    private $redis;
    
    public function __construct($params = null) {
        // Get CI instance
        $CI =& get_instance();
        
        // If no params passed, use empty array
        if ($params === null) {
            $params = array();
        }
        
        // Extract connection details - check for direct 'host', 'port' params first,
        // then check for redis_* format, then fall back to config
        $host = $params['host'] ?? $params['redis_host'] ?? $CI->config->item('redis_host') ?? '127.0.0.1';
        $port = $params['port'] ?? $params['redis_port'] ?? $CI->config->item('redis_port') ?? 6379;
        $timeout = $params['timeout'] ?? $params['redis_timeout'] ?? $CI->config->item('redis_timeout') ?? 0;
        
        // Debug output
        log_message('debug', 'Redis Host: ' . $host);
        log_message('debug', 'Redis Port: ' . $port);
        log_message('debug', 'Redis Timeout: ' . $timeout);
        
        try {
            // Initialize without params
            $this->redis = new \Redis();
            
            // Then connect with the extracted parameters
            $persistent_id = $params['persistent_id'] ?? null;
            
            if ($persistent_id) {
                $connected = $this->redis->pconnect($host, $port, $timeout, $persistent_id);
            } else {
                $connected = $this->redis->connect($host, $port, $timeout);
            }
            
            if (!$connected) {
                throw new Exception("Failed to connect to Redis at $host:$port");
            }
        } catch (Exception $e) {
            log_message('error', 'Redis connection error: ' . $e->getMessage());
            // Log error but don't exit completely to allow the script to continue
            // exit('Redis connection error: ' . $e->getMessage());
        }
    }
}