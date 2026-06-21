/* Digicars — Concierge match. Enhances .concierge blocks, launchers, and the
 * inline catalogue variant. Stable integration seam: action=digicars_concierge_match.
 * Vanilla JS, no dependencies. Degrades gracefully; respects reduced motion. */
(function () {
  'use strict';

  var data = (window.digicarsData && typeof window.digicarsData === 'object') ? window.digicarsData : {};
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function $(sel, ctx) { return (ctx || document).querySelector(sel); }
  function $all(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }

  function toast(message, tone) {
    if (typeof window.digicarsToast === 'function') window.digicarsToast(message, tone);
  }

  var FOCUSABLE = 'a[href], button:not([disabled]), textarea, input:not([disabled]), select, [tabindex]:not([tabindex="-1"])';
  function trapFocus(container, e) {
    var nodes = $all(FOCUSABLE, container).filter(function (n) { return n.offsetParent !== null; });
    if (!nodes.length) return;
    var first = nodes[0];
    var last = nodes[nodes.length - 1];
    if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
    else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
  }

  /* ----------------------------------------------------- core enhancement */
  function initConcierge(root) {
    if (!root || root.dataset.conciergeReady === '1') return;
    root.dataset.conciergeReady = '1';

    var input = $('.concierge__input', root);
    var count = $('.concierge__count', root);
    var stage = $('.concierge__stage', root);
    var chips = $all('[data-concierge-chip]', root);

    var active = {}; // slug -> true
    chips.forEach(function (chip) {
      if (chip.getAttribute('aria-pressed') === 'true' || chip.classList.contains('is-active')) {
        active[chip.getAttribute('data-concierge-chip')] = true;
      }
    });

    var controller = null;
    var debounceTimer = null;

    function activeSlugs() {
      return Object.keys(active);
    }

    function deriveBudget() {
      // Look for an explicit budget field, otherwise parse a number out of the free text.
      var budgetEl = $('[data-concierge-budget]', root);
      if (budgetEl && budgetEl.value) {
        var v = parseInt(String(budgetEl.value).replace(/[^\d]/g, ''), 10);
        if (!isNaN(v)) return v;
      }
      return '';
    }

    function setLoading(on) {
      root.classList.toggle('is-loading', !!on);
    }

    function fadeIn(html) {
      if (!stage) return;
      if (reduceMotion) {
        stage.innerHTML = html;
        stage.style.opacity = '';
        return;
      }
      stage.style.transition = 'opacity 180ms ease';
      stage.style.opacity = '0';
      window.setTimeout(function () {
        stage.innerHTML = html;
        // next frame so the browser registers opacity:0 before transitioning up
        window.requestAnimationFrame(function () { stage.style.opacity = '1'; });
      }, 180);
    }

    function request() {
      if (!data.ajaxUrl) return;

      if (controller) controller.abort();
      controller = ('AbortController' in window) ? new AbortController() : null;

      var params = new URLSearchParams();
      params.set('action', 'digicars_concierge_match');
      params.set('nonce', data.nonce || '');
      activeSlugs().forEach(function (slug) { params.append('chips[]', slug); });
      params.set('text', input ? input.value : '');
      var budget = deriveBudget();
      if (budget !== '') params.set('budget_monthly', String(budget));

      setLoading(true);

      fetch(data.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString(),
        signal: controller ? controller.signal : undefined
      }).then(function (res) {
        return res.json();
      }).then(function (json) {
        if (!json || !json.success || !json.data) {
          toast('Concierge could not load results. Please try again.', 'error');
          return;
        }
        var d = json.data;
        if (count) {
          var n = typeof d.count === 'number' ? d.count : parseInt(d.count, 10) || 0;
          count.textContent = n + (n === 1 ? ' match' : ' matches');
        }
        if (stage && typeof d.cards_html === 'string') fadeIn(d.cards_html);
      }).catch(function (err) {
        if (err && err.name === 'AbortError') return; // superseded request
        toast('Concierge could not load results. Please try again.', 'error');
      }).then(function () {
        setLoading(false);
      });
    }

    function schedule() {
      if (debounceTimer) window.clearTimeout(debounceTimer);
      debounceTimer = window.setTimeout(request, 300);
    }

    chips.forEach(function (chip) {
      chip.addEventListener('click', function () {
        var slug = chip.getAttribute('data-concierge-chip');
        var on = chip.getAttribute('aria-pressed') === 'true' || chip.classList.contains('is-active');
        on = !on;
        chip.setAttribute('aria-pressed', on ? 'true' : 'false');
        chip.classList.toggle('is-active', on);
        if (on) active[slug] = true; else delete active[slug];
        request(); // chip toggles are deliberate — fire immediately
      });
    });

    if (input) {
      input.addEventListener('input', schedule);
    }
    var budgetEl = $('[data-concierge-budget]', root);
    if (budgetEl) budgetEl.addEventListener('input', schedule);

    return { request: request, root: root };
  }

  /* ------------------------------------------------- concierge launcher modal */
  function buildMinimalConcierge() {
    var block = document.createElement('div');
    block.className = 'concierge';

    var label = document.createElement('label');
    label.className = 'concierge__field';
    var span = document.createElement('span');
    span.className = 'concierge__label';
    span.textContent = 'What are you looking for?';
    var input = document.createElement('input');
    input.type = 'text';
    input.className = 'concierge__input';
    input.placeholder = 'e.g. family SUV under R6 000 a month';
    label.appendChild(span);
    label.appendChild(input);

    var count = document.createElement('p');
    count.className = 'concierge__count';
    count.textContent = '0 matches';

    var stage = document.createElement('div');
    stage.className = 'concierge__stage';

    block.appendChild(label);
    block.appendChild(count);
    block.appendChild(stage);
    return block;
  }

  function initLaunchers() {
    var launchers = $all('[data-concierge-open]');
    if (!launchers.length) return;

    var modal = null;
    var refocus = null;

    function buildModal() {
      if (modal) return modal;
      var backdrop = document.createElement('div');
      backdrop.className = 'concierge-modal';
      backdrop.hidden = true;

      var dialog = document.createElement('div');
      dialog.className = 'concierge-modal__dialog';
      dialog.setAttribute('role', 'dialog');
      dialog.setAttribute('aria-modal', 'true');
      dialog.setAttribute('aria-label', 'Concierge');

      var closeBtn = document.createElement('button');
      closeBtn.type = 'button';
      closeBtn.className = 'concierge-modal__close';
      closeBtn.setAttribute('aria-label', 'Close');
      closeBtn.innerHTML = '&times;';

      var body = document.createElement('div');
      body.className = 'concierge-modal__body';

      // Clone an existing block if one is on the page, else build a minimal one.
      var source = $('.concierge');
      var clone;
      if (source) {
        clone = source.cloneNode(true);
        clone.removeAttribute('data-concierge-ready');
        $all('[data-concierge-ready]', clone).forEach(function (n) { n.removeAttribute('data-concierge-ready'); });
      } else {
        clone = buildMinimalConcierge();
      }
      body.appendChild(clone);

      dialog.appendChild(closeBtn);
      dialog.appendChild(body);
      backdrop.appendChild(dialog);
      document.body.appendChild(backdrop);

      initConcierge(clone);

      function close() {
        backdrop.hidden = true;
        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';
        document.removeEventListener('keydown', onKey);
        if (refocus) { refocus.focus(); refocus = null; }
      }
      function onKey(e) {
        if (e.key === 'Escape') close();
        else if (e.key === 'Tab') trapFocus(dialog, e);
      }

      closeBtn.addEventListener('click', close);
      backdrop.addEventListener('click', function (e) { if (e.target === backdrop) close(); });

      modal = {
        open: function (opener) {
          refocus = opener || null;
          backdrop.hidden = false;
          document.documentElement.style.overflow = 'hidden';
          document.body.style.overflow = 'hidden';
          document.addEventListener('keydown', onKey);
          var f = $(FOCUSABLE, dialog);
          if (f) f.focus();
        }
      };
      return modal;
    }

    launchers.forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        buildModal().open(btn);
      });
    });
  }

  /* ---------------------------------------------- inline catalogue variant */
  function initInline() {
    $all('[data-concierge-inline]').forEach(function (host) {
      if (host.dataset.conciergeReady === '1') return;

      var block = document.createElement('div');
      block.className = 'concierge';

      var label = document.createElement('label');
      label.className = 'concierge__field';
      var span = document.createElement('span');
      span.className = 'concierge__label';
      span.textContent = 'Describe your ideal car';
      var input = document.createElement('input');
      input.type = 'text';
      input.className = 'concierge__input';
      input.placeholder = 'e.g. low-km hatchback, automatic';
      label.appendChild(span);
      label.appendChild(input);

      var count = document.createElement('p');
      count.className = 'concierge__count';
      count.textContent = '0 matches';

      var stage = document.createElement('div');
      stage.className = 'concierge__stage';

      block.appendChild(label);
      block.appendChild(count);
      block.appendChild(stage);

      // Replace the static fallback content (kept until JS hydrates).
      host.innerHTML = '';
      host.appendChild(block);
      initConcierge(block);
    });
  }

  /* ------------------------------------------------------------------ boot */
  function init() {
    $all('.concierge').forEach(initConcierge);
    initLaunchers();
    initInline();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
