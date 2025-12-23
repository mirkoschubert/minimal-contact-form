<?php

namespace MinimalContactForm\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * Handles React admin interface integration.
 *
 * @since 1.0.0
 */
class AdminPanel
{
    /**
     * The ID of this plugin.
     *
     * @var string
     */
    private $mcf;

    /**
     * The version of this plugin.
     *
     * @var string
     */
    private $version;

    /**
     * REST API handler
     *
     * @var RestAPI
     */
    private $rest_api;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $mcf The name of this plugin.
     * @param string $version The version of this plugin.
     */
    public function __construct($mcf, $version)
    {
        $this->mcf = $mcf;
        $this->version = $version;
        $this->rest_api = new RestAPI();
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since 1.0.0
     */
    public function enqueue_styles()
    {
        $screen = get_current_screen();
        if ($screen && $screen->id !== 'settings_page_minimal-contact-form') {
            return;
        }

        // Enqueue built React CSS
        $css_file = plugin_dir_path(dirname(__FILE__, 2)) . 'assets/admin/css/admin.css';
        if (file_exists($css_file)) {
            wp_enqueue_style(
                'mcf-admin',
                plugin_dir_url(dirname(__FILE__, 2)) . 'assets/admin/css/admin.css',
                [],
                filemtime($css_file)
            );
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts()
    {
        $screen = get_current_screen();
        if ($screen && $screen->id !== 'settings_page_minimal-contact-form') {
            return;
        }

        // Check if built React bundle exists
        $js_file = plugin_dir_path(dirname(__FILE__, 2)) . 'assets/admin/js/admin.js';
        $asset_file = plugin_dir_path(dirname(__FILE__, 2)) . 'assets/admin/js/admin.asset.php';

        if (!file_exists($js_file)) {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-error"><p>';
                echo '<strong>Minimal Contact Form:</strong> ';
                echo __('React admin bundle not found. Please run <code>npm run build</code> in the admin-app directory.', 'mcf');
                echo '</p></div>';
            });
            return;
        }

        // Load asset file for dependencies and version
        $asset = file_exists($asset_file) ? include $asset_file : ['dependencies' => [], 'version' => $this->version];

        // Enqueue React bundle
        wp_enqueue_script(
            'mcf-admin',
            plugin_dir_url(dirname(__FILE__, 2)) . 'assets/admin/js/admin.js',
            $asset['dependencies'],
            $asset['version'],
            true
        );

        // Localize script with WordPress data
        wp_localize_script('mcf-admin', 'mcfAdmin', [
            'apiUrl' => rest_url('mcf/v1'),
            'nonce' => wp_create_nonce('wp_rest'),
            'pluginUrl' => plugin_dir_url(dirname(__FILE__, 2)),
        ]);

        // Set REST API nonce
        wp_set_script_translations('mcf-admin', 'mcf');
    }

    /**
     * Register settings (for legacy compatibility)
     *
     * @since 1.0.0
     */
    public function register_settings()
    {
        // REST API handles settings now, but keep this for legacy hooks
        register_setting('mcf_options_group', 'mcf_options');
    }

    /**
     * Register REST API routes
     *
     * @since 1.0.0
     */
    public function register_rest_routes()
    {
        $this->rest_api->register_routes();
    }

    /**
     * Add admin menu page
     *
     * @since 1.0.0
     */
    public function add_admin_menu()
    {
        $plugin_hook = add_options_page(
            __('Minimal Contact Form', 'mcf'),
            __('Contact Form', 'mcf'),
            'manage_options',
            'minimal-contact-form',
            [$this, 'settings_page']
        );

        if ($plugin_hook) {
            add_action('load-' . $plugin_hook, [$this, 'add_help']);
        }
    }

    /**
     * Render settings page
     *
     * @since 1.0.0
     */
    public function settings_page()
    {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Minimal Contact Form', 'mcf'); ?></h1>
            <p><?php echo esc_html__('Configure your contact form with the modern React interface below.', 'mcf'); ?></p>

            <!-- React root -->
            <div id="mcf-admin-root"></div>

            <!-- Fallback if React doesn't load -->
            <noscript>
                <div class="notice notice-error">
                    <p><?php echo esc_html__('Please enable JavaScript to use the Minimal Contact Form settings.', 'mcf'); ?></p>
                </div>
            </noscript>
        </div>
        <?php
    }

    /**
     * Add contextual help
     *
     * @since 1.0.0
     */
    public function add_help()
    {
        $current_screen = get_current_screen();

        $about = '<p><strong>' . __('Minimal Contact Form', 'mcf') . '</strong> '
            . __('is a simple, clean and secure contact form.', 'mcf') . '</p><p>'
            . __('This plugin was developed with usability in mind and uses data that already exists. It provides security features to prevent the receipt of spam without passing on data to third parties. In addition, it automatically inserts a corresponding notice to comply with the requirements of the GDPR.', 'mcf') . '</p>';

        $settings = '<h4>' . __('About the settings', 'mcf') . '</h4><ul><li>'
            . __('If you refer to Art. 6 (1) let. b or let. f GDPR in your privacy policy, you do not need an opt-in. Only if you reference Art. 6 (1) let. a GDPR should you tick the relevant checkbox.', 'mcf') . '</li><li>'
            . __("The WordPress PHPmailer (SMTP) should be used by default. If you encounter an error, please turn on the PHP mail function. However, the emails will end up in the recipient's spam folder more likely.", 'mcf') . '</li><li>'
            . __('To display the form on any WP Post or Page, simply add the shortcode:', 'mcf') . ' <code>[minimal_contact_form]</code>.</li></ul>';

        $current_screen->add_help_tab([
            'id' => 'mcf-about-help-tab',
            'title' => __('About', 'mcf'),
            'content' => $about,
        ]);

        $current_screen->add_help_tab([
            'id' => 'mcf-settings-help-tab',
            'title' => __('Settings', 'mcf'),
            'content' => $settings,
        ]);
    }
}
