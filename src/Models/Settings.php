<?php

namespace MinimalContactForm\Models;


// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Settings Data Model
 *
 * Handles validation and access to plugin settings.
 *
 * @since 1.0.0
 */
class Settings
{
    /**
     * Settings data
     *
     * @var array
     */
    private $data;

    /**
     * Constructor
     *
     * @param array $data Settings data
     */
    public function __construct($data = [])
    {
        $this->data = wp_parse_args($data, self::get_defaults());
    }

    /**
     * Get default settings
     *
     * Uses centralized Defaults class for consistency.
     *
     * @since 1.0.0
     * @return array Default settings
     */
    public static function get_defaults()
    {
        return \MinimalContactForm\Core\Defaults::get_options()['settings'];
    }

    /**
     * Get a setting value
     *
     * @since 1.0.0
     * @param string $key Setting key
     * @return mixed Setting value
     */
    public function get($key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Set a setting value
     *
     * @since 1.0.0
     * @param string $key Setting key
     * @param mixed $value Setting value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Get all settings
     *
     * @since 1.0.0
     * @return array All settings
     */
    public function to_array()
    {
        return $this->data;
    }

    /**
     * Validate settings
     *
     * @since 1.0.0
     * @return bool|array True if valid, array of errors otherwise
     */
    public function validate()
    {
        $errors = [];

        // Validate new email fields (required if not using recipient_user_id)
        if (empty($this->data['recipient_user_id'])) {
            // New format validation
            if (empty($this->data['sender_name'])) {
                $errors['sender_name'] = 'Sender name is required';
            }
            if (empty($this->data['sender_email']) || !is_email($this->data['sender_email'])) {
                $errors['sender_email'] = 'Valid sender email is required';
            }
            if (!empty($this->data['reply_to']) && !is_email($this->data['reply_to'])) {
                $errors['reply_to'] = 'Reply-to must be a valid email address';
            }
        } else {
            // Legacy format validation
            if (!is_numeric($this->data['recipient_user_id'])) {
                $errors['recipient_user_id'] = 'Recipient user ID must be numeric';
            }
        }

        // Validate antispam_enabled
        if (!is_bool($this->data['antispam_enabled'])) {
            $errors['antispam_enabled'] = 'Antispam enabled must be boolean';
        }

        // Validate mail_service
        if (!in_array($this->data['mail_service'], ['wp_mail', 'php_mail', 'smtp'])) {
            $errors['mail_service'] = 'Mail service must be "wp_mail", "php_mail", or "smtp"';
        }

        // Validate SMTP config if SMTP is selected
        if ($this->data['mail_service'] === 'smtp') {
            $smtp = $this->data['smtp_config'];

            if (empty($smtp['host'])) {
                $errors['smtp_host'] = 'SMTP host is required when using SMTP';
            }

            if (!is_numeric($smtp['port']) || $smtp['port'] < 1 || $smtp['port'] > 65535) {
                $errors['smtp_port'] = 'SMTP port must be between 1 and 65535';
            }

            if (empty($smtp['username'])) {
                $errors['smtp_username'] = 'SMTP username is required';
            }

            if (empty($smtp['password'])) {
                $errors['smtp_password'] = 'SMTP password is required';
            }

            if (!in_array($smtp['encryption'], ['tls', 'ssl', 'none'])) {
                $errors['smtp_encryption'] = 'SMTP encryption must be "tls", "ssl", or "none"';
            }
        }

        return empty($errors) ? true : $errors;
    }
}
