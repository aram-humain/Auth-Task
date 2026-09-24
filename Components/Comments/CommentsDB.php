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