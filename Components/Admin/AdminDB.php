<?php

// new function #1 instead of getAllUsers
function getUsers(
    PDO $pdo,
    string $search,
    int $limit,
    int $offset
): array {

    $stmt = $pdo->prepare(
        '
    SELECT
        u.id,
        u.name,
        u.email,
        u.created_at,
        ev.verified_at,
        GROUP_CONCAT(
            DISTINCT r.name
            ORDER BY r.name
            SEPARATOR ", "
        ) AS role,
        MIN(r.id) AS role_id
    FROM users u
    
    LEFT JOIN email_verifications ev
        ON ev.user_id = u.id
    LEFT JOIN user_roles ur
        ON ur.user_id = u.id
    LEFT JOIN roles r
        ON r.id = ur.role_id
        
    WHERE (
        u.name LIKE :search
        OR u.email LIKE :search
    )
    
    GROUP BY
        u.id,
        u.name,
        u.email,
        u.created_at,
        ev.verified_at
        
    ORDER BY u.id
    
    LIMIT ' . $limit .'
    OFFSET '. $offset
    );

    $stmt->execute(['search' => '%' . $search . '%']);

    return $stmt->fetchAll();
}

// new function #2 instead of getAllUsers
function getUsersCount(PDO $pdo, string $search): int 
{
    $stmt = $pdo->prepare('
    SELECT COUNT(*)
    FROM users
    WHERE name LIKE :search
        OR email LIKE :search');

    $stmt->execute(['search' => '%' . $search . '%']);

    return (int) $stmt->fetchColumn();

}


// doesnt need after search+pagination implementation
// function getAllUsers(PDO $pdo): array
// {
//     $stmt = $pdo->query(
//     'SELECT
//         u.id,
//         u.name,
//         u.email,
//         u.created_at,
//         ev.verified_at,
//         GROUP_CONCAT(r.name ORDER BY r.name SEPARATOR ", ") AS role,
//         MIN(r.id) AS role_id
//     FROM users u 
//     LEFT JOIN email_verifications ev
//         ON ev.user_id = u.id
//     LEFT JOIN user_roles ur
//         ON ur.user_id = u.id
//     LEFT JOIN roles r
//         ON r.id = ur.role_id
//     GROUP BY
//         u.id,
//         u.name,
//         u.email,
//         u.created_at,
//         ev.verified_at
//     ORDER BY u.id'
//     );

//     return $stmt->fetchAll();
// }

function getAllRoles(PDO $pdo): array
{
    $stmt = $pdo->query(
        'SELECT id, name
        FROM roles
        ORDER BY id'
    );

    return $stmt->fetchAll();
}

function logAudit(
    PDO $pdo, 
    int $actorUserId,
    ?int $targetUserId,
    string $action,
    ?string $oldValue = null,
    ?string $newValue = null
): void {
    $stmt = $pdo->prepare(
        'INSERT INTO audit_logs (
        actor_user_id,
        target_user_id,
        action,
        old_value,
        new_value
        )
        VALUES (
        :actor_user_id,
        :target_user_id,
        :action,
        :old_value,
        :new_value
        )'
    );

    $stmt->execute([
        'actor_user_id' => $actorUserId,
        'target_user_id' => $targetUserId,
        'action' => $action,
        'old_value' => $oldValue,
        'new_value' => $newValue
    ]);
}

function getAuditLogs(PDO $pdo): array
{
    $stmt = $pdo->query(
        'SELECT
        al.id,
        al.action,
        al.old_value,
        al.new_value,
        al.created_at,
        
        actor.id AS actor_id,
        actor.name AS actor_name,
        
        target.id AS target_id,
        target.name AS target_name
        
        FROM audit_logs al
        
        INNER JOIN users actor
        ON actor.id = al.actor_user_id
        
        LEFT JOIN users target
        ON target.id = al.target_user_id
        
        ORDER BY al.id DESC'
    );

    return $stmt->fetchAll();
}

function changeUserRole(PDO $pdo, int $userId, int $roleId, int $actorUserId): void
{
    $pdo->beginTransaction();

    try {

        // get user
        $userStatement = $pdo->prepare(
            'SELECT id
            FROM users
            WHERE id = :id
            LIMIT 1'
        );

        $userStatement->execute(['id' => $userId]);

        if(!$userStatement->fetchColumn()) {
            throw new RuntimeException('User does not exist.');
        }

        // get current role
        $oldRoleStatement = $pdo->prepare(
            'SELECT r.id, r.name
            FROM user_roles ur
            INNER JOIN roles r
                ON r.id = ur.role_id
            WHERE ur.user_id = :user_id
            LIMIT 1'
        );

        $oldRoleStatement->execute(['user_id' => $userId]);

        $oldRole = $oldRoleStatement->fetch();

        if(!$oldRole) {
            throw new RuntimeException('User does not have a role.');
        }

        // check new role
        $newRoleStatement = $pdo->prepare(
            'SELECT id, name
            FROM roles
            WHERE id = :id
            LIMIT 1'
        );

        $newRoleStatement->execute(['id' => $roleId]);

        $newRole = $newRoleStatement->fetch();

        if(!$newRole) {
            throw new RuntimeException('Role does not exist.');
        }

        // if current role = new role dont change anything
        if((int) $oldRole['id'] === (int) $newRole['id']) {
            $pdo->commit();
            return;
        }

        // remove role
        $deleteStatement = $pdo->prepare(
            'DELETE FROM user_roles
            WHERE user_id = :user_id'
        );

        $deleteStatement->execute(['user_id' => $userId]);

        // new role
        $insertStatement = $pdo->prepare(
            'INSERT INTO user_roles (user_id, role_id)
            VALUES (:user_id, :role_id)'
        );

        $insertStatement->execute(['user_id' => $userId, 'role_id' => $roleId]);

        logAudit(
            $pdo,
            $actorUserId,
            $userId,
            'change_user_role',
            $oldRole['name'],
            $newRole['name']
        );

        $pdo->commit();


    } catch (Throwable $exception) {
        if($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $exception;
    }
}