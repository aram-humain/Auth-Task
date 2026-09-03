<?php

function findUserByCredentials(PDO $pdo, string $email, string $password): ?array
{
    $statement = $pdo->prepare(
                "SELECT users.id, users.name, users.email, users.password
                 FROM users
                 INNER JOIN email_verifications ON email_verifications.user_id = users.id
                 WHERE email_verifications.verified_at IS NOT NULL
                     AND users.email = :email
         LIMIT 1"
    );

    $statement->execute([
        'email' => $email
    ]);

    $user = $statement->fetch();

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }

    return null;
}
