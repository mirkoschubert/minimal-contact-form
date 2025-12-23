<?php

class MCF_Admin
{

  private $mcf;
  private $version;

  public $defaults;

  protected $options;



  public function __construct($mcf, $version)
  {

    $this->mcf = $mcf;
    $this->version = $version;
    $this->defaults = $this->set_defaults();
    $this->options = get_option('mcf_options');
  }


  /**
   * Sets default options.
   * @since 0.3.0
   */
  public function set_defaults()
  {
    $options = array(
      'settings' => array(
        'user' => 1,
        'gdpr' => 0,
        'spam' => 1,
        'phpmail' => 0,
      ),
      'layout' => array(
        'company' => 0,
        'first-name' => 1,
        'last-name' => 1,
        'name' => 0,
        'phone' => 0,
        'email' => 1,
        'subject' => 1,
        'message' => 1
      ),
      'styling' => array(
        'item-text-color' => '',
        'item-placeholder-color' => '',
        'item-background-color' => '',
        'item-background-color-focus' => '',
        'item-border-color' => '',
        'item-border-color-focus' => '',
        'item-border-width' => 1,
        'item-border-bottom' => 0,
        'item-border-radius' => 0.25,
        'item-padding' => 0.75,
        'item-spacing' => 0.75,
        'item-font-size' => 1,
        'item-single-column' => 0,
        'item-labels' => 0,
        'button-text-color' => '#ffffff',
        'button-text-color-hover' => '#ffffff',
        'button-background-color' => '#222222',
        'button-background-color-hover' => '#555555',
        'button-border-color' => '',
        'button-border-color-hover' => '',
        'button-border-width' => 2,
        'button-border-radius' => 0.25,
        'button-border-none' => 0,
        'button-padding' => 0.75,
        'button-font-size' => 1,
        'button-alignment' => 0,
        'show-labels' => 0,
        'misc-notice-color' => '#333333',
        'misc-success-color' => '#46b450',
        'misc-warning-color' => '#ffb900',
        'misc-error-color' => '#dc3232',
        'misc-custom-css' => ''
      )
    );
    return $options;
  }


  /**
   * Register the stylesheets for the admin area.
   * @since 1.0.0
   */
  public function enqueue_styles()
  {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_style($this->mcf, plugin_dir_url(__FILE__) . 'assets/css/mcf-admin.css', array(), $this->version, 'all');
  }


  /**
   * Register the JavaScript for the admin area.
   * @since 1.0.0
   */
  public function enqueue_scripts()
  {
    wp_enqueue_script($this->mcf, plugin_dir_url(__FILE__) . 'assets/js/mcf-admin.js', array('jquery', 'wp-color-picker', 'jquery-ui-accordion', 'jquery-ui-slider'), $this->version, false);
  }


  /**
   * Adds the admin menu.
   * @since 0.2.0
   */
  public function add_admin_menu()
  {
    global $mcf_plugin, $mcf_slug, $plugin_hook;

    $plugin_hook = add_options_page(
      __('Minimal Contact Form', 'mcf'),
      __('Contact Form', 'mcf'),
      'manage_options',
      'minimal-contact-form',
      array($this, 'settings_page')
    );

    if ($plugin_hook) {
      add_action('load-' . $plugin_hook, array($this, 'add_help'));
    }
  }


  /**
   * Add Contextual Help
   *
   * @since 1.0.0
   */
  public function add_help() {
    $current_screen = get_current_screen();

    $about = '<p><strong>' . __('Minimal Contact Form', 'mcf') . '</strong> ' . __('is a simple, clean and secure contact form.', 'mcf') . '</p><p>' . __('This plugin was developed with usability in mind and uses data that already exists. It provides security features to prevent the receipt of spam without passing on data to third parties. In addition, it automatically inserts a corresponding notice to comply with the requirements of the GDPR.', 'mcf') . '</p>';

    $settings = '<h4>' . __('About the settings', 'mcf') . '</h4><ul><li>' . __('If you refer to Art. 6 (1) let. b or let. f GDPR in your privacy policy, you do not need an opt-in. Only if you reference Art. 6 (1) let. a GDPR should you tick the relevant checkbox.', 'mcf') . '</li><li>' . __("The WordPress PHPmailer (SMTP) should be used by default. If you encounter an error, please turn on the PHP mail function. However, the emails will end up in the recipient's spam folder more likely.", 'mcf') . '</li><li>' . __('To display the form on any WP Post or Page, simply add the shortcode:', 'mcf') . ' <code>[minimal_contact_form]</code>.</li></ul>';

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
  }


  /**
   * Registers the settings
   * @since 0.2.0
   */
  public function register_settings()
  {
    do_action('qm/debug', $_POST);
    do_action('qm/debug', $_GET);
    do_action('qm/debug', get_option('mcf_options'));
    if (!get_option('mcf_options')) {
      add_option('mcf_options', $this->defaults);
    } else {
      register_setting('mcf_options', 'mcf_options', array($this, 'sanitize'));
    }
  }


  /**
   * Sanitizes the new settings before saving.
   * @since 0.3.0
   * @todo
   */
  public function sanitize($options)
  {

    $output = array();

    // Settings
    $output['settings']['user'] = sanitize_int($options['settings']['user'], $this->defaults['settings']['user']);
    $output['settings']['gdpr'] = sanitize_checkbox($options['settings']['gdpr'], $this->defaults['settings']['gdpr']);
    $output['settings']['spam'] = sanitize_checkbox($options['settings']['spam'], $this->defaults['settings']['spam']);
    $output['settings']['phpmail'] = sanitize_checkbox($options['settings']['phpmail'], $this->defaults['settings']['phpmail']);

    // Layout
    $output['layout']['company'] = sanitize_checkbox($options['layout']['company'], $this->defaults['settings']['company']);
    $output['layout']['first-name'] = sanitize_checkbox($options['layout']['first-name'], $this->defaults['settings']['first-name']);
    $output['layout']['last-name'] = sanitize_checkbox($options['layout']['last-name'], $this->defaults['settings']['last-name']);
    $output['layout']['name'] = sanitize_checkbox($options['layout']['name'], $this->defaults['settings']['name']);
    $output['layout']['phone'] = sanitize_checkbox($options['layout']['phone'], $this->defaults['settings']['phone']);
    $output['layout']['email'] = sanitize_checkbox($options['layout']['email'], $this->defaults['settings']['email']);
    $output['layout']['subject'] = sanitize_checkbox($options['layout']['subject'], $this->defaults['settings']['subject']);
    $output['layout']['message'] = sanitize_checkbox($options['layout']['message'], $this->defaults['settings']['message']);


    // Item Colors
    $output['styling']['item-text-color'] = sanitize_color($options['styling']['item-text-color'], $this->defaults['styling']['item-text-color']);
    $output['styling']['item-placeholder-color'] = sanitize_color($options['styling']['item-placeholder-color'], $this->defaults['styling']['item-placeholder-color']);
    $output['styling']['item-background-color'] = sanitize_color($options['styling']['item-background-color'], $this->defaults['styling']['item-background-color']);
    $output['styling']['item-background-color-focus'] = sanitize_color($options['styling']['item-background-color-focus'], $this->defaults['styling']['item-background-color-focus']);
    $output['styling']['item-border-color'] = sanitize_color($options['styling']['item-border-color'], $this->defaults['styling']['item-border-color']);
    $output['styling']['item-border-color-focus'] = sanitize_color($options['styling']['item-border-color-focus'], $this->defaults['styling']['item-border-color-focus']);

    // Item Stylling
    $output['styling']['item-border-width'] = sanitize_int($options['styling']['item-border-width'], $this->defaults['styling']['item-border-width']);
    $output['styling']['item-border-bottom'] = sanitize_checkbox($options['styling']['item-border-bottom'], $this->defaults['styling']['item-border-bottom']);
    $output['styling']['item-border-radius'] = sanitize_float($options['styling']['item-border-radius'], $this->defaults['styling']['item-border-radius']);
    $output['styling']['item-padding'] = sanitize_float($options['styling']['item-padding'], $this->defaults['styling']['item-padding']);
    $output['styling']['item-spacing'] = sanitize_float($options['styling']['item-spacing'], $this->defaults['styling']['item-spacing']);
    $output['styling']['item-font-size'] = sanitize_float($options['styling']['item-font-size'], $this->defaults['styling']['item-font-size']);
    $output['styling']['item-single-column'] = sanitize_checkbox($options['styling']['item-single-column'], $this->defaults['styling']['item-single-column']);
    $output['styling']['item-labels'] = sanitize_checkbox($options['styling']['item-labels'], $this->defaults['styling']['item-labels']);
    
    // Button Colors
    $output['styling']['button-text-color'] = sanitize_color($options['styling']['button-text-color'], $this->defaults['styling']['button-text-color']);
    $output['styling']['button-text-color-hover'] = sanitize_color($options['styling']['button-text-color-hover'], $this->defaults['styling']['button-text-color-hover']);
    $output['styling']['button-background-color'] = sanitize_color($options['styling']['button-background-color'], $this->defaults['styling']['button-background-color']);
    $output['styling']['button-background-color-hover'] = sanitize_color($options['styling']['button-background-color-hover'], $this->defaults['styling']['button-background-color-hover']);
    $output['styling']['button-border-color'] = sanitize_color($options['styling']['button-border-color'], $this->defaults['styling']['button-border-color']);
    $output['styling']['button-border-color-hover'] = sanitize_color($options['styling']['button-border-color-hover'], $this->defaults['styling']['button-border-color-hover']);
    $output['styling']['button-border-width'] = sanitize_float($options['styling']['button-border-width'], $this->defaults['styling']['button-border-width']);
    $output['styling']['button-border-radius'] = sanitize_float($options['styling']['button-border-radius'], $this->defaults['styling']['button-border-radius']);
    $output['styling']['button-border-none'] = sanitize_checkbox($options['styling']['button-border-none'], $this->defaults['styling']['button-border-none']);
    $output['styling']['button-padding'] = sanitize_float($options['styling']['button-padding'], $this->defaults['styling']['button-padding']);
    $output['styling']['button-font-size'] = sanitize_float($options['styling']['button-font-size'], $this->defaults['styling']['button-font-size']);
    $output['styling']['button-alignment'] = sanitize_checkbox($options['styling']['button-alignment'], $this->defaults['styling']['button-alignment']);
    
    $output['styling']['misc-notice-color'] = sanitize_color($options['styling']['misc-notice-color'], $this->defaults['styling']['misc-notice-color']);
    $output['styling']['misc-success-color'] = sanitize_color($options['styling']['misc-success-color'], $this->defaults['styling']['misc-success-color']);
    $output['styling']['misc-warning-color'] = sanitize_color($options['styling']['misc-warning-color'], $this->defaults['styling']['misc-warning-color']);
    $output['styling']['misc-error-color'] = sanitize_color($options['styling']['misc-error-color'], $this->defaults['styling']['misc-error-color']);
    $output['styling']['misc-custom-css'] = sanitize_textarea($options['styling']['misc-custom-css'], $this->defaults['styling']['misc-custom-css']);
    /* $options['phone'] = (!empty($options['phone'])) ? 'on' : 'off';
    $options['hidesubject'] = (!empty($options['hidesubject'])) ? 'on' : 'off';
    $options['oneline'] = (!empty($options['oneline'])) ? 'on' : 'off';
    $options['labels'] = (!empty($options['labels'])) ? 'on' : 'off';
    $options['css'] = sanitize_textarea_field($options['css']); */

    return $output;
  }



