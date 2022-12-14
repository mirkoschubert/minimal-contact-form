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


/**
 * Displays the contact form at the frontend
 * @since 0.4.0
 */
function mcf_display_contact_form() {

  global $mcf_options;

  $content  = '<div id="minimal-contact-form" class="' . ($mcf_options['labels'] === 1 ? 'with-labels' : '') . ($mcf_options['oneline'] === 1 ? ' one-line' : ''). '">';
  $content .= '  <div class="notice"></div>';
  $content .= '  <form method="POST">';

  $content .= mcf_input('name', __('Name', 'mcf'), 'text', true);
  $content .= mcf_input('email', __('Email Address', 'mcf'), 'email', true);
  $content .= $mcf_options['phone'] === 1 ? mcf_input('phone', __('Phone Number', 'mcf'), 'tel') : '';
  $content .= $mcf_options['spam'] === 1 ? mcf_input('address', __('Address', 'mcf'), 'text', false, false) : '';
  $content .= $mcf_options['hidesubject'] === 0 ? mcf_input('subject', __('Subject', 'mcf')) : '';
  $content .= mcf_textarea('message', __('Message', 'mcf'), true);
  $content .= mcf_gdpr();
  $content .= mcf_submit();

  $content .= '  </form>';
  $content .= '</div>';

  return $content;
}


/**
 * Returns a input field with the given arguments
 * @since 0.9.0
 */
function mcf_input($name, $label, $type = 'text', $required = false, $has_label = true) {
  global $mcf_options;

  $content  = "<div class='item item-$name'>";
  $content .= "  <label class='" . ($has_label ? 'label' : 'no-label') . "' for='$name'>$label" . ($required ? '<span class="required">*</span>' : '') . "</label>";
  $content .= "  <input type='$type' id='$name' class='$name' name='$name' placeholder='" . ($mcf_options['labels'] !== 1 ? $label . ($required ? '*' : '') : '') . "' " . ($required ? 'required' : '') . " />";
  $content .= "</div>";

  return $content;
}


/**
 * Returns a textarea with the given arguments
 * @since 0.9.0
 */
function mcf_textarea($name, $label, $required = false) {
  global $mcf_options;

  $content  = "<div class='item item-$name'>";
  $content .= "  <label class='label' for='$name'>$label" . ($required ? '<span class="required">*</span>' : '') . "</label>";
  $content .= "  <textarea id='$name' class='$name' name='$name' placeholder='" . ($mcf_options['labels'] !== 1 ? $label . ($required ? '*' : '') : '')  . "' rows='5' " . ($required ? 'required' : '') . "></textarea>";
  $content .= "</div>";

  return $content;
}


/**
 * Returns the GDPR text or checkbox
 * @since 0.9.0
 */
function mcf_gdpr() {
  global $mcf_options;

  $content  = "<div class='item item-gdpr'>";
  if (get_option('wp_page_for_privacy_policy') || (int)get_option('wp_page_for_privacy_policy') > 0) {
    $pp_url = get_permalink(get_option('wp_page_for_privacy_policy'));
    if ($mcf_options['gdpr'] === 1) {
      // Opt-In
      $content .= '<input id="gdpr" class="gdpr" name="gdpr" type="checkbox" value="1" />';
      $content .= '<label class="gdpr-caption" for="gdpr">' . __('I consent to having you process my submitted information so you can respond to my inquiry.', 'mcf') . ' ' . __('For further information please visit our', 'mcf') . ' <a href="' . $pp_url . '" target="_blank">' . __('Privacy Policy', 'mcf') . '</a>.<span class="required">' . __('*', 'mcf') . '</span></label>';
    } else {
      // Only Information
      $content .= '<p class="privacy">' . __('Your submitted information will only be processed to respond to your inquiry.', 'mcf') . ' ' . __('For further information please visit our', 'mcf') . ' <a href="' . $pp_url . '" target="_blank">' . __('Privacy Policy', 'mcf') . '</a>.</p>';

    }
  }
  $content .= "</div>";

  return $content;
}

/**
 * Returns the submit button
 * @since 0.9.0
 */
function mcf_submit() {
  $content  = '<div class="item item-submit">';
  $content .= '  <button class="submit" type="button">' . __('Submit', 'mcf') . '</button>';
  $content .= '</div>';

  return $content;
}


/**
 * HELPER: Send translated validation message via AJAX
 * @since 0.5.1
 */
function mcf_ajax_translate_message() {
  $type = $_POST['type'];

  if ($type !== '') {
    if ($type === 'validation_error') echo __('Sorry! Please fill in all required fields correctly.', 'mcf');
    if ($type === 'validation_phone_error') echo __('Sorry! Please enter a valid phone number.', 'mcf');
  }

  die();
}
add_action('wp_ajax_mcf_ajax_translate_message', 'mcf_ajax_translate_message');
add_action('wp_ajax_nopriv_mcf_ajax_translate_message', 'mcf_ajax_translate_message');


/**
 * Sends an email and answers with an AJAX response
 * @since 0.5.0
 */
function mcf_ajax_send_mail() {
  global $mcf_options;

  $data = $_POST['data'];

  // AJAX data
  $name = sanitize_text_field($data['name']);
  $email = sanitize_email($data['email']);
  $phone = ($mcf_options['phone'] === 1) ? sanitize_text_field($data['phone']) : false;
  $address = ($mcf_options['spam'] === 1) ? sanitize_text_field($data['address']) : false; // honeypot
  $subject = (isset($data['subject']) && $data['subject'] !== '') ? sanitize_text_field($data['subject']) : __('Someone left you a message!', 'mcf');
  $msg = sanitize_textarea_field($data['message']);
  $consent = (int)$data['consent'];
  // Plugin data
  $user = get_userdata($mcf_options['user']); 
  $charset = get_option('blog_charset', 'UTF-8');
  $date    = get_date_from_gmt(current_time('mysql', true), 'r');

  // E-Mail Headers
  $headers  = "From: $name <$email>\n";
  $headers .= "Sender: $name <$email>\n";
  $headers .= "Reply-To: $name <$email>\n";
  $headers .= ($mcf_options['phpmail'] === 1) ? "MIME-Version: 1.0\n" : ""; // not wp_mail()
  $headers .= ($mcf_options['phpmail'] === 1) ? "X-Mailer: PHP/". phpversion() . "\n" : ""; // not wp_mail()
  $headers .= "Content-Type: text/plain; charset=$charset\n";

  $message  = "";
  $message .= ($mcf_options['gdpr'] === 1) ? ($consent === 1 ? "[x] " . __('The user has agreed to the data collection.', 'mcf') . "\n\n" : "[ ] " . __('The user has not agreed to the data collection.', 'mcf') . "\n\n") : "";
  $message .= ($mcf_options['phone'] === 1 && $phone !== '') ? "[x] " . __('Phone Number', 'mcf') . ": " . $phone . "\n\n" : "";
  $message .= wp_strip_all_tags($msg) . "\n";

  if ($address === false || $address === '') {
    if ($mcf_options['phpmail'] === 1)
      $success = mail("$user->display_name <$user->user_email>", $subject, $message, $headers);
    else
      $success = wp_mail("$user->display_name <$user->user_email>", $subject, $message, $headers);
  
    if ($success)
      echo '<p class="success">' . __('Thank you for sending us a message! You will hear from us shortly.', 'mcf') . '</p>';
    else
      echo '<p class="error">' . __("Sorry! Your message couldn't be sent. Please try again later.", 'mcf') . '</p>';
  } else {
    echo '<p class="warning">' . __("You're a bot!") . '</p>';
  }
  
  die();
}
add_action('wp_ajax_mcf_ajax_send_mail', 'mcf_ajax_send_mail');
add_action('wp_ajax_nopriv_mcf_ajax_send_mail', 'mcf_ajax_send_mail');
