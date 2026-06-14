import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';

(function () {
  'use strict';

  var REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  var wrap = document.getElementById('pdp-3d-wrap');
  var canvasEl = document.getElementById('glow-pdp-3d');
  var toggleBtn = document.getElementById('glow-view-3d-btn');
  var hasImage = wrap && wrap.getAttribute('data-has-image') === 'true';

  if (!wrap || !canvasEl) { return; }
  if (!window.WebGLRenderingContext) { return; }

  var W = wrap.clientWidth;
  var H = wrap.clientHeight || W;

  var renderer = new THREE.WebGLRenderer({ canvas: canvasEl, antialias: true, alpha: true });
  renderer.setSize(W, H);
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  renderer.outputColorSpace = THREE.SRGBColorSpace;
  renderer.toneMapping = THREE.ACESFilmicToneMapping;
  renderer.toneMappingExposure = 1.1;

  var scene = new THREE.Scene();
  scene.background = new THREE.Color(0xf3f2ed);

  var camera = new THREE.PerspectiveCamera(45, W / H, 0.1, 100);
  camera.position.set(0, 0, 4.5);

  var key = new THREE.PointLight(0xFFF5E4, 3.5, 20);
  key.position.set(3, 3, 4);
  scene.add(key);

  var fill = new THREE.PointLight(0xC8F0E8, 2.0, 20);
  fill.position.set(-3, 1, 2);
  scene.add(fill);

  var rim = new THREE.PointLight(0xFFE4E1, 2.8, 20);
  rim.position.set(0, -3, -2);
  scene.add(rim);

  scene.add(new THREE.AmbientLight(0xffffff, 0.5));

  var geo = new THREE.CylinderGeometry(0.42, 0.38, 1.7, 64);
  var capGeo = new THREE.CylinderGeometry(0.12, 0.12, 0.55, 32);

  var mat = new THREE.MeshPhysicalMaterial({
    color: 0xd0ede5,
    transmission: 0.4,
    roughness: 0.08,
    metalness: 0.18,
    iridescence: 1.0,
    iridescenceIOR: 1.42,
    iridescenceThicknessRange: [100, 400],
    thickness: 0.5,
    clearcoat: 0.6,
    clearcoatRoughness: 0.1,
    envMapIntensity: 1.8,
  });

  var bottle = new THREE.Mesh(geo, mat);
  var cap = new THREE.Mesh(capGeo, mat);
  cap.position.y = 1.125;

  var group = new THREE.Group();
  group.add(bottle);
  group.add(cap);
  scene.add(group);

  var pmremGen = new THREE.PMREMGenerator(renderer);
  var envScene = new RoomEnvironment();
  scene.environment = pmremGen.fromScene(envScene, 0.04).texture;
  pmremGen.dispose();
  envScene.dispose();

  var controls = new OrbitControls(camera, renderer.domElement);
  controls.enableZoom = false;
  controls.enablePan = false;
  controls.enableDamping = true;
  controls.dampingFactor = 0.06;
  controls.rotateSpeed = 0.7;
  controls.autoRotate = !REDUCED;
  controls.autoRotateSpeed = 1.2;

  var defaultQ = group.quaternion.clone();

  function resetRotation() {
    group.quaternion.slerp(defaultQ, 0.12);
  }

  canvasEl.addEventListener('dblclick', resetRotation, { passive: true });
  var lastTap = 0;
  canvasEl.addEventListener('touchend', function () {
    var now = Date.now();
    if (now - lastTap < 300) { resetRotation(); }
    lastTap = now;
  }, { passive: true });

  var animId = null;
  function animate() {
    animId = requestAnimationFrame(animate);
    controls.update();
    renderer.render(scene, camera);
  }
  animate();

  if (hasImage && toggleBtn) {
    var pdpMain = wrap.querySelector('[data-pdp-main]');
    var showing3d = false;

    toggleBtn.addEventListener('click', function () {
      showing3d = !showing3d;

      if (showing3d) {
        if (pdpMain) { pdpMain.hidden = true; }
        canvasEl.hidden = false;
        toggleBtn.textContent = 'View photo';
        controls.autoRotate = !REDUCED;
      } else {
        canvasEl.hidden = true;
        if (pdpMain) { pdpMain.hidden = false; }
        toggleBtn.textContent = 'View 3D';
        controls.autoRotate = false;
      }
    });
  }

  new ResizeObserver(function () {
    W = wrap.clientWidth;
    H = wrap.clientHeight || W;
    camera.aspect = W / H;
    camera.updateProjectionMatrix();
    renderer.setSize(W, H);
  }).observe(wrap);

  document.addEventListener('visibilitychange', function () {
    if (document.hidden) {
      cancelAnimationFrame(animId);
    } else {
      animate();
    }
  });
})();
