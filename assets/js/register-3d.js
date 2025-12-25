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
  camera.position.z = 8;
  camera.position.y = 0;

  // RENDERER
  const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
  renderer.setSize(container.clientWidth, container.clientHeight);
  renderer.setPixelRatio(window.devicePixelRatio);
  container.appendChild(renderer.domElement);

  // GROUP for the DNA
  const dnaGroup = new THREE.Group();
  scene.add(dnaGroup);

  // DNA PARAMETERS
  const particleCount = 60; // Number of base pairs
  const radius = 2;
  const height = 12;
  const turns = 2.5; // How many twists

  const particlesGeo = new THREE.BufferGeometry();
  const particlePositions = [];

  // STRANDS (Lines connecting the helix) - Optional, but particles look better for tech feel.
  // We will draw lines connecting the PAIRS (rungs of the ladder)

  const lineMat = new THREE.LineBasicMaterial({
    color: 0x00d26a,
    transparent: true,
    opacity: 0.15,
  });

  for (let i = 0; i < particleCount; i++) {
    const t = i / (particleCount - 1);
    const angle = t * Math.PI * 2 * turns;
    const y = (t - 0.5) * height;

    // Strand 1
    const x1 = Math.cos(angle) * radius;
    const z1 = Math.sin(angle) * radius;

    // Strand 2 (Offset by PI)
    const x2 = Math.cos(angle + Math.PI) * radius;
    const z2 = Math.sin(angle + Math.PI) * radius;

    // Add particle positions
    particlePositions.push(x1, y, z1);
    particlePositions.push(x2, y, z2);

    // Create a "rungs" line connecting the two strands
    const points = [];
    points.push(new THREE.Vector3(x1, y, z1));
    points.push(new THREE.Vector3(x2, y, z2));
    const lineGeo = new THREE.BufferGeometry().setFromPoints(points);
    const line = new THREE.Line(lineGeo, lineMat);
    dnaGroup.add(line);
  }

  particlesGeo.setAttribute(
    "position",
    new THREE.Float32BufferAttribute(particlePositions, 3)
  );

  // DNA PARTICLES MATERIAL
  const particlesMat = new THREE.PointsMaterial({
    color: 0x00ff88,
    size: 0.15,
    transparent: true,
    opacity: 0.8,
  });

  const particleSystem = new THREE.Points(particlesGeo, particlesMat);
  dnaGroup.add(particleSystem);

  // FLOATING PARTICLES (Background ambience)
  const floatGeo = new THREE.BufferGeometry();
  const floatCount = 60;
  const floatPos = new Float32Array(floatCount * 3);
  for (let i = 0; i < floatCount * 3; i++) {
    floatPos[i] = (Math.random() - 0.5) * 20;
  }
  floatGeo.setAttribute("position", new THREE.BufferAttribute(floatPos, 3));
  const floatMat = new THREE.PointsMaterial({
    color: 0xffffff,
    size: 0.05,
    transparent: true,
    opacity: 0.2,
  });
  const floatingParticles = new THREE.Points(floatGeo, floatMat);
  scene.add(floatingParticles);

  // LIGHTS
  const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
  scene.add(ambientLight);

  const pointLight = new THREE.PointLight(0x00d26a, 2);
  pointLight.position.set(5, 5, 5);
  scene.add(pointLight);

  // ANIMATION LOOP
  function animate() {
    requestAnimationFrame(animate);

    // Rotate DNA
    dnaGroup.rotation.y += 0.005;
    dnaGroup.rotation.z = Math.sin(Date.now() * 0.0005) * 0.1; // Gentle tilt

    // Float Background
    floatingParticles.rotation.y -= 0.0005;

    renderer.render(scene, camera);
  }
  animate();

  // INTERACTION (Mouse Parallax)
  let mouseX = 0;
  let mouseY = 0;

  document.addEventListener("mousemove", (e) => {
    const windowHalfX = window.innerWidth / 2;
    const windowHalfY = window.innerHeight / 2;
    mouseX = (e.clientX - windowHalfX) / 100;
    mouseY = (e.clientY - windowHalfY) / 100;
  });

  function updateParallax() {
    camera.position.x += (mouseX - camera.position.x) * 0.05;
    camera.position.y += (-mouseY - camera.position.y) * 0.05;
    camera.lookAt(scene.position);
    requestAnimationFrame(updateParallax);
  }
  updateParallax();

  // RESIZE HANDLER
  window.addEventListener("resize", () => {
    const width = container.clientWidth;
    const height = container.clientHeight;
    renderer.setSize(width, height);
    camera.aspect = width / height;
    camera.updateProjectionMatrix();
  });
});
