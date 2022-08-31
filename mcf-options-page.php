<div id="mcf-plugin" class="wrap"> 
  <div id="icon-plugins" class="icon32"></div> 
  <h1><?php _e( 'Minimal Contact Form', 'mcf' ); ?> <small><?php echo 'v'. $mcf_version; ?></small></h1>
  <form action="options.php" method="POST">
  <?php settings_fields( 'mcf_plugin_options' ); ?>
    <div class="metabox-holder">
      <div class="meta-box-sortables ui-sortable">
        <div class="postbox panel-options">
          <div class="postbox-header">
            <h2 class="hndle ui-sortable-handle"><?php esc_html_e('Options', 'mcf'); ?></h2>
            <div class="handle-actions hide-if-no-js">
              <button type="button" class="handlediv" aria-expanded="true">
                <span class="screen-reader-text">Toggle panel: Options</span>
                <span class="toggle-indicator" aria-hidden="true"></span>
            </button>
            </div>
          </div>
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
          <div class="postbox-header">
            <h2 class="hndle ui-sortable-handle"><?php esc_html_e('Styling', 'mcf'); ?></h2>
            <div class="handle-actions hide-if-no-js">
              <button type="button" class="handlediv" aria-expanded="true">
                <span class="screen-reader-text">Toggle panel: Styling</span>
                <span class="toggle-indicator" aria-hidden="true"></span>
            </button>
            </div>
          </div>
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
                  <tr>
                    <th scope="row"><label class="" for="mcf_options[hidesubject]"><?php esc_html_e('Subject', 'mcf'); ?></label></th>
                    <td>
                      <input name="mcf_options[hidesubject]" type="checkbox" value="1" <?php if (isset($mcf_options['hidesubject'])) { checked('1', $mcf_options['hidesubject']); } ?> />
                      <span class="mm-item-caption"><?php esc_html_e('Hide the subject item.', 'mcf'); ?></span>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"><label class="" for="mcf_options[oneline]"><?php esc_html_e('Layout', 'mcf'); ?></label></th>
                    <td>
                      <input name="mcf_options[oneline]" type="checkbox" value="1" <?php if (isset($mcf_options['oneline'])) { checked('1', $mcf_options['oneline']); } ?> />
                      <span class="mm-item-caption"><?php esc_html_e('Show name and email in one line.', 'mcf'); ?></span>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"><label class="" for="mcf_options[labels]"><?php esc_html_e('Show Labels', 'mcf'); ?></label></th>
                    <td>
                      <input name="mcf_options[labels]" type="checkbox" value="1" <?php if (isset($mcf_options['labels'])) { checked('1', $mcf_options['labels']); } ?> />
                      <span class="mm-item-caption"><?php esc_html_e('Show labels instead of placeholders.', 'mcf'); ?></span>
                    </td>
                  </tr>
                  <tr>
                    <th scope="row"><label class="" for="mcf_options[labels]"><?php esc_html_e('Custom CSS', 'mcf'); ?></label></th>
                    <td>
                      <textarea name="mcf_options[css]" id="mcf-custom-css" rows="6"><?php if (isset($mcf_options['css'])) { esc_html_e($mcf_options['css']); } ?></textarea>
                      <p class="description"><?php esc_html_e('Write your own CSS to alter the styling of the contact form.', 'mcf'); ?></p>
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