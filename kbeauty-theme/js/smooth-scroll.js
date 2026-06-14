(function () {
  'use strict';

  if (window.matchMedia('(pointer: coarse)').matches) { return; }
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }

  function init() {
    if (typeof window.Lenis === 'undefined' || typeof window.gsap === 'undefined') {
      return requestAnimationFrame(init);
    }

    var lenis = new Lenis({
      duration: 1.2,
      easing: function (t) { return Math.min(1, 1.001 - Math.pow(2, -10 * t)); },
      orientation: 'vertical',
      gestureOrientation: 'vertical',
      smoothWheel: true,
      touchMultiplier: 2,
    });

    gsap.ticker.add(function (time) {
      lenis.raf(time * 1000);
    });

    gsap.ticker.lagSmoothing(0);

    window.glowLenis = lenis;
  }

  requestAnimationFrame(init);
})();
