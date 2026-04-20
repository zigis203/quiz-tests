<?php
require_once __DIR__ . '/../config/config.php';
$db = Database::getInstance()->getConnection();
$userModel = new User($db);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $repeat = trim($_POST['repeat_password'] ?? '');

    if ($password !== $repeat) {
        $errors[] = 'Paroles nesakrīt.';
    } else {
        $result = $userModel->register($username, $email, $password);
        if ($result['success']) {
            header('Location: login.php');
            exit;
        }
        $errors = array_merge($errors, $result['errors']);
    }
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>Reģistrēties</title>
</head>
<body>
    <main class="page-card">
        <h1>Reģistrēties</h1>
        <?php if (!empty($errors)): ?>
            <div class="error-message"><?php echo implode('<br>', $errors); ?></div>
        <?php endif; ?>
        <form action="register.php" method="post" novalidate>
            <label>Lietotājvārds<input type="text" name="username" required></label>
            <label>E-pasts<input type="email" name="email" required></label>
            <label>Parole<input type="password" name="password" required></label>
            <label>Atkārtot paroli<input type="password" name="repeat_password" required></label>
            <button type="submit">Reģistrēties</button>
        </form>
        <p class="small-note">Jau ir konts? <a href="login.php">Ienākt</a></p>
    </main>
</body>
</html>
