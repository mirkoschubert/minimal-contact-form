<div id="mcf-plugin" class="wrap"> 
  <div id="icon-plugins" class="icon32"></div> 
  <h1><?php _e( 'Minimal Contact Form', 'mcf' ); ?> <small><?php echo 'v'. $mcf_version; ?></small></h1>
  <form action="options.php" method="POST">
  <?php settings_fields( 'mcf_plugin_options' ); ?>
    <div class="metabox-holder">
      <div class="meta-box-sortables ui-sortable">
        <div class="postbox panel-options">
          <h2 class="hndle ui-sortable-handle"><?php esc_html_e('Options', 'mcf'); ?></h2>
          <div class="inside">
            <div class="main">
              <table class="form-table">
                <tbody>
                  <tr>
                    <th scope="row"><label class="" for="mcf_options[user]"><?php esc_html_e('Recipient', 'mcf'); ?></label></th>
                    <td>
                      <?php wp_dropdown_users(array('name' => 'mcf_options[user]', 'selected' => $mcf_options['user'], 'include_selected' => true, 'role__in' => array('administrator', 'editor'))); ?>
                      <p class="description"><?php esc_html_e('Select the administrator or editor who should receive the emails.', 'mcf'); ?></p>
                    </td>
                  </tr>
                  <?php if (get_option('wp_page_for_privacy_policy') === false || get_option('wp_page_for_privacy_policy') === '0') : ?>
                  <tr>
                    <th scope="row"><label class=""><?php esc_html_e('Privacy Policy', 'mcf'); ?></label></th>
                    <td>
                      <p class="description"><?php printf(__('You have to set a privacy policy page in the %1$s!', 'mcf'), '<a href="' . get_admin_url() . 'privacy.php">' . esc_html__('Privacy Settings', 'mcf') . '</a>'); ?></p>
                    </td>
                  </tr>
                  <?php endif; ?>
                  <tr>
                    <th scope="row"><label class="" for="mcf_options[gdpr]"><?php esc_html_e('GDPR', 'mcf'); ?></label></th>
                    <td>
                      <input name="mcf_options[gdpr]" type="checkbox" value="1" <?php if (isset($mcf_options['gdpr'])) { checked('1', $mcf_options['gdpr']); } ?> />
                      <span class="mm-item-caption"><?php esc_html_e('Use an opt-in instead of just informing the visitor about the GDPR.', 'mcf'); ?></span>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"><label class="" for="mcf_options[spam]"><?php esc_html_e('Antispam', 'mcf'); ?></label></th>
                    <td>
                      <input name="mcf_options[spam]" type="checkbox" value="1" <?php if (isset($mcf_options['spam'])) { checked('1', $mcf_options['spam']); } ?> />
                      <span class="mm-item-caption"><?php esc_html_e('Use the honeypot mechanism to protect against SPAM emails.', 'mcf'); ?></span>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"><label class="" for="mcf_options[phpmail]"><?php esc_html_e('Mail Service', 'mcf'); ?></label></th>
                    <td>
                      <input name="mcf_options[phpmail]" type="checkbox" value="1" <?php if (isset($mcf_options['phpmail'])) { checked('1', $mcf_options['phpmail']); } ?> />
                      <span class="mm-item-caption"><?php esc_html_e('Use the PHP mail function instead of the WordPress PHP mailer.', 'mcf'); ?></span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="postbox panel-options">
          <h2 class="hndle ui-sortable-handle"><?php esc_html_e('Styling', 'mcf'); ?></h2>
          <div class="inside">
            <div class="main">
              <table class="form-table">
                <tbody>
                  <tr>
                    <th scope="row"><label class="" for="mcf_options[phone]"><?php esc_html_e('Phone Number', 'mcf'); ?></label></th>
                    <td>
                      <input name="mcf_options[phone]" type="checkbox" value="1" <?php if (isset($mcf_options['phone'])) { checked('1', $mcf_options['phone']); } ?> />
                      <span class="mm-item-caption"><?php esc_html_e('Add a phone number field to the contact form.', 'mcf'); ?></span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          </div>
      </div>
    </div>
  <?php submit_button(); ?>
  </form>
</div>