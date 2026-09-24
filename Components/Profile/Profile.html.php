<?php

/** @var array $profile */
/** @var array $isOwner */
/** @var array $profilePosts */
/** @var array $isAdmin */


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

            <div class="profile-section">

                <h2>Posts</h2>

                <?php if (empty($profilePosts)): ?>

                    <div class="profile-empty">

                        No published posts yet.

                    </div>

                <?php else: ?>

                    <div class="profile-posts">

                        <?php foreach ($profilePosts as $post): ?>

                            <article class="profile-post">

                                <div class="profile-post-meta">

                                    <span>

                                        <?= htmlspecialchars(
                                            $post['category_name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </span>

                                    <span>·</span>

                                    <?php if ($isOwner || $isAdmin): ?>

                                        <span>
                                            <?= htmlspecialchars(
                                                ucfirst($post['status']),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>

                                    <?php endif; ?>

                                    <time>

                                        <?= htmlspecialchars(
                                            date(
                                                'd M Y, H:i',
                                                strtotime(
                                                    $post['created_at']
                                                )
                                            ),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </time>

                                </div>


                                <h3>

                                    <a
                                        href="/Components/Posts/Post.php?id=<?= urlencode(
                                                                                $post['uuid']
                                                                            ) ?>">

                                        <?= htmlspecialchars(
                                            $post['title'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </a>

                                </h3>


                                <?php if (
                                    trim($post['content'] ?? '') !== ''
                                ): ?>

                                    <?php

                                    $preview = $post['content'];

                                    if (mb_strlen($preview) > 250) {
                                        $preview =
                                            mb_substr(
                                                $preview,
                                                0,
                                                250
                                            )
                                            . '...';
                                    }

                                    ?>

                                    <p>

                                        <?= nl2br(
                                            htmlspecialchars(
                                                $preview,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            )
                                        ) ?>

                                    </p>

                                <?php endif; ?>

                            </article>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

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