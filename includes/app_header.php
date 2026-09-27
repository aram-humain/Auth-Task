<?php

require_once $_SERVER['DOCUMENT_ROOT']
    . '/config/db.php';

require_once $_SERVER['DOCUMENT_ROOT']
    . '/includes/auth.php';

require_once $_SERVER['DOCUMENT_ROOT']
    . '/includes/navigation.php';


$navLoggedIn = isLoggedIn();

$navContext = null;


if ($navLoggedIn) {

    $navContext =
        getNavigationContext(
            $pdo,
            (int) currentUserId()
        );
}


$currentPath =
    parse_url(
        $_SERVER['REQUEST_URI'] ?? '/',
        PHP_URL_PATH
    ) ?: '/';


$isActive =
    static function (
        string $path
    ) use ($currentPath): string {

        return $currentPath === $path
            ? ' app-nav-active'
            : '';
    };


$canCreatePost =
    $navContext !== null
    && navigationHasPermission(
        $navContext,
        'create_post'
    );


$canModerate =
    $navContext !== null
    && navigationHasPermission(
        $navContext,
        'access_moderator_page'
    );


$canAccessAdmin =
    $navContext !== null
    && navigationHasPermission(
        $navContext,
        'access_admin_page'
    );


$canViewUsers =
    $navContext !== null
    && navigationHasPermission(
        $navContext,
        'view_users'
    );


$canViewDeletedPosts =
    $navContext !== null
    && navigationHasPermission(
        $navContext,
        'view_deleted_posts'
    );


$isAdmin =
    $navContext !== null
    && navigationHasRole(
        $navContext,
        'Admin'
    );


$showAdminMenu =
    $canAccessAdmin
    || $canViewUsers
    || $canViewDeletedPosts
    || $isAdmin;


$unreadNotifications =
    (int) (
        $navContext['unread_notifications']
        ?? 0
    );


$profilePicture =
    trim(
        $navContext['profile_picture']
            ?? ''
    );


$firstName =
    trim(
        $navContext['first_name']
            ?? ''
    );


$lastName =
    trim(
        $navContext['last_name']
            ?? ''
    );


$displayName =
    trim(
        $firstName
            . ' '
            . $lastName
    );


if (
    $displayName === ''
    && $navContext !== null
) {

    $displayName =
        $navContext['email'];
}


$initials = 'U';


if ($firstName !== '') {

    $initials =
        mb_strtoupper(
            mb_substr(
                $firstName,
                0,
                1
            )
        );


    if ($lastName !== '') {

        $initials .=
            mb_strtoupper(
                mb_substr(
                    $lastName,
                    0,
                    1
                )
            );
    }
}

?>


<header class="app-header">

    <div class="app-header-inner">


        <a
            href="/posts.php"
            class="app-brand">

            Test App

        </a>


        <nav class="app-main-nav">

            <a
                href="/posts.php"
                class="app-nav-link<?= $isActive(
                                        '/posts.php'
                                    ) ?>">

                Feed

            </a>


            <?php if ($navLoggedIn): ?>


                <a
                    href="/Components/Dashboard/Dashboard.php"
                    class="app-nav-link<?= $isActive(
                                            '/Components/Dashboard/Dashboard.php'
                                        ) ?>">

                    Dashboard

                </a>


                <?php if ($canCreatePost): ?>

                    <a
                        href="/Components/Posts/CreatePosts.php"
                        class="app-nav-link<?= $isActive(
                                                '/Components/Posts/CreatePosts.php'
                                            ) ?>">

                        Create Post

                    </a>

                <?php endif; ?>


                <a
                    href="/Components/SavedPosts/SavedPosts.php"
                    class="app-nav-link<?= $isActive(
                                            '/Components/SavedPosts/SavedPosts.php'
                                        ) ?>">

                    Saved

                </a>


                <?php if ($canModerate): ?>

                    <a
                        href="/moderator.php"
                        class="app-nav-link">

                        Moderation

                    </a>

                <?php endif; ?>


                <?php if ($showAdminMenu): ?>

                    <details class="app-nav-dropdown">

                        <summary class="app-nav-link">
                            Administration
                        </summary>


                        <div class="app-dropdown-menu">


                            <?php if ($canAccessAdmin): ?>

                                <a
                                    href="/Components/Admin/Admin.php">

                                    Admin Panel

                                </a>

                            <?php endif; ?>


                            <?php if ($canViewUsers): ?>

                                <a
                                    href="/Components/Admin/Users/Users.php">

                                    Users

                                </a>

                            <?php endif; ?>


                            <?php if ($isAdmin): ?>

                                <a
                                    href="/Components/Categories/AdminCategories.php">

                                    Categories

                                </a>

                            <?php endif; ?>


                            <?php if ($canViewDeletedPosts): ?>

                                <a
                                    href="/Components/Admin/DeletedPosts.php">

                                    Deleted Posts

                                </a>

                            <?php endif; ?>


                            <?php if ($canAccessAdmin): ?>

                                <a
                                    href="/Components/Admin/Audit/Audit.php">

                                    Activity Log

                                </a>

                            <?php endif; ?>


                        </div>

                    </details>

                <?php endif; ?>


            <?php endif; ?>


        </nav>


        <div class="app-header-actions">


            <button
                type="button"
                id="theme-toggle"
                class="app-theme-button">

                Theme

            </button>


            <?php if ($navLoggedIn): ?>


                <a
                    href="/Components/Notifications/Notifications.php"
                    class="app-notification-button"
                    aria-label="Notifications"
                    title="Notifications">


                    <svg
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <path
                            d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4">
                        </path>

                    </svg>


                    <?php if ($unreadNotifications > 0): ?>

                        <span class="app-notification-badge">

                            <?= $unreadNotifications > 99
                                ? '99+'
                                : $unreadNotifications ?>

                        </span>

                    <?php endif; ?>


                </a>


                <details class="app-profile-menu">


                    <summary
                        class="app-avatar"
                        title="<?= htmlspecialchars(
                                    $displayName,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">


                        <?php if ($profilePicture !== ''): ?>

                            <img
                                src="<?= htmlspecialchars(
                                            $profilePicture,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                alt="Profile">

                        <?php else: ?>

                            <span>

                                <?= htmlspecialchars(
                                    $initials,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </span>

                        <?php endif; ?>


                    </summary>


                    <div class="app-profile-dropdown">


                        <div class="app-profile-info">

                            <strong>

                                <?= htmlspecialchars(
                                    $displayName,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </strong>


                            <span>

                                <?= htmlspecialchars(
                                    $navContext['email']
                                        ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </span>

                        </div>


                        <a
                            href="/Components/Profile/Profile.php">

                            Profile

                        </a>


                        <a
                            href="/Components/Profile/EditProfile.php">

                            Edit Profile

                        </a>


                        <?php if (
                            is_file(
                                $_SERVER['DOCUMENT_ROOT']
                                    . '/Components/Settings/Settings.php'
                            )
                        ): ?>

                            <a
                                href="/Components/Settings/Settings.php">

                                Settings

                            </a>

                        <?php endif; ?>


                        <div class="app-profile-separator">
                        </div>


                        <a
                            href="/logout.php"
                            class="app-logout-link">

                            Logout

                        </a>


                    </div>


                </details>


            <?php else: ?>


                <a
                    href="/Components/Login/Login.php"
                    class="app-login-link">

                    Login

                </a>


                <a
                    href="/Components/Registration/Registration.php"
                    class="app-register-link">

                    Register

                </a>


            <?php endif; ?>


        </div>


    </div>

</header>