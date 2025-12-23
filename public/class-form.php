<?php

class MCF_Form
{

  private $mcf;
  private $version;

  protected $options;


  public function __construct($mcf, $version) {
    $this->mcf = $mcf;
    $this->version = $version;
    $this->options = get_option('mcf_options');

    add_shortcode('minimal_contact_form', array($this, 'register_shortcode'));
  }


  /**
   * Register Shortcode
   *
   * @since 1.0.0
   */
  public function register_shortcode($atts, $content = NULL) {
		ob_start();
		global $post, $shortcode_args;

		// Shortcode Default Array
		$shortcode_args = array(
			'before' => '<div>',
			'after' => '</div>',
		);

		// Combines user shortcode attributes with known attributes
		$shortcode_args = shortcode_atts(apply_filters('mcf_template_params', $shortcode_args, $atts), $atts);

		// Content
		$this->create_form();

		$html = ob_get_clean();
		return apply_filters('sep_fb_event_listing_shortcode', $html . do_shortcode($content));
	}


  /**
   * Create the form
   *
   * @since 1.0.0
   */
  public function create_form() {
    
    ?>
    <!-- Minimal Contact Form -->
    <div id="minimal-contact-form">
      <?php $this->add_styles(); ?>
      <div class="notice"></div>
      <form action="" method="post" class="<?php if ($this->options['styling']['item-single-column']) echo 'single-column'; ?>" novalidate>        
      <?php 
      if ($this->options['layout']['company'] === 1) $this->add_input('company', __('Company', 'mcf'), 'text', 'organization');
      if ($this->options['layout']['first-name'] === 1) $this->add_input('first-name', __('First Name', 'mcf'), 'text', 'given-name', true);
      if ($this->options['layout']['last-name'] === 1) $this->add_input('last-name', __('Last Name', 'mcf'), 'text', 'family-name', true);
      if ($this->options['layout']['name'] === 1) $this->add_input('name', __('Name', 'mcf'), 'text', 'name', true);
      if ($this->options['layout']['phone'] === 1) $this->add_input('phone', __('Phone', 'mcf'), 'tel', 'tel');
      $this->add_input('email', __('Email', 'mcf'), 'email', 'email', true);
      if ($this->options['layout']['subject'] === 1) $this->add_input('subject', __('Subject', 'mcf'), 'text', 'off', true);
      $this->add_textarea('message', __('Message', 'mcf'), true);
      $this->add_privacy();
      $this->add_security();
      $this->add_submit_button(__('Submit', 'mcf'));
      ?>
      </form>
    </div>
    <?php
  }


  /**
   * Returns a input field with the given arguments
   * 
   * @since 1.0.0
   */
  public function add_input($name, $label, $type = 'text', $autocomplete = '', $required = false) {
    $show_labels = $this->options['styling']['item-labels'] === 1;
    $bottom_border = $this->options['styling']['item-border-bottom'] === 1;
    ?>

    <div class="item item-<?php echo $name; ?>">
      <label
        class="<?php  echo ($show_labels) ? 'label' : 'no-label' ?>"
        for="<?php echo $name; ?>"
      >
        <?php echo $label; if ($required) echo '<span class="required">*</span>'; ?>
      </label>
      <input
        type="<?php echo $type; ?>"
        id="<?php echo $name; ?>"
        class="<?php echo $name; if ($bottom_border) echo ' bottom-border'; ?>"
        name="<?php echo $name; ?>"
        placeholder="<?php echo (!$show_labels) ? $label . ($required ? '*' : '') : ''; ?>"
        autocomplete="<?php echo ($autocomplete !== '') ? $autocomplete : 'off'; ?>"
        <?php echo ($required ? 'required' : '') ?>
      />
    </div>

    <?php
  }


  /**
   * Returns a textarea with the given arguments\
   * 
   * @since 1.0.0
   */
  public function add_textarea($name, $label, $required = false) {
    $show_labels = $this->options['styling']['item-labels'] === 1;
    $bottom_border = $this->options['styling']['item-border-bottom'] === 1;
    ?>

    <div class="item item-<?php echo $name; ?>">
      <label 
        class="<?php  echo ($show_labels) ? 'label' : 'no-label' ?>"
        for="<?php echo $name; ?>"
      >
        <?php echo $label; if ($required) echo '<span class="required">*</span>'; ?>
      </label>
      <textarea
        id="<?php echo $name; ?>"
        class="<?php echo $name; if ($bottom_border) echo ' bottom-border'; ?>"
        name="<?php echo $name; ?>"
        placeholder="<?php echo (!$show_labels) ? $label . ($required ? '*' : '') : ''; ?>"
        rows="5"
        autocomplete="off"
       <?php echo ($required ? 'required' : '') ?>
      ></textarea>
    </div>

    <?php
  }


