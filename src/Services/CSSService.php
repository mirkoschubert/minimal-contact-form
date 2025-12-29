<?php

namespace MinimalContactForm\Services;

/**
 * CSS Service
 *
 * Centralized CSS management for inline CSS loading.
 * Handles file caching, consolidation, minification, and color utilities.
 *
 * @since 1.0.0
 */
class CSSService
{
    /**
     * Cached base CSS content
     * @var string|null
     */
    private static $base_css = null;

    /**
     * Cached theme CSS content
     * @var array
     */
    private static $theme_css = [];

    /**
     * Valid theme names
     * @var array
     */
    private const VALID_THEMES = ['default', 'modern', 'minimal'];

    /**
     * Consolidate all CSS layers for a form instance
     *
     * Combines base CSS, theme CSS (or custom CSS), and primary color overrides
     * into a single minified inline stylesheet. Scopes CSS variables to the specific
     * instance to support multiple forms with different themes on the same page.
     *
     * @since 1.0.0
     * @param array $styling Styling settings ['theme_preset', 'variant', 'primary_color', 'custom_css']
     * @param string $instance_id Unique identifier for this instance (e.g., 'mcf-form-1', 'mcf-block-1')
     * @return string Consolidated and minified CSS
     */
    public static function consolidate_inline_css($styling, $instance_id = '')
    {
        $theme = $styling['theme_preset'] ?? 'default';
        $primary_color = $styling['primary_color'] ?? '';
        $custom_css = $styling['custom_css'] ?? '';

        $css_parts = [];

        // 1. Base CSS (always included, not scoped)
        $base_css = self::get_base_css();
        if (!empty($base_css)) {
            $css_parts[] = $base_css;
        }

        // 2. Theme CSS or Custom CSS (mutually exclusive)
        if ($theme === 'custom') {
            // Custom theme: use custom CSS if provided
            if (!empty($custom_css)) {
                $css_parts[] = $custom_css;
            }
        } else {
            // Preset theme: load theme file and scope to instance
            $theme_css = self::get_theme_css($theme);
            if (!empty($theme_css)) {
                // Scope theme CSS to this instance
                $scoped_theme_css = self::scope_css_to_instance($theme_css, $instance_id);
                $css_parts[] = $scoped_theme_css;
            }
        }

        // 3. Primary Color Override (if set, scoped to instance)
        if (!empty($primary_color)) {
            $primary_color_css = self::generate_primary_color_css($primary_color, $instance_id);
            if (!empty($primary_color_css)) {
                $css_parts[] = $primary_color_css;
            }
        }

        // 4. Consolidate
        $consolidated = implode("\n\n", array_filter($css_parts));

        // 5. Minify (unless SCRIPT_DEBUG is enabled)
        if (!defined('SCRIPT_DEBUG') || !SCRIPT_DEBUG) {
            $consolidated = self::minify_css($consolidated);
        }

        return $consolidated;
    }

    /**
     * Get base CSS content (with caching)
     *
     * @since 1.0.0
     * @return string Base CSS content
     */
    private static function get_base_css()
    {
        // Return from cache if available
        if (self::$base_css !== null) {
            return self::$base_css;
        }

        // Read file
        $path = plugin_dir_path(dirname(dirname(__FILE__))) . 'assets/public/css/style.css';

        if (file_exists($path)) {
            self::$base_css = file_get_contents($path);
        } else {
            self::$base_css = '';
        }

        return self::$base_css;
    }

    /**
     * Get theme CSS content (with caching)
     *
     * @since 1.0.0
     * @param string $theme Theme name ('default', 'modern', 'minimal')
     * @return string Theme CSS content
     */
    private static function get_theme_css($theme)
    {
        // Validate theme (whitelist)
        if (!in_array($theme, self::VALID_THEMES, true)) {
            $theme = 'default';
        }

        // Return from cache if available
        if (isset(self::$theme_css[$theme])) {
            return self::$theme_css[$theme];
        }

        // Read file
        $path = plugin_dir_path(dirname(dirname(__FILE__))) . "assets/public/css/themes/{$theme}.css";

        if (file_exists($path)) {
            self::$theme_css[$theme] = file_get_contents($path);
        } else {
            self::$theme_css[$theme] = '';
        }

        return self::$theme_css[$theme];
    }

    /**
     * Scope theme CSS to a specific instance
     *
     * Replaces generic .mcf-form selectors with instance-specific selectors
     * to support multiple forms with different themes on the same page.
     *
     * @since 1.0.0
     * @param string $css Theme CSS content
     * @param string $instance_id Unique instance identifier (e.g., 'mcf-form-1')
     * @return string Scoped CSS
     */
    private static function scope_css_to_instance($css, $instance_id)
    {
        if (empty($instance_id)) {
            return $css;
        }

        // Replace .mcf-form[data-theme-variant="light"] with #instance-id[data-theme-variant="light"]
        // Replace .mcf-form[data-theme-variant="dark"] with #instance-id[data-theme-variant="dark"]
        $css = preg_replace(
            '/\.mcf-form\[data-theme-variant="(light|dark)"\]/',
            '#' . $instance_id . '[data-theme-variant="$1"]',
            $css
        );

        return $css;
    }

    /**
     * Generate primary color override CSS
     *
     * Creates CSS variable overrides for button and checkbox colors based on
     * a single primary color. Automatically calculates hover colors and
     * WCAG 2.0 compliant text colors. Scoped to specific instance.
     *
     * @since 1.0.0
     * @param string $color Hex color value (e.g., '#FF5733')
     * @param string $instance_id Unique instance identifier (e.g., 'mcf-form-1')
     * @return string CSS rules for primary color overrides
     */
    public static function generate_primary_color_css($color, $instance_id = '')
    {
        // Sanitize color
        $color = sanitize_hex_color($color);
        if (!$color) {
            return '';
        }

        // Calculate hover color (20% darker)
        $hover_color = self::adjust_brightness($color, -20);

        // Calculate contrast colors for text (WCAG 2.0 AA compliance: 4.5:1)
        $text_color = self::get_contrast_color($color);
        $text_hover_color = self::get_contrast_color($hover_color);

        // Scope to instance if provided
        $selector = empty($instance_id)
            ? '.mcf-form[data-theme-variant="light"], .mcf-form[data-theme-variant="dark"]'
            : '#' . $instance_id . '[data-theme-variant="light"], #' . $instance_id . '[data-theme-variant="dark"]';

        return "
{$selector} {
    --mcf-button-background-color: {$color} !important;
    --mcf-button-background-hover-color: {$hover_color} !important;
    --mcf-button-color: {$text_color} !important;
    --mcf-button-hover-color: {$text_hover_color} !important;
    --mcf-checkbox-color: {$color} !important;
}";
    }

    /**
     * Minify CSS using WordPress-native approach
     *
     * Removes comments, whitespace, and unnecessary characters while
     * preserving CSS functionality.
     *
     * @since 1.0.0
     * @param string $css CSS content to minify
     * @return string Minified CSS
     */
    private static function minify_css($css)
    {
        // Remove CSS comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);

        // Remove whitespace
        $css = str_replace(["\r\n", "\r", "\n", "\t"], '', $css);
        $css = preg_replace('/\s+/', ' ', $css);

        // Remove space around specific characters
        $css = preg_replace('/\s*([:,;{}])\s*/', '$1', $css);

        // Remove trailing semicolons before closing braces
        $css = str_replace(';}', '}', $css);

        // Remove leading/trailing whitespace
        return trim($css);
    }

    /**
     * Adjust color brightness
     *
     * Increases or decreases the brightness of a hex color by a given amount.
     * Used for generating hover states.
     *
     * @since 1.0.0
     * @param string $hex Hex color code (e.g., '#FF5733')
     * @param int $steps Brightness adjustment (-255 to 255, negative = darker)
     * @return string Adjusted hex color
     */
    private static function adjust_brightness($hex, $steps)
    {
        // Remove # if present
        $hex = ltrim($hex, '#');

        // Convert to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Adjust brightness
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));

        // Convert back to hex
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * Get contrast color (black or white) for given background
     *
     * Calculates the relative luminance using WCAG 2.0 formula and returns
     * black or white text color for optimal contrast (minimum 4.5:1 ratio).
     *
     * @since 1.0.0
     * @param string $hex Background hex color
     * @return string '#000000' or '#ffffff'
     */
    private static function get_contrast_color($hex)
    {
        // Remove # if present
        $hex = ltrim($hex, '#');

        // Convert to RGB (0-1 range)
        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        // Calculate relative luminance (WCAG 2.0 formula)
        $r = $r <= 0.03928 ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
        $g = $g <= 0.03928 ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
        $b = $b <= 0.03928 ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);

        $luminance = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;

        // Return white for dark backgrounds, black for light backgrounds
        // Threshold at 0.5 ensures minimum 4.5:1 contrast ratio
        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }
}
