<?php
require_once __DIR__ . '/../config/config.php';
$db = Database::getInstance()->getConnection();
$currentUser = User::getCurrentUser($db);
if (!$currentUser || $currentUser['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$stmt = $db->query('SELECT t.*, COUNT(q.id) AS question_count FROM topics t LEFT JOIN questions q ON q.topic_id = t.id GROUP BY t.id ORDER BY t.name ASC');
$topics = $stmt->fetchAll();

// Iegūt kopējo statistiku
$stmt = $db->query('SELECT COUNT(*) FROM topics');
$topicCount = (int)$stmt->fetchColumn();

$stmt = $db->query('SELECT COUNT(*) FROM questions');
$questionCount = (int)$stmt->fetchColumn();

$stmt = $db->query('SELECT COUNT(*) FROM answers');
$answerCount = (int)$stmt->fetchColumn();

$stmt = $db->query('SELECT COUNT(*) FROM quiz_results');
$resultCount = (int)$stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>Admin panelis</title>
    <style>
        .admin-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #4CAF50;
        }
        .stat-card h3 {
            margin: 0;
            font-size: 2em;
            color: #4CAF50;
        }
        .stat-card p {
            margin: 5px 0 0 0;
            font-size: 0.9em;
            color: #666;
        }
        .admin-sections {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .section-box {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .section-box h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .section-box .button-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .btn-admin {
            padding: 10px 15px;
            text-decoration: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 4px;
            text-align: center;
            font-size: 0.95em;
            transition: background-color 0.3s;
        }
        .btn-admin:hover {
            background-color: #45a049;
        }
        .topics-list {
            margin-top: 20px;
        }
        .topics-list ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .topics-list li {
            padding: 8px;
            background-color: white;
            margin-bottom: 5px;
            border-radius: 4px;
            border-left: 3px solid #4CAF50;
        }
        .question-count {
            font-size: 0.85em;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <main class="page-card">
        <div class="admin-header">
            <h1>📊 Admin panelis</h1>
            <p>Sveiki, <?php echo htmlspecialchars($currentUser['username'], ENT_QUOTES); ?> (Administrators)</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo $topicCount; ?></h3>
                <p>Tēmas</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $questionCount; ?></h3>
                <p>Jautājumi</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $answerCount; ?></h3>
                <p>Atbildes</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $resultCount; ?></h3>
                <p>Testi izpildīti</p>
            </div>
        </div>

        <div class="admin-sections">
            <div class="section-box">
                <h3>📚 Tēmu pārvaldība</h3>
                <div class="button-group">
                    <a class="btn-admin" href="add_topic.php">➕ Pievienot tēmu</a>
                    <a class="btn-admin" href="manage_topics.php">👁️ Skatīt visas tēmas</a>
                </div>
            </div>

            <div class="section-box">
                <h3>❓ Jautājumu pārvaldība</h3>
                <div class="button-group">
                    <a class="btn-admin" href="add_question.php">➕ Pievienot jautājumu</a>
                    <a class="btn-admin" href="manage_questions.php">👁️ Skatīt visus jautājumus</a>
                </div>
            </div>

            <div class="section-box">
                <h3>🔄 Atbil­žu pārvaldība</h3>
                <p style="margin: 10px 0; font-size: 0.9em; color: #666;">Rediģēt atbildes var, atvērt konkrēta jautājuma atbildes.</p>
                <div class="button-group">
                    <a class="btn-admin" href="manage_questions.php">⚙️ Pārvaldīt atbildes</a>
                </div>
            </div>
        </div>

        <section class="topics-list">
            <h2>Tēmu pārskats</h2>
            <?php if (empty($topics)): ?>
                <p>Nav tēmu. <a href="add_topic.php">Pievienot pirmo tēmu</a></p>
            <?php else: ?>
                <ul>
                    <?php foreach ($topics as $topic): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($topic['name'], ENT_QUOTES); ?></strong>
                            <span class="question-count">(<?php echo (int)$topic['question_count']; ?> jautājumi)</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <div class="button-group">
            <a class="button-link" href="topics.php">← Atpakaļ uz tēmu izvēli</a>
            <a class="button-link" href="logout.php">Izlogoties</a>
        </div>
    </main>
</body>
</html>
