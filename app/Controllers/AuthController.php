<?php

session_start();

require_once __DIR__ . '/../Models/User.php';
require_once '../Security.php';

class AuthController {

    private static function sendJsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    private static function validatePassword($password) {
        return strlen($password) >= 6;
    }

    public static function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::sendJsonResponse(['error' => 'Method not allowed'], 405);
        }

        // Validate CSRF token
        if (!Security::validateCSRFToken()) {
            self::sendJsonResponse(['error' => 'Invalid CSRF token'], 403);
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        if (empty($email) || empty($password)) {
            self::sendJsonResponse(['error' => 'Email and password are required'], 400);
        }

        if (!self::validateEmail($email)) {
            self::sendJsonResponse(['error' => 'Invalid email format'], 400);
        }

        // Check rate limiting
        if (!Security::checkRateLimit($ip)) {
            self::sendJsonResponse(['error' => 'Too many login attempts. Please try again later.'], 429);
        }

        $user = User::findByEmail($email);
        if (!$user || !User::verifyPassword($password, $user['password_hash'])) {
            self::sendJsonResponse(['error' => 'Invalid credentials'], 401);
        }

        // Regenerate session ID for session fixation protection
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ];

        self::sendJsonResponse(['success' => true, 'message' => 'Login successful', 'user' => $_SESSION['user']]);
    }

    public static function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::sendJsonResponse(['error' => 'Method not allowed'], 405);
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($email) || empty($password)) {
            self::sendJsonResponse(['error' => 'Username, email, and password are required'], 400);
        }

        if (!self::validateEmail($email)) {
            self::sendJsonResponse(['error' => 'Invalid email format'], 400);
        }

        if (!self::validatePassword($password)) {
            self::sendJsonResponse(['error' => 'Password must be at least 6 characters long'], 400);
        }

        if (User::findByEmail($email)) {
            self::sendJsonResponse(['error' => 'Email already exists'], 409);
        }

        try {
            $userId = User::create($username, $email, $password);
            self::sendJsonResponse(['success' => true, 'message' => 'Registration successful', 'user_id' => $userId], 201);
        } catch (Exception $e) {
            self::sendJsonResponse(['error' => 'Registration failed: ' . $e->getMessage()], 500);
        }
    }

    public static function logout() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            self::sendJsonResponse(['error' => 'Method not allowed'], 405);
        }

        session_destroy();
        self::sendJsonResponse(['success' => true, 'message' => 'Logout successful']);
    }
}