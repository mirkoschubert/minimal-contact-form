<?php

namespace MinimalContactForm\Admin;

/**
 * Theme Presets for styling
 *
 * Defines predefined color schemes and styling options to simplify
 * the admin UI and provide good-looking defaults.
 *
 * @since 1.0.0
 */
class ThemePresets
{
    /**
     * Get all available theme presets
     *
     * @since 1.0.0
     * @return array Array of theme presets with their styling values
     */
    public static function get_presets()
    {
        return [
            'light' => self::get_light_theme(),
            'dark' => self::get_dark_theme(),
            'modern' => self::get_modern_theme(),
            'minimal' => self::get_minimal_theme(),
        ];
    }

    /**
     * Get a specific preset by name
     *
     * @since 1.0.0
     * @param string $name Preset name
     * @return array|null Preset data or null if not found
     */
    public static function get_preset($name)
    {
        $presets = self::get_presets();
        return $presets[$name] ?? null;
    }

    /**
     * Light Theme (Default)
     * Clean, professional look with dark text on light background
     *
     * @since 1.0.0
     * @return array Theme configuration
     */
    private static function get_light_theme()
    {
        return [
            'name' => 'Light',
            'description' => 'Clean and professional with dark text on light background',
            'colors' => [
                'item-text-color' => '#333333',
                'item-placeholder-color' => '#999999',
                'item-background-color' => '#ffffff',
                'item-background-color-focus' => '#f9f9f9',
                'item-border-color' => '#dddddd',
                'item-border-color-focus' => '#999999',
                'item-border-width' => 1,
                'item-border-bottom' => 0,
                'item-border-radius' => 0.25,
                'item-padding' => 0.75,
                'item-spacing' => 0.75,
                'item-font-size' => 1,
                'item-single-column' => 0,
                'item-labels' => 0,
                'button-text-color' => '#ffffff',
                'button-text-color-hover' => '#ffffff',
                'button-background-color' => '#222222',
                'button-background-color-hover' => '#555555',
                'button-border-color' => '#222222',
                'button-border-color-hover' => '#555555',
                'button-border-width' => 0,
                'button-border-radius' => 0.25,
                'button-border-none' => 1,
                'button-padding' => 0.75,
                'button-font-size' => 1,
                'button-alignment' => 0,
                'misc-notice-color' => '#333333',
                'misc-success-color' => '#46b450',
                'misc-warning-color' => '#ffb900',
                'misc-error-color' => '#dc3232',
                'misc-custom-css' => '',
            ],
        ];
    }

    /**
     * Dark Theme
     * Modern dark theme with light text on dark background
     *
     * @since 1.0.0
     * @return array Theme configuration
     */
    private static function get_dark_theme()
    {
        return [
            'name' => 'Dark',
            'description' => 'Modern dark theme with light text',
            'colors' => [
                'item-text-color' => '#e0e0e0',
                'item-placeholder-color' => '#999999',
                'item-background-color' => '#2a2a2a',
                'item-background-color-focus' => '#333333',
                'item-border-color' => '#444444',
                'item-border-color-focus' => '#666666',
                'item-border-width' => 1,
                'item-border-bottom' => 0,
                'item-border-radius' => 0.25,
                'item-padding' => 0.75,
                'item-spacing' => 0.75,
                'item-font-size' => 1,
                'item-single-column' => 0,
                'item-labels' => 0,
                'button-text-color' => '#222222',
                'button-text-color-hover' => '#333333',
                'button-background-color' => '#e0e0e0',
                'button-background-color-hover' => '#ffffff',
                'button-border-color' => '#e0e0e0',
                'button-border-color-hover' => '#ffffff',
                'button-border-width' => 0,
                'button-border-radius' => 0.25,
                'button-border-none' => 1,
                'button-padding' => 0.75,
                'button-font-size' => 1,
                'button-alignment' => 0,
                'misc-notice-color' => '#e0e0e0',
                'misc-success-color' => '#46b450',
                'misc-warning-color' => '#ffb900',
                'misc-error-color' => '#dc3232',
                'misc-custom-css' => '',
            ],
        ];
    }

    /**
     * Modern Theme
     * Contemporary design with accent colors
     *
     * @since 1.0.0
     * @return array Theme configuration
     */
    private static function get_modern_theme()
    {
        return [
            'name' => 'Modern',
            'description' => 'Contemporary design with vibrant accent colors',
            'colors' => [
                'item-text-color' => '#2c3e50',
                'item-placeholder-color' => '#95a5a6',
                'item-background-color' => '#ffffff',
                'item-background-color-focus' => '#f8f9fa',
                'item-border-color' => '#e3e8ed',
                'item-border-color-focus' => '#3498db',
                'item-border-width' => 2,
                'item-border-bottom' => 0,
                'item-border-radius' => 0.5,
                'item-padding' => 0.875,
                'item-spacing' => 1,
                'item-font-size' => 1,
                'item-single-column' => 0,
                'item-labels' => 0,
                'button-text-color' => '#ffffff',
                'button-text-color-hover' => '#ffffff',
                'button-background-color' => '#3498db',
                'button-background-color-hover' => '#2980b9',
                'button-border-color' => '#3498db',
                'button-border-color-hover' => '#2980b9',
                'button-border-width' => 0,
                'button-border-radius' => 0.5,
                'button-border-none' => 1,
                'button-padding' => 0.875,
                'button-font-size' => 1.05,
                'button-alignment' => 0,
                'misc-notice-color' => '#2c3e50',
                'misc-success-color' => '#27ae60',
                'misc-warning-color' => '#f39c12',
                'misc-error-color' => '#e74c3c',
                'misc-custom-css' => '',
            ],
        ];
    }

    /**
     * Minimal Theme
     * Ultra-clean design with minimal styling
     *
     * @since 1.0.0
     * @return array Theme configuration
     */
    private static function get_minimal_theme()
    {
        return [
            'name' => 'Minimal',
            'description' => 'Ultra-clean design focusing on content',
            'colors' => [
                'item-text-color' => '#000000',
                'item-placeholder-color' => '#aaaaaa',
                'item-background-color' => '#ffffff',
                'item-background-color-focus' => '#ffffff',
                'item-border-color' => '#000000',
                'item-border-color-focus' => '#000000',
                'item-border-width' => 1,
                'item-border-bottom' => 1,
                'item-border-radius' => 0,
                'item-padding' => 0.5,
                'item-spacing' => 1,
                'item-font-size' => 1,
                'item-single-column' => 1,
                'item-labels' => 0,
                'button-text-color' => '#ffffff',
                'button-text-color-hover' => '#000000',
                'button-background-color' => '#000000',
                'button-background-color-hover' => '#ffffff',
                'button-border-color' => '#000000',
                'button-border-color-hover' => '#000000',
                'button-border-width' => 1,
                'button-border-radius' => 0,
                'button-border-none' => 0,
                'button-padding' => 0.5,
                'button-font-size' => 1,
                'button-alignment' => 0,
                'misc-notice-color' => '#000000',
                'misc-success-color' => '#008000',
                'misc-warning-color' => '#ff8c00',
                'misc-error-color' => '#ff0000',
                'misc-custom-css' => '',
            ],
        ];
    }

    /**
     * Apply a preset theme to styling options
     *
     * @since 1.0.0
     * @param string $preset_name Name of the preset to apply
     * @return array Styling array ready to save
     */
    public static function apply_preset($preset_name)
    {
        $preset = self::get_preset($preset_name);

        if (!$preset) {
            // Fallback to light theme
            $preset = self::get_light_theme();
            $preset_name = 'light';
        }

        return [
            'theme_preset' => $preset_name,
            'advanced' => $preset['colors'],
        ];
    }
}
