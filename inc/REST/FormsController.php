<?php
declare(strict_types=1);

namespace BITS\AccessibleForms\REST;

use BITS\AccessibleForms\Validator\SchemaValidator;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_Error;

/**
 * REST controller for Forms CRUD operations
 */
class FormsController extends BaseController
{
    private SchemaValidator $validator;

    public function __construct()
    {
        $this->validator = new SchemaValidator();
    }

    public function register_routes(): void
    {
        // List forms
        register_rest_route($this->namespace, '/forms', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'get_forms'],
            'permission_callback' => [$this, 'check_manage_forms_permission'],
            'args' => [
                'status' => [
                    'type' => 'string',
                    'enum' => ['active', 'inactive', 'trash'],
                    'default' => 'active',
                ],
                'per_page' => [
                    'type' => 'integer',
                    'default' => 20,
                    'minimum' => 1,
                    'maximum' => 100,
                ],
                'page' => [
                    'type' => 'integer',
                    'default' => 1,
                    'minimum' => 1,
                ],
            ],
        ]);

        // Create form
        register_rest_route($this->namespace, '/forms', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'create_form'],
            'permission_callback' => [$this, 'check_manage_forms_permission'],
        ]);

        // Get single form
        register_rest_route($this->namespace, '/forms/(?P<id>\d+)', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'get_form'],
            'permission_callback' => [$this, 'check_manage_forms_permission'],
            'args' => [
                'id' => [
                    'type' => 'integer',
                    'required' => true,
                ],
            ],
        ]);

        // Update form
        register_rest_route($this->namespace, '/forms/(?P<id>\d+)', [
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => [$this, 'update_form'],
            'permission_callback' => [$this, 'check_manage_forms_permission'],
            'args' => [
                'id' => [
                    'type' => 'integer',
                    'required' => true,
                ],
            ],
        ]);

        // Delete (trash) form
        register_rest_route($this->namespace, '/forms/(?P<id>\d+)', [
            'methods' => WP_REST_Server::DELETABLE,
            'callback' => [$this, 'delete_form'],
            'permission_callback' => [$this, 'check_manage_forms_permission'],
            'args' => [
                'id' => [
                    'type' => 'integer',
                    'required' => true,
                ],
            ],
        ]);

        // Export form
        register_rest_route($this->namespace, '/forms/(?P<id>\d+)/export', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'export_form'],
            'permission_callback' => [$this, 'check_manage_forms_permission'],
            'args' => [
                'id' => [
                    'type' => 'integer',
                    'required' => true,
                ],
            ],
        ]);

        // Import form
        register_rest_route($this->namespace, '/forms/import', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'import_form'],
            'permission_callback' => [$this, 'check_manage_forms_permission'],
        ]);

        // Lint form
        register_rest_route($this->namespace, '/forms/(?P<id>\d+)/lint', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, 'lint_form'],
            'permission_callback' => [$this, 'check_manage_forms_permission'],
            'args' => [
                'id' => [
                    'type' => 'integer',
                    'required' => true,
                ],
            ],
        ]);
    }

    /**
     * Get list of forms
     */
    public function get_forms(WP_REST_Request $request): WP_REST_Response
    {
        global $wpdb;
        $table = $wpdb->prefix . 'bits_forms';
        
        $status = $request->get_param('status');
        $per_page = $request->get_param('per_page');
        $page = $request->get_param('page');
        $offset = ($page - 1) * $per_page;

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, title, slug, status, created_at, updated_at, version 
                FROM {$table} 
                WHERE status = %s 
                ORDER BY created_at DESC 
                LIMIT %d OFFSET %d",
                $status,
                $per_page,
                $offset
            ),
            ARRAY_A
        );

        $total = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE status = %s",
                $status
            )
        );

        return $this->success_response([
            'forms' => $results,
            'total' => (int) $total,
            'page' => $page,
            'per_page' => $per_page,
            'total_pages' => ceil($total / $per_page),
        ]);
    }

    /**
     * Get single form
     */
    public function get_form(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        global $wpdb;
        $table = $wpdb->prefix . 'bits_forms';
        $id = (int) $request->get_param('id');

        $form = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE id = %d",
                $id
            ),
            ARRAY_A
        );

        if (!$form) {
            return $this->error_response(
                'form_not_found',
                'Form not found',
                404
            );
        }

        // Decode JSON fields
        $form['schema_json'] = $form['schema_json'] ? json_decode($form['schema_json'], true) : null;
        $form['settings_json'] = $form['settings_json'] ? json_decode($form['settings_json'], true) : null;

        return $this->success_response($form);
    }

    /**
     * Create new form
     */
    public function create_form(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        global $wpdb;
        $table = $wpdb->prefix . 'bits_forms';
        
        $body = $request->get_json_params();
        
        if (!isset($body['title']) || empty($body['title'])) {
            return $this->error_response(
                'missing_title',
                'Form title is required',
                400
            );
        }

        // Validate schema if provided
        if (isset($body['schema'])) {
            if (!$this->validator->validate($body['schema'])) {
                return $this->error_response(
                    'invalid_schema',
                    'Form schema validation failed',
                    400,
                    ['errors' => $this->validator->getErrors()]
                );
            }
        }

        // Generate slug from title
        $slug = sanitize_title($body['title']);
        $original_slug = $slug;
        $counter = 1;
        
        // Ensure unique slug
        while ($wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE slug = %s", $slug)) > 0) {
            $slug = $original_slug . '-' . $counter;
            $counter++;
        }

        $now = current_time('mysql', true);
        
        $data = [
            'title' => sanitize_text_field($body['title']),
            'slug' => $slug,
            'status' => isset($body['status']) ? sanitize_text_field($body['status']) : 'inactive',
            'schema_json' => isset($body['schema']) ? wp_json_encode($body['schema']) : null,
            'settings_json' => isset($body['settings']) ? wp_json_encode($body['settings']) : null,
            'next_field_id' => isset($body['next_field_id']) ? (int) $body['next_field_id'] : 1,
            'created_at' => $now,
            'updated_at' => $now,
            'version' => '1.0',
        ];

        $result = $wpdb->insert($table, $data);

        if ($result === false) {
            return $this->error_response(
                'create_failed',
                'Failed to create form',
                500
            );
        }

        $form_id = $wpdb->insert_id;
        
        return $this->success_response([
            'id' => $form_id,
            'message' => 'Form created successfully',
        ], 201);
    }

    /**
     * Update existing form
     */
    public function update_form(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        global $wpdb;
        $table = $wpdb->prefix . 'bits_forms';
        $id = (int) $request->get_param('id');
        $body = $request->get_json_params();

        // Check if form exists
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE id = %d", $id));
        if (!$exists) {
            return $this->error_response(
                'form_not_found',
                'Form not found',
                404
            );
        }

        // Validate schema if provided
        if (isset($body['schema'])) {
            if (!$this->validator->validate($body['schema'])) {
                return $this->error_response(
                    'invalid_schema',
                    'Form schema validation failed',
                    400,
                    ['errors' => $this->validator->getErrors()]
                );
            }
        }

        $data = [
            'updated_at' => current_time('mysql', true),
        ];

        if (isset($body['title'])) {
            $data['title'] = sanitize_text_field($body['title']);
        }

        if (isset($body['status'])) {
            $data['status'] = sanitize_text_field($body['status']);
        }

        if (isset($body['schema'])) {
            $data['schema_json'] = wp_json_encode($body['schema']);
        }

        if (isset($body['settings'])) {
            $data['settings_json'] = wp_json_encode($body['settings']);
        }

        if (isset($body['next_field_id'])) {
            $data['next_field_id'] = (int) $body['next_field_id'];
        }

        $result = $wpdb->update($table, $data, ['id' => $id]);

        if ($result === false) {
            return $this->error_response(
                'update_failed',
                'Failed to update form',
                500
            );
        }

        return $this->success_response([
            'id' => $id,
            'message' => 'Form updated successfully',
        ]);
    }

    /**
     * Delete (trash) form
     */
    public function delete_form(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        global $wpdb;
        $table = $wpdb->prefix . 'bits_forms';
        $id = (int) $request->get_param('id');

        // Check if form exists
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE id = %d", $id));
        if (!$exists) {
            return $this->error_response(
                'form_not_found',
                'Form not found',
                404
            );
        }

        // Soft delete - set status to trash
        $result = $wpdb->update(
            $table,
            [
                'status' => 'trash',
                'updated_at' => current_time('mysql', true),
            ],
            ['id' => $id]
        );

        if ($result === false) {
            return $this->error_response(
                'delete_failed',
                'Failed to delete form',
                500
            );
        }

        return $this->success_response([
            'id' => $id,
            'message' => 'Form moved to trash',
        ]);
    }

    /**
     * Export form as JSON
     */
    public function export_form(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        global $wpdb;
        $table = $wpdb->prefix . 'bits_forms';
        $id = (int) $request->get_param('id');

        $form = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT title, schema_json, settings_json, version FROM {$table} WHERE id = %d",
                $id
            ),
            ARRAY_A
        );

        if (!$form) {
            return $this->error_response(
                'form_not_found',
                'Form not found',
                404
            );
        }

        $export = [
            'version' => $form['version'] ?? '1.0',
            'title' => $form['title'],
            'schema' => $form['schema_json'] ? json_decode($form['schema_json'], true) : null,
            'settings' => $form['settings_json'] ? json_decode($form['settings_json'], true) : null,
            'exported_at' => current_time('mysql', true),
        ];

        return $this->success_response($export);
    }

    /**
     * Import form from JSON
     */
    public function import_form(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $body = $request->get_json_params();
        
        if (!isset($body['title'])) {
            return $this->error_response(
                'missing_title',
                'Form title is required',
                400
            );
        }

        // Validate imported schema
        if (isset($body['schema'])) {
            if (!$this->validator->validate($body['schema'])) {
                return $this->error_response(
                    'invalid_schema',
                    'Imported form schema validation failed',
                    400,
                    ['errors' => $this->validator->getErrors()]
                );
            }
        }

        // Create the form using existing create logic
        return $this->create_form($request);
    }

    /**
     * Lint form for heading structure issues
     */
    public function lint_form(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        global $wpdb;
        $table = $wpdb->prefix . 'bits_forms';
        $id = (int) $request->get_param('id');

        $form = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT schema_json FROM {$table} WHERE id = %d",
                $id
            ),
            ARRAY_A
        );

        if (!$form || !$form['schema_json']) {
            return $this->error_response(
                'form_not_found',
                'Form not found or has no schema',
                404
            );
        }

        $schema = json_decode($form['schema_json'], true);
        $warnings = [];

        // Check heading structure
        if (isset($schema['pages'])) {
            foreach ($schema['pages'] as $page_idx => $page) {
                if (isset($page['sections'])) {
                    $prev_level = 1;
                    foreach ($page['sections'] as $section_idx => $section) {
                        if (isset($section['headingLevel'])) {
                            $level = (int) $section['headingLevel'];
                            
                            // Check for skipped heading levels
                            if ($level > $prev_level + 1) {
                                $warnings[] = [
                                    'type' => 'heading_skip',
                                    'message' => "Section '{$section['label']}' skips from h{$prev_level} to h{$level}",
                                    'location' => [
                                        'page' => $page_idx,
                                        'section' => $section_idx,
                                    ],
                                    'severity' => 'warning',
                                ];
                            }
                            
                            $prev_level = $level;
                        }
                    }
                }
            }
        }

        return $this->success_response([
            'valid' => count($warnings) === 0,
            'warnings' => $warnings,
            'info_count' => 0,
            'warning_count' => count($warnings),
            'error_count' => 0,
        ]);
    }
}
