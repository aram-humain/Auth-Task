<?php

function emailAlreadyExists(PDO $pdo, string $email): bool
{
    $statement = $pdo->prepare(
        "SELECT id FROM users WHERE email = :email LIMIT 1"
    );

    $statement->execute([
        'email' => $email
    ]);

    return (bool) $statement->fetch();
}

function createUser(PDO $pdo, string $name, string $email, string $password): void
{
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $statement = $pdo->prepare(
        "INSERT INTO users (name, email, password)
         VALUES (:name, :email, :password)"
    );

    $statement->execute([
        'name' => $name,
        'email' => $email,
        'password' => $hashedPassword
    ]);
}
