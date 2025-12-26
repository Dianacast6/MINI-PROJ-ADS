<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickNote | The Second Brain for Creators</title>
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2300d26a'%3E%3Ccircle cx='12' cy='12' r='12'/%3E%3C/svg%3E">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Grotesk:wght@500;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="assets/css/landing.css">

    <style>
        body {
            background-color: transparent;
        }
    </style>
</head>

<body>
    <canvas id="glCanvas"></canvas>

    <?php include 'includes/navbar.php'; ?>

    <div class="hero">
        <div class="glow-blob"></div>

        <h1>Your thoughts,<br>organized beautifully.</h1>
        <p>Expertly crafted for ideas, lists, and projects. Experience the ultimate "Second Brain" designed for speed
            and clarity.</p>

        <div class="cta-group">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="btn-primary">Go to Dashboard</a>
            <?php else: ?>
                <a href="register.php" class="btn-primary">Start now</a>
                <a href="login.php" class="btn-secondary">Log In</a>
            <?php endif; ?>
        </div>

        <!-- Fake UI Mockup -->
        <div class="ui-preview-container">
            <div class="ui-mockup-header">
                <div class="dot red"></div>
                <div class="dot yellow"></div>
                <div class="dot green"></div>
            </div>
            <div class="ui-content" style="position:relative; overflow:hidden;">
                <!-- Abstract representation of the app -->
                <div style="width:20%; height:100%; border-right:1px solid #333; padding:20px;">
                    <div style="height:20px; width:80%; background:#333; border-radius:4px; margin-bottom:20px;"></div>
                    <div style="height:10px; width:60%; background:#252525; border-radius:4px; margin-bottom:10px;">
                    </div>
                    <div style="height:10px; width:90%; background:#252525; border-radius:4px; margin-bottom:10px;">
                    </div>
                    <div style="height:10px; width:70%; background:#252525; border-radius:4px; margin-bottom:10px;">
                    </div>
                </div>
                <div style="width:30%; height:100%; border-right:1px solid #333; padding:20px;">
                    <div style="height:15px; width:40%; background:#333; border-radius:4px; margin-bottom:20px;"></div>
                    <div
                        style="height:60px; width:100%; background:#222; border-radius:8px; margin-bottom:10px; border-left:3px solid var(--accent-primary);">
                    </div>
                    <div style="height:60px; width:100%; background:#1a1a1a; border-radius:8px; margin-bottom:10px;">
                    </div>
                    <div style="height:60px; width:100%; background:#1a1a1a; border-radius:8px; margin-bottom:10px;">
                    </div>
                </div>
                <div style="width:50%; height:100%; padding:30px;">
                    <div style="height:30px; width:50%; background:#444; border-radius:4px; margin-bottom:20px;"></div>
                    <div style="height:10px; width:90%; background:#333; border-radius:4px; margin-bottom:10px;"></div>
                    <div style="height:10px; width:85%; background:#333; border-radius:4px; margin-bottom:10px;"></div>
                    <div style="height:10px; width:95%; background:#333; border-radius:4px; margin-bottom:10px;"></div>
                    <div style="margin-top:30px; display:flex; gap:10px;">
                        <div
                            style="padding:5px 15px; background:#222; border-radius:15px; border:1px solid #444; color:#777; font-size:0.8rem;">
                            #ideas</div>
                        <div
                            style="padding:5px 15px; background:#222; border-radius:15px; border:1px solid #444; color:#777; font-size:0.8rem;">
                            📎 sketch.png</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Infinite Marquee Spacer -->
    <div class="marquee-strip">
        <div class="marquee-content">
            <span>CAPTURE • ORGANIZE • CLARITY • SPEED • CAPTURE • ORGANIZE • CLARITY • SPEED •</span>
            <span>CAPTURE • ORGANIZE • CLARITY • SPEED • CAPTURE • ORGANIZE • CLARITY • SPEED •</span>
        </div>
    </div>

    <section class="features">
        <div class="feature-card">
            <div class="icon-box">⚡</div>
            <h3>Auto-Save</h3>
            <p>Never lose a thought properly again. We save every keystroke securely to the cloud instantly.</p>
        </div>
        <div class="feature-card">
            <div class="icon-box">🏷️</div>
            <h3>Smart Tagging</h3>
            <p>Organize your notes with flexible tags. Group ideas, projects, and tasks for effortless retrieval.</p>
        </div>
        <div class="feature-card">
            <div class="icon-box">📎</div>
            <h3>Rich Media</h3>
            <p>Upload images, PDFs, and documents directly into your notes. Visual and functional.</p>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>

</html>