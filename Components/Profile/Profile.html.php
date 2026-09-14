<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?> | Profile</title>
    <link rel="stylesheet" href="./Profile.css">
    <link rel="stylesheet" href="/assets/css/theme.css">
    <script src="/assets/js/theme.js"></script>
</head>
<body>

    <div class="profile-container">
        
        <!-- Main Column -->
        <main class="profile-main">
            
            <!-- Intro / Header Card -->
            <div class="profile-card header-card">
                <div class="profile-banner"></div>
                
                <div class="avatar-wrapper">
                    <img class="profile-avatar" 
                         src="<?php echo !empty($profile['profile_picture']) ? htmlspecialchars($profile['profile_picture']) : '/assets/images/default-avatar.png'; ?>" 
                         alt="<?php echo htmlspecialchars($profile['first_name']); ?>'s Profile Picture">
                </div>
                
                <div class="profile-intro">
                    <h1 class="profile-name">
                        <?php echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?>
                    </h1>
                    
                    <p class="profile-meta location-meta">
                        <strong>Location:</strong> <?php echo htmlspecialchars($profile['location']); ?>
                    </p>
                    
                    <?php if (!empty($profile['date_of_birth'])): ?>
                    <p class="profile-meta">
                        <strong>Born:</strong> <?php echo htmlspecialchars($profile['date_of_birth']); ?>
                    </p>
                    <?php endif; ?>

                    <div class="profile-actions">
                        <button class="btn btn-primary">Connect</button>
                        <button class="btn btn-secondary">Message</button>
                    </div>
                </div>
            </div>

            <!-- About / Bio Card -->
            <div class="profile-card content-card">
                <h2 class="card-title">About</h2>
                <div class="card-body">
                    <p><?php echo nl2br(htmlspecialchars($profile['bio'])); ?></p>
                </div>
            </div>
            
        </main>

        <!-- Right Side Sidebar -->
        <aside class="profile-sidebar">
            <div class="profile-card sidebar-card">
                <h3 class="sidebar-title">Contact Information</h3>
                <div class="sidebar-body">
                    <p class="contact-item">
                        <strong>Phone:</strong> 
                        <a href="tel:<?php echo htmlspecialchars($profile['phone']); ?>">
                            <?php echo htmlspecialchars($profile['phone']); ?>
                        </a>
                    </p>
                </div>
            </div>
        </aside>

    </div>

</body>
</html>
