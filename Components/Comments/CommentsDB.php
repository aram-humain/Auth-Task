<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Ramsey\Uuid\Uuid;

function createComment(
    PDO $pdo,
    int $postId,
    int $userId,
    string $content,
    ?int $parentId = null
): array {

    $uuid = Uuid::uuid7();

    $statement = $pdo->prepare(
        "INSERT INTO comments (
            public_id,
            post_id,
            user_id,
            parent_id,
            content
        )
        VALUES (
            :public_id,
            :post_id,
            :user_id,
            :parent_id,
            :content
        )"
    );

    $statement->execute([
        'public_id' => $uuid->getBytes(),
        'post_id' => $postId,
        'user_id' => $userId,
        'parent_id' => $parentId,
        'content' => $content
    ]);

    return [
        'id' => (int) $pdo->lastInsertId(),
        'uuid' => $uuid->toString()
    ];
}


function getPostComments(
    PDO $pdo,
    int $postId,
): array {
    $statement = $pdo->prepare(
        "SELECT
            c.id,
            c.public_id,
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

function getCommentByPublicId(
    PDO $pdo,
    string $publicId
): ?array {

    $statement = $pdo->prepare(
        "SELECT
            c.id,
            c.public_id,
            c.post_id,
            c.user_id,
            c.parent_id,
            c.content,
            c.created_at,
            c.updated_at,
            c.deleted_at,

            p.user_id AS post_user_id,
            p.public_id AS post_public_id,
            p.status AS post_status,
            p.deleted_at AS post_deleted_at

         FROM comments c

         JOIN posts p
            ON p.id = c.post_id

         WHERE c.public_id = :public_id

         LIMIT 1"
    );

    $statement->execute([
        'public_id' => $publicId
    ]);

    $comment = $statement->fetch(PDO::FETCH_ASSOC);

    return $comment ?: null;
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
        
        p.user_id AS post_user_id,
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

         WHERE (
                id = :comment_id
                OR parent_id = :parent_id
         )
           AND deleted_at IS NULL"
    );

    $statement->execute([
        'comment_id' => $commentId,
        'parent_id' => $commentId
    ]);

    return $statement->rowCount() > 0;
}

function getActiveCommentByid(
    PDO $pdo,
    int $commentId
): ?array {
    $statement = $pdo->prepare(
        "SELECT 
        id,
        post_id,
        user_id,
        parent_id,
        content,
        created_at,
        updated_at
        
        FROM comments
        
        WHERE id = :comment_id
        AND deleted_AT IS NULL
        
        LIMIT 1"
    );

    $statement->execute([
        'comment_id' => $commentId
    ]);

    $comment = $statement->fetch(PDO::FETCH_ASSOC);

    return $comment ?: null;
}

function getActiveCommentByPublicId(
    PDO $pdo,
    string $publicId
): ?array {

    $statement = $pdo->prepare(
        "SELECT
            id,
            public_id,
            post_id,
            user_id,
            parent_id,
            content,
            created_at,
            updated_at

         FROM comments

         WHERE public_id = :public_id
           AND deleted_at IS NULL

         LIMIT 1"
    );

    $statement->execute([
        'public_id' => $publicId
    ]);

    $comment = $statement->fetch(PDO::FETCH_ASSOC);

    return $comment ?: null;
}