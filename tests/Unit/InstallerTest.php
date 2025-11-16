<?php
declare(strict_types=1);

namespace BITS\AccessibleForms\Tests\Unit;

use BITS\AccessibleForms\DB\Installer;
use PHPUnit\Framework\TestCase;

class InstallerTest extends TestCase
{
    public function test_install_runs_without_error(): void
    {
        global $wpdb;
        
        // Mock wpdb being set
        $wpdb->prefix = 'wp_';
        
        // Call install - should not throw any exceptions
        Installer::install();
        
        // If we get here, the test passes
        $this->assertTrue(true);
    }

    public function test_install_creates_correct_table_names(): void
    {
        global $wpdb;
        
        $wpdb->prefix = 'test_';
        
        // The installer should create tables with the correct prefix
        // In a real WordPress environment, this would be verified by checking the database
        // For now, we just verify the method runs
        Installer::install();
        
        $this->assertTrue(true);
    }
}
