<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'super_admin') {
    header('Location: ../public/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['post_title'];
    $content = $_POST['post_content'];
    $status = isset($_POST['publish']) ? 'published' : 'draft';
    $author_id = $_SESSION['user_id'];

    // Basic slug generation
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

    $db = get_db_connection();
    $stmt = $db->prepare('INSERT INTO blogs (title, slug, content, status, author_id) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$title, $slug, $content, $status, $author_id]);

    header('Location: blog-management.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog Post - Super Admin</title>

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
                <a href="blog-management.php" class="active"><i class="ri-article-line"></i> Blog Management</a>
                <a href="gallery-management.php"><i class="ri-gallery-line"></i> Gallery Management</a>

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
                    <h1 class="page-title">Create New Blog Post</h1>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <img src="../assets/images/pro.jpg" alt="User Avatar">
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <div class="dash-card">
                    <div class="dash-card-header">
                        <h3>Post Editor</h3>
                    </div>
                    <div class="dash-card-content">
                        <form class="dash-form" method="POST" action="create_blog_post.php">
                            <div class="form-group">
                                <label for="post_title">Post Title</label>
                                <input type="text" id="post_title" name="post_title" class="form-control" placeholder="Enter a catchy title..." required>
                            </div>
                            <div class="form-group">
                                <label for="post_content">Content</label>
                                <textarea id="post_content" name="post_content" class="form-control" rows="15" placeholder="Write your article here..." required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="feature_image">Feature Image</label>
                                <input type="file" id="feature_image" name="feature_image" class="form-control">
                            </div>
                            <div style="display: flex; gap: 16px;">
                                <button type="submit" name="draft" class="btn btn-secondary">Save as Draft</button>
                                <button type="submit" name="publish" class="btn btn-primary">Publish Post</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
