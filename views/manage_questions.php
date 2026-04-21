<?php
require_once __DIR__ . '/../config/config.php';
$db = Database::getInstance()->getConnection();
$currentUser = User::getCurrentUser($db);
if (!$currentUser || $currentUser['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$topicModel = new Topic($db);
$questionModel = new Question($db);

$topicId = (int)($_GET['topic_id'] ?? 0);
if ($topicId <= 0) {
    // Parādīt visus jautājumus, ja nav norādīta tēma
    $stmt = $db->query('SELECT q.*, t.name AS topic_name FROM questions q JOIN topics t ON q.topic_id = t.id ORDER BY t.name ASC, q.id ASC');
    $questions = $stmt->fetchAll();
    $topic = null;
} else {
    $topic = $topicModel->getById($topicId);
    if (!$topic) {
        header('Location: admin.php');
        exit;
    }
    $questions = $questionModel->getQuestionsByTopic($topicId);
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>Jautājumu pārvaldība</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .question-text {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        .btn-small {
            padding: 6px 12px;
            font-size: 0.9em;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: inline-block;
        }
        .btn-edit {
            background-color: #4CAF50;
            color: white;
        }
        .btn-delete {
            background-color: #f44336;
            color: white;
        }
        .btn-edit:hover {
            background-color: #45a049;
        }
        .btn-delete:hover {
            background-color: #da190b;
        }
    </style>
</head>
<body>
    <main class="page-card">
        <h1>Jautājumu pārvaldība<?php echo $topic ? ' - ' . htmlspecialchars($topic['name'], ENT_QUOTES) : ''; ?></h1>
        
        <?php if (empty($questions)): ?>
            <p>Nav jautājumu <?php echo $topic ? 'šajā tēmā' : ''; ?></p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <?php if (!$topic): ?>
                            <th>Tēma</th>
                        <?php endif; ?>
                        <th>Jautājums</th>
                        <th>Darbības</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($questions as $question): ?>
                        <tr>
                            <?php if (!$topic): ?>
                                <td><?php echo htmlspecialchars($question['topic_name'], ENT_QUOTES); ?></td>
                            <?php endif; ?>
                            <td class="question-text" title="<?php echo htmlspecialchars($question['question_text'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($question['question_text'], ENT_QUOTES); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a class="btn-small btn-edit" href="edit_question.php?id=<?php echo (int)$question['id']; ?>">Rediģēt</a>
                                    <a class="btn-small btn-delete" href="delete_question.php?id=<?php echo (int)$question['id']; ?>" onclick="return confirm('Vai tiešām dzēst šo jautājumu?');">Dzēst</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <div class="button-group">
            <a class="button-link" href="add_question.php">Pievienot jaunu jautājumu</a>
            <?php if ($topic): ?>
                <a class="button-link" href="manage_questions.php">Skatīt visus jautājumus</a>
            <?php endif; ?>
            <a class="button-link" href="admin.php">Atpakaļ uz adminu</a>
        </div>
    </main>
</body>
</html>
