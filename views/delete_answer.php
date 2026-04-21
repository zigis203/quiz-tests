<?php
require_once __DIR__ . '/../config/config.php';
$db = Database::getInstance()->getConnection();
$currentUser = User::getCurrentUser($db);
if (!$currentUser || $currentUser['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$answerId = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare('SELECT a.*, q.id AS question_id, q.question_text FROM answers a JOIN questions q ON a.question_id = q.id WHERE a.id = :id LIMIT 1');
$stmt->execute([':id' => $answerId]);
$answer = $stmt->fetch();

if (!$answer) {
    header('Location: manage_questions.php');
    exit;
}

// Pārbaudīt, vai ir vēl viena pareiza atbilde
$stmt = $db->prepare('SELECT COUNT(*) FROM answers WHERE question_id = :question_id AND is_correct = 1');
$stmt->execute([':question_id' => $answer['question_id']]);
$correctCount = (int)$stmt->fetchColumn();

$questionModel = new Question($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pārbaudīt, vai atbilde ir pareiza un vai ir vēl viena pareiza atbilde
    if ($answer['is_correct'] && $correctCount <= 1) {
        // Neļaut dzēst vienu un vienīgo pareizo atbildi
        $error = 'Nevar dzēst vienu un vienīgo pareizo atbildi! Vispirms izveido citu pareizu atbildi.';
    } else {
        $questionModel->deleteAnswer($answerId);
        header('Location: manage_answers.php?question_id=' . (int)$answer['question_id']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>Dzēst atbildi</title>
    <style>
        .confirmation-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .error-box {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            color: #721c24;
        }
    </style>
</head>
<body>
    <main class="page-card">
        <h1>Dzēst atbildi</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-box"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
            <div class="button-group">
                <a class="button-link" href="manage_answers.php?question_id=<?php echo (int)$answer['question_id']; ?>">Atpakaļ uz atbildēm</a>
            </div>
        <?php else: ?>
            <div class="confirmation-box">
                <p><strong>Vai tiešām dzēst šo atbildi?</strong></p>
                <p style="font-style: italic; margin-top: 10px;"><?php echo htmlspecialchars($answer['answer_text'], ENT_QUOTES); ?></p>
                <p style="margin-top: 10px;">Šī darbība ir neatgriezeniska.</p>
            </div>
            
            <form action="delete_answer.php?id=<?php echo (int)$answerId; ?>" method="post">
                <div class="button-group">
                    <button type="submit" class="button-link" style="background-color: #f44336; color: white;">Dzēst atbildi</button>
                    <a class="button-link" href="manage_answers.php?question_id=<?php echo (int)$answer['question_id']; ?>">Atcelt</a>
                </div>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
