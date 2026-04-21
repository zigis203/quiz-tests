<?php
require_once __DIR__ . '/../config/config.php';
$db = Database::getInstance()->getConnection();
$currentUser = User::getCurrentUser($db);
if (!$currentUser || $currentUser['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$questionModel = new Question($db);
$questionId = (int)($_GET['question_id'] ?? 0);
$question = $questionModel->getQuestionById($questionId);

if (!$question) {
    header('Location: manage_questions.php');
    exit;
}

$answers = $question['answers'];
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>Atbil­žu pārvaldība</title>
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
        .badge {
            display: inline-block;
            padding: 4px 8px;
            background-color: #4CAF50;
            color: white;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: bold;
        }
        .question-box {
            background-color: #f0f0f0;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #4CAF50;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <main class="page-card">
        <h1>Atbil­žu pārvaldība</h1>
        
        <div class="question-box">
            <strong>Jautājums:</strong> <?php echo htmlspecialchars($question['question_text'], ENT_QUOTES); ?>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Atbilde</th>
                    <th>Pareiza</th>
                    <th>Darbības</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($answers as $answer): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($answer['answer_text'], ENT_QUOTES); ?></td>
                        <td><?php echo $answer['is_correct'] ? '<span class="badge">Pareiza</span>' : '-'; ?></td>
                        <td>
                            <div class="action-buttons">
                                <a class="btn-small btn-edit" href="edit_answer.php?id=<?php echo (int)$answer['id']; ?>">Rediģēt</a>
                                <a class="btn-small btn-delete" href="delete_answer.php?id=<?php echo (int)$answer['id']; ?>" onclick="return confirm('Vai tiešām dzēst šo atbildi?');">Dzēst</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="button-group">
            <a class="button-link" href="manage_questions.php">Atpakaļ uz jautājumiem</a>
            <a class="button-link" href="admin.php">Atpakaļ uz adminu</a>
        </div>
    </main>
</body>
</html>
