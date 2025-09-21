<?php
$title = 'Manage Standards';
require_once __DIR__ . '/../Security.php';
$csrfToken = Security::generateCSRFToken();
include __DIR__ . '/../templates/header.php';
?>
<div class="admin-standards">
    <h1>Manage Standards</h1>
    <a href="/admin/dashboard">Back to Dashboard</a>

    <?php if (isset($standards)): ?>
    <h2>Standards List</h2>
    <ul>
        <?php foreach ($standards as $s): ?>
            <li>
                <?php echo htmlspecialchars($s['name']); ?> - <?php echo htmlspecialchars($s['description']); ?>
                (Lesson: <?php echo htmlspecialchars($lessons[array_search($s['lesson_id'], array_column($lessons, 'id'))]['title'] ?? 'Unknown'); ?>)
                <a href="/admin/standards/edit/<?php echo $s['id']; ?>">Edit</a>
                <a href="/admin/standards/delete/<?php echo $s['id']; ?>" onclick="return confirm('Delete?')">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="/admin/standards/add">Add New Standard</a>
    <?php endif; ?>

    <?php if (isset($lessons)): ?>
    <h2><?php echo isset($standard) ? 'Edit Standard' : 'Add Standard'; ?></h2>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
        <label>Name: <input type="text" name="name" value="<?php echo isset($standard) ? htmlspecialchars($standard['name']) : ''; ?>" required></label><br>
        <label>Description: <textarea name="description" required><?php echo isset($standard) ? htmlspecialchars($standard['description']) : ''; ?></textarea></label><br>
        <label>Lesson:
            <select name="lesson_id" required>
                <?php foreach ($lessons as $l): ?>
                    <option value="<?php echo $l['id']; ?>" <?php echo isset($standard) && $standard['lesson_id'] == $l['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($l['title']); ?></option>
                <?php endforeach; ?>
            </select>
        </label><br>
        <button type="submit"><?php echo isset($standard) ? 'Update' : 'Add'; ?> Standard</button>
    </form>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../templates/footer.php'; ?>