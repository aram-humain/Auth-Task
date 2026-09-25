<?php

require_once __DIR__
    . '/../../config/db.php';

require_once __DIR__
    . '/../../config/mail.php';

require_once __DIR__
    . '/ForgotDB.php';


$email =
    trim(
        $_POST['email']
            ?? ''
    );


$error = '';

$success = '';


if (
    $_SERVER['REQUEST_METHOD']
    === 'POST'
) {

    if (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $error =
            'Enter a valid email address.';
    } else {

        $user =
            findUserByEmail(
                $pdo,
                $email
            );


        if ($user) {

            try {

                $token =
                    bin2hex(
                        random_bytes(32)
                    );

                $recipientName = trim(
                    ($user['first_name'] ?? '')
                        . ' '
                        . ($user['last_name'] ?? '')
                );

                if ($recipientName === '') {
                    $recipientName =
                        $user['email'];
                }


                $pdo->beginTransaction();


                saveResetToken(
                    $pdo,
                    (int) $user['id'],
                    $token
                );


                sendPasswordResetEmail(
                    $user['email'],
                    $recipientName,
                    $token
                );


                $pdo->commit();
            } catch (
                Throwable $exception
            ) {

                if (
                    $pdo->inTransaction()
                ) {
                    $pdo->rollBack();
                }


                error_log(
                    $exception->getMessage()
                );
            }
        }

        $success =
            'A reset code has been sent.';
    }
}


require_once __DIR__
    . '/Forgot.html.php';