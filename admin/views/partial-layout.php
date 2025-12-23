<?php defined('ABSPATH') or die('No script kiddies please!'); ?>

<div id="dashboard_layout" class="postbox panel-layout">
  <div class="postbox-header">
    <h2 class="hndle ui-sortable-handle">
      <?php esc_html_e('Layout', 'mcf'); ?>
    </h2>
    <div class="handle-actions hide-if-no-js">
      <button type="button" class="handlediv" aria-expanded="true">
        <span class="screen-reader-text">Toggle panel: <?php esc_html_e('Styling', 'mcf'); ?></span>
        <span class="toggle-indicator" aria-hidden="true"></span>
      </button>
    </div>
  </div>
  <div class="inside">
    <div class="main">
      <p class="description">Please click every item you want to use!</p>
      <div class="form-layout">
        <div class="item company">
          <input type="checkbox" name="mcf_options[layout][company]" id="company" class="toggle" <?php echo $this->options['layout']['company'] == 1 ? 'checked' :  '' ?>>
          <label for="company">Company</label>
        </div>
        <div class="item first-name">
          <input type="checkbox" name="mcf_options[layout][first-name]" id="first-name" class="toggle" <?php echo $this->options['layout']['first-name'] == 1 ? 'checked' :  '' ?>>
          <label for="first-name">First Name<span class="required">*</span></label>
        </div>
        <div class="item last-name">
          <input type="checkbox" name="mcf_options[layout][last-name]" id="last-name" class="toggle" <?php echo $this->options['layout']['last-name'] == 1 ? 'checked' :  '' ?>>
          <label for="last-name">Last Name<span class="required">*</span></label>
        </div>
        <div class="item name">
          <input type="checkbox" name="mcf_options[layout][name]" id="name" class="toggle" <?php echo $this->options['layout']['name'] == 1 ? 'checked' :  '' ?>>
          <label for="name">Name<span class="required">*</span></label>
        </div>
        <div class="item phone">
          <input type="checkbox" name="mcf_options[layout][phone]" id="phone" class="toggle" <?php echo $this->options['layout']['phone'] == 1 ? 'checked' :  '' ?>>
          <label for="phone">Phone</label>
        </div>
        <div class="item email">
          <input type="checkbox" name="mcf_options[layout][email]" id="email" class="toggle" <?php echo $this->options['layout']['email'] == 1 ? 'checked' :  '' ?>>
          <label for="email">E-Mail<span class="required">*</span></label>
        </div>
        <div class="item subject">
          <input type="checkbox" name="mcf_options[layout][subject]" id="subject" class="toggle" <?php echo $this->options['layout']['subject'] == 1 ? 'checked' :  '' ?>>
          <label for="subject">Subject<span class="required">*</span></label>
        </div>
        <div class="item message">
          <input type="checkbox" name="mcf_options[layout][message]" id="message" class="toggle" <?php echo $this->options['layout']['message'] == 1 ? 'checked' :  '' ?>>
          <label for="message">Message<span class="required">*</span></label>
        </div>
        <div class="item privacy">
          <?php $privacy_url = get_permalink(get_option('wp_page_for_privacy_policy')); ?>
          <?php if ($privacy_url) : ?>
          <div class="with-checkbox">
            <input id="privacy" name="privacy" type="checkbox" disabled />
            <label class="privacy-caption" for="privacy"><?php echo __('I consent to having you process my submitted information so you can respond to my inquiry.', 'mcf') . ' ' . __('For further information please visit our', 'mcf') . ' <a href="' . $privacy_url . '" target="_blank">' . __('Privacy Policy', 'mcf') . '</a>.<span class="required">' . __('*', 'mcf') . '</span>'; ?></label>
          </div>
          <div class="without-checkbox">
            <p class="privacy"><?php echo __('Your submitted information will only be processed to respond to your inquiry.', 'mcf') . ' ' . __('For further information please visit our', 'mcf') . ' <a href="' . $privacy_url . '" target="_blank">' . __('Privacy Policy', 'mcf') . '</a>'; ?></p>
          </div>
          <?php else : ?>
          <div class="no-policy">
            <?php printf(__('You have to set a privacy policy page in the %1$s!', 'mcf'), '<a href="' . get_admin_url() . 'options-privacy.php">' . esc_html__('Privacy Settings', 'mcf') . '</a>'); ?>
          </div>
          <?php endif; ?>
        </div>
        <a class="button submit">Submit</a>
      </div>

    </div>
  </div>
  <div class="postbox-footer">
    <?php submit_button();?>
  </div>
</div>