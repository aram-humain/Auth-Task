<?php

function logActivity(
    PDO $pdo,
    ?int $userId,
    string $action,
    ?string $targetType = null,
    ?int $targetId = null,
    ?string $ipAddress = null
): void {
    $statement = $pdo->prepare(
        'INSERT INTO activity_log(
        user_id,
        action,
        target_type,
        target_id,
        ip_address
        )
        VALUES (
        :user_id,
        :action,
        :target_type,
        :target_id,
        :ip_address
        )'
    );

    $statement->execute([
        'user_id' => $userId,
        'action' => $action,
        'target_type' => $targetType,
        'target_id' => $targetId,
        'ip_address' => $ipAddress
    ]);
}

function getActivityLogsCount(
    PDO $pdo,
    ?int $userId = null
): int {

    $sql = "
        SELECT COUNT(*)
        FROM activity_log al
    ";

    $params = [];


    if ($userId !== null) {

        $sql .= "
            WHERE al.user_id = :user_id
        ";

        $params['user_id'] = $userId;
    }


    $statement = $pdo->prepare($sql);

    $statement->execute($params);


    return (int) $statement->fetchColumn();
}


function getActivityLogs(
    PDO $pdo,
    ?int $userId,
    int $limit,
    int $offset
): array {

    $sql = "
        SELECT
            al.id,
            al.user_id,
            al.action,
            al.target_type,
            al.target_id,
            al.ip_address,
            al.created_at,

            u.email,

            p.first_name,
            p.last_name

        FROM activity_log al

        LEFT JOIN users u
            ON u.id = al.user_id

        LEFT JOIN profiles p
            ON p.user_id = u.id
    ";

    $params = [];


    if ($userId !== null) {

        $sql .= "
            WHERE al.user_id = :user_id
        ";

        $params['user_id'] = $userId;
    }


    $sql .= "
        ORDER BY al.id DESC

        LIMIT {$limit}
        OFFSET {$offset}
    ";


    $statement = $pdo->prepare($sql);

    $statement->execute($params);


    return $statement->fetchAll(
        PDO::FETCH_ASSOC
    );
}


function getActivityLogUsers(
    PDO $pdo
): array {

    $statement = $pdo->query(
        "SELECT DISTINCT
            u.id,
            u.email,
            p.first_name,
            p.last_name

         FROM activity_log al

         INNER JOIN users u
            ON u.id = al.user_id

         LEFT JOIN profiles p
            ON p.user_id = u.id

         ORDER BY
            p.first_name,
            p.last_name,
            u.email"
    );


    return $statement->fetchAll(
        PDO::FETCH_ASSOC
    );
}
