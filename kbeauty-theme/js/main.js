/**
 * Glow K-Beauty — interaction layer.
 *
 * Header scroll shadow, scroll reveals, the hero stage swap driven by
 * the Routine Rail, quick-add AJAX with toast, wishlist toggle, mobile
 * menu, search overlay, filter drawer, PDP thumbnails, quantity
 * steppers, and AJAX newsletter/contact forms.
 *
 * Motion budget: one orchestrated signature interaction (the stage
 * swap), reveals, and card micro-interactions. `prefers-reduced-motion`
 * is respected — CSS kills transitions, JS skips choreography.
 */
(function () {
	'use strict';

	var REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var IS_EDITOR = typeof window.elementorFrontend !== 'undefined' && typeof window.elementorFrontend.isEditMode === 'function' && window.elementorFrontend.isEditMode();

	function qs(sel, ctx) { return (ctx || document).querySelector(sel); }
	function qsa(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }

	/* ------------------------------------------------------------------
	 * Toast
	 * ------------------------------------------------------------------ */
	var toastTimer = null;

	function toast(message) {
		var el = qs('[data-toast]');
		var msg = qs('[data-toast-message]');
		if (!el || !msg) { return; }

		msg.textContent = message;
		el.classList.add('is-visible');

		window.clearTimeout(toastTimer);
		toastTimer = window.setTimeout(function () {
			el.classList.remove('is-visible');
		}, 3200);
	}

	/* ------------------------------------------------------------------
	 * Header scroll shadow
	 * ------------------------------------------------------------------ */
	var header = qs('[data-header]');

	function onScroll() {
		if (header) {
			header.classList.toggle('is-scrolled', window.scrollY > 8);
		}
	}

	window.addEventListener('scroll', onScroll, { passive: true });
	onScroll();

	/* ------------------------------------------------------------------
	 * Scroll reveals
	 * ------------------------------------------------------------------ */
	var revealEls = qsa('[data-reveal]');

	var io = null;

	function observeReveal(el) {
		if (REDUCED || IS_EDITOR || !('IntersectionObserver' in window)) {
			el.classList.add('is-visible');
		} else if (io) {
			io.observe(el);
		}
	}

	if (revealEls.length) {
		if (REDUCED || IS_EDITOR || !('IntersectionObserver' in window)) {
			revealEls.forEach(function (el) { el.classList.add('is-visible'); });
		} else {
			io = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						io.unobserve(entry.target);
					}
				});
			}, { threshold: 0.08, rootMargin: '0px 0px -20px 0px' });

			revealEls.forEach(function (el) { io.observe(el); });
		}
	}

	if (typeof window.elementorFrontend !== 'undefined') {
		window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function () {
			qsa('[data-reveal]:not(.is-visible)').forEach(observeReveal);
		});
	}

	/* ------------------------------------------------------------------
	 * Hero stage swap — the signature. Hovering or focusing a rail step
	 * swaps the staged product with a 180ms fade/translate.
	 *
	 * Uses event delegation from document so the listeners survive
	 * Elementor widget re-renders. activate() re-queries the DOM on
	 * each call for the same reason.
	 * ------------------------------------------------------------------ */
	var activeStep = 1;

	function activateStep(stepNo) {
		if (REDUCED) { return; }
		var stage = qs('[data-hero-stage]');
		if (!stage) { return; }

		// Compute slide direction based on step distance (handles wrap-around)
		var n = qsa('[data-stage]', stage).length;
		if (stepNo !== activeStep) {
			var fwdDist = (stepNo - activeStep + n) % n;
			var bwdDist = (activeStep - stepNo + n) % n;
			stage.setAttribute('data-nav-dir', fwdDist <= bwdDist ? 'forward' : 'backward');
		}

		activeStep = stepNo;

		// Clear any lingering exit animation before starting new one
		qsa('.stage-item.is-leaving', stage).forEach(function (item) {
			item.classList.remove('is-leaving');
		});

		qsa('[data-stage]', stage).forEach(function (item) {
			var isActive = parseInt(item.getAttribute('data-stage'), 10) === stepNo;
			var wasActive = item.classList.contains('is-active');

			if (wasActive && !isActive) {
				item.classList.remove('is-active');
				item.classList.add('is-leaving');
				setTimeout(function () { item.classList.remove('is-leaving'); }, 320);
			} else if (!wasActive && isActive) {
				item.classList.add('is-active');
			}

			if (isActive) {
				stage.classList.remove('tone-seafoam', 'tone-petal', 'tone-rice-deep');
				stage.classList.add(item.getAttribute('data-tone') || 'tone-seafoam');
			}
		});

		qsa('.rail-step[data-step]').forEach(function (step) {
			step.classList.toggle('is-active', parseInt(step.getAttribute('data-step'), 10) === stepNo);
		});

		// Update dot indicators if present
		qsa('.stage-dot[data-dot]', stage).forEach(function (dot) {
			dot.classList.toggle('is-active', parseInt(dot.getAttribute('data-dot'), 10) === stepNo);
		});
	}

	document.addEventListener('mouseover', function (e) {
		var step = e.target && e.target.closest && e.target.closest('.rail-step[data-step]');
		if (step) { activateStep(parseInt(step.getAttribute('data-step'), 10)); }
	});

	document.addEventListener('focusin', function (e) {
		var step = e.target && e.target.closest && e.target.closest('.rail-step[data-step]');
		if (step) { activateStep(parseInt(step.getAttribute('data-step'), 10)); }
	});

	/* ------------------------------------------------------------------
	 * Hero stage auto-advance
	 * ------------------------------------------------------------------ */
	(function () {
		if (REDUCED) { return; }
		var stageEl = qs('[data-hero-stage]');
		if (!stageEl) { return; }

		var INTERVAL = 4000;
		var timer = null;

		// Inject progress bar
		var progressWrap = document.createElement('div');
		progressWrap.className = 'stage-progress';
		var progressBar = document.createElement('div');
		progressBar.className = 'stage-progress-bar';
		progressWrap.appendChild(progressBar);
		stageEl.appendChild(progressWrap);

		function totalSteps() {
			return qsa('[data-stage]', stageEl).length;
		}

		function restartBar() {
			progressBar.classList.remove('is-running');
			progressBar.offsetWidth; // force reflow so animation restarts
			progressBar.classList.add('is-running');
		}

		function advance() {
			var stageEl2 = qs('[data-hero-stage]');
			if (stageEl2) { stageEl2.setAttribute('data-nav-dir', 'forward'); }
			var next = (activeStep % totalSteps()) + 1;
			activateStep(next);
			restartBar();
		}

		function start() {
			if (timer) { return; }
			restartBar();
			timer = window.setInterval(advance, INTERVAL);
		}

		function stop() {
			window.clearInterval(timer);
			timer = null;
			progressBar.classList.remove('is-running');
		}

		// Pause while hovering the stage or the routine rail
		stageEl.addEventListener('mouseenter', stop);
		stageEl.addEventListener('mouseleave', start);

		var rail = qs('.routine-rail');
		if (rail) {
			rail.addEventListener('mouseenter', stop);
			rail.addEventListener('mouseleave', start);
		}

		// Also stop on manual rail-step interaction (mouseover already fires activateStep)
		document.addEventListener('mouseover', function (e) {
			if (e.target && e.target.closest && e.target.closest('.rail-step[data-step]')) { stop(); }
		});

		start();

		// Stop auto-advance permanently after any manual navigation
		stageEl.addEventListener('glow:stage-manual', stop);
	}());

	/* ------------------------------------------------------------------
	 * Hero stage — touch swipe + arrow buttons + dot indicators
	 * ------------------------------------------------------------------ */
	(function () {
		var stageEl = qs('[data-hero-stage]');
		if (!stageEl) { return; }

		function stageCount() {
			return qsa('[data-stage]', stageEl).length;
		}

		function navigate(toStep) {
			activateStep(toStep);
			stageEl.dispatchEvent(new CustomEvent('glow:stage-manual'));
		}

		function goPrev() {
			var n = stageCount();
			navigate(((activeStep - 2 + n) % n) + 1);
		}

		function goNext() {
			navigate((activeStep % stageCount()) + 1);
		}

		// Arrow buttons
		var prevBtn = qs('[data-stage-prev]');
		var nextBtn = qs('[data-stage-next]');
		if (prevBtn) { prevBtn.addEventListener('click', goPrev); }
		if (nextBtn) { nextBtn.addEventListener('click', goNext); }

		// Dot indicators (injected into stage, shown on touch devices via CSS)
		var dotsEl = document.createElement('div');
		dotsEl.className = 'stage-dots';
		dotsEl.setAttribute('aria-hidden', 'true');
		var total = stageCount();
		for (var i = 1; i <= total; i++) {
			var dot = document.createElement('button');
			dot.type = 'button';
			dot.className = 'stage-dot' + (i === 1 ? ' is-active' : '');
			dot.setAttribute('data-dot', String(i));
			dotsEl.appendChild(dot);
		}
		stageEl.appendChild(dotsEl);

		dotsEl.addEventListener('click', function (e) {
			var dot = e.target.closest('[data-dot]');
			if (!dot) { return; }
			navigate(parseInt(dot.getAttribute('data-dot'), 10));
		});

		// Touch swipe — horizontal swipe of ≥40 px that is more horizontal than vertical
		var touchX = 0;
		var touchY = 0;
		stageEl.addEventListener('touchstart', function (e) {
			touchX = e.changedTouches[0].clientX;
			touchY = e.changedTouches[0].clientY;
		}, { passive: true });

		stageEl.addEventListener('touchend', function (e) {
			var dx = e.changedTouches[0].clientX - touchX;
			var dy = e.changedTouches[0].clientY - touchY;
			if (Math.abs(dx) < 40 || Math.abs(dx) < Math.abs(dy)) { return; }
			if (dx < 0) { goNext(); } else { goPrev(); }
		}, { passive: true });
	}());

	/* ------------------------------------------------------------------
	 * Quick add to bag (AJAX)
	 * ------------------------------------------------------------------ */
	function updateCartCount(count) {
		qsa('[data-cart-count]').forEach(function (el) {
			el.textContent = count;
		});
	}

	document.addEventListener('click', function (event) {
		var btn = event.target.closest('[data-quick-add]');
		if (!btn || typeof glowData === 'undefined') { return; }

		event.preventDefault();
		btn.setAttribute('aria-busy', 'true');

		var body = new FormData();
		body.append('action', 'glow_quick_add');
		body.append('nonce', glowData.nonce);
		body.append('product_id', btn.getAttribute('data-quick-add'));

		fetch(glowData.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' })
			.then(function (res) { return res.json(); })
			.then(function (json) {
				if (json && json.success) {
					updateCartCount(json.data.count);
					toast(json.data.message);
					document.body.dispatchEvent(new Event('wc_fragment_refresh'));
				} else {
					toast((json && json.data && json.data.message) || 'Something went sideways. Try the product page.');
				}
			})
			.catch(function () {
				toast('Connection hiccup — nothing was added. Try again.');
			})
			.finally(function () {
				btn.removeAttribute('aria-busy');
			});
	});

	/* ------------------------------------------------------------------
	 * Wishlist (localStorage)
	 * ------------------------------------------------------------------ */
	var WISHLIST_KEY = 'glow_wishlist';

	function getWishlist() {
		try {
			return JSON.parse(window.localStorage.getItem(WISHLIST_KEY)) || [];
		} catch (e) {
			return [];
		}
	}

	function setWishlist(ids) {
		try {
			window.localStorage.setItem(WISHLIST_KEY, JSON.stringify(ids));
		} catch (e) { /* Private browsing — wishlist just won't persist. */ }
	}

	function paintWishlistButtons() {
		var saved = getWishlist();
		qsa('[data-wishlist]').forEach(function (btn) {
			var on = saved.indexOf(btn.getAttribute('data-wishlist')) !== -1;
			btn.classList.toggle('is-saved', on);
			btn.setAttribute('aria-pressed', on ? 'true' : 'false');
		});
	}

	document.addEventListener('click', function (event) {
		var btn = event.target.closest('[data-wishlist]');
		if (!btn) { return; }

		event.preventDefault();
		var id = btn.getAttribute('data-wishlist');
		var saved = getWishlist();
		var index = saved.indexOf(id);

		if (index === -1) {
			saved.push(id);
			toast('Saved to your wishlist.');
		} else {
			saved.splice(index, 1);
			toast('Removed from your wishlist.');
		}

		setWishlist(saved);
		paintWishlistButtons();
	});

	paintWishlistButtons();

	/* ------------------------------------------------------------------
	 * Mobile menu
	 * ------------------------------------------------------------------ */
	var menu = qs('[data-mobile-menu]');
	var menuToggle = qs('[data-menu-toggle]');

	function openMenu() {
		if (!menu) { return; }
		menu.hidden = false;
		// Next frame so the transition runs from the hidden state.
		window.requestAnimationFrame(function () {
			document.body.classList.add('menu-open');
		});
		if (menuToggle) { menuToggle.setAttribute('aria-expanded', 'true'); }
		var firstLink = qs('a', menu);
		if (firstLink) { firstLink.focus(); }
	}

	function closeMenu() {
		if (!menu) { return; }
		document.body.classList.remove('menu-open');
		if (menuToggle) {
			menuToggle.setAttribute('aria-expanded', 'false');
			menuToggle.focus();
		}
		window.setTimeout(function () { menu.hidden = true; }, REDUCED ? 0 : 320);
	}

	if (menuToggle) { menuToggle.addEventListener('click', openMenu); }

	var menuClose = qs('[data-menu-close]');
	if (menuClose) { menuClose.addEventListener('click', closeMenu); }

	/* ------------------------------------------------------------------
	 * Search overlay
	 * ------------------------------------------------------------------ */
	var searchOverlay = qs('[data-search-overlay]');
	var searchToggle = qs('[data-search-toggle]');

	function openSearch() {
		if (!searchOverlay) { return; }
		searchOverlay.hidden = false;
		window.requestAnimationFrame(function () {
			document.body.classList.add('search-open');
		});
		if (searchToggle) { searchToggle.setAttribute('aria-expanded', 'true'); }
		var field = qs('[data-search-field]');
		if (field) { field.focus(); }
	}

	function closeSearch() {
		if (!searchOverlay) { return; }
		document.body.classList.remove('search-open');
		if (searchToggle) {
			searchToggle.setAttribute('aria-expanded', 'false');
			searchToggle.focus();
		}
		window.setTimeout(function () { searchOverlay.hidden = true; }, REDUCED ? 0 : 180);
	}

	if (searchToggle) { searchToggle.addEventListener('click', openSearch); }

	if (searchOverlay) {
		searchOverlay.addEventListener('click', function (event) {
			if (event.target === searchOverlay) { closeSearch(); }
		});
	}

	/* ------------------------------------------------------------------
	 * Filter drawer (mobile shop archives)
	 * ------------------------------------------------------------------ */
	var filterToggle = qs('[data-filter-toggle]');
	var filterScrim = qs('[data-filter-scrim]');

	function openFilters() {
		document.body.classList.add('filters-open');
		if (filterScrim) { filterScrim.hidden = false; }
		if (filterToggle) { filterToggle.setAttribute('aria-expanded', 'true'); }
	}

	function closeFilters() {
		document.body.classList.remove('filters-open');
		if (filterScrim) { filterScrim.hidden = true; }
		if (filterToggle) { filterToggle.setAttribute('aria-expanded', 'false'); }
	}

	if (filterToggle) { filterToggle.addEventListener('click', openFilters); }
	if (filterScrim) { filterScrim.addEventListener('click', closeFilters); }

	var filterClose = qs('[data-filters-close]');
	if (filterClose) { filterClose.addEventListener('click', closeFilters); }

	/* ------------------------------------------------------------------
	 * Shop nav flyout
	 * ------------------------------------------------------------------ */
	var flyout = qs('[data-flyout]');
	var flyoutTrigger = qs('[data-flyout-trigger]');

	function isFlyoutOpen() {
		return flyout && flyout.classList.contains('is-open');
	}

	function openFlyout() {
		if (!flyout) { return; }
		flyout.removeAttribute('hidden');
		window.requestAnimationFrame(function () {
			flyout.classList.add('is-open');
		});
		if (flyoutTrigger) { flyoutTrigger.setAttribute('aria-expanded', 'true'); }
	}

	function closeFlyout() {
		if (!flyout) { return; }
		flyout.classList.remove('is-open');
		if (flyoutTrigger) { flyoutTrigger.setAttribute('aria-expanded', 'false'); }
		window.setTimeout(function () {
			if (!flyout.classList.contains('is-open')) { flyout.hidden = true; }
		}, REDUCED ? 0 : 220);
	}

	if (flyoutTrigger) {
		flyoutTrigger.addEventListener('click', function () {
			if (isFlyoutOpen()) { closeFlyout(); } else { openFlyout(); }
		});
	}

	// Click outside closes flyout
	document.addEventListener('click', function (event) {
		if (!isFlyoutOpen()) { return; }
		var header = qs('[data-header]');
		if (header && !header.contains(event.target)) { closeFlyout(); }
	}, true);

	/* ------------------------------------------------------------------
	 * Escape closes whichever layer is open
	 * ------------------------------------------------------------------ */
	document.addEventListener('keydown', function (event) {
		if (event.key !== 'Escape') { return; }
		if (isFlyoutOpen()) { closeFlyout(); if (flyoutTrigger) { flyoutTrigger.focus(); } }
		if (document.body.classList.contains('menu-open')) { closeMenu(); }
		if (document.body.classList.contains('search-open')) { closeSearch(); }
		if (document.body.classList.contains('filters-open')) { closeFilters(); }
	});

	/* ------------------------------------------------------------------
	 * PDP thumbnails
	 * ------------------------------------------------------------------ */
	var pdpMain = qs('[data-pdp-main] img');

	if (pdpMain) {
		qsa('.pdp-thumb').forEach(function (thumb) {
			thumb.addEventListener('click', function () {
				var full = thumb.getAttribute('data-full');
				if (!full) { return; }
				pdpMain.src = full;
				pdpMain.removeAttribute('srcset');
				qsa('.pdp-thumb').forEach(function (t) { t.classList.remove('is-active'); });
				thumb.classList.add('is-active');
			});
		});
	}

	/* ------------------------------------------------------------------
	 * Quantity steppers (PDP and cart)
	 * ------------------------------------------------------------------ */
	qsa('.quantity').forEach(function (wrap) {
		if (wrap.querySelector('.qty-step')) { return; }

		var input = wrap.querySelector('input.qty');
		if (!input || input.type === 'hidden') { return; }

		var minus = document.createElement('button');
		minus.type = 'button';
		minus.className = 'qty-step qty-minus';
		minus.setAttribute('aria-label', 'Decrease quantity');
		minus.textContent = '−';

		var plus = document.createElement('button');
		plus.type = 'button';
		plus.className = 'qty-step qty-plus';
		plus.setAttribute('aria-label', 'Increase quantity');
		plus.textContent = '+';

		wrap.insertBefore(minus, input);
		wrap.appendChild(plus);

		function step(delta) {
			var value = parseInt(input.value, 10) || 1;
			var min = parseInt(input.min, 10) || 1;
			var max = parseInt(input.max, 10);
			value += delta;
			if (value < min) { value = min; }
			if (!isNaN(max) && max > 0 && value > max) { value = max; }
			input.value = value;
			input.dispatchEvent(new Event('change', { bubbles: true }));
		}

		minus.addEventListener('click', function () { step(-1); });
		plus.addEventListener('click', function () { step(1); });
	});

	/* ------------------------------------------------------------------
	 * AJAX forms: newsletter + contact
	 * ------------------------------------------------------------------ */
	qsa('[data-ajax-form]').forEach(function (form) {
		form.addEventListener('submit', function (event) {
			event.preventDefault();
			if (typeof glowData === 'undefined') { return; }

			var action = form.getAttribute('data-ajax-form');
			var submit = form.querySelector('[type="submit"]');
			var body = new FormData(form);
			body.append('action', action);
			body.append('nonce', glowData.nonce);

			if (submit) { submit.setAttribute('aria-busy', 'true'); }

			var oldNote = form.querySelector('.form-feedback');
			if (oldNote) { oldNote.remove(); }

			fetch(glowData.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' })
				.then(function (res) { return res.json(); })
				.then(function (json) {
					var ok = json && json.success;
					var note = document.createElement('p');
					note.className = 'form-feedback ' + (ok ? 'is-success' : 'is-error');
					note.setAttribute('role', 'status');
					note.textContent = (json && json.data && json.data.message) || 'Something went sideways. Try again.';
					form.appendChild(note);
					if (ok) { form.reset(); }
				})
				.catch(function () {
					var note = document.createElement('p');
					note.className = 'form-feedback is-error';
					note.setAttribute('role', 'status');
					note.textContent = 'Connection hiccup — nothing was sent. Try again.';
					form.appendChild(note);
				})
				.finally(function () {
					if (submit) { submit.removeAttribute('aria-busy'); }
				});
		});
	});
})();
