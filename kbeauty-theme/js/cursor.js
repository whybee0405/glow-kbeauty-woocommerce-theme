(function () {
  'use strict';

  if (!window.matchMedia('(pointer: fine)').matches) { return; }
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }

  var dot = document.createElement('div');
  dot.className = 'glow-cursor-dot';

  var ring = document.createElement('div');
  ring.className = 'glow-cursor-ring';

  document.body.appendChild(dot);
  document.body.appendChild(ring);
  document.body.classList.add('has-custom-cursor');

  var mouseX = window.innerWidth / 2;
  var mouseY = window.innerHeight / 2;
  var ringX = mouseX;
  var ringY = mouseY;
  var LERP = 0.12;

  var hoverTargets = 'a, button, [role="button"], label, input, select, textarea, .rail-step, .concern-tile';

  window.addEventListener('mousemove', function (e) {
    mouseX = e.clientX;
    mouseY = e.clientY;
  }, { passive: true });

  document.addEventListener('mouseover', function (e) {
    if (e.target && e.target.closest && e.target.closest(hoverTargets)) {
      dot.classList.add('is-hovering');
      ring.classList.add('is-hovering');
    }
  }, { passive: true });

  document.addEventListener('mouseout', function (e) {
    if (e.target && e.target.closest && e.target.closest(hoverTargets)) {
      dot.classList.remove('is-hovering');
      ring.classList.remove('is-hovering');
    }
  }, { passive: true });

  document.addEventListener('mouseleave', function () {
    dot.style.opacity = '0';
    ring.style.opacity = '0';
  });

  document.addEventListener('mouseenter', function () {
    dot.style.opacity = '';
    ring.style.opacity = '';
  });

  function tick() {
    dot.style.left = mouseX + 'px';
    dot.style.top = mouseY + 'px';

    ringX += (mouseX - ringX) * LERP;
    ringY += (mouseY - ringY) * LERP;
    ring.style.left = ringX + 'px';
    ring.style.top = ringY + 'px';

    requestAnimationFrame(tick);
  }

  tick();
})();
