<?php

// سكريبت اختبار قاعدة البيانات
// يتحقق من الاتصال وقاعدة البيانات واستعلامات أساسية

require_once '../config/database.php';
require_once '../app/Database.php';

echo "=== اختبار قاعدة البيانات ===\n\n";

try {
    // اختبار الاتصال
    echo "1. اختبار الاتصال بقاعدة البيانات...\n";
    $db = new Database();
    echo "   ✓ تم الاتصال بنجاح\n\n";

    // اختبار وجود الجداول
    echo "2. اختبار وجود الجداول...\n";
    $tables = ['users', 'levels', 'courses', 'lessons', 'standards', 'quizzes', 'questions', 'attempts', 'audit_log'];

    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "   ✓ الجدول '$table' موجود\n";
        } else {
            echo "   ✗ الجدول '$table' غير موجود\n";
        }
    }
    echo "\n";

    // اختبار استعلامات أساسية
    echo "3. اختبار الاستعلامات الأساسية...\n";

    // عدد المستخدمين
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   ✓ عدد المستخدمين: " . $result['count'] . "\n";

    // عدد الدورات
    $stmt = $db->query("SELECT COUNT(*) as count FROM courses");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   ✓ عدد الدورات: " . $result['count'] . "\n";

    // عدد الدروس
    $stmt = $db->query("SELECT COUNT(*) as count FROM lessons");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   ✓ عدد الدروس: " . $result['count'] . "\n";

    // عدد الاختبارات
    $stmt = $db->query("SELECT COUNT(*) as count FROM quizzes");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   ✓ عدد الاختبارات: " . $result['count'] . "\n";

    echo "\n";

    // اختبار إدراج بيانات تجريبية (اختياري)
    echo "4. اختبار إدراج بيانات تجريبية...\n";
    $db->beginTransaction();

    // إدراج مستوى تجريبي إذا لم يكن موجوداً
    $stmt = $db->query("SELECT COUNT(*) as count FROM levels");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result['count'] == 0) {
        $db->exec("INSERT INTO levels (name, description) VALUES ('المستوى الأول', 'مستوى تعليمي أساسي')");
        echo "   ✓ تم إدراج مستوى تجريبي\n";
    } else {
        echo "   ✓ المستويات موجودة بالفعل\n";
    }

    $db->commit();
    echo "\n";

    echo "=== تم الانتهاء من الاختبارات بنجاح ===\n";

} catch (PDOException $e) {
    echo "خطأ في قاعدة البيانات: " . $e->getMessage() . "\n";
    echo "تأكد من:\n";
    echo "- صحة إعدادات قاعدة البيانات في config/database.php\n";
    echo "- تشغيل ملف database_schema.sql\n";
    echo "- تشغيل خادم MySQL\n";
    exit(1);
} catch (Exception $e) {
    echo "خطأ عام: " . $e->getMessage() . "\n";
    exit(1);
}

?>