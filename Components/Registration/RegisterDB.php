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

function createUser(PDO $pdo, string $name, string $email, string $password, string $token): void
{
    $ownsTransaction = !$pdo->inTransaction();

    if ($ownsTransaction) {
        $pdo->beginTransaction();
    }

    try {
        $statement = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $statement->execute(['name' => $name, 'email' => $email, 'password' => password_hash($password, PASSWORD_DEFAULT)]);
        $verification = $pdo->prepare("INSERT INTO email_verifications (user_id, token_hash, expires_at) VALUES (:user_id, :token_hash, DATE_ADD(NOW(), INTERVAL 60 MINUTE))");
        $verification->execute([
            'user_id' => $pdo->lastInsertId(),
            'token_hash' => password_hash($token, PASSWORD_DEFAULT)
        ]);
        if ($ownsTransaction) {
            $pdo->commit();
        }
    } catch (Throwable $exception) {
        if ($ownsTransaction && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }
}
