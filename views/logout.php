<?php
require_once __DIR__ . '/../config/config.php';
User::logout();
header('Location: index.php');
exit;
