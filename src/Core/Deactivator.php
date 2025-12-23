<?php

namespace MinimalContactForm\Core;

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since 1.0.0
 */
class Deactivator
{
    /**
     * Deactivation hook.
     *
     * @since 1.0.0
     */
    public static function deactivate()
    {
        // Intentionally empty - we don't delete options on deactivation
        // Options are only deleted on uninstall
    }
}
