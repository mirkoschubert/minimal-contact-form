<?php

namespace MinimalContactForm\Public;

use MinimalContactForm\Services\FormHandler as FormHandlerService;
use MinimalContactForm\Services\SecurityService;
use MinimalContactForm\Services\EmailService;

/**
 * Public Form Handler
 *
 * Handles AJAX form submissions and integrates with services.
 * Replaces the legacy mcf_ajax_send_mail function.
 *
 * @since 1.0.0
 */
class FormHandler
{
    /**
     * @var FormHandlerService
     */
    private $formHandler;

    /**
     * Constructor
     */
    public function __construct()
    {
        $security = new SecurityService();
        $email = new EmailService();
        $this->formHandler = new FormHandlerService($security, $email);
    }

    /**
     * Register hooks
     *
     * @since 1.0.0
     */
    public function register()
    {
        add_action('wp_ajax_mcf_submit_form', [$this, 'handle_submission']);
        add_action('wp_ajax_nopriv_mcf_submit_form', [$this, 'handle_submission']);
    }

    /**
     * Handle AJAX form submission
     *
     * @since 1.0.0
     */
    public function handle_submission()
    {
        // Process the submission using FormHandlerService
        $result = $this->formHandler->processSubmission($_POST);

        // Send JSON response
        if ($result['success']) {
            wp_send_json_success([
                'message' => $result['message']
            ]);
        } else {
            wp_send_json_error([
                'message' => $result['message']
            ]);
        }
    }
}
