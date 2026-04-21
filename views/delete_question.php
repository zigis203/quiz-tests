<?php
require_once __DIR__ . '/../config/config.php';
$db = Database::getInstance()->getConnection();
$currentUser = User::getCurrentUser($db);
if (!$currentUser || $currentUser['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$questionModel = new Question($db);
$questionId = (int)($_GET['id'] ?? 0);
$question = $questionModel->getQuestionById($questionId);

if (!$question) {
    header('Location: manage_questions.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $questionModel->deleteQuestion($questionId);
    header('Location: manage_questions.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>Dzēst jautājumu</title>
    <style>
        .confirmation-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <main class="page-card">
        <h1>Dzēst jautājumu</h1>
        
        <div class="confirmation-box">
            <p><strong>Vai tiešām dzēst jautājumu?</strong></p>
            <p style="font-style: italic; margin-top: 10px;"><?php echo htmlspecialchars($question['question_text'], ENT_QUOTES); ?></p>
            <p style="margin-top: 10px;">Šī darbība ir neatgriezeniska. Tiks dzēstas arī visas šim jautājumam saistītās atbildes.</p>
        </div>
        
        <form action="delete_question.php?id=<?php echo (int)$questionId; ?>" method="post">
            <div class="button-group">
                <button type="submit" class="button-link" style="background-color: #f44336; color: white;">Dzēst jautājumu</button>
                <a class="button-link" href="manage_questions.php">Atcelt</a>
            </div>
        </form>
    </main>
</body>
</html>
