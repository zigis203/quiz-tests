<?php
class Topic
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM topics ORDER BY name ASC');
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM topics WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $topic = $stmt->fetch();
        return $topic ?: null;
    }

    public function addTopic(string $name, string $description = null): int
    {
        $stmt = $this->db->prepare('INSERT INTO topics (name, description) VALUES (:name, :description)');
        $stmt->execute([':name' => $name, ':description' => $description]);
        return (int)$this->db->lastInsertId();
    }

    public function updateTopic(int $id, string $name, string $description = null): bool
    {
        $stmt = $this->db->prepare('UPDATE topics SET name = :name, description = :description WHERE id = :id');
        return $stmt->execute([':id' => $id, ':name' => $name, ':description' => $description]);
    }

    public function deleteTopic(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM topics WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
