<footer>
    <div class="footer-grid">
        <div class="footer-brand">
            <h2><span style="color:var(--accent-primary)">●</span> QuickNote</h2>
            <p>The privacy-first second brain for creators, developers, and thinkers. Organize instantly.</p>
        </div>
        <div class="footer-col">
            <h4>Product</h4>
            <ul>
                <li><a href="features.php">Features</a></li>
                <li><a href="changelog.php">Changelog</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Company</h4>
            <ul>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Get in Touch</h4>
            <ul>
                <li><a href="contact.php">dianacastillon@gmail.com</a></li>
                <li><span>Zamboanga City</span></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <div>&copy; <?php echo date("Y"); ?> QuickNote Inc. All rights reserved.</div>
        <div style="display:flex; gap:20px;">
            <a href="privacy.php">Privacy Policy</a>
            <a href="terms.php">Terms of Service</a>
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
    if (nav) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) nav.classList.add('nav-scrolled');
            else nav.classList.remove('nav-scrolled');
        });
    }
</script>