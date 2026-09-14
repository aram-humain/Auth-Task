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
    string $firstName,
    string $lastName,
    string $email,
    string $password,
    string $token,
    string $publicSlug
): void {
    $ownsTransaction = !$pdo->inTransaction();

    if($ownsTransaction) {
        $pdo->beginTransaction();
    }

    try {
        // create user
        $stmt = $pdo->prepare(
        'INSERT INTO users 
        (email, password)
         VALUES (:email, :password)');

        $stmt->execute([
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        $userId = (int) $pdo->lastInsertId();

        // profile creation
        $profileStatement = $pdo->prepare(
            'INSERT INTO profiles (
                user_id,
                first_name,
                last_name,
                public_slug
            )
            VALUES (
                :user_id,
                :first_name,
                :last_name,
                :public_slug
            )'
        );

        $profileStatement->execute([
            'user_id' => $userId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'public_slug' => $publicSlug
        ]);

        $roleStatment = $pdo->prepare(
            'SELECT id 
            FROM roles 
            WHERE name = :role_name 
            LIMIT 1');

        $roleStatment->execute([
            'role_name' => 'user'
        ]);

        $roleId = $roleStatment->fetchColumn();

        if($roleId === false) {
            throw new RuntimeException(
                'Default user role doest not exist.'
            );
        }

        $userRoleStatement = $pdo->prepare(
            'INSERT INTO user_roles (
                user_id,
                role_id
            )
              VALUES
               (
                :user_id,
                :role_id
             )'
        );

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

function generatePublicSlug(
    string $firstName,
    string $lastName
): string {

    $name = strtolower(trim($firstName . '-' . $lastName));
    
    $name = str_replace(' ', '-', $name);

    $uniquePart = bin2hex(random_bytes(5));

    return $name . '-' . $uniquePart;
}