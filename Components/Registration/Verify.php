<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/VerifyDB.php';
require_once __DIR__ . '/../../config/mail.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/rate_limit.php';

$token = trim($_GET['token'] ?? '');
$email = trim($_GET['email'] ?? $_POST['email'] ?? '');
$error = '';
$success = '';

$csrfToken = csrfToken();

if (isset($_GET['unverified'])) {
    $error = 'Please verify your email before opening the Dashboard.';
}

if ($token !== '') {
    if (verifyEmailToken($pdo, $token)) {
        header('Location: ../Login/Login.php?verified=1');
        exit;
    }

    $error = 'The verification link is invalid or expired.';
} elseif (
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


    $action =
        $_POST['action']
        ?? 'verify';


    $email =
        strtolower(
            trim(
                $_POST['email']
                    ?? ''
            )
        );


    if ($action === 'resend') {

        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {

            $error =
                'A valid email address is required.';
        } else {


            $ipAddress =
                $_SERVER['REMOTE_ADDR']
                ?? null;


            $emailAttempts =
                countRecentAttempts(
                    $pdo,
                    'verification_resend',
                    900,
                    null,
                    $email
                );


            $ipAttempts =
                $ipAddress !== null
                ? countRecentAttempts(
                    $pdo,
                    'verification_resend',
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
                    'verification_resend',
                    null,
                    $email,
                    $ipAddress
                );


                try {

                    $newToken =
                        bin2hex(
                            random_bytes(32)
                        );


                    $recipientName =
                        replaceVerificationToken(
                            $pdo,
                            $email,
                            $newToken
                        );
                    if (
                        $recipientName !== null
                    ) {

                        sendVerificationLinkEmail(
                            $email,
                            $recipientName,
                            $newToken
                        );
                    }


                    $success =
                        'If an unverified account exists, '
                        . 'a new verification email has been sent.';
                } catch (
                    Throwable $exception
                ) {

                    error_log(
                        $exception->getMessage()
                    );


                    $success =
                        'If an unverified account exists, '
                        . 'a new verification email has been sent.';
                }
            }
        }
    } else {

        $error =
            'Open the verification link from your email.';
    }
}


require_once __DIR__ . '/Verify.html.php';
