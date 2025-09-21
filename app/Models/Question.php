<?php

require_once __DIR__ . '/../Database.php';

// فئة السؤال
class Question {
    private static $db;

    // تهيئة قاعدة البيانات
    public static function init() {
        if (!self::$db) {
            self::$db = new Database();
        }
    }

    // إنشاء سؤال جديد
    public static function create($quiz_id, $question_text, $question_type, $options, $correct_answer) {
        self::init();
        $optionsJson = $options ? json_encode($options) : null;
        $stmt = self::$db->prepare("INSERT INTO questions (quiz_id, question_text, question_type, options, correct_answer) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$quiz_id, $question_text, $question_type, $optionsJson, $correct_answer]);
        return self::$db->lastInsertId();
    }

    // العثور على سؤال بالمعرف
    public static function findById($id) {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM questions WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && $result['options']) {
            $result['options'] = json_decode($result['options'], true);
        }
        return $result;
    }

    // العثور على جميع الأسئلة
    public static function findAll() {
        self::init();
        $stmt = self::$db->query("SELECT * FROM questions ORDER BY quiz_id, id ASC");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as &$result) {
            if ($result['options']) {
                $result['options'] = json_decode($result['options'], true);
            }
        }
        return $results;
    }

    // تحديث سؤال
    public static function update($id, $data) {
        self::init();
        if (isset($data['options']) && is_array($data['options'])) {
            $data['options'] = json_encode($data['options']);
        }
        $setParts = [];
        $values = [];
        foreach ($data as $key => $value) {
            $setParts[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $stmt = self::$db->prepare("UPDATE questions SET " . implode(', ', $setParts) . " WHERE id = ?");
        $stmt->execute($values);
        return $stmt->rowCount();
    }

    // حذف سؤال
    public static function delete($id) {
        self::init();
        $stmt = self::$db->prepare("DELETE FROM questions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    // العثور على الأسئلة بالاختبار
    public static function findByQuiz($quiz_id) {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id ASC");
        $stmt->execute([$quiz_id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as &$result) {
            if ($result['options']) {
                $result['options'] = json_decode($result['options'], true);
            }
        }
        return $results;
    }

    // منطق التصحيح
    public static function gradeAnswer($question_id, $user_answer) {
        self::init();
        $stmt = self::$db->prepare("SELECT correct_answer FROM questions WHERE id = ?");
        $stmt->execute([$question_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return strtolower(trim($user_answer)) === strtolower(trim($result['correct_answer']));
        }
        return false;
    }
}