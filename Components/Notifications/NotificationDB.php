<?php


function createNotification(
    PDO $pdo,
    int $recipientUserId,
    int $actorUserId,
    string $type,
    ?int $postId = null,
    ?int $commentId = null
): void {

    if ($recipientUserId === $actorUserId) {
        return;
    }


    $allowedTypes = [
        'post_like',
        'post_comment',
        'comment_reply'
    ];


    if (!in_array(
        $type,
        $allowedTypes,
        true
    )) {
        throw new InvalidArgumentException(
            'Invalid notification type.'
        );
    }


    $statement = $pdo->prepare(
        'INSERT INTO notifications (
            user_id,
            actor_user_id,
            type,
            post_id,
            comment_id
        )
        VALUES (
            :user_id,
            :actor_user_id,
            :type,
            :post_id,
            :comment_id
        )'
    );


    $statement->execute([
        'user_id' => $recipientUserId,
        'actor_user_id' => $actorUserId,
        'type' => $type,
        'post_id' => $postId,
        'comment_id' => $commentId
    ]);
}


function getNotifications(
    PDO $pdo,
    int $userId,
    int $limit = 50
): array {

    $limit = max(
        1,
        min($limit, 100)
    );


    $statement = $pdo->prepare(
        "SELECT
            n.id,
            n.user_id,
            n.actor_user_id,
            n.type,
            n.post_id,
            n.comment_id,
            n.is_read,
            n.created_at,

            COALESCE(
                NULLIF(
                    TRIM(
                        CONCAT_WS(
                            ' ',
                            actor_profile.first_name,
                            actor_profile.last_name
                        )
                    ),
                    ''
                ),
                actor.email,
                'Unknown user'
            ) AS actor_name,

            actor_profile.public_slug
                AS actor_slug,

            p.title
                AS post_title

        FROM notifications n

        LEFT JOIN users actor
            ON actor.id = n.actor_user_id

        LEFT JOIN profiles actor_profile
            ON actor_profile.user_id =
               n.actor_user_id

        LEFT JOIN posts p
            ON p.id = n.post_id

        WHERE n.user_id = :user_id

        ORDER BY
            n.created_at DESC,
            n.id DESC

        LIMIT {$limit}"
    );


    $statement->execute([
        'user_id' => $userId
    ]);


    return $statement->fetchAll(
        PDO::FETCH_ASSOC
    );
}


function getUnreadNotificationCount(
    PDO $pdo,
    int $userId
): int {

    $statement = $pdo->prepare(
        'SELECT COUNT(*)

         FROM notifications

         WHERE user_id = :user_id
           AND is_read = 0'
    );


    $statement->execute([
        'user_id' => $userId
    ]);


    return (int) $statement->fetchColumn();
}


function markAllNotificationsRead(
    PDO $pdo,
    int $userId
): void {

    $statement = $pdo->prepare(
        'UPDATE notifications

         SET is_read = 1

         WHERE user_id = :user_id
           AND is_read = 0'
    );


    $statement->execute([
        'user_id' => $userId
    ]);
}


function getNotificationForUser(
    PDO $pdo,
    int $notificationId,
    int $userId
): ?array {

    $statement = $pdo->prepare(
        'SELECT
            n.id,
            n.user_id,
            n.type,
            n.post_id,
            n.comment_id,
            n.is_read,

            p.public_id
                AS post_public_id,

            c.public_id
                AS comment_public_id

        FROM notifications n

        LEFT JOIN posts p
            ON p.id = n.post_id

        LEFT JOIN comments c
            ON c.id = n.comment_id

        WHERE n.id = :notification_id
          AND n.user_id = :user_id

        LIMIT 1'
    );


    $statement->execute([
        'notification_id' => $notificationId,
        'user_id' => $userId
    ]);


    $notification =
        $statement->fetch(PDO::FETCH_ASSOC);


    return $notification ?: null;
}


function markNotificationRead(
    PDO $pdo,
    int $notificationId,
    int $userId
): void {

    $statement = $pdo->prepare(
        'UPDATE notifications

         SET is_read = 1

         WHERE id = :notification_id
           AND user_id = :user_id'
    );


    $statement->execute([
        'notification_id' => $notificationId,
        'user_id' => $userId
    ]);
}