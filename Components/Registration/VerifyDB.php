<?php

function verifyEmailToken(
    PDO $pdo,
    string $token
): bool {

    $statement = $pdo->prepare(
        "SELECT
            id,
            user_id,
            token_hash

         FROM email_verifications

         WHERE verified_at IS NULL
           AND expires_at > NOW()"
    );


    $statement->execute();


    foreach (
        $statement->fetchAll(
            PDO::FETCH_ASSOC
        )
        as $verification
    ) {

        if (
            password_verify(
                $token,
                $verification['token_hash']
            )
        ) {

            $update = $pdo->prepare(
                "UPDATE email_verifications

                 SET
                    verified_at = NOW(),
                    token_hash = ''

                 WHERE id = :id
                   AND verified_at IS NULL"
            );


            $update->execute([
                'id' =>
                $verification['id']
            ]);


            return
                $update->rowCount()
                === 1;
        }
    }


    return false;
}


function replaceVerificationToken(
    PDO $pdo,
    string $email,
    string $token
): ?string {

    $statement = $pdo->prepare(
        "SELECT
            u.id,

            p.first_name,
            p.last_name

         FROM users u

         INNER JOIN email_verifications ev
            ON ev.user_id = u.id

         LEFT JOIN profiles p
            ON p.user_id = u.id

         WHERE u.email = :email
           AND ev.verified_at IS NULL

         LIMIT 1"
    );


    $statement->execute([
        'email' => $email
    ]);


    $user = $statement->fetch(
        PDO::FETCH_ASSOC
    );


    if (!$user) {
        return null;
    }


    $update = $pdo->prepare(
        "UPDATE email_verifications

         SET
            token_hash = :token_hash,
            expires_at = DATE_ADD(
                NOW(),
                INTERVAL 60 MINUTE
            )

         WHERE user_id = :user_id
           AND verified_at IS NULL"
    );


    $update->execute([
        'token_hash' =>
        password_hash(
            $token,
            PASSWORD_DEFAULT
        ),

        'user_id' =>
        (int) $user['id']
    ]);


    $fullName = trim(
        ($user['first_name'] ?? '')
            . ' '
            . ($user['last_name'] ?? '')
    );

    return
        $fullName !== ''
        ? $fullName
        : $email;
}
