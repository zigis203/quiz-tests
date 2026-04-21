<?php
/**
 * EXAMPLE PAGE TEMPLATE
 * 
 * This is a template showing best practices for creating new pages
 * in the Quiz System. Copy this file and customize it.
 * 
 * File: views/example_page.php
 */

// Step 1: Load configuration (always first)
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/auth_middleware.php';

// Step 2: Check authentication (protect the page)
AuthMiddleware::requireLogin();  // Only logged-in users
// OR
// AuthMiddleware::requireAdmin();  // Only admins

// Step 3: Get database connection and models
$db = Database::getInstance()->getConnection();
$currentUser = AuthMiddleware::getCurrentUser();

// Step 4: Handle form submissions (if any)
$message = '';
$error = '';
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token here (important!)
    // if (!validatCSRFToken($_POST['csrf_token'])) { ... }
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'do_something':
            try {
                // Get posted data
                $input = trim($_POST['example_field'] ?? '');
                
                // Validate
                if (empty($input)) {
                    $error = 'Field is required';
                    break;
                }
                
                // Process (use model)
                $topicModel = new Topic($db);
                // $topicModel->addTopic($input, 'Description');
                
                $message = 'Action completed successfully!';
            } catch (Exception $e) {
                $error = 'Error: ' . htmlspecialchars($e->getMessage());
            }
            break;
            
        default:
            $error = 'Unknown action';
    }
}

// Step 5: Load data for the page
try {
    // Example: Load all topics
    $topicModel = new Topic($db);
    $topics = $topicModel->getAll();
} catch (Exception $e) {
    $error = 'Failed to load data: ' . htmlspecialchars($e->getMessage());
    $topics = [];
}

?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/style.css">
    <title>Example Page</title>
</head>
<body>
    <header>
        <h1>Quiz System - Example Page</h1>
        <nav>
            <a href="topics.php">Topics</a>
            <a href="history.php">History</a>
            <?php if (AuthMiddleware::isAdmin()): ?>
                <a href="admin.php">Admin</a>
            <?php endif; ?>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main class="container">
        <!-- Welcome message -->
        <section class="welcome">
            <h2>Welcome, <?= htmlspecialchars($currentUser['username']) ?></h2>
            <p>Role: <strong><?= htmlspecialchars($currentUser['role']) ?></strong></p>
        </section>

        <!-- Display messages -->
        <?php if ($message): ?>
            <div class="success-message">
                ✓ <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error-message">
                ✗ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Example form -->
        <section class="form-section">
            <h3>Example Form</h3>
            <form action="example_page.php" method="post" novalidate>
                <input type="hidden" name="action" value="do_something">
                
                <div class="form-group">
                    <label for="example_field">Example Field:</label>
                    <input 
                        type="text" 
                        id="example_field" 
                        name="example_field" 
                        required
                        placeholder="Enter something"
                    >
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="4"
                        placeholder="Optional description"
                    ></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="reset" class="btn btn-secondary">Clear</button>
            </form>
        </section>

        <!-- Example data display -->
        <section class="data-section">
            <h3>Available Topics (<?= count($topics) ?>)</h3>
            
            <?php if (empty($topics)): ?>
                <p class="no-data">No topics available yet.</p>
            <?php else: ?>
                <ul class="topics-list">
                    <?php foreach ($topics as $topic): ?>
                        <li>
                            <strong><?= htmlspecialchars($topic['name']) ?></strong>
                            <p><?= htmlspecialchars($topic['description'] ?? 'No description') ?></p>
                            
                            <?php if (AuthMiddleware::isAdmin()): ?>
                                <div class="admin-actions">
                                    <a href="edit_topic.php?id=<?= $topic['id'] ?>">Edit</a>
                                    <a href="delete_topic.php?id=<?= $topic['id'] ?>" onclick="return confirm('Delete this topic?')">Delete</a>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <!-- Example table -->
        <section class="table-section">
            <h3>Topics Table View</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Created</th>
                        <?php if (AuthMiddleware::isAdmin()): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topics as $topic): ?>
                        <tr>
                            <td><?= htmlspecialchars($topic['id']) ?></td>
                            <td><?= htmlspecialchars($topic['name']) ?></td>
                            <td><?= htmlspecialchars($topic['description'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($topic['created_at']) ?></td>
                            <?php if (AuthMiddleware::isAdmin()): ?>
                                <td>
                                    <a href="edit_topic.php?id=<?= $topic['id'] ?>">Edit</a> |
                                    <a href="delete_topic.php?id=<?= $topic['id'] ?>">Delete</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 Quiz System. All rights reserved.</p>
    </footer>

    <script src="../public/js/app.js"></script>
    <script src="../public/js/validation.js"></script>
</body>
</html>

<?php
/**
 * ============================================================================
 * KEY POINTS FOR CREATING NEW PAGES
 * ============================================================================
 * 
 * 1. ALWAYS REQUIRE CONFIG AND AUTH FIRST
 *    require_once __DIR__ . '/../config/config.php';
 *    require_once __DIR__ . '/../config/auth_middleware.php';
 * 
 * 2. CHECK PERMISSIONS
 *    AuthMiddleware::requireLogin();  // For user pages
 *    AuthMiddleware::requireAdmin();  // For admin pages
 * 
 * 3. ESCAPE ALL OUTPUT
 *    echo htmlspecialchars($variable);  // Always!
 * 
 * 4. USE PREPARED STATEMENTS
 *    Never concatenate variables directly into SQL:
 *    BAD:  "SELECT * FROM users WHERE id = $id"
 *    GOOD: $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
 *          $stmt->execute([':id' => $id]);
 * 
 * 5. HANDLE ERRORS GRACEFULLY
 *    try {
 *        // Database operations
 *    } catch (Exception $e) {
 *        $error = $e->getMessage();
 *    }
 * 
 * 6. USE MODELS FOR DATABASE ACCESS
 *    $userModel = new User($db);
 *    $user = $userModel->findById(1);
 * 
 * 7. CHECK AUTHENTICATION STATUS
 *    if (AuthMiddleware::isLoggedIn()) { ... }
 *    if (AuthMiddleware::isAdmin()) { ... }
 * 
 * 8. GET CURRENT USER INFO
 *    $user = AuthMiddleware::getCurrentUser();
 *    echo $user['username'];
 *    echo $user['role'];  // 'user' or 'admin'
 * 
 * 9. HANDLE FORMS SAFELY
 *    Always validate form input:
 *    - Check if fields are not empty
 *    - Validate email format with filter_var()
 *    - Validate numbers with is_numeric()
 *    - Use models for complex validation
 * 
 * 10. USE MEANINGFUL VARIABLE NAMES
 *    $currentUser = AuthMiddleware::getCurrentUser();
 *    $topicModel = new Topic($db);
 *    $topics = $topicModel->getAll();
 * 
 * ============================================================================
 * EXAMPLE: PROTECTED ADMIN PAGE
 * ============================================================================
 * 
 * <?php
 * require_once __DIR__ . '/../config/config.php';
 * require_once __DIR__ . '/../config/auth_middleware.php';
 * 
 * // Only admin can access
 * AuthMiddleware::requireAdmin();
 * 
 * $db = Database::getInstance()->getConnection();
 * $adminUser = AuthMiddleware::getCurrentUser();
 * 
 * // Admin-specific logic here
 * ?>
 * 
 * ============================================================================
 * EXAMPLE: FORM WITH DATABASE INSERT
 * ============================================================================
 * 
 * <?php
 * if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 *     $name = trim($_POST['name'] ?? '');
 *     $email = trim($_POST['email'] ?? '');
 * 
 *     // Validate
 *     if (empty($name) || empty($email)) {
 *         $error = 'All fields required';
 *     } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
 *         $error = 'Invalid email format';
 *     } else {
 *         // Process
 *         $userModel = new User($db);
 *         $result = $userModel->register($name, $email, 'password123');
 *         if ($result['success']) {
 *             $message = 'User created successfully!';
 *         } else {
 *             $error = implode(', ', $result['errors']);
 *         }
 *     }
 * }
 * ?>
 * 
 * ============================================================================
 */
