<?php

require_once __DIR__ . '/auth.php';

function setFlash(string $type, string $message): void 
{
    $_SESSION['flash'][$type] = $message;
}

function getFlash(string $type): ?string
{
    if(!isset($_SESSION['flash'][$type])) {
        return null;
    }

    $message = $_SESSION['flash'][$type];

    unset($_SESSION['flash'][$type]);

    return $message;
}