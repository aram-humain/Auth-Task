<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();

$statement = $pdo -> prepare(
    "SELECT id, name, email, created_at
    FROM users
    WHERE id = :id
    LIMIT 1"
);

$statement->execute([
    'id' => currentUserId()
]);

$user = $statement->fetch();

if (!$user) {
    logoutUser();

    header('Location: ../Login/Login.php');
    exit;
}

?>

<?php require_once __DIR__ . '/Dashboard.html'; ?>