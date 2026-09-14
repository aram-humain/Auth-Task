<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/cloudinary.php';

function uploadFile(
    array $file,
    string $type
): array {
    global $cloudinary;

    $uploadTypes = [
        'profile' => [
            'asset_folder' => 'profile-pictures',
            'max_size' => 5 * 1024 * 1024,
            'mime_types' => [
                'image/jpeg',
                'image/png',
                'image/webp'
            ]
        ],

        'post' => [
            'asset_folder' => 'post-images',
            'max_size' => 10 * 1024 * 1024,
            'mime_types' => [
                'image/jpeg',
                'image/png',
                'image/webp'
            ]
        ],
            'certificate' => [
                'asset_folder' => 'certificates',
                'max_size' => 10 * 1024 * 1024,
                'mime_types' => [
                    'application/pdf',
                    'image/jpeg',
                    'image/png'
                ]
            ]
    ];

    if(!isset($uploadTypes[$type])) {
        throw new RuntimeException('Invalid upload type.');
    }

    if(!isset($file['error'], $file['size'], $file['tmp_name'])) {
        throw new RuntimeException('Invalid uploaded file');
    }

    if($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('File upload failed');
    }

    $config = $uploadTypes[$type];

    

    if($file['size'] > $config['max_size']) {
        throw new RuntimeException('File is too large.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);

    $mimeType = $finfo->file($file['tmp_name']);

    if(!in_array($mimeType, $config['mime_types'], true)) {
        throw new RuntimeException('File type is not allowed.');
    }

    $result = $cloudinary->uploadApi()->upload($file['tmp_name'], ['asset_folder' => $config['asset_folder'], 'resource_type' => 'auto']);

    return [
        'url' => $result['secure_url'],
        'public_id' => $result['public_id']
    ];
}


function deleteFile(string $publicId, string $resourceType = 'image'): void {
    global $cloudinary;

    if($publicId === '') {
        return;
    }

    $cloudinary->uploadApi()->destroy($publicId, ['resource_type' => $resourceType, 'invalidate' => true]);
}