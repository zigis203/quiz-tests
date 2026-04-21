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

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_text = trim($_POST['question_text'] ?? '');

    if ($question_text === '') {
        $errors[] = 'Jautājuma teksts ir obligāts.';
    }

    // Jaunināt atbildes
    foreach ($question['answers'] as $answer) {
        $answerId = (int)$answer['id'];
        $answerText = trim($_POST["answer_text_$answerId"] ?? '');
        $isCorrect = isset($_POST["correct_$answerId"]);

        if ($answerText === '') {
            $errors[] = "Atbildes teksts ir obligāts visām atbildēm.";
        }
    }

    if (empty($errors)) {
        // Atjaunināt jautājumu
        $questionModel->updateQuestion($questionId, $question_text);

        // Atjaunināt atbildes
        foreach ($question['answers'] as $answer) {
            $answerId = (int)$answer['id'];
            $answerText = trim($_POST["answer_text_$answerId"]);
            $isCorrect = isset($_POST["correct_$answerId"]);
            $questionModel->updateAnswer($answerId, $answerText, $isCorrect);
        }

        $message = 'Jautājums atjaunināts veiksmīgi.';
        $question = $questionModel->getQuestionById($questionId);
    }
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>Rediģēt jautājumu</title>
    <style>
        .answer-group {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .answer-group label {
            margin-bottom: 8px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
        }
        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
    </style>
</head>
<body>
    <main class="page-card">
        <h1>Rediģēt jautājumu</h1>
        <?php if ($message): ?>
            <div class="success-message"><?php echo htmlspecialchars($message, ENT_QUOTES); ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="error-message"><?php echo implode('<br>', $errors); ?></div>
        <?php endif; ?>
        
        <form action="edit_question.php?id=<?php echo (int)$questionId; ?>" method="post" novalidate>
            <label>Jautājums
                <textarea name="question_text" rows="3" required><?php echo htmlspecialchars($question['question_text'] ?? $_POST['question_text'] ?? '', ENT_QUOTES); ?></textarea>
            </label>
            
            <h3>Atbildes</h3>
            <?php foreach ($question['answers'] as $answer): ?>
                <div class="answer-group">
                    <label>Atbilde
                        <input type="text" name="answer_text_<?php echo (int)$answer['id']; ?>" value="<?php echo htmlspecialchars($answer['answer_text'] ?? $_POST["answer_text_" . $answer['id']] ?? '', ENT_QUOTES); ?>" required>
                    </label>
                    <div class="checkbox-group">
                        <input type="checkbox" name="correct_<?php echo (int)$answer['id']; ?>" id="correct_<?php echo (int)$answer['id']; ?>" <?php echo ($answer['is_correct'] || isset($_POST["correct_" . $answer['id']])) ? 'checked' : ''; ?>>
                        <label for="correct_<?php echo (int)$answer['id']; ?>" style="margin: 0; display: inline; font-weight: normal;">Pareiza atbilde</label>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <button type="submit">Saglabāt izmaiņas</button>
        </form>
        
        <div class="button-group">
            <a class="button-link" href="manage_questions.php">Atpakaļ uz jautājumiem</a>
            <a class="button-link" href="admin.php">Atpakaļ uz adminu</a>
        </div>
    </main>
</body>
</html>
