<?php

// Disable direct access
if (!defined( 'ABSPATH' )) exit();


/**
 * Initialize the options menu page
 * @since 0.2.0
 */
function mcf_menu_page() {
  add_options_page( __( 'Contact Form', 'mcf' ), __( 'Contact Form', 'mcf' ), 'manage_options', 'mcf', 'mcf_options_page' );
}
add_action( 'admin_menu', 'mcf_menu_page' );


/**
 * Initialize the settings
 * @since 0.2.0
 */
function mcf_options_init() {
  //add_settings_section( 'mcf-general-section', __( 'General', 'mcf' ), '', 'mcf' );
}
add_action( 'admin_init', 'mcf_options_init' );


/**
 * Display the options page
 * @since 0.2.0
 */
function mcf_options_page() {

  global $mcf_version, $mcf_plugin;
?>
<div id="mcf-plugin" class="wrap"> 
  <div id="icon-plugins" class="icon32"></div> 
  <h1><?php _e( 'Minimal Contact Form', 'mcf' ); ?> <small><?php echo 'v'. $mcf_version; ?></small></h1> 
  <form action="options.php" method="POST">
  <?php settings_fields( 'mcf-options' ); ?>
    <div class="metabox-holder">
      <div class="meta-box-sortables ui-sortable">
        <div class="postbox panel-overview">
          <h2 class="hndle ui-sortable-handle"><?php esc_html_e('Overview', 'mcf'); ?></h2>
          <div class="inside">
            <div class="main">
              <p>
                <strong><?php echo $mcf_plugin; ?></strong> <?php esc_html_e('is a secure contact form that&rsquo;s fast and flexible.', 'mcf'); ?> 
                <?php esc_html_e('To display the form on any WP Post or Page, add the shortcode:', 'mcf'); ?>  <code>[minimal_contact_form]</code>. 
              </p>
            </div>
          </div>
        </div>
        <div class="postbox panel-options">
          <h2 class="hndle ui-sortable-handle"><?php esc_html_e('Options', 'mcf'); ?></h2>
          <div class="inside">
            <div class="main">
              <table class="form-table">
                <tbody>
                  <tr>
                    <th scope="row"><label class="" for="mcf_options[mcf_user]"><?php esc_html_e('User', 'mcf'); ?></label></th>
                    <td>
                      <?php wp_dropdown_users(array('name' => 'mcf_options[mcf_user]', 'selected' => $mcf_options['mcf_user'], 'include_selected' => true, 'role__in' => array('administrator', 'editor'))); ?>
                      <p class="description"><?php esc_html_e('The administrator or editor who should receive the emails.', 'mcf'); ?></p>
                    </td>
                  </tr>
                  <?php if (get_option('wp_page_for_privacy_policy') === false || get_option('wp_page_for_privacy_policy') === '0') : ?>
                  <tr>
                    <th scope="row"><label class=""><?php esc_html_e('Privacy Policy', 'mcf'); ?></label></th>
                    <td>
                      <p class="description"><?php esc_html_e('You have to set a privacy policy page in the', 'mcf') ?> <a href="<?php echo get_admin_url(); ?>privacy.php"><?php esc_html_e('Privacy Settings', 'mcf') ?></a>!</p>
                    </td>
                  </tr>
                  <?php endif; ?>
                  <tr>
                    <th scope="row"><label class="" for="mcf_options[mcf_gdpr]"><?php esc_html_e('GDPR', 'mcf'); ?></label></th>
                    <td>
                      <input name="mcf_options[mcf_gdpr]" type="checkbox" value="1" <?php if (isset($mcf_options['mcf_gdpr'])) { checked('1', $mcf_options['mcf_gdpr']); } ?> />
                      <span class="mm-item-caption"><?php esc_html_e('Use Opt-In instead of infoming the visitor about the GDPR.', 'mcf'); ?></span>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"><label class="" for="mcf_options[mcf_spam]"><?php esc_html_e('Antispam', 'mcf'); ?></label></th>
                    <td>
                      <input name="mcf_options[mcf_spam]" type="checkbox" value="1" <?php if (isset($mcf_options['mcf_spam'])) { checked('1', $mcf_options['mcf_spam']); } ?> />
                      <span class="mm-item-caption"><?php esc_html_e('Use honeypot to protect against SPAM emails.', 'mcf'); ?></span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php do_settings_sections( 'mcf' ); ?>
  <?php submit_button(); ?>
  </form>
</div>
<?php
}

?>