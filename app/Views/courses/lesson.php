<?php $title = 'Lesson Content'; include __DIR__ . '/../templates/header.php'; ?>
<div class="lesson-content">
    <h1><?php echo htmlspecialchars($lesson['title']); ?></h1>
    <div><?php echo nl2br(htmlspecialchars($lesson['content'])); ?></div>
    <a href="?action=viewCourse&course_id=<?php echo $lesson['course_id']; ?>">Back to Course</a>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>