<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';

requireLogin();
$userId = currentUserId();

// also we need to add checks to know it is the user that created the post and not any other user

function createPost(PDO $pdo, int $userId, string $title, string $content): void {
    $statement = $pdo->prepare('
    INSERT INTO posts (user_id, title, content) 
    VALUES (:user_id, :title, :content)'
    );
    $statement->execute(['user_id' => $userId, 'title' => $title, 'content' => $content]);
}

function getPosts(PDO $pdo, int $userId): ?array {
    $statement = $pdo->prepare('SELECT * FROM posts WHERE user_id = :user_id ORDER BY created_at DESC');
    $statement->execute(['user_id' => $userId]);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function editPosts(PDO $pdo, int $postId, string $title, string $content): void {
    $statement = $pdo->prepare('
    UPDATE posts SET title = :title, content = :content WHERE id = :post_id');
    $statement->execute(['post_id' => $postId, 'title' => $title, 'content' => $content]);
}

function deletePost(PDO $pdo, int $postId): void {
    $statement = $pdo->prepare('
    DELETE from posts WHERE id = :post_id');
    $statement->execute(['post_id' => $postId]);
}


require_once __DIR__ . '/Posts.html.php';