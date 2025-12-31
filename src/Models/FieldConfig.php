<?php

namespace MinimalContactForm\Models;


// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Field Configuration Data Model
 *
 * Handles validation and access to form field configuration.
 *
 * @since 1.0.0
 */
class FieldConfig
{
    /**
     * Field configuration data
     *
     * @var array
     */
    private $data;

    /**
     * Available field IDs
     *
     * @var array
     */
    private static $available_fields = [
        'company',
        'first-name',
        'last-name',
        'name',
        'phone',
        'email',
        'subject',
        'message',
    ];

    /**
     * Constructor
     *
     * @param array $data Field configuration data
     */
    public function __construct($data = [])
    {
        $this->data = wp_parse_args($data, self::get_defaults());
    }

    /**
     * Get default field configuration
     *
     * Uses centralized Defaults class for consistency.
     *
     * @since 1.0.0
     * @return array Default field configuration
     */
    public static function get_defaults()
    {
        return \MinimalContactForm\Core\Defaults::get_options()['fields'];
    }

    /**
     * Get field groups
     *
     * @since 1.0.0
     * @return array Field groups configuration
     */
    public function get_field_groups()
    {
        return $this->data['field_groups'];
    }

    /**
     * Get a specific field group
     *
     * @since 1.0.0
     * @param string $group Group name
     * @return array|null Group configuration or null
     */
    public function get_group($group)
    {
        return $this->data['field_groups'][$group] ?? null;
    }

    /**
     * Update a field group
     *
     * @since 1.0.0
     * @param string $group Group name
     * @param array $config Group configuration
     */
    public function set_group($group, $config)
    {
        $this->data['field_groups'][$group] = $config;
    }

    /**
     * Get field label
     *
     * @since 1.0.0
     * @param string $field_id Field ID
     * @return string Field label
     */
    public function get_label($field_id)
    {
        return $this->data['labels'][$field_id] ?? '';
    }

    /**
     * Set field label
     *
     * @since 1.0.0
     * @param string $field_id Field ID
     * @param string $label New label
     */
    public function set_label($field_id, $label)
    {
        $this->data['labels'][$field_id] = sanitize_text_field($label);
    }

    /**
     * Get field placeholder
     *
     * @since 1.0.0
     * @param string $field_id Field ID
     * @return string Field placeholder
     */
    public function get_placeholder($field_id)
    {
        return $this->data['placeholders'][$field_id] ?? $this->get_label($field_id);
    }

    /**
     * Set field placeholder
     *
     * @since 1.0.0
     * @param string $field_id Field ID
     * @param string $placeholder New placeholder
     */
    public function set_placeholder($field_id, $placeholder)
    {
        $this->data['placeholders'][$field_id] = sanitize_text_field($placeholder);
    }


    /**
     * Get all field configuration
     *
     * @since 1.0.0
     * @return array All field configuration
     */
    public function to_array()
    {
        return $this->data;
    }

    /**
     * Validate field configuration
     *
     * @since 1.0.0
     * @return bool|array True if valid, array of errors otherwise
     */
    public function validate()
    {
        $errors = [];

        // Validate field_groups structure exists
        if (!isset($this->data['field_groups'])) {
            $errors['field_groups'] = 'field_groups configuration is required';
            return $errors;
        }

        $groups = $this->data['field_groups'];

        // Validate name mode
        if (isset($groups['name']['mode']) && !in_array($groups['name']['mode'], ['single', 'split'])) {
            $errors['name_mode'] = 'Name mode must be "single" or "split"';
        }

        // Validate contact mode
        if (isset($groups['contact']['mode']) && !in_array($groups['contact']['mode'], ['email', 'email-phone'])) {
            $errors['contact_mode'] = 'Contact mode must be "email" or "email-phone"';
        }

        // Validate submit alignment
        if (isset($groups['submit']['alignment']) && !in_array($groups['submit']['alignment'], ['left', 'right'])) {
            $errors['submit_alignment'] = 'Submit alignment must be "left" or "right"';
        }

        // Validate GDPR mode
        if (isset($groups['gdpr']['mode']) && !in_array($groups['gdpr']['mode'], ['inform', 'optin'])) {
            $errors['gdpr_mode'] = 'GDPR mode must be "inform" or "optin"';
        }

        // Message and contact (email) are always required
        if (!isset($groups['message']['enabled']) || !$groups['message']['enabled']) {
            $errors['message'] = 'Message field must be enabled';
        }
        if (!isset($groups['contact']['enabled']) || !$groups['contact']['enabled']) {
            $errors['contact'] = 'Contact field must be enabled';
        }

        return empty($errors) ? true : $errors;
    }
}
