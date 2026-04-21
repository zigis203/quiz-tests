<?php
/**
 * Database Connection & Configuration Guide
 * 
 * This file explains how the database connection works and how to use it.
 */

// ============================================================================
// 1. AUTOMATIC CONNECTION & SCHEMA INITIALIZATION
// ============================================================================
// The Database class (in config/Database.php) is a singleton that:
// - Connects to MySQL using PDO
// - Automatically creates all tables on first connection
// - Seeds initial data (5 topics with 15 questions each)
// 
// To get a connection in any file:
// 
//     require_once __DIR__ . '/config/config.php';
//     $db = Database::getInstance()->getConnection();
// 
// The config.php file handles:
// - Starting the session
// - Autoloading model classes
// - Defining database credentials
// ============================================================================

// ============================================================================
// 2. DATABASE CREDENTIALS
// ============================================================================
// Edit config/config.php to match your local environment:
//
//     define('DB_HOST', '127.0.0.1');    // localhost or remote host
//     define('DB_NAME', 'quiz_app');     // database name
//     define('DB_USER', 'quiz_user');    // MySQL user
//     define('DB_PASS', 'quiz_pass');    // MySQL password
//
// For local development (Laragon):
// - Host: 127.0.0.1 or localhost
// - User: root (default)
// - Password: (empty by default)
// - Port: 3306 (default)
// ============================================================================

// ============================================================================
// 3. USING MODELS IN YOUR CODE
// ============================================================================
/*

Example 1: User Registration
-------------------------------
require_once __DIR__ . '/config/config.php';

$db = Database::getInstance()->getConnection();
$userModel = new User($db);

$result = $userModel->register(
    username: 'john_doe',
    email: 'john@example.com',
    password: 'secure_password_123',
    role: 'user'
);

if ($result['success']) {
    echo "Registration successful!";
} else {
    foreach ($result['errors'] as $error) {
        echo "Error: " . htmlspecialchars($error) . "\n";
    }
}


Example 2: User Authentication
-------------------------------
require_once __DIR__ . '/config/config.php';

$db = Database::getInstance()->getConnection();
$userModel = new User($db);

$user = $userModel->authenticate('john_doe', 'secure_password_123');

if ($user) {
    // Login successful
    User::login($user);  // Store user data in $_SESSION
    header('Location: topics.php');
} else {
    // Login failed
    echo "Invalid username or password";
}


Example 3: Create a Topic (Admin Only)
-------------------------------
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/auth_middleware.php';

AuthMiddleware::requireAdmin();  // Only admin can access

$db = Database::getInstance()->getConnection();
$topicModel = new Topic($db);

$topicId = $topicModel->addTopic(
    name: 'Advanced Physics',
    description: 'Quantum mechanics and relativity'
);

echo "Topic created with ID: " . $topicId;


Example 4: Get Random Questions for Quiz
-------------------------------
require_once __DIR__ . '/config/config.php';

$db = Database::getInstance()->getConnection();
$questionModel = new Question($db);

// Get 15 random questions from a topic
$questions = $questionModel->getByTopic(topicId: 3, limit: 15);

foreach ($questions as $q) {
    echo $q['question_text'] . "\n";
    // Questions already have answers loaded as $q['answers']
}


Example 5: Save Quiz Result
-------------------------------
require_once __DIR__ . '/config/config.php';

$db = Database::getInstance()->getConnection();
$resultModel = new QuizResult($db);

$resultModel->saveResult(
    userId: $_SESSION['user_id'],
    topicId: 3,
    score: 12,  // Correct answers
    total: 15   // Total questions
);


Example 6: Get User's High Scores
-------------------------------
require_once __DIR__ . '/config/config.php';

$db = Database::getInstance()->getConnection();
$resultModel = new QuizResult($db);

$scores = $resultModel->getUserResults(userId: $_SESSION['user_id']);

foreach ($scores as $score) {
    $percentage = round(($score['score'] / $score['total']) * 100);
    echo "Topic: {$score['topic_name']} - Score: {$score['score']}/{$score['total']} ({$percentage}%)\n";
}

*/

