/* ==========================================================================
   Digicars — Hero neural-network canvas
   Particle system with connecting lines rendered in brand colors.
   Signal (orange) + volt (blue) nodes activate on Concierge focus.
   ========================================================================== */

(function () {
  'use strict';

  var canvas = document.getElementById('hero-neural');
  if (!canvas) return;

  var ctx    = canvas.getContext('2d');
  var REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* Tunables ---------------------------------------------------------------- */
  var NODE_COUNT   = 70;
  var CONNECT_DIST = 145;
  var BASE_SPEED   = 0.26;

  /* Brand palette — rgba prefix strings ------------------------------------ */
  var CLR = {
    paper:  'rgba(246,246,244,',
    signal: 'rgba(244,86,29,',
    volt:   'rgba(43,111,240,',
  };

  var W = 0, H = 0, nodes = [], raf = 0, active = false;

  /* -------------------------------------------------------------------------
   * Canvas sizing — match parent hero dimensions.
   * ---------------------------------------------------------------------- */
  function resize() {
    var p = canvas.parentElement;
    W = canvas.width  = p.offsetWidth  || window.innerWidth;
    H = canvas.height = p.offsetHeight || window.innerHeight;
  }

  /* -------------------------------------------------------------------------
   * Node factory.
   * ---------------------------------------------------------------------- */
  function makeNode(type) {
    var angle = Math.random() * Math.PI * 2;
    var spd   = BASE_SPEED * (0.4 + Math.random() * 0.9);
    return {
      x:     Math.random() * W,
      y:     Math.random() * H,
      vx:    Math.cos(angle) * spd,
      vy:    Math.sin(angle) * spd,
      r:     type === 'base' ? 1.1 + Math.random() * 0.9 : 1.9 + Math.random() * 1.4,
      phase: Math.random() * Math.PI * 2,
      type:  type,
    };
  }

  function buildNodes() {
    nodes = [];
    var i;
    for (i = 0; i < 10; i++)                nodes.push(makeNode('signal'));
    for (i = 0; i < 6;  i++)                nodes.push(makeNode('volt'));
    for (i = 0; i < NODE_COUNT - 16; i++)   nodes.push(makeNode('base'));
  }

  /* -------------------------------------------------------------------------
   * Main draw loop.
   * ---------------------------------------------------------------------- */
  function draw(ts) {
    ctx.clearRect(0, 0, W, H);

    var t = ts * 0.001;
    var n, i, j, a, b, dx, dy, d2, strength, color, alpha;

    /* Move nodes */
    if (!REDUCED) {
      for (i = 0; i < nodes.length; i++) {
        n = nodes[i];
        var boost = (active && n.type !== 'base') ? 1.9 : 1;
        n.x += n.vx * boost;
        n.y += n.vy * boost;
        if (n.x < 0) { n.x = 0; n.vx = Math.abs(n.vx); }
        if (n.x > W) { n.x = W; n.vx = -Math.abs(n.vx); }
        if (n.y < 0) { n.y = 0; n.vy = Math.abs(n.vy); }
        if (n.y > H) { n.y = H; n.vy = -Math.abs(n.vy); }
      }
    }

    /* Draw connections */
    var cd2 = CONNECT_DIST * CONNECT_DIST;
    ctx.lineWidth = 0.75;
    for (i = 0; i < nodes.length; i++) {
      for (j = i + 1; j < nodes.length; j++) {
        a = nodes[i]; b = nodes[j];
        dx = a.x - b.x; dy = a.y - b.y;
        d2 = dx * dx + dy * dy;
        if (d2 > cd2) continue;

        strength = 1 - Math.sqrt(d2) / CONNECT_DIST;

        if (active && (a.type === 'signal' || b.type === 'signal')) {
          color = CLR.signal; alpha = strength * 0.32;
        } else if (active && (a.type === 'volt' || b.type === 'volt')) {
          color = CLR.volt;   alpha = strength * 0.24;
        } else {
          color = CLR.paper;  alpha = strength * 0.065;
        }

        ctx.beginPath();
        ctx.moveTo(a.x, a.y);
        ctx.lineTo(b.x, b.y);
        ctx.strokeStyle = color + alpha + ')';
        ctx.stroke();
      }
    }

    /* Draw nodes */
    for (i = 0; i < nodes.length; i++) {
      n = nodes[i];
      var pulse = REDUCED ? 1 : 1 + 0.22 * Math.sin(t * 1.7 + n.phase);
      var r = n.r * pulse;
      var alphaCore, alphaGlow;

      if (n.type === 'signal') {
        color = CLR.signal;
        alphaCore = active ? 0.85 : 0.42;
        alphaGlow = active ? 0.20 : 0.09;
      } else if (n.type === 'volt') {
        color = CLR.volt;
        alphaCore = active ? 0.72 : 0.34;
        alphaGlow = active ? 0.15 : 0.07;
      } else {
        color = CLR.paper;
        alphaCore = 0.11;
        alphaGlow = 0;
      }

      /* Soft glow halo for colored nodes */
      if (alphaGlow > 0) {
        var gr = ctx.createRadialGradient(n.x, n.y, 0, n.x, n.y, r * 7);
        gr.addColorStop(0, color + alphaGlow + ')');
        gr.addColorStop(1, color + '0)');
        ctx.beginPath();
        ctx.arc(n.x, n.y, r * 7, 0, Math.PI * 2);
        ctx.fillStyle = gr;
        ctx.fill();
      }

      /* Core dot */
      ctx.beginPath();
      ctx.arc(n.x, n.y, r, 0, Math.PI * 2);
      ctx.fillStyle = color + alphaCore + ')';
      ctx.fill();
    }

    raf = requestAnimationFrame(draw);
  }

  /* -------------------------------------------------------------------------
   * Boot and resize.
   * ---------------------------------------------------------------------- */
  function init() {
    resize();
    buildNodes();
    cancelAnimationFrame(raf);
    raf = requestAnimationFrame(draw);
  }

  /* Wait one tick so the hero has its final layout height */
  window.addEventListener('load', init);
  /* Immediate start too so it shows before images load */
  init();

  var resizeId;
  window.addEventListener('resize', function () {
    clearTimeout(resizeId);
    resizeId = setTimeout(init, 220);
  });

  /* -------------------------------------------------------------------------
   * Concierge interaction — signal nodes activate when user engages.
   * ---------------------------------------------------------------------- */
  document.addEventListener('focusin', function (e) {
    if (e.target && e.target.closest && e.target.closest('.home-hero__search-wrap')) {
      active = true;
    }
  });
  document.addEventListener('focusout', function (e) {
    if (e.target && e.target.closest && e.target.closest('.home-hero__search-wrap')) {
      active = false;
    }
  });
  document.addEventListener('click', function (e) {
    if (e.target && e.target.closest && e.target.closest('[data-concierge-open]')) {
      active = true;
    }
  });
  /* Custom event dispatched by concierge.js when the panel closes */
  document.addEventListener('digicars:concierge:close', function () {
    active = false;
  });
})();
