/**
 * WebGL Background Animation
 * Shared script for legal pages (and potentially others)
 */
document.addEventListener("DOMContentLoaded", () => {
  const canvas = document.getElementById("glCanvas");
  if (!canvas) return; // Exit if no canvas found

  const gl = canvas.getContext("webgl");

  if (gl) {
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
    function createShader(gl, type, source) {
      const s = gl.createShader(type);
      gl.shaderSource(s, source);
      gl.compileShader(s);
      return s;
    }
    const program = gl.createProgram();
    gl.attachShader(program, createShader(gl, gl.VERTEX_SHADER, vsSource));
    gl.attachShader(program, createShader(gl, gl.FRAGMENT_SHADER, fsSource));
    gl.linkProgram(program);
    const positionBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, positionBuffer);
    gl.bufferData(
      gl.ARRAY_BUFFER,
      new Float32Array([-1.0, 1.0, 1.0, 1.0, -1.0, -1.0, 1.0, -1.0]),
      gl.STATIC_DRAW
    );
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
  } else {
    console.warn("WebGL not supported, skipping background animation");
  }
});
