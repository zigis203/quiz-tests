<?php
require_once __DIR__ . '/../config/config.php';
$db = Database::getInstance()->getConnection();
$currentUser = User::getCurrentUser($db);
if ($currentUser) {
    header('Location: topics.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>Quiz Tests</title>
</head>
<body>
    <main class="page-card">
        <h1>Quiz Tests</h1>
        <p>Reģistrējies vai pieraksties, lai sāktu testu, skatītu rezultātus un lohistiku.</p>
        <div class="button-group">
            <a class="button-link" href="register.php">Reģistrēties</a>
            <a class="button-link" href="login.php">Ienākt</a>
        </div>
    </main>
</body>
</html>
