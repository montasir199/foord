<?php

session_start();

require_once '../Models/Quiz.php';
require_once '../Models/Attempt.php';
require_once '../Models/Question.php';
require_once '../Security.php';

class QuizController {

    private static function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    public static function startQuiz($quiz_id) {
        self::checkAuth();

        $user_id = $_SESSION['user_id'];
        $quiz = Quiz::findById($quiz_id);
        if (!$quiz) {
            echo "Quiz not found";
            return;
        }

        // Check max attempts (assume 3 max attempts)
        $max_attempts = 3;
        $attempts = Attempt::findByUserAndQuiz($user_id, $quiz_id);
        $completed_attempts = array_filter($attempts, function($a) { return $a['completed_at'] !== null; });
        if (count($completed_attempts) >= $max_attempts) {
            echo "Maximum attempts reached";
            return;
        }

        // Create new attempt
        $attempt_id = Attempt::create($user_id, $quiz_id);

        // Redirect to take quiz
        header("Location: /quiz/take/$attempt_id");
        exit;
    }

    public static function takeQuiz($attempt_id) {
        self::checkAuth();

        $user_id = $_SESSION['user_id'];
        $attempt = Attempt::findById($attempt_id);
        if (!$attempt || $attempt['user_id'] != $user_id) {
            echo "Unauthorized";
            return;
        }

        if ($attempt['completed_at']) {
            header("Location: /quiz/results/$attempt_id");
            exit;
        }

        $quiz = Quiz::findById($attempt['quiz_id']);
        $questions = Quiz::getQuestions($attempt['quiz_id']);

        require_once '../Views/quizzes/take.php';
    }

    public static function submitQuiz($attempt_id) {
        self::checkAuth();

        if (!Security::validateCSRFToken()) {
            echo "Invalid CSRF token";
            return;
        }

        $user_id = $_SESSION['user_id'];
        $attempt = Attempt::findById($attempt_id);
        if (!$attempt || $attempt['user_id'] != $user_id) {
            echo "Unauthorized";
            return;
        }

        if ($attempt['completed_at']) {
            echo "Quiz already submitted";
            return;
        }

        // Check time limit (assume 30 minutes)
        $time_limit_minutes = 30;
        $started_at = strtotime($attempt['started_at']);
        $now = time();
        if (($now - $started_at) > ($time_limit_minutes * 60)) {
            // Time expired, grade with submitted answers or empty
            $answers = $_POST['answers'] ?? [];
            Attempt::gradeAttempt($attempt_id, $answers);
            header("Location: /quiz/results/$attempt_id");
            exit;
        }

        $answers = $_POST['answers'] ?? [];
        if (Attempt::gradeAttempt($attempt_id, $answers)) {
            header("Location: /quiz/results/$attempt_id");
        } else {
            echo "Error grading quiz";
        }
    }

    public static function viewResults($attempt_id) {
        self::checkAuth();

        $user_id = $_SESSION['user_id'];
        $attempt = Attempt::findById($attempt_id);
        if (!$attempt || $attempt['user_id'] != $user_id) {
            echo "Unauthorized";
            return;
        }

        if (!$attempt['completed_at']) {
            echo "Quiz not completed";
            return;
        }

        $quiz = Quiz::findById($attempt['quiz_id']);
        $questions = Quiz::getQuestions($attempt['quiz_id']);

        require_once '../Views/quizzes/results.php';
    }

}