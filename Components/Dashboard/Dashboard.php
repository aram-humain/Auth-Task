<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';


requireLogin();

$statement = $pdo->prepare(
    'SELECT 
        users.id,
        users.email, 
        users.created_at,
        profiles.first_name,
        profiles.last_name,
        email_verifications.verified_at
     FROM users
     LEFT JOIN profiles ON profiles.user_id = users.id
     LEFT JOIN email_verifications ON email_verifications.user_id = users.id
     WHERE users.id = :id
     LIMIT 1'
);

$statement->execute([
    'id' => currentUserId()
]);

$user = $statement->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    logoutUser();

    header('Location: ../Login/Login.php');
    exit;
}

if ($user['verified_at'] === null) {
    header('Location: ../Registration/Verify.php?email=' . urlencode($user['email']) . '&unverified=1');
    exit;
}

requirePermission($pdo, 'view_dashboard');

require_once __DIR__ . '/Dashboard.html.php';