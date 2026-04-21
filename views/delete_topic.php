<?php
require_once __DIR__ . '/../config/config.php';
$db = Database::getInstance()->getConnection();
$currentUser = User::getCurrentUser($db);
if (!$currentUser || $currentUser['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$topicModel = new Topic($db);
$topicId = (int)($_GET['id'] ?? 0);
$topic = $topicModel->getById($topicId);

if (!$topic) {
    header('Location: manage_topics.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topicModel->deleteTopic($topicId);
    header('Location: manage_topics.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>Dzēst tēmu</title>
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
        <h1>Dzēst tēmu</h1>
        
        <div class="confirmation-box">
            <p><strong>Vai tiešām dzēst tēmu: "<?php echo htmlspecialchars($topic['name'], ENT_QUOTES); ?>"?</strong></p>
            <p>Šī darbība ir neatgriezeniska. Tiks dzēsti arī visi jautājumi un atbildes, kas saistīti ar šo tēmu.</p>
        </div>
        
        <form action="delete_topic.php?id=<?php echo (int)$topicId; ?>" method="post">
            <div class="button-group">
                <button type="submit" class="button-link" style="background-color: #f44336; color: white;">Dzēst tēmu</button>
                <a class="button-link" href="manage_topics.php">Atcelt</a>
            </div>
        </form>
    </main>
</body>
</html>
