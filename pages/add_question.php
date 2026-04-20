<?php
require_once __DIR__ . '/../config.php';
$db = Database::getInstance()->getConnection();
$currentUser = User::getCurrentUser($db);
if (!$currentUser || $currentUser['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$topicModel = new Topic($db);
$questionModel = new Question($db);
$topics = $topicModel->getAll();
$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topic_id = (int)($_POST['topic_id'] ?? 0);
    $question_text = trim($_POST['question_text'] ?? '');
    $answers = [];
    
    if ($topic_id <= 0) {
        $errors[] = 'Tēma ir obligāta.';
    }
    
    if ($question_text === '') {
        $errors[] = 'Jautājuma teksts ir obligāts.';
    }
    
    for ($i = 1; $i <= 4; $i++) {
        $answer_text = trim($_POST["answer_$i"] ?? '');
        if ($answer_text === '') {
            $errors[] = "Atbilde $i ir obligāta.";
        } else {
            $answers[] = [
                'text' => $answer_text,
                'correct' => isset($_POST["correct_$i"]) ? 1 : 0
            ];
        }
    }
    
    $correct_count = count(array_filter($answers, fn($a) => $a['correct']));
    if ($correct_count == 0) {
        $errors[] = 'Vismaz viena atbilde ir jābūt pareizai.';
    }
    
    if (empty($errors)) {
        try {
            $questionModel->addQuestionWithAnswers($topic_id, $question_text, $answers);
            $message = 'Jautājums pievienots veiksmīgi.';
        } catch (Exception $e) {
            $errors[] = 'Kļūda pievienojot jautājumu: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/style.css">
    <title>Pievienot jautājumu</title>
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
        <h1>Pievienot jautājumu</h1>
        <?php if ($message): ?>
            <div class="success-message"><?php echo htmlspecialchars($message, ENT_QUOTES); ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="error-message"><?php echo implode('<br>', $errors); ?></div>
        <?php endif; ?>
        
        <form action="add_question.php" method="post" novalidate>
            <label>Tēma
                <select name="topic_id" required>
                    <option value="">-- Izvēlēties tēmu --</option>
                    <?php foreach ($topics as $topic): ?>
                        <option value="<?php echo (int)$topic['id']; ?>" <?php echo isset($_POST['topic_id']) && (int)$_POST['topic_id'] === (int)$topic['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($topic['name'], ENT_QUOTES); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            
            <label>Jautājums
                <textarea name="question_text" rows="3" required><?php echo htmlspecialchars($_POST['question_text'] ?? '', ENT_QUOTES); ?></textarea>
            </label>
            
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <div class="answer-group">
                    <label>Atbilde <?php echo $i; ?>
                        <input type="text" name="answer_<?php echo $i; ?>" value="<?php echo htmlspecialchars($_POST["answer_$i"] ?? '', ENT_QUOTES); ?>" required>
                    </label>
                    <div class="checkbox-group">
                        <input type="checkbox" name="correct_<?php echo $i; ?>" id="correct_<?php echo $i; ?>" <?php echo isset($_POST["correct_$i"]) ? 'checked' : ''; ?>>
                        <label for="correct_<?php echo $i; ?>" style="margin: 0; display: inline; font-weight: normal;">Pareiza atbilde</label>
                    </div>
                </div>
            <?php endfor; ?>
            
            <button type="submit">Saglabāt jautājumu</button>
        </form>
        
        <div class="button-group">
            <a class="button-link" href="admin.php">Atpakaļ uz adminu</a>
        </div>
    </main>
</body>
</html>
