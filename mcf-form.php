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

  if (get_option('wp_page_for_privacy_policy') || (int)get_option('wp_page_for_privacy_policy') > 0) {
    $pp_url = get_permalink(get_option('wp_page_for_privacy_policy'));

    if ($mcf_options['gdpr'] === 1) {
      // Opt-In
      $content .= '<input class="consent" name="consent" type="checkbox" value="1" />';
      $content .= '<label class="consent-caption" for="consent">' . __('By sending us a message via this contact form you agree that your personal data will be processed.', 'mcf') . ' ' . __('For further information please visit our', 'mcf') . ' <a href="' . $pp_url . '" target="_blank">' . __('Privacy Policy', 'mcf') . '</a>.<span class="required">' . __(' (required)', 'mcf') . '<span></label>';
    } else {
      // Only Information
      $content .= '<p class="privacy">' . __('By sending us a message via this contact form you understand that your personal data will be processed.', 'mcf') . ' ' . __('For further information please visit our', 'mcf') . ' <a href="' . $pp_url . '" target="_blank">' . __('Privacy Policy', 'mcf') . '</a>.</p>';

    }

  }


  $content .= '    <button class="submit" type="button">' . __('Submit', 'mcf') . '</button>';
  $content .= '  </form>';
  $content .= '</div>';

  return $content;
}


function mcf_send_mail() {
  global $mcf_options;

  $data = $_POST['data'];
  // AJAX data
  $name = sanitize_text_field($data['name']);
  $email = sanitize_email($data['email']);
  $subject = ($data['subject'] !== '') ? sanitize_text_field($data['subject']) : __('Someone left you a message!', 'mcf');
  $msg = sanitize_textarea_field($data['message']);
  $consent = (int)$data['consent'];
  // Plugin data
  $user = get_userdata($mcf_options['user']); 
  $charset = get_option('blog_charset', 'UTF-8');
  $date    = get_date_from_gmt(current_time('mysql', true), 'r');

  // E-Mail Headers
  $headers  = "X-Mailer: Minimal Contact Form \n";
  $headers .= "Date: $date\n"; // Thu, 21 Jun 2018 12:41:47 +0000
  $headers .= "From: $name <$email>\n";
  $headers .= "Sender: $name <$email>\n";
  $headers .= "Reply-To: $name <$email>\n";
  $headers .= "To: $user->display_name <$user->user_email>\n";
  $headers .= "MIME-Version: 1.0\n";
  $headers .= "Content-Type: text/plain; charset=$charset\n";
  $headers .= "Subject: $subject\n";


  $message  = "\n";
  $message .= ($mcf_options['gdpr'] === 1) ? ($consent === 1 ? "[x] " . __('The user has agreed to the data collection.!', 'mcf') . "\n\n" : "[ ] " . __('The user has not agreed to the data collection.!', 'mcf') . "\n\n") : "";
  $message .= wp_strip_all_tags($msg) . "\n";
  
  echo $headers;
  echo $message;

  echo mb_detect_encoding($msg) . "\n";
  echo get_option('timezone_string') . "\n";
  echo get_date_from_gmt(current_time('mysql', true), 'r');

  die();
}
add_action('wp_ajax_mcf_send_mail', 'mcf_send_mail');
add_action('wp_ajax_nopriv_mcf_send_mail', 'mcf_send_mail');