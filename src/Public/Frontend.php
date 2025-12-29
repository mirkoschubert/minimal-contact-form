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
     * CSS is now loaded inline on a per-instance basis by FormRenderer and ContactFormBlock.
     * This method is kept for backward compatibility with any hooks/filters that may reference it.
     *
     * @since 1.0.0
     * @deprecated 1.0.0 CSS is now handled inline per instance
     */
    public function enqueue_styles()
    {
        // CSS is now injected inline per form instance
        // See: FormRenderer::enqueue_instance_css() and ContactFormBlock::enqueue_block_inline_css()
        // This method is kept for backward compatibility only
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
