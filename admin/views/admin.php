<?php defined('ABSPATH') or die('No script kiddies please!');?>

<div id="mcf-plugin" class="wrap">
  <div id="icon-plugins" class="icon32"></div>
  <h1>
    <?php _e('Minimal Contact Form', 'mcf'); ?> <small>
      <?php echo 'v' . MCF_VERSION; ?>
    </small>
  </h1>
  <form action="options.php" method="POST">
    <?php settings_fields('mcf_options'); ?>
    <div id="dashboard-widgets-wrap">
      <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" class="postbox-container">
          <div id="normal-sortables" class="meta-box-sortables ui-sortable">
            <!-- Widget Row 1 -->
            
            <!-- Layout -->
            <?php require_once plugin_dir_path(__FILE__) . 'partial-layout.php'; ?>
            <!-- Options -->
            <?php require_once plugin_dir_path(__FILE__) . 'partial-settings.php'; ?>
            
          </div>
        </div>
        <div id="postbox-container-2" class="postbox-container">
          <div id="side-sortables" class="meta-box-sortables ui-sortable">
            <!-- Widget Row 2 -->
            
            <!-- Styling -->
            <?php require_once plugin_dir_path(__FILE__) . 'partial-styling.php'; ?>

          </div>
        </div>
      </div>
    </div>
  </form>
</div>
