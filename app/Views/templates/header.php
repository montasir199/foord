<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Learning Platform'; ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="site-header">
        <nav class="main-nav">
            <ul>
                <li><a href="./">Home</a></li>
                <?php if (isset($_SESSION['user'])): ?>
                    <li><a href="courses">Courses</a></li>
                    <li><a href="quizzes">Quizzes</a></li>
                    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <li><a href="admin">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="login">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main class="main-content">