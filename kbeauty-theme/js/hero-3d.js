import * as THREE from 'three';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';

(function () {
  'use strict';

  var REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (REDUCED) { return; }
  if (window.innerWidth < 768) { return; }
  if (!window.WebGLRenderingContext) { return; }

  var stage = document.querySelector('[data-hero-stage]');
  if (!stage) { return; }

  var canvas = document.createElement('canvas');
  canvas.id = 'glow-hero-3d';
  canvas.setAttribute('aria-hidden', 'true');
  canvas.setAttribute('role', 'presentation');
  stage.insertBefore(canvas, stage.firstChild);
  stage.classList.add('is-3d');

  var W = stage.clientWidth;
  var H = stage.clientHeight;

  var renderer = new THREE.WebGLRenderer({ canvas: canvas, antialias: true, alpha: true });
  renderer.setSize(W, H);
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
  renderer.outputColorSpace = THREE.SRGBColorSpace;
  renderer.toneMapping = THREE.ACESFilmicToneMapping;
  renderer.toneMappingExposure = 1.2;

  var scene = new THREE.Scene();
  var camera = new THREE.PerspectiveCamera(40, W / H, 0.1, 100);
  camera.position.set(0, 0, 4.5);

  var keyLight = new THREE.PointLight(0xFFF5E4, 3.5, 20);
  keyLight.position.set(3, 3, 4);
  scene.add(keyLight);

  var fillLight = new THREE.PointLight(0xC8F0E8, 2.2, 20);
  fillLight.position.set(-3, 1, 2);
  scene.add(fillLight);

  var rimLight = new THREE.PointLight(0xFFE4E1, 2.8, 20);
  rimLight.position.set(0, -3, -2);
  scene.add(rimLight);

  scene.add(new THREE.AmbientLight(0xffffff, 0.4));

  // Bottle body — lathe profile gives proper shoulder + neck taper
  // Points: [radius, height], swept 360° around Y axis
  var profilePoints = [
    new THREE.Vector2(0.30, -1.12), // base inner edge
    new THREE.Vector2(0.38, -1.06), // base rim
    new THREE.Vector2(0.40, -0.82), // lower body
    new THREE.Vector2(0.425,-0.22), // body widest
    new THREE.Vector2(0.415, 0.22), // upper body
    new THREE.Vector2(0.360, 0.56), // shoulder
    new THREE.Vector2(0.195, 0.79), // neck base
    new THREE.Vector2(0.152, 0.96), // neck
    new THREE.Vector2(0.142, 1.11), // neck top
  ];

  var bodyGeo = new THREE.LatheGeometry(profilePoints, 80);

  // Pump collar — sits on top of neck
  var collarGeo = new THREE.CylinderGeometry(0.162, 0.152, 0.40, 48);

  // Pump actuator platform — wider disc the user presses
  var headGeo = new THREE.CylinderGeometry(0.205, 0.165, 0.07, 48);

  // Nozzle spout — horizontal cylinder from pump head edge
  var spoutGeo = new THREE.CylinderGeometry(0.026, 0.022, 0.22, 12);

  // Glass body material
  var glassMat = new THREE.MeshPhysicalMaterial({
    color: 0xd0ede5,
    transmission: 0.42,
    roughness: 0.06,
    metalness: 0.12,
    iridescence: 1.0,
    iridescenceIOR: 1.42,
    iridescenceThicknessRange: [100, 400],
    thickness: 0.5,
    envMapIntensity: 2.0,
    clearcoat: 0.85,
    clearcoatRoughness: 0.08,
  });

  // Cap material — dark forest green matte
  var capMat = new THREE.MeshPhysicalMaterial({
    color: 0x263820,
    roughness: 0.32,
    metalness: 0.08,
    clearcoat: 0.4,
    clearcoatRoughness: 0.2,
  });

  var bottle = new THREE.Mesh(bodyGeo, glassMat);

  // Collar: neck top (1.11) + half collar height (0.20) = 1.31
  var collar = new THREE.Mesh(collarGeo, capMat);
  collar.position.y = 1.31;

  // Head: collar top (1.51) + half head height (0.035) = 1.545
  var head = new THREE.Mesh(headGeo, capMat);
  head.position.y = 1.545;

  // Spout: horizontal, extending right from head edge
  // rotated 90° on Z so the cylinder axis is along X
  // center X = head radius (0.205) + half spout length (0.11) = 0.315
  var spout = new THREE.Mesh(spoutGeo, capMat);
  spout.rotation.z = Math.PI / 2;
  spout.position.set(0.315, 1.545, 0);

  var group = new THREE.Group();
  group.add(bottle);
  group.add(collar);
  group.add(head);
  group.add(spout);
  // Offset down slightly so bottle sits centered in frame
  group.position.y = -0.15;
  scene.add(group);

  var pmremGen = new THREE.PMREMGenerator(renderer);
  var envScene = new RoomEnvironment();
  scene.environment = pmremGen.fromScene(envScene, 0.04).texture;
  pmremGen.dispose();
  envScene.dispose();

  // Scroll tracking — smooth lerp toward scroll target
  var scrollTarget = 0;
  var scrollCurrent = 0;
  window.addEventListener('scroll', function () {
    scrollTarget = Math.min(1, window.scrollY / Math.max(1, stage.offsetHeight));
  }, { passive: true });

  var targetScale = 1;
  var currentScale = 1;
  var idleRotY = 0;
  var animId = null;

  function animate() {
    animId = requestAnimationFrame(animate);

    // Smooth scroll progress (lerp at 6% per frame ≈ ~250ms settle)
    scrollCurrent += (scrollTarget - scrollCurrent) * 0.06;

    // Gentle idle rotation + scroll adds up to 270° additional rotation
    idleRotY += 0.004;
    group.rotation.y = idleRotY + scrollCurrent * Math.PI * 1.5;

    // Subtle parallax: bottle drifts upward as hero scrolls away
    group.position.y = -0.15 + scrollCurrent * 0.32;

    // Hover scale (step rail interaction)
    currentScale += (targetScale - currentScale) * 0.08;
    group.scale.setScalar(currentScale);

    renderer.render(scene, camera);
  }
  animate();

  document.addEventListener('mouseover', function (e) {
    var step = e.target && e.target.closest && e.target.closest('.rail-step[data-step]');
    targetScale = step ? 1.08 : 1;
  }, { passive: true });

  document.addEventListener('mouseleave', function () {
    targetScale = 1;
  }, { passive: true, capture: true });

  var resizeObs = new ResizeObserver(function () {
    W = stage.clientWidth;
    H = stage.clientHeight;
    camera.aspect = W / H;
    camera.updateProjectionMatrix();
    renderer.setSize(W, H);
  });
  resizeObs.observe(stage);

  document.addEventListener('visibilitychange', function () {
    if (document.hidden) {
      cancelAnimationFrame(animId);
    } else {
      animate();
    }
  });
})();
