<?php

// Disable direct access
if (!defined( 'ABSPATH' )) exit();


/**
 * Initialize the options menu page
 * @since 0.2.0
 */
function mcf_options_page() {
  global $mcf_plugin, $mcf_slug;
  add_options_page(
    $mcf_plugin,
    __( 'Contact Form', 'mcf' ),
    'manage_options',
    $mcf_slug,
    'mcf_render_options_page'
  );
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

?>