<?php
require_once 'config/database.php';
require_once 'app/Database.php';
require_once 'app/Models/User.php';

$db = new Database();
$user = User::findByEmail('admin@example.com');
if ($user) {
    echo "User found: " . $user['username'] . "\n";
    $verify = User::verifyPassword('password', $user['password_hash']);
    echo "Password verify: " . ($verify ? 'true' : 'false') . "\n";
} else {
    echo "User not found\n";
}
?>