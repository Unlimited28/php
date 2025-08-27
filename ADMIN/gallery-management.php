<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'super_admin') {
    header('Location: ../public/login.php');
    exit;
}

$db = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
    $upload_dir = '../assets/images/gallery/';
    $caption = $_POST['caption'] ?? '';
    $uploaded_by = $_SESSION['user_id'];

    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        $file_name = uniqid() . '-' . basename($_FILES['images']['name'][$key]);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($tmp_name, $target_file)) {
            $stmt = $db->prepare('INSERT INTO gallery (title, image_path, uploaded_by) VALUES (?, ?, ?)');
            $stmt->execute([$caption, 'assets/images/gallery/' . $file_name, $uploaded_by]);
        }
    }
    header('Location: gallery-management.php');
    exit;
}

$photos = $db->query('SELECT * FROM gallery ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Management - Super Admin</title>

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
                <a href="gallery-management.php" class="active"><i class="ri-gallery-line"></i> Gallery Management</a>

                <div class="nav-title">Marketing</div>
                <a href="ads-management.php"><i class="ri-advertisement-line"></i> Ads Management</a>

                <div class="nav-title">System</div>
                <a href="notification-management.php"><i class="ri-notification-3-line"></i> Notifications</a>
                <a href="system_settings.php"><i class="ri-settings-3-line"></i> System Settings</a>
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
                    <h1 class="page-title">Gallery Management</h1>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <img src="../assets/images/pro.jpg" alt="User Avatar">
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <!-- Upload Card -->
                <div class="dash-card" style="margin-bottom: 24px;">
                    <div class="dash-card-header">
                        <h3>Upload New Photos</h3>
                    </div>
                    <div class="dash-card-content">
                        <form class="dash-form" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="images">Select Images (Multiple Allowed)</label>
                                <input type="file" id="images" name="images[]" class="form-control" multiple required>
                            </div>
                            <div class="form-group">
                                <label for="caption">Caption (Optional)</label>
                                <input type="text" id="caption" name="caption" class="form-control" placeholder="A short description for the photos...">
                            </div>
                            <button type="submit" class="btn btn-primary">Upload to Gallery</button>
                        </form>
                    </div>
                </div>

                <!-- Existing Photos -->
                <div class="dash-card">
                    <div class="dash-card-header">
                        <h3>Existing Photos</h3>
                    </div>
                    <div class="dash-card-content">
                        <div class="gallery-grid" style="grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
                            <?php foreach ($photos as $photo): ?>
                            <div class="gallery-item" style="position: relative;">
                                <img src="../<?php echo htmlspecialchars($photo['image_path']); ?>" alt="<?php echo htmlspecialchars($photo['title']); ?>" style="height: 150px;">
                                <a href="delete_gallery_item.php?id=<?php echo $photo['id']; ?>" class="btn btn-secondary" style="position: absolute; top: 10px; right: 10px; background: var(--danger); color: white;" onclick="return confirm('Are you sure?')">Delete</a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
