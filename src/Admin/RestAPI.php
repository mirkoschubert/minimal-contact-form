<?php

namespace MinimalContactForm\Admin;


// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}
use MinimalContactForm\Models\Settings;
use MinimalContactForm\Models\FieldConfig;
use MinimalContactForm\Core\Defaults;

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

        // Reset database to fresh v1.0.0 (DEV ONLY)
        register_rest_route('mcf/v1', '/reset-database', [
            'methods' => 'POST',
            'callback' => [$this, 'reset_database'],
            'permission_callback' => [$this, 'check_admin_permission'],
        ]);

        // Get preview CSS (Base + Theme CSS only)
        register_rest_route('mcf/v1', '/preview-css', [
            'methods' => 'GET',
            'callback' => [$this, 'get_preview_css'],
            'permission_callback' => [$this, 'check_admin_permission'],
            'args' => [
                'theme' => [
                    'required' => true,
                    'type' => 'string',
                    'enum' => ['default', 'modern', 'minimal', 'custom'],
                ],
            ],
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
            // Use Plugin activation to create defaults
            \MinimalContactForm\Core\Plugin::activate();
            $options = get_option('mcf_options');
        }

        // If fields structure is missing (old version), run migration
        if (!isset($options['fields'])) {
            // Lazy-Load Migration mit class_exists Check
            if (!class_exists('MinimalContactForm\Core\Migration')) {
                require_once plugin_dir_path(dirname(__FILE__, 2)) . 'src/Core/Migration.php';
            }
            \MinimalContactForm\Core\Migration::maybe_migrate();
            $options = get_option('mcf_options');
        }

        // Auto-populate sender fields if empty (one-time conversion)
        if (empty($options['settings']['sender_email']) && !empty($options['settings']['recipient_user_id'])) {
            $user = get_user_by('id', $options['settings']['recipient_user_id']);
            if ($user) {
                $options['settings']['sender_name'] = $user->display_name;
                $options['settings']['sender_email'] = $user->user_email;
                $options['settings']['reply_to'] = '';
                // Save updated options
                update_option('mcf_options', $options);
            }
        }

        // Ensure labels and placeholders are objects, not arrays (for JSON serialization)
        if (empty($options['fields']['labels'])) {
            $options['fields']['labels'] = new \stdClass();
        }
        if (empty($options['fields']['placeholders'])) {
            $options['fields']['placeholders'] = new \stdClass();
        }

        // NOTE: Default labels are NOT added here anymore!
        // They are handled in the frontend (FormFieldRenderer, EditModal)
        // This ensures that only custom labels are stored in the database

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
     * Get preview CSS for FormPreview component
     *
     * Returns Base CSS + Theme CSS only (NO primary_color, NO custom_css).
     * Primary color and custom CSS are handled client-side for live updates.
     *
     * @since 1.0.0
     * @param \WP_REST_Request $request Request object
     * @return \WP_REST_Response Response object
     */
    public function get_preview_css($request)
    {
        $theme = $request->get_param('theme');

        // Prepare styling array for CSSService (base + theme only)
        $styling = [
            'theme_preset' => $theme,
            'variant' => 'light',  // Variant is handled via HTML attribute only
            'primary_color' => '', // NOT included - handled client-side
            'custom_css' => '',    // NOT included - handled client-side
        ];

        // Use CSSService to get Base CSS + Theme CSS only
        // Note: Use 'mcf-preview' as instance ID for admin preview
        $css = \MinimalContactForm\Services\CSSService::consolidate_inline_css(
            $styling,
            'mcf-preview'
        );

        return new \WP_REST_Response([
            'success' => true,
            'css' => $css,
            'theme' => $theme,
        ], 200);
    }

    /**
     * Get translated default labels for all form fields
     *
     * Returns an array of translated default labels using WordPress __() function.
     * These labels match the defaults used in the admin FormPreview component.
     *
     * @since 1.0.0
     * @return array Associative array of field_id => translated_label
     */
    private function get_default_labels()
    {
        return \MinimalContactForm\Core\Defaults::get_all_field_labels();
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
