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

        // Reset database to fresh v1.0.0 (DEV ONLY)
        register_rest_route('mcf/v1', '/reset-database', [
            'methods' => 'POST',
            'callback' => [$this, 'reset_database'],
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

        // If fields structure is missing (old version), run migration
        if (!isset($options['fields'])) {
            require_once plugin_dir_path(dirname(__FILE__, 2)) . 'src/Core/Migration.php';
            \MinimalContactForm\Core\Migration::maybe_migrate();
            $options = get_option('mcf_options');
        }

        // Ensure labels and placeholders are objects, not arrays (for JSON serialization)
        if (empty($options['fields']['labels'])) {
            $options['fields']['labels'] = new \stdClass();
        }
        if (empty($options['fields']['placeholders'])) {
            $options['fields']['placeholders'] = new \stdClass();
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

        // Filter labels: only save non-empty custom labels
        $custom_labels = [];
        foreach ($params['fields']['labels'] as $key => $value) {
            // Only save if not empty (empty = back to default)
            if (!empty(trim($value))) {
                $custom_labels[$key] = sanitize_text_field($value);
            }
        }

        // Filter placeholders: only save non-empty custom placeholders
        $custom_placeholders = [];
        foreach ($params['fields']['placeholders'] as $key => $value) {
            // Only save if not empty
            if (!empty(trim($value))) {
                $custom_placeholders[$key] = sanitize_text_field($value);
            }
        }

        // Validate fields (still need validation for field_groups)
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
        $options['fields'] = [
            'field_groups' => $params['fields']['field_groups'],
            'labels' => $custom_labels,  // Only custom labels!
            'placeholders' => $custom_placeholders,  // Only custom placeholders!
            'hide_labels' => $params['fields']['hide_labels'] ?? false,
        ];
        $options['privacy_texts'] = [
            'optin_text' => sanitize_textarea_field($params['privacy_texts']['optin_text']),
            'inform_text' => sanitize_textarea_field($params['privacy_texts']['inform_text']),
        ];
        $options['styling'] = [
            'theme_preset' => sanitize_text_field($params['styling']['theme_preset'] ?? 'modern'),
            'variant' => sanitize_text_field($params['styling']['variant'] ?? 'light'),
            'primary_color' => sanitize_hex_color($params['styling']['primary_color'] ?? ''),
            'custom_css' => wp_strip_all_tags($params['styling']['custom_css'] ?? ''),
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
     * Reset database to fresh v1.0.0 structure (DEV ONLY)
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response Response object
     */
    public function reset_database($request)
    {
        // Delete old options
        delete_option('mcf_options');
        delete_option('mcf_options_backup_v0');
        delete_transient('mcf_migration_notice');

        // Create fresh v1.0.0 structure using centralized defaults
        $fresh_options = \MinimalContactForm\Core\Defaults::get_options();

        add_option('mcf_options', $fresh_options, '', true);

        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Database reset to fresh v1.0.0 structure',
            'data' => $fresh_options,
        ], 200);
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
