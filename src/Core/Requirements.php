<?php

namespace MinimalContactForm\Core;


// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Check plugin requirements (WordPress version, PHP version, etc.)
 *
 * @since 1.0.0
 */
class Requirements
{
    /**
     * Minimum WordPress version required
     */
    const MIN_WP_VERSION = '4.9.6';

    /**
     * Minimum PHP version required
     */
    const MIN_PHP_VERSION = '7.2';

    /**
     * Plugin name for error messages
     */
    private $plugin_name;

    /**
     * Plugin path for activation check
     */
    private $plugin_path;

    /**
     * Plugin slug for settings page check
     */
    private $plugin_slug;

    /**
     * Constructor
     *
     * @param string $plugin_name The plugin name
     * @param string $plugin_path The plugin path
     * @param string $plugin_slug The plugin slug
     */
    public function __construct($plugin_name, $plugin_path, $plugin_slug)
    {
        $this->plugin_name = $plugin_name;
        $this->plugin_path = $plugin_path;
        $this->plugin_slug = $plugin_slug;
    }

    /**
     * Check WordPress version and show error if needed
     *
     * @since 1.0.0
     */
    public function check_wordpress_version()
    {
        global $pagenow;

        $wp_version = get_bloginfo('version');

        if ($pagenow === 'plugins.php') {
            if (is_plugin_active($this->plugin_path) && version_compare($wp_version, self::MIN_WP_VERSION, '<')) {
                add_action('admin_notices', array($this, 'display_version_error'));
            }
        } elseif ($pagenow === 'options-general.php' && isset($_GET['page'])) {
            if ($_GET['page'] === $this->plugin_slug && is_plugin_active($this->plugin_path) && version_compare($wp_version, self::MIN_WP_VERSION, '<')) {
                add_action('admin_notices', array($this, 'display_version_error'));
            }
        }
    }

    /**
     * Display WordPress version error message
     *
     * @since 1.0.0
     */
    public function display_version_error()
    {
        $class = 'notice notice-error';
        $msg  = '<strong>' . $this->plugin_name . '</strong>';
        $msg .= ' ' . esc_html__('requires WordPress', 'mcf') . ' ' . self::MIN_WP_VERSION;
        $msg .= ' ' . esc_html__('or higher!', 'mcf');
        $msg .= ' ' . esc_html__('Please upgrade WordPress and try again.', 'mcf');

        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), $msg);
    }

    /**
     * Check if all requirements are met
     *
     * @since 1.0.0
     * @return bool True if all requirements are met, false otherwise
     */
    public static function check_all_requirements()
    {
        $wp_version = get_bloginfo('version');
        $php_version = phpversion();

        $wp_ok = version_compare($wp_version, self::MIN_WP_VERSION, '>=');
        $php_ok = version_compare($php_version, self::MIN_PHP_VERSION, '>=');

        return $wp_ok && $php_ok;
    }
}
