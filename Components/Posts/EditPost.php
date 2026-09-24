<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/activity_log.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Posts/PostsDB.php';

use Ramsey\Uuid\Uuid;

requireLogin();

$userId = currentUserId();

$uuid = trim($_GET['id'] ?? '');

if ($uuid === '' || !Uuid::isValid($uuid)) {
    http_response_code(404);
    exit('Post not found.');
}

$publicId = Uuid::fromString(
    $uuid
)->getBytes();

$post = getOwnedPostByPublicId(
    $pdo,
    $publicId,
    $userId
);

if ($post === null) {
    http_response_code(403);

    require $_SERVER['DOCUMENT_ROOT']
        . '/Components/Error/403.php';

    exit;
}

$categories = getAllCategories($pdo);

$currentTags = getPostTags(
    $pdo,
    (int) $post['id']
);

$tagsInput = implode(
    ', ',
    array_column($currentTags, 'name')
);

$error = null;

$title = $post['title'];
$content = $post['content'];
$categoryId = (int) $post['category_id'];
$status = $post['status'];

$csrfToken = csrfToken();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verifyCsrfToken(
        $_POST['csrf_token'] ?? null
    )) {
        http_response_code(403);

        require $_SERVER['DOCUMENT_ROOT']
            . '/Components/Error/403.php';

        exit;
    }

    $title = trim(
        $_POST['title'] ?? ''
    );

    $content = trim(
        $_POST['content'] ?? ''
    );

    $tagsInput = trim(
        $_POST['tags'] ?? ''
    );

    $categoryId = filter_input(
        INPUT_POST,
        'category_id',
        FILTER_VALIDATE_INT
    );

    $status = trim(
        $_POST['status'] ?? ''
    );


    if ($title === '') {
        $error = 'Title is required.';
    }

    if (
        $error === null
        && (
            !$categoryId
            || !isCategoryExists(
                $pdo,
                $categoryId
            )
        )
    ) {
        $error = 'Invalid category.';
    }

    if (
        $error === null
        && !in_array(
            $status,
            [
                'draft',
                'published',
                'archived'
            ],
            true
        )
    ) {
        $error = 'Invalid post status.';
    }


    if ($error === null) {

        try {

            $pdo->beginTransaction();

            $updated = updateOwnedPost(
                $pdo,
                (int) $post['id'],
                $userId,
                $title,
                $categoryId,
                $content,
                $status
            );


            replacePostTags(
                $pdo,
                (int) $post['id'],
                $tagsInput
            );

            logActivity(
                $pdo,
                $userId,
                'post_updated',
                'post',
                (int) $post['id'],
                $_SERVER['REMOTE_ADDR'] ?? null
            );

            $pdo->commit();

            setFlash(
                'success',
                'Post updated successfully.'
            );

            header(
                'Location: /Components/Posts/Post.php?id='
                    . urlencode($uuid)
            );

            exit;
        } catch (Throwable $exception) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $error = $exception->getMessage();
        }
    }
}


require_once __DIR__ . '/EditPost.html.php';
