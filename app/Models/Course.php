<?php

require_once __DIR__ . '/../Database.php';

// فئة الدورة التعليمية
class Course {
    private static $db;

    // تهيئة قاعدة البيانات
    public static function init() {
        if (!self::$db) {
            self::$db = new Database();
        }
    }

    // إنشاء دورة جديدة
    public static function create($title, $description, $level_id, $instructor_id) {
        self::init();
        $stmt = self::$db->prepare("INSERT INTO courses (title, description, level_id, instructor_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $description, $level_id, $instructor_id]);
        return self::$db->lastInsertId();
    }

    // العثور على دورة بالمعرف
    public static function findById($id) {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // العثور على جميع الدورات
    public static function findAll() {
        self::init();
        $stmt = self::$db->query("SELECT * FROM courses ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // تحديث دورة
    public static function update($id, $data) {
        self::init();
        $setParts = [];
        $values = [];
        foreach ($data as $key => $value) {
            $setParts[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $stmt = self::$db->prepare("UPDATE courses SET " . implode(', ', $setParts) . " WHERE id = ?");
        $stmt->execute($values);
        return $stmt->rowCount();
    }

    // حذف دورة
    public static function delete($id) {
        self::init();
        $stmt = self::$db->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    // العثور على الدورات بالمستوى
    public static function findByLevel($level_id) {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM courses WHERE level_id = ? ORDER BY created_at DESC");
        $stmt->execute([$level_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // الحصول على الدروس لدورة معينة
    public static function getLessons($course_id) {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM lessons WHERE course_id = ? ORDER BY order_number ASC");
        $stmt->execute([$course_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}