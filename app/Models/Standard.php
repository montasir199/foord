<?php

require_once __DIR__ . '/../Database.php';

// فئة المعيار
class Standard {
    private static $db;

    // تهيئة قاعدة البيانات
    public static function init() {
        if (!self::$db) {
            self::$db = new Database();
        }
    }

    // إنشاء معيار جديد
    public static function create($name, $description, $lesson_id) {
        self::init();
        $stmt = self::$db->prepare("INSERT INTO standards (name, description, lesson_id) VALUES (?, ?, ?)");
        $stmt->execute([$name, $description, $lesson_id]);
        return self::$db->lastInsertId();
    }

    // العثور على معيار بالمعرف
    public static function findById($id) {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM standards WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // العثور على جميع المعايير
    public static function findAll() {
        self::init();
        $stmt = self::$db->query("SELECT * FROM standards ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // تحديث معيار
    public static function update($id, $data) {
        self::init();
        $setParts = [];
        $values = [];
        foreach ($data as $key => $value) {
            $setParts[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $stmt = self::$db->prepare("UPDATE standards SET " . implode(', ', $setParts) . " WHERE id = ?");
        $stmt->execute($values);
        return $stmt->rowCount();
    }

    // حذف معيار
    public static function delete($id) {
        self::init();
        $stmt = self::$db->prepare("DELETE FROM standards WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    // العثور على المعايير بالاختصاص (jurisdiction) - افتراض البحث في الاسم
    public static function findByJurisdiction($jurisdiction) {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM standards WHERE name LIKE ? ORDER BY created_at DESC");
        $stmt->execute(['%' . $jurisdiction . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // تحديث تاريخ آخر فحص
    public static function updateLastCheckedAt($id, $last_checked_at) {
        self::init();
        $stmt = self::$db->prepare("UPDATE standards SET last_checked_at = ? WHERE id = ?");
        $stmt->execute([$last_checked_at, $id]);
        return $stmt->rowCount();
    }
}