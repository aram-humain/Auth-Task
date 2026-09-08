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

function createUser(
    PDO $pdo,
    string $name,
    string $email,
    string $password,
    string $token
): void {
    $ownsTransaction = !$pdo->inTransaction();

    if($ownsTransaction) {
        $pdo->beginTransaction();
    }

    try {
        // create user
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)');

        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        $userId = (int) $pdo->lastInsertId();

        $roleStatment = $pdo->prepare('SELECT id FROM roles WHERE name = :role_name LIMIT 1');

        $roleStatment->execute([
            'role_name' => 'user'
        ]);

        $roleId = $roleStatment->fetchColumn();

        if($roleId === false) {
            throw new RuntimeException('Default user role doest not exist.');
        }

        $userRoleStatement = $pdo->prepare('INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)');

        $userRoleStatement->execute([
            'user_id' => $userId,
            'role_id' => (int) $roleId
        ]);

        $verification = $pdo->prepare('INSERT INTO email_verifications (user_id, token_hash, expires_at) VALUES (:user_id, :token_hash, DATE_ADD(NOW(), INTERVAL 60 MINUTE))');

        $verification->execute([
            'user_id' => $userId,
            'token_hash' => password_hash($token, PASSWORD_DEFAULT)
        ]);

        if ($ownsTransaction) {
            $pdo->commit();
        }
    } catch (\Throwable $exception) {
        if($ownsTransaction && $pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $exception;
    }
}