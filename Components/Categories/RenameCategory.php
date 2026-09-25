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


$name = trim(
    $_POST['name'] ?? ''
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


if ($name === '') {

    setFlash(
        'error',
        'Category name is required.'
    );

    header(
        'Location: /Components/Categories/AdminCategories.php'
    );

    exit;
}


if (mb_strlen($name) > 100) {

    setFlash(
        'error',
        'Category name is too long.'
    );

    header(
        'Location: /Components/Categories/AdminCategories.php'
    );

    exit;
}


if (
    categoryNameExists(
        $pdo,
        $name,
        $categoryId
    )
) {

    setFlash(
        'error',
        'Another category already has this name.'
    );

    header(
        'Location: /Components/Categories/AdminCategories.php'
    );

    exit;
}


renameCategory(
    $pdo,
    $categoryId,
    $name
);


setFlash(
    'success',
    'Category renamed successfully.'
);


header(
    'Location: /Components/Categories/AdminCategories.php'
);

exit;