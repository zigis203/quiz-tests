<?php
require_once __DIR__ . '/../config/config.php';
$db = Database::getInstance()->getConnection();
$currentUser = User::getCurrentUser($db);
if (!$currentUser) {
    header('Location: login.php');
    exit;
}

$resultModel = new QuizResult($db);
$result = $resultModel->getLatestForUser($currentUser['id']);
if (!$result) {
    header('Location: topics.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>Rezultāts</title>
</head>
<body>
    <main class="page-card results-card">
        <h1>Rezultāts</h1>
        <p class="result-text">Tu atbildēji pareizi uz <?php echo (int)$result['score']; ?> no <?php echo (int)$result['total']; ?> jautājumiem.</p>
        <p>Testa tēma: <?php echo htmlspecialchars($result['topic_name'], ENT_QUOTES); ?></p>
        <div class="button-group">
            <a class="button-link" href="topics.php">Izvēlēties citu tematu</a>
            <a class="button-link" href="quiz.php?topic_id=<?php echo (int)$result['topic_id']; ?>">Atkārtot šo testu</a>
            <a class="button-link" href="history.php">Skatīt vēsturi</a>
        </div>
    </main>
</body>
</html>
