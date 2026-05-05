<?php

declare(strict_types=1);

class AuthController
{
    public function __construct(private User $userModel)
    {
    }

    public function register(array $data): void
    {
        $errors = requireFields($data, ['name', 'email', 'password']);

        if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'A valid email is required.';
        }

        if (strlen((string)($data['password'] ?? '')) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }

        if (!empty($errors)) {
            jsonResponse(['success' => false, 'errors' => $errors], 422);
        }

        $existing = $this->userModel->findByEmail(trim($data['email']));
        if ($existing) {
            jsonResponse(['success' => false, 'message' => 'Email already in use.'], 409);
        }

        $hashed = password_hash($data['password'], PASSWORD_BCRYPT);
        $userId = $this->userModel->create(trim($data['name']), trim($data['email']), $hashed);

        $_SESSION['user_id'] = $userId;

        $user = $this->userModel->findPublicById($userId);
        jsonResponse(['success' => true, 'message' => 'Registration successful.', 'user' => $user], 201);
    }

    public function login(array $data): void
    {
        $errors = requireFields($data, ['email', 'password']);
        if (!empty($errors)) {
            jsonResponse(['success' => false, 'errors' => $errors], 422);
        }

        $user = $this->userModel->findByEmail(trim($data['email']));
        if (!$user || !password_verify($data['password'], $user['password'])) {
            jsonResponse(['success' => false, 'message' => 'Invalid credentials.'], 401);
        }

        $_SESSION['user_id'] = (int)$user['id'];
        $publicUser = $this->userModel->findPublicById((int)$user['id']);

        jsonResponse(['success' => true, 'message' => 'Login successful.', 'user' => $publicUser]);
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();

        jsonResponse(['success' => true, 'message' => 'Logged out successfully.']);
    }

    public function me(): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            jsonResponse(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $user = $this->userModel->findPublicById((int)$userId);
        if (!$user) {
            jsonResponse(['success' => false, 'message' => 'User not found.'], 404);
        }

        jsonResponse(['success' => true, 'user' => $user]);
    }
}
