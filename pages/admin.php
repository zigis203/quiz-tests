<?php
require_once __DIR__ . '/../config.php';
$db = Database::getInstance()->getConnection();
$currentUser = User::getCurrentUser($db);
if (!$currentUser || $currentUser['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$stmt = $db->query('SELECT t.*, COUNT(q.id) AS question_count FROM topics t LEFT JOIN questions q ON q.topic_id = t.id GROUP BY t.id ORDER BY t.name ASC');
$topics = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/style.css">
    <title>Admin panelis</title>
</head>
<body>
    <main class="page-card">
        <h1>Admin panelis</h1>
        <p>Šeit var pievienot jaunas tēmas un pievienot jautājumus.</p>
        <div class="button-group">
            <a class="button-link" href="add_topic.php">Pievienot tēmu</a>
            <a class="button-link" href="add_question.php">Pievienot jautājumu</a>
            <a class="button-link" href="topics.php">Atpakaļ uz tēmu izvēli</a>
        </div>
        <section class="admin-topics">
            <h2>Temati</h2>
            <ul>
                <?php foreach ($topics as $topic): ?>
                    <li><?php echo htmlspecialchars($topic['name'], ENT_QUOTES); ?> — <?php echo (int)$topic['question_count']; ?> jautājumi</li>
                <?php endforeach; ?>
            </ul>
        </section>
    </main>
</body>
</html>
