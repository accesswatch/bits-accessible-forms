<?php
/**
 * PHPUnit bootstrap file for BITS Accessible Forms
 */

// Load Composer autoloader
$autoloader = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($autoloader)) {
    require_once $autoloader;
}

// Define WordPress test environment constants if not already defined
if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/wordpress/');
}

// Mock WordPress functions for unit tests
if (!function_exists('add_action')) {
    function add_action($hook, $callback, $priority = 10, $accepted_args = 1) {
        return true;
    }
}

if (!function_exists('add_filter')) {
    function add_filter($hook, $callback, $priority = 10, $accepted_args = 1) {
        return true;
    }
}

if (!function_exists('add_shortcode')) {
    function add_shortcode($tag, $callback) {
        return true;
    }
}

if (!function_exists('register_rest_route')) {
    function register_rest_route($namespace, $route, $args = []) {
        return true;
    }
}

if (!function_exists('current_user_can')) {
    function current_user_can($capability) {
        return true; // Default to true for testing
    }
}

if (!function_exists('wp_verify_nonce')) {
    function wp_verify_nonce($nonce, $action = -1) {
        return 1;
    }
}

if (!function_exists('current_time')) {
    function current_time($type, $gmt = 0) {
        return gmdate('Y-m-d H:i:s');
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) {
        return strip_tags($str);
    }
}

if (!function_exists('sanitize_title')) {
    function sanitize_title($title) {
        return strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $title));
    }
}

if (!function_exists('wp_json_encode')) {
    function wp_json_encode($data, $options = 0, $depth = 512) {
        return json_encode($data, $options, $depth);
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('wp_kses_post')) {
    function wp_kses_post($data) {
        return $data; // Simplified for testing
    }
}

if (!function_exists('has_shortcode')) {
    function has_shortcode($content, $tag) {
        return strpos($content, '[' . $tag) !== false;
    }
}

if (!function_exists('has_block')) {
    function has_block($block_name, $post = null) {
        return false;
    }
}

if (!function_exists('register_block_type')) {
    function register_block_type($block_type, $args = []) {
        return true;
    }
}

if (!function_exists('plugins_url')) {
    function plugins_url($path = '', $plugin = '') {
        return 'https://example.com/wp-content/plugins/bits-accessible-forms' . $path;
    }
}

if (!function_exists('wp_enqueue_style')) {
    function wp_enqueue_style($handle, $src = '', $deps = [], $ver = false, $media = 'all') {
        return true;
    }
}

if (!function_exists('wp_enqueue_script')) {
    function wp_enqueue_script($handle, $src = '', $deps = [], $ver = false, $in_footer = false) {
        return true;
    }
}

if (!function_exists('wp_nonce_field')) {
    function wp_nonce_field($action = -1, $name = "_wpnonce", $referer = true, $echo = true) {
        $nonce = '<input type="hidden" id="' . $name . '" name="' . $name . '" value="test-nonce" />';
        if ($echo) {
            echo $nonce;
        }
        return $nonce;
    }
}

if (!function_exists('shortcode_atts')) {
    function shortcode_atts($pairs, $atts, $shortcode = '') {
        return array_merge($pairs, (array) $atts);
    }
}

if (!function_exists('__')) {
    function __($text, $domain = 'default') {
        return $text;
    }
}

// Mock WP_REST_Request
if (!class_exists('WP_REST_Request')) {
    class WP_REST_Request {
        private $params = [];
        private $json_params = [];
        private $headers = [];

        public function get_param($key) {
            return $this->params[$key] ?? null;
        }

        public function get_json_params() {
            return $this->json_params;
        }

        public function get_header($key) {
            return $this->headers[$key] ?? null;
        }

        public function set_param($key, $value) {
            $this->params[$key] = $value;
        }

        public function set_json_params($params) {
            $this->json_params = $params;
        }

        public function set_header($key, $value) {
            $this->headers[$key] = $value;
        }
    }
}

// Mock WP_REST_Response
if (!class_exists('WP_REST_Response')) {
    class WP_REST_Response {
        private $data;
        private $status;

        public function __construct($data = null, $status = 200) {
            $this->data = $data;
            $this->status = $status;
        }

        public function get_data() {
            return $this->data;
        }

        public function get_status() {
            return $this->status;
        }
    }
}

// Mock WP_Error
if (!class_exists('WP_Error')) {
    class WP_Error {
        private $code;
        private $message;
        private $data;

        public function __construct($code = '', $message = '', $data = '') {
            $this->code = $code;
            $this->message = $message;
            $this->data = $data;
        }

        public function get_error_code() {
            return $this->code;
        }

        public function get_error_message() {
            return $this->message;
        }

        public function get_error_data() {
            return $this->data;
        }
    }
}

// Mock WP_REST_Server constants
if (!class_exists('WP_REST_Server')) {
    class WP_REST_Server {
        const READABLE = 'GET';
        const CREATABLE = 'POST';
        const EDITABLE = 'PUT';
        const DELETABLE = 'DELETE';
    }
}

// Mock global $wpdb for database tests
global $wpdb;
if (!isset($wpdb)) {
    $wpdb = new class {
        public $prefix = 'wp_';
        private $results = [];
        private $last_query = '';

        public function prepare($query, ...$args) {
            $this->last_query = vsprintf(str_replace('%s', "'%s'", str_replace('%d', '%d', $query)), $args);
            return $this->last_query;
        }

        public function get_results($query, $output = OBJECT) {
            return $this->results;
        }

        public function get_row($query, $output = OBJECT, $y = 0) {
            return $this->results[0] ?? null;
        }

        public function get_var($query = null, $x = 0, $y = 0) {
            return 0;
        }

        public function insert($table, $data, $format = null) {
            return 1;
        }

        public function update($table, $data, $where, $format = null, $where_format = null) {
            return 1;
        }

        public function get_charset_collate() {
            return 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci';
        }

        public $insert_id = 1;

        public function set_results($results) {
            $this->results = $results;
        }
    };
}
