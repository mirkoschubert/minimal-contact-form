<?php defined('ABSPATH') or die('No script kiddies please!'); ?>

<div id="dashboard_styling" class="postbox panel-styling">
  <div class="postbox-header">
    <h2 class="hndle ui-sortable-handle">
      <?php esc_html_e('Styling', 'mcf'); ?>
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
      <div id="accordion">
        <h3>Form Items</h3>
        <div class="accordion-panel">
          <table class="form-table items">
            <tbody>
              <?php
                $this->add_color_picker(
                  'styling',
                  'item-text-color',
                  __('Text Color', 'mcf')
                );
                $this->add_color_picker(
                  'styling',
                  'item-placeholder-color',
                  __('Placeholder Color', 'mcf')
                );
                $this->add_multi_color_picker(
                  'styling',
                  'item-background-color',
                  __('Background Color', 'mcf'),
                  array(
                    'normal' => __('Normal', 'mcf'),
                    'focus' => __('Focus', 'mcf')
                  )
                );
                $this->add_multi_color_picker(
                  'styling',
                  'item-border-color',
                  __('Border Color', 'mcf'),
                  array(
                    'normal' => __('Normal', 'mcf'),
                    'focus' => __('Focus', 'mcf')
                  )
                );
                $this->add_slider(
                  'styling',
                  'item-border-width',
                  __('Border Width', 'mcf'),
                  0,
                  5,
                  1,
                  'px'
                );
                $this->add_slider(
                  'styling',
                  'item-border-radius',
                  __('Border Radius', 'mcf'),
                  0,
                  2,
                  0.25,
                  'rem'
                );
                $this->add_toggle(
                  'styling',
                  'item-border-bottom',
                  __('Border Style', 'mcf'),
                  esc_html__('Only show the bottom line of the border.', 'mcf')
                );
                $this->add_slider(
                  'styling',
                  'item-padding',
                  __('Padding', 'mcf'),
                  0,
                  2,
                  0.25,
                  'rem'
                );
                $this->add_slider(
                  'styling',
                  'item-spacing',
                  __('Spacing', 'mcf'),
                  0,
                  2,
                  0.25,
                  'rem'
                );
                $this->add_slider(
                  'styling',
                  'item-font-size',
                  __('Font Size', 'mcf'),
                  0.5,
                  1.5,
                  0.125,
                  'em'
                );
                $this->add_toggle(
                  'styling',
                  'item-single-column',
                  __('Columns', 'mcf'),
                  esc_html__('Always show form elements in a single column.', 'mcf')
                );
                $this->add_toggle(
                  'styling',
                  'item-labels',
                  __('Labels', 'mcf'),
                  esc_html__("Show labels instead of placeholders", 'mcf'),
                );                
              ?>
            </tbody>
          </table>
        </div>
        <h3>Submit Button</h3>
        <div class="accordion-panel">
          <table class="form-table custom">
            <tbody>
              <?php
                $this->add_multi_color_picker(
                  'styling',
                  'button-text-color',
                  __('Text Color', 'mcf'),
                  array(
                    'normal' => __('Normal', 'mcf'),
                    'hover' => __('Hover', 'mcf')
                  )
                );
                $this->add_multi_color_picker(
                  'styling',
                  'button-background-color',
                  __('Background Color', 'mcf'),
                  array(
                    'normal' => __('Normal', 'mcf'),
                    'hover' => __('Hover', 'mcf')
                  )
                );
                $this->add_multi_color_picker(
                  'styling',
                  'button-border-color',
                  __('Border Color', 'mcf'),
                  array(
                    'normal' => __('Normal', 'mcf'),
                    'hover' => __('Hover', 'mcf')
                  )
                );
                $this->add_slider(
                  'styling',
                  'button-border-width',
                  __('Border Width', 'mcf'),
                  0,
                  5,
                  1,
                  'px'
                );
                $this->add_slider(
                  'styling',
                  'button-border-radius',
                  __('Border Radius', 'mcf'),
                  0,
                  2,
                  0.25,
                  'rem'
                );
                $this->add_toggle(
                  'styling',
                  'button-border-none',
                  __('Border Style', 'mcf'),
                  esc_html__("Don't show any border for the button.", 'mcf')
                );
                $this->add_slider(
                  'styling',
                  'button-padding',
                  __('Padding', 'mcf'),
                  0,
                  2,
                  0.25,
                  'rem'
                );
                $this->add_slider(
                  'styling',
                  'button-font-size',
                  __('Font Size', 'mcf'),
                  0.5,
                  1.5,
                  0.125,
                  'em'
                );
                $this->add_toggle(
                  'styling',
                  'button-alignment',
                  __('Alignment', 'mcf'),
                  esc_html__("Show the button left or right.", 'mcf'),
                  __('Right', 'mcf'),
                  __('Left', 'mcf')
                );
              ?>
            </tbody>
          </table>
        </div>
        <h3>Miscellaneous</h3>
        <div class="accordion-panel">
          <table class="form-table misc">
            <tbody>
              <?php
                $this->add_color_picker(
                  'styling',
                  'misc-notice-color',
                  __('Notice Color', 'mcf')
                );
                $this->add_color_picker(
                  'styling',
                  'misc-success-color',
                  __('Success Color', 'mcf')
                );
                $this->add_color_picker(
                  'styling',
                  'misc-warning-color',
                  __('Warning Color', 'mcf')
                );
                $this->add_color_picker(
                  'styling',
                  'misc-error-color',
                  __('Error Color', 'mcf')
                );
                $this->add_textfield(
                  'styling',
                  'misc-custom-css',
                  __('Custom CSS', 'mcf'),
                  esc_html__('Write your own CSS to alter the styling of the contact form.', 'mcf')
                );          
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="postbox-footer">
    <?php submit_button();?>
  </div>
</div>