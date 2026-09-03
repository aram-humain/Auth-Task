<?php

function findUserByCredentials(PDO $pdo, string $email, string $password): ?array
{
    $statement = $pdo->prepare(
        "SELECT id, name, email, password
         FROM users
         WHERE email = :email
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
