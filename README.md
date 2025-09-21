# دليل إعداد ونشر التطبيق

## نظرة عامة
هذا التطبيق هو منصة تعليمية مبنية باستخدام PHP و MySQL، تستخدم نمط MVC لتنظيم الكود.

## متطلبات النظام
- PHP 7.4 أو أحدث
- MySQL 5.7 أو أحدث
- خادم ويب Apache مع دعم mod_rewrite
- Composer (اختياري لإدارة التبعيات)

## خطوات الإعداد

### 1. إعداد قاعدة البيانات
1. قم بإنشاء قاعدة بيانات جديدة في MySQL:
   ```sql
   CREATE DATABASE myapp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. قم بتشغيل ملف مخطط قاعدة البيانات:
   ```bash
   mysql -u root -p myapp < database_schema.sql
   ```
   أو من خلال phpMyAdmin أو أداة إدارة قاعدة البيانات الأخرى.

### 2. تكوين قاعدة البيانات
قم بتحرير ملف `config/database.php` وتعديل الإعدادات حسب بيئتك:
```php
return [
    'host' => 'localhost',     // عنوان خادم قاعدة البيانات
    'dbname' => 'myapp',       // اسم قاعدة البيانات
    'username' => 'root',      // اسم المستخدم
    'password' => '',          // كلمة المرور
    'charset' => 'utf8mb4'     // ترميز الأحرف
];
```

### 3. إعداد خادم الويب (Apache)
1. تأكد من تفعيل mod_rewrite في Apache:
   ```bash
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

2. قم بتوجيه جذر المستند (Document Root) إلى مجلد `public/`:
   ```
   DocumentRoot /path/to/your/project/public
   <Directory /path/to/your/project/public>
       AllowOverride All
       Require all granted
   </Directory>
   ```

3. ملف `.htaccess` في مجلد `public/` سيتم إنشاؤه تلقائياً لإعادة توجيه جميع الطلبات إلى `index.php`.

### 4. إنشاء مستخدم إداري
قم بإدراج مستخدم إداري في قاعدة البيانات:
```sql
INSERT INTO users (username, email, password_hash, role) VALUES (
    'admin',
    'admin@example.com',
    '$2y$10$example.hash.here', -- استخدم password_hash() في PHP لتوليد الهاش
    'admin'
);
```

يمكنك استخدام هذا الكود في PHP لتوليد الهاش:
```php
echo password_hash('your_password', PASSWORD_DEFAULT);
```

### 5. تشغيل سكريبت التحديث
قم بتشغيل سكريبت تحديث المعايير:
```bash
php scripts/update_standards.php
```

يمكن جدولة هذا السكريبت للتشغيل التلقائي باستخدام cron:
```bash
# أضف هذا السطر إلى crontab للتشغيل يومياً
0 2 * * * /usr/bin/php /path/to/project/scripts/update_standards.php
```

## اختبار التطبيق
1. تأكد من أن خادم الويب يعمل وأن جذر المستند موجه إلى `public/`.
2. قم بتشغيل سكريبت الاختبار:
   ```bash
   php tests/test_db.php
   ```
3. افتح المتصفح وانتقل إلى `http://localhost` (أو عنوان خادمك).

## استكشاف الأخطاء
- تأكد من صحة إعدادات قاعدة البيانات في `config/database.php`.
- تحقق من صلاحيات الملفات والمجلدات.
- راجع سجلات الأخطاء في `storage/logs/`.
- تأكد من تفعيل mod_rewrite في Apache.

## هيكل المشروع
```
/
├── app/                 # منطق التطبيق
│   ├── Controllers/     # متحكمات MVC
│   ├── Models/          # نماذج البيانات
│   └── Views/           # طرق العرض
├── config/              # ملفات التكوين
├── public/              # الملفات العامة (جذر الويب)
├── scripts/             # سكريبتات الصيانة
├── storage/             # ملفات التخزين
├── tests/               # اختبارات التطبيق
├── database_schema.sql  # مخطط قاعدة البيانات
└── README.md           # هذا الملف
```

## الأمان
- لا تقم بتخزين ملفات التكوين في مجلد قابل للوصول عبر الويب.
- استخدم كلمات مرور قوية لقاعدة البيانات والمستخدمين.
- قم بتحديث PHP و MySQL بانتظام.
- راجع إعدادات الأمان في `app/Security.php`.

## الدعم
إذا واجهت مشاكل، راجع سجلات الأخطاء أو تواصل مع مطور التطبيق.