  /**
   * ELEMENT: User Dropdown
   * @since 1.0.0
   */
  public function add_user_dropdown($category, $id, $title, $description, $version = NULL) {
    ?>
    <tr>
      <th scope="row">
        <label for="<?php echo $id; ?>"><?php echo $title; ?></label>
      </th>
      <td>
        <div class="dropdown">
          <?php wp_dropdown_users(
            array(
              'name' => 'mcf_options['.$category.']['.$id.']',
              'id' => $id,
              'class' => $id,
              'selected' => $this->options[$category][$id],
              'include_selected' => true,
              'role__in' => array('administrator', 'editor')
            )
          ); ?>
          <p class="description"><?php echo $description; ?></p>
        </div>
      </td>
    </tr>
    <?php
  }


  /**
   * ELEMENT: Checkbox
   * @since 1.0.0
   */
  public function add_checkbox($category, $id, $title, $description, $version = NULL)
  {
    ?>
    <tr>
      <th scope="row">
        <?php echo $title; ?>
      </th>
      <td>
        <label for="<?php echo $id; ?>">
          <input type="checkbox" name="<?php echo 'mcf_options['.$category.']['.$id.']'; ?>" id="<?php echo $id; ?>" <?php echo ($this->options[$category][$id] == 1) ? 'checked' :  ''; ?>>
          <span class="caption">
            <?php echo $description;
            if ($version) { ?> <span class="versions">
                <?php echo $version; ?>
              </span>
            <?php } ?>
          </span>
        </label>
      </td>
    </tr>
    <?php
  }


  /**
   * Element: Toggle
   * 
   * @since 1.0.0
   */
  public function add_toggle($category, $id, $title, $description, $on = 'On', $off = 'Off', $version = NULL ) {
    ?>
    <tr>
      <th scope="row">
        <?php echo $title; ?>
      </th>
      <td>
        <div class="toggle-container">
          <label class="toggle" for="<?php echo $id; ?>">
            <input type="checkbox" name="<?php echo 'mcf_options['.$category.']['.$id.']'; ?>" id="<?php echo $id; ?>" class="toggle-input" <?php echo ($this->options[$category][$id] == 1) ? 'checked' :  ''; ?>>
            <span class="toggle-label" data-on="<?php echo $on; ?>" data-off="<?php echo $off; ?>"></span>
            <span class="toggle-handle"></span>
          </label>
          <span class="caption">
            <?php echo $description;
            if ($version) { ?> <span class="versions">
                <?php echo $version; ?>
              </span>
            <?php } ?>
          </span>
        </div>
      </td>
    </tr>
    <?php
  }

