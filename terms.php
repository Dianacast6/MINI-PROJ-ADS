<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - QuickNote</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Grotesk:wght@500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/landing.css">
</head>

<body>
    <canvas id="glCanvas"></canvas>

    <?php include 'includes/navbar.php'; ?>

    <div class="terms-container">
        <!-- Sidebar Navigation -->
        <aside class="terms-sidebar">
            <h3>Contents</h3>
            <a href="#acceptance" class="toc-link">1. Acceptance of Terms</a>
            <a href="#accounts" class="toc-link">2. User Accounts</a>
            <a href="#content" class="toc-link">3. Content Ownership</a>
            <a href="#prohibited" class="toc-link">4. Prohibited Uses</a>
            <a href="#termination" class="toc-link">5. Termination</a>
            <a href="#changes" class="toc-link">6. Changes</a>
            <a href="#contact" class="toc-link">7. Contact Us</a>
        </aside>

        <!-- Main Content -->
        <main>
            <div class="legal-header">
                <h1>Terms of Service</h1>
                <div class="last-updated">Last updated: <?php echo date("F j, Y"); ?></div>
            </div>

            <section id="acceptance" class="legal-section">
                <h2>1. Acceptance of Terms</h2>
                <p>Welcome to QuickNote. By accessing or using our websites and services, you agree to comply with and
                    be bound by these Terms of Service. If you do not agree to these terms, please do not use our
                    Service. These terms apply to all visitors, users, and others who access the Service.</p>
            </section>

            <section id="accounts" class="legal-section">
                <h2>2. User Accounts</h2>
                <p>When you create an account with us, you guarantee that the information you provide is accurate,
                    complete, and current at all times. Inaccurate, incomplete, or obsolete information may result in
                    the immediate termination of your account.</p>
                <p>You are responsible for maintaining the confidentiality of your account and password, including but
                    not limited to the restriction of access to your computer and/or account.</p>
            </section>

            <section id="content" class="legal-section">
                <h2>3. Content Ownership</h2>
                <p>Our Service allows you to post, link, store, share and otherwise make available certain information,
                    text, graphics, videos, or other material ("Content"). You strictly retain ownership of any content
                    you submit, post or display on or through the Service.</p>
                <p>By posting Content to the Service, you grant us the right and license to use, modify, publicly
                    perform, publicly display, reproduce, and distribute such Content on and through the Service only
                    for the purpose of providing the Service to you.</p>
            </section>

            <section id="prohibited" class="legal-section">
                <h2>4. Prohibited Uses</h2>
                <p>You may use the Service only for lawful purposes and in accordance with the Terms. You agree not to
                    use the Service:</p>
                <p>In any way that violates any applicable national or international law or regulation. For the purpose
                    of exploiting, harming, or attempting to exploit or harm minors in any way by exposing them to
                    inappropriate content or otherwise.</p>
            </section>

            <section id="termination" class="legal-section">
                <h2>5. Termination</h2>
                <p>We may terminate or suspend access to our Service immediately, without prior notice or liability, for
                    any reason whatsoever, including without limitation if you breach the Terms.</p>
                <p>All provisions of the Terms which by their nature should survive termination shall survive
                    termination, including, without limitation, ownership provisions, warranty disclaimers, indemnity
                    and limitations of liability.</p>
            </section>

            <section id="changes" class="legal-section">
                <h2>6. Changes</h2>
                <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a
                    revision is material we will try to provide at least 30 days notice prior to any new terms taking
                    effect. What constitutes a material change will be determined at our sole discretion.</p>
            </section>

            <section id="contact" class="legal-section">
                <h2>7. Contact Us</h2>
                <p>If you have any questions about these Terms, please contact us at:</p>
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