<?php
declare(strict_types=1);

namespace BITS\AccessibleForms\REST;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

/**
 * Base REST controller with common functionality
 */
abstract class BaseController
{
    protected string $namespace = 'bits/v1';

    /**
     * Register routes for this controller
     */
    abstract public function register_routes(): void;

    /**
     * Check if user has capability to manage forms
     *
     * @return bool|WP_Error
     */
    protected function check_manage_forms_permission()
    {
        if (!current_user_can('manage_options')) {
            return new WP_Error(
                'rest_forbidden',
                __('You do not have permission to manage forms.', 'bits-accessible-forms'),
                ['status' => 403]
            );
        }

        return true;
    }

    /**
     * Verify nonce from request header
     *
     * @param WP_REST_Request $request
     * @return bool
     */
    protected function verify_nonce(WP_REST_Request $request): bool
    {
        $nonce = $request->get_header('X-WP-Nonce');
        
        if (!$nonce) {
            return false;
        }
        
        return wp_verify_nonce($nonce, 'wp_rest') !== false;
    }

    /**
     * Create REST response with standardized format
     *
     * @param mixed $data
     * @param int $status
     * @return WP_REST_Response
     */
    protected function success_response($data, int $status = 200): WP_REST_Response
    {
        return new WP_REST_Response($data, $status);
    }

    /**
     * Create error response
     *
     * @param string $code
     * @param string $message
     * @param int $status
     * @param array $data Additional error data
     * @return WP_Error
     */
    protected function error_response(string $code, string $message, int $status = 400, array $data = []): WP_Error
    {
        return new WP_Error($code, $message, array_merge(['status' => $status], $data));
    }
}
