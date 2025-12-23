<?php

namespace MinimalContactForm\Core;

use MinimalContactForm\Admin\ThemePresets;

/**
 * Handle plugin data migration from old versions.
 *
 * @since 1.0.0
 */
class Migration
{
    /**
     * Run migration if needed
     *
     * @since 1.0.0
     */
    public static function maybe_migrate()
    {
        $options = get_option('mcf_options');

        // No options exist - fresh install, nothing to migrate
        if (false === $options) {
            return;
        }

        // Check if migration is needed
        if (!isset($options['version']) || version_compare($options['version'], '1.0.0', '<')) {
            self::migrate_from_legacy($options);
        }
    }

    /**
     * Migrate from legacy (v0.10.0 and earlier) to v1.0.0
     *
     * @since 1.0.0
     * @param array $old Old options structure
     */
    private static function migrate_from_legacy($old)
    {
        // Create backup
        update_option('mcf_options_backup_v0', $old, false);

        // Transform to new structure
        $new = [
            'version' => '1.0.0',
            'settings' => self::transform_settings($old),
            'fields' => self::transform_fields($old),
            'privacy_texts' => self::get_default_privacy_texts(),
            'styling' => self::transform_styling($old),
        ];

        // Update options
        update_option('mcf_options', $new);

        // Set migration notice
        set_transient('mcf_migration_notice', true, HOUR_IN_SECONDS);
    }

    /**
     * Transform legacy settings to new structure
     *
     * @since 1.0.0
     * @param array $old Old options
     * @return array New settings structure
     */
    private static function transform_settings($old)
    {
        $settings = isset($old['settings']) ? $old['settings'] : [];

        return [
            'recipient_user_id' => $settings['user'] ?? 1,
            'gdpr_mode' => isset($settings['gdpr']) && $settings['gdpr'] === 1 ? 'optin' : 'inform',
            'antispam_enabled' => !isset($settings['spam']) || $settings['spam'] === 1,
            'mail_service' => isset($settings['phpmail']) && $settings['phpmail'] === 1 ? 'php_mail' : 'wp_mail',
        ];
    }

    /**
     * Transform legacy layout to new fields structure
     *
     * @since 1.0.0
     * @param array $old Old options
     * @return array New fields structure
     */
    private static function transform_fields($old)
    {
        $layout = isset($old['layout']) ? $old['layout'] : [];

        // Default order (all available fields)
        $default_order = ['company', 'first-name', 'last-name', 'name', 'phone', 'email', 'subject', 'message'];

        return [
            'order' => $default_order,
            'enabled' => [
                'company' => isset($layout['company']) && $layout['company'] === 1,
                'first-name' => !isset($layout['first-name']) || $layout['first-name'] === 1,
                'last-name' => !isset($layout['last-name']) || $layout['last-name'] === 1,
                'name' => isset($layout['name']) && $layout['name'] === 1,
                'phone' => isset($layout['phone']) && $layout['phone'] === 1,
                'email' => true, // Always enabled
                'subject' => !isset($layout['subject']) || $layout['subject'] === 1,
                'message' => true, // Always enabled
            ],
            'labels' => [
                'company' => 'Company',
                'first-name' => 'First Name',
                'last-name' => 'Last Name',
                'name' => 'Name',
                'phone' => 'Phone',
                'email' => 'Email',
                'subject' => 'Subject',
                'message' => 'Message',
            ],
            'placeholders' => [],
            'custom_fields' => [],
        ];
    }

    /**
     * Get default privacy texts
     *
     * @since 1.0.0
     * @return array Privacy texts
     */
    private static function get_default_privacy_texts()
    {
        return [
            'optin_text' => 'I consent to having you process my submitted information so you can respond to my inquiry.',
            'inform_text' => 'Your submitted information will only be processed to respond to your inquiry.',
        ];
    }

    /**
     * Transform legacy styling to new structure with theme detection
     *
     * @since 1.0.0
     * @param array $old Old options
     * @return array New styling structure
     */
    private static function transform_styling($old)
    {
        $styling = isset($old['styling']) ? $old['styling'] : [];

        // Detect closest theme preset
        $theme_preset = self::detect_theme_preset($styling);

        return [
            'theme_preset' => $theme_preset,
            'advanced' => $styling, // Preserve all old styling settings in advanced
        ];
    }

    /**
     * Detect which theme preset matches the old styling best
     *
     * @since 1.0.0
     * @param array $old_styling Old styling options
     * @return string Theme preset name
     */
    private static function detect_theme_preset($old_styling)
    {
        // Count how many custom colors are set
        $custom_count = 0;
        $color_fields = [
            'item-text-color',
            'item-placeholder-color',
            'item-background-color',
            'item-border-color',
            'button-text-color',
            'button-background-color',
            'button-border-color',
        ];

        foreach ($color_fields as $field) {
            if (isset($old_styling[$field]) && !empty($old_styling[$field])) {
                $custom_count++;
            }
        }

        // If heavily customized (5+ color fields set), use custom theme
        if ($custom_count >= 5) {
            return 'custom';
        }

        // Check button background color for basic theme detection
        $btn_bg = $old_styling['button-background-color'] ?? '#222222';

        // Simple heuristic based on button color lightness
        if (self::is_light_color($btn_bg)) {
            return 'dark'; // Light button color suggests dark theme
        } elseif ($btn_bg === '#222222' || $btn_bg === '#333333') {
            return 'light'; // Default dark button suggests light theme
        } else {
            return 'modern'; // Other colors suggest modern theme
        }
    }

    /**
     * Check if a hex color is light
     *
     * @since 1.0.0
     * @param string $hex Hex color code
     * @return bool True if color is light
     */
    private static function is_light_color($hex)
    {
        $hex = str_replace('#', '', $hex);

        // Default to dark if invalid hex
        if (strlen($hex) !== 6) {
            return false;
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Calculate perceived brightness
        $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;

        return $brightness > 155;
    }

    /**
     * Rollback to previous version
     *
     * @since 1.0.0
     * @return bool True on success, false on failure
     */
    public static function rollback()
    {
        $backup = get_option('mcf_options_backup_v0');

        if ($backup) {
            update_option('mcf_options', $backup);
            delete_option('mcf_options_backup_v0');
            set_transient('mcf_rollback_notice', true, HOUR_IN_SECONDS);
            return true;
        }

        return false;
    }
}
