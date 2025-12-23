<?php

namespace MinimalContactForm\Public;

/**
 * The form handling functionality of the plugin.
 *
 * Temporary wrapper for the existing MCF_Form class.
 * Will be split into FormRenderer, FormHandler, and EmailService in Phase 5.
 *
 * @since 1.0.0
 */
class FormHandler extends \MCF_Form
{
    // Temporarily extends the old class to maintain functionality
    // This will be split into separate classes in Phase 5:
    // - FormRenderer (shortcode + HTML)
    // - FormHandler (REST API submission)
    // - EmailService (email sending)
}
