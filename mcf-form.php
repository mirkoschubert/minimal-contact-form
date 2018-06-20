<?php

// Disable direct access
if (!defined( 'ABSPATH' )) exit();


/**
 * Add Shortcode
 * @since 0.4.0
 */
function mcf_shortcode_init() {
  function mcf_shortcode($attr = [], $content = null) {

    return mcf_display_contact_form();
  }
  add_shortcode('minimal_contact_form', 'mcf_shortcode');
}
add_action('init', 'mcf_shortcode_init');



function mcf_display_contact_form() {

  global $mcf_options;

  $content  = '<div id="minimal-contact-form">';
  $content .= '  <div class="notice"></div>';
  $content .= '  <form method="POST">';

  $content .= '    <input class="name" name="name" placeholder="' . __('Your Name', 'mcf') . __(' (required)', 'mcf')  . '" type="text" required>';
  $content .= '    <input class="email" name="email" placeholder="' . __('Your Email Address', 'mcf') . __(' (required)', 'mcf')  . '" type="email" required>';
  $content .= ($mcf_options['spam'] === 1) ? '    <input class="phone" name="phone" placeholder="' . __('Your Phone Number', 'mcf') . '" type="text">' : '';
  $content .= '    <input class="subject" name="subject" placeholder="' . __('Subject', 'mcf') . '" type="text">';
  $content .= '    <textarea class="message" name="message" placeholder="' . __('Your Message', 'mcf') . __(' (required)', 'mcf')  . '" rows="5" required></textarea>';
  $content .= '    <button class="submit" type="button">' . __('Submit', 'mcf') . '</button>';
  $content .= '  </form>';
  $content .= '</div>';

  return $content;
}


function mcf_send_mail() {
  $data = $_POST['data'];
  $data['name'] = sanitize_text_field($data['name']);
  $data['email'] = sanitize_email($data['email']);
  $data['subject'] = sanitize_text_field($data['subject']);
  $data['message'] = sanitize_textarea_field($data['message']);

  var_dump($data);

  die();
}
add_action('wp_ajax_mcf_send_mail', 'mcf_send_mail');
add_action('wp_ajax_nopriv_mcf_send_mail', 'mcf_send_mail');