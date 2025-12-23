<?php

namespace MinimalContactForm\Models;

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
     * @since 1.0.0
     * @return array Default settings
     */
    public static function get_defaults()
    {
        return [
            'recipient_user_id' => 1,
            'gdpr_mode' => 'inform',
            'antispam_enabled' => true,
            'mail_service' => 'wp_mail',
        ];
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

        // Validate recipient_user_id
        if (!is_numeric($this->data['recipient_user_id'])) {
            $errors['recipient_user_id'] = 'Recipient user ID must be numeric';
        }

        // Validate gdpr_mode
        if (!in_array($this->data['gdpr_mode'], ['inform', 'optin'])) {
            $errors['gdpr_mode'] = 'GDPR mode must be either "inform" or "optin"';
        }

        // Validate antispam_enabled
        if (!is_bool($this->data['antispam_enabled'])) {
            $errors['antispam_enabled'] = 'Antispam enabled must be boolean';
        }

        // Validate mail_service
        if (!in_array($this->data['mail_service'], ['wp_mail', 'php_mail'])) {
            $errors['mail_service'] = 'Mail service must be either "wp_mail" or "php_mail"';
        }

        return empty($errors) ? true : $errors;
    }
}
