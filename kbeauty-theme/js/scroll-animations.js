(function () {
  'use strict';

  var REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var IS_EDITOR = typeof window.elementorFrontend !== 'undefined' &&
    typeof window.elementorFrontend.isEditMode === 'function' &&
    window.elementorFrontend.isEditMode();

  // Pre-mark all [data-reveal] so main.js IntersectionObserver skips them.
  document.querySelectorAll('[data-reveal]').forEach(function (el) {
    el.classList.add('is-visible');
  });

  if (REDUCED || IS_EDITOR) { return; }

  function initGSAP() {
    if (typeof window.gsap === 'undefined' || typeof window.ScrollTrigger === 'undefined') {
      return requestAnimationFrame(initGSAP);
    }

    gsap.registerPlugin(ScrollTrigger);

    document.querySelectorAll('[data-reveal]').forEach(function (el) {
      gsap.from(el, {
        y: 28,
        opacity: 0,
        duration: 0.55,
        ease: 'power3.out',
        clearProps: 'transform,opacity',
        scrollTrigger: {
          trigger: el,
          start: 'top 85%',
          once: true,
        },
      });
    });

    document.querySelectorAll('.t-hero, .t-1').forEach(function (el) {
      gsap.from(el, {
        clipPath: 'inset(0 100% 0 0)',
        duration: 0.7,
        ease: 'power4.out',
        clearProps: 'clipPath',
        scrollTrigger: {
          trigger: el,
          start: 'top 88%',
          once: true,
        },
      });
    });

    document.querySelectorAll('.products.grid-4, .products.grid-3').forEach(function (grid) {
      var cards = grid.querySelectorAll('li.product, .product-card');
      if (!cards.length) { return; }

      gsap.from(cards, {
        y: 20,
        opacity: 0,
        duration: 0.45,
        ease: 'power3.out',
        stagger: 0.06,
        clearProps: 'transform,opacity',
        scrollTrigger: {
          trigger: grid,
          start: 'top 88%',
          once: true,
        },
      });
    });

    if (typeof window.elementorFrontend !== 'undefined') {
      window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function () {
        ScrollTrigger.refresh();
      });
    }
  }

  requestAnimationFrame(initGSAP);
})();
