<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/auth.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/authorization.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/csrf.php';

$navLoggedIn =
    isLoggedIn();

$headerCsrfToken =
    $navLoggedIn
    ? csrfToken()
    : '';

$navUser =
    null;


$unreadNotifications =
    0;


if ($navLoggedIn) {

    $statement = $pdo->prepare(
        "SELECT
            u.id,
            u.email,

            p.first_name,
            p.last_name,
            p.public_slug,
            p.profile_picture,

            COALESCE(
                (
                    SELECT COUNT(*)

                    FROM notifications n

                    WHERE n.user_id = u.id
                      AND n.is_read = 0
                ),
                0
            ) AS unread_notifications

        FROM users u

        LEFT JOIN profiles p
            ON p.user_id = u.id

        WHERE u.id = :user_id

        LIMIT 1"
    );


    $statement->execute([
        'user_id' =>
        (int) currentUserId()
    ]);


    $navUser =
        $statement->fetch(
            PDO::FETCH_ASSOC
        );


    if ($navUser) {

        $unreadNotifications =
            (int) $navUser['unread_notifications'];
    }
}

$currentPath =
    parse_url(
        $_SERVER['REQUEST_URI']
            ?? '/',
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


$isSectionActive =
    static function (
        string $prefix
    ) use ($currentPath): string {

        return str_starts_with(
            $currentPath,
            $prefix
        )
            ? ' app-nav-active'
            : '';
    };




$isAdmin =
    $navLoggedIn
    && hasRole(
        $pdo,
        'Admin'
    );


$isModerator =
    $navLoggedIn
    && hasRole(
        $pdo,
        'Moderator'
    );




$canCreatePost =
    $navLoggedIn
    && can(
        $pdo,
        'create_post'
    );



$canModerate =
    $isModerator
    || $isAdmin;


$canAccessAdmin =
    $isAdmin;



$canViewUsers =
    $navLoggedIn
    && can(
        $pdo,
        'view_users'
    );


$canManageCategories =
    $isAdmin;


$canViewDeletedPosts =
    $isAdmin;


$canViewActivityLog =
    $isAdmin;

$showAdminMenu =
    $isAdmin
    || (
        $isModerator
        && $canViewUsers
    );



$email =
    trim(
        $navUser['email']
            ?? ''
    );


$firstName =
    trim(
        $navUser['first_name']
            ?? ''
    );


$lastName =
    trim(
        $navUser['last_name']
            ?? ''
    );


$profilePicture =
    trim(
        $navUser['profile_picture']
            ?? ''
    );


$displayName =
    trim(
        $firstName
            . ' '
            . $lastName
    );


if ($displayName === '') {

    $displayName =
        $email !== ''
        ? $email
        : 'User';
}



$initials =
    'U';


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
} elseif ($email !== '') {

    $initials =
        mb_strtoupper(
            mb_substr(
                $email,
                0,
                1
            )
        );
}

?>


<header class="app-header">


    <div class="app-header-inner">


        <!-- =========================
             BRAND
             ========================= -->

        <a
            href="/posts.php"
            class="app-brand">

            Aram App

        </a>


        <!-- =========================
             MAIN NAVIGATION
             ========================= -->

        <nav class="app-main-nav">


            <!-- FEED -->

            <a
                href="/posts.php"
                class="app-nav-link<?= $isActive(
                                        '/posts.php'
                                    ) ?>">

                Feed

            </a>


            <?php if ($navLoggedIn): ?>


                <!-- DASHBOARD -->

                <a
                    href="/Components/Dashboard/Dashboard.php"
                    class="app-nav-link<?= $isActive(
                                            '/Components/Dashboard/Dashboard.php'
                                        ) ?>">

                    Dashboard

                </a>


                <!-- CREATE POST -->

                <?php if ($canCreatePost): ?>

                    <a
                        href="/Components/Posts/CreatePosts.php"
                        class="app-nav-link<?= $isActive(
                                                '/Components/Posts/CreatePosts.php'
                                            ) ?>">

                        Create Post

                    </a>

                <?php endif; ?>


                <!-- SAVED POSTS -->

                <a
                    href="/Components/SavedPosts/SavedPosts.php"
                    class="app-nav-link<?= $isSectionActive(
                                            '/Components/SavedPosts/'
                                        ) ?>">

                    Saved

                </a>


                <!-- =========================
                     MODERATOR
                     ========================= -->

                <?php if ($canModerate): ?>

                    <a
                        href="/moderator.php"
                        class="app-nav-link<?= (
                                                $currentPath === '/moderator.php'
                                                || str_starts_with(
                                                    $currentPath,
                                                    '/Components/Moderator/'
                                                )
                                            )
                                                ? ' app-nav-active'
                                                : '' ?>">

                        Moderator

                    </a>

                <?php endif; ?>


                <!-- =========================
                     ADMIN MAIN PAGE
                     ADMIN ONLY
                     ========================= -->

                <?php if ($canAccessAdmin): ?>

                    <a
                        href="/Components/Admin/Admin.php"
                        class="app-nav-link<?= $isActive(
                                                '/Components/Admin/Admin.php'
                                            ) ?>">

                        Admin

                    </a>

                <?php endif; ?>


                <!-- =========================
                     ADMIN PAGES
                     ========================= -->

                <?php if ($showAdminMenu): ?>


                    <details
                        class="app-nav-dropdown">


                        <summary
                            class="app-nav-link<?= (
                                                    str_starts_with(
                                                        $currentPath,
                                                        '/Components/Admin/'
                                                    )
                                                    || str_starts_with(
                                                        $currentPath,
                                                        '/Components/Categories/'
                                                    )
                                                )
                                                    ? ' app-nav-active'
                                                    : '' ?>">

                            Admin Pages

                        </summary>


                        <div
                            class="app-dropdown-menu">


                            <!-- =====================
                                 USERS

                                 Admin + Moderator
                                 with view_users
                                 ===================== -->

                            <?php if ($canViewUsers): ?>

                                <a
                                    href="/Components/Admin/Users/Users.php">

                                    Users

                                </a>

                            <?php endif; ?>


                            <!-- =====================
                                 ADMIN-ONLY PAGES
                                 ===================== -->

                            <?php if ($isAdmin): ?>


                                <a
                                    href="/Components/Categories/AdminCategories.php">

                                    Categories

                                </a>


                                <a
                                    href="/Components/Admin/DeletedPosts.php">

                                    Deleted Posts

                                </a>


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


        <!-- =========================
             RIGHT SIDE
             ========================= -->

        <div class="app-header-actions">


            <!-- THEME -->

            <button
                type="button"
                id="theme-toggle"
                class="app-theme-button">

                Theme

            </button>


            <?php if ($navLoggedIn): ?>


                <!-- =========================
                     NOTIFICATIONS
                     ========================= -->

                <a
                    href="/Components/Notifications/Notifications.php"
                    class="app-notification-button"
                    aria-label="Notifications"
                    title="Notifications">


                    <svg
                        viewBox="0 0 24 24"
                        aria-hidden="true">

                        <path
                            d="
                                M18 8
                                a6 6 0 0 0-12 0
                                c0 7-3 7-3 9
                                h18
                                c0-2-3-2-3-9

                                M10 21
                                h4
                            ">
                        </path>

                    </svg>


                    <?php if (
                        $unreadNotifications > 0
                    ): ?>


                        <span
                            class="app-notification-badge">

                            <?= $unreadNotifications > 99
                                ? '99+'
                                : $unreadNotifications ?>

                        </span>


                    <?php endif; ?>


                </a>


                <!-- =========================
                     PROFILE
                     ========================= -->

                <details class="app-profile-menu">


                    <summary
                        class="app-avatar"
                        title="<?= htmlspecialchars(
                                    $displayName,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">


                        <?php if (
                            $profilePicture !== ''
                        ): ?>


                            <img
                                src="<?= htmlspecialchars(
                                            $profilePicture,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                alt="<?= htmlspecialchars(
                                            $displayName,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>">


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


                    <div
                        class="app-profile-dropdown">


                        <!-- USER INFORMATION -->

                        <div
                            class="app-profile-info">


                            <strong>

                                <?= htmlspecialchars(
                                    $displayName,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </strong>


                            <span>

                                <?= htmlspecialchars(
                                    $email,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>

                            </span>


                        </div>


                        <!-- PROFILE -->

                        <a
                            href="/Components/Profile/Profile.php">

                            Profile

                        </a>


                        <!-- EDIT PROFILE -->

                        <a
                            href="/Components/Profile/EditProfile.php">

                            Edit Profile

                        </a>


                        <div
                            class="app-profile-separator">
                        </div>


                        <form
                            method="POST"
                            action="/logout.php"
                            class="app-logout-form">

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= htmlspecialchars(
                                            $headerCsrfToken,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>">

                            <button
                                type="submit"
                                class="app-logout-link">

                                Logout

                            </button>

                        </form>


                    </div>


                </details>


            <?php else: ?>


                <!-- =========================
                     GUEST
                     ========================= -->

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