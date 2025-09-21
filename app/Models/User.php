<?php

require_once __DIR__ . '/../Database.php';

class User {
    private static $db;

    public static function init() {
        if (!self::$db) {
            self::$db = new Database();
        }
    }

    public static function findByEmail($email) {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($username, $email, $password, $role = 'student') {
        self::init();
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = self::$db->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $passwordHash, $role]);
        return self::$db->lastInsertId();
    }

    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}