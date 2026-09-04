<?php

function verifyEmailToken(PDO $pdo, string $token): bool
{
    $statement = $pdo->prepare(
        "SELECT id, user_id, token_hash
         FROM email_verifications
         WHERE verified_at IS NULL AND expires_at > NOW()"
    );
    $statement->execute();

    foreach ($statement->fetchAll() as $verification) {
        if (password_verify($token, $verification['token_hash'])) {
            $update = $pdo->prepare(
                "UPDATE email_verifications
                 SET verified_at = NOW(), token_hash = ''
                 WHERE id = :id AND verified_at IS NULL"
            );
            $update->execute(['id' => $verification['id']]);

            return $update->rowCount() === 1;
        }
    }

    return false;
}

function replaceVerificationToken(PDO $pdo, string $email, string $token): ?string
{
    $statement = $pdo->prepare(
        "SELECT users.id, users.name
         FROM users
         INNER JOIN email_verifications ON email_verifications.user_id = users.id
         WHERE users.email = :email
           AND email_verifications.verified_at IS NULL
         LIMIT 1"
    );
    $statement->execute(['email' => $email]);
    $user = $statement->fetch();

    if (!$user) {
        return null;
    }

    $update = $pdo->prepare(
        "UPDATE email_verifications
         SET token_hash = :token_hash,
             expires_at = DATE_ADD(NOW(), INTERVAL 60 MINUTE)
         WHERE user_id = :user_id
           AND verified_at IS NULL"
    );
    $update->execute([
        'token_hash' => password_hash($token, PASSWORD_DEFAULT),
        'user_id' => $user['id']
    ]);

    return $user['name'];
}
