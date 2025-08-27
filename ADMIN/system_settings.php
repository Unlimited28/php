<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'super_admin') {
    header('Location: ../public/login.php');
    exit;
}

// For simplicity, we'll store settings in a JSON file.
// In a real app, a dedicated settings table would be better.
$settings_file = __DIR__ . '/../config/settings.json';
$settings = json_decode(file_get_contents($settings_file), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings['site_name'] = $_POST['site_name'];
    $settings['admin_email'] = $_POST['admin_email'];
    $settings['president_passcode'] = $_POST['president_passcode'];
    $settings['super_admin_passcode'] = $_POST['super_admin_passcode'];
    $settings['public_registration_enabled'] = isset($_POST['public_registration']);
    $settings['about_content'] = $_POST['about_content'];
    $settings['mission_content'] = $_POST['mission_content'];
    $settings['vision_content'] = $_POST['vision_content'];

    file_put_contents($settings_file, json_encode($settings, JSON_PRETTY_PRINT));

    header('Location: system_settings.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - Super Admin</title>

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
                <div class="logo-container">
                    <img src="../assets/images/logo.png" alt="Logo" class="logo-img">
                </div>
                <div class="title">Super Admin</div>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php"><i class="ri-dashboard-line"></i> Dashboard</a>

                <div class="nav-title">User Management</div>
                <a href="user-management.php"><i class="ri-team-line"></i> Manage Users</a>
                <a href="finance_dashboard.php"><i class="ri-bank-card-line"></i> Finance Dashboard</a>
                <a href="voucher-management.php"><i class="ri-coupon-3-line"></i> Voucher Management</a>
                <a href="camp_files.php"><i class="ri-file-excel-2-line"></i> Camp Files</a>

                <div class="nav-title">Content</div>
                <a href="exam-management.php"><i class="ri-file-list-3-line"></i> Exam Management</a>
                <a href="blog-management.php"><i class="ri-article-line"></i> Blog Management</a>
                <a href="gallery-management.php"><i class="ri-gallery-line"></i> Gallery Management</a>

                <div class="nav-title">Marketing</div>
                <a href="ads-management.php"><i class="ri-advertisement-line"></i> Ads Management</a>

                <div class="nav-title">System</div>
                <a href="notification-management.php"><i class="ri-notification-3-line"></i> Notifications</a>
                <a href="system_settings.php" class="active"><i class="ri-settings-3-line"></i> System Settings</a>
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
                    <h1 class="page-title">System Settings</h1>
                </div>
                <div class="header-right">
                    <div class="user-profile">

                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <div class="dash-card">
                    <div class="dash-card-header">
                        <h3>General System Configuration</h3>
                    </div>
                    <div class="dash-card-content">
                        <form class="dash-form" method="POST" action="system_settings.php">
                            <div class="form-group">
                                <label for="site_name">Site Name</label>
                                <input type="text" id="site_name" name="site_name" class="form-control" value="<?php echo htmlspecialchars($settings['site_name']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="admin_email">Admin Email</label>
                                <input type="email" id="admin_email" name="admin_email" class="form-control" value="<?php echo htmlspecialchars($settings['admin_email']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="president_passcode">President Registration Passcode</label>
                                <input type="text" id="president_passcode" name="president_passcode" class="form-control" value="<?php echo htmlspecialchars($settings['president_passcode']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="super_admin_passcode">Super Admin Registration Passcode</label>
                                <input type="text" id="super_admin_passcode" name="super_admin_passcode" class="form-control" value="<?php echo htmlspecialchars($settings['super_admin_passcode']); ?>">
                            </div>
                             <div class="form-group">
                                <label>
                                    <input type="checkbox" name="public_registration" <?php echo $settings['public_registration_enabled'] ? 'checked' : ''; ?>> Enable Public Registration
                                </label>
                            </div>
                            <hr style="border-color: rgba(255,255,255,.1); margin: 24px 0;">
                            <h4>Public Content Management</h4>
                            <div class="form-group">
                                <label for="about_content">About Section Content</label>
                                <textarea id="about_content" name="about_content" class="form-control" rows="5"><?php echo htmlspecialchars($settings['about_content']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="mission_content">Mission Statement</label>
                                <textarea id="mission_content" name="mission_content" class="form-control" rows="3"><?php echo htmlspecialchars($settings['mission_content']); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="vision_content">Vision Statement</label>
                                <textarea id="vision_content" name="vision_content" class="form-control" rows="3"><?php echo htmlspecialchars($settings['vision_content']); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
