<?php
class User
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function register(string $username, string $email, string $password, string $role = 'user'): array
    {
        $errors = [];
        if (!$username || !$email || !$password) {
            $errors[] = 'Visi lauki ir obligāti.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'E-pasta adrese nav derīga.';
        }

        if (strlen($password) < 8) {
            $errors[] = 'Parolei jābūt vismaz 8 rakstzīmes garai.';
        }

        if ($this->findByUsername($username)) {
            $errors[] = 'Lietotājvārds jau tiek izmantots.';
        }

        if ($this->findByEmail($email)) {
            $errors[] = 'E-pasts jau reģistrēts.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $stmt = $this->db->prepare('INSERT INTO users (username, email, password_hash, role) VALUES (:username, :email, :password_hash, :role)');
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ':role' => $role,
        ]);

        return ['success' => true, 'errors' => []];
    }

    public function authenticate(string $username, string $password): ?array
    {
        $user = $this->findByUsername($username);
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return null;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function login(array $user): void
    {
        $_SESSION['user_id'] = $user['id'];
    }

    public static function logout(): void
    {
        unset($_SESSION['user_id']);
    }

    public static function getCurrentUser(PDO $db): ?array
    {
        if (empty($_SESSION['user_id'])) {
            return null;
        }
        $userModel = new User($db);
        return $userModel->findById((int)$_SESSION['user_id']);
    }
}
