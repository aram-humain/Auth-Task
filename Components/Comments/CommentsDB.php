<?php

function createComment(
    PDO $pdo,
    int $postId,
    int $userId,
    string $content,
    ?int $parentId = null
): int {
    $statement = $pdo->prepare(
        "INSERT INTO comments(
        post_id,
        user_id,
        parent_id,
        content)
        
        VALUES (
        :post_id,
        :user_id,
        :parent_id,
        :content)"
    );

    $statement->execute([
        'post_id' => $postId,
        'user_id' => $userId,
        'parent_id' => $parentId,
        'content' => $content
    ]);

    return (int) $pdo->lastInsertId();
}


function getPostComments(
    PDO $pdo,
    int $postId,
): array {
    $statement = $pdo->prepare(
        "SELECT
            c.id,
            c.post_id,
            c.user_id,
            c.parent_id,
            c.content,
            c.created_at,
            c.updated_at,
            
            pr.first_name,
            pr.last_name,
            pr.public_slug
            
        FROM comments c
        
        JOIN profiles pr
        ON pr.user_id = c.user_id
        
        WHERE c.post_id = :post_id
        AND c.deleted_at IS NULL
        
        ORDER BY c.created_at ASC"
    );

    $statement->execute(['post_id' => $postId]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function getCommentById(
    PDO $pdo,
    int $commentId
): ?array {
    $statement = $pdo->prepare(
        "SELECT
        c.id,
        c.post_id,
        c.user_id,
        c.parent_id,
        c.content,
        c.created_at,
        c.updated_at,
        c.deleted_at,
        
        p.user_id AS post_user__id,
        p.public_id AS post_public_id,
        p.status AS post_status,
        p.deleted_at AS post_deleted_at
        
        FROM comments c
        
        JOIN posts p
            on p.id = c.post_id
            
        WHERE c.id = :comment_id
        
        LIMIT 1"
    );

    $statement->execute([
        'comment_id' => $commentId
    ]);

    $comment = $statement->fetch(PDO::FETCH_ASSOC);

    return $comment ?: null;
}


function updateOwnComment(
    PDO $pdo,
    int $commentId,
    int $userId,
    string $content
): bool {
    $statement = $pdo->prepare(
        "UPDATE comments
        SET
        content = :content,
        updated_at = CURRENT_TIMESTAMP
        
        WHERE id = :comment_id
        AND user_id = :user_id
        AND deleted_at IS NULL"
    );

    $statement->execute([
        'content' => $content,
        'comment_id' => $commentId,
        'user_id' => $userId
    ]);

    return $statement->rowCount() > 0;
}

function softDeleteComment(
    PDO $pdo,
    int $commentId
): bool {
    $statement = $pdo->prepare(
        "UPDATE comments
        
        SET
        deleted_at = CURRENT_TIMESTAMP,
        updated_at = CURRENT_TIMESTAMP
        
        WHERE id = :comment_id
        AND deleted_at IS NULL"
    );

    $statement->execute([
        'comment_id' => $commentId
    ]);

    return $statement->rowCount() > 0;
}