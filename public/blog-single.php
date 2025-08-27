<?php
require_once '../includes/database.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    die('Post not found.');
}

$db = get_db_connection();
$stmt = $db->prepare('SELECT b.*, u.full_name as author_name FROM blogs b LEFT JOIN users u ON b.author_id = u.id WHERE b.slug = ? AND b.status = "published"');
$stmt->execute([$slug]);
$post = $stmt->fetch();

if (!$post) {
    die('Post not found.');
}

$recent_posts_stmt = $db->query('SELECT * FROM blogs WHERE status = "published" ORDER BY created_at DESC LIMIT 5');
$recent_posts = $recent_posts_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - Royal Ambassadors Portal</title>

    <!-- Google Fonts & Remix Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Premium Theme CSS -->
    <link rel="stylesheet" href="../assets/css/theme.css">

    <style>
        .page-header {
            padding-top: 120px;
            padding-bottom: 60px;
            text-align: center;
            background: linear-gradient(180deg, rgba(15, 27, 55, 0.5), transparent);
        }
        .page-header h1 {
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1.3;
        }
        .blog-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 48px;
            padding: 80px 24px;
            max-width: 1200px;
            margin: 0 auto;
        }
        @media (min-width: 992px) {
            .blog-layout {
                grid-template-columns: 2.5fr 1fr;
            }
        }
        .post-content {
            color: var(--muted);
            line-height: 1.8;
            font-size: 1.1rem;
        }
        .post-content h2 {
            color: var(--text);
            font-size: 1.8rem;
            margin: 32px 0 16px;
        }
        .post-content blockquote {
            border-left: 4px solid var(--accent-1);
            padding-left: 24px;
            margin: 24px 0;
            font-style: italic;
            color: var(--text);
        }
        .post-feature-image {
            width: 100%;
            height: auto;
            max-height: 450px;
            object-fit: cover;
            border-radius: var(--radius);
            margin-bottom: 32px;
        }
        .post-meta {
            color: var(--muted);
            font-size: 0.9rem;
            margin-bottom: 16px;
        }
        .post-meta span {
            margin-right: 16px;
        }
        .sidebar-widget {
            background: var(--panel);
            border: 1px solid rgba(255,255,255,.07);
            border-radius: var(--radius);
            padding: 24px;
            margin-bottom: 32px;
        }
        .sidebar-widget h4 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .sidebar-widget ul {
            list-style: none;
        }
        .sidebar-widget ul li a {
            color: var(--muted);
            text-decoration: none;
            transition: var(--transition);
            display: block;
            padding: 8px 0;
        }
        .sidebar-widget ul li a:hover {
            color: var(--accent-2);
        }
    </style>
</head>
<body>
    <!-- Navigation Header -->
    <nav class="nav-header">
        <div class="nav-container">
            <div class="brand">
                <div class="logo-container"><img src="../assets/images/logo.png" alt="Logo" class="logo-img"></div>
                <div class="title">Royal Ambassadors</div>
            </div>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="about.php">About</a>
                <a href="gallery.php">Gallery</a>
                <a href="blog.php">Blog</a>
                <a href="login.php" class="btn btn-secondary">Login</a>
                <a href="register.php" class="btn btn-primary">Join Now</a>
            </div>
        </div>
    </nav>

    <!-- Blog Layout -->
    <div class="blog-layout">
        <!-- Main Content -->
        <main class="blog-single-post">
            <header class="page-header" style="padding: 0; text-align: left;">
                <h1 style="background: linear-gradient(135deg, var(--text), var(--accent-2)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    <?php echo htmlspecialchars($post['title']); ?>
                </h1>
                <div class="post-meta" style="margin-top: 16px;">
                    <span><i class="ri-user-line"></i> <?php echo htmlspecialchars($post['author_name'] ?? 'Admin'); ?></span>
                    <span><i class="ri-calendar-line"></i> <?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                </div>
            </header>

            <?php if ($post['featured_image']): ?>
            <img src="../<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="post-feature-image">
            <?php endif; ?>

            <article class="post-content">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </article>

        </main>

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-widget">
                <h4>Recent Posts</h4>
                <ul>
                    <?php foreach ($recent_posts as $recent): ?>
                    <li><a href="blog-single.php?slug=<?php echo $recent['slug']; ?>"><?php echo htmlspecialchars($recent['title']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>
    </div>

    <!-- Footer -->
    <footer style="background: linear-gradient(180deg, #0b1530, #0b162f); border-top: 1px solid rgba(255,255,255,.06); padding: 40px 24px 20px;">
        <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
            <p>&copy; 2025 Royal Ambassadors Ogun Baptist Conference. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
