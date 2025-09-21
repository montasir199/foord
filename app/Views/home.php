<?php $title = 'الصفحة الرئيسية'; include __DIR__ . '/templates/header.php'; ?>
<div class="home">
    <h1>مرحباً بك في المنصة التعليمية</h1>
    <p>منصة تعليمية شاملة للطلاب والمعلمين</p>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="auth-links">
            <a href="login" class="btn">تسجيل الدخول</a>
            <a href="register" class="btn">التسجيل</a>
        </div>
    <?php else: ?>
        <div class="dashboard-links">
            <a href="/courses" class="btn">الدورات</a>
            <a href="/quizzes" class="btn">الاختبارات</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="/admin" class="btn">الإدارة</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/templates/footer.php'; ?>