/* Digicars — Affordability calculator. Mirrors PHP digicars_monthly_from EXACTLY.
 * Vanilla JS, no dependencies. Degrades gracefully. */
(function () {
  'use strict';

  function $(sel, ctx) { return (ctx || document).querySelector(sel); }
  function $all(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }

  // Must match the PHP formula exactly.
  function monthlyFrom(price, deposit, term, rate, balloonFraction) {
    if (deposit == null) deposit = 0;
    if (term == null) term = 72;
    if (rate == null) rate = 0.115;
    if (balloonFraction == null) balloonFraction = 0;

    var financed = Math.max(0, price - deposit);
    var residual = price * balloonFraction;
    var i = rate / 12;
    var n = Math.max(1, term);
    if (i <= 0) return Math.ceil((financed - residual) / n);
    var factor = Math.pow(1 + i, n);
    var monthly = (financed - residual / factor) * (i / (1 - 1 / factor));
    return Math.ceil(monthly);
  }

  function clamp(value, min, max) {
    return Math.min(max, Math.max(min, value));
  }

  function parseIntSafe(value) {
    var n = parseInt(String(value).replace(/[^\d-]/g, ''), 10);
    return isNaN(n) ? 0 : n;
  }

  // "R 7 990" — space thousands separators.
  function formatRand(value) {
    var n = Math.round(value);
    var sign = n < 0 ? '-' : '';
    n = Math.abs(n);
    return 'R ' + sign + String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
  }

  function initContainer(root) {
    var price = parseIntSafe(root.getAttribute('data-price'));
    if (!price) return;

    var depositInput = $('[data-aff-deposit]', root);
    var termInput = $('[data-aff-term]', root);
    var balloonInput = $('[data-aff-balloon]', root);
    var output = $('[data-aff-output]', root);
    if (!output) return;

    function recompute() {
      var deposit = depositInput ? clamp(parseIntSafe(depositInput.value), 0, price) : 0;
      var term = termInput ? parseIntSafe(termInput.value) || 72 : 72;
      var balloonPercent = balloonInput ? clamp(parseIntSafe(balloonInput.value), 0, 40) : 0;

      // Reflect clamped values back into the inputs so the UI stays honest.
      if (depositInput && String(deposit) !== String(parseIntSafe(depositInput.value)) && depositInput.value !== '') {
        depositInput.value = deposit;
      }
      if (balloonInput && String(balloonPercent) !== String(parseIntSafe(balloonInput.value)) && balloonInput.value !== '') {
        balloonInput.value = balloonPercent;
      }

      var monthly = monthlyFrom(price, deposit, term, 0.115, balloonPercent / 100);
      output.textContent = formatRand(monthly);
    }

    [depositInput, termInput, balloonInput].forEach(function (el) {
      if (!el) return;
      el.addEventListener('input', recompute);
      el.addEventListener('change', recompute);
    });

    recompute();
  }

  function init() {
    $all('[data-affordability]').forEach(initContainer);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
