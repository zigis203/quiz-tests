<?php
require_once __DIR__ . '/../config/config.php';
$db = Database::getInstance()->getConnection();
$currentUser = User::getCurrentUser($db);
if (!$currentUser || $currentUser['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$topicModel = new Topic($db);
$topics = $topicModel->getAll();
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>Tēmu pārvaldība</title>
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
    </style>
</head>
<body>
    <main class="page-card">
        <h1>Tēmu pārvaldība</h1>
        
        <table>
            <thead>
                <tr>
                    <th>Nosaukums</th>
                    <th>Apraksts</th>
                    <th>Darbības</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topics as $topic): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($topic['name'], ENT_QUOTES); ?></td>
                        <td><?php echo htmlspecialchars($topic['description'] ?? '-', ENT_QUOTES); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a class="btn-small btn-edit" href="edit_topic.php?id=<?php echo (int)$topic['id']; ?>">Rediģēt</a>
                                <a class="btn-small btn-delete" href="delete_topic.php?id=<?php echo (int)$topic['id']; ?>" onclick="return confirm('Vai tiešām dzēst šo tēmu?');">Dzēst</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="button-group">
            <a class="button-link" href="add_topic.php">Pievienot jaunu tēmu</a>
            <a class="button-link" href="admin.php">Atpakaļ uz adminu</a>
        </div>
    </main>
</body>
</html>
