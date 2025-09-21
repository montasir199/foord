<?php $title = 'Courses List'; include __DIR__ . '/../templates/header.php'; ?>
<div class="courses-list">
    <h1>Courses by Level</h1>
    <?php foreach ($coursesByLevel as $levelData): ?>
        <h2><?php echo htmlspecialchars($levelData['level']['name']); ?></h2>
        <p><?php echo htmlspecialchars($levelData['level']['description']); ?></p>
        <ul>
            <?php foreach ($levelData['courses'] as $course): ?>
                <li>
                    <a href="?action=viewCourse&course_id=<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['title']); ?></a>
                    <p><?php echo htmlspecialchars($course['description']); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>