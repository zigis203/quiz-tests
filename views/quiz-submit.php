<?php
require_once __DIR__ . '/../config/config.php';
$db = Database::getInstance()->getConnection();
$currentUser = User::getCurrentUser($db);
if (!$currentUser) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Nav autorizēts lietotājs.']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
$selectedAnswers = is_array($payload['answers'] ?? null) ? $payload['answers'] : [];

if (empty($_SESSION['current_quiz']) || empty($_SESSION['current_quiz']['topic_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nav aktīvs tests.']);
    exit;
}

$questionIds = array_column($_SESSION['current_quiz']['questions'], 'id');
$filteredAnswers = array_filter($selectedAnswers, function ($answerId) use ($questionIds) {
    return is_numeric($answerId);
});

if (empty($filteredAnswers)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nav atlasīta neviena atbilde.']);
    exit;
}

$placeholders = implode(',', array_fill(0, count($filteredAnswers), '?'));
$questionPlaceholders = implode(',', array_fill(0, count($questionIds), '?'));
$sql = sprintf(
    'SELECT COUNT(*) FROM answers WHERE is_correct = 1 AND id IN (%s) AND question_id IN (%s)',
    $placeholders,
    $questionPlaceholders
);
$stmt = $db->prepare($sql);
$params = array_merge($filteredAnswers, $questionIds);
$stmt->execute($params);
$correctCount = (int)$stmt->fetchColumn();

$total = count($questionIds);
$resultModel = new QuizResult($db);
$resultId = $resultModel->saveResult($currentUser['id'], $_SESSION['current_quiz']['topic_id'], $correctCount, $total);
$_SESSION['last_result_id'] = $resultId;
unset($_SESSION['current_quiz']);

header('Content-Type: application/json');
echo json_encode(['success' => true, 'score' => $correctCount, 'total' => $total]);
