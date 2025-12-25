<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickNote | The Second Brain for Creators</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Grotesk:wght@500;700&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --bg-dark: #0a0a0a;
            --bg-card: #141414;
            --accent-primary: #00d26a;
            /* Vibrant Green */
            --accent-glow: rgba(0, 210, 106, 0.4);
            --text-main: #ffffff;
            --text-muted: #888888;
            --border-light: #333;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: transparent;
            /* Changed from var(--bg-dark) */
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* --- ANIMATIONS --- */
        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        @keyframes glow {
            0% {
                box-shadow: 0 0 20px var(--accent-glow);
            }

            50% {
                box-shadow: 0 0 40px var(--accent-glow);
            }

            100% {
                box-shadow: 0 0 20px var(--accent-glow);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* --- NAV --- */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 5%;
            position: fixed;
            width: 100%;
            top: 0;
            background: transparent;
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .nav-hidden {
            transform: translateY(-100%);
        }

        .nav-scrolled {
            background: rgba(5, 5, 5, 0.6);
            backdrop-filter: blur(15px);
            padding: 15px 5%;
            /* Compact mode */
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
        }

        .logo {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.8rem;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo span {
            color: var(--accent-primary);
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            margin-left: 30px;
            font-weight: 500;
            transition: 0.3s;
        }

        .nav-links a:hover {
            color: #fff;
        }

        .btn-cta-nav {
            padding: 10px 24px;
            background: #fff;
            color: #000;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn-cta-nav:hover {
            background: var(--accent-primary);
            color: #fff;
            box-shadow: 0 0 15px var(--accent-glow);
        }

        /* --- HERO --- */
        .hero {
            min-height: 90vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            padding: 160px 20px 60px;
            /* Reduced for symmetry with marquee */
            /* overflow: hidden; REMOVED to prevent shadow clipping */
        }

        /* Background Mesh Gradient Blob */
        .glow-blob {
            position: absolute;
            width: 800px;
            height: 600px;
            background: radial-gradient(circle, rgba(0, 210, 106, 0.15) 0%, transparent 60%);
            top: -150px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 0;
            pointer-events: none;
            filter: blur(80px);
            /* Softer blur */
            animation: pulseGlow 8s infinite alternate;
        }

        @keyframes pulseGlow {
            0% {
                opacity: 0.5;
                transform: translateX(-50%) scale(1);
            }

            100% {
                opacity: 0.8;
                transform: translateX(-50%) scale(1.1);
            }
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

        .hero h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 5.5rem;
            line-height: 1.05;
            letter-spacing: -2px;
            margin-bottom: 30px;
            z-index: 1;

            /* Gradient Text Logic */
            background: linear-gradient(90deg, #ffffff 0%, #aaffd3 50%, #ffffff 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;

            /* Drop shadow connects to the shape of the letters */
            filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.8));

            animation: fadeIn 0.8s ease-out, gradientAnim 6s ease infinite;
            max-width: 900px;
        }

        .hero p {
            font-size: 1.25rem;
            color: #999;
            max-width: 650px;
            margin-bottom: 50px;
            z-index: 1;
            line-height: 1.7;
            animation: fadeIn 1s ease-out 0.2s backwards;
        }

        .cta-group {
            display: flex;
            gap: 25px;
            /* More breathing room between buttons */
            z-index: 1;
            animation: fadeIn 1s ease-out 0.4s backwards;
            margin-bottom: 60px;
            /* Space between CTA and UI Preview */
        }

        .btn-primary {
            padding: 16px 42px;
            background: var(--accent-primary);
            color: #050505;
            border-radius: 50px;
            /* Pill shape */
            font-weight: 700;
            font-size: 1.05rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 0 25px rgba(0, 210, 106, 0.25);
            border: 2px solid var(--accent-primary);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 50px rgba(0, 210, 106, 0.5);
            background: #00e074;
            border-color: #00e074;
        }

        .btn-secondary {
            padding: 16px 42px;
            background: rgba(255, 255, 255, 0.03);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 50px;
            /* Pill shape */
            font-weight: 600;
            font-size: 1.05rem;
            text-decoration: none;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #fff;
            transform: translateY(-3px);
        }

        /* --- UI PREVIEW --- */
        .ui-preview-container {
            /* Existing styles preserved, just adding margin adjustment via margin-top in hero padding */
            width: 85%;
            /* Slightly narrower */
            max-width: 1000px;
            background: #191919;
            border: 1px solid #2a2a2a;
            border-radius: 16px;
            padding: 12px;
            box-shadow: 0 80px 140px -40px rgba(0, 0, 0, 0.9);
            /* Deeper shadow */
            transform: perspective(1200px) rotateX(5deg);
            /* Stronger perspective */
            z-index: 2;
            animation: fadeIn 1.2s ease-out 0.5s backwards;
            position: relative;
        }

        /* Add a glow behind the UI card */
        .ui-preview-container::after {
            content: '';
            position: absolute;
            top: 20%;
            left: 10%;
            right: 10%;
            bottom: -20px;
            background: var(--accent-glow);
            filter: blur(60px);
            z-index: -1;
            opacity: 0.3;
        }

        .ui-mockup-header {
            display: flex;
            gap: 8px;
            padding: 12px 16px;
            border-bottom: 1px solid #2a2a2a;
            background: #141414;
            border-radius: 12px 12px 0 0;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .red {
            background: #ff5f56;
        }

        .yellow {
            background: #ffbd2e;
        }

        .green {
            background: #27c93f;
        }

        .ui-content {
            height: 400px;
            /* Placeholder for screenshot */
            background: linear-gradient(135deg, #222 0%, #151515 100%);
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #444;
            font-family: 'Roboto Mono', monospace;
        }

        /* --- FEATURES --- */
        .marquee-strip {
            width: 100%;
            overflow: hidden;
            padding: 40px 0;
            background: transparent;
            position: relative;
            z-index: 1;
            /* Fade edges */
            -webkit-mask-image: linear-gradient(to right, transparent, black 20%, black 80%, transparent);
            mask-image: linear-gradient(to right, transparent, black 20%, black 80%, transparent);
        }

        .marquee-content {
            display: flex;
            white-space: nowrap;
            /* Simple infinite loop: requires duplicate text inline */
            animation: marquee 40s linear infinite;
        }

        .marquee-content span {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 6rem;
            font-weight: 700;
            color: transparent;
            -webkit-text-stroke: 2px rgba(255, 255, 255, 0.15);
            /* Boosted visibility */
            margin-right: 60px;
            text-transform: uppercase;
            letter-spacing: 4px;
        }

        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .features {
            padding: 60px 8% 100px;
            /* Symmetrical top padding */
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 40px;
            max-width: 1400px;
            margin: 0 auto;
            perspective: 1000px;
            /* Enable 3D perspective for children */
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.03);
            /* Ultra subtle glass */
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 60px 40px;
            border-radius: 24px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(20px);
            /* Heavy blur for readability */
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        /* Gradient sheen effect on hover */
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
            transition: 0.5s;
        }

        .feature-card:hover::before {
            left: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px) rotateX(5deg);
            border-color: rgba(0, 210, 106, 0.3);
            /* Green border hint */
            background: rgba(255, 255, 255, 0.05);
            box-shadow: 0 30px 60px -20px rgba(0, 0, 0, 0.5);
        }

        .icon-box {
            font-size: 3.5rem;
            /* Much larger icons */
            color: var(--text-main);
            margin-bottom: 30px;
            transition: 0.3s;
            background: transparent;
            border: none;
            width: auto;
            height: auto;
            text-shadow: 0 0 30px var(--accent-glow);
            /* Glowing icons */
        }

        .feature-card:hover .icon-box {
            transform: scale(1.1) translateZ(20px);
            /* Pop out in 3D */
            color: var(--accent-primary);
        }

        .feature-card h3 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            color: #fff;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
        }

        .feature-card p {
            color: #aaa;
            font-size: 1.1rem;
            line-height: 1.7;
            margin: 0;
            font-weight: 300;
        }

        /* --- FOOTER --- */
        footer {
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding: 60px 8% 40px;
            /* Reduced top padding (100px -> 60px) */
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(20px);
            color: var(--text-muted);
            position: relative;
            overflow: hidden;
        }

        /* Glowing top border effect */
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
            /* Tighter column distribution */
            gap: 30px;
            /* Reduced gap (60px -> 30px) */
            max-width: 1200px;
            /* Reduced max-width to pull content inward */
            margin: 0 auto 50px;
            /* Reduced bottom margin */
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
            line-height: 1.7;
            max-width: 320px;
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
            position: relative;
        }

        .footer-col ul li a:hover {
            color: var(--accent-primary);
            transform: translateX(8px);
            text-shadow: 0 0 15px rgba(0, 210, 106, 0.4);
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

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 3rem;
            }

            .nav-links {
                display: none;
            }
        }

        /* ... (previous styles) ... */

        #glCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            opacity: 1;
            /* Full Opacity - no ghosting */
        }
    </style>
