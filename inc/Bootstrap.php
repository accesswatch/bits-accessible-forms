<?php
declare(strict_types=1);

namespace BITS\AccessibleForms;

use BITS\AccessibleForms\DB\Installer;

class Bootstrap
{
    public function init(): void
    {
        $this->register_caps();
        $this->register_assets();
        $this->register_rest();
    }

    public static function activate(): void
    {
        // Run DB migrations/installation.
        if (class_exists(Installer::class)) {
            Installer::install();
        }
        // Store schema version option for future migrations.
        if (function_exists('add_option')) {
            add_option('bits_schema_version', '1.0');
        }
    }

    public static function deactivate(): void
    {
        // Reserved for scheduled event cleanup or cache purge.
    }

    private function register_assets(): void
    {
        // Enqueue admin/public assets later (builder, renderer). Stub for now.
        // add_action('admin_enqueue_scripts', ...);
        // add_action('wp_enqueue_scripts', ...);
    }

    private function register_caps(): void
    {
        // Define custom capabilities; mapping to roles can be done on activation in future.
        // manage_bits_forms, edit_bits_forms, view_bits_entries, export_bits_entries
    }

    private function register_rest(): void
    {
        // Register REST routes in later sprint.
        // add_action('rest_api_init', function () { ... });
    }
}
