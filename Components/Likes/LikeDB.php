<?php

function hasUserLikedPost(
    PDO $pdo,
    int $postId,
    int $userId
): bool {
    $statement = $pdo->prepare(
        "SELECT 1
        FROM post_likes
        WHERE post_id = :post_id
        AND user_id = :user_id
        LIMIT 1"
    );

    $statement->execute([
        'post_id' => $postId,
        'user_id' => $userId
    ]);

    return (bool) $statement->fetchColumn();
}

function addPostLike(
    PDO $pdo,
    int $postId,
    int $userId
): void {
    $statement = $pdo->prepare(
        "INSERT INTO post_likes (
        post_id,
        user_id
        )
        
        VALUES (
        :post_id,
        :user_id)"
    );

    $statement->execute([
        'post_id' => $postId,
        'user_id' => $userId
    ]);
}

function removePostLike(
    PDO $pdo,
    int $postId,
    int $userId
): void {
    $statement = $pdo->prepare(
        "DELETE FROM post_likes
        WHERE post_id = :post_id
        AND user_id = :user_id"
    );

    $statement->execute([
        'post_id' => $postId,
        'user_id' => $userId
    ]);
}

function getPostLikeCount(
    PDO $pdo,
    int $postId
): int {
    $statement = $pdo->prepare(
        "SELECT COUNT(*)
        FROM post_likes
        WHERE post_id = :post_id"
    );

    $statement->execute([
        'post_id' => $postId
    ]);

    return (int) $statement->fetchColumn();
}