// ============================================================================
// 4. PROTECTING PAGES WITH AUTHENTICATION
// ============================================================================
/*

Add this to the TOP of any page that requires login:

    require_once __DIR__ . '/../config/auth_middleware.php';
    AuthMiddleware::requireLogin();  // Redirect to login if not logged in

Add this to the TOP of any page restricted to admins:

    require_once __DIR__ . '/../config/auth_middleware.php';
    AuthMiddleware::requireAdmin();  // Redirect with 403 error if not admin

Example: views/quiz.php
------------------------
<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/auth_middleware.php';

// Only logged-in users can access this page
AuthMiddleware::requireLogin();

// Get the current user
$user = AuthMiddleware::getCurrentUser();

// Load the quiz
$db = Database::getInstance()->getConnection();
$questionModel = new Question($db);
$questions = $questionModel->getByTopic($_GET['topic_id'], 15);
?>

<h1>Quiz for <?= htmlspecialchars($user['username']) ?></h1>
<!-- Rest of your HTML -->

*/

// ============================================================================
// 5. MYSQL USER SETUP (For Production)
// ============================================================================
/*

If you need to create a dedicated MySQL user:

CREATE USER 'quiz_user'@'localhost' IDENTIFIED BY 'quiz_pass';
GRANT ALL PRIVILEGES ON quiz_app.* TO 'quiz_user'@'localhost';
FLUSH PRIVILEGES;

To verify the user was created:
SELECT USER, HOST FROM mysql.user;

*/

// ============================================================================
// 6. TROUBLESHOOTING DATABASE ISSUES
// ============================================================================
/*

Problem: "SQLSTATE[HY000]: General error: 1030 Got error..."
Solution: Check MySQL is running and credentials are correct

Problem: "SQLSTATE[HY000] [2002] No such file or directory"
Solution: 
  - MySQL socket issue
  - Add charset to DSN: mysql:host=127.0.0.1;port=3306;charset=utf8mb4
  - Don't use 'localhost', use '127.0.0.1' instead

Problem: "Access denied for user 'quiz_user'@'localhost'"
Solution:
  - Check password is correct
  - Verify user exists: mysql -u root -p -e "SELECT USER FROM mysql.user;"
  - Reset password if needed

Problem: Database tables not created on first access
Solution:
  - Check file permissions on data directory
  - Ensure database exists: CREATE DATABASE quiz_app;
  - Check Database.php initializeSchema() method runs

Problem: Sessions not persisting
Solution:
  - Verify session_start() is called in config.php (it is)
  - Check php.ini session.save_path is writable
  - Clear browser cookies
  - Check if headers are sent before session_start()

*/

// ============================================================================
// 7. QUICK DATABASE CHECKS
// ============================================================================
/*

To verify your setup is working, run this SQL:

-- Check database exists
SHOW DATABASES LIKE 'quiz_app';

-- Check all tables
USE quiz_app;
SHOW TABLES;

-- Check users table has admin account
SELECT id, username, role FROM users LIMIT 1;

-- Count questions per topic
SELECT t.name, COUNT(q.id) as question_count 
FROM topics t 
LEFT JOIN questions q ON t.id = q.topic_id 
GROUP BY t.id;

-- Check quiz results/high scores
SELECT u.username, t.name, qr.score, qr.total, qr.created_at 
FROM quiz_results qr
JOIN users u ON qr.user_id = u.id
JOIN topics t ON qr.topic_id = t.id
ORDER BY qr.created_at DESC LIMIT 10;

*/

// ============================================================================
// 8. BACKUP & RESTORE
// ============================================================================
/*

Backup database:
    mysqldump -u quiz_user -p quiz_app > quiz_app_backup.sql

Restore database:
    mysql -u quiz_user -p quiz_app < quiz_app_backup.sql

*/

// ============================================================================
// 9. PDO PREPARED STATEMENTS EXAMPLES
// ============================================================================
/*

All queries in this project use prepared statements to prevent SQL injection:

Example - SELECT:
    $stmt = $db->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();

Example - INSERT:
    $stmt = $db->prepare('INSERT INTO users (username, email) VALUES (:username, :email)');
    $stmt->execute([
        ':username' => $username,
        ':email' => $email
    ]);

Example - UPDATE:
    $stmt = $db->prepare('UPDATE topics SET name = :name WHERE id = :id');
    $stmt->execute([
        ':name' => $newName,
        ':id' => $topicId
    ]);

Example - DELETE:
    $stmt = $db->prepare('DELETE FROM topics WHERE id = :id');
    $stmt->execute([':id' => $topicId]);

Key Security Features:
- Values are separated from SQL code
- User input cannot modify query structure
- Prevents SQL injection attacks
- Works with all data types (strings, numbers, booleans)

*/
