<?php $title = 'Course Details'; include __DIR__ . '/../templates/header.php'; ?>
<div class="course-view">
    <h1><?php echo htmlspecialchars($course['title']); ?></h1>
    <p><?php echo htmlspecialchars($course['description']); ?></p>
    <h2>Lessons</h2>
    <ul>
        <?php foreach ($lessons as $lesson): ?>
            <li>
                <a href="?action=viewLesson&lesson_id=<?php echo $lesson['id']; ?>"><?php echo htmlspecialchars($lesson['title']); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="?action=index">Back to Courses</a>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>