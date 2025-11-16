<?php
/**
 * Plugin Name: BITS Accessible Forms
 * Description: WCAG 2.2 AA accessible form builder with rich logic, pricing/tax, and payments.
 * Version: 0.1.0
 * Author: AccessWatch
 * Text Domain: bits-accessible-forms
 */

// Abort if this file is called directly.
if (!defined('ABSPATH')) {
    exit;
}

// Load Composer autoloader if present.
$autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

// Minimal fallback autoloader if Composer is not yet set up.
spl_autoload_register(function ($class) {
    $prefix = 'BITS\\AccessibleForms\\';
    $base_dir = __DIR__ . '/inc/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Bootstrap plugin.
add_action('plugins_loaded', function () {
    if (class_exists('BITS\\AccessibleForms\\Bootstrap')) {
        (new BITS\AccessibleForms\Bootstrap())->init();
    }
});

// Activation / Deactivation hooks.
register_activation_hook(__FILE__, function () {
    if (class_exists('BITS\\AccessibleForms\\Bootstrap')) {
        BITS\AccessibleForms\Bootstrap::activate();
    }
});

register_deactivation_hook(__FILE__, function () {
    if (class_exists('BITS\\AccessibleForms\\Bootstrap')) {
        BITS\AccessibleForms\Bootstrap::deactivate();
    }
});
