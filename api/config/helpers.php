<?php

declare(strict_types=1);

function jsonResponse(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
}

function getJsonInput(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return [];
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        jsonResponse([
            'success' => false,
            'message' => 'Invalid JSON payload.',
        ], 400);
    }

    return $decoded;
}

function requireFields(array $data, array $fields): array
{
    $errors = [];

    foreach ($fields as $field) {
        if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
            $errors[$field] = ucfirst($field) . ' is required.';
        }
    }

    return $errors;
}