  public function add_privacy() {
    ?>
    <div class='item item-privacy'>
    <?php if (get_option('wp_page_for_privacy_policy') || (int)get_option('wp_page_for_privacy_policy') > 0) : ?>
      <?php $policy_url = get_permalink(get_option('wp_page_for_privacy_policy')); ?>
      <?php if ($this->options['settings']['gdpr'] === 1) : ?>
        <input id="privacy" class="privacy" name="privacy" type="checkbox" value="1" required />
        <label class="privacy-caption" for="privacy">
          <?php echo __('I consent to having you process my submitted information so you can respond to my inquiry.', 'mcf'), " ", __('For further information please visit our', 'mcf'); ?>
           <a href="<?php echo $policy_url; ?>" target="_blank"><?php _e('Privacy Policy', 'mcf'); ?></a>.<span class="required">*</span>
        </label>
      <?php else : ?>
        <p class="privacy">
          <?php echo __('Your submitted information will only be processed to respond to your inquiry.', 'mcf'), " ", __('For further information please visit our', 'mcf'); ?>
         <a href="<?php echo $policy_url ?>" target="_blank"><?php _e('Privacy Policy', 'mcf'); ?></a>.</p>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <?php
  }


  /**
   * Generate CSRF and Honeypot
   *
   * @since 1.0.0
   */
  public function add_security() {
    session_start();
    if (empty($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    ?>
    <div class="item item-security">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    </div>
    <?php
  }


  /**
   * Submit Button
   *
   * @since 1.0.0
   */
  public function add_submit_button($name = 'Submit') {
    $right = $this->options['styling']['button-alignment'] === 1;
    ?>
    <div class="item item-submit">
      <input type="submit" class="submit<?php echo ($right) ? ' right' : ''; ?>" value="<?php echo $name; ?>" />
    </div>
    <?php
  }


  /**
   * Add Style Variables to frontend
   *
   * @since 1.0.0
   */
  public function add_styles() {

    $colors = array(
      'item-text-color' => '',
      'item-placeholder-color' => '',
      'item-background-color' => '',
      'item-background-color-focus' => '',
      'item-border-color' => '',
      'item-border-color-focus' => '',
      'item-border-width' => 'px',
      'item-border-radius' => 'rem',
      'item-padding' => 'rem',
      'item-spacing' => 'rem',
      'item-font-size' => 'em',
      'button-text-color' => '',
      'button-text-color-hover' => '',
      'button-background-color' => '',
      'button-background-color-hover' => '',
      'button-border-color' => '',
      'button-border-color-hover' => '',
      'button-border-width' => 'px',
      'button-border-radius' => 'rem',
      'button-padding' => 'rem',
      'button-font-size' => 'em',
      'misc-notice-color' => '',
      'misc-success-color' => '',
      'misc-warning-color' => '',
      'misc-error-color' => ''
    );

    ?>
    <style>
      :root {
        <?php
        foreach ($colors as $key => $unit) {
          if (isset($this->options['styling'][$key]) && $this->options['styling'][$key] !== '') {
            echo "--{$key}: ", $this->options['styling'][$key], $unit, ";\n";
          }
        }
        //echo "--item-border-width: {$this->options['styling']['item-border-width']}px;\n"; 
        ?>
      }
    </style>
    <?php
  }


  /**
   * Register REST Routes
   *
   * @since 1.0.0
   */
  public function register_routes() {
    register_rest_route('minimal-contact-form/v1', '/submit', [
      'methods' => 'POST',
      'callback' => array($this, 'process_form'),
    ]);
  }


  /**
   * Process the form
   *
   * @since 1.0.0
   */
  public function process_form(WP_REST_Request $request) {

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $posted_token = $request->get_param('csrf_token');
    if ($posted_token !== $_SESSION['csrf_token']) {
        return new WP_Error('invalid_request', __('Invalid Request', 'mcf'), array('status' => 400));
    }

    $data = $request->get_params();

    $valid = $this->validate_form_data($data);

    if ($valid === true) {
      // Senden Sie die E-Mail und senden Sie eine Erfolgsmeldung zurück

      return new WP_REST_Response(array(
        'message' => __('Die Nachricht wurde erfolgreich gesendet.', 'mcf'),
      ), 200);

    } else {
      // Senden Sie die Fehlermeldung zurück
      return new WP_Error($valid['status'], $valid['message'], array('status' => 400, 'fields' => $valid['fields']));
    }
  } 


  /**
   * Validate Form data
   *
   * @since 1.0.0
   */
  public function validate_form_data($data) {

    //var_dump($data);

    $missing = array();

    foreach ($this->options['layout'] as $field => $status) {
      if ($status === 1 && $field !== 'company' && $field !== 'phone' && empty($data[$field])) {
        array_push($missing, $field);
      }
    }

    if ($this->options['settings']['gdpr'] === 1 && !isset($data['privacy'])) {
      array_push($missing, 'privacy');
    }

    //var_dump($missing);

    // missing fields
    if (!empty($missing)) {
      return array(
        'status' => 'form_missing_fields',
        'message' => __('Please fill out all required fields.', 'mcf'),
        'fields' => $missing
      );
    }

    // email validation
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      return array(
        'status' => 'form_invalid_email',
        'message' => __('Please enter a valid e-mail address.', 'mcf'),
        'fields' => array('email')
      );
    }

    // too many links in the message
    if (substr_count($data['message'], 'http') > 2) {
      return array(
        'status' => 'form_too_many_links',
        'message' => __('Your message contains too many links. Please remove some and try again.', 'mcf'),
        'fields' => array('message')
      );
    }

    return true;
  }


  public function send_email($data) {

  }

}