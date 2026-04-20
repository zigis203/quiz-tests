<?php
class QuizResult
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function saveResult(int $userId, int $topicId, int $score, int $total): int
    {
        $stmt = $this->db->prepare('INSERT INTO quiz_results (user_id, topic_id, score, total) VALUES (:user_id, :topic_id, :score, :total)');
        $stmt->execute([
            ':user_id' => $userId,
            ':topic_id' => $topicId,
            ':score' => $score,
            ':total' => $total,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getLatestForUser(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT qr.*, t.name AS topic_name FROM quiz_results qr JOIN topics t ON qr.topic_id = t.id WHERE qr.user_id = :user_id ORDER BY qr.created_at DESC LIMIT 1');
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getHistoryForUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT qr.*, t.name AS topic_name FROM quiz_results qr JOIN topics t ON qr.topic_id = t.id WHERE qr.user_id = :user_id ORDER BY qr.created_at DESC');
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }
}
