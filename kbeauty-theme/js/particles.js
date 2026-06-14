(function () {
  'use strict';

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }

  var stage = document.querySelector('[data-hero-stage]');
  if (!stage) { return; }

  var COUNT = 90;
  var REPEL_RADIUS = 80;
  var REPEL_FORCE = 1.6;

  var canvas = document.createElement('canvas');
  canvas.id = 'glow-particles';
  canvas.setAttribute('aria-hidden', 'true');
  canvas.setAttribute('role', 'presentation');
  stage.appendChild(canvas);

  var ctx = canvas.getContext('2d');
  var W, H;
  var mouseX = -999, mouseY = -999;
  var hasMouse = !window.matchMedia('(pointer: coarse)').matches;
  var particles = [];

  function resize() {
    W = canvas.width = stage.offsetWidth;
    H = canvas.height = stage.offsetHeight;
  }

  function rand(min, max) { return min + Math.random() * (max - min); }

  function initParticles() {
    particles = [];
    for (var i = 0; i < COUNT; i++) {
      particles.push({
        x: rand(0, W),
        y: rand(0, H),
        r: rand(1.5, 4),
        alpha: rand(0.14, 0.42),
        speed: rand(0.18, 0.55),
        vx: 0,
        vy: 0,
      });
    }
  }

  function draw() {
    ctx.clearRect(0, 0, W, H);

    particles.forEach(function (p) {
      if (hasMouse) {
        var rect = stage.getBoundingClientRect();
        var lx = mouseX - rect.left;
        var ly = mouseY - rect.top;
        var dx = p.x - lx;
        var dy = p.y - ly;
        var dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < REPEL_RADIUS && dist > 0) {
          var force = (REPEL_RADIUS - dist) / REPEL_RADIUS * REPEL_FORCE;
          p.vx += (dx / dist) * force;
          p.vy += (dy / dist) * force;
        }
      }

      p.vx *= 0.88;
      p.vy *= 0.88;
      p.y -= p.speed + p.vy;
      p.x += p.vx;

      if (p.y < -p.r * 2) { p.y = H + p.r; p.x = rand(0, W); }
      if (p.x < -p.r * 2) { p.x = W + p.r; }
      if (p.x > W + p.r * 2) { p.x = -p.r; }

      ctx.beginPath();
      ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
      ctx.fillStyle = 'rgba(255,255,255,' + p.alpha + ')';
      ctx.fill();
    });

    requestAnimationFrame(draw);
  }

  if (hasMouse) {
    window.addEventListener('mousemove', function (e) {
      mouseX = e.clientX;
      mouseY = e.clientY;
    }, { passive: true });
  }

  new ResizeObserver(function () {
    resize();
    initParticles();
  }).observe(stage);

  resize();
  initParticles();
  draw();
})();
