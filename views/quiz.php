<?php
require_once __DIR__ . '/../config/config.php';
$db = Database::getInstance()->getConnection();
$currentUser = User::getCurrentUser($db);
if (!$currentUser) {
    header('Location: login.php');
    exit;
}

$topicModel = new Topic($db);
$questionModel = new Question($db);

if (isset($_GET['topic_id']) && filter_var($_GET['topic_id'], FILTER_VALIDATE_INT)) {
    $topicId = (int)$_GET['topic_id'];
    $topic = $topicModel->getById($topicId);
    if (!$topic) {
        header('Location: topics.php');
        exit;
    }

    $questions = $questionModel->getRandomQuestions($topicId, 15);
    $_SESSION['current_quiz'] = [
        'topic_id' => $topicId,
        'topic_name' => $topic['name'],
        'questions' => $questions,
    ];
} elseif (!isset($_SESSION['current_quiz'])) {
    header('Location: topics.php');
    exit;
} else {
    $questions = $_SESSION['current_quiz']['questions'];
    $topic = $topicModel->getById($_SESSION['current_quiz']['topic_id']);
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/quiz.css">
    <script>
        window.quizData = <?php echo json_encode($questions, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        window.quizTopicName = <?php echo json_encode($topic['name'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    </script>
    <script src="../public/js/quiz.js" defer></script>
    <title><?php echo htmlspecialchars($topic['name'], ENT_QUOTES); ?> - Quiz</title>
</head>
<body>
    <div class="app">
        <div class="quiz-card">
            <header class="quiz-header">
                <div>
                    <h1><?php echo htmlspecialchars($topic['name'], ENT_QUOTES); ?></h1>
                    <p class="quiz-subtitle">Testā ir <?php echo count($questions); ?> jautājumi.</p>
                </div>
                <a class="button-link small" href="topics.php">Atpakaļ uz tēmu izvēli</a>
            </header>

            <div class="progress-container">
                <div class="progress-bar">
                    <div id="progress-fill" class="progress-fill"></div>
                </div>
                <span id="progress-label" class="progress-label"></span>
            </div>

            <div id="quiz-card" class="quiz-body">
                <h2 id="question-text"></h2>
                <div id="answer-buttons" class="answer-buttons"></div>
                <button id="next-btn" class="next-button" type="button">Nākamais jautājums</button>
            </div>
        </div>
    </div>
</body>
</html>
