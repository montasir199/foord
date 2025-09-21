<?php $title = 'Admin Dashboard'; include __DIR__ . '/../templates/header.php'; ?>
<div class="admin-dashboard">
    <h1>Admin Dashboard</h1>
    <p>Welcome, Admin!</p>
    <h2>Overview</h2>
    <p>Total Courses: <?php echo $courseCount; ?></p>
    <p>Total Lessons: <?php echo $lessonCount; ?></p>
    <p>Total Standards: <?php echo $standardCount; ?></p>
    <h2>Management</h2>
    <ul>
        <li><a href="/admin/courses">Manage Courses</a></li>
        <li><a href="/admin/lessons">Manage Lessons</a></li>
        <li><a href="/admin/standards">Manage Standards</a></li>
    </ul>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>