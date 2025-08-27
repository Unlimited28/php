<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'super_admin') {
    header('Location: ../public/login.php');
    exit;
}

$db = get_db_connection();
$search = $_GET['search'] ?? '';
$role_filter = $_GET['role'] ?? '';

$sql = 'SELECT u.*, a.name as association_name FROM users u LEFT JOIN associations a ON u.association_id = a.id WHERE 1=1';
$params = [];

if ($search) {
    $sql .= ' AND (u.full_name LIKE ? OR u.unique_id LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($role_filter) {
    $sql .= ' AND u.role = ?';
    $params[] = $role_filter;
}
$sql .= ' ORDER BY u.created_at DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Super Admin</title>

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
                <a href="user-management.php" class="active"><i class="ri-team-line"></i> Manage Users</a>
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
                    <h1 class="page-title">User Management</h1>
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
                        <h3>All System Users</h3>
                        <form method="GET" action="user-management.php" style="display: flex; gap: 16px;">
                            <input type="search" name="search" class="form-control" placeholder="Search by name or ID..." value="<?php echo htmlspecialchars($search); ?>">
                            <select name="role" class="form-control" style="width: 200px;" onchange="this.form.submit()">
                                <option value="">Filter by Role</option>
                                <option value="ambassador" <?php if ($role_filter === 'ambassador') echo 'selected'; ?>>Ambassador</option>
                                <option value="president" <?php if ($role_filter === 'president') echo 'selected'; ?>>President</option>
                                <option value="super_admin" <?php if ($role_filter === 'super_admin') echo 'selected'; ?>>Super Admin</option>
                            </select>
                        </form>
                    </div>
                    <div class="dash-card-content">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Full Name</th>
                                    <th>Role</th>
                                    <th>Association</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['unique_id']); ?></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $user['role']))); ?></td>
                                    <td><?php echo htmlspecialchars($user['association_name'] ?? 'N/A'); ?></td>
                                    <td><span class="status status-<?php echo strtolower($user['status']); ?>"><?php echo ucfirst($user['status']); ?></span></td>
                                    <td><a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-secondary btn-sm">View/Edit</a></td>
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
