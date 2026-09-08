<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Admin/AdminDB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';

requirePermission($pdo, 'view_users');

$error = null;
$success = getFlash('success');

$canManageUsers = can($pdo, 'manage_users');
$csrfToken = csrfToken();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    requirePermission($pdo, 'manage_users');

    if(!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        http_response_code(403);

        require $_SERVER['DOCUMENT_ROOT'] . '/Components/Error/403.php';

        exit;
    }

    $userId = filter_input(
        INPUT_POST,
        'user_id',
        FILTER_VALIDATE_INT
    );

    $roleId = filter_input(
        INPUT_POST,
        'role_id',
        FILTER_VALIDATE_INT
    );

    if(!$userId || !$roleId) {
        $error = 'Invalid user or role';
    } else if($userId === currentUserId()) {
        $error = 'You cannot cahnge your own role';
    } else {
        try {
            changeUserRole($pdo, $userId, $roleId, currentUserId());

            setFlash('success', 'Role updated successfully');

             header('Location: /Components/Admin/Users/Users.php');

            exit;

        } catch (Throwable $exception) {
            $error = $exception->getMessage();
        }
    }
}

$search = trim($_GET['q'] ?? '');

$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);

if(!$page || $page < 1) {
    $page = 1;
}

$perPage = 5;

$totalUsers = getUsersCount($pdo, $search);

$totalPages = max(1, (int) ceil($totalUsers / $perPage));

if($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $perPage;

$users = getUsers($pdo, $search, $perPage, $offset);

$roles = getAllRoles($pdo);

require_once __DIR__ . '/Users.html.php';