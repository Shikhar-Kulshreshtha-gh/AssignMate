<?php

declare(strict_types=1);

class User
{
    public function __construct(private PDO $db)
    {
    }

    public function create(string $name, string $email, string $hashedPassword): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password, created_at) VALUES (:name, :email, :password, NOW())'
        );
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashedPassword,
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT id, name, email, password FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function findPublicById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, name, email, created_at FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();

        return $user ?: null;
    }
}
