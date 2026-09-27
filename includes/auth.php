<?php


if (session_status() === PHP_SESSION_NONE) {

    $isHttps =
        !empty($_SERVER['HTTPS'])
        && $_SERVER['HTTPS'] !== 'off';


    ini_set(
        'session.use_strict_mode',
        '1'
    );


    ini_set(
        'session.use_only_cookies',
        '1'
    );


    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);


    session_start();
}


function isLoggedIn(): bool
{
    return isset(
        $_SESSION['user_id']
    );
}


function requireLogin(): void
{
    if (!isLoggedIn()) {

        header(
            'Location: /Components/Login/Login.php'
        );

        exit;
    }
}


function currentUserId(): ?int
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }


    return (int) $_SESSION['user_id'];
}


function logoutUser(): void
{
    $_SESSION = [];


    if (ini_get('session.use_cookies')) {

        $params =
            session_get_cookie_params();


        setcookie(
            session_name(),
            '',
            [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' =>
                    $params['samesite']
                    ?? 'Lax'
            ]
        );
    }


    if (
        session_status()
        === PHP_SESSION_ACTIVE
    ) {

        session_destroy();
    }
}