  /**
   * ELEMENT: Text Field
   * @since 1.0.0
   */
  public function add_textfield($category, $id, $title, $description, $version = NULL) {
    ?>
    <tr>
      <th scope="row"><label for="<?php echo $id; ?>"><?php echo $title; ?></label></th>
      <td>
        <textarea name="mcf_options[<?php echo $category; ?>][<?php echo $id; ?>]" id="<?php echo $id; ?>" rows="6"><?php echo isset($this->options[$category][$id]) ? $this->options[$category][$id] : ''; ?></textarea>
        <p class="description"><?php echo $description ?></p>
      </td>
    </tr>
    <?php
  }


  /**
   * ELEMENT: Privacy Policy
   * @since 1.0.0
   */
  public function add_privacy_policy($title, $description, $version = NULL) {
    if (get_option('wp_page_for_privacy_policy') === false || get_option('wp_page_for_privacy_policy') === '0') : ?>
    <tr>
      <th scope="row"><?php echo $title; ?></th>
      <td>
        <p class="description"><?php printf($description, '<a href="' . get_admin_url() . 'options-privacy.php">' . esc_html__('Privacy Settings', 'mcf') . '</a>'); ?></p>
      </td>
    </tr>
    <?php endif;
  }

  /**
   * ELEMENT: Color Picker
   * @since 1.0.0
   */
  public function add_color_picker($category, $id, $title) {
    ?>
    <tr>
      <th scope="row">
        <?php echo $title; ?>
      </th>
      <td>
        <input type="text" class="color-picker" name="mcf_options[<?php echo $category; ?>][<?php echo $id; ?>]" value="<?php echo $this->options[$category][$id]; ?>" data-css-var="<?php echo '--'.$id; ?>" />
      </td>
    </tr>
    <?php
  }



  /**
   * ELEMENT: Multiple Color Pickers in one line
   *
   * @param array $states ($key => $name)
   * @since 1.0.0
   */
  public function add_multi_color_picker($category, $id, $title, $states = array()) {
    if (!isset($states) || sizeof($states) == 0) {
      return;
    }
    ?>
    <tr>
      <th scope="row">
        <?php echo $title; ?>
      </th>
      <td class="multi-color-pickers">
        <?php foreach ($states as $key => $name) : ?>
        <?php $suffix = ($key === 'normal' || $key === 'standard') ? '' : '-' . $key; ?>
        <div class="state state-<?php echo $key; ?>">
          <label><?php echo $name; ?></label>
          <input type="text" class="color-picker" name="mcf_options[<?php echo $category; ?>][<?php echo $id.$suffix; ?>]" value="<?php echo $this->options[$category][$id.$suffix]; ?>" data-css-var="<?php echo '--'.$id.$suffix; ?>" />              
        </div>          
        <?php endforeach; ?>
      </td>
    </tr>
    <?php
  }


  /**
   * ELEMENT: Slider
   *
   * @since 1.0.0
   */
  public function add_slider($category, $id, $title, $min, $max, $step, $unit) {
    ?>
    <tr>
      <th scope="row">
        <?php echo $title; ?>
      </th>
      <td class="slider-row">
        <div class="styling-slider styling-slider-<?php echo $id; ?>" data-value="<?php echo $this->options[$category][$id]; ?>"></div>
        <input type="number" min="<?php echo $min; ?>" max="<?php echo $max; ?>" step="<?php echo $step; ?>" class="styling-slider-value" name="mcf_options[<?php echo $category; ?>][<?php echo $id; ?>]" value="<?php echo $this->options[$category][$id]; ?>" data-css-var="<?php echo '--'.$id; ?>">
        <span class="styling-slider-amount" data-unit="<?php echo $unit ?>"><?php echo $this->options[$category][$id] . $unit; ?></span>
      </td>
    </tr>
    <?php
  }


  /**
   * ELEMENT: Headline
   *
   * @since 1.0.0
   */
  public function add_headline($title, $weight = 'h3') {
    echo '<' . $weight . '>' . $title . '</' . $weight . '>';
  }


  /**
   * Loads the settings page.
   * @since 1.0.0
   */
  public function settings_page()
  {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized user');
    }
    require_once plugin_dir_path(dirname(__FILE__)) . 'admin/views/admin.php';
  }

}