<?php
session_start();

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'quiz_app');
define('DB_USER', 'quiz_user');
define('DB_PASS', 'quiz_pass');
define('BASE_URL', '/pages');

spl_autoload_register(function ($class) {
    $path = __DIR__ . '/../app/models/' . $class . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});
