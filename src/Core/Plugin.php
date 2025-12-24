<?php

namespace MinimalContactForm\Core;

use MinimalContactForm\Admin\AdminPanel;
use MinimalContactForm\Public\Frontend;
use MinimalContactForm\Public\FormHandler;
use MinimalContactForm\Public\FormRenderer;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since 1.0.0
 */
class Plugin
{
    public const TEXT_DOMAIN = 'mcf';

    protected $mcf;
    protected $version;
    protected $loader;

    /**
     * Define the core functionality of the plugin.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        if (defined('MCF_VERSION')) {
            $this->version = MCF_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->mcf = 'mcf';

        $this->loader = new Loader();
        $this->set_locale();
        $this->define_global_hooks();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * @since 1.0.0
     */
    private function set_locale()
    {
        $plugin_i18n = new I18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks which should be loaded globally.
     *
     * @since 1.0.0
     */
    private function define_global_hooks()
    {
        // WordPress version requirement check
        $requirements = new Requirements(
            'Minimal Contact Form',
            plugin_basename(dirname(__FILE__, 3) . '/mcf.php'),
            'minimal-contact-form'
        );
        $this->loader->add_action('admin_init', $requirements, 'check_wordpress_version');

        // Add settings link to plugin action links
        $this->loader->add_filter(
            'plugin_action_links_' . plugin_basename(dirname(__FILE__, 3) . '/mcf.php'),
            $this,
            'add_plugin_action_links'
        );
    }

    /**
     * Add settings link to plugin action links
     *
     * @since 1.0.0
     * @param array $links Existing plugin action links
     * @return array Modified plugin action links
     */
    public function add_plugin_action_links($links)
    {
        $settings_link = '<a href="' . get_admin_url() . 'options-general.php?page=minimal-contact-form">' . esc_html__('Settings', 'mcf') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     *
     * @since 1.0.0
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new AdminPanel($this->get_mcf(), $this->get_version());

        $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
        $this->loader->add_action('rest_api_init', $plugin_admin, 'register_rest_routes');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_admin_menu');
    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     *
     * @since 1.0.0
     */
    private function define_public_hooks()
    {
        $plugin_public = new Frontend($this->get_mcf(), $this->get_version());
        $plugin_form_handler = new FormHandler();
        $plugin_form_renderer = new FormRenderer($this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_action('init', $plugin_form_handler, 'register');
        $this->loader->add_action('init', $plugin_form_renderer, 'register');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since 1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it.
     *
     * @since 1.0.0
     * @return string The name of the plugin.
     */
    public function get_mcf()
    {
        return $this->mcf;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since 1.0.0
     * @return Loader Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since 1.0.0
     * @return string The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }
}
