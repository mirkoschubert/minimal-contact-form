<?php
/*
Plugin Name:  Minimal Contact Form
Plugin URI:   https://github.com/mirkoschubert/minimal-contact-form
Description:  A WordPress Plugin for a simple, clean and secure contact form.
Version:      0.1.0
Author:       Mirko Schubert
Author URI:   https://mirkoschubert.de/
License:      MIT
License URI:  https://tldrlegal.com/license/mit-license
Text Domain:  mcf
Domain Path:  /languages

MIT License (MIT)

Copyright (c) 2018-present Mirko Schubert <mailto:office@mirkoschubert.de> (https://mirkoschubert.de)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

// Disable direct access
if (!defined( 'ABSPATH' )) exit();

$mcf_wp_version = '4.1';
$mcf_version = '0.2.0';
$mcf_plugin  = esc_html__('Minimal Contact Form', 'mcf');
$mcf_options = get_option('scf_options');
$mcf_path    = plugin_basename(__FILE__);


/**
 * Loads the text domain of the plugin
 * @since 0.1.0
 */
if (!function_exists('mcf_init')) {
  function mcf_init() {
    load_plugin_textdomain('mcf', false, dirname(plugin_basename(__FILE__)) .'/languages/');
  }
  add_action( 'plugins_loaded', 'mcf_init' );
}


/**
 * Enqueues plugin frontend scripts
 * @since 0.1.0
 */
if (!function_exists('mcf_scripts')) {
  function mcf_scripts() {
    if(!is_admin())	{
      wp_enqueue_style('mcf-style', plugins_url('/css/style.css',__FILE__));
    }
  }
  add_action('wp_enqueue_scripts', 'mcf_scripts');
}


/**
 * Enqueues plugin admin scripts
 * @since 0.2.0
 */
if (!function_exists('mcf_admin_scripts')) {
  function mcf_admin_scripts($hook) {
    if($hook !== 'settings_page_mcf') return;
    wp_enqueue_style('mcf-admin-style', plugins_url('/css/admin.css',__FILE__));
  }
  add_action('admin_enqueue_scripts', 'mcf_admin_scripts');
}


include 'mcf-options.php';

?>