<?php
declare(strict_types=1);

namespace BITS\AccessibleForms\DB;

class Installer
{
    public static function install(): void
    {
        global $wpdb;
        if (!isset($wpdb)) {
            return;
        }

        $charset_collate = $wpdb->get_charset_collate();
        $forms_table = $wpdb->prefix . 'bits_forms';

        $sql = "CREATE TABLE {$forms_table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL DEFAULT '',
            slug VARCHAR(191) NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'inactive',
            schema_json LONGTEXT NULL,
            settings_json LONGTEXT NULL,
            next_field_id INT NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
            updated_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
            version VARCHAR(32) NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY slug (slug)
        ) {$charset_collate};";

        // dbDelta is required for reliable table creation/updates.
        if (!function_exists('dbDelta')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        dbDelta($sql);
    }
}
