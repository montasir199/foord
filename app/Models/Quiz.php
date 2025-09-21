<?php

require_once __DIR__ . '/../Database.php';

// فئة الاختبار
class Quiz {
    private static $db;

    // تهيئة قاعدة البيانات
    public static function init() {
        if (!self::$db) {
            self::$db = new Database();
        }
    }

    // إنشاء اختبار جديد
    public static function create($title, $lesson_id) {
        self::init();
        $stmt = self::$db->prepare("INSERT INTO quizzes (title, lesson_id) VALUES (?, ?)");
        $stmt->execute([$title, $lesson_id]);
        return self::$db->lastInsertId();
    }

    // العثور على اختبار بالمعرف
    public static function findById($id) {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM quizzes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // العثور على جميع الاختبارات
    public static function findAll() {
        self::init();
        $stmt = self::$db->query("SELECT * FROM quizzes ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // تحديث اختبار
    public static function update($id, $data) {
        self::init();
        $setParts = [];
        $values = [];
        foreach ($data as $key => $value) {
            $setParts[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $stmt = self::$db->prepare("UPDATE quizzes SET " . implode(', ', $setParts) . " WHERE id = ?");
        $stmt->execute($values);
        return $stmt->rowCount();
    }

    // حذف اختبار
    public static function delete($id) {
        self::init();
        $stmt = self::$db->prepare("DELETE FROM quizzes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    // العثور على الاختبارات بالدرس
    public static function findByLesson($lesson_id) {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM quizzes WHERE lesson_id = ? ORDER BY created_at DESC");
        $stmt->execute([$lesson_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // الحصول على الأسئلة لاختبار معين
    public static function getQuestions($quiz_id) {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id ASC");
        $stmt->execute([$quiz_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}