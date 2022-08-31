<?php
/*
Plugin Name:  Minimal Contact Form
Plugin URI:   https://github.com/mirkoschubert/minimal-contact-form
Description:  A WordPress Plugin for a simple, clean and secure contact form.
Version:      0.9.0
Author:       Mirko Schubert
Author URI:   https://mirkoschubert.de/
License:      GPL 3.0
License URI:  https://tldrlegal.com/license/gnu-general-public-license-v3-(gpl-3)
Text Domain:  mcf
Domain Path:  /languages

Minimal Contact Form is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Minimal Contact Form is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Minimal Contact Form. If not, see https://github.com/mirkoschubert/minimal-contact-form/blob/master/license.txt.
*/

// Disable direct access
if (!defined( 'ABSPATH' )) exit();

$mcf_wp_version = '4.9.6';
$mcf_version = '0.9.0';
$mcf_plugin  = esc_html__('Minimal Contact Form', 'mcf');
$mcf_slug = dirname(plugin_basename(__FILE__));
$mcf_path    = plugin_basename(__FILE__);
$mcf_options = get_option('mcf_options');

include 'mcf-options.php';
include 'mcf-form.php';


/**
 * Activation Hook
 * @since 0.3.0
 */
function mcf_plugin_activation() {
  
  // Write default options to database
  add_option( 'mcf_options', array(
    'user' => 1,
    'gdpr' => 0,
    'spam' => 1,
    'phpmail' => 0,
    'phone' => 0,
    'hidesubject' => 0,
    'oneline' => 0,
    'labels' => 0,
    'css' => '#minimal-contact-form {}'), '', 'yes');
}
register_activation_hook( __FILE__, 'mcf_plugin_activation' );


/**
 * Deactivation Hook
 * @since 0.3.0
 */
function mcf_plugin_deactivation() {

  // FOR TESTING: delete options from database
  //delete_option('mcf_options');
  
}
register_deactivation_hook( __FILE__, 'mcf_plugin_deactivation' );


/**
 * Uninstallation Hook
 * @since 0.3.0
 */
function mcf_plugin_uninstall() {

  // delete options from database
  delete_option('mcf_options');
}
register_uninstall_hook (__FILE__, 'mcf_plugin_uninstall');


/**
 * Loads the text domain of the plugin
 * @since 0.1.0
 */
function mcf_init() {
  global $mcf_slug;
  load_plugin_textdomain('mcf', false, $mcf_slug .'/languages/');
}
add_action( 'plugins_loaded', 'mcf_init' );


/**
 * Checks the version of WordPress and deactivates the Plugin when necessary
 * @since 0.6.1
 */
function mcf_check_version() {
  global $pagenow, $mcf_wp_version, $mcf_path, $mcf_slug, $mcf_plugin;

  $wp_version = get_bloginfo('version');

  if ($pagenow === 'plugins.php') {
    if (is_plugin_active($mcf_path) && version_compare($wp_version, $mcf_wp_version, '<')) {
      add_action('admin_notices', 'mcf_version__error');
    }
  } else if ($pagenow === 'options-general.php' && isset($_GET['page'])) {
    if ($_GET['page'] === $mcf_slug && is_plugin_active($mcf_path) && version_compare($wp_version, $mcf_wp_version, '<')) {
      add_action('admin_notices', 'mcf_version__error');
    }
  }
}
add_action('admin_init', 'mcf_check_version');


/**
 * Error message for version check
 * @since 0.6.1
 */
function mcf_version__error() {
  global $mcf_plugin, $mcf_wp_version;
  $class = 'notice notice-error';
  $msg  = "<strong>$mcf_plugin</strong>";
  $msg .= ' ' . esc_html__('requires WordPress', 'mcf') . ' ' . $mcf_wp_version;
  $msg .= ' ' . esc_html__('or higher!', 'mcf');
  $msg .= ' ' . esc_html__('Please upgrade WordPress and try again.', 'mcf');

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $msg ); 
}


/**
 * Enqueues plugin frontend scripts
 * @since 0.1.0
 */
function mcf_scripts() {
  global $mcf_options;

  if(!is_admin())	{
    wp_enqueue_script('jquery');
    wp_enqueue_script('mcf-script', plugins_url( '/js/mcf-script.js', __FILE__ ), 'jquery', true);
    wp_enqueue_style('mcf-style', plugins_url('/css/style.css',__FILE__));
    wp_localize_script( 'mcf-script', 'minimal_contact_form', array( 'mcf_ajaxurl' => admin_url( 'admin-ajax.php')));
    
    wp_add_inline_style('mcf-style', isset($mcf_options['css']) ? $mcf_options['css'] . "\r\n" : '');
  }
}
add_action('wp_enqueue_scripts', 'mcf_scripts');



/**
 * Enqueues plugin admin scripts
 * @since 0.2.0
 */
function mcf_admin_scripts($hook) {
  global $mcf_slug;

  if($hook !== 'settings_page_' . $mcf_slug) return;
  wp_enqueue_style('mcf-admin-style', plugins_url('/css/admin.css',__FILE__));
}
add_action('admin_enqueue_scripts', 'mcf_admin_scripts');


/**
 * Sets a link to the settings page in the plugin list
 * @since 0.3.0
 */
function mcf_plugin_action_links($links, $file) {
	
	global $mcf_path, $mcf_slug;
	
	if ($file == $mcf_path) array_unshift($links, '<a href="'. get_admin_url() .'options-general.php?page='. $mcf_slug .'">'. esc_html__('Settings', 'mcf') .'</a>');
	return $links;
}
add_filter ('plugin_action_links', 'mcf_plugin_action_links', 10, 2);
