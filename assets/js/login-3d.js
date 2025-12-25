document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("model-container");
  if (!container) return;

  // SCENE SETUP
  const scene = new THREE.Scene();

  // CAMERA
  const camera = new THREE.PerspectiveCamera(
    75,
    container.clientWidth / container.clientHeight,
    0.1,
    1000
  );
  camera.position.z = 15;

  // RENDERER
  const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
  renderer.setSize(container.clientWidth, container.clientHeight);
  renderer.setPixelRatio(window.devicePixelRatio);
  container.appendChild(renderer.domElement);

  // VORTEX GEOMETRY
  const particleCount = 1500;
  const geometry = new THREE.BufferGeometry();
  const positions = [];

  // Create simpler particle array
  for (let i = 0; i < particleCount; i++) {
    const angle = Math.random() * Math.PI * 2;
    const radius = 3 + Math.random() * 8;
    const z = (Math.random() - 0.5) * 60;

    const x = Math.cos(angle) * radius;
    const y = Math.sin(angle) * radius;

    positions.push(x, y, z);
  }

  geometry.setAttribute(
    "position",
    new THREE.Float32BufferAttribute(positions, 3)
  );

  // MATERIAL - Use standard PointsMaterial
  const material = new THREE.PointsMaterial({
    color: 0x00d26a,
    size: 0.15,
    transparent: true,
    opacity: 0.8,
  });

  const vortex = new THREE.Points(geometry, material);
  scene.add(vortex);

  // LIGHTS
  const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
  scene.add(ambientLight);

  const pointLight = new THREE.PointLight(0x00d26a, 2, 50);
  pointLight.position.set(0, 0, 5);
  scene.add(pointLight);

  // ANIMATION LOOP
  function animate() {
    requestAnimationFrame(animate);

    // Rotate the whole tunnel
    vortex.rotation.z += 0.002;

    // Move particles "forward" to simulate moving through tunnel
    const positions = vortex.geometry.attributes.position.array;
    for (let i = 2; i < positions.length; i += 3) {
      positions[i] += 0.1; // Move towards camera

      // Reset if passed camera
      if (positions[i] > 20) {
        positions[i] = -40;
      }
    }
    vortex.geometry.attributes.position.needsUpdate = true;

    // Interaction parallax
    camera.rotation.x += (mouseY * 0.5 - camera.rotation.x) * 0.05;
    camera.rotation.y += (mouseX * 0.5 - camera.rotation.y) * 0.05;

    renderer.render(scene, camera);
  }

  // INTERACTION (Mouse Parallax)
  let mouseX = 0;
  let mouseY = 0;

  document.addEventListener("mousemove", (e) => {
    const windowHalfX = window.innerWidth / 2;
    const windowHalfY = window.innerHeight / 2;
    mouseX = (e.clientX - windowHalfX) / 1000;
    mouseY = (e.clientY - windowHalfY) / 1000;
  });

  // START
  animate();

  // RESIZE HANDLER
  window.addEventListener("resize", () => {
    const width = container.clientWidth;
    const height = container.clientHeight;
    renderer.setSize(width, height);
    camera.aspect = width / height;
    camera.updateProjectionMatrix();
  });
});
