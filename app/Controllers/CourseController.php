<?php

session_start();

require_once __DIR__ . '/../Models/Course.php';
require_once __DIR__ . '/../Models/Lesson.php';
require_once '../Database.php';

class CourseController {

    private static function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    public static function index() {
        self::checkAuth();

        $db = new Database();
        $levels = $db->query("SELECT * FROM levels ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

        $coursesByLevel = [];
        foreach ($levels as $level) {
            $coursesByLevel[] = [
                'level' => $level,
                'courses' => Course::findByLevel($level['id'])
            ];
        }

        require_once '../Views/courses/list.php';
    }

    public static function viewCourse($course_id) {
        self::checkAuth();

        $course = Course::findById($course_id);
        if (!$course) {
            echo "Course not found";
            return;
        }

        $lessons = Course::getLessons($course_id);

        require_once '../Views/courses/view.php';
    }

    public static function viewLesson($lesson_id) {
        self::checkAuth();

        $lesson = Lesson::findById($lesson_id);
        if (!$lesson) {
            echo "Lesson not found";
            return;
        }

        require_once '../Views/courses/lesson.php';
    }
}