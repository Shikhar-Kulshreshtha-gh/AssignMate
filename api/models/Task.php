<?php

declare(strict_types=1);

class Task
{
    public function __construct(private PDO $db)
    {
    }

    public function listByUser(
        int $userId,
        ?string $status,
        ?string $subject,
        ?string $search,
        int $page,
        int $limit
    ): array {
        $offset = ($page - 1) * $limit;

        $conditions = ['user_id = :user_id'];
        $params = [':user_id' => $userId];

        if ($status !== null && in_array($status, ['pending', 'completed'], true)) {
            $conditions[] = 'status = :status';
            $params[':status'] = $status;
        }

        if ($subject !== null && $subject !== '') {
            $conditions[] = 'subject = :subject';
            $params[':subject'] = $subject;
        }

        if ($search !== null && $search !== '') {
            $conditions[] = '(title LIKE :search OR description LIKE :search OR subject LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        $whereClause = implode(' AND ', $conditions);

        $countStmt = $this->db->prepare("SELECT COUNT(*) as total FROM tasks WHERE {$whereClause}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetch()['total'];

        $sql = "SELECT id, title, description, subject, priority, due_date, status, created_at
                FROM tasks
                WHERE {$whereClause}
                ORDER BY
                    CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END,
                    due_date ASC,
                    created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'tasks' => $stmt->fetchAll(),
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => (int)ceil($total / max($limit, 1)),
            ],
        ];
    }

    public function create(int $userId, array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO tasks (user_id, title, description, subject, priority, due_date, status, created_at)
             VALUES (:user_id, :title, :description, :subject, :priority, :due_date, :status, NOW())'
        );
        $stmt->execute([
            ':user_id' => $userId,
            ':title' => $data['title'],
            ':description' => $data['description'] ?? null,
            ':subject' => $data['subject'],
            ':priority' => $data['priority'],
            ':due_date' => $data['due_date'],
            ':status' => $data['status'] ?? 'pending',
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $taskId, int $userId, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE tasks
             SET title = :title,
                 description = :description,
                 subject = :subject,
                 priority = :priority,
                 due_date = :due_date,
                 status = :status
             WHERE id = :id AND user_id = :user_id'
        );

        $stmt->execute([
            ':id' => $taskId,
            ':user_id' => $userId,
            ':title' => $data['title'],
            ':description' => $data['description'] ?? null,
            ':subject' => $data['subject'],
            ':priority' => $data['priority'],
            ':due_date' => $data['due_date'],
            ':status' => $data['status'],
        ]);

        return $stmt->rowCount() > 0;
    }

    public function delete(int $taskId, int $userId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM tasks WHERE id = :id AND user_id = :user_id');
        $stmt->execute([
            ':id' => $taskId,
            ':user_id' => $userId,
        ]);

        return $stmt->rowCount() > 0;
    }

    public function stats(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN status = 'pending' AND due_date < CURDATE() THEN 1 ELSE 0 END) AS overdue
             FROM tasks
             WHERE user_id = :user_id"
        );
        $stmt->execute([':user_id' => $userId]);

        $stats = $stmt->fetch() ?: [];

        return [
            'total' => (int)($stats['total'] ?? 0),
            'completed' => (int)($stats['completed'] ?? 0),
            'pending' => (int)($stats['pending'] ?? 0),
            'overdue' => (int)($stats['overdue'] ?? 0),
        ];
    }
}
