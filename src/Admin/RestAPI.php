<?php

namespace MinimalContactForm\Admin;

use MinimalContactForm\Models\Settings;
use MinimalContactForm\Models\FieldConfig;

/**
 * REST API endpoints for React admin
 *
 * @since 1.0.0
 */
class RestAPI
{
    /**
     * Register REST API routes
     *
     * @since 1.0.0
     */
    public function register_routes()
    {
        // Get all settings
        register_rest_route('mcf/v1', '/settings', [
            'methods' => 'GET',
            'callback' => [$this, 'get_settings'],
            'permission_callback' => [$this, 'check_admin_permission'],
        ]);

        // Update all settings
        register_rest_route('mcf/v1', '/settings', [
            'methods' => 'POST',
            'callback' => [$this, 'update_settings'],
            'permission_callback' => [$this, 'check_admin_permission'],
            'args' => [
                'settings' => [
                    'required' => true,
                    'type' => 'object',
                ],
                'fields' => [
                    'required' => true,
                    'type' => 'object',
                ],
                'privacy_texts' => [
                    'required' => true,
                    'type' => 'object',
                ],
                'styling' => [
                    'required' => true,
                    'type' => 'object',
                ],
            ],
        ]);

        // Get theme presets
        register_rest_route('mcf/v1', '/presets', [
            'methods' => 'GET',
            'callback' => [$this, 'get_presets'],
            'permission_callback' => [$this, 'check_admin_permission'],
        ]);

        // Get users for recipient dropdown
        register_rest_route('mcf/v1', '/users', [
            'methods' => 'GET',
            'callback' => [$this, 'get_users'],
            'permission_callback' => [$this, 'check_admin_permission'],
        ]);
    }

    /**
     * Get all settings
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response Response object
     */
    public function get_settings($request)
    {
        $options = get_option('mcf_options');

        // If no options exist, create defaults (fresh install or after uninstall)
        if (!$options) {
            // Use Activator defaults
            require_once plugin_dir_path(dirname(__FILE__, 2)) . 'src/Core/Activator.php';
            \MinimalContactForm\Core\Activator::activate();
            $options = get_option('mcf_options');
        }

        return new \WP_REST_Response($options, 200);
    }

    /**
     * Update all settings
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response Response object
     */
    public function update_settings($request)
    {
        $params = $request->get_params();

        // Validate settings
        $settings = new Settings($params['settings']);
        $validation = $settings->validate();
        if ($validation !== true) {
            return new \WP_REST_Response([
                'error' => 'Validation failed',
                'details' => $validation,
            ], 400);
        }

        // Validate fields
        $fields = new FieldConfig($params['fields']);
        $validation = $fields->validate();
        if ($validation !== true) {
            return new \WP_REST_Response([
                'error' => 'Validation failed',
                'details' => $validation,
            ], 400);
        }

        // Build options array
        $options = get_option('mcf_options');
        $options['settings'] = $settings->to_array();
        $options['fields'] = $fields->to_array();
        $options['privacy_texts'] = [
            'optin_text' => sanitize_textarea_field($params['privacy_texts']['optin_text']),
            'inform_text' => sanitize_textarea_field($params['privacy_texts']['inform_text']),
        ];
        $options['styling'] = [
            'theme_preset' => sanitize_text_field($params['styling']['theme_preset']),
            'advanced' => $params['styling']['advanced'] ?? [],
        ];

        // Update options
        update_option('mcf_options', $options);

        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => $options,
        ], 200);
    }

    /**
     * Get theme presets
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response Response object
     */
    public function get_presets($request)
    {
        $presets = ThemePresets::get_presets();

        return new \WP_REST_Response($presets, 200);
    }

    /**
     * Get users for recipient dropdown
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response Response object
     */
    public function get_users($request)
    {
        $users = get_users([
            'fields' => ['ID', 'display_name', 'user_email'],
            'orderby' => 'display_name',
        ]);

        $formatted_users = array_map(function ($user) {
            return [
                'id' => $user->ID,
                'name' => $user->display_name,
                'email' => $user->user_email,
            ];
        }, $users);

        return new \WP_REST_Response($formatted_users, 200);
    }

    /**
     * Check if user has admin permission
     *
     * @since 1.0.0
     * @return bool True if user can manage options
     */
    public function check_admin_permission()
    {
        return current_user_can('manage_options');
    }
}
