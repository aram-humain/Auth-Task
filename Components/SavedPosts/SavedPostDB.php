<?php

function hasUserSavedPost(
    PDO $pdo,
    int $postId,
    int $userId
): bool {
    $statement = $pdo->prepare(
        "SELECT 1
        
        FROM saved_posts
        
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

function addSavedPost(PDO $pdo, int $postId, int $userId): void {
    $statement = $pdo->prepare(
        "INSERT INTO saved_posts (post_id, user_id) VALUES (:post_id, :user_id)"
    );

    $statement->execute([
        'post_id' => $postId,
        'user_id' => $userId
    ]);
}

function removeSavedPost(PDO $pdo, int $postId, int $userId): void 
{
    $statement = $pdo->prepare(
        "DELETE FROM saved_posts
        
        WHERE post_id = :post_id
        AND user_id = :user_id"
    );

    $statement->execute([
        'post_id' => $postId,
        'user_id' => $userId
    ]);
}

function getSavedPosts(
    PDO $pdo,
    int $userId
): array {
    $statement = $pdo->prepare(
        "SELECT
        sp.created_at AS saved_at,
        
        p.id,
        p.public_id,
        p.title,
        p.content,
        p.created_at,
        p.user_id,
        
        c.name AS category_name,
        
        pr.first_name,
        pr.last_name,
        pr.public_slug,
        pr.profile_picture,
        
        COALESCE(likes.likes_count, 0) AS likes_count,

        COALESCE(comments_data.comments_count, 0) AS comments_count
        
        FROM saved_posts sp
        
        INNER JOIN posts p
        ON p.id = sp.post_id
        
        INNER JOIN categories c
        ON c.id = p.category_id
        
        INNER JOIN profiles pr
        ON pr.user_id = p.user_id
        
        LEFT JOIN(
        SELECT post_id,
        COUNT(*) AS likes_count
        
        FROM post_likes
        
        GROUP BY post_id
        ) likes
        ON likes.post_id = p.id
        
        LEFT JOIN (
        SELECT
        post_id,
        COUNT(*) AS comments_count
        
        FROM comments

        WHERE deleted_at IS NULL

        GROUP BY post_id
        ) comments_data
        ON comments_data.post_id = p.id
        
        WHERE sp.user_id = :user_id
        AND p.status = 'published'
        AND p.deleted_at IS NULL
        
        ORDER BY sp.created_at DESC"
    );

    $statement->execute([
        'user_id' => $userId
    ]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}