</head>

<body>
    <canvas id="glCanvas"></canvas>

    <nav>
        <div class="logo">
            <span>●</span> QuickNote
        </div>
        <div class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="btn-cta-nav">Dashboard</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php" class="btn-cta-nav">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="hero">
        <div class="glow-blob"></div>

        <h1>Your thoughts,<br>organized beautifully.</h1>
        <p>Expertly crafted for ideas, lists, and projects. Experience the ultimate "Second Brain" designed for speed
            and clarity.</p>

        <div class="cta-group">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="btn-primary">Go to Dashboard</a>
            <?php else: ?>
                <a href="register.php" class="btn-primary">Start for Free</a>
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

    <footer>
        <div class="footer-grid">
            <div class="footer-brand">
                <h2><span style="color:var(--accent-primary)">●</span> QuickNote</h2>
                <p>The privacy-first second brain for creators, developers, and thinkers. Organize instantly.</p>
            </div>
            <div class="footer-col">
                <h4>Product</h4>
                <ul>
                    <li><a href="#">Features</a></li>
                    <li><a href="#">Pricing</a></li>
                    <li><a href="#">Changelog</a></li>
                    <li><a href="#">Download</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Company</h4>
                <ul>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Connect</h4>
                <ul>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">GitHub</a></li>
                    <li><a href="#">Discord</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div>&copy; <?php echo date("Y"); ?> QuickNote Inc. All rights reserved.</div>
            <div style="display:flex; gap:20px;">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </footer>

    <script>
        const canvas = document.getElementById("glCanvas");
        const gl = canvas.getContext("webgl");

        if (!gl) {
            console.error("WebGL not supported");
        }

        function resize() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            gl.viewport(0, 0, canvas.width, canvas.height);
        }
        window.addEventListener("resize", resize);
        resize();

        // Vertex Shader
        const vsSource = `
            attribute vec4 aVertexPosition;
            void main() {
                gl_Position = aVertexPosition;
            }
        `;

        // Fragment Shader (Inspired by The Book of Shaders / Fluid Noise)
        const fsSource = `
            precision mediump float;
            uniform vec2 u_resolution;
            uniform float u_time;

            // Simple random
            float random (in vec2 st) {
                return fract(sin(dot(st.xy, vec2(12.9898,78.233))) * 43758.5453123);
            }

            // Noise function
            float noise (in vec2 st) {
                vec2 i = floor(st);
                vec2 f = fract(st);

                // Four corners in 2D of a tile
                float a = random(i);
                float b = random(i + vec2(1.0, 0.0));
                float c = random(i + vec2(0.0, 1.0));
                float d = random(i + vec2(1.0, 1.0));

                vec2 u = f * f * (3.0 - 2.0 * f);

                return mix(a, b, u.x) +
                        (c - a)* u.y * (1.0 - u.x) +
                        (d - b) * u.x * u.y;
            }

            // Fractal Brownian Motion
            float fbm (in vec2 st) {
                float v = 0.0;
                float a = 0.5;
                vec2 shift = vec2(100.0);
                mat2 rot = mat2(cos(0.5), sin(0.5),
                                -sin(0.5), cos(0.50));
                for (int i = 0; i < 5; i++) {
                    v += a * noise(st);
                    st = rot * st * 2.0 + shift;
                    a *= 0.5;
                }
                return v;
            }

            void main() {
                vec2 st = gl_FragCoord.xy/u_resolution.xy;
                st.x *= u_resolution.x/u_resolution.y;

                vec3 color = vec3(0.0);

                vec2 q = vec2(0.);
                q.x = fbm( st + 0.00*u_time);
                q.y = fbm( st + vec2(1.0));

                vec2 r = vec2(0.);
                r.x = fbm( st + 1.0*q + vec2(1.7,9.2)+ 0.15*u_time );
                r.y = fbm( st + 1.0*q + vec2(8.3,2.8)+ 0.126*u_time);

                float f = fbm(st+r);

                // Mix colors: Deep Black -> Dark Green -> Vibrant Green
                vec3 colorBlack = vec3(0.0, 0.0, 0.0);
                vec3 colorDarkGreen = vec3(0.0, 0.12, 0.06); // Slightly richer green
                vec3 colorVibrant = vec3(0.0, 0.4, 0.2); // Restore some vibrancy

                color = mix(colorBlack,
                            colorDarkGreen,
                            clamp((f*f)*4.0,0.0,1.0));

                color = mix(color,
                            colorVibrant,
                            clamp(length(q),0.0,1.0));

                color = mix(color,
                            vec3(0.0, 0.0, 0.0),
                            clamp(length(r.x),0.0,1.0));

                // Standard multiplier now that background is black
                gl_FragColor = vec4((f*f*f+.6*f*f+.5*f)*color * 1.0, 1.0);
            }
        `;

        function createShader(gl, type, source) {
            const shader = gl.createShader(type);
            gl.shaderSource(shader, source);
            gl.compileShader(shader);
            if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
                console.error(gl.getShaderInfoLog(shader));
                gl.deleteShader(shader);
                return null;
            }
            return shader;
        }

        const vertexShader = createShader(gl, gl.VERTEX_SHADER, vsSource);
        const fragmentShader = createShader(gl, gl.FRAGMENT_SHADER, fsSource);
        const program = gl.createProgram();
        gl.attachShader(program, vertexShader);
        gl.attachShader(program, fragmentShader);
        gl.linkProgram(program);

        const positionBuffer = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, positionBuffer);
        const positions = [-1.0, 1.0, 1.0, 1.0, -1.0, -1.0, 1.0, -1.0];
        gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(positions), gl.STATIC_DRAW);

        const positionLocation = gl.getAttribLocation(program, "aVertexPosition");
        const resolutionLocation = gl.getUniformLocation(program, "u_resolution");
        const timeLocation = gl.getUniformLocation(program, "u_time");

        function render(time) {
            time *= 0.001; // convert to seconds

            gl.useProgram(program);
            gl.enableVertexAttribArray(positionLocation);
            gl.bindBuffer(gl.ARRAY_BUFFER, positionBuffer);
            gl.vertexAttribPointer(positionLocation, 2, gl.FLOAT, false, 0, 0);

            gl.uniform2f(resolutionLocation, canvas.width, canvas.height);
            gl.uniform1f(timeLocation, time);

            gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);
            requestAnimationFrame(render);
        }
        requestAnimationFrame(render);

        // Smart Navbar Logic
        let lastScrollY = 0;
        const nav = document.querySelector('nav');

        window.addEventListener('scroll', () => {
            const currentScrollY = window.scrollY;

            // Glass effect when not at top
            if (currentScrollY > 50) {
                nav.classList.add('nav-scrolled');
            } else {
                nav.classList.remove('nav-scrolled');
            }

            // Hide/Show logic
            if (currentScrollY > lastScrollY && currentScrollY > 100) {
                // Scrolling DOWN
                nav.classList.add('nav-hidden');
            } else {
                // Scrolling UP
                nav.classList.remove('nav-hidden');
            }

            lastScrollY = currentScrollY;
        });
    </script>
</body>

</html>