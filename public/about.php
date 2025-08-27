<?php
$settings_file = __DIR__ . '/../config/settings.json';
$settings = json_decode(file_get_contents($settings_file), true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - <?php echo htmlspecialchars($settings['site_name']); ?></title>

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
                <a href="index.php">Home</a>
                <a href="about.php">About</a>
                <a href="blog.php">Blog</a>
                <a href="gallery.php">Gallery</a>
                <a href="contact.php">Contact</a>
                <a href="login.php" class="btn btn-secondary">Login</a>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <header class="page-header">
        <h1>About the Royal Ambassadors</h1>
        <p>Learn about our mission, vision, and the values that guide our journey in faith and leadership.</p>
    </header>

    <!-- Main Content Section -->
    <main class="content-section">
        <div id="about" style="text-align: center;">
            <h2 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 16px; background: linear-gradient(135deg, var(--text), var(--gold)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                Our Foundation
            </h2>
            <p style="font-size: 1.1rem; color: var(--muted); max-width: 800px; margin: 0 auto 40px; line-height: 1.6;">
                <?php echo htmlspecialchars($settings['about_content']); ?>
            </p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; margin-top: 48px; text-align: left;">
                <div style="background: linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02)); border: 1px solid rgba(255,255,255,.07); border-radius: var(--radius); padding: 24px;">
                    <h4 style="color: var(--gold); font-size: 1.2rem; margin-bottom: 12px; font-weight: 700;">Our Mission</h4>
                    <p style="color: var(--muted); line-height: 1.6;"><?php echo htmlspecialchars($settings['mission_content']); ?></p>
                </div>

                <div style="background: linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02)); border: 1px solid rgba(255,255,255,.07); border-radius: var(--radius); padding: 24px;">
                    <h4 style="color: var(--accent-2); font-size: 1.2rem; margin-bottom: 12px; font-weight: 700;">Our Vision</h4>
                    <p style="color: var(--muted); line-height: 1.6;"><?php echo htmlspecialchars($settings['vision_content']); ?></p>
                </div>

                <div style="background: linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02)); border: 1px solid rgba(255,255,255,.07); border-radius: var(--radius); padding: 24px;">
                    <h4 style="color: var(--success); font-size: 1.2rem; margin-bottom: 12px; font-weight: 700;">Our Values</h4>
                    <p style="color: var(--muted); line-height: 1.6;">Faith, Excellence, Service, Leadership, Community, and Integrity guide everything we do in developing tomorrow's Christian leaders.</p>
                </div>
            </div>
        </div>
    </main>

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
