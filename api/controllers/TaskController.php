<?php

declare(strict_types=1);

class TaskController
{
    public function __construct(private Task $taskModel)
    {
    }

    public function index(int $userId): void
    {
        $status = $_GET['status'] ?? null;
        $subject = $_GET['subject'] ?? null;
        $search = $_GET['search'] ?? null;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = min(50, max(1, (int)($_GET['limit'] ?? 10)));

        $result = $this->taskModel->listByUser($userId, $status, $subject, $search, $page, $limit);
        $stats = $this->taskModel->stats($userId);

        jsonResponse([
            'success' => true,
            'data' => $result['tasks'],
            'pagination' => $result['pagination'],
            'stats' => $stats,
        ]);
    }

    public function store(int $userId, array $data): void
    {
        $errors = $this->validateTaskPayload($data, false);
        if (!empty($errors)) {
            jsonResponse(['success' => false, 'errors' => $errors], 422);
        }

        $taskId = $this->taskModel->create($userId, $data);

        jsonResponse([
            'success' => true,
            'message' => 'Task created successfully.',
            'task_id' => $taskId,
        ], 201);
    }

    public function update(int $taskId, int $userId, array $data): void
    {
        $errors = $this->validateTaskPayload($data, true);
        if (!empty($errors)) {
            jsonResponse(['success' => false, 'errors' => $errors], 422);
        }

        $updated = $this->taskModel->update($taskId, $userId, $data);
        if (!$updated) {
            jsonResponse(['success' => false, 'message' => 'Task not found or no changes applied.'], 404);
        }

        jsonResponse(['success' => true, 'message' => 'Task updated successfully.']);
    }

    public function destroy(int $taskId, int $userId): void
    {
        $deleted = $this->taskModel->delete($taskId, $userId);
        if (!$deleted) {
            jsonResponse(['success' => false, 'message' => 'Task not found.'], 404);
        }

        jsonResponse(['success' => true, 'message' => 'Task deleted successfully.']);
    }

    private function validateTaskPayload(array $data, bool $isUpdate): array
    {
        $required = ['title', 'subject', 'priority', 'due_date'];
        if ($isUpdate) {
            $required[] = 'status';
        }

        $errors = requireFields($data, $required);

        if (isset($data['priority']) && !in_array($data['priority'], ['low', 'medium', 'high'], true)) {
            $errors['priority'] = 'Priority must be low, medium, or high.';
        }

        $status = $data['status'] ?? 'pending';
        if (!in_array($status, ['pending', 'completed'], true)) {
            $errors['status'] = 'Status must be pending or completed.';
        }

        if (!empty($data['due_date'])) {
            $date = DateTime::createFromFormat('Y-m-d', (string)$data['due_date']);
            $isValidDate = $date && $date->format('Y-m-d') === $data['due_date'];
            if (!$isValidDate) {
                $errors['due_date'] = 'Due date must be in YYYY-MM-DD format.';
            }
        }

        if (!empty($data['title']) && mb_strlen(trim((string)$data['title'])) > 255) {
            $errors['title'] = 'Title cannot exceed 255 characters.';
        }

        if (!empty($data['subject']) && mb_strlen(trim((string)$data['subject'])) > 100) {
            $errors['subject'] = 'Subject cannot exceed 100 characters.';
        }

        return $errors;
    }
}
