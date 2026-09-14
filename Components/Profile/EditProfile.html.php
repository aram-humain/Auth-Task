<?php

/** @var array $profileInfo */
/** @var string $csrfToken */
/** @var string|null $error */

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Edit Profile</title>

    <link
        rel="stylesheet"
        href="/Components/Profile/EditProfile.css">

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

    <main class="edit-profile-container">

        <section class="edit-profile-card">

            <div class="edit-profile-header">

                <div>

                    <h1>Edit Profile</h1>

                    <p>
                        Update your personal information
                        and profile picture.
                    </p>

                </div>

            </div>


            <?php if ($error !== null): ?>

                <p
                    class="error-message"
                    role="alert">

                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </p>

            <?php endif; ?>


            <form
                method="POST"
                enctype="multipart/form-data"
                class="edit-profile-form">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars(
                        $csrfToken,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">


                <div class="profile-picture-section">

                    <h2>Profile Picture</h2>


                    <?php if (!empty($profileInfo['profile_picture'])): ?>

                        <div class="current-picture">

                            <img
                                src="<?= htmlspecialchars(
                                    $profileInfo['profile_picture'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                alt="Current profile picture"
                                class="profile-picture-preview">

                        </div>

                    <?php else: ?>

                        <div class="profile-picture-empty">

                            No profile picture

                        </div>

                    <?php endif; ?>


                    <div class="form-group">

                        <label for="profile_picture">
                            Choose New Picture
                        </label>

                        <input
                            type="file"
                            id="profile_picture"
                            name="profile_picture"
                            accept="image/jpeg,image/png,image/webp">

                        <small>
                            JPG, PNG or WEBP. Maximum 5 MB.
                        </small>

                    </div>


                    <?php if (!empty($profileInfo['profile_picture'])): ?>

                        <label class="remove-picture">

                            <input
                                type="checkbox"
                                name="remove_picture"
                                value="1">

                            Remove current profile picture

                        </label>

                    <?php endif; ?>

                </div>


                <div class="profile-information-section">

                    <h2>Personal Information</h2>


                    <div class="form-grid">

                        <div class="form-group">

                            <label for="first_name">
                                First Name
                            </label>

                            <input
                                type="text"
                                id="first_name"
                                name="first_name"
                                value="<?= htmlspecialchars(
                                    $profileInfo['first_name'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                        </div>


                        <div class="form-group">

                            <label for="last_name">
                                Last Name
                            </label>

                            <input
                                type="text"
                                id="last_name"
                                name="last_name"
                                value="<?= htmlspecialchars(
                                    $profileInfo['last_name'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                        </div>


                        <div class="form-group">

                            <label for="phone">
                                Phone
                            </label>

                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                value="<?= htmlspecialchars(
                                    $profileInfo['phone'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                        </div>


                        <div class="form-group">

                            <label for="location">
                                Location
                            </label>

                            <input
                                type="text"
                                id="location"
                                name="location"
                                value="<?= htmlspecialchars(
                                    $profileInfo['location'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                        </div>


                        <div class="form-group">

                            <label for="date_of_birth">
                                Date of Birth
                            </label>

                            <input
                                type="date"
                                id="date_of_birth"
                                name="date_of_birth"
                                value="<?= htmlspecialchars(
                                    $profileInfo['date_of_birth'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                        </div>

                    </div>


                    <div class="form-group bio-group">

                        <label for="bio">
                            Bio
                        </label>

                        <textarea
                            id="bio"
                            name="bio"
                            rows="6"><?= htmlspecialchars(
                                $profileInfo['bio'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></textarea>

                    </div>

                </div>


                <div class="edit-profile-actions">

                    <a
                        href="/Components/Profile/Profile.php?slug=<?= urlencode(
                            $profileInfo['public_slug']
                        ) ?>"
                        class="profile-button profile-button-secondary">
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="profile-button profile-button-primary">
                        Save Changes
                    </button>

                </div>

            </form>

        </section>

    </main>

</body>

</html>