<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - QuickNote</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Grotesk:wght@500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/landing.css">
</head>

<body>
    <canvas id="glCanvas"></canvas>

    <?php include 'includes/navbar.php'; ?>

    <div class="privacy-container">
        <!-- Sidebar Navigation -->
        <aside class="privacy-sidebar">
            <h3>Contents</h3>
            <a href="#collection" class="toc-link">1. Information Collection</a>
            <a href="#usage" class="toc-link">2. Usage of Information</a>
            <a href="#security" class="toc-link">3. Data Security</a>
            <a href="#cookies" class="toc-link">4. Cookies</a>
            <a href="#contact" class="toc-link">5. Contact Us</a>
        </aside>

        <!-- Main Content -->
        <main>
            <div class="legal-header">
                <h1>Privacy Policy</h1>
                <div class="last-updated">Last updated: <?php echo date("F j, Y"); ?></div>
            </div>

            <section id="collection" class="legal-section">
                <h2>1. Information We Collect</h2>
                <p>At QuickNote, transparency is our core value. We collect only the information necessary to provide
                    you with a seamless experience. This includes information you provide directly to us, such as when
                    you create an account, save notes, or contact us for support.</p>
                <p>The types of personal information we may collect include:</p>
                <ul>
                    <li><strong>Account Information:</strong> Your name, email address, and password (hashed).</li>
                    <li><strong>User Content:</strong> The notes, lists, and data you choose to sync.</li>
                    <li><strong>Usage Data:</strong> Anonymous metrics on how you interact with our platform to help us
                        improve performance.</li>
                </ul>
            </section>

            <section id="usage" class="legal-section">
                <h2>2. How We Use Your Information</h2>
                <p>We use the information we collect to provide, maintain, and improve our services. Specifically, we
                    use it to:</p>
                <ul>
                    <li>Process your registration and maintain your secure login session.</li>
                    <li>Store and sync your encrypted notes across your devices.</li>
                    <li>Send you technical notices, updates, security alerts, and support messages.</li>
                    <li>Monitor and analyze trends, usage, and activities in connection with our Service.</li>
                </ul>
            </section>

            <section id="security" class="legal-section">
                <h2>3. Data Security</h2>
                <p>Security is not an afterthought; it's our foundation. We implement industry-leading security measures
                    to protect your data from unauthorized access, alteration, disclosure, or destruction.</p>
                <p>Your notes are encrypted using <strong>AES-256 encryption</strong> at rest. This means that even if
                    our servers were compromised, your personal thoughts would remain unreadable.</p>
            </section>

            <section id="cookies" class="legal-section">
                <h2>4. Cookies</h2>
                <p>We believe in a clean web. We use cookies solely for essential authentication purposes—to keep you
                    logged in as you navigate between pages. We do not use third-party tracking cookies or sell your
                    data to advertisers.</p>
            </section>

            <section id="contact" class="legal-section">
                <h2>5. Contact Us</h2>
                <p>If you have any questions, concerns, or feedback about this Privacy Policy, please strictly contact
                    our privacy team:</p>
                <p><a href="mailto:dianacastillon@gmail.com"
                        style="color:var(--accent-primary); text-decoration:none; font-weight:700; font-size:1.2rem;">dianacastillon@gmail.com</a>
                </p>
            </section>
        </main>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="assets/js/webgl-background.js"></script>
    <script src="assets/js/legal-scroll.js"></script>
</body>

</html>