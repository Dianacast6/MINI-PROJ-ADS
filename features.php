<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Features - QuickNote</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Grotesk:wght@500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/landing.css">
    <style>
        /* --- COPY OF INDEX.PHP STYLES FOR CONSISTENCY --- */
        :root {
            --primary-bg: #050505;
            --card-bg: rgba(20, 20, 20, 0.6);
            --accent-primary: #00d26a;
            --accent-glow: rgba(0, 210, 106, 0.4);
            --text-main: #ffffff;
            --text-muted: #888888;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #000;
            color: var(--text-main);
            overflow-x: hidden;
            min-height: 100vh;
            line-height: 1.6;
        }

        /* --- SHARED STYLES removed (using landing.css) --- */

        /* --- HERO FOR SUBPAGES --- */
        .page-hero {
            padding: 180px 20px 80px;
            text-align: center;
            position: relative;
        }

        .page-hero h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 4rem;
            line-height: 1.1;
            margin-bottom: 20px;
            background: linear-gradient(90deg, #ffffff 0%, #aaffd3 50%, #ffffff 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            /* Fix lint */
            animation: gradientAnim 6s ease infinite;
        }

        .page-hero p {
            font-size: 1.2rem;
            color: #aaa;
            max-width: 600px;
            margin: 0 auto;
        }

        @keyframes gradientAnim {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* --- BENTO GRID & VISUALS --- */
        .grid-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 5%;
        }

        .grid-item {
            background: rgba(20, 20, 20, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px;
            padding: 40px;
            position: relative;
            backdrop-filter: blur(20px);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .grid-item.span-2 {
            grid-column: span 2;
        }

        .grid-item.row-2 {
            grid-row: span 2;
        }

        .grid-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.5);
        }

        .highlight-glow {
            border-color: rgba(0, 210, 106, 0.3);
            background: radial-gradient(circle at top right, rgba(0, 210, 106, 0.05), rgba(20, 20, 20, 0.4));
        }

        .highlight-glow:hover {
            border-color: var(--accent-primary);
            box-shadow: 0 0 30px rgba(0, 210, 106, 0.15);
        }

        .icon-box,
        .icon-large {
            color: var(--accent-primary);
            margin-bottom: 20px;
        }

        .icon-large svg {
            filter: drop-shadow(0 0 10px var(--accent-glow));
        }

        .feature-card h3 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.4rem;
            margin-bottom: 10px;
            color: #fff;
        }

        .feature-card p {
            color: #888;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        /* --- VISUAL MOCKUPS CSS --- */
        .visual-mockup {
            margin-top: auto;
            border-radius: 12px;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 20px;
            position: relative;
            height: 150px;
            overflow: hidden;
        }

        .graph-mockup {
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle, rgba(0, 210, 106, 0.1) 0%, transparent 70%);
            position: relative;
        }

        .node {
            width: 12px;
            height: 12px;
            background: var(--accent-primary);
            border-radius: 50%;
            box-shadow: 0 0 15px var(--accent-primary);
            position: absolute;
            z-index: 2;
        }

        .n-center {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 18px;
            height: 18px;
            box-shadow: 0 0 25px var(--accent-primary);
        }

        .n1 {
            top: 30%;
            left: 20%;
            animation: pulseNode 3s infinite;
        }

        .n2 {
            top: 20%;
            left: 80%;
            animation: pulseNode 3s infinite 0.5s;
        }

        .n3 {
            top: 70%;
            left: 70%;
            animation: pulseNode 3s infinite 1.0s;
        }

        .n4 {
            top: 80%;
            left: 30%;
            animation: pulseNode 3s infinite 1.5s;
        }

        .n5 {
            top: 60%;
            left: 10%;
            animation: pulseNode 3s infinite 2.0s;
        }

        @keyframes pulseNode {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.8;
            }

            50% {
                transform: scale(1.3);
                opacity: 1;
                box-shadow: 0 0 20px var(--accent-primary);
            }
        }

        .search-visual {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding-left: 20px;
        }

        .search-bar-mock {
            background: #000;
            border: 1px solid #333;
            padding: 12px 20px;
            border-radius: 8px;
            width: 100%;
            display: flex;
            align-items: center;
            color: #666;
            font-family: monospace;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .cursor-blink {
            width: 2px;
            height: 16px;
            background: var(--accent-primary);
            margin-left: 5px;
            animation: blink 1s infinite;
        }

        @keyframes blink {
            50% {
                opacity: 0;
            }
        }

        .flex-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100%;
        }

        .text-side {
            width: 50%;
        }

        .visual-side {
            width: 45%;
        }

        /* Mobile adjustments for Bento */
        @media(max-width: 900px) {
            .grid-container {
                grid-template-columns: 1fr;
            }

            .grid-item.span-2 {
                grid-column: span 1;
            }

            .grid-item.row-2 {
                grid-row: span 1;
            }

            .flex-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .text-side,
            .visual-side {
                width: 100%;
                margin-top: 20px;
            }
        }

        /* --- DEEP DIVE --- */
        .deep-dive-section {
            padding: 120px 5%;
            margin-top: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            border-top: none;
        }

        .dive-container {
            max-width: 1100px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 60px;
        }

        .dive-content h2 {
            font-family: 'Space Grotesk';
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .dive-content p {
            font-size: 1.1rem;
            color: #999;
            margin-bottom: 30px;
            max-width: 500px;
        }

        .learn-more {
            color: var(--accent-primary);
            text-decoration: none;
            font-weight: 600;
            border-bottom: 1px solid transparent;
            transition: 0.3s;
        }

        .learn-more:hover {
            border-color: var(--accent-primary);
        }

        .lock-visual svg {
            display: block;
            margin: 0 auto;
            filter: drop-shadow(0 0 20px var(--accent-glow));
            animation: floatLock 4s ease-in-out infinite;
        }

        @keyframes floatLock {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        @media(max-width: 768px) {
            .dive-container {
                flex-direction: column;
                text-align: center;
            }

            .dive-content p {
                margin: 0 auto 30px;
            }
        }

        /* --- FOOTER --- */
        footer {
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding: 60px 8% 40px;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(20px);
            color: var(--text-muted);
            position: relative;
            overflow: hidden;
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 1px;
            background: radial-gradient(circle, rgba(0, 210, 106, 0.5) 0%, transparent 100%);
            box-shadow: 0 0 20px rgba(0, 210, 106, 0.5);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr 0.8fr 0.8fr;
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto 50px;
        }

        .footer-brand h2 {
            color: #fff;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 2rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -1px;
        }

        .footer-brand p {
            font-size: 1.05rem;
            color: #777;
            max-width: 320px;
            line-height: 1.7;
        }

        .footer-col h4 {
            color: #fff;
            margin-bottom: 25px;
            font-size: 1.2rem;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 15px;
        }

        .footer-col ul li a {
            color: #888;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1rem;
            display: inline-block;
        }

        .footer-col ul li a:hover {
            color: var(--accent-primary);
            transform: translateX(5px);
        }

        .footer-bottom {
            max-width: 1400px;
            margin: 0 auto;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.95rem;
            color: #444;
        }

        .footer-bottom a {
            color: #666;
            text-decoration: none;
            transition: 0.3s;
        }

        .footer-bottom a:hover {
            color: #fff;
        }

        .footer-bottom a:hover {
            color: #fff;
        }

        @media (max-width: 768px) {
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .footer-col {
                text-align: center;
            }

            .footer-brand {
                text-align: center;
                align-items: center;
            }

            .footer-brand h2 {
                justify-content: center;
            }

            .footer-brand p {
                margin: 0 auto;
            }

            .footer-bottom {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }
        }

        #glCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
        }
    </style>
</head>

<body>
    <canvas id="glCanvas"></canvas>

    <?php include 'includes/navbar.php'; ?>

    <div class="page-hero">
        <div class="glow-stack"></div>
        <h1>Tools for Thought</h1>
        <p>Discover the features that make QuickNote the ultimate second brain.</p>
    </div>

    <section class="deep-dive" style="margin-top: -60px;">
        <div class="grid-container">
            <!-- Large Hero Card -->
            <div class="grid-item span-2 row-2 feature-card highlight-glow">
                <div class="card-content">
                    <div class="icon-large">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h3>Smart Graph Tags</h3>
                    <p>Connect ideas with a flexible tagging system. Visualize relationships between your notes in a
                        force-directed graph.</p>
                    <div class="visual-mockup graph-mockup">
                        <svg width="100%" height="100%" style="position:absolute; top:0; left:0; z-index:1;">
                            <line x1="50%" y1="50%" x2="20%" y2="30%" stroke="rgba(0,210,106,0.3)" stroke-width="1" />
                            <line x1="50%" y1="50%" x2="80%" y2="20%" stroke="rgba(0,210,106,0.3)" stroke-width="1" />
                            <line x1="50%" y1="50%" x2="70%" y2="70%" stroke="rgba(0,210,106,0.3)" stroke-width="1" />
                            <line x1="50%" y1="50%" x2="30%" y2="80%" stroke="rgba(0,210,106,0.3)" stroke-width="1" />
                            <line x1="50%" y1="50%" x2="10%" y2="60%" stroke="rgba(0,210,106,0.3)" stroke-width="1" />
                        </svg>
                        <div class="node n-center"></div>
                        <div class="node n1"></div>
                        <div class="node n2"></div>
                        <div class="node n3"></div>
                        <div class="node n4"></div>
                        <div class="node n5"></div>
                    </div>
                </div>
            </div>

            <!-- Standard Card -->
            <div class="grid-item feature-card">
                <div class="icon-box">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <h3>Instant Auto-Save</h3>
                <p>We save every keystroke instantly.</p>
            </div>

            <!-- Standard Card -->
            <div class="grid-item feature-card">
                <div class="icon-box">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path
                            d="M21 12V7a2 2 0 00-2-2H5a2 2 0 00-2 2v5m18 0v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5m18 0h-3m-12 0H3m6 0h6"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <h3>Dark Mode</h3>
                <p>Easy on the eyes, day or night.</p>
            </div>

            <!-- Wide Card -->
            <div class="grid-item span-2 feature-card">
                <div class="flex-row">
                    <div class="text-side">
                        <div class="icon-box">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                        <h3>Full-Text Search</h3>
                        <p>Find anything instantly, even text inside PDFs. Your memory, upgraded.</p>
                    </div>
                    <div class="visual-side search-visual">
                        <div class="search-bar-mock"><span>Search...</span>
                            <div class="cursor-blink"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tall Card -->
            <!-- Removed Mobile Sync Card -->

            <!-- Standard Card -->
            <div class="grid-item feature-card">
                <div class="icon-box">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <h3>Media Embeds</h3>
                <p>Drag & drop images and files.</p>
            </div>
        </div>
    </section>

    <!-- Deep Dive Section -->
    <section class="deep-dive-section">
        <div class="dive-container">
            <div class="dive-content">
                <h2>Encryption at Rest</h2>
                <p>Your thoughts are private by default. We use AES-256 encryption to ensure that only you can read your
                    notes. Not even our engineers can see them.</p>
                <a href="#" class="learn-more">Read Security Whitepaper →</a>
            </div>
            <div class="dive-visual">
                <div class="lock-visual" style="display:flex; gap:15px; flex-wrap:wrap; justify-content: center;">
                    <!-- Lock -->
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="var(--accent-primary)"
                        stroke-width="1.5">
                        <path
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <!-- Shield -->
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="var(--accent-primary)"
                        stroke-width="1.5" style="animation-delay: 0.5s;">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    <!-- Key -->
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="var(--accent-primary)"
                        stroke-width="1.5" style="animation-delay: 1.0s;">
                        <path
                            d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 11-7.778 7.778 5.5 5.5 0 017.778-7.778zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <!-- Eye Off (Privacy) -->
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="var(--accent-primary)"
                        stroke-width="1.5" style="animation-delay: 1.5s;">
                        <path
                            d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24M1 1l22 22"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <!-- Database (Storage) -->
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="var(--accent-primary)"
                        stroke-width="1.5" style="animation-delay: 2.0s;">
                        <path
                            d="M4 7v10c0 2.21 3.58 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.58 4 8 4s8-1.79 8-4M4 7c0-2.21 3.58-4 8-4s8 1.79 8 4m0 5c0 2.21-3.58 4-8 4s-8-1.79-8-4"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>

</html>