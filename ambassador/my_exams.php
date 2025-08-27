<?php
session_start();
require_once '../includes/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ambassador') {
    header('Location: ../public/login.php');
    exit;
}

$db = get_db_connection();
$user_id = $_SESSION['user_id'];

// Fetch user's rank level
$stmt = $db->prepare('SELECT r.level FROM users u JOIN ranks r ON u.rank_id = r.id WHERE u.id = ?');
$stmt->execute([$user_id]);
$user_rank_level = $stmt->fetchColumn();

// Fetch all published exams and user's results
$stmt = $db->prepare('
    SELECT
        e.*,
        r.name as required_rank_name,
        r.level as required_rank_level,
        er.id as result_id
    FROM exams e
    JOIN ranks r ON e.id = r.id
    LEFT JOIN exam_results er ON e.id = er.exam_id AND er.user_id = ?
    WHERE e.status = "published"
');
$stmt->execute([$user_id]);
$exams = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Exams - Royal Ambassadors</title>

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
                <a href="my_exams.php" class="active"><i class="ri-file-list-3-line"></i> My Exams</a>
                <a href="my_results.php"><i class="ri-trophy-line"></i> My Results</a>
                <a href="profile_settings.php"><i class="ri-user-settings-line"></i> Profile Settings</a>

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
                    <h1 class="page-title">My Exams</h1>
                </div>
                <div class="header-right">
                    <div class="user-profile">
                        <img src="../assets/images/team-1.jpg" alt="User Avatar">
                        <div class="user-info">
                            <span class="user-name">Amb. <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <span class="user-role"></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Wrapper -->
            <div class="content-wrapper">
                <div class="dash-card">
                    <div class="dash-card-header">
                        <h3>Available & Upcoming Exams</h3>
                    </div>
                    <div class="dash-card-content">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>Exam Title</th>
                                    <th>Rank Requirement</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($exams as $exam): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($exam['title']); ?></td>
                                        <td><?php echo htmlspecialchars($exam['required_rank_name']); ?></td>
                                        <td>
                                            <?php
                                            if ($exam['result_id']) {
                                                echo '<span class="status status-pending">Taken</span>';
                                            } elseif ($user_rank_level >= $exam['required_rank_level']) {
                                                echo '<span class="status status-approved">Available</span>';
                                            } else {
                                                echo '<span class="status status-rejected">Not Eligible</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if (!$exam['result_id'] && $user_rank_level >= $exam['required_rank_level']): ?>
                                                <a href="live_exam.php?exam_id=<?php echo $exam['id']; ?>" class="btn btn-primary">Take Exam</a>
                                            <?php else: ?>
                                                -
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
