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
            'antispam_enabled' => !isset($settings['spam']) || $settings['spam'] === 1,
            'mail_service' => isset($settings['phpmail']) && $settings['phpmail'] === 1 ? 'php_mail' : 'wp_mail',
            'smtp_config' => [
                'enabled' => false,
                'host' => '',
                'port' => 587,
                'username' => '',
                'password' => '',
                'encryption' => 'tls',
                'from_name' => '',
                'from_email' => '',
            ],
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
        $labels = isset($old['labels']) ? $old['labels'] : [];
        $placeholders = isset($old['placeholders']) ? $old['placeholders'] : [];
        $settings = isset($old['settings']) ? $old['settings'] : [];

        // Build old-style structure for migration
        $old_fields = [
            'enabled' => [
                'company' => isset($layout['company']) && $layout['company'] === 1,
                'first-name' => !isset($layout['first-name']) || $layout['first-name'] === 1,
                'last-name' => !isset($layout['last-name']) || $layout['last-name'] === 1,
                'name' => isset($layout['name']) && $layout['name'] === 1,
                'phone' => isset($layout['phone']) && $layout['phone'] === 1,
                'email' => true,
                'subject' => !isset($layout['subject']) || $layout['subject'] === 1,
                'message' => true,
            ],
            'labels' => $labels,
            'placeholders' => $placeholders,
        ];

        // Extract GDPR mode from old settings
        $gdpr_mode = isset($settings['gdpr']) && $settings['gdpr'] === 1 ? 'optin' : 'inform';

        return self::migrate_to_field_groups($old_fields, $gdpr_mode);
    }

    /**
     * Migrate old fields structure to new field_groups structure
     *
     * @since 1.0.0
     * @param array $old_fields Old fields structure
     * @param string $gdpr_mode GDPR mode from old settings
     * @return array New field_groups structure
     */
    private static function migrate_to_field_groups($old_fields, $gdpr_mode = 'inform')
    {
        $enabled = $old_fields['enabled'] ?? [];

        // Detect name mode
        $name_mode = 'split';
        if (isset($enabled['name']) && $enabled['name']) {
            // Single name field is enabled
            $name_mode = 'single';
        } elseif (
            (!isset($enabled['first-name']) || !$enabled['first-name']) &&
            (!isset($enabled['last-name']) || !$enabled['last-name'])
        ) {
            // Both split fields disabled = user prefers single
            $name_mode = 'single';
        }

        // Detect contact mode
        $contact_mode = 'email';
        if (isset($enabled['phone']) && $enabled['phone']) {
            $contact_mode = 'email-phone';
        }

        return [
            'field_groups' => [
                'company' => ['enabled' => $enabled['company'] ?? false],
                'name' => ['mode' => $name_mode, 'enabled' => true],
                'contact' => ['mode' => $contact_mode, 'enabled' => true],
                'subject' => ['enabled' => $enabled['subject'] ?? true],
                'message' => ['enabled' => true],
                'gdpr' => ['enabled' => true, 'mode' => $gdpr_mode],
                'submit' => ['alignment' => 'left'],
            ],
            'labels' => $old_fields['labels'] ?: self::get_default_labels(),
            'placeholders' => $old_fields['placeholders'] ?: [],
            'hide_labels' => false,
        ];
    }

    /**
     * Get default labels
     *
     * @since 1.0.0
     * @return array Default labels
     */
    private static function get_default_labels()
    {
        return [
            'company' => 'Company',
            'first-name' => 'First Name',
            'last-name' => 'Last Name',
            'name' => 'Name',
            'phone' => 'Phone',
            'email' => 'Email',
            'subject' => 'Subject',
            'message' => 'Message',
            'submit' => 'Submit',
            'gdpr-optin' => 'I consent to having you process my submitted information so you can respond to my inquiry.',
            'gdpr-inform' => 'Your submitted information will only be processed to respond to your inquiry.',
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

        // Extract custom CSS from old structure
        $custom_css = $old['css'] ?? '';
        if (empty($custom_css) && isset($styling['misc-custom-css'])) {
            $custom_css = $styling['misc-custom-css'];
        }

        // Detect closest theme preset
        $old_theme = self::detect_theme_preset($styling);

        // Map old themes to new structure
        $theme_map = [
            'light' => 'modern',
            'dark' => 'modern',
            'modern' => 'modern',
            'minimal' => 'minimal',
            'custom' => 'modern',
        ];

        $new_theme = $theme_map[$old_theme] ?? 'modern';
        $variant = in_array($old_theme, ['dark']) ? 'dark' : 'light';

        // Extract primary color from button background
        $primary_color = $styling['button-background-color'] ?? '';

        return [
            'theme_preset' => $new_theme,
            'variant' => $variant,
            'primary_color' => $primary_color,
            'custom_css' => $custom_css,
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
