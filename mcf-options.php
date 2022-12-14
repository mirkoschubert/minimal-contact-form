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

  $about = '<p><strong>' . $mcf_plugin . '</strong> ' . __('is a simple, clean and secure contact form.', 'mcf') . '</p><p>' . __('This plugin was developed with usability in mind and uses data that already exists. It provides security features to prevent the receipt of spam without passing on data to third parties. In addition, it automatically inserts a corresponding notice to comply with the requirements of the GDPR.', 'mcf') . '</p>';

  $settings = '<h4>' . __('About the settings', 'mcf') . '</h4><ul><li>' . __('If you refer to Art. 6 (1) let. b or let. f GDPR in your privacy policy, you do not need an opt-in. Only if you reference Art. 6 (1) let. a GDPR should you tick the relevant checkbox.', 'mcf') . '</li><li>' . __("The WordPress PHPmailer (SMTP) should be used by default. If you encounter an error, please turn on the PHP mail function. However, the emails will end up in the recipient's spam folder more likely.", 'mcf') . '</li><li>' . __('To display the form on any WP Post or Page, simply add the shortcode:', 'mcf') . ' <code>[minimal_contact_form]</code>.</li></ul>';

  $variables = '<h4>' . __('CSS Variables', 'mcf') . '</h4><p>' . __('Instead of writing pure CSS you can use predefined CSS variables to style your form. Please choose some of the following variables:', 'mcf') . '</p><table class="variables"><thead><tr><th>' . __('Variable', 'mcf') . '</th><th>' .__('Default', 'mcf') .'</th></tr></thead><tbody>';

  $variables .= '<tr class="headline"><td colspan="2">' . __('General', 'mcf') . '</td></tr>';
  $variables .= '<tr><td><code>--mcf-text-color</code></td><td>#ccc</td></tr>';
  $variables .= '<tr><td><code>--mcf-placeholder-color</code></td><td>#999</td></tr>';
  $variables .= '<tr><td><code>--mcf-border-color</code></td><td>#bbb</td></tr>';
  $variables .= '<tr><td><code>--mcf-border-radius</code></td><td>0.25rem</td></tr>';
  $variables .= '<tr><td><code>--mcf-checkbox-color</code></td><td>#dc3232</td></tr>';
  $variables .= '<tr><td><code>--mcf-success-color</code></td><td>#46b450</td></tr>';
  $variables .= '<tr><td><code>--mcf-warning-color</code></td><td>#ffb900</td></tr>';
  $variables .= '<tr><td><code>--mcf-error-color</code></td><td>#dc3232</td></tr>';
  $variables .= '<tr><td><code>--mcf-gdpr-font-size</code></td><td>1rem</td></tr>';
  $variables .= '<tr><td><code>--mcf-desktop-max-width</code></td><td>36em</td></tr>';
  $variables .= '<tr class="headline"><td colspan="2">' . __('Item', 'mcf') . '</td></tr>';
  $variables .= '<tr><td><code>--mcf-item-background-color</code></td><td>#fff</td></tr>';
  $variables .= '<tr><td><code>--mcf-item-padding</code></td><td>1rem</td></tr>';
  $variables .= '<tr><td><code>--mcf-item-spacing</code></td><td>1.5rem</td></tr>';
  $variables .= '<tr class="headline"><td colspan="2">' . __('Label', 'mcf') . '</td></tr>';
  $variables .= '<tr><td><code>--mcf-label-font-size</code></td><td>inherit</td></tr>';
  $variables .= '<tr><td><code>--mcf-label-font-weight</code></td><td>inherit</td></tr>';
  $variables .= '<tr class="headline"><td colspan="2">' . __('Button', 'mcf') . '</td></tr>';
  $variables .= '<tr><td><code>--mcf-button-color</code></td><td>#fff</td></tr>';
  $variables .= '<tr><td><code>--mcf-button-hover-color</code></td><td>#fff</td></tr>';
  $variables .= '<tr><td><code>--mcf-button-background-color</code></td><td>#222</td></tr>';
  $variables .= '<tr><td><code>--mcf-button-background-hover-color</code></td><td>#555</td></tr>';
  $variables .= '<tr><td><code>--mcf-button-padding</code></td><td>1rem 2rem</td></tr>';
  $variables .= '<tr><td><code>--mcf-button-font-weight</code></td><td>700</td></tr>';
  $variables .= '</tbody></table>';

  $current_screen->add_help_tab(array(
    'id' => 'mcf-about-help-tab',
    'title' => __('About', 'mcf'),
    'content' => $about
  ));
  $current_screen->add_help_tab(array(
    'id' => 'mcf-settings-help-tab',
    'title' => __('Settings', 'mcf'),
    'content' => $settings
  ));
  $current_screen->add_help_tab(array(
    'id' => 'mcf-variables-help-tab',
    'title' => __('CSS Variables', 'mcf'),
    'content' => $variables
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
  // gdpr
  if (!isset($input['gdpr'])) $input['gdpr'] = 0;
  $validated['gdpr'] = ($input['gdpr'] == 1) ? 1 : 0;
  // spam
  if (!isset($input['spam'])) $input['spam'] = 0;
  $validated['spam'] = ($input['spam'] == 1) ? 1 : 0;
  // phpmail
  if (!isset($input['phpmail'])) $input['phpmail'] = 0;
  $validated['phpmail'] = ($input['phpmail'] == 1) ? 1 : 0;
  
  // phone
  if (!isset($input['phone'])) $input['phone'] = 0;
  $validated['phone'] = ($input['phone'] == 1) ? 1 : 0;
  // hide subject
  if (!isset($input['hidesubject'])) $input['hidesubject'] = 0;
  $validated['hidesubject'] = ($input['hidesubject'] == 1) ? 1 : 0;
  // one line
  if (!isset($input['oneline'])) $input['oneline'] = 0;
  $validated['oneline'] = ($input['oneline'] == 1) ? 1 : 0;
  // labels
  if (!isset($input['labels'])) $input['labels'] = 0;
  $validated['labels'] = ($input['labels'] == 1) ? 1 : 0;
  // css
  if (!isset($input['css'])) $input['css'] = '';
  $validated['css'] = trim((string)$input['css']);


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


