<?php
/**
 * CLI skripts, lai padarītu lietotāju par administratoru
 * Izmantošana: php make-admin.php <username>
 * Piemērs: php make-admin.php john
 */

require_once __DIR__ . '/config/config.php';

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "Šis skripts tik nozīmē darbam no komandrindas!";
    exit(1);
}

if ($argc < 2) {
    echo "Izmantošana: php make-admin.php <lietotajvardsn>\n";
    echo "Piemērs: php make-admin.php john\n";
    exit(1);
}

$username = $argv[1];
$db = Database::getInstance()->getConnection();
$userModel = new User($db);

$user = $userModel->findByUsername($username);
if (!$user) {
    echo "❌ Kļūda: Lietotājs '$username' nav atrasts.\n";
    exit(1);
}

try {
    $stmt = $db->prepare('UPDATE users SET role = :role WHERE id = :id');
    $stmt->execute([':role' => 'admin', ':id' => $user['id']]);
    echo "✅ Lietotājs '$username' ir sekmīgi padarīts par administratoru!\n";
    echo "   Var sākt pierakstīties ar admin paneli.\n";
} catch (Exception $e) {
    echo "❌ Kļūda atjaunināšanā: " . $e->getMessage() . "\n";
    exit(1);
}
