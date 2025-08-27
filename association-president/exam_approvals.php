<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'president') {
    header('Location: ../public/login.php');
    exit;
}

$db = get_db_connection();
$user_id = $_SESSION['user_id'];

// Fetch president's association
$stmt = $db->prepare('SELECT association_id FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$association_id = $stmt->fetchColumn();

// Fetch pending approvals for the president's association
// This is a placeholder for a real approval system.
// In a real system, you'd have a table for exam_registrations or similar.
$requests = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Approvals - Royal Ambassadors</title>

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
                <div class="title">President Portal</div>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php"><i class="ri-dashboard-line"></i> Dashboard</a>
                <a href="manage_ambassadors.php"><i class="ri-team-line"></i> Manage Ambassadors</a>
                <a href="exam_approvals.php" class="active"><i class="ri-checkbox-multiple-line"></i> Exam Approvals</a>
                <a href="camp_registrations.php"><i class="ri-quill-pen-line"></i> Camp Registrations</a>
                <a href="payments_upload.php"><i class="ri-upload-cloud-2-line"></i> Upload Payments</a>
                <a href="notifications.php"><i class="ri-notification-3-line"></i> Notifications</a>
                <a href="profile_settings.php"><i class="ri-user-settings-line"></i> Profile Settings</a>
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
                    <h1 class="page-title">Exam Approvals</h1>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <img src="../assets/images/director.jpg" alt="User Avatar">
                        <div class="user-info">
                            <span class="user-name">Pres. <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <span class="user-role"></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <div class="dash-card">
                    <div class="dash-card-header">
                        <h3>Pending Exam Requests</h3>
                    </div>
                    <div class="dash-card-content">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Ambassador Name</th>
                                    <th>Requested Exam</th>
                                    <th>Request Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($requests)): ?>
                                    <tr>
                                        <td colspan="4">No pending requests.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($requests as $request): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($request['ambassador_name']); ?></td>
                                        <td><?php echo htmlspecialchars($request['exam_title']); ?></td>
                                        <td><?php echo date('Y-m-d', strtotime($request['request_date'])); ?></td>
                                        <td>
                                            <a href="handle_approval.php?action=approve&id=<?php echo $request['id']; ?>" class="btn btn-primary btn-sm" style="background: var(--success);">Approve</a>
                                            <a href="handle_approval.php?action=reject&id=<?php echo $request['id']; ?>" class="btn btn-secondary btn-sm" style="background: var(--danger); color: white;">Reject</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>
