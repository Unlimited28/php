<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ambassador') {
    header('Location: ../public/login.php');
    exit;
}

$db = get_db_connection();
$user_id = $_SESSION['user_id'];

$stmt = $db->prepare('
    SELECT u.*, r.name as rank_name, a.name as association_name
    FROM users u
    LEFT JOIN ranks r ON u.rank_id = r.id
    LEFT JOIN associations a ON u.association_id = a.id
    WHERE u.id = ?
');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update logic here
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $church = $_POST['church'];

    $update_stmt = $db->prepare('UPDATE users SET full_name = ?, phone = ?, church = ? WHERE id = ?');
    $update_stmt->execute([$fullname, $phone, $church, $user_id]);

    if (!empty($_POST['new_password']) && password_verify($_POST['current_password'], $user['password'])) {
        $new_password_hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $pass_stmt = $db->prepare('UPDATE users SET password = ? WHERE id = ?');
        $pass_stmt->execute([$new_password_hash, $user_id]);
    }

    header('Location: profile_settings.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - Royal Ambassadors</title>

    <!-- Google Fonts & Remix Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Theme & Dashboard CSS -->
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo-container"><img src="../assets/images/logo.png" alt="Logo" class="logo-img"></div>
                <div class="title">Ambassador Portal</div>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php"><i class="ri-dashboard-line"></i> Dashboard</a>
                <a href="my_exams.php"><i class="ri-file-list-3-line"></i> My Exams</a>
                <a href="my_results.php"><i class="ri-trophy-line"></i> My Results</a>
                <a href="profile_settings.php" class="active"><i class="ri-user-settings-line"></i> Profile Settings</a>

                <div class="nav-title">Resources</div>
                <a href="../public/gallery.php"><i class="ri-gallery-line"></i> Gallery</a>
                <a href="../public/blog.php"><i class="ri-article-line"></i> Blog</a>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php"><i class="ri-logout-box-r-line"></i> Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="main-header">
                <div class="header-left">
                    <button class="menu-toggle"><i class="ri-menu-2-line"></i></button>
                    <h1 class="page-title">Profile Settings</h1>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <img src="../assets/images/team-1.jpg" alt="User Avatar">
                        <div class="user-info">
                            <span class="user-name">Amb. <?php echo htmlspecialchars($user['full_name']); ?></span>
                            <span class="user-role"><?php echo htmlspecialchars($user['rank_name']); ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <div class="dash-card">
                    <div class="dash-card-header">
                        <h3>Edit Your Profile Information</h3>
                    </div>
                    <div class="dash-card-content">
                        <form class="dash-form" method="POST" action="profile_settings.php">
                            <div style="display: flex; align-items: center; gap: 24px; margin-bottom: 24px;">
                                <img src="../assets/images/team-1.jpg" alt="User Avatar" style="width: 80px; height: 80px; border-radius: 50%;">
                                <div class="form-group">
                                    <label for="profile_picture">Update Profile Picture</label>
                                    <input type="file" id="profile_picture" name="profile_picture" class="form-control">
                                </div>
                            </div>
                            <hr style="border-color: rgba(255,255,255,.1); margin: 24px 0;">
                            <div class="form-group">
                                <label for="fullname">Full Name</label>
                                <input type="text" id="fullname" name="fullname" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                            </div>
                             <div class="form-group">
                                <label for="rank">Rank</label>
                                <input type="text" id="rank" class="form-control" value="<?php echo htmlspecialchars($user['rank_name']); ?>" readonly>
                            </div>
                             <div class="form-group">
                                <label for="association">Association</label>
                                <input type="text" id="association" class="form-control" value="<?php echo htmlspecialchars($user['association_name']); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="church">Church</label>
                                <input type="text" id="church" name="church" class="form-control" value="<?php echo htmlspecialchars($user['church']); ?>">
                            </div>
                            <hr style="border-color: rgba(255,255,255,.1); margin: 24px 0;">
                            <h4>Change Password</h4>
                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" id="current_password" name="current_password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" id="new_password" name="new_password" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>
