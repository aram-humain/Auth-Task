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