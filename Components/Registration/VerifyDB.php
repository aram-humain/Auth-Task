<?php

function verifyEmailCode(PDO $pdo, string $email, string $code): bool
{
    $statement = $pdo->prepare(
        "SELECT email_verifications.id, email_verifications.code_hash
         FROM email_verifications
         INNER JOIN users ON users.id = email_verifications.user_id
         WHERE users.email = :email
           AND email_verifications.verified_at IS NULL
           AND email_verifications.expires_at > NOW()
         LIMIT 1"
    );
    $statement->execute(['email' => $email]);
    $verification = $statement->fetch();

    if (!$verification || !password_verify($code, $verification['code_hash'])) {
        return false;
    }

    $update = $pdo->prepare(
        "UPDATE email_verifications SET verified_at = NOW() WHERE id = :id"
    );
    $update->execute(['id' => $verification['id']]);

    return true;
}
