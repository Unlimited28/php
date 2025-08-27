<?php
require_once '../includes/database.php';
$db = get_db_connection();
$photos = $db->query('SELECT * FROM gallery WHERE status = "active" ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Royal Ambassadors Portal</title>

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
            background: linear-gradient(135deg, var(--text), var(--accent-2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .page-header p {
            font-size: 1.1rem;
            color: var(--muted);
            max-width: 600px;
            margin: 16px auto 0;
        }
        .content-section {
            padding: 80px 24px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }
        .gallery-item {
            border-radius: var(--radius);
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.07);
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
        }
        .gallery-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 35px rgba(0,0,0,.4);
        }
        .gallery-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            display: block;
        }
        .gallery-item .caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: linear-gradient(to top, rgba(10,18,36,1), rgba(10,18,36,0.7) 70%, transparent);
        }
        .gallery-item .caption h4 {
            font-weight: 700;
            color: var(--text);
        }
        .gallery-item .caption p {
            color: var(--muted);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Navigation Header -->
    <nav class="nav-header">
        <div class="nav-container">
            <div class="brand">
                <div class="logo-container">
                    <img src="../assets/images/logo.png" alt="Logo" class="logo-img">
                </div>
                <div class="title">Royal Ambassadors</div>
            </div>

            <div class="nav-links">
                <a href="index.php#features">Features</a>
                <a href="about.php">About</a>
                <a href="gallery.php">Gallery</a>
                <a href="login.php" class="btn btn-secondary">Login</a>
                <a href="register.php" class="btn btn-primary">Join Now</a>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <header class="page-header">
        <h1>Our Gallery</h1>
        <p>A glimpse into our events, camps, and community activities.</p>
    </header>

    <!-- Main Content Section -->
    <main class="content-section">
        <div class="gallery-grid">
            <?php foreach ($photos as $photo): ?>
            <div class="gallery-item">
                <img src="../<?php echo htmlspecialchars($photo['image_path']); ?>" alt="<?php echo htmlspecialchars($photo['title']); ?>">
                <div class="caption">
                    <h4><?php echo htmlspecialchars($photo['title']); ?></h4>
                    <p><?php echo htmlspecialchars($photo['description']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Ad Placeholder -->
    <section class="ad-placeholder">
        <p>Advertisement</p>
    </section>

    <!-- Footer -->
    <footer style="background: linear-gradient(180deg, #0b1530, #0b162f); border-top: 1px solid rgba(255,255,255,.06); padding: 40px 24px 20px;">
        <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
            <div class="brand" style="justify-content: center; margin-bottom: 20px;">
                <div class="logo-container">
                    <img src="../assets/images/logo.png" alt="Logo" class="logo-img">
                </div>
                <div class="title">Royal Ambassadors - Ogun Baptist Conference</div>
            </div>
            <p style="color: var(--muted); margin-bottom: 20px;">
                Empowering young Christian leaders across Ogun State through faith, education, and service.
            </p>
            <div style="border-top: 1px solid rgba(255,255,255,.06); padding-top: 20px; color: var(--muted); font-size: 0.9rem;">
                <p>&copy; 2025 Royal Ambassadors Ogun Baptist Conference. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
