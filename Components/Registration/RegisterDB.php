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

function createUser(PDO $pdo, string $name, string $email, string $password, string $code): void
{
    $ownsTransaction = !$pdo->inTransaction();

    if ($ownsTransaction) {
        $pdo->beginTransaction();
    }

    try {
        $statement = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $statement->execute(['name' => $name, 'email' => $email, 'password' => password_hash($password, PASSWORD_DEFAULT)]);
        $verification = $pdo->prepare("INSERT INTO email_verifications (user_id, code_hash, expires_at) VALUES (:user_id, :code_hash, DATE_ADD(NOW(), INTERVAL 15 MINUTE))");
        $verification->execute([
            'user_id' => $pdo->lastInsertId(),
            'code_hash' => password_hash($code, PASSWORD_DEFAULT)
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
