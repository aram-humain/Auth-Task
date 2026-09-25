<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Categories/CategoryDB.php';

requireRole($pdo, 'Admin');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    exit('Method not allowed.');
}


if (!verifyCsrfToken(
    $_POST['csrf_token'] ?? null
)) {

    http_response_code(403);

    exit('Invalid CSRF token.');
}


$categoryId = filter_input(
    INPUT_POST,
    'category_id',
    FILTER_VALIDATE_INT
);


if (!$categoryId) {

    http_response_code(404);

    exit('Category not found.');
}


$category = getCategoryById(
    $pdo,
    $categoryId
);


if ($category === null) {

    http_response_code(404);

    exit('Category not found.');
}


$postCount = countCategoryPosts(
    $pdo,
    $categoryId
);


if ($postCount > 0) {

    setFlash(
        'error',
        'This category cannot be deleted because it is used by posts.'
    );

    header(
        'Location: /Components/Categories/AdminCategories.php'
    );

    exit;
}


if (!deleteCategory(
    $pdo,
    $categoryId
)) {

    setFlash(
        'error',
        'Category could not be deleted.'
    );

    header(
        'Location: /Components/Categories/AdminCategories.php'
    );

    exit;
}


setFlash(
    'success',
    'Category deleted successfully.'
);


header(
    'Location: /Components/Categories/AdminCategories.php'
);

exit;