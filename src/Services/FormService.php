<?php

namespace MinimalContactForm\Services;


// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}
use MinimalContactForm\Core\Plugin;

class FormService
{
    private SecurityService $security;
    private EmailService $email;

    public function __construct(SecurityService $security, EmailService $email)
    {
        $this->security = $security;
        $this->email = $email;
    }

    public function processSubmission(array $postData): array
    {
        // Verify nonce
        if (!$this->security->verifyNonce($postData['_wpnonce'] ?? '', 'mcf_submit')) {
            return [
                'success' => false,
                'message' => __('Security verification failed. Please try again.', Plugin::TEXT_DOMAIN)
            ];
        }

        // Validate and sanitize input
        $validation = $this->validateInput($postData);
        if (!$validation['valid']) {
            return [
                'success' => false,
                'message' => $validation['message']
            ];
        }

        $data = $validation['data'];

        // Check honeypot fields
        if (!empty($postData['website']) || !empty($postData['url']) || !empty($postData['business_email'])) {
            return [
                'success' => false,
                'message' => __('Spam detected.', Plugin::TEXT_DOMAIN)
            ];
        }

        // Rate limiting
        if (!$this->security->checkRateLimit()) {
            return [
                'success' => false,
                'message' => __('Too many submissions. Please try again later.', Plugin::TEXT_DOMAIN)
            ];
        }

        // Send email
        $emailSent = $this->email->sendContactEmail($data);

        if (!$emailSent) {
            return [
                'success' => false,
                'message' => __('Failed to send message. Please try again later.', Plugin::TEXT_DOMAIN)
            ];
        }

        // Log submission
        $this->logSubmission($data);

        return [
            'success' => true,
            'message' => get_option('mcf_success_message', __('Thank you for your message!', Plugin::TEXT_DOMAIN))
        ];
    }

    private function validateInput(array $postData): array
    {
        $errors = [];

        // Name validation (handle both single and split name modes)
        $name = '';
        if (!empty($postData['name'])) {
            $name = sanitize_text_field($postData['name']);
        } elseif (!empty($postData['first-name']) && !empty($postData['last-name'])) {
            $name = sanitize_text_field($postData['first-name']) . ' ' . sanitize_text_field($postData['last-name']);
        }

        if (empty($name)) {
            $errors[] = __('Name is required.', Plugin::TEXT_DOMAIN);
        }

        // Email validation
        $email = sanitize_email($postData['email'] ?? '');
        if (empty($email) || !is_email($email)) {
            $errors[] = __('Valid email is required.', Plugin::TEXT_DOMAIN);
        }

        // Subject validation
        $subject = sanitize_text_field($postData['subject'] ?? '');
        if (empty($subject)) {
            $errors[] = __('Subject is required.', Plugin::TEXT_DOMAIN);
        }

        // Message validation
        $message = sanitize_textarea_field($postData['message'] ?? '');
        if (empty($message)) {
            $errors[] = __('Message is required.', Plugin::TEXT_DOMAIN);
        }

        if (!empty($errors)) {
            return [
                'valid' => false,
                'message' => implode(' ', $errors)
            ];
        }

        return [
            'valid' => true,
            'data' => [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message
            ]
        ];
    }

    private function logSubmission(array $data): void
    {
        if (get_option('mcf_enable_logging', false)) {
            global $wpdb;
            $table = $wpdb->prefix . 'mcf_submissions';

            $wpdb->insert(
                $table,
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'subject' => $data['subject'],
                    'message' => $data['message'],
                    'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                    'submitted_at' => current_time('mysql')
                ],
                ['%s', '%s', '%s', '%s', '%s', '%s', '%s']
            );
        }
    }
}
