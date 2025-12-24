<?php

namespace MinimalContactForm\Services;

use MinimalContactForm\Core\Plugin;

class EmailService
{
    public function sendContactEmail(array $data): bool
    {
        $to = get_option('mcf_recipient_email', get_option('admin_email'));
        $subject = sprintf('[%s] %s', get_bloginfo('name'), $data['subject']);
        $message = $this->buildEmailMessage($data);
        $headers = $this->buildEmailHeaders($data);

        return wp_mail($to, $subject, $message, $headers);
    }

    private function buildEmailMessage(array $data): string
    {
        $message = sprintf(
            __('Name: %s', Plugin::TEXT_DOMAIN) . "\n",
            $data['name']
        );
        $message .= sprintf(
            __('Email: %s', Plugin::TEXT_DOMAIN) . "\n",
            $data['email']
        );
        $message .= sprintf(
            __('Subject: %s', Plugin::TEXT_DOMAIN) . "\n\n",
            $data['subject']
        );
        $message .= sprintf(
            __('Message:', Plugin::TEXT_DOMAIN) . "\n%s",
            $data['message']
        );

        return $message;
    }

    private function buildEmailHeaders(array $data): array
    {
        $headers = [];
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $headers[] = sprintf('From: %s <%s>', $data['name'], $data['email']);
        $headers[] = sprintf('Reply-To: %s', $data['email']);

        return $headers;
    }

    public function sendNotification(string $to, string $subject, string $message): bool
    {
        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        return wp_mail($to, $subject, $message, $headers);
    }
}
