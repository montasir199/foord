<?php

require_once __DIR__ . '/../Database.php';

// فئة المحاولة
class Attempt {
    private static $db;

    // تهيئة قاعدة البيانات
    public static function init() {
        if (!self::$db) {
            self::$db = new Database();
        }
    }

    // إنشاء محاولة جديدة
    public static function create($user_id, $quiz_id) {
        self::init();
        $stmt = self::$db->prepare("INSERT INTO attempts (user_id, quiz_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $quiz_id]);
        return self::$db->lastInsertId();
    }

    // العثور على محاولة بالمعرف
    public static function findById($id) {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM attempts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // العثور على جميع المحاولات
    public static function findAll() {
        self::init();
        $stmt = self::$db->query("SELECT * FROM attempts ORDER BY started_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // تحديث محاولة
    public static function update($id, $data) {
        self::init();
        $setParts = [];
        $values = [];
        foreach ($data as $key => $value) {
            $setParts[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;
        $stmt = self::$db->prepare("UPDATE attempts SET " . implode(', ', $setParts) . " WHERE id = ?");
        $stmt->execute($values);
        return $stmt->rowCount();
    }

    // حذف محاولة
    public static function delete($id) {
        self::init();
        $stmt = self::$db->prepare("DELETE FROM attempts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    // إنشاء محاولة
    public static function createAttempt($user_id, $quiz_id) {
        return self::create($user_id, $quiz_id);
    }

    // تصحيح المحاولة
    public static function gradeAttempt($attempt_id, $answers) {
        self::init();
        // الحصول على المحاولة
        $attempt = self::findById($attempt_id);
        if (!$attempt) {
            return false;
        }
        $quiz_id = $attempt['quiz_id'];

        // الحصول على الأسئلة
        $questions = Question::findByQuiz($quiz_id);
        if (empty($questions)) {
            return false;
        }

        $total_questions = count($questions);
        $correct_answers = 0;

        foreach ($questions as $question) {
            $question_id = $question['id'];
            if (isset($answers[$question_id])) {
                $user_answer = $answers[$question_id];
                if (Question::gradeAnswer($question_id, $user_answer)) {
                    $correct_answers++;
                }
            }
        }

        $score = round(($correct_answers / $total_questions) * 100, 2);

        // تحديث المحاولة
        $stmt = self::$db->prepare("UPDATE attempts SET score = ?, completed_at = NOW() WHERE id = ?");
        $stmt->execute([$score, $attempt_id]);
        return $stmt->rowCount() > 0;
    }

    // العثور على المحاولات للمستخدم والاختبار
    public static function findByUserAndQuiz($user_id, $quiz_id) {
        self::init();
        $stmt = self::$db->prepare("SELECT * FROM attempts WHERE user_id = ? AND quiz_id = ? ORDER BY started_at DESC");
        $stmt->execute([$user_id, $quiz_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}