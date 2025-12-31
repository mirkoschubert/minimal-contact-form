<?php

namespace MinimalContactForm\Core;


// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Central default values for all plugin options
 *
 * Single source of truth for default configuration values.
 * Used by Activator and reset_database endpoint.
 *
 * @since 1.0.0
 */
class Defaults
{
    /**
     * Get complete default options structure
     *
     * @since 1.0.0
     * @return array Complete default options
     */
    public static function get_options()
    {
        return [
            'version' => '1.0.0',
            'settings' => self::get_settings(),
            'fields' => self::get_fields(),
            'privacy_texts' => self::get_privacy_texts(),
            'styling' => self::get_styling(),
        ];
    }

    /**
     * Get default settings
     *
     * @since 1.0.0
     * @return array Default settings configuration
     */
    private static function get_settings()
    {
        return [
            'recipient_user_id' => 1,        // Keep for backwards compat
            'sender_name' => '',             // New
            'sender_email' => '',            // New
            'reply_to' => '',                // New
            'antispam_enabled' => true,
            'mail_service' => 'wp_mail',
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
     * Get default field configuration
     *
     * @since 1.0.0
     * @return array Default field configuration
     */
    private static function get_fields()
    {
        return [
            'field_groups' => [
                'company' => ['enabled' => false],
                'name' => ['mode' => 'split', 'enabled' => true],
                'contact' => ['mode' => 'email', 'enabled' => true],
                'subject' => ['enabled' => false],
                'message' => ['enabled' => true],
                'gdpr' => ['enabled' => true, 'mode' => 'inform'],
                'submit' => ['alignment' => 'left'],
            ],
            'labels' => [],           // Empty! Only custom labels are stored
            'placeholders' => [],     // Empty! Only custom placeholders are stored
        ];
    }

    /**
     * Get default privacy texts
     *
     * @since 1.0.0
     * @return array Default privacy texts
     */
    public static function get_privacy_texts()
    {
        return [
            'optin_text' => '',
            'inform_text' => '',
        ];
    }

    /**
     * Get default styling configuration
     *
     * @since 1.0.0
     * @return array Default styling configuration
     */
    private static function get_styling()
    {
        return [
            'theme_preset' => 'modern',
            'variant' => 'light',
            'primary_color' => '',
            'custom_css' => '',
        ];
    }

    /**
     * Get all default field labels as associative array.
     *
     * This is the single source of truth for default labels.
     *
     * @since 1.0.0
     * @return array field_id => translated_label
     */
    public static function get_all_field_labels()
    {
        return [
            'company'    => __('Company', 'mcf'),
            'name'       => __('Name', 'mcf'),
            'first-name' => __('First Name', 'mcf'),
            'last-name'  => __('Last Name', 'mcf'),
            'email'      => __('Email', 'mcf'),
            'phone'      => __('Phone', 'mcf'),
            'subject'    => __('Subject', 'mcf'),
            'message'    => __('Message', 'mcf'),
            'submit'     => __('Submit', 'mcf'),
        ];
    }

    /**
     * Get translatable default label for a field ID.
     *
     * Returns translated default labels for all form field IDs.
     * This ensures consistent translations across frontend rendering
     * and admin preview.
     *
     * @since 1.0.0
     * @param string $field_id The field ID (e.g., 'name', 'email', 'company')
     * @return string Translated default label
     */
    public static function get_field_label($field_id)
    {
        $defaults = self::get_all_field_labels();

        if (isset($defaults[$field_id])) {
            return $defaults[$field_id];
        }

        // Fallback: Convert field ID to title case
        return ucwords(str_replace('-', ' ', $field_id));
    }
}
