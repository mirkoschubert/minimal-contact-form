<?php

namespace MinimalContactForm\Services;

use MinimalContactForm\Core\Plugin;

class EmailService
{
    public function sendContactEmail(array $data): bool
    {
        $options = get_option('mcf_options');
        $settings = $options['settings'] ?? [];

        // Get recipient email (prefer new sender_email, fallback to user)
        $to = $this->getRecipientEmail($settings);

        $subject = sprintf('[%s] %s', get_bloginfo('name'), $data['subject'] ?? __('Contact Form Submission', 'mcf'));
        $message = $this->buildEmailMessage($data);
        $headers = $this->buildEmailHeaders($data, $settings);

        // Handle different mail services
        $mail_service = $settings['mail_service'] ?? 'wp_mail';

        switch ($mail_service) {
            case 'smtp':
                return $this->sendViaSMTP($to, $subject, $message, $headers, $settings['smtp_config'] ?? []);
            case 'php_mail':
                return $this->sendViaPHPMail($to, $subject, $message, $headers);
            default:
                return wp_mail($to, $subject, $message, $headers);
        }
    }

    private function getRecipientEmail($settings): string
    {
        // Prefer new sender_email field
        if (!empty($settings['sender_email'])) {
            return $settings['sender_email'];
        }

        // Fallback to user email (legacy)
        if (!empty($settings['recipient_user_id'])) {
            $user = get_user_by('id', $settings['recipient_user_id']);
            if ($user) {
                return $user->user_email;
            }
        }

        // Final fallback
        return get_option('admin_email');
    }

    private function buildEmailMessage(array $data): string
    {
        $message = '';

        if (!empty($data['name'])) {
            $message .= sprintf(__('Name: %s', 'mcf') . "\n", $data['name']);
        }
        if (!empty($data['company'])) {
            $message .= sprintf(__('Company: %s', 'mcf') . "\n", $data['company']);
        }
        if (!empty($data['email'])) {
            $message .= sprintf(__('Email: %s', 'mcf') . "\n", $data['email']);
        }
        if (!empty($data['phone'])) {
            $message .= sprintf(__('Phone: %s', 'mcf') . "\n", $data['phone']);
        }
        if (!empty($data['subject'])) {
            $message .= sprintf(__('Subject: %s', 'mcf') . "\n\n", $data['subject']);
        }

        $message .= sprintf(__('Message:', 'mcf') . "\n%s", $data['message'] ?? '');

        return $message;
    }

    private function buildEmailHeaders(array $data, array $settings): array
    {
        $headers = [];
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';

        // Use sender_name and sender_email if available
        $from_name = $settings['sender_name'] ?? get_bloginfo('name');
        $from_email = $settings['sender_email'] ?? get_option('admin_email');

        $headers[] = sprintf('From: %s <%s>', $from_name, $from_email);

        // Reply-To: prefer settings, then user's email from form
        if (!empty($settings['reply_to'])) {
            $headers[] = sprintf('Reply-To: %s', $settings['reply_to']);
        } elseif (!empty($data['email'])) {
            $headers[] = sprintf('Reply-To: %s', $data['email']);
        }

        return $headers;
    }

    private function sendViaSMTP($to, $subject, $message, $headers, $smtp_config): bool
    {
        // Use PHPMailer with SMTP
        add_action('phpmailer_init', function ($phpmailer) use ($smtp_config) {
            $phpmailer->isSMTP();
            $phpmailer->Host = $smtp_config['host'] ?? '';
            $phpmailer->SMTPAuth = true;
            $phpmailer->Username = $smtp_config['username'] ?? '';
            $phpmailer->Password = $smtp_config['password'] ?? '';
            $phpmailer->SMTPSecure = ($smtp_config['encryption'] ?? 'tls') === 'ssl' ? 'ssl' : 'tls';
            $phpmailer->Port = $smtp_config['port'] ?? 587;
        });

        return wp_mail($to, $subject, $message, $headers);
    }

    private function sendViaPHPMail($to, $subject, $message, $headers): bool
    {
        // Use native PHP mail() (deprecated)
        $headers_string = implode("\r\n", $headers);
        return mail($to, $subject, $message, $headers_string);
    }

    public function sendNotification(string $to, string $subject, string $message): bool
    {
        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        return wp_mail($to, $subject, $message, $headers);
    }
}
