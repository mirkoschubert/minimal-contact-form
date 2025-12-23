<?php

class MCF
{

  protected $mcf;
  protected $version;
  protected $loader;


  /**
   * Define the core functionality of the plugin.
   * @since 0.1.0
   */
  public function __construct()
  {
    if (defined('MCF_VERSION')) {
      $this->version = MCF_VERSION;
    } else {
      $this->version = '1.0.0';
    }
    $this->mcf = 'mcf';

    $this->load_dependencies();
    $this->set_locale();
    $this->define_global_hooks();
    $this->define_admin_hooks();
    $this->define_public_hooks();
  }


  /**
   * Load the required dependencies for this plugin.
   * @since 1.0.0
   */
  private function load_dependencies()
  {

    require_once plugin_dir_path(dirname(__FILE__)) . 'core/class-loader.php';
    require_once plugin_dir_path(dirname(__FILE__)) . 'core/class-i18n.php';

    // Modules


    require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-admin.php';
    require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-public.php';
    require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-form.php';

    $this->loader = new mcf_Loader();
  }

  /**
   * Define the locale for this plugin for internationalization.
   * @since 1.0.0
   */
  private function set_locale()
  {

    $plugin_i18n = new mcf_i18n();

    $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
  }


  /**
   * Register all of the hooks which should be loaded globally.
   * @since 1.0.0
   */
  private function define_global_hooks()
  {

/*     add_action('updated_option', function($option_name, $old_value, $value) {
      if ('mcf_options' === $option_name) {
          var_dump($value);
      }
    }, 10, 3); */
  }


  /**
   * Register all of the hooks related to the admin area functionality of the plugin.
   * @since 1.0.0
   */
  private function define_admin_hooks()
  {

    $plugin_admin = new MCF_Admin($this->get_mcf(), $this->get_version());

    $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
    $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
    $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    $this->loader->add_action('admin_menu', $plugin_admin, 'add_admin_menu');
    $this->loader->add_action('rest_api_init', $plugin_admin, 'add_admin_menu');
  }


  /**
   * Register all of the hooks related to the public-facing functionality of the plugin.
   * @since 1.0.0
   */
  private function define_public_hooks()
  {

    $plugin_public = new MCF_Public($this->get_mcf(), $this->get_version());
    $plugin_form = new MCF_Form($this->get_mcf(), $this->get_version());

    $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
    $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

    $this->loader->add_action('rest_api_init', $plugin_form, 'register_routes');
  }


  /**
   * Run the loader to execute all of the hooks with WordPress.
   * @since 1.0.0
   */
  public function run()
  {
    $this->loader->run();
  }


  /**
   * The name of the plugin used to uniquely identify it within the context of
   * WordPress and to define internationalization functionality.
   * @since 1.0.0
   */
  public function get_mcf()
  {
    return $this->mcf;
  }


  /**
   * The reference to the class that orchestrates the hooks with the plugin.
   * @since 1.0.0
   */
  public function get_loader()
  {
    return $this->loader;
  }


  /**
   * Retrieve the version number of the plugin.
   * @since 1.0.0
   */
  public function get_version()
  {
    return $this->version;
  }
}
