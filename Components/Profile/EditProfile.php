<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/flash.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/upload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Components/Profile/ProfileDB.php';

requireLogin();

$userId = currentUserId();

$profileInfo = getProfileByUserId($pdo, $userId);

$error = null;

if($profileInfo === null) {
    http_response_code(404);
    exit('Profile not found.');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        http_response_code(403);

        require $_SERVER['DOCUMENT_ROOT']
            . '/Components/Error/403.php';

        exit;
    }

    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $dateOfBirth = trim($_POST['date_of_birth'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    if ($dateOfBirth === '') {
        $dateOfBirth = null;
    }
    try {
    updateProfile(
        $pdo,
        $userId,
        $firstName,
        $lastName,
        $phone,
        $location,
        $dateOfBirth,
        $bio
    );

    $oldPublicId = $profileInfo['profile_picture_public_id'] ?? null;

    $removePicture = isset($_POST['remove_picture']);

    $hasNewPicture = isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE;

    if ($removePicture && $hasNewPicture) {
        throw new RuntimeException('Choose either a new picture or remove the current picture.');
    }

    if ($hasNewPicture) {
        $uploaded = uploadFile($_FILES['profile_picture'], 'profile');

        updateProfilePicture($pdo, $userId, $uploaded['url'], $uploaded['public_id']);

        if (!empty($oldPublicId)) {
            deleteFile($oldPublicId);
        }
    }

    if ($removePicture) {
        removeProfilePicture($pdo, $userId);

        if(!empty($oldPublicId)) {
            deleteFile($oldPublicId);
        }
    }

    setFlash('success', 'Profile updated successfully.');

    header('Location: /Components/Profile/Profile.php');

    exit;
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

function updateProfilePicture(PDO $pdo, int $userId, string $url, string $publicId): void {
    $statement = $pdo->prepare(
        'UPDATE profiles
        SET
            profile_picture = :profile_picture,
            profile_picture_public_id = :public_id,
            updated_at = CURRENT_TIMESTAMP
        WHERE user_id = :user_id'
    );

    $statement->execute([
        'profile_picture' => $url,
        'public_id' => $publicId,
        'user_id' => $userId
    ]);
}

function removeProfilePicture(PDO $pdo, int $userId): void {
    $statement = $pdo->prepare(
        'UPDATE profiles
        SET
            profile_picture = NULL,
            profile_picture_public_id = NULL,
            updated_at = CURRENT_TIMESTAMP
        WHERE user_id = :user_id'
    );

    $statement->execute([
        'user_id' => $userId
    ]);
}

$csrfToken = csrfToken();

require_once __DIR__ . '/EditProfile.html.php';