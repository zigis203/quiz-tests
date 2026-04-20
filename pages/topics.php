<?php
require_once __DIR__ . '/../config.php';
$db = Database::getInstance()->getConnection();
$currentUser = User::getCurrentUser($db);
if (!$currentUser) {
    header('Location: login.php');
    exit;
}

$topicModel = new Topic($db);
$topics = $topicModel->getAll();
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/style.css">
    <title>Tēmas izvēle</title>
</head>
<body>
    <main class="page-card">
        <div class="page-header">
            <h1>Izvēlies testa tēmu</h1>
            <p>Sveiks, <?php echo htmlspecialchars($currentUser['username'], ENT_QUOTES); ?>!</p>
        </div>
        <div class="topic-list">
            <?php foreach ($topics as $topic): ?>
                <article class="topic-card">
                    <h2><?php echo htmlspecialchars($topic['name'], ENT_QUOTES); ?></h2>
                    <p><?php echo htmlspecialchars($topic['description'] ?? 'Atbildi uz jautājumiem šajā tēmā.', ENT_QUOTES); ?></p>
                    <a class="button-link" href="quiz.php?topic_id=<?php echo $topic['id']; ?>">Sākt testu</a>
                </article>
            <?php endforeach; ?>
        </div>
        <div class="button-group">
            <a class="button-link" href="history.php">Manas atzīmes</a>
            <?php if ($currentUser['role'] === 'admin'): ?>
                <a class="button-link" href="admin.php">Admin panelis</a>
            <?php endif; ?>
            <a class="button-link" href="logout.php">Izlogoties</a>
        </div>
    </main>
</body>
</html>
