<?php
$title = 'Take Quiz';
require_once __DIR__ . '/../Security.php';
$csrfToken = Security::generateCSRFToken();
include __DIR__ . '/../templates/header.php';
?>
<div class="quiz-take">
    <h1><?php echo htmlspecialchars($quiz['title']); ?></h1>
    <form action="/quiz/submit/<?php echo $attempt_id; ?>" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
        <?php foreach ($questions as $question): ?>
            <div class="question">
                <p><?php echo htmlspecialchars($question['question_text']); ?></p>
                <?php if ($question['question_type'] === 'multiple_choice' && $question['options']): ?>
                    <?php foreach ($question['options'] as $option): ?>
                        <label>
                            <input type="radio" name="answers[<?php echo $question['id']; ?>]" value="<?php echo htmlspecialchars($option); ?>">
                            <?php echo htmlspecialchars($option); ?>
                        </label><br>
                    <?php endforeach; ?>
                <?php elseif ($question['question_type'] === 'short_answer'): ?>
                    <input type="text" name="answers[<?php echo $question['id']; ?>]" placeholder="Your answer">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit">Submit Quiz</button>
    </form>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>