<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Posts/PostsDB.php';

use Ramsey\Uuid\Uuid;


$currentUserId =
    currentUserId();


$perPage = 5;

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

$search =
    trim(
        $_GET['q'] ?? ''
    );

$sort =
    $_GET['sort']
    ?? 'newest';


$allowedSorts = [
    'newest',
    'liked',
    'commented'
];


if (
    !in_array(
        $sort,
        $allowedSorts,
        true
    )
) {
    $sort = 'newest';
}


$filterOptions =
    getFeedFilterOptions($pdo);


$categories =
    $filterOptions['categories'];


$tags =
    $filterOptions['tags'];


$authors =
    $filterOptions['authors'];

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

if ($categoryId !== null) {

    $validCategoryIds =
        array_map(
            'intval',
            array_column(
                $categories,
                'id'
            )
        );


    if (
        !in_array(
            $categoryId,
            $validCategoryIds,
            true
        )
    ) {
        $categoryId = null;
    }
}

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


if ($tagId !== null) {

    $validTagIds =
        array_map(
            'intval',
            array_column(
                $tags,
                'id'
            )
        );


    if (
        !in_array(
            $tagId,
            $validTagIds,
            true
        )
    ) {
        $tagId = null;
    }
}

$authorSlug =
    trim(
        $_GET['author']
            ?? ''
    );


if ($authorSlug === '') {

    $authorSlug = null;
}

if ($authorSlug !== null) {

    $validAuthorSlugs =
        array_column(
            $authors,
            'public_slug'
        );


    if (
        !in_array(
            $authorSlug,
            $validAuthorSlugs,
            true
        )
    ) {

        $authorSlug = null;
    }
}

$totalPosts =
    countFeedPosts(
        $pdo,
        $search,
        $categoryId,
        $tagId,
        $authorSlug
    );


$totalPages = max(
    1,
    (int) ceil(
        $totalPosts
            / $perPage
    )
);


if ($page > $totalPages) {

    $page =
        $totalPages;
}


$offset =
    ($page - 1)
    * $perPage;


$posts =
    getFeedPosts(
        $pdo,
        $perPage,
        $offset,
        $search,
        $categoryId,
        $sort,
        $tagId,
        $authorSlug
    );


$postIds =
    array_map(
        fn($post) =>
        (int) $post['id'],
        $posts
    );


$imagesByPost =
    getFeedPostImages(
        $pdo,
        $postIds
    );


$tagsByPost =
    getFeedPostTags(
        $pdo,
        $postIds
    );


foreach ($posts as &$post) {

    $post['uuid'] =
        Uuid::fromBytes(
            $post['public_id']
        )->toString();


    $post['images'] =
        $imagesByPost[(int) $post['id']]
        ?? [];


    $post['tags'] =
        $tagsByPost[(int) $post['id']]
        ?? [];


    $post['author_name'] =
        trim(
            ($post['first_name'] ?? '')
                . ' '
                . ($post['last_name'] ?? '')
        );


    if (
        $post['author_name']
        === ''
    ) {

        $post['author_name'] =
            'User';
    }
}


unset($post);


require_once __DIR__
    . '/Posts.html.php';
