<?php

/** @var array $users */
/** @var string|null $success */
/** @var bool $canManageUsers */
/** @var array $roles */
/** @var string|null $error */
/** @var string $search */
/** @var int $page */
/** @var int $totalUsers */
/** @var int $totalPages */
/** @var string $csrfToken */

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Users</title>

    <link
        rel="stylesheet"
        href="/Components/Admin/Users/Users.css">

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

    <main class="users-container">

        <section class="users-card">

            <div class="users-header">

                <div>

                    <h1>
                        Users
                    </h1>

                    <p>
                        Manage users and their roles.
                    </p>

                </div>

                <?php if (can($pdo, 'access_admin_page')): ?>

                    <a
                        href="/Components/Admin/Audit/Audit.php"
                        class="users-button users-button-audit">
                        Audit Log
                    </a>

                <?php endif; ?>

            </div>

            <?php if ($success !== null): ?>

                <p
                    id="success-message"
                    class="success-message"
                    role="status"
                >
                    <?= htmlspecialchars(
                        $success,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            <?php endif; ?>

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

            <div class="users-toolbar">

                <form
                    method="GET"
                    action=""
                    class="search-form">

                    <input
                        type="search"
                        name="q"
                        placeholder="Search by name or email..."
                        value="<?= htmlspecialchars(
                                    $search,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                    <button
                        type="submit"
                        class="search-button">
                        Search
                    </button>

                    <?php if ($search !== ''): ?>

                        <a
                            href="/Components/Admin/Users/Users.php"
                            class="clear-search">
                            Clear
                        </a>

                    <?php endif; ?>

                </form>

                <div class="users-count">
                    <?= $totalUsers ?> user<?= $totalUsers === 1 ? '' : 's' ?>
                </div>

            </div>

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Verified</th>
                            <th>Created At</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php if (empty($users)): ?>

                            <tr>
                                <td colspan="6" class="empty-users">
                                    No users found
                                </td>
                            </tr>

                        <?php else: ?>

                            <?php foreach ($users as $user): ?>

                                <tr>

                                    <td>
                                        <?= (int) $user['id'] ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $user['name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $user['email'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>

                                        <?php if (
                                            $canManageUsers
                                            && (int) $user['id'] !== currentUserId()
                                        ): ?>

                                            <form
                                                method="POST"
                                                class="role-form">

                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                                                <input
                                                    type="hidden"
                                                    name="user_id"
                                                    value="<?= (int) $user['id'] ?>">

                                                <select
                                                    name="role_id"
                                                    aria-label="Select user role">

                                                    <?php foreach ($roles as $role): ?>

                                                        <option
                                                            value="<?= (int) $role['id'] ?>"
                                                            <?= (int) $role['id'] === (int) $user['role_id']
                                                                ? 'selected'
                                                                : '' ?>>
                                                            <?= htmlspecialchars(
                                                                ucfirst($role['name']),
                                                                ENT_QUOTES,
                                                                'UTF-8'
                                                            ) ?>
                                                        </option>

                                                    <?php endforeach; ?>

                                                </select>

                                                <button
                                                    type="submit"
                                                    class="role-button">
                                                    Change Role
                                                </button>

                                            </form>

                                        <?php else: ?>

                                            <?= htmlspecialchars(
                                                $user['role'] ?? '',
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        <?php endif; ?>

                                    </td>

                                    <td>

                                        <span class="<?= $user['verified_at'] !== null
                                                            ? 'status-verified'
                                                            : 'status-unverified' ?>">

                                            <?= $user['verified_at'] !== null
                                                ? 'Yes'
                                                : 'No' ?>

                                        </span>

                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $user['created_at'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php endif; ?>
                    </tbody>

                </table>

            </div>

            <nav
                class="pagination"
                aria-label="Users pagination">

                <?php if ($page > 1): ?>

                    <a
                        class="pagination-link"
                        href="?<?= htmlspecialchars(
                                    http_build_query([
                                        'q' => $search,
                                        'page' => $page - 1
                                    ]),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">
                        Previous
                    </a>

                <?php endif; ?>


                <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                    <a
                        class="pagination-link <?= $i === $page
                                                    ? 'pagination-active'
                                                    : '' ?>"
                        href="?<?= htmlspecialchars(
                                    http_build_query([
                                        'q' => $search,
                                        'page' => $i
                                    ]),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">
                        <?= $i ?>
                    </a>

                <?php endfor; ?>


                <?php if ($page < $totalPages): ?>

                    <a
                        class="pagination-link"
                        href="?<?= htmlspecialchars(
                                    http_build_query([
                                        'q' => $search,
                                        'page' => $page + 1
                                    ]),
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">
                        Next
                    </a>

                <?php endif; ?>

            </nav>

            <div class="users-actions">

                <?php if (can($pdo, 'access_admin_page')): ?>

                    <a
                        href="/Components/Admin/admin.php"
                        class="users-button users-button-secondary">
                        Back to Admin
                    </a>

                <?php endif; ?>

                <a
                    href="/Components/Dashboard/Dashboard.php"
                    class="users-button users-button-secondary">
                    Back to Dashboard
                </a>

            </div>

        </section>

    </main>

    <script>
        setTimeout(() => {

            const message =
                document.getElementById('success-message');

            if (!message) {
                return;
            }

            message.style.opacity = '0';

            setTimeout(() => {
                message.remove();
            }, 500);

        }, 3000);
    </script>

</body>

</html>