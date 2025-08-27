<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'president') {
    header('Location: ../public/login.php');
    exit;
}

$db = get_db_connection();
$user_id = $_SESSION['user_id'];

$stmt = $db->prepare('SELECT association_id FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$association_id = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['registration_file'])) {
    $camp_year = $_POST['camp_year'];
    $upload_dir = '../uploads/camp_registrations/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $file_name = $association_id . '_' . $camp_year . '_' . basename($_FILES['registration_file']['name']);
    $target_file = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['registration_file']['tmp_name'], $target_file)) {
        $insert_stmt = $db->prepare('INSERT INTO camp_registrations (association_id, camp_year, file_path, uploaded_by) VALUES (?, ?, ?, ?)');
        $insert_stmt->execute([$association_id, $camp_year, 'uploads/camp_registrations/' . $file_name, $user_id]);
    }
    header('Location: camp_registrations.php');
    exit;
}

$history_stmt = $db->prepare('SELECT * FROM camp_registrations WHERE association_id = ? ORDER BY uploaded_at DESC');
$history_stmt->execute([$association_id]);
$history = $history_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camp Registrations - Royal Ambassadors</title>

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
                <a href="exam_approvals.php"><i class="ri-checkbox-multiple-line"></i> Exam Approvals</a>
                <a href="camp_registrations.php" class="active"><i class="ri-quill-pen-line"></i> Camp Registrations</a>
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
                    <h1 class="page-title">Camp Registrations</h1>
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
                <!-- Upload Card -->
                <div class="dash-card" style="margin-bottom: 24px;">
                    <div class="dash-card-header">
                        <h3>Upload Camp Registration File</h3>
                    </div>
                    <div class="dash-card-content">
                        <p style="color: var(--muted); margin-bottom: 16px;">Please upload an Excel file (.xlsx) with the list of registered ambassadors. Ensure the file follows the specified template.</p>
                        <form class="dash-form" method="POST" action="camp_registrations.php" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="camp_year">Select Camp Year</label>
                                <select id="camp_year" name="camp_year" class="form-control">
                                    <option><?php echo date('Y'); ?></option>
                                    <option><?php echo date('Y') + 1; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="registration_file">Registration File</label>
                                <input type="file" id="registration_file" name="registration_file" class="form-control" accept=".xlsx, .xls" required>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="ri-upload-cloud-2-line"></i> Upload Sheet</button>
                        </form>
                    </div>
                </div>

                <!-- History Card -->
                <div class="dash-card">
                    <div class="dash-card-header">
                        <h3>Upload History</h3>
                    </div>
                    <div class="dash-card-content">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>Camp Year</th>
                                    <th>Upload Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($history as $entry): ?>
                                <tr>
                                    <td><a href="../<?php echo htmlspecialchars($entry['file_path']); ?>" download><?php echo htmlspecialchars(basename($entry['file_path'])); ?></a></td>
                                    <td><?php echo htmlspecialchars($entry['camp_year']); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($entry['uploaded_at'])); ?></td>
                                    <td><span class="status status-<?php echo strtolower($entry['status']); ?>"><?php echo ucfirst($entry['status']); ?></span></td>
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
