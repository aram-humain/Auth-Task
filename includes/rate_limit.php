<?php


function countRecentAttempts(
    PDO $pdo,
    string $action,
    int $seconds,
    ?int $userId = null,
    ?string $identifier = null,
    ?string $ipAddress = null
): int {
    $cutoff = date(
        'Y-m-d H:i:s',
        time() - $seconds
    );

    $sql = '
        SELECT COUNT(*)
        FROM rate_limit_attempts
        WHERE action = :action
          AND created_at >= :cutoff
    ';

    $params = [
        'action' => $action,
        'cutoff' => $cutoff
    ];

    if ($userId !== null) {
        $sql .= ' AND user_id = :user_id';
        $params['user_id'] = $userId;
    }

    if ($identifier !== null) {
        $sql .= ' AND identifier = :identifier';
        $params['identifier'] = $identifier;
    }

    if ($ipAddress !== null) {
        $sql .= ' AND ip_address = :ip_address';
        $params['ip_address'] = $ipAddress;
    }

    $statement = $pdo->prepare($sql);
    $statement->execute($params);

    return (int) $statement->fetchColumn();
}

function recordRateLimitAttempt(
    PDO $pdo,
    string $action,
    ?int $userId = null,
    ?string $identifier = null,
    ?string $ipAddress = null
): void {
    $statement = $pdo->prepare(
        'INSERT INTO rate_limit_attempts (
            action,
            user_id,
            identifier,
            ip_address
        )
        VALUES (
            :action,
            :user_id,
            :identifier,
            :ip_address
        )'
    );

    $statement->execute([
        'action' => $action,
        'user_id' => $userId,
        'identifier' => $identifier,
        'ip_address' => $ipAddress
    ]);
}