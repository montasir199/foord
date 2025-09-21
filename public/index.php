<?php

// نقطة الدخول الرئيسية للتطبيق
require_once '../config/app.php';
require_once '../app/Database.php';

// تحليل الطلب
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];

// إزالة اسم السكريبت من الطلب
$basePath = str_replace('/index.php', '', $scriptName);
$path = str_replace($basePath, '', $requestUri);

// إزالة الاستعلامات
$path = parse_url($path, PHP_URL_PATH);

// توجيه الطلبات
switch ($path) {
    case '/':
    case '':
        // الصفحة الرئيسية
        $title = 'الصفحة الرئيسية';
        include '../app/Views/home.php';
        break;

    case '/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once '../app/Controllers/AuthController.php';
            AuthController::login();
        } else {
            $title = 'تسجيل الدخول';
            include '../app/Views/auth/login.php';
        }
        break;

    case '/register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once '../app/Controllers/AuthController.php';
            AuthController::register();
        } else {
            $title = 'التسجيل';
            include '../app/Views/auth/register.php';
        }
        break;

    case '/logout':
        require_once '../app/Controllers/AuthController.php';
        AuthController::logout();
        break;

    case '/courses':
        require_once '../app/Controllers/CourseController.php';
        CourseController::index();
        break;

    case '/admin':
        require_once '../app/Controllers/AdminController.php';
        AdminController::dashboard();
        break;

    default:
        // صفحة 404
        http_response_code(404);
        $title = 'صفحة غير موجودة';
        include '../app/Views/404.php';
        break;
}