<?php

namespace MinimalContactForm\Core;

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
            'recipient_user_id' => 1,
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
    private static function get_privacy_texts()
    {
        return [
            'optin_text' => __('I consent to having you process my submitted information so you can respond to my inquiry.', 'mcf'),
            'inform_text' => __('Your submitted information will only be processed to respond to your inquiry.', 'mcf'),
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
}
