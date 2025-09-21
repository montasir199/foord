<?php

require_once __DIR__ . '/../Database.php';

// فئة الدرس
class Lesson {
    private static $db;

    // تهيئة قاعدة البيانات
    public static function init() {
        if (!self::$db) {
            self::$db = new Database();
        }
    }

    // إنشاء درس جديد
    public static function create($title, $content, $course_id, $order_number) {
        self::init();
        $stmt = self::$db->prepare("INSERT INTO lessons (title, content, course_id, order_number) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $content, $course_id, $order_number]);
        return self::$db->lastInsertId();
    }

    // العثور على درس بالمعرف
    public static function findById($id) {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM lessons WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // العثور على جميع الدروس
    public static function findAll() {
        self::init();
        $stmt = self::$db->query("SELECT * FROM lessons ORDER BY course_id, order_number ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // تحديث درس
    public static function update($id, $data) {
        self::init();
        $setParts = [];
        $values = [];
        foreach ($data as $key => $value) {
            $setParts[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $stmt = self::$db->prepare("UPDATE lessons SET " . implode(', ', $setParts) . " WHERE id = ?");
        $stmt->execute($values);
        return $stmt->rowCount();
    }

    // حذف درس
    public static function delete($id) {
        self::init();
        $stmt = self::$db->prepare("DELETE FROM lessons WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    // العثور على الدروس بالدورة
    public static function findByCourse($course_id) {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY order_number ASC");
        $stmt->execute([$course_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // الحصول على محتوى الدرس
    public static function getContent($id) {
        self::init();
        $stmt = self::$db->prepare("SELECT content FROM lessons WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['content'] : null;
    }
}