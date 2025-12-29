<?php

namespace MinimalContactForm\Public;

/**
 * The public-facing functionality of the plugin.
 *
 * Handles enqueuing styles and scripts for the frontend.
 * Implements theme-based CSS loading with variant support.
 *
 * @since 1.0.0
 */
class Frontend
{
    /**
     * Plugin identifier
     *
     * @var string
     */
    private $mcf;

    /**
     * Plugin version
     *
     * @var string
     */
    private $version;

    /**
     * Plugin options
     *
     * @var array
     */
    private $options;

    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     * @param string $mcf Plugin identifier
     * @param string $version Plugin version
     */
    public function __construct($mcf, $version)
    {
        $this->mcf = $mcf;
        $this->version = $version;
        $this->options = get_option('mcf_options', []);
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * Enqueues in this order:
     * 1. Base structure CSS (style.css) - only if not using blocks
     * 2. Theme CSS (modern.css or minimal.css with both variants)
     * 3. Primary color override (inline)
     * 4. Custom CSS (inline)
     *
     * Note: Blocks handle their own CSS via block.json
     *
     * @since 1.0.0
     */
    public function enqueue_styles()
    {
        // Skip if using blocks (blocks handle their own CSS via block.json)
        if (has_block('mcf/contact-form')) {
            return;
        }

        $styling = $this->options['styling'] ?? [];
        $theme_preset = $styling['theme_preset'] ?? 'default';
        $primary_color = $styling['primary_color'] ?? '';
        $custom_css = $styling['custom_css'] ?? '';

        // 1. Base structure CSS
        $base_css_path = plugin_dir_path(dirname(__FILE__, 2)) . 'assets/public/css/style.css';
        if (file_exists($base_css_path)) {
            wp_enqueue_style(
                $this->mcf . '-base',
                plugin_dir_url(dirname(__FILE__, 2)) . 'assets/public/css/style.css',
                [],
                filemtime($base_css_path),
                'all'
            );
        }

        // 2. Theme CSS (skip for 'custom' theme)
        if ($theme_preset !== 'custom') {
            $theme_css_path = plugin_dir_path(dirname(__FILE__, 2)) . 'assets/public/css/themes/' . $theme_preset . '.css';
            if (file_exists($theme_css_path)) {
                wp_enqueue_style(
                    $this->mcf . '-theme',
                    plugin_dir_url(dirname(__FILE__, 2)) . 'assets/public/css/themes/' . $theme_preset . '.css',
                    [$this->mcf . '-base'],
                    filemtime($theme_css_path),
                    'all'
                );
            }
        }

        // 3. Custom CSS with its own handle (for 'custom' theme only)
        if ($theme_preset === 'custom' && !empty($custom_css)) {
            wp_register_style(
                $this->mcf . '-custom',
                false, // No file, inline only
                [$this->mcf . '-base'],
                $this->version,
                'all'
            );
            wp_enqueue_style($this->mcf . '-custom');
            wp_add_inline_style($this->mcf . '-custom', $custom_css);
        }

        // 4. Primary color override
        if (!empty($primary_color)) {
            $primary_color_css = $this->generate_primary_color_css($primary_color);

            // Attach to appropriate handle
            if ($theme_preset === 'custom' && !empty($custom_css)) {
                // For custom theme with CSS: attach to mcf-custom handle
                wp_add_inline_style($this->mcf . '-custom', $primary_color_css);
            } elseif ($theme_preset !== 'custom') {
                // For other themes: attach to mcf-theme handle
                wp_add_inline_style($this->mcf . '-theme', $primary_color_css);
            }
            // Note: If custom theme without Custom CSS, Primary Color won't load (acceptable)
        }

        // 5. Custom CSS for non-custom themes (inline addition to theme)
        if ($theme_preset !== 'custom' && !empty($custom_css)) {
            wp_add_inline_style($this->mcf . '-theme', $custom_css);
        }
    }

    /**
     * Generate CSS for primary color override
     *
     * @since 1.0.0
     * @param string $color Hex color code
     * @return string CSS code
     */
    private function generate_primary_color_css($color)
    {
        $color = sanitize_hex_color($color);
        if (!$color) {
            return '';
        }

        // Generate slightly darker color for hover state
        $hover_color = $this->adjust_brightness($color, -20);

        // Calculate text color based on contrast (WCAG 2.0 AA compliance: 4.5:1)
        $text_color = $this->get_contrast_color($color);
        $text_hover_color = $this->get_contrast_color($hover_color);

        return "
.mcf-form[data-theme-variant=\"light\"],
.mcf-form[data-theme-variant=\"dark\"] {
    --mcf-button-background-color: {$color} !important;
    --mcf-button-background-hover-color: {$hover_color} !important;
    --mcf-button-color: {$text_color} !important;
    --mcf-button-hover-color: {$text_hover_color} !important;
    --mcf-checkbox-color: {$color} !important;
}
";
    }

    /**
     * Adjust color brightness
     *
     * @since 1.0.0
     * @param string $hex Hex color code
     * @param int $steps Brightness adjustment (-255 to 255)
     * @return string Adjusted hex color
     */
    private function adjust_brightness($hex, $steps)
    {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));

        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT)
            . str_pad(dechex($g), 2, '0', STR_PAD_LEFT)
            . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate contrast color (black or white) based on background
     * Uses WCAG 2.0 formula for relative luminance
     *
     * @since 1.0.0
     * @param string $hex Hex color code
     * @return string '#ffffff' or '#000000'
     */
    private function get_contrast_color($hex)
    {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Calculate relative luminance (WCAG 2.0)
        $r = $r / 255;
        $g = $g / 255;
        $b = $b / 255;

        $r = ($r <= 0.03928) ? $r / 12.92 : pow(($r + 0.055) / 1.055, 2.4);
        $g = ($g <= 0.03928) ? $g / 12.92 : pow(($g + 0.055) / 1.055, 2.4);
        $b = ($b <= 0.03928) ? $b / 12.92 : pow(($b + 0.055) / 1.055, 2.4);

        $luminance = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;

        // Return white for dark backgrounds, black for light backgrounds
        // Threshold at 0.5 ensures minimum 4.5:1 contrast ratio
        return ($luminance > 0.5) ? '#000000' : '#ffffff';
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts()
    {
        $js_path = plugin_dir_path(dirname(__FILE__, 2)) . 'public/assets/js/mcf-public.js';
        if (file_exists($js_path)) {
            wp_enqueue_script(
                $this->mcf,
                plugin_dir_url(dirname(__FILE__, 2)) . 'public/assets/js/mcf-public.js',
                ['jquery'],
                $this->version,
                false
            );

            $script_data = [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('mcf_submit')
            ];
            wp_localize_script($this->mcf, 'mcfData', $script_data);
        }
    }
}
