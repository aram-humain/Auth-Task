<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/upload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Posts/PostsDB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/rate_limit.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/activity_log.php';


use Ramsey\Uuid\Uuid;

requirePermission($pdo, 'create_post');
$userId = currentUserId();
$error = null;
$categories = getAllCategories($pdo);
$csrfToken = csrfToken();
$publicId = Uuid::uuid7()->getBytes();

$title = '';
$content = '';
$tagsInput = '';
$categoryId = null;
$status = 'draft';


if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        http_response_code(403);

        require $_SERVER['DOCUMENT_ROOT'] . '/Components/Error/403.php';
        exit;
    }

    if(!isUserVerified($pdo, $userId)) {
        $error = 'You must verify your email to creating post.';
    }

    if($error === null) {
        $postsLast24Hours = countRecentAttempts($pdo, 'create_post', 86400, $userId);

        if($postsLast24Hours >= 10) {
            $error = 'You can create a maximum of 10 posts within 24 hours.';
        }
    }

    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $tagsInput = trim($_POST['tags'] ?? '');
    $categoryId = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    $status = trim($_POST['status'] ?? '');

    if($error === null && $title === '') {
        $error = 'Title is required.';
    }

    if ($error === null && !isValidPostStatus($status)) {
        $error = 'Invalid post status.';
    }

    if($error === null && !in_array($status, ['draft', 'published'], true)) {
        $error = 'A new post must be draf or published';
    }

    $uploadedFiles = [];

    if ($error === null) {
        try {
            $pdo->beginTransaction();

        $postId = createPost(
            $pdo, 
            $publicId,
            $userId,
            $title,
            $categoryId,
            $content,
            $status
        );

        if ($tagsInput !== '') {
            processPostTags($pdo, $postId, $tagsInput);
        }

        if (isset($_FILES['images']) && isset($_FILES['images']['name'])) {

            $imageCount = count($_FILES['images']['name']);

            for($i = 0; $i < $imageCount; $i++) {
                if($_FILES['images']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                $file = [
                    'name' => $_FILES['images']['name'][$i],
                    'type' => $_FILES['images']['type'][$i],
                    'tmp_name' => $_FILES['images']['tmp_name'][$i],
                    'error' => $_FILES['images']['error'][$i],
                    'size' => $_FILES['images']['size'][$i],
                ];

                $uploaded = uploadFile($file, 'post');

                $uploadedFiles[] = $uploaded['public_id'];

                addPostImage($pdo, $postId, $uploaded['url'], $uploaded['public_id'], $i);
            }
        }

        if ($content === '' && empty($uploadedFiles)) {
            throw new RuntimeException(
                'Post must containt text or at least one image.'
            );
        }

        recordRateLimitAttempt($pdo, 'create_post', $userId, null, $_SERVER['REMOTE_ADDR'] ?? null);

        logActivity($pdo, $userId, 'post_created', 'post', $postId, $_SERVER['REMOTE_ADDR'] ?? null);

        $pdo->commit();

        setFlash('success', 'Post created successfully.');

        header('Location: /Components/Posts/Posts.php');

        exit;
        } catch(Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            foreach($uploadedFiles as $publicId) {
                try {
                    deleteFile($publicId);
                } catch (Throwable $deleteException) {
                    error_log($deleteException->getMessage());
                }
            }

            $error = $exception->getMessage();
        }
    }
}

require_once __DIR__ . '/CreatePosts.html.php';