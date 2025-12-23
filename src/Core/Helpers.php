<?php

namespace MinimalContactForm\Core;

/**
 * Helper functions for the plugin.
 *
 * @since 1.0.0
 */
class Helpers
{
    /**
     * Gets a theme option by a specific ID.
     *
     * @since 1.0.0
     * @param string $category The category of the option (e.g., 'settings', 'fields', 'styling').
     * @param string $id The ID of the option.
     * @return mixed|null The option value or null if not found.
     */
    public static function get_option($category, $id)
    {
        $options = get_option('mcf_options');
        if (isset($options[$category]) && isset($options[$category][$id])) {
            return $options[$category][$id];
        }
        return null;
    }

    /**
     * Sanitize checkbox value.
     *
     * @since 1.0.0
     * @param mixed $checked The checkbox value.
     * @param mixed $default The default value.
     * @return int Returns 1 if checked, 0 otherwise.
     */
    public static function sanitize_checkbox($checked, $default)
    {
        if (isset($checked)) {
            return true == $checked ? 1 : 0;
        } else {
            return $default;
        }
    }

    /**
     * Sanitize color value.
     *
     * @since 1.0.0
     * @param string $hex The hex color value.
     * @param string $default The default color value.
     * @return string The sanitized hex color.
     */
    public static function sanitize_color($hex, $default)
    {
        return isset($hex) ? sanitize_hex_color($hex) : $default;
    }

    /**
     * Sanitize integer value.
     *
     * @since 1.0.0
     * @param mixed $int The integer value.
     * @param int $default The default value.
     * @return int The sanitized integer.
     */
    public static function sanitize_int($int, $default)
    {
        return isset($int) ? intval($int) : $default;
    }

    /**
     * Sanitize float value.
     *
     * @since 1.0.0
     * @param mixed $float The float value.
     * @param float $default The default value.
     * @return float The sanitized float.
     */
    public static function sanitize_float($float, $default)
    {
        return isset($float) ? floatval($float) : $default;
    }

    /**
     * Sanitize textarea content.
     *
     * @since 1.0.0
     * @param string $content The textarea content.
     * @param string $default The default value.
     * @return string The sanitized content.
     */
    public static function sanitize_textarea($content, $default)
    {
        return isset($content) ? sanitize_textarea_field($content) : $default;
    }
}
