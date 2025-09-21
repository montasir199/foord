<?php
$title = 'Manage Courses';
require_once __DIR__ . '/../Security.php';
$csrfToken = Security::generateCSRFToken();
include __DIR__ . '/../templates/header.php';
?>
<div class="admin-courses">
    <h1>Manage Courses</h1>
    <a href="/admin/dashboard">Back to Dashboard</a>

    <?php if (isset($courses)): ?>
    <h2>Courses List</h2>
    <ul>
        <?php foreach ($courses as $c): ?>
            <li>
                <?php echo htmlspecialchars($c['title']); ?> - <?php echo htmlspecialchars($c['description']); ?>
                <a href="/admin/courses/edit/<?php echo $c['id']; ?>">Edit</a>
                <a href="/admin/courses/delete/<?php echo $c['id']; ?>" onclick="return confirm('Delete?')">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="/admin/courses/add">Add New Course</a>
    <?php endif; ?>

    <?php if (isset($levels)): ?>
    <h2><?php echo isset($course) ? 'Edit Course' : 'Add Course'; ?></h2>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
        <label>Title: <input type="text" name="title" value="<?php echo isset($course) ? htmlspecialchars($course['title']) : ''; ?>" required></label><br>
        <label>Description: <textarea name="description" required><?php echo isset($course) ? htmlspecialchars($course['description']) : ''; ?></textarea></label><br>
        <label>Level:
            <select name="level_id" required>
                <?php foreach ($levels as $level): ?>
                    <option value="<?php echo $level['id']; ?>" <?php echo isset($course) && $course['level_id'] == $level['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($level['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <button type="submit"><?php echo isset($course) ? 'Update' : 'Add'; ?> Course</button>
    </form>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>