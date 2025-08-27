<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'super_admin') {
    header('Location: ../public/login.php');
    exit;
}

$db = get_db_connection();
$stmt = $db->prepare('SELECT blogs.*, users.full_name AS author_name FROM blogs LEFT JOIN users ON blogs.author_id = users.id ORDER BY created_at DESC');
$stmt->execute();
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Management - Super Admin</title>

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
                    <h1 class="page-title">Blog Management</h1>
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
                        <h3>All Blog Posts</h3>
                        <a href="create_blog_post.php" class="btn btn-primary"><i class="ri-add-line"></i> Create New Post</a>
                    </div>
                    <div class="dash-card-content">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($post['title']); ?></td>
                                    <td><?php echo htmlspecialchars($post['author_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($post['created_at'])); ?></td>
                                    <td><span class="status status-<?php echo $post['status'] === 'published' ? 'approved' : 'pending'; ?>"><?php echo ucfirst($post['status']); ?></span></td>
                                    <td>
                                        <a href="edit_blog_post.php?id=<?php echo $post['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                                        <a href="delete_blog_post.php?id=<?php echo $post['id']; ?>" class="btn btn-secondary btn-sm" style="background: var(--danger); color: white;" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
