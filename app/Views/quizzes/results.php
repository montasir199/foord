<?php $title = 'Quiz Results'; include __DIR__ . '/../templates/header.php'; ?>
<div class="quiz-results">
    <h1><?php echo htmlspecialchars($quiz['title']); ?> - Results</h1>
    <p>Score: <?php echo $attempt['score']; ?>%</p>
    <p>Completed at: <?php echo $attempt['completed_at']; ?></p>
    <h2>Questions</h2>
    <?php foreach ($questions as $question): ?>
        <div class="question">
            <p><?php echo htmlspecialchars($question['question_text']); ?></p>
            <p>Correct Answer: <?php echo htmlspecialchars($question['correct_answer']); ?></p>
        </div>
    <?php endforeach; ?>
    <a href="/courses">Back to Courses</a>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>