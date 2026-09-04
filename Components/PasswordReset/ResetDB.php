<?php

function findResetRequest(PDO $pdo, string $token): ?array
{
    $statement = $pdo->prepare(
        "SELECT password_resets.id, password_resets.user_id,
                password_resets.token_hash, users.email
         FROM password_resets
         INNER JOIN users ON users.id = password_resets.user_id
         WHERE password_resets.used_at IS NULL
           AND password_resets.expires_at > NOW()"
    );
        $statement->execute();

    foreach ($statement->fetchAll() as $reset) {
        if (password_verify($token, $reset['token_hash'])) {
            return $reset;
        }
    }

    return null;
}

function updatePasswordAndConsumeToken(PDO $pdo, int $resetId, int $userId, string $password): void
{
    $pdo->beginTransaction();

    try {
        $passwordStatement = $pdo->prepare(
            "UPDATE users SET password = :password WHERE id = :user_id"
        );
        $passwordStatement->execute([
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'user_id' => $userId
        ]);

        $tokenStatement = $pdo->prepare(
            "UPDATE password_resets SET used_at = NOW()
             WHERE id = :reset_id AND used_at IS NULL"
        );
        $tokenStatement->execute(['reset_id' => $resetId]);

        if ($tokenStatement->rowCount() !== 1) {
            throw new RuntimeException('Reset token has already been used.');
        }

        $pdo->commit();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }
}
