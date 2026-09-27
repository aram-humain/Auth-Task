<?php

use Cloudinary\Api\Upload\ContextCommand;

function getNavigationContext (
    PDO $pdo, 
    int $userId
): ?array {
    $statement = $pdo->prepare(
        "SELECT
        u.id,
        u.email,
        
        p.fist_name,
        p.last_name,
        p.public_slug,
        p.profile_picture,
        
        COALESCE(
        (
        SELECT COUNT(*)
        FROM notifications n
        WHERE n.user_id = u.id
        AND n.is_read = 0
        ),
        0
        ) AS unread_notifications,
        
        COALESCE((
        SELECT GROUP_CONCAT(
        DISTINCT permission.name
        SEPARATOR ',')
        FROM user_roles ur
        INNER JOIN role_permissions rp
        ON rp.role_id = ur.role_id
        
        INNER JOIN permissions permission
        ON permission.id = rp.permission_id
        
        WHERE ur.user_id = u.id),
        ''
        ) AS permission_names,
        
        COALESCE (
        (
        SELECT GROUP_CONCAT(
        DISTINCT role.name
        SEPARATOR ','
        )
        FROM user_roles ur
        
        INNER JOIN roles role
        ON role.id = ur.role_id
        
        WHERE ur.user_id = u.id
        ),
        ''
        ) AS role_names
        
        FROM users u 
        
        LEFT JOIN profiles p
        ON p.user_id = u.id
        
        WHERE u.id = :user_id
        
        LIMIT 1"
    );

    $statement->bindValue(':user_id', $userId, PDO::PARAM_INT);

    $context = $statement->fetch(PDO::FETCH_ASSOC);

    if(!$context) {
        return null;
    }

    $context['permissions'] = $context['permission_names'] === '' ? [] : explode(',', $context['permission_names']);

    $context['roles'] = $context['role_names'] === '' ? [] : explode(',', $context['role_names']);

    return $context;
}

function navigationHasPermission(
    array $context,
    string $permission
): bool {
    return in_array($permission, $context['permissions'] ?? [], true);
}

function navigationHasRole(array $context, string $role): bool 
{
    return in_array($role, $context['roles'] ?? [], true);
}

