<?php

class Security {

    private static $rateLimitFile = '../storage/login_attempts.json';

    /**
     * Generate a CSRF token and store it in session
     */
    public static function generateCSRFToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validate CSRF token from POST data
     */
    public static function validateCSRFToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $token = $_POST['csrf_token'] ?? '';
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            return false;
        }
        // Invalidate token after use
        unset($_SESSION['csrf_token']);
        return true;
    }

    /**
     * Check rate limiting for login attempts (max 5 per hour per IP)
     */
    public static function checkRateLimit($ip) {
        $attempts = self::getLoginAttempts();
        $currentTime = time();
        $oneHourAgo = $currentTime - 3600;

        // Clean old attempts
        if (isset($attempts[$ip])) {
            $attempts[$ip] = array_filter($attempts[$ip], function($timestamp) use ($oneHourAgo) {
                return $timestamp > $oneHourAgo;
            });
        }

        // Check if under limit
        if (count($attempts[$ip] ?? []) >= 5) {
            return false; // Rate limited
        }

        // Add new attempt
        $attempts[$ip][] = $currentTime;
        self::saveLoginAttempts($attempts);
        return true;
    }

    private static function getLoginAttempts() {
        if (!file_exists(self::$rateLimitFile)) {
            return [];
        }
        $data = json_decode(file_get_contents(self::$rateLimitFile), true);
        return $data ?: [];
    }

    private static function saveLoginAttempts($attempts) {
        file_put_contents(self::$rateLimitFile, json_encode($attempts));
    }
}