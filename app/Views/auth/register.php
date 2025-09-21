<?php $title = 'التسجيل'; include __DIR__ . '/../templates/header.php'; ?>
<div class="register-form">
    <h1>التسجيل</h1>
    <form id="registerForm" method="POST" action="register">
        <input type="hidden" name="csrf_token" value="<?php echo \Security::generateCSRFToken(); ?>">
        <div class="form-group">
            <label for="username">اسم المستخدم:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="email">البريد الإلكتروني:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">كلمة المرور:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">التسجيل</button>
    </form>
    <p>لديك حساب بالفعل؟ <a href="login">سجل الدخول</a></p>
</div>
<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('register', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('تم التسجيل بنجاح! يمكنك الآن تسجيل الدخول.');
            window.location.href = 'login';
        } else {
            alert(data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ في التسجيل');
    });
});
</script>
<?php include __DIR__ . '/../templates/footer.php'; ?>