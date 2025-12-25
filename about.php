<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - QuickNote</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Grotesk:wght@500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/landing.css">
    <style>
        :root {
            --accent-primary: #00d26a;
            --accent-glow: rgba(0, 210, 106, 0.4);
            --bg-glass: rgba(255, 255, 255, 0.03);
            --border-glass: rgba(255, 255, 255, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #000;
            color: #fff;
            overflow-x: hidden;
            min-height: 100vh;
            line-height: 1.6;
        }

        /* WEBGL CANVAS */
        #glCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
        }

        /* --- NAV STYLES REMOVED (using landing.css) --- */

        /* PAGE HERO */
        .about-hero {
            padding: 200px 10% 120px;
            text-align: center;
            position: relative;
        }

        .about-hero h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 4.5rem;
            line-height: 1.1;
            margin-bottom: 30px;
            background: linear-gradient(90deg, #fff, #aaffd3, #fff);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200%;
            animation: grad 6s infinite;
        }

        .about-hero p {
            color: #ccc;
            font-size: 1.3rem;
            max-width: 700px;
            margin: 0 auto;
        }

        @keyframes grad {
            0% {
                background-position: 0% 50%
            }

            50% {
                background-position: 100% 50%
            }

            100% {
                background-position: 0% 50%
            }
        }

        /* MISSION SECTION */
        .mission-section {
            padding: 100px 10%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 60px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .mission-text {
            flex: 1;
        }

        .mission-text h2 {
            font-family: 'Space Grotesk';
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .mission-text p {
            color: #aaa;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }

        .mission-visual {
            flex: 1;
            height: 400px;
            background: radial-gradient(circle at center, rgba(0, 210, 106, 0.1), transparent 70%);
            border: 1px solid var(--border-glass);
            border-radius: 24px;
            position: relative;
            overflow: hidden;
        }

        .mv-particle {
            position: absolute;
            background: #fff;
            border-radius: 50%;
            opacity: 0.6;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
        }

        /* Chaos State (Orbiting wildly) */
        .mv-p1 {
            width: 8px;
            height: 8px;
            top: 20%;
            left: 20%;
            animation: chaos1 10s infinite alternate;
        }

        .mv-p2 {
            width: 12px;
            height: 12px;
            top: 80%;
            left: 80%;
            animation: chaos2 12s infinite alternate;
        }

        .mv-p3 {
            width: 6px;
            height: 6px;
            top: 10%;
            left: 90%;
            animation: chaos3 8s infinite alternate;
        }

        .mv-p4 {
            width: 10px;
            height: 10px;
            top: 90%;
            left: 10%;
            animation: chaos4 15s infinite alternate;
        }

        .mv-p5 {
            width: 15px;
            height: 15px;
            top: 50%;
            left: 50%;
            animation: pulse-core 4s infinite;
            background: var(--accent-primary);
            box-shadow: 0 0 20px var(--accent-primary);
        }

        @keyframes chaos1 {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(100px, 50px);
            }
        }

        @keyframes chaos2 {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(-80px, -100px);
            }
        }

        @keyframes chaos3 {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(-150px, 100px);
            }
        }

        @keyframes chaos4 {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(50px, -80px);
            }
        }

        @keyframes pulse-core {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.8;
            }

            50% {
                transform: scale(1.5);
                opacity: 1;
            }
        }

        /* VALUES GRID (BENTO) */
        .values-section {
            padding: 100px 10%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-family: 'Space Grotesk';
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .value-card {
            background: var(--bg-glass);
            border: 1px solid var(--border-glass);
            border-radius: 20px;
            padding: 40px;
            backdrop-filter: blur(10px);
            transition: 0.3s;
        }

        .value-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent-primary);
        }

        .value-card h3 {
            font-family: 'Space Grotesk';
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #fff;
        }

        .value-card p {
            color: #999;
            font-size: 1rem;
        }

        .value-card.large {
            grid-column: span 2;
        }

        .icon-box {
            width: 50px;
            height: 50px;
            background: rgba(0, 210, 106, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            color: var(--accent-primary);
        }

        /* TEAM */
        .team-section {
            padding: 100px 10%;
            text-align: center;
        }

        .team-grid {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
            margin-top: 60px;
        }

        .team-member {
            width: 250px;
        }

        .member-img {
            width: 120px;
            height: 120px;
            background: #222;
            border-radius: 50%;
            margin: 0 auto 20px;
            border: 2px solid var(--border-glass);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #444;
            font-size: 2rem;
            font-family: 'Space Grotesk';
        }

        .member-img::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.8);
        }

        .team-member h4 {
            font-size: 1.2rem;
            margin-bottom: 5px;
            color: #fff;
        }

        .team-member p {
            color: var(--accent-primary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* FOOTER */
        footer {
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding: 60px 8% 40px;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(20px);
            color: #888;
            margin-top: 100px;
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
            font-family: 'Space Grotesk';
            margin-bottom: 20px;
        }

        .footer-col h4 {
            color: #fff;
            margin-bottom: 25px;
            font-family: 'Space Grotesk';
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
            transition: 0.3s;
        }

        .footer-col ul li a:hover {
            color: var(--accent-primary);
            padding-left: 5px;
        }

        .footer-bottom {
            max-width: 1400px;
            margin: 0 auto;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        @media(max-width: 768px) {
            .about-hero h1 {
                font-size: 3rem;
            }

            .mission-section {
                flex-direction: column;
            }

            .values-grid {
                grid-template-columns: 1fr;
            }

            .value-card.large {
                grid-column: span 1;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <!-- SHADER BG -->
    <canvas id="glCanvas"></canvas>

    <?php include 'includes/navbar.php'; ?>

    <section class="about-hero">
        <h1>Building the Extension<br> of Your Mind</h1>
        <p>We are on a mission to create the world's most intuitive and secure second brain. For thinkers, builders, and
            dreamers.</p>
    </section>

    <section class="mission-section">
        <div class="mission-text">
            <h2>From Chaos to Clarity</h2>
            <p>In a world of information overload, your best ideas often get lost. QuickNote was born from a frustration
                with cluttered, slow, and overly complex tools.</p>
            <p>We believe that note-taking should be as fast as thought itself. No friction, no loading screens, just
                pure flow. We're building the tool we always wanted to use.</p>
        </div>
        <div class="mission-visual">
            <div class="mv-particle mv-p1"></div>
            <div class="mv-particle mv-p2"></div>
            <div class="mv-particle mv-p3"></div>
            <div class="mv-particle mv-p4"></div>
            <div class="mv-particle mv-p5"></div> <!-- Core -->
            <!-- We could add SVG lines if needed, or leave abstract -->
        </div>
    </section>

    <section class="values-section">
        <div class="section-title">
            <h2>Our Core Values</h2>
        </div>
        <div class="values-grid">
            <div class="value-card large">
                <div class="icon-box">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                </div>
                <h3>Privacy First</h3>
                <p>We believe your thoughts are yours alone. That's why we built QuickNote with local-first principles
                    and AES-256 encryption. We can't see your notes, and we never will.</p>
            </div>
            <div class="value-card">
                <div class="icon-box">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M16.2 7.8l-2 6.3-6.4 2.1 2-6.3z" />
                    </svg>
                </div>
                <h3>Speed Matters</h3>
                <p>Friction kills creativity. Every interaction in QuickNote is optimized for milliseconds, not seconds.
                </p>
            </div>
            <div class="value-card">
                <div class="icon-box">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96" />
                        <line x1="12" y1="22.08" x2="12" y2="12" />
                    </svg>
                </div>
                <h3>Simplicity</h3>
                <p>We fight against feature bloat. We only build what truly adds value to your thinking process.</p>
            </div>
            <div class="value-card large">
                <div class="icon-box">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                </div>
                <h3>Community Driven</h3>
                <p>We build in public and listen to our users. Our changelog is a love letter to the people who use
                    QuickNote every day to organize their lives.</p>
            </div>
        </div>
    </section>

    <section class="team-section">
        <div class="section-title">
            <h2>Meet the Builder</h2>
        </div>
        <div class="team-grid">
            <div class="team-member">
                <div class="member-img">DC</div>
                <h4>Diana Mae T. Castillon</h4>
                <p>Creator & Lead Developer</p>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>

</html>