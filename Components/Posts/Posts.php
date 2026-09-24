<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Posts/PostsDB.php';

use Ramsey\Uuid\Uuid;

$perPage = 6;

$page = filter_input(
    INPUT_GET,
    'page',
    FILTER_VALIDATE_INT
);

if (
    $page === false
    || $page === null
    || $page < 1
) {
    $page = 1;
}

$search = trim(
    $_GET['q'] ?? ''
);

$sort = $_GET['sort'] ?? 'newest';

$allowedSorts = [
    'newest',
    'liked',
    'commented'
];

if (!in_array($sort, $allowedSorts, true)) {
    $sort = 'newest';
}

$categoryId = filter_input(
    INPUT_GET,
    'category_id',
    FILTER_VALIDATE_INT
);

if (
    $categoryId === false
    || $categoryId === null
    || $categoryId < 1
) {
    $categoryId = null;
}

if (
    $categoryId !== null
    && !isCategoryExists(
        $pdo,
        $categoryId
    )
) {
    $categoryId = null;
}

$categories = getAllCategories($pdo);

$tagId = filter_input(
    INPUT_GET,
    'tag_id',
    FILTER_VALIDATE_INT
);

if (
    $tagId === false
    || $tagId === null
    || $tagId < 1
) {
    $tagId = null;
}

if (
    $tagId !== null
    && !tagExists(
        $pdo,
        $tagId
    )
) {
    $tagId = null;
}

$tags = getAllTags($pdo);


$authorSlug = trim($_GET['author'] ?? '');

$authorId = null;

if ($authorSlug !== '') {
    if (!feedAuthorExists($pdo, $authorSlug)) {
        $authorSlug = '';
    } else {
        $authorId = getFeedAuthorIdBySlug(
            $pdo,
            $authorSlug
        );
    }
}

$authors = getFeedAuthors($pdo);

$totalPosts = countFeedPosts(
    $pdo,
    $search,
    $categoryId,
    $tagId,
    $authorId
);

$totalPages = max(
    1,
    (int) ceil($totalPosts / $perPage)
);

if ($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $perPage;

$posts = getFeedPosts(
    $pdo,
    $perPage,
    $offset,
    $search,
    $categoryId,
    $sort,
    $tagId,
    $authorId
);

$postIds = array_map(
    fn ($post) => (int) $post['id'],
    $posts
);

$imagesByPost = getFeedPostImages(
    $pdo,
    $postIds
);

$tagsByPost = getFeedPostTags(
    $pdo,
    $postIds
);


foreach ($posts as &$post) {

    $post['uuid'] = Uuid::fromBytes(
        $post['public_id']
    )->toString();

    $post['images'] =
        $imagesByPost[(int) $post['id']]
        ?? [];

    $post['tags'] =
        $tagsByPost[(int) $post['id']]
        ?? [];

    $post['author_name'] = trim(
        ($post['first_name'] ?? '')
        . ' '
        . ($post['last_name'] ?? '')
    );

    if ($post['author_name'] === '') {
        $post['author_name'] = 'User';
    }
}

unset($post);

$currentUserId = currentUserId();

require_once __DIR__ . '/Posts.html.php';