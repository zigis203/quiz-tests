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

$message = '';
$errors = [];
$questionModel = new Question($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answerText = trim($_POST['answer_text'] ?? '');
    $isCorrect = isset($_POST['is_correct']);

    if ($answerText === '') {
        $errors[] = 'Atbildes teksts ir obligāts.';
    }

    if (empty($errors)) {
        $questionModel->updateAnswer($answerId, $answerText, $isCorrect);
        $message = 'Atbilde atjaunināta veiksmīgi.';
        
        // Atjaunināt datus no DB
        $stmt = $db->prepare('SELECT a.*, q.id AS question_id, q.question_text FROM answers a JOIN questions q ON a.question_id = q.id WHERE a.id = :id LIMIT 1');
        $stmt->execute([':id' => $answerId]);
        $answer = $stmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>Rediģēt atbildi</title>
    <style>
        .question-box {
            background-color: #f0f0f0;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #2196F3;
            border-radius: 4px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
        }
        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
    </style>
</head>
<body>
    <main class="page-card">
        <h1>Rediģēt atbildi</h1>
        
        <div class="question-box">
            <strong>Jautājums:</strong> <?php echo htmlspecialchars($answer['question_text'], ENT_QUOTES); ?>
        </div>
        
        <?php if ($message): ?>
            <div class="success-message"><?php echo htmlspecialchars($message, ENT_QUOTES); ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="error-message"><?php echo implode('<br>', $errors); ?></div>
        <?php endif; ?>
        
        <form action="edit_answer.php?id=<?php echo (int)$answerId; ?>" method="post" novalidate>
            <label>Atbildes teksts
                <input type="text" name="answer_text" value="<?php echo htmlspecialchars($answer['answer_text'] ?? $_POST['answer_text'] ?? '', ENT_QUOTES); ?>" required>
            </label>
            
            <div class="checkbox-group">
                <input type="checkbox" name="is_correct" id="is_correct" <?php echo ($answer['is_correct'] || isset($_POST['is_correct'])) ? 'checked' : ''; ?>>
                <label for="is_correct" style="margin: 0; display: inline; font-weight: normal;">Pareiza atbilde</label>
            </div>
            
            <button type="submit">Saglabāt izmaiņas</button>
        </form>
        
        <div class="button-group">
            <a class="button-link" href="manage_answers.php?question_id=<?php echo (int)$answer['question_id']; ?>">Atpakaļ uz atbildēm</a>
            <a class="button-link" href="admin.php">Atpakaļ uz adminu</a>
        </div>
    </main>
</body>
</html>
