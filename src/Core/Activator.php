<?php

namespace MinimalContactForm\Core;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since 1.0.0
 */
class Activator
{
    /**
     * Activation hook.
     *
     * Writes default options to database and runs migration if needed.
     *
     * @since 1.0.0
     */
    public static function activate()
    {
        $options = get_option('mcf_options');

        // Run migration for existing installations
        Migration::maybe_migrate();

        // If options still don't exist (fresh install), create default structure
        $options = get_option('mcf_options');
        if (false === $options) {
            add_option(
                'mcf_options',
                Defaults::get_options(),
                '',
                true
            );
        }
    }
}
