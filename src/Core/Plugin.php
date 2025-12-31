<?php

namespace MinimalContactForm\Core;

// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}

use MinimalContactForm\Admin\AdminPanel;
use MinimalContactForm\Blocks\ContactFormBlock;
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

    /**
     * Activation hook.
     *
     * Writes default options to database and runs migration if needed.
     *
     * @since 1.0.0
     */
    public static function activate()
    {
        // Lazy-Load Migration mit class_exists Check
        if (!class_exists('MinimalContactForm\Core\Migration')) {
            require_once plugin_dir_path(__FILE__) . 'Migration.php';
        }
        Migration::maybe_migrate();

        // Frische Installation: Default-Optionen erstellen
        $options = get_option('mcf_options');
        if (false === $options) {
            add_option(
                'mcf_options',
                Defaults::get_options(),
                '',
                true
            );
        }
    }

    /**
     * Deactivation hook.
     *
     * Intentionally empty - we don't delete options on deactivation.
     * Options are only deleted on uninstall.
     *
     * @since 1.0.0
     */
    public static function deactivate()
    {
        // Intentionally empty - preserve settings when plugin is deactivated
    }

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
        add_action('plugins_loaded', function() {
            load_plugin_textdomain(
                'mcf',
                false,
                dirname(plugin_basename(dirname(dirname(__FILE__)))) . '/languages'
            );
        });
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
        add_action('admin_init', [$requirements, 'check_wordpress_version']);

        // Add settings link to plugin action links
        add_filter(
            'plugin_action_links_' . plugin_basename(dirname(__FILE__, 3) . '/mcf.php'),
            [$this, 'add_plugin_action_links']
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

        add_action('admin_init', [$plugin_admin, 'register_settings']);
        add_action('rest_api_init', [$plugin_admin, 'register_rest_routes']);
        add_action('admin_enqueue_scripts', [$plugin_admin, 'enqueue_styles']);
        add_action('admin_enqueue_scripts', [$plugin_admin, 'enqueue_scripts']);
        add_action('admin_menu', [$plugin_admin, 'add_admin_menu']);
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
        $plugin_block = new ContactFormBlock($this->get_version());

        add_action('wp_enqueue_scripts', [$plugin_public, 'enqueue_styles']);
        add_action('wp_enqueue_scripts', [$plugin_public, 'enqueue_scripts']);
        add_action('init', [$plugin_form_handler, 'register']);
        add_action('init', [$plugin_form_renderer, 'register']);
        add_action('init', [$plugin_block, 'register']);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * Kept for backward compatibility but no longer needed as hooks
     * are registered directly in the constructor.
     *
     * @since 1.0.0
     */
    public function run()
    {
        // Empty - hooks are now registered directly in constructor
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
