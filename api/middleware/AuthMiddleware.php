<?php

declare(strict_types=1);

class AuthMiddleware
{
    public static function requireAuth(): int
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId || !is_numeric($userId)) {
            jsonResponse(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        return (int)$userId;
    }
}
