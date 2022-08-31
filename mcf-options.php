<?php

// Disable direct access
if (!defined( 'ABSPATH' )) exit();


/**
 * Initialize the options menu page
 * @since 0.2.0
 */
function mcf_options_page() {
  global $mcf_plugin, $mcf_slug, $plugin_hook;

  $plugin_hook = add_options_page(
    $mcf_plugin,
    __( 'Contact Form', 'mcf' ),
    'manage_options',
    $mcf_slug,
    'mcf_render_options_page'
  );

  if ($plugin_hook) {
    add_action( 'load-' . $plugin_hook, 'mcf_add_contextual_help');
  }

}
add_action( 'admin_menu', 'mcf_options_page' );


/**
 * Initialize the settings
 * @since 0.2.0
 */
function mcf_options_init() {
  register_setting('mcf_plugin_options', 'mcf_options', array('type' => 'string', 'sanitize_callback' => 'mcf_validate_options'));
}
add_action( 'admin_init', 'mcf_options_init' );


/**
 * Add the contextual help to the settings
 * @since 0.9.0
 */
function mcf_add_contextual_help() {
  global $mcf_plugin;

  $current_screen = get_current_screen();

  $about = '<p><strong>' . $mcf_plugin . '</strong> ' . esc_html('is a simple, clean and secure contact form.', 'mcf') . '</p><p>' . esc_html('This plugin was developed with usability in mind and uses data that already exists. It provides security features to prevent the receipt of spam without passing on data to third parties. In addition, it automatically inserts a corresponding notice to comply with the requirements of the GDPR.', 'mcf') . '</p>';

  $settings = '<h4>' . esc_html('About the settings', 'mcf') . '</h4><ul><li>' . esc_html('If you refer to Art. 6 (1) let. b or let. f GDPR in your privacy policy, you do not need an opt-in. Only if you reference Art. 6 (1) let. a GDPR should you tick the relevant checkbox.', 'mcf') . '</li><li>' . esc_html("The WordPress PHPmailer (SMTP) should be used by default. If you encounter an error, please turn on the PHP mail function. However, the emails will end up in the recipient's spam folder more likely.", 'mcf') . '</li><li>' . esc_html('To display the form on any WP Post or Page, simply add the shortcode:', 'mcf') . ' <code>[minimal_contact_form]</code>.</li></ul>';

  $current_screen->add_help_tab(array(
    'id' => 'mcf-about-help-tab',
    'title' => __('About'),
    'content' => $about
  ));
  $current_screen->add_help_tab(array(
    'id' => 'mcf-settings-help-tab',
    'title' => __('Settings'),
    'content' => $settings
  ));
}

/**
 * Validates Options
 * @since 0.3.0
 */
function mcf_validate_options($input) {

  $validated = [];

  // user
  $validated['user'] = (isset($input['user'])) ? (int)$input['user'] : 1;
  // phone
  if (!isset($input['phone'])) $input['phone'] = 0;
  $validated['phone'] = ($input['phone'] == 1) ? 1 : 0;
  // gdpr
  if (!isset($input['gdpr'])) $input['gdpr'] = 0;
  $validated['gdpr'] = ($input['gdpr'] == 1) ? 1 : 0;
  // spam
  if (!isset($input['spam'])) $input['spam'] = 0;
  $validated['spam'] = ($input['spam'] == 1) ? 1 : 0;
  // phpmail
  if (!isset($input['phpmail'])) $input['phpmail'] = 0;
  $validated['phpmail'] = ($input['phpmail'] == 1) ? 1 : 0;

  return $validated;
}


/**
 * Display the options page
 * @since 0.2.0
 */
function mcf_render_options_page() {

  global $mcf_version, $mcf_plugin, $mcf_options;

  include_once('mcf-options-page.php');
}


