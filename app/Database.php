<?php

// فئة قاعدة البيانات باستخدام PDO
class Database extends PDO {

    public function __construct() {
        $config = require __DIR__ . '/../config/database.php';
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        parent::__construct($dsn, $config['username'], $config['password']);
    }

}