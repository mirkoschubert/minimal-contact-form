<?php
/**
 * Plugin Name: Minimal Contact Form
 * Plugin URI: https://github.com/mirkoschubert/minimal-contact-form
 * Description: A WordPress Plugin for a simple, clean and secure contact form.
 * Version: 1.0.0
 * Author: Mirko Schubert
 * Author URI: https://mirkoschubert.de/
 * License: LGPL-3.0-or-later
 * License URI: https://www.gnu.org/licenses/lgpl-3.0.html
 * Text Domain: mcf
 * Domain Path: /languages
 */

/*
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
if (!defined('ABSPATH')) {
    exit();
}

define('MCF_VERSION', '1.0.0');
define('MCF_PLUGIN_URL', plugin_dir_url(__FILE__));

// Composer autoloader
if (file_exists(plugin_dir_path(__FILE__) . 'vendor/autoload.php')) {
    require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
}

use MinimalContactForm\Core\Plugin;
use MinimalContactForm\Core\Activator;
use MinimalContactForm\Core\Deactivator;
use MinimalContactForm\Core\Uninstaller;

/**
 * Register activation hook
 *
 * @since 1.0.0
 */
register_activation_hook(__FILE__, [Activator::class, 'activate']);

/**
 * Register deactivation hook
 *
 * @since 1.0.0
 */
register_deactivation_hook(__FILE__, [Deactivator::class, 'deactivate']);

/**
 * Register uninstall hook
 *
 * @since 1.0.0
 */
register_uninstall_hook(__FILE__, [Uninstaller::class, 'uninstall']);

/**
 * Initialize and run the plugin
 *
 * @since 1.0.0
 */
(new Plugin())->run();
