<?php $title = 'تسجيل الدخول'; include __DIR__ . '/../templates/header.php'; ?>
<div class="login-form">
    <h1>تسجيل الدخول</h1>
    <form id="loginForm" method="POST" action="login">
        <input type="hidden" name="csrf_token" value="<?php echo \Security::generateCSRFToken(); ?>">
        <div class="form-group">
            <label for="email">البريد الإلكتروني:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">كلمة المرور:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">تسجيل الدخول</button>
    </form>
    <p>ليس لديك حساب؟ <a href="register">سجل الآن</a></p>
</div>
<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('login', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = './';
        } else {
            alert(data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ في تسجيل الدخول');
    });
});
</script>
<?php include __DIR__ . '/../templates/footer.php'; ?>