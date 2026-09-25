<?php

function findUserByEmail(
    PDO $pdo,
    string $email
): ?array {

    $statement = $pdo->prepare(
        "SELECT
            u.id,
            u.email,

            p.first_name,
            p.last_name

         FROM users u

         LEFT JOIN profiles p
            ON p.user_id = u.id

         WHERE u.email = :email

         LIMIT 1"
    );

    $statement->execute([
        'email' => $email
    ]);

    $user = $statement->fetch(
        PDO::FETCH_ASSOC
    );

    return $user ?: null;
}


function saveResetToken(
    PDO $pdo,
    int $userId,
    string $token
): void {

    $delete = $pdo->prepare(
        "DELETE FROM password_resets

         WHERE user_id = :user_id
           AND used_at IS NULL"
    );

    $delete->execute([
        'user_id' => $userId
    ]);


    $statement = $pdo->prepare(
        "INSERT INTO password_resets (
            user_id,
            token_hash,
            expires_at
         )

         VALUES (
            :user_id,
            :token_hash,
            DATE_ADD(
                NOW(),
                INTERVAL 60 MINUTE
            )
         )"
    );


    $statement->execute([
        'user_id' => $userId,

        'token_hash' =>
            password_hash(
                $token,
                PASSWORD_DEFAULT
            )
    ]);
}