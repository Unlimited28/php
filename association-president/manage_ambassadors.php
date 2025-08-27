<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'president') {
    header('Location: ../public/login.php');
    exit;
}

$db = get_db_connection();
$user_id = $_SESSION['user_id'];

$stmt = $db->prepare('SELECT u.association_id, a.name as association_name FROM users u JOIN associations a ON u.association_id = a.id WHERE u.id = ?');
$stmt->execute([$user_id]);
$president_info = $stmt->fetch();
$association_id = $president_info['association_id'];
$association_name = $president_info['association_name'];

$search = $_GET['search'] ?? '';
$sql = 'SELECT u.*, r.name as rank_name FROM users u LEFT JOIN ranks r ON u.rank_id = r.id WHERE u.association_id = ? AND u.role = "ambassador"';
$params = [$association_id];

if ($search) {
    $sql .= ' AND (u.full_name LIKE ? OR u.unique_id LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$ambassadors_stmt = $db->prepare($sql);
$ambassadors_stmt->execute($params);
$ambassadors = $ambassadors_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Ambassadors - Royal Ambassadors</title>

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
                <a href="manage_ambassadors.php" class="active"><i class="ri-team-line"></i> Manage Ambassadors</a>
                <a href="exam_approvals.php"><i class="ri-checkbox-multiple-line"></i> Exam Approvals</a>
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
                    <h1 class="page-title">Manage Ambassadors</h1>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <img src="../assets/images/director.jpg" alt="User Avatar">
                        <div class="user-info">
                            <span class="user-name">Pres. <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <span class="user-role"><?php echo htmlspecialchars($association_name); ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <div class="dash-card">
                    <div class="dash-card-header">
                        <h3>Ambassadors in <?php echo htmlspecialchars($association_name); ?></h3>
                        <form method="GET" action="manage_ambassadors.php" style="display: flex; gap: 16px;">
                            <input type="search" name="search" class="form-control" placeholder="Search Ambassadors..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>
                    </div>
                    <div class="dash-card-content">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Unique ID</th>
                                    <th>Full Name</th>
                                    <th>Rank</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ambassadors as $ambassador): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($ambassador['unique_id']); ?></td>
                                    <td><?php echo htmlspecialchars($ambassador['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($ambassador['rank_name'] ?? 'N/A'); ?></td>
                                    <td><span class="status status-<?php echo strtolower($ambassador['status']); ?>"><?php echo ucfirst($ambassador['status']); ?></span></td>
                                    <td>
                                        <a href="view_ambassador.php?id=<?php echo $ambassador['id']; ?>" class="btn btn-secondary btn-sm">View</a>
                                        <?php if ($ambassador['status'] === 'pending'): ?>
                                            <a href="approve_ambassador.php?id=<?php echo $ambassador['id']; ?>" class="btn btn-primary btn-sm">Approve</a>
                                        <?php endif; ?>
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
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>
