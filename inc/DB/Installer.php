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
        
        // dbDelta is required for reliable table creation/updates.
        if (!function_exists('dbDelta')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        // bits_forms table
        $forms_table = $wpdb->prefix . 'bits_forms';
        $sql_forms = "CREATE TABLE {$forms_table} (
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
        dbDelta($sql_forms);

        // bits_entries table
        $entries_table = $wpdb->prefix . 'bits_entries';
        $sql_entries = "CREATE TABLE {$entries_table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            form_id BIGINT(20) UNSIGNED NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'active',
            user_id BIGINT(20) UNSIGNED NULL,
            user_email VARCHAR(320) NULL,
            ip_hash VARBINARY(64) NULL,
            ip_plain VARBINARY(48) NULL,
            user_agent TEXT NULL,
            source_url TEXT NULL,
            currency CHAR(3) NULL,
            subtotal DECIMAL(20,6) NULL DEFAULT 0,
            discount_total DECIMAL(20,6) NULL DEFAULT 0,
            tax_total DECIMAL(20,6) NULL DEFAULT 0,
            shipping_total DECIMAL(20,6) NULL DEFAULT 0,
            grand_total DECIMAL(20,6) NULL DEFAULT 0,
            coupon_code VARCHAR(64) NULL,
            payment_status VARCHAR(20) NOT NULL DEFAULT 'none',
            gateway VARCHAR(20) NOT NULL DEFAULT 'none',
            transaction_id VARCHAR(191) NULL,
            metadata_json LONGTEXT NULL,
            created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
            updated_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY  (id),
            KEY form_id (form_id),
            KEY created_at (created_at),
            KEY status (status)
        ) {$charset_collate};";
        dbDelta($sql_entries);

        // bits_entry_meta table
        $entry_meta_table = $wpdb->prefix . 'bits_entry_meta';
        $sql_entry_meta = "CREATE TABLE {$entry_meta_table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            entry_id BIGINT(20) UNSIGNED NOT NULL,
            field_id VARCHAR(32) NOT NULL,
            value_long LONGTEXT NULL,
            value_num DECIMAL(20,6) NULL,
            value_text TEXT NULL,
            PRIMARY KEY  (id),
            KEY entry_id (entry_id),
            KEY field_id (field_id),
            KEY entry_field (entry_id, field_id)
        ) {$charset_collate};";
        dbDelta($sql_entry_meta);

        // bits_drafts table
        $drafts_table = $wpdb->prefix . 'bits_drafts';
        $sql_drafts = "CREATE TABLE {$drafts_table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            form_id BIGINT(20) UNSIGNED NOT NULL,
            token CHAR(64) NOT NULL,
            values_json LONGTEXT NULL,
            expires_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY  (id),
            UNIQUE KEY token (token),
            KEY form_id (form_id),
            KEY expires_at (expires_at)
        ) {$charset_collate};";
        dbDelta($sql_drafts);

        // bits_payments table
        $payments_table = $wpdb->prefix . 'bits_payments';
        $sql_payments = "CREATE TABLE {$payments_table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            entry_id BIGINT(20) UNSIGNED NOT NULL,
            gateway VARCHAR(20) NOT NULL,
            event VARCHAR(64) NOT NULL,
            amount DECIMAL(20,6) NOT NULL,
            currency CHAR(3) NOT NULL,
            payload_json LONGTEXT NULL,
            created_at DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY  (id),
            KEY entry_id (entry_id),
            KEY created_at (created_at)
        ) {$charset_collate};";
        dbDelta($sql_payments);
    }
}
