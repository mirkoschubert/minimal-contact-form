<?php

namespace MinimalContactForm\Core;

/**
 * Fired during plugin uninstall.
 *
 * This class defines all code necessary to run during the plugin's uninstall.
 *
 * @since 1.0.0
 */
class Uninstaller
{
    /**
     * Uninstall hook.
     *
     * Removes all plugin data from the database.
     *
     * @since 1.0.0
     */
    public static function uninstall()
    {
        // Delete plugin options
        delete_option('mcf_options');
        delete_option('mcf_options_backup_v0');

        // Note: We don't delete user submissions here if we add that feature later
        // That would require explicit user confirmation
    }
}
