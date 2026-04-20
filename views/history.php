<?php
require_once __DIR__ . '/../config/config.php';
$db = Database::getInstance()->getConnection();
$currentUser = User::getCurrentUser($db);
if (!$currentUser) {
    header('Location: login.php');
    exit;
}

$resultModel = new QuizResult($db);
$history = $resultModel->getHistoryForUser($currentUser['id']);
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>Vēsture</title>
</head>
<body>
    <main class="page-card">
        <h1>Mana testa vēsture</h1>
        <?php if (empty($history)): ?>
            <p>Vēl nav saglabātu rezultātu.</p>
        <?php else: ?>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Tēma</th>
                        <th>Rezultāts</th>
                        <th>Datums</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['topic_name'], ENT_QUOTES); ?></td>
                            <td><?php echo (int)$row['score']; ?> / <?php echo (int)$row['total']; ?></td>
                            <td><?php echo htmlspecialchars($row['created_at'], ENT_QUOTES); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <div class="button-group">
            <a class="button-link" href="topics.php">Atpakaļ uz tēmu izvēli</a>
        </div>
    </main>
</body>
</html>
