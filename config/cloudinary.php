<?php

require_once $_SERVER['DOCUMENT_ROOT']
    . '/vendor/autoload.php';

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

$envPath = __DIR__ . '/../.env';
$env = [];

if (is_file($envPath)) {
    $env = parse_ini_file(
        $envPath,
        true,
        INI_SCANNER_TYPED
    );

    if ($env === false) {
        $env = [];
    }
}

$cloudName = $env['CLOUDINARY_CLOUD_NAME'] ?? getenv('CLOUDINARY_CLOUD_NAME') ?: '';
$apiKey = $env['CLOUDINARY_API_KEY'] ?? getenv('CLOUDINARY_API_KEY') ?: '';
$apiSecret = $env['CLOUDINARY_API_SECRET'] ?? getenv('CLOUDINARY_API_SECRET') ?: '';

if ($cloudName === '' || $apiKey === '' || $apiSecret === '') {
    throw new RuntimeException('Cloudinary configuration is missing.');
}

$configuration = new Configuration([
    'cloud' => [
        'cloud_name' => $cloudName,
        'api_key' => $apiKey,
        'api_secret' => $apiSecret
    ],
    'url' => [
        'secure' => true
    ]
]);

$cloudinary = new Cloudinary($configuration);