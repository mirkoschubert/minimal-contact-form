<?php

namespace MinimalContactForm\Services;


// Prevent direct file access
if (!defined('ABSPATH')) {
    exit;
}
class SecurityService
{
    private const RATE_LIMIT_KEY = 'mcf_rate_limit_';
    private const RATE_LIMIT_ATTEMPTS = 3;
    private const RATE_LIMIT_WINDOW = 3600; // 1 hour in seconds

    public function verifyNonce(string $nonce, string $action): bool
    {
        return wp_verify_nonce($nonce, $action) !== false;
    }

    public function createNonce(string $action): string
    {
        return wp_create_nonce($action);
    }

    public function checkRateLimit(): bool
    {
        $ip = $this->getClientIp();
        $key = self::RATE_LIMIT_KEY . md5($ip);
        $attempts = get_transient($key);

        if ($attempts === false) {
            // First attempt
            set_transient($key, 1, self::RATE_LIMIT_WINDOW);
            return true;
        }

        if ($attempts >= self::RATE_LIMIT_ATTEMPTS) {
            return false;
        }

        // Increment attempts
        set_transient($key, $attempts + 1, self::RATE_LIMIT_WINDOW);
        return true;
    }

    public function sanitizeInput(string $input, string $type = 'text'): string
    {
        switch ($type) {
            case 'email':
                return sanitize_email($input);
            case 'textarea':
                return sanitize_textarea_field($input);
            case 'url':
                return esc_url_raw($input);
            case 'text':
            default:
                return sanitize_text_field($input);
        }
    }

    public function escapeOutput(string $output, string $context = 'html'): string
    {
        switch ($context) {
            case 'attr':
                return esc_attr($output);
            case 'url':
                return esc_url($output);
            case 'js':
                return esc_js($output);
            case 'textarea':
                return esc_textarea($output);
            case 'html':
            default:
                return esc_html($output);
        }
    }

    private function getClientIp(): string
    {
        $ip = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        }

        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '';
    }

    public function checkHoneypot(string $value): bool
    {
        return empty($value);
    }

    public function isBlockedIp(string $ip): bool
    {
        $blockedIps = get_option('mcf_blocked_ips', []);
        return in_array($ip, $blockedIps, true);
    }

    public function blockIp(string $ip): bool
    {
        $blockedIps = get_option('mcf_blocked_ips', []);

        if (!in_array($ip, $blockedIps, true)) {
            $blockedIps[] = $ip;
            return update_option('mcf_blocked_ips', $blockedIps);
        }

        return true;
    }

    public function unblockIp(string $ip): bool
    {
        $blockedIps = get_option('mcf_blocked_ips', []);
        $key = array_search($ip, $blockedIps, true);

        if ($key !== false) {
            unset($blockedIps[$key]);
            return update_option('mcf_blocked_ips', array_values($blockedIps));
        }

        return true;
    }
}
