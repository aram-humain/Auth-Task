<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth.php';

requireLogin();

$statement = $pdo->prepare(
    "SELECT users.id, users.name, users.email, users.created_at,
            email_verifications.verified_at
     FROM users
     LEFT JOIN email_verifications ON email_verifications.user_id = users.id
     WHERE users.id = :id
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

if ($user['verified_at'] === null) {
    header('Location: ../Registration/Verify.php?email=' . urlencode($user['email']) . '&unverified=1');
    exit;
}

?>

<?php require_once __DIR__ . '/Dashboard.html'; ?>