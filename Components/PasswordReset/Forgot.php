<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/mail.php';
require_once __DIR__ . '/ForgotDB.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/rate_limit.php';


$email =
    trim(
        $_POST['email']
            ?? ''
    );


$error = '';

$success = '';

$csrfToken =
    csrfToken();


if (
    $_SERVER['REQUEST_METHOD']
    === 'POST'
) {

    if (
        !verifyCsrfToken(
            $_POST['csrf_token']
                ?? null
        )
    ) {

        http_response_code(403);

        exit('Invalid CSRF token.');
    }


    $email =
        strtolower(
            trim(
                $_POST['email']
                    ?? ''
            )
        );


    $ipAddress =
        $_SERVER['REMOTE_ADDR']
        ?? null;


    if (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $error =
            'Enter a valid email address.';
    } else {


        $emailAttempts =
            countRecentAttempts(
                $pdo,
                'password_reset_request',
                900,
                null,
                $email
            );


        $ipAttempts =
            $ipAddress !== null
            ? countRecentAttempts(
                $pdo,
                'password_reset_request',
                900,
                null,
                null,
                $ipAddress
            )
            : 0;


        if (
            $emailAttempts >= 3
            || $ipAttempts >= 10
        ) {

            $error =
                'Too many requests. '
                . 'Please try again later.';
        } else {


            recordRateLimitAttempt(
                $pdo,
                'password_reset_request',
                null,
                $email,
                $ipAddress
            );


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


                    $recipientName =
                        trim(
                            ($user['first_name'] ?? '')
                                . ' '
                                . ($user['last_name'] ?? '')
                        );


                    if (
                        $recipientName === ''
                    ) {

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
                'If an account with that email exists, '
                . 'a password reset link has been sent.';
        }
    }
}


require_once __DIR__
    . '/Forgot.html.php';
