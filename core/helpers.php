<?php

/**
 * Gets a theme option by a specific ID.
 * @since 1.0.0
 */
function get_mcf_option($category, $id)
{
  $options = get_option('mcf_options');
  if (isset($options[$category]) && isset($options[$category][$id])) {
    return $options[$category][$id];
  }
}

  /**
   * HELPER: Sanitize Checkbox
   *
   * @since 1.0.0
   */
  function sanitize_checkbox($checked, $default) {
    // Boolean-Check 
    if (isset($checked)) {
      return true == $checked ? 1 : 0;
    } else {
      return $default;
    }
  }


  /**
   * HELPER: Sanitize Color
   *
   * @since 1.0.0
   */
  function sanitize_color($hex, $default) {

    return isset($hex) ? sanitize_hex_color($hex) : $default;
  }


  function sanitize_int($int, $default) {
    return isset($int) ? intval($int) : $default;
  }
  
  function sanitize_float($float, $default) {
    return isset($float) ? floatval($float) : $default;
  }

  function sanitize_textarea($content, $default) {
    return isset($content) ? sanitize_textarea_field($content) : $default;
  }