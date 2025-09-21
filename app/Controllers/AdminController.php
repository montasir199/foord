<?php

session_start();

require_once __DIR__ . '/../Models/Course.php';
require_once __DIR__ . '/../Models/Lesson.php';
require_once __DIR__ . '/../Models/Standard.php';
require_once '../Database.php';
require_once '../Security.php';

class AdminController {

    private static function checkAdmin() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
    }

    public static function dashboard() {
        self::checkAdmin();

        $db = new Database();
        $courseCount = $db->query("SELECT COUNT(*) as count FROM courses")->fetch(PDO::FETCH_ASSOC)['count'];
        $lessonCount = $db->query("SELECT COUNT(*) as count FROM lessons")->fetch(PDO::FETCH_ASSOC)['count'];
        $standardCount = $db->query("SELECT COUNT(*) as count FROM standards")->fetch(PDO::FETCH_ASSOC)['count'];

        require_once '../Views/admin/dashboard.php';
    }

    // Courses
    public static function listCourses() {
        self::checkAdmin();

        $courses = Course::findAll();
        $db = new Database();
        $levels = $db->query("SELECT * FROM levels ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

        require_once '../Views/admin/courses.php';
    }

    public static function addCourse() {
        self::checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCSRFToken()) {
                echo "Invalid CSRF token";
                exit;
            }
            $title = $_POST['title'];
            $description = $_POST['description'];
            $level_id = $_POST['level_id'];
            $instructor_id = $_SESSION['user_id'];

            Course::create($title, $description, $level_id, $instructor_id);
            header('Location: /admin/courses');
            exit;
        }

        $db = new Database();
        $levels = $db->query("SELECT * FROM levels ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

        require_once '../Views/admin/courses.php';
    }

    public static function editCourse($id) {
        self::checkAdmin();

        $course = Course::findById($id);
        if (!$course) {
            echo "Course not found";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCSRFToken()) {
                echo "Invalid CSRF token";
                exit;
            }
            $data = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'level_id' => $_POST['level_id']
            ];
            Course::update($id, $data);
            header('Location: /admin/courses');
            exit;
        }

        $db = new Database();
        $levels = $db->query("SELECT * FROM levels ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

        require_once '../Views/admin/courses.php';
    }

    public static function deleteCourse($id) {
        self::checkAdmin();

        Course::delete($id);
        header('Location: /admin/courses');
        exit;
    }

    // Lessons
    public static function listLessons($courseId = null) {
        self::checkAdmin();

        if ($courseId) {
            $lessons = Lesson::findByCourse($courseId);
            $course = Course::findById($courseId);
        } else {
            $lessons = Lesson::findAll();
            $course = null;
        }
        $courses = Course::findAll();

        require_once '../Views/admin/lessons.php';
    }

    public static function addLesson() {
        self::checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCSRFToken()) {
                echo "Invalid CSRF token";
                exit;
            }
            $title = $_POST['title'];
            $content = $_POST['content'];
            $course_id = $_POST['course_id'];
            $order_number = $_POST['order_number'];

            Lesson::create($title, $content, $course_id, $order_number);
            header('Location: /admin/lessons?course=' . $course_id);
            exit;
        }

        $courses = Course::findAll();

        require_once '../Views/admin/lessons.php';
    }

    public static function editLesson($id) {
        self::checkAdmin();

        $lesson = Lesson::findById($id);
        if (!$lesson) {
            echo "Lesson not found";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCSRFToken()) {
                echo "Invalid CSRF token";
                exit;
            }
            $data = [
                'title' => $_POST['title'],
                'content' => $_POST['content'],
                'course_id' => $_POST['course_id'],
                'order_number' => $_POST['order_number']
            ];
            Lesson::update($id, $data);
            header('Location: /admin/lessons?course=' . $_POST['course_id']);
            exit;
        }

        $courses = Course::findAll();

        require_once '../Views/admin/lessons.php';
    }

    public static function deleteLesson($id) {
        self::checkAdmin();

        $lesson = Lesson::findById($id);
        $courseId = $lesson['course_id'];
        Lesson::delete($id);
        header('Location: /admin/lessons?course=' . $courseId);
        exit;
    }

    // Standards
    public static function listStandards() {
        self::checkAdmin();

        $standards = Standard::findAll();
        $lessons = Lesson::findAll();

        require_once '../Views/admin/standards.php';
    }

    public static function addStandard() {
        self::checkAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCSRFToken()) {
                echo "Invalid CSRF token";
                exit;
            }
            $name = $_POST['name'];
            $description = $_POST['description'];
            $lesson_id = $_POST['lesson_id'];

            Standard::create($name, $description, $lesson_id);
            header('Location: /admin/standards');
            exit;
        }

        $lessons = Lesson::findAll();

        require_once '../Views/admin/standards.php';
    }

    public static function editStandard($id) {
        self::checkAdmin();

        $standard = Standard::findById($id);
        if (!$standard) {
            echo "Standard not found";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCSRFToken()) {
                echo "Invalid CSRF token";
                exit;
            }
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'lesson_id' => $_POST['lesson_id']
            ];
            Standard::update($id, $data);
            header('Location: /admin/standards');
            exit;
        }

        $lessons = Lesson::findAll();

        require_once '../Views/admin/standards.php';
    }

    public static function deleteStandard($id) {
        self::checkAdmin();

        Standard::delete($id);
        header('Location: /admin/standards');
        exit;
    }

    // Check for updates from IFRS and FASB
    public static function checkForUpdates() {
        $logFile = '../storage/logs/standards_updates.log';
        $logMessage = date('Y-m-d H:i:s') . " - Starting standards update check.\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);

        $urls = [
            'IFRS' => 'https://www.ifrs.org/small-and-medium-sized-entities/',
            'FASB' => 'https://www.fasb.org/page/PageContent?pageId=/standards/authoritative-standards/asu.html'
        ];

        $db = new Database();
        $updated = false;

        foreach ($urls as $source => $url) {
            $content = self::fetchUrl($url);
            if ($content) {
                $parsedUpdates = self::parseUpdates($content, $source);
                if (!empty($parsedUpdates)) {
                    foreach ($parsedUpdates as $update) {
                        // Assume lesson_id = 1 for new standards, or find existing
                        $lesson_id = 1; // Default lesson
                        Standard::create($update['name'], $update['description'], $lesson_id);
                        $logMessage = date('Y-m-d H:i:s') . " - New standard added: " . $update['name'] . "\n";
                        file_put_contents($logFile, $logMessage, FILE_APPEND);
                        $updated = true;
                    }
                }
            } else {
                $logMessage = date('Y-m-d H:i:s') . " - Failed to fetch $source\n";
                file_put_contents($logFile, $logMessage, FILE_APPEND);
            }
        }

        // Update last_checked_at for all standards
        $now = date('Y-m-d H:i:s');
        $stmt = $db->prepare("UPDATE standards SET last_checked_at = ?");
        $stmt->execute([$now]);

        $logMessage = date('Y-m-d H:i:s') . " - Standards update check completed. Updated: " . ($updated ? 'Yes' : 'No') . "\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    private static function fetchUrl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    private static function parseUpdates($html, $source) {
        $text = strip_tags($html);
        $updates = [];

        if ($source === 'IFRS') {
            // Simple parsing: look for IFRS followed by number
            preg_match_all('/IFRS\s+\d+/', $text, $matches);
            foreach ($matches[0] as $match) {
                if (!self::standardExists($match)) {
                    $updates[] = [
                        'name' => $match,
                        'description' => 'New IFRS standard detected.'
                    ];
                }
            }
        } elseif ($source === 'FASB') {
            // Look for ASU
            preg_match_all('/ASU\s+\d+-\d+/', $text, $matches);
            foreach ($matches[0] as $match) {
                if (!self::standardExists($match)) {
                    $updates[] = [
                        'name' => $match,
                        'description' => 'New FASB ASU detected.'
                    ];
                }
            }
        }

        return $updates;
    }

    private static function standardExists($name) {
        $standards = Standard::findAll();
        foreach ($standards as $standard) {
            if (stripos($standard['name'], $name) !== false) {
                return true;
            }
        }
        return false;
    }
}