<?php
require_once __DIR__ . '/../config/config.php';
$db = Database::getInstance()->getConnection();
$userModel = new User($db);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $user = $userModel->authenticate($username, $password);
    if ($user) {
        User::login($user);
        header('Location: topics.php');
        exit;
    }
    $errors[] = 'Nederīgs lietotājvārds vai parole.';
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>Login</title>
</head>
<body>
    <main class="page-card">
        <h1>Ienākt</h1>
        <?php if (!empty($errors)): ?>
            <div class="error-message"><?php echo implode('<br>', $errors); ?></div>
        <?php endif; ?>
        <form action="login.php" method="post" novalidate>
            <label>Lietotājvārds<input type="text" name="username" required></label>
            <label>Parole<input type="password" name="password" required></label>
            <button type="submit">Ienākt</button>
        </form>
        <p class="small-note">Nav konta? <a href="register.php">Reģistrējies</a></p>
    </main>
</body>
</html>
