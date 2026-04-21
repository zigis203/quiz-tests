<?php
require_once __DIR__ . '/config/config.php';

$db = Database::getInstance()->getConnection();
$userModel = new User($db);
$message = '';
$error = '';

// Pārbaudīt, vai jau ir admin
$adminExists = false;
$stmt = $db->query('SELECT COUNT(*) FROM users WHERE role = "admin"');
$adminCount = (int)$stmt->fetchColumn();
$adminExists = $adminCount > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        // Izveidot jaunu admin
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $repeat = trim($_POST['repeat_password'] ?? '');
        
        if (!$username) {
            $error = 'Lietotājvārds ir obligāts.';
        } elseif (!$email) {
            $error = 'E-pasts ir obligāts.';
        } elseif (!$password) {
            $error = 'Parole ir obligāta.';
        } elseif ($password !== $repeat) {
            $error = 'Paroles nesakrīt.';
        } elseif (strlen($password) < 8) {
            $error = 'Parolei jābūt vismaz 8 rakstzīmes garai.';
        } elseif ($userModel->findByUsername($username)) {
            $error = 'Lietotājvārds jau tiek izmantots.';
        } elseif ($userModel->findByEmail($email)) {
            $error = 'E-pasts jau reģistrēts.';
        } else {
            $result = $userModel->register($username, $email, $password, 'admin');
            if ($result['success']) {
                $message = "✅ Admin lietotājs '$username' ir sekmīgi izveidots!";
                $adminExists = true;
            } else {
                $error = implode(', ', $result['errors']);
            }
        }
    } elseif ($action === 'make_admin') {
        // Padarīt esošu lietotāju par admin
        $username = trim($_POST['username'] ?? '');
        
        if (!$username) {
            $error = 'Lietotājvārds ir obligāts.';
        } else {
            $user = $userModel->findByUsername($username);
            if (!$user) {
                $error = "Lietotājs '$username' nav atrasts.";
            } else {
                try {
                    $stmt = $db->prepare('UPDATE users SET role = :role WHERE id = :id');
                    $stmt->execute([':role' => 'admin', ':id' => $user['id']]);
                    $message = "✅ Lietotājs '$username' ir sekmīgi padarīts par administratoru!";
                    $adminExists = true;
                } catch (Exception $e) {
                    $error = 'Kļūda atjaunināšanā: ' . $e->getMessage();
                }
            }
        }
    }
}

// Iegūt visus lietotājus
$stmt = $db->query('SELECT id, username, email, role FROM users ORDER BY id ASC');
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>Admin Iestatīšana</title>
    <style>
        .setup-container {
            max-width: 600px;
            margin: 0 auto;
        }
        .admin-status {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
            font-weight: bold;
        }
        .admin-status.exists {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .admin-status.missing {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .section {
            background-color: #f9f9f9;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .section h3 {
            margin-top: 0;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .users-list {
            margin-top: 15px;
        }
        .user-item {
            background-color: white;
            padding: 10px;
            margin-bottom: 8px;
            border-radius: 4px;
            border-left: 3px solid #4CAF50;
        }
        .user-role {
            font-size: 0.85em;
            font-weight: bold;
            color: #666;
        }
        .admin-badge {
            background-color: #4CAF50;
            color: white;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 0.8em;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <main class="page-card setup-container">
        <h1>⚙️ Admin Iestatīšana</h1>
        
        <div class="admin-status <?php echo $adminExists ? 'exists' : 'missing'; ?>">
            <?php if ($adminExists): ?>
                ✅ Admin lietotājs jau eksistē - jūs varat pierakstīties!
            <?php else: ?>
                ⚠️ Nav admin lietotāja - jāizveido vai jāpadarī lietotājs par administratoru
            <?php endif; ?>
        </div>

        <?php if ($message): ?>
            <div class="success-message"><?php echo htmlspecialchars($message, ENT_QUOTES); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
        <?php endif; ?>

        <!-- Opcija 1: Izveidot jaunu Admin -->
        <div class="section">
            <h3>Opcija 1️⃣: Izveidot Jaunu Admin</h3>
            <form method="post" novalidate>
                <input type="hidden" name="action" value="create">
                <label>Lietotājvārds
                    <input type="text" name="username" required>
                </label>
                <label>E-pasts
                    <input type="email" name="email" required>
                </label>
                <label>Parole (min. 8 rakstzīmes)
                    <input type="password" name="password" required>
                </label>
                <label>Atkārtot paroli
                    <input type="password" name="repeat_password" required>
                </label>
                <button type="submit">Izveidot Admin</button>
            </form>
        </div>

        <!-- Opcija 2: Padarīt Esošu Lietotāju par Admin -->
        <?php if (!empty($users)): ?>
        <div class="section">
            <h3>Opcija 2️⃣: Padarīt Esošu Lietotāju par Admin</h3>
            <form method="post" novalidate>
                <input type="hidden" name="action" value="make_admin">
                <label>Izvēlieties lietotāju
                    <select name="username" required>
                        <option value="">-- Izvēlēties --</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>">
                                <?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>
                                <?php echo $user['role'] === 'admin' ? ' (jau admin)' : ''; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <button type="submit">Padarīt Admin</button>
            </form>
        </div>

        <!-- Esošie Lietotāji -->
        <div class="section">
            <h3>👥 Esošie Lietotāji</h3>
            <div class="users-list">
                <?php foreach ($users as $user): ?>
                    <div class="user-item">
                        <strong><?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?></strong>
                        <br>
                        <small><?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?></small>
                        <br>
                        <span class="user-role">
                            <?php echo $user['role'] === 'admin' ? '<span class="admin-badge">ADMIN</span>' : 'Lietotājs'; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="button-group">
            <a class="button-link" href="login.php">← Uz Login</a>
        </div>
    </main>
</body>
</html>
