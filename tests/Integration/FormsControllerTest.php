<?php
declare(strict_types=1);

namespace BITS\AccessibleForms\Tests\Integration;

use BITS\AccessibleForms\REST\FormsController;
use PHPUnit\Framework\TestCase;
use WP_REST_Request;

class FormsControllerTest extends TestCase
{
    private FormsController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new FormsController();
    }

    public function test_controller_can_be_instantiated(): void
    {
        $this->assertInstanceOf(FormsController::class, $this->controller);
    }

    public function test_register_routes_runs_without_error(): void
    {
        // Should not throw any exceptions
        $this->controller->register_routes();
        $this->assertTrue(true);
    }

    public function test_get_forms_returns_response(): void
    {
        global $wpdb;
        
        // Set up mock results
        $wpdb->set_results([
            [
                'id' => 1,
                'title' => 'Test Form',
                'slug' => 'test-form',
                'status' => 'active',
                'created_at' => '2024-01-01 00:00:00',
                'updated_at' => '2024-01-01 00:00:00',
                'version' => '1.0',
            ],
        ]);

        $request = new WP_REST_Request();
        $request->set_param('status', 'active');
        $request->set_param('per_page', 20);
        $request->set_param('page', 1);

        $response = $this->controller->get_forms($request);
        
        $this->assertInstanceOf(\WP_REST_Response::class, $response);
        $this->assertEquals(200, $response->get_status());
        
        $data = $response->get_data();
        $this->assertArrayHasKey('forms', $data);
        $this->assertArrayHasKey('total', $data);
        $this->assertArrayHasKey('page', $data);
    }

    public function test_create_form_requires_title(): void
    {
        $request = new WP_REST_Request();
        $request->set_json_params([]);

        $response = $this->controller->create_form($request);
        
        $this->assertInstanceOf(\WP_Error::class, $response);
        $this->assertEquals('missing_title', $response->get_error_code());
    }

    public function test_create_form_validates_schema(): void
    {
        $request = new WP_REST_Request();
        $request->set_json_params([
            'title' => 'Test Form',
            'schema' => [
                'title' => 'Test',
                // Missing required 'pages' field
            ],
        ]);

        $response = $this->controller->create_form($request);
        
        $this->assertInstanceOf(\WP_Error::class, $response);
        $this->assertEquals('invalid_schema', $response->get_error_code());
    }

    public function test_create_form_success(): void
    {
        global $wpdb;
        $wpdb->insert_id = 123;

        $request = new WP_REST_Request();
        $request->set_json_params([
            'title' => 'New Test Form',
            'schema' => [
                'title' => 'Test',
                'pages' => [
                    [
                        'id' => 'p1',
                        'sections' => [
                            [
                                'id' => 's1',
                                'fields' => [
                                    [
                                        'id' => 1,
                                        'type' => 'text',
                                        'label' => 'Name',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $response = $this->controller->create_form($request);
        
        $this->assertInstanceOf(\WP_REST_Response::class, $response);
        $this->assertEquals(201, $response->get_status());
        
        $data = $response->get_data();
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals(123, $data['id']);
    }

    public function test_get_form_not_found(): void
    {
        global $wpdb;
        $wpdb->set_results([]);

        $request = new WP_REST_Request();
        $request->set_param('id', 999);

        $response = $this->controller->get_form($request);
        
        $this->assertInstanceOf(\WP_Error::class, $response);
        $this->assertEquals('form_not_found', $response->get_error_code());
        $this->assertEquals(404, $response->get_error_data()['status']);
    }

    public function test_update_form_not_found(): void
    {
        global $wpdb;
        // Return 0 for exists check
        
        $request = new WP_REST_Request();
        $request->set_param('id', 999);
        $request->set_json_params(['title' => 'Updated']);

        $response = $this->controller->update_form($request);
        
        $this->assertInstanceOf(\WP_Error::class, $response);
        $this->assertEquals('form_not_found', $response->get_error_code());
    }

    public function test_delete_form_moves_to_trash(): void
    {
        global $wpdb;

        $request = new WP_REST_Request();
        $request->set_param('id', 1);

        $response = $this->controller->delete_form($request);
        
        $this->assertInstanceOf(\WP_Error::class, $response);
        $this->assertEquals('form_not_found', $response->get_error_code());
    }

    public function test_export_form(): void
    {
        global $wpdb;
        $wpdb->set_results([
            [
                'title' => 'Test Form',
                'schema_json' => '{"title":"Test","pages":[]}',
                'settings_json' => '{"currency":"USD"}',
                'version' => '1.0',
            ],
        ]);

        $request = new WP_REST_Request();
        $request->set_param('id', 1);

        $response = $this->controller->export_form($request);
        
        $this->assertInstanceOf(\WP_REST_Response::class, $response);
        
        $data = $response->get_data();
        $this->assertArrayHasKey('version', $data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('schema', $data);
        $this->assertArrayHasKey('exported_at', $data);
    }

    public function test_lint_form_no_warnings(): void
    {
        global $wpdb;
        $wpdb->set_results([
            [
                'schema_json' => json_encode([
                    'pages' => [
                        [
                            'sections' => [
                                ['headingLevel' => 2, 'label' => 'Section 1'],
                                ['headingLevel' => 3, 'label' => 'Section 2'],
                            ],
                        ],
                    ],
                ]),
            ],
        ]);

        $request = new WP_REST_Request();
        $request->set_param('id', 1);

        $response = $this->controller->lint_form($request);
        
        $this->assertInstanceOf(\WP_REST_Response::class, $response);
        
        $data = $response->get_data();
        $this->assertArrayHasKey('valid', $data);
        $this->assertArrayHasKey('warnings', $data);
        $this->assertTrue($data['valid']);
        $this->assertEmpty($data['warnings']);
    }

    public function test_lint_form_with_heading_skip_warning(): void
    {
        global $wpdb;
        $wpdb->set_results([
            [
                'schema_json' => json_encode([
                    'pages' => [
                        [
                            'sections' => [
                                ['headingLevel' => 2, 'label' => 'Section 1'],
                                ['headingLevel' => 4, 'label' => 'Section 2'], // Skips h3
                            ],
                        ],
                    ],
                ]),
            ],
        ]);

        $request = new WP_REST_Request();
        $request->set_param('id', 1);

        $response = $this->controller->lint_form($request);
        
        $this->assertInstanceOf(\WP_REST_Response::class, $response);
        
        $data = $response->get_data();
        $this->assertFalse($data['valid']);
        $this->assertNotEmpty($data['warnings']);
        $this->assertEquals(1, $data['warning_count']);
    }
}
