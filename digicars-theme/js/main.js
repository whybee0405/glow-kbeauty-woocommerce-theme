/* Digicars — global interactions, filter drawer, compare, enquiry modal, toast.
 * Vanilla JS, no dependencies. Degrades gracefully; respects reduced motion. */
(function () {
  'use strict';

  var data = (window.digicarsData && typeof window.digicarsData === 'object') ? window.digicarsData : {};
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var COMPARE_KEY = 'digicars_compare';

  /* ---------------------------------------------------------------- utils */
  function $(sel, ctx) { return (ctx || document).querySelector(sel); }
  function $all(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }

  var scrollLocks = 0;
  function lockScroll() {
    scrollLocks++;
    document.documentElement.style.overflow = 'hidden';
    document.body.style.overflow = 'hidden';
  }
  function unlockScroll() {
    scrollLocks = Math.max(0, scrollLocks - 1);
    if (scrollLocks === 0) {
      document.documentElement.style.overflow = '';
      document.body.style.overflow = '';
    }
  }

  var FOCUSABLE = 'a[href], button:not([disabled]), textarea, input:not([disabled]), select, [tabindex]:not([tabindex="-1"])';
  function trapFocus(container, e) {
    var nodes = $all(FOCUSABLE, container).filter(function (n) { return n.offsetParent !== null || n === document.activeElement; });
    if (!nodes.length) return;
    var first = nodes[0];
    var last = nodes[nodes.length - 1];
    if (e.shiftKey && document.activeElement === first) {
      e.preventDefault();
      last.focus();
    } else if (!e.shiftKey && document.activeElement === last) {
      e.preventDefault();
      first.focus();
    }
  }

  /* ----------------------------------------------------------------- toast */
  function ensureToastRegion() {
    var region = $('.toast-region');
    if (!region) {
      region = document.createElement('div');
      region.className = 'toast-region';
      region.setAttribute('aria-live', 'polite');
      region.setAttribute('aria-atomic', 'true');
      document.body.appendChild(region);
    }
    return region;
  }

  function digicarsToast(message, tone) {
    var region = ensureToastRegion();
    var toast = document.createElement('div');
    toast.className = 'toast' + (tone ? ' toast--' + tone : '');
    toast.setAttribute('role', 'status');

    var text = document.createElement('span');
    text.className = 'toast__message';
    text.textContent = message == null ? '' : String(message);
    toast.appendChild(text);

    var close = document.createElement('button');
    close.type = 'button';
    close.className = 'toast__close';
    close.setAttribute('aria-label', 'Dismiss');
    close.innerHTML = '&times;';
    toast.appendChild(close);

    region.appendChild(toast);

    var dismissed = false;
    function dismiss() {
      if (dismissed) return;
      dismissed = true;
      toast.classList.remove('is-visible');
      window.setTimeout(function () {
        if (toast.parentNode) toast.parentNode.removeChild(toast);
      }, reduceMotion ? 0 : 200);
    }

    close.addEventListener('click', dismiss);
    // allow CSS transition to pick up the class
    window.requestAnimationFrame(function () { toast.classList.add('is-visible'); });
    window.setTimeout(dismiss, 4000);
    return toast;
  }
  window.digicarsToast = digicarsToast;

  /* -------------------------------------------------------- sticky header */
  function initHeader() {
    var header = $('[data-site-header]');
    if (!header) return;
    var ticking = false;
    function update() {
      header.classList.toggle('is-scrolled', window.scrollY > 8);
      ticking = false;
    }
    window.addEventListener('scroll', function () {
      if (!ticking) {
        ticking = true;
        window.requestAnimationFrame(update);
      }
    }, { passive: true });
    update();
  }

  /* ----------------------------------------------------------- mobile nav */
  function initMobileNav() {
    var toggle = $('[data-nav-toggle]');
    var menu = $('[data-mobile-menu]');
    if (!toggle || !menu) return;
    var closeBtns = $all('[data-nav-close]');

    function open() {
      menu.classList.add('is-open');
      toggle.setAttribute('aria-expanded', 'true');
      lockScroll();
      var firstClose = closeBtns[0] || $(FOCUSABLE, menu);
      if (firstClose) firstClose.focus();
      document.addEventListener('keydown', onKey);
    }
    function close() {
      if (!menu.classList.contains('is-open')) return;
      menu.classList.remove('is-open');
      toggle.setAttribute('aria-expanded', 'false');
      unlockScroll();
      document.removeEventListener('keydown', onKey);
      toggle.focus();
    }
    function onKey(e) {
      if (e.key === 'Escape') close();
      else if (e.key === 'Tab') trapFocus(menu, e);
    }

    toggle.addEventListener('click', function () {
      if (menu.classList.contains('is-open')) close(); else open();
    });
    closeBtns.forEach(function (b) { b.addEventListener('click', close); });
  }

  /* -------------------------------------------------------- search toggle */
  function initSearch() {
    var toggle = $('[data-search-toggle]');
    var panel = $('[data-search-panel]');
    if (!toggle || !panel) return;

    function open() {
      panel.hidden = false;
      toggle.setAttribute('aria-expanded', 'true');
      var input = $('input', panel);
      if (input) input.focus();
      document.addEventListener('keydown', onKey);
      document.addEventListener('click', onOutside, true);
    }
    function close() {
      panel.hidden = true;
      toggle.setAttribute('aria-expanded', 'false');
      document.removeEventListener('keydown', onKey);
      document.removeEventListener('click', onOutside, true);
    }
    function onKey(e) { if (e.key === 'Escape') { close(); toggle.focus(); } }
    function onOutside(e) {
      if (!panel.contains(e.target) && !toggle.contains(e.target)) close();
    }

    toggle.addEventListener('click', function () {
      if (panel.hidden) open(); else close();
    });
  }

  /* --------------------------------------------------------- reveal on scroll */
  function initReveals() {
    var els = $all('[data-reveal]');
    if (!els.length) return;
    if (reduceMotion || !('IntersectionObserver' in window)) {
      els.forEach(function (el) { el.classList.add('is-visible'); });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        }
      });
    }, { rootMargin: '0px 0px -10% 0px', threshold: 0.1 });
    els.forEach(function (el) { io.observe(el); });
  }

  /* ---------------------------------------------------------- filter drawer */
  function initFilterDrawer() {
    var drawer = $('.filter-drawer');
    var scrim = $('.filter-scrim');
    var openBtns = $all('[data-filter-open]');
    if (!drawer || !openBtns.length) return;

    function open() {
      drawer.classList.add('is-open');
      if (scrim) scrim.hidden = false;
      lockScroll();
      document.addEventListener('keydown', onKey);
      var f = $(FOCUSABLE, drawer);
      if (f) f.focus();
    }
    function close() {
      if (!drawer.classList.contains('is-open')) return;
      drawer.classList.remove('is-open');
      if (scrim) scrim.hidden = true;
      unlockScroll();
      document.removeEventListener('keydown', onKey);
    }
    function onKey(e) {
      if (e.key === 'Escape') close();
      else if (e.key === 'Tab') trapFocus(drawer, e);
    }

    openBtns.forEach(function (b) { b.addEventListener('click', open); });
    $all('[data-filter-close]').forEach(function (b) { b.addEventListener('click', close); });
    if (scrim) scrim.addEventListener('click', close);
  }

  /* --------------------------------------------------------------- compare */
  function readCompare() {
    try {
      var raw = window.localStorage.getItem(COMPARE_KEY);
      var arr = raw ? JSON.parse(raw) : [];
      return Array.isArray(arr) ? arr.map(String) : [];
    } catch (e) { return []; }
  }
  function writeCompare(ids) {
    try { window.localStorage.setItem(COMPARE_KEY, JSON.stringify(ids)); } catch (e) { /* quota / disabled */ }
  }
  function updateCompareCount(ids) {
    $all('[data-compare-count]').forEach(function (el) { el.textContent = String(ids.length); });
  }

  function initCompare() {
    var toggles = $all('[data-compare-toggle]');
    var ids = readCompare();

    // reflect stored state onto present toggles
    toggles.forEach(function (btn) {
      var id = btn.getAttribute('data-vehicle-id');
      var on = ids.indexOf(String(id)) !== -1;
      btn.setAttribute('aria-pressed', on ? 'true' : 'false');
      btn.classList.toggle('is-active', on);
    });
    updateCompareCount(ids);

    toggles.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var id = String(btn.getAttribute('data-vehicle-id'));
        if (!id) return;
        var current = readCompare();
        var idx = current.indexOf(id);
        var on;
        if (idx === -1) { current.push(id); on = true; }
        else { current.splice(idx, 1); on = false; }
        writeCompare(current);
        btn.setAttribute('aria-pressed', on ? 'true' : 'false');
        btn.classList.toggle('is-active', on);
        updateCompareCount(current);
      });
    });

    // compare link: append ids as query string on click (still works as link if JS fails)
    $all('[data-compare-link]').forEach(function (link) {
      link.addEventListener('click', function () {
        var current = readCompare();
        if (!current.length) return;
        try {
          var url = new URL(link.href, window.location.origin);
          url.searchParams.set('ids', current.join(','));
          link.href = url.toString();
        } catch (e) { /* leave href untouched */ }
      });
    });
  }

  /* ------------------------------------------------------------ enquiry modal */
  var enquiryModal = null;
  var enquiryRefocus = null;

  function buildEnquiryModal() {
    if (enquiryModal) return enquiryModal;

    var backdrop = document.createElement('div');
    backdrop.className = 'enquiry-modal';
    backdrop.setAttribute('hidden', '');

    var dialog = document.createElement('div');
    dialog.className = 'enquiry-modal__dialog';
    dialog.setAttribute('role', 'dialog');
    dialog.setAttribute('aria-modal', 'true');
    dialog.setAttribute('aria-labelledby', 'enquiry-modal-title');

    var heading = document.createElement('h2');
    heading.className = 'enquiry-modal__title';
    heading.id = 'enquiry-modal-title';
    heading.textContent = 'Enquire';

    var closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.className = 'enquiry-modal__close';
    closeBtn.setAttribute('aria-label', 'Close');
    closeBtn.innerHTML = '&times;';

    var form = document.createElement('form');
    form.className = 'enquiry-modal__form';

    function field(label, name, type, required) {
      var wrap = document.createElement('label');
      wrap.className = 'enquiry-modal__field';
      var span = document.createElement('span');
      span.className = 'enquiry-modal__label';
      span.textContent = label;
      var input = type === 'textarea' ? document.createElement('textarea') : document.createElement('input');
      if (type !== 'textarea') input.type = type;
      input.name = name;
      if (required) input.required = true;
      input.className = 'enquiry-modal__input';
      wrap.appendChild(span);
      wrap.appendChild(input);
      return wrap;
    }

    form.appendChild(field('Name', 'name', 'text', true));
    form.appendChild(field('Email', 'email', 'email', true));
    form.appendChild(field('Phone', 'phone', 'tel', false));
    form.appendChild(field('Message', 'message', 'textarea', false));

    var hiddenId = document.createElement('input');
    hiddenId.type = 'hidden';
    hiddenId.name = 'vehicle_id';
    var hiddenName = document.createElement('input');
    hiddenName.type = 'hidden';
    hiddenName.name = 'vehicle_name';
    var hiddenTopic = document.createElement('input');
    hiddenTopic.type = 'hidden';
    hiddenTopic.name = 'topic';
    form.appendChild(hiddenId);
    form.appendChild(hiddenName);
    form.appendChild(hiddenTopic);

    var error = document.createElement('p');
    error.className = 'enquiry-modal__error';
    error.setAttribute('role', 'alert');
    error.hidden = true;
    form.appendChild(error);

    var submit = document.createElement('button');
    submit.type = 'submit';
    submit.className = 'enquiry-modal__submit';
    submit.textContent = 'Send enquiry';
    form.appendChild(submit);

    dialog.appendChild(heading);
    dialog.appendChild(closeBtn);
    dialog.appendChild(form);
    backdrop.appendChild(dialog);
    document.body.appendChild(backdrop);

    function close() {
      backdrop.hidden = true;
      unlockScroll();
      document.removeEventListener('keydown', onKey);
      error.hidden = true;
      form.reset();
      if (enquiryRefocus) { enquiryRefocus.focus(); enquiryRefocus = null; }
    }
    function onKey(e) {
      if (e.key === 'Escape') close();
      else if (e.key === 'Tab') trapFocus(dialog, e);
    }

    closeBtn.addEventListener('click', close);
    backdrop.addEventListener('click', function (e) { if (e.target === backdrop) close(); });

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      error.hidden = true;
      submit.disabled = true;

      var params = new URLSearchParams();
      params.set('action', 'digicars_enquiry');
      params.set('nonce', data.nonce || '');
      params.set('vehicle_id', hiddenId.value);
      params.set('vehicle_name', hiddenName.value);
      params.set('topic', hiddenTopic.value);
      params.set('name', form.elements.name.value);
      params.set('email', form.elements.email.value);
      params.set('phone', form.elements.phone.value);
      params.set('message', form.elements.message.value);

      fetch(data.ajaxUrl || '', {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString()
      }).then(function (res) {
        return res.json().catch(function () { return { success: false }; });
      }).then(function (json) {
        if (json && json.success) {
          close();
          digicarsToast('Enquiry sent. We will be in touch shortly.', 'success');
        } else {
          error.textContent = (json && json.data && json.data.message) ? json.data.message : 'Something went wrong. Please try again.';
          error.hidden = false;
        }
      }).catch(function () {
        error.textContent = 'Network error. Please try again.';
        error.hidden = false;
      }).then(function () {
        submit.disabled = false;
      });
    });

    enquiryModal = {
      root: backdrop,
      dialog: dialog,
      open: function (vehicleId, vehicleName, topic, opener) {
        heading.textContent = vehicleName ? ('Enquire about ' + vehicleName) : 'Enquire';
        hiddenId.value = vehicleId || '';
        hiddenName.value = vehicleName || '';
        hiddenTopic.value = topic || '';
        error.hidden = true;
        enquiryRefocus = opener || null;
        backdrop.hidden = false;
        lockScroll();
        document.addEventListener('keydown', onKey);
        var firstInput = $(FOCUSABLE, form);
        if (firstInput) firstInput.focus();
      },
      close: close
    };
    return enquiryModal;
  }

  function initEnquiry() {
    var buttons = $all('[data-enquire]');
    if (!buttons.length) return;
    buttons.forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        var modal = buildEnquiryModal();
        modal.open(
          btn.getAttribute('data-vehicle-id'),
          btn.getAttribute('data-vehicle-name'),
          btn.getAttribute('data-topic'),
          btn
        );
      });
    });
  }

  /* ------------------------------------------------------------------ boot */
  function init() {
    initHeader();
    initMobileNav();
    initSearch();
    initReveals();
    initFilterDrawer();
    initCompare();
    initEnquiry();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
