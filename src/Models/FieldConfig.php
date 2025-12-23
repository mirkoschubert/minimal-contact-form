<?php

namespace MinimalContactForm\Models;

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
     * @since 1.0.0
     * @return array Default field configuration
     */
    public static function get_defaults()
    {
        return [
            'order' => ['first-name', 'last-name', 'email', 'subject', 'message'],
            'enabled' => [
                'company' => false,
                'first-name' => true,
                'last-name' => true,
                'name' => false,
                'phone' => false,
                'email' => true,
                'subject' => true,
                'message' => true,
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
     * Get field order
     *
     * @since 1.0.0
     * @return array Field order
     */
    public function get_order()
    {
        return $this->data['order'];
    }

    /**
     * Set field order
     *
     * @since 1.0.0
     * @param array $order New field order
     */
    public function set_order($order)
    {
        $this->data['order'] = array_values($order);
    }

    /**
     * Check if a field is enabled
     *
     * @since 1.0.0
     * @param string $field_id Field ID
     * @return bool True if enabled
     */
    public function is_enabled($field_id)
    {
        return $this->data['enabled'][$field_id] ?? false;
    }

    /**
     * Enable/disable a field
     *
     * @since 1.0.0
     * @param string $field_id Field ID
     * @param bool $enabled Enable or disable
     */
    public function set_enabled($field_id, $enabled)
    {
        $this->data['enabled'][$field_id] = (bool) $enabled;
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
     * Get custom fields
     *
     * @since 1.0.0
     * @return array Custom fields
     */
    public function get_custom_fields()
    {
        return $this->data['custom_fields'];
    }

    /**
     * Add a custom field
     *
     * @since 1.0.0
     * @param array $field Custom field data
     */
    public function add_custom_field($field)
    {
        $this->data['custom_fields'][] = $field;
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

        // Validate order contains valid field IDs
        foreach ($this->data['order'] as $field_id) {
            if (!in_array($field_id, self::$available_fields)) {
                $errors['order'][] = "Invalid field ID in order: {$field_id}";
            }
        }

        // Validate email and message are always enabled (required)
        if (!$this->is_enabled('email')) {
            $errors['enabled'] = 'Email field must be enabled';
        }
        if (!$this->is_enabled('message')) {
            $errors['enabled'] = 'Message field must be enabled';
        }

        return empty($errors) ? true : $errors;
    }
}
