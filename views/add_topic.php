<?php
require_once __DIR__ . '/../config/config.php';
$db = Database::getInstance()->getConnection();
$currentUser = User::getCurrentUser($db);
if (!$currentUser || $currentUser['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$topicModel = new Topic($db);
$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === '') {
        $errors[] = 'Tēmas nosaukums ir obligāts.';
    }

    if (empty($errors)) {
        $topicModel->addTopic($name, $description ?: null);
        $message = 'Tēma pievienota veiksmīgi.';
    }
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>Pievienot tēmu</title>
</head>
<body>
    <main class="page-card">
        <h1>Pievienot tēmu</h1>
        <?php if ($message): ?>
            <div class="success-message"><?php echo htmlspecialchars($message, ENT_QUOTES); ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="error-message"><?php echo implode('<br>', $errors); ?></div>
        <?php endif; ?>
        <form action="add_topic.php" method="post" novalidate>
            <label>Tēmas nosaukums<input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES); ?>" required></label>
            <label>Apraksts<input type="text" name="description" value="<?php echo htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES); ?>"></label>
            <button type="submit">Saglabāt tēmu</button>
        </form>
        <div class="button-group">
            <a class="button-link" href="admin.php">Atpakaļ uz adminu</a>
        </div>
    </main>
</body>
</html>
