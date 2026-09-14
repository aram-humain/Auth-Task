<?php

/** @var array $profile */
/** @var array $isOwner */

$firstName = $profile['first_name'] ?? '';
$lastName = $profile['last_name'] ?? '';

$fullName = trim($firstName . ' ' . $lastName);

if ($fullName === '') {
    $fullName = 'User';
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars(
            $fullName,
            ENT_QUOTES,
            'UTF-8'
        ) ?> | Profile
    </title>

    <link
        rel="stylesheet"
        href="/Components/Profile/Profile.css">

    <link
        rel="stylesheet"
        href="/assets/css/theme.css">

    <script src="/assets/js/theme.js"></script>
</head>

<body>

    <button
        type="button"
        id="theme-toggle"
        class="theme-toggle">
        Theme
    </button>

    <main class="profile-container">

        <section class="profile-card">

            <div class="profile-header">

                <div class="profile-avatar-wrapper">

                    <?php if (!empty($profile['profile_picture'])): ?>

                        <img
                            class="profile-avatar"
                            src="<?= htmlspecialchars(
                                        $profile['profile_picture'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                            alt="Profile picture">

                    <?php else: ?>

                        <div class="profile-avatar profile-avatar-empty">
                            No Photo
                        </div>

                    <?php endif; ?>

                </div>

                <div class="profile-heading">

                    <h1>
                        <?= htmlspecialchars(
                            $fullName,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h1>

                    <p>
                        <?= htmlspecialchars(
                            $profile['email'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </p>

                </div>

            </div>

            <div class="profile-section">

                <h2>Personal Information</h2>

                <div class="profile-info-grid">

                    <div class="profile-info-item">
                        <span class="profile-label">
                            First Name
                        </span>

                        <span class="profile-value">
                            <?= htmlspecialchars(
                                $firstName !== ''
                                    ? $firstName
                                    : 'Not specified',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>
                    </div>

                    <div class="profile-info-item">
                        <span class="profile-label">
                            Last Name
                        </span>

                        <span class="profile-value">
                            <?= htmlspecialchars(
                                $lastName !== ''
                                    ? $lastName
                                    : 'Not specified',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>
                    </div>

                    <div class="profile-info-item">
                        <span class="profile-label">
                            Phone
                        </span>

                        <span class="profile-value">
                            <?= htmlspecialchars(
                                $profile['phone']
                                    ?? 'Not specified',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>
                    </div>

                    <div class="profile-info-item">
                        <span class="profile-label">
                            Location
                        </span>

                        <span class="profile-value">
                            <?= htmlspecialchars(
                                $profile['location']
                                    ?? 'Not specified',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>
                    </div>

                    <div class="profile-info-item">
                        <span class="profile-label">
                            Date of Birth
                        </span>

                        <span class="profile-value">
                            <?= htmlspecialchars(
                                $profile['date_of_birth']
                                    ?? 'Not specified',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>
                    </div>

                </div>

            </div>

            <div class="profile-section">

                <h2>About</h2>

                <div class="profile-bio">

                    <?php if (!empty($profile['bio'])): ?>

                        <?= nl2br(
                            htmlspecialchars(
                                $profile['bio'],
                                ENT_QUOTES,
                                'UTF-8'
                            )
                        ) ?>

                    <?php else: ?>

                        <span class="profile-empty">
                            No bio added yet.
                        </span>

                    <?php endif; ?>

                </div>

            </div>

            <div class="profile-actions">

                <a
                    href="/Components/Dashboard/Dashboard.php"
                    class="profile-button profile-button-secondary">
                    Back to Dashboard
                </a>

                 <?php if ($isOwner): ?>

                    <a
                        href="/Components/Profile/EditProfile.php"
                        class="profile-button">
                        Edit Profile
                    </a>

                <?php endif; ?>

            </div>

        </section>

    </main>

</body>

</html>