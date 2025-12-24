<?php

namespace MinimalContactForm\Core;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since 1.0.0
 */
class Activator
{
    /**
     * Activation hook.
     *
     * Writes default options to database and runs migration if needed.
     *
     * @since 1.0.0
     */
    public static function activate()
    {
        $options = get_option('mcf_options');

        // Run migration for existing installations
        Migration::maybe_migrate();

        // If options still don't exist (fresh install), create default structure
        $options = get_option('mcf_options');
        if (false === $options) {
            add_option(
                'mcf_options',
                [
                    'version' => '1.0.0',
                    'settings' => [
                        'recipient_user_id' => 1,
                        'gdpr_mode' => 'inform',
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
                    ],
                    'fields' => [
                        'field_groups' => [
                            'company' => ['enabled' => false],
                            'name' => ['mode' => 'split', 'enabled' => true],
                            'contact' => ['mode' => 'email', 'enabled' => true],
                            'subject' => ['enabled' => false],
                            'message' => ['enabled' => true],
                            'gdpr' => ['enabled' => true],
                            'submit' => ['alignment' => 'left'],
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
                            'submit' => 'Submit',
                            'gdpr-optin' => 'I consent to having you process my submitted information so you can respond to my inquiry.',
                            'gdpr-inform' => 'Your submitted information will only be processed to respond to your inquiry.',
                        ],
                        'placeholders' => [],
                    ],
                    'privacy_texts' => [
                        'optin_text' => 'I consent to having you process my submitted information so you can respond to my inquiry.',
                        'inform_text' => 'Your submitted information will only be processed to respond to your inquiry.',
                    ],
                    'styling' => [
                        'theme_preset' => 'modern',
                        'variant' => 'light',
                        'primary_color' => '',
                        'custom_css' => '',
                    ],
                ],
                '',
                true
            );
        }
    }
}
