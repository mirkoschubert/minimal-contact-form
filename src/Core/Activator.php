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

        // If options don't exist, create default structure for fresh install
        if (false === $options) {
            add_option(
                'mcf_options',
                array(
                    'version' => '1.0.0',
                    'settings' => array(
                        'recipient_user_id' => 1,
                        'gdpr_mode' => 'inform',
                        'antispam_enabled' => true,
                        'mail_service' => 'wp_mail',
                    ),
                    'fields' => array(
                        'order' => array('first-name', 'last-name', 'email', 'subject', 'message'),
                        'enabled' => array(
                            'company' => false,
                            'first-name' => true,
                            'last-name' => true,
                            'name' => false,
                            'phone' => false,
                            'email' => true,
                            'subject' => true,
                            'message' => true,
                        ),
                        'labels' => array(
                            'company' => __('Company', 'mcf'),
                            'first-name' => __('First Name', 'mcf'),
                            'last-name' => __('Last Name', 'mcf'),
                            'name' => __('Name', 'mcf'),
                            'phone' => __('Phone', 'mcf'),
                            'email' => __('Email', 'mcf'),
                            'subject' => __('Subject', 'mcf'),
                            'message' => __('Message', 'mcf'),
                        ),
                        'placeholders' => array(),
                        'custom_fields' => array(),
                    ),
                    'privacy_texts' => array(
                        'optin_text' => __('I consent to having you process my submitted information so you can respond to my inquiry.', 'mcf'),
                        'inform_text' => __('Your submitted information will only be processed to respond to your inquiry.', 'mcf'),
                    ),
                    'styling' => array(
                        'theme_preset' => 'light',
                        'advanced' => array(),
                    ),
                ),
                '',
                'yes'
            );
        }
    }
}
