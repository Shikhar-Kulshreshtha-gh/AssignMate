<?php

declare(strict_types=1);

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Task.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/TaskController.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';

try {
    $db = Database::getConnection();
} catch (Throwable $e) {
    jsonResponse(['success' => false, 'message' => 'Database connection failed.'], 500);
}

$userModel = new User($db);
$taskModel = new Task($db);
$authController = new AuthController($userModel);
$taskController = new TaskController($taskModel);

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$scriptDir = $scriptDir === '/' ? '' : rtrim($scriptDir, '/');

if ($scriptDir !== '' && str_starts_with($path, $scriptDir)) {
    $path = substr($path, strlen($scriptDir));
}

if (str_starts_with($path, '/index.php')) {
    $path = substr($path, strlen('/index.php'));
}

$path = $path === '' ? '/' : $path;

if ($path === '/register' && $method === 'POST') {
    $authController->register(getJsonInput());
}

if ($path === '/login' && $method === 'POST') {
    $authController->login(getJsonInput());
}

if ($path === '/logout' && $method === 'POST') {
    AuthMiddleware::requireAuth();
    $authController->logout();
}

if ($path === '/me' && $method === 'GET') {
    $authController->me();
}

if ($path === '/tasks' && $method === 'GET') {
    $userId = AuthMiddleware::requireAuth();
    $taskController->index($userId);
}

if ($path === '/tasks' && $method === 'POST') {
    $userId = AuthMiddleware::requireAuth();
    $taskController->store($userId, getJsonInput());
}

if (preg_match('#^/tasks/(\d+)$#', $path, $matches) === 1) {
    $userId = AuthMiddleware::requireAuth();
    $taskId = (int)$matches[1];

    if ($method === 'PUT') {
        $taskController->update($taskId, $userId, getJsonInput());
    }

    if ($method === 'DELETE') {
        $taskController->destroy($taskId, $userId);
    }
}

jsonResponse(['success' => false, 'message' => 'Endpoint not found.'], 404);
