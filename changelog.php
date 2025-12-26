<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changelog - QuickNote</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Grotesk:wght@500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/landing.css">
    <style>
        /* PAGE HERO OVERRIDES */
        .page-hero {
            padding: 180px 20px 60px;
            text-align: center;
        }

        .page-hero h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 4rem;
            margin-bottom: 20px;
            background: linear-gradient(90deg, #fff, #aaffd3, #fff);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200%;
            animation: grad 6s infinite;
        }

        .page-hero p {
            color: #aaa;
            font-size: 1.2rem;
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

        /* TIMELINE */
        .timeline-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px 120px;
            position: relative;
        }

        /* Vertical Line */
        .timeline-container::before {
            content: '';
            position: absolute;
            top: 40px;
            bottom: 40px;
            left: 50px;
            width: 2px;
            background: rgba(255, 255, 255, 0.1);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 60px;
            padding-left: 100px;
        }

        /* Dot */
        .timeline-item::after {
            content: '';
            position: absolute;
            left: 45px;
            top: 5px;
            width: 12px;
            height: 12px;
            background: #111;
            border: 2px solid var(--accent-primary);
            border-radius: 50%;
            z-index: 2;
        }

        .timeline-item.major::after {
            background: var(--accent-primary);
            box-shadow: 0 0 15px var(--accent-glow);
        }

        .version-date {
            position: absolute;
            left: -20px;
            top: 5px;
            width: 60px;
            text-align: right;
            font-size: 0.85rem;
            color: #666;
            font-family: 'Space Grotesk';
        }

        .changelog-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 30px;
            backdrop-filter: blur(10px);
        }

        .version-tag {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            background: rgba(0, 210, 106, 0.1);
            color: var(--accent-primary);
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .changelog-card h3 {
            font-family: 'Space Grotesk';
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #fff;
        }

        .changelog-list {
            list-style: none;
        }

        .changelog-list li {
            margin-bottom: 10px;
            color: #ccc;
            position: relative;
            padding-left: 20px;
        }

        .changelog-list li::before {
            content: '•';
            color: var(--accent-primary);
            position: absolute;
            left: 0;
        }

        @media(max-width: 768px) {
            .timeline-container::before {
                left: 20px;
            }

            .timeline-item {
                padding-left: 50px;
            }

            .timeline-item::after {
                left: 15px;
            }

            .version-date {
                display: none;
            }
        }
    </style>
</head>

<body>
    <canvas id="glCanvas"></canvas>

    <?php include 'includes/navbar.php'; ?>

    <div class="page-hero">
        <h1>Changelog</h1>
        <p>A history of improvements and updates.</p>
    </div>

    <div class="timeline-container">
        <!-- v1.5.1 -->
        <div class="timeline-item major">
            <span class="version-date">Dec 26</span>
            <div class="changelog-card">
                <span class="version-tag">v1.5.1</span>
                <h3>Visual Polish & Sync</h3>
                <ul class="changelog-list">
                    <li><strong>Real-Time Sidebar Sync:</strong> Sidebar note cards now instantly reflect the Title's
                        Font, Weight, Color, and Style as you type.</li>
                    <li><strong>Smart Content Preview:</strong> The "Start writing" snippet in the sidebar now
                        dynamically mimics the font and color of your note's body text.</li>
                    <li><strong>Toolbar Refined:</strong> Removed margins for a sleek, full-width toolbar layout.</li>
                    <li><strong>Zero FOUC:</strong> Eliminated the initial "Flash of Unstyled Content" for a buttery
                        smooth editor load.</li>
                </ul>
            </div>
        </div>
        <!-- v1.5.0 -->
        <div class="timeline-item major">
            <span class="version-date">Dec 26</span>
            <div class="changelog-card">
                <span class="version-tag">v1.5.0</span>
                <h3>Authentication & Deployment</h3>
                <ul class="changelog-list">
                    <li><strong>Authentication Overhaul:</strong> Redesigned Login, Register, and Forgot Password pages
                        with premium Split-Card layout and interactive 3D elements (DNA Helix, Vortex).</li>
                    <li><strong>Enhanced Login:</strong> Added support for Username/Email login (case-insensitive) and
                        password visibility toggles.</li>
                    <li><strong>Production Ready:</strong> Implemented dynamic database connection for seamless
                        switching between Localhost and InfinityFree.</li>
                    <li><strong>Database Sync:</strong> Updated schema to include missing tables (tags, note_tags,
                        attachments) for full feature support.</li>
                    <li><strong>Bug Fixes:</strong> Fixed HTTP 500 errors, password case-sensitivity, and corrected
                        logout redirection.</li>
                </ul>
            </div>
        </div>
        <!-- v1.4.0 -->
        <div class="timeline-item major">
            <span class="version-date">Dec 25</span>
            <div class="changelog-card">
                <span class="version-tag">v1.4.0</span>
                <h3>Visual Overhaul & Simplification</h3>
                <ul class="changelog-list">
                    <li><strong>Redesigned Contact Page:</strong> A stunning, split-layout design with glassmorphism,
                        animated inputs, and WebGL shader support.</li>
                    <li><strong>Standardized Footer:</strong> Implemented a consistent 4-column footer (Brand, Product,
                        Company, Get in Touch) across the entire platform.</li>
                    <li><strong>Simplified Navigation:</strong> Streamlined the top navbar. "QuickNote" now links home,
                        and links are focused purely on authentication.</li>
                    <li><strong>Performance Boost:</strong> Removed 600+ lines of redundant CSS from the landing page.
                    </li>
                    <li><strong>Cleanup:</strong> Deprecated and removed unused Blog and Careers pages for a tighter
                        focus.</li>
                </ul>
            </div>
        </div>
        <!-- v1.3.0 -->
        <div class="timeline-item major">
            <div class="version-date">Dec 24</div>
            <div class="changelog-card">
                <span class="version-tag">v1.3.0</span>
                <h3>Features Redesign</h3>
                <ul class="changelog-list">
                    <li>Complete redesign of Features page with Bento Grid layout.</li>
                    <li>Enhanced "Smart Graph" visualization with dynamic animations.</li>
                    <li>Added detailed "Deep Dive" visual sections for Encryption and Search.</li>
                    <li>Streamlined feature set for better clarity.</li>
                </ul>
            </div>
        </div>
        <!-- v1.2.0 -->
        <div class="timeline-item major">
            <div class="version-date">Dec 23</div>
            <div class="changelog-card">
                <span class="version-tag">v1.2.0</span>
                <h3>Visual Overhaul</h3>
                <ul class="changelog-list">
                    <li>Added WebGL "Smoke" shader background globally.</li>
                    <li>Redesigned footer with glassmorphism and glow effects.</li>
                    <li>Updated Navigation bar with smart scroll behavior and transparency.</li>
                    <li>Introduced infinite scrolling marquee on homepage.</li>
                </ul>
            </div>
        </div>

        <!-- v1.1.0 -->
        <div class="timeline-item">
            <div class="version-date">Dec 10</div>
            <div class="changelog-card">
                <span class="version-tag">v1.1.0</span>
                <h3>Graph View Beta</h3>
                <ul class="changelog-list">
                    <li>Visualizing note connections with a force-directed graph.</li>
                    <li>Improved search performance by 40%.</li>
                    <li>Fixed bug where dark mode wouldn't persist.</li>
                </ul>
            </div>
        </div>

        <!-- v1.0.0 -->
        <div class="timeline-item">
            <div class="version-date">Nov 22</div>
            <div class="changelog-card">
                <span class="version-tag">v1.0.0</span>
                <h3>Initial Launch</h3>
                <ul class="changelog-list">
                    <li>Released QuickNote to the public.</li>
                    <li>Core features: Auto-save, Markdown support, Tagging.</li>
                </ul>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        const canvas = document.getElementById("glCanvas");
        const gl = canvas.getContext("webgl");
        if (!gl) console.error("WebGL not supported");
        function resize() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            gl.viewport(0, 0, canvas.width, canvas.height);
        }
        window.addEventListener("resize", resize);
        resize();

        const vsSource = `attribute vec4 aVertexPosition; void main() { gl_Position = aVertexPosition; }`;
        const fsSource = `
            precision mediump float;
            uniform vec2 u_resolution;
            uniform float u_time;
            float random (in vec2 st) { return fract(sin(dot(st.xy, vec2(12.9898,78.233))) * 43758.5453123); }
            float noise (in vec2 st) {
                vec2 i = floor(st); vec2 f = fract(st);
                float a = random(i); float b = random(i + vec2(1.0, 0.0));
                float c = random(i + vec2(0.0, 1.0)); float d = random(i + vec2(1.0, 1.0));
                vec2 u = f * f * (3.0 - 2.0 * f);
                return mix(a, b, u.x) + (c - a)* u.y * (1.0 - u.x) + (d - b) * u.x * u.y;
            }
            float fbm (in vec2 st) {
                float v = 0.0; float a = 0.5; vec2 shift = vec2(100.0);
                mat2 rot = mat2(cos(0.5), sin(0.5), -sin(0.5), cos(0.50));
                for (int i = 0; i < 5; i++) { v += a * noise(st); st = rot * st * 2.0 + shift; a *= 0.5; }
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
                vec3 colorBlack = vec3(0.0, 0.0, 0.0);
                vec3 colorDarkGreen = vec3(0.0, 0.12, 0.06);
                vec3 colorVibrant = vec3(0.0, 0.4, 0.2);
                color = mix(colorBlack, colorDarkGreen, clamp((f*f)*4.0,0.0,1.0));
                color = mix(color, colorVibrant, clamp(length(q),0.0,1.0));
                color = mix(color, vec3(0.0, 0.0, 0.0), clamp(length(r.x),0.0,1.0));
                gl_FragColor = vec4((f*f*f+.6*f*f+.5*f)*color * 1.0, 1.0);
            }
        `;
        function createShader(gl, type, source) { const s = gl.createShader(type); gl.shaderSource(s, source); gl.compileShader(s); return s; }
        const program = gl.createProgram();
        gl.attachShader(program, createShader(gl, gl.VERTEX_SHADER, vsSource));
        gl.attachShader(program, createShader(gl, gl.FRAGMENT_SHADER, fsSource));
        gl.linkProgram(program);
        const positionBuffer = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, positionBuffer);
        gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1.0, 1.0, 1.0, 1.0, -1.0, -1.0, 1.0, -1.0]), gl.STATIC_DRAW);
        const posLoc = gl.getAttribLocation(program, "aVertexPosition");
        const resLoc = gl.getUniformLocation(program, "u_resolution");
        const timeLoc = gl.getUniformLocation(program, "u_time");
        function render(time) {
            time *= 0.001;
            gl.useProgram(program);
            gl.enableVertexAttribArray(posLoc);
            gl.bindBuffer(gl.ARRAY_BUFFER, positionBuffer);
            gl.vertexAttribPointer(posLoc, 2, gl.FLOAT, false, 0, 0);
            gl.uniform2f(resLoc, canvas.width, canvas.height);
            gl.uniform1f(timeLoc, time);
            gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);
            requestAnimationFrame(render);
        }
        requestAnimationFrame(render);
        // Nav Logic
        const nav = document.querySelector('nav');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) nav.classList.add('nav-scrolled'); else nav.classList.remove('nav-scrolled');
        });
    </script>
</body>

</html>