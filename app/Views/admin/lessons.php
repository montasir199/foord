<?php
$title = 'Manage Lessons';
require_once __DIR__ . '/../Security.php';
$csrfToken = Security::generateCSRFToken();
include __DIR__ . '/../templates/header.php';
?>
<div class="admin-lessons">
    <h1>Manage Lessons</h1>
    <a href="/admin/dashboard">Back to Dashboard</a>

    <?php if (isset($lessons)): ?>
    <h2>Lessons List <?php echo isset($course) ? 'for ' . htmlspecialchars($course['title']) : ''; ?></h2>
    <ul>
        <?php foreach ($lessons as $l): ?>
            <li>
                <?php echo htmlspecialchars($l['title']); ?> (Order: <?php echo $l['order_number']; ?>)
                <?php if (!isset($course)): ?>
                    - Course: <?php echo htmlspecialchars($courses[array_search($l['course_id'], array_column($courses, 'id'))]['title'] ?? 'Unknown'); ?>
                <?php endif; ?>
                <a href="/admin/lessons/edit/<?php echo $l['id']; ?>">Edit</a>
                <a href="/admin/lessons/delete/<?php echo $l['id']; ?>" onclick="return confirm('Delete?')">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="/admin/lessons/add">Add New Lesson</a>
    <?php if (!isset($course)): ?>
        <form method="get" action="/admin/lessons">
            <label>Filter by Course:
                <select name="course">
                    <option value="">All</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo isset($_GET['course']) && $_GET['course'] == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['title']); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button type="submit">Filter</button>
        </form>
    <?php endif; ?>
    <?php endif; ?>

    <?php if (isset($courses)): ?>
    <h2><?php echo isset($lesson) ? 'Edit Lesson' : 'Add Lesson'; ?></h2>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
        <label>Title: <input type="text" name="title" value="<?php echo isset($lesson) ? htmlspecialchars($lesson['title']) : ''; ?>" required></label><br>
        <label>Content: <textarea name="content" required><?php echo isset($lesson) ? htmlspecialchars($lesson['content']) : ''; ?></textarea></label><br>
        <label>Course:
            <select name="course_id" required>
                <?php foreach ($courses as $c): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo isset($lesson) && $lesson['course_id'] == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['title']); ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <label>Order Number: <input type="number" name="order_number" value="<?php echo isset($lesson) ? $lesson['order_number'] : ''; ?>" required></label><br>
        <button type="submit"><?php echo isset($lesson) ? 'Update' : 'Add'; ?> Lesson</button>
    </form>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>