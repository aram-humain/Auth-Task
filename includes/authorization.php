<?php

require_once __DIR__ . '/auth.php';

// stugum a konkret role-y
function hasRole(PDO $pdo, string $roleName): bool
{
    $userId = currentUserId();

    if($userId === null) {
        return false;
    }

    $stmt = $pdo->prepare(
        'SELECT 1
        FROM user_roles ur
        INNER JOIN roles r ON r.id = ur.role_id
        WHERE ur.user_id = :user_id
            AND r.name = :role_name
        LIMIT 1'
    );

    $stmt->execute([
        'user_id' => $userId,
        'role_name' => $roleName
    ]);

    return (bool) $stmt->fetchColumn();
}
// stugum a permission
function can(PDO $pdo, string $permissionName): bool
{
    $userId = currentUserId();

    if($userId === null) {
        return false;
    }

    $stmt = $pdo->prepare(
        'SELECT 1
        FROM user_roles ur
        INNER JOIN role_permissions rp ON rp.role_id = ur.role_id
        INNER JOIN permissions p ON p.id = rp.permission_id
        WHERE ur.user_id = :user_id
            AND p.name = :permission_name
        LIMIT 1'
    );

    $stmt->execute([
        'user_id' => $userId,
        'permission_name' => $permissionName
    ]);

    return (bool) $stmt->fetchColumn();
}

function requireRole(PDO $pdo, string $roleName): void
{
    requireLogin();

    if (!hasRole($pdo, $roleName)) {
        http_response_code(403);
        require $_SERVER['DOCUMENT_ROOT'] . '/Components/Error/403.php';
        exit;
    }
}

function requirePermission(PDO $pdo, string $permissionName): void
{
    requireLogin();

    if(!can($pdo, $permissionName)) {
        http_response_code(403);
        require $_SERVER['DOCUMENT_ROOT'] . '/Components/Error/403.php';
        exit;
    }
}