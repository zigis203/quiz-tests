<?php
class Question
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getRandomQuestions(int $topicId, int $limit = 15): array
    {
        $stmt = $this->db->prepare('SELECT id, question_text FROM questions WHERE topic_id = :topic_id ORDER BY RAND() LIMIT :limit');
        $stmt->bindValue(':topic_id', $topicId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $questions = $stmt->fetchAll();

        foreach ($questions as &$question) {
            $question['answers'] = $this->getAnswers($question['id']);
        }
        return $questions;
    }

    public function getAnswers(int $questionId): array
    {
        $stmt = $this->db->prepare('SELECT id, answer_text FROM answers WHERE question_id = :question_id ORDER BY RAND()');
        $stmt->execute([':question_id' => $questionId]);
        return $stmt->fetchAll();
    }

    public function getAnswersWithCorrect(int $questionId): array
    {
        $stmt = $this->db->prepare('SELECT id, answer_text, is_correct FROM answers WHERE question_id = :question_id ORDER BY id ASC');
        $stmt->execute([':question_id' => $questionId]);
        return $stmt->fetchAll();
    }

    public function addQuestionWithAnswers(int $topicId, string $questionText, array $answers): int
    {
        $stmt = $this->db->prepare('INSERT INTO questions (topic_id, question_text) VALUES (:topic_id, :question_text)');
        $stmt->execute([':topic_id' => $topicId, ':question_text' => $questionText]);
        $questionId = (int)$this->db->lastInsertId();

        foreach ($answers as $answer) {
            $stmt = $this->db->prepare('INSERT INTO answers (question_id, answer_text, is_correct) VALUES (:question_id, :answer_text, :is_correct)');
            $stmt->execute([
                ':question_id' => $questionId,
                ':answer_text' => $answer['text'],
                ':is_correct' => $answer['correct'] ? 1 : 0,
            ]);
        }

        return $questionId;
    }

    public function getQuestionById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM questions WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $question = $stmt->fetch();
        if ($question) {
            $question['answers'] = $this->getAnswersWithCorrect($id);
        }
        return $question ?: null;
    }

    public function getQuestionsByTopic(int $topicId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM questions WHERE topic_id = :topic_id ORDER BY id ASC');
        $stmt->execute([':topic_id' => $topicId]);
        return $stmt->fetchAll();
    }

    public function updateQuestion(int $id, string $questionText): bool
    {
        $stmt = $this->db->prepare('UPDATE questions SET question_text = :question_text WHERE id = :id');
        return $stmt->execute([':id' => $id, ':question_text' => $questionText]);
    }

    public function deleteQuestion(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM questions WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function updateAnswer(int $id, string $answerText, bool $isCorrect): bool
    {
        $stmt = $this->db->prepare('UPDATE answers SET answer_text = :answer_text, is_correct = :is_correct WHERE id = :id');
        return $stmt->execute([':id' => $id, ':answer_text' => $answerText, ':is_correct' => $isCorrect ? 1 : 0]);
    }

    public function deleteAnswer(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM answers WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
