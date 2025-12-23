<?php defined('ABSPATH') or die('No script kiddies please!'); ?>

<div id="dashboard_options" class="postbox panel-options">
  <div class="postbox-header">
    <h2 class="hndle ui-sortable-handle">
      <?php esc_html_e('Settings', 'mcf'); ?>
    </h2>
    <div class="handle-actions hide-if-no-js">
      <button type="button" class="handlediv" aria-expanded="true">
        <span class="screen-reader-text">Toggle panel: <?php esc_html_e('Settings', 'mcf'); ?></span>
        <span class="toggle-indicator" aria-hidden="true"></span>
      </button>
    </div>
  </div>
  <div class="inside">
    <div class="main">
      <table class="form-table">
        <tbody>
          <?php
            $this->add_user_dropdown(
              'settings',
              'user',
              __('User', 'mcf'),
              esc_html__('Select the administrator or editor who should receive the emails.', 'mcf'),
            );
            $this->add_privacy_policy(
              __('Privacy Policy', 'mcf'),
              __('You have to set a privacy policy page in the %1$s!', 'mcf')
            );
            $this->add_toggle(
              'settings',
              'gdpr',
              __('GDPR', 'mcf'),
              esc_html__('Use an opt-in instead of just informing the visitor about the GDPR.', 'mcf')
            );
            $this->add_toggle(
              'settings',
              'spam',
              __('Antipam', 'mcf'),
              esc_html__('Use the honeypot mechanism to protect against SPAM emails.', 'mcf')
            );
            $this->add_toggle(
              'settings',
              'phpmail',
              __('Mail Service', 'mcf'),
              esc_html__('Use the PHP mail function instead of the WordPress PHP mailer.', 'mcf')
            );
          ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="postbox-footer">
    <?php submit_button();?>
  </div>
</div>