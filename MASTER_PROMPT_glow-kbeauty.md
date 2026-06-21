# MASTER_PROMPT — Glow K-Beauty WooCommerce Theme (v2)

You are a senior WordPress/WooCommerce theme developer and design lead. Build a complete, production-ready WooCommerce theme called **Glow K-Beauty** from this specification alone. Do not deviate from the design system. Do not add features not specified here. When a decision is not covered, choose the option that best serves the customer journeys in Section 4.

---

## 1. PROJECT BRIEF

Build an award-calibre ecommerce theme for a South African K-beauty store. It must:

- Look designed by a human studio, not generated. Avoid all AI-design tells: no glassmorphism, no floating gradient orbs, no centered hero with a big stat, no cream-plus-terracotta-serif default, no dark-mode-with-acid-green default, no decorative animation for its own sake.
- Be a **standard, clean WooCommerce theme** with no proprietary plugin hooks, no AI branding, no custom action hooks beyond WordPress/WooCommerce norms. (A plugin will be integrated later via normal WooCommerce hooks; the theme must not know or care.)
- Be organised around the **customer journey**, not around what looks pretty. Every section must serve one of the five personas in Section 3.
- Ship with SEO baked in: persona-driven meta descriptions, Open Graph, and JSON-LD schema (Organization, WebSite+SearchAction, Product, BreadcrumbList, FAQPage).
- Work out of the box with the bundled dummy data (20 products, SVG assets).

Theme slug / text domain: `glow-kbeauty`. Version: `2.0.0`. Author: CloudIA.

---

## 2. DESIGN SYSTEM (non-negotiable)

### 2.1 Concept
**"Skin is a practice, not a product."** K-beauty's real differentiator is sequence — the routine. The store's information architecture, navigation, and signature interaction are all built on the 7-step routine. The routine IS the navigation.

### 2.2 The signature element: The Routine Rail
A horizontal 7-step strip (STEP 01 Cleanse → STEP 07 Protect), each step showing its number (mono font), English name, and Korean name. Steps link to their product categories. A 3px yuja-yellow progress line animates across the top of a step on hover/active.

The rail appears on: homepage (below hero), shop/category archives (as wayfinding), and the 404 page (as recovery). On the **homepage**, hovering a rail step swaps the hero "stage" — product image, name, price, and background colour — with a 180ms fade/translate transition.

The 7 steps (single source of truth, one PHP function `glow_routine_steps()`):

| No | Name | Korean | Category slug |
|----|------|--------|---------------|
| 01 | Cleanse | 클렌징 | cleansers |
| 02 | Exfoliate | 각질 | exfoliators |
| 03 | Tone | 토너 | toners-essences |
| 04 | Treat | 세럼 | serums-ampoules |
| 05 | Moisturise | 보습 | moisturisers |
| 06 | Eye | 아이 | eye-care |
| 07 | Protect | 자외선 | sun-care |

Numbered markers are justified here because routine order is genuine information, not decoration. Do not use numbering anywhere it doesn't encode real sequence.

### 2.3 Palette — drawn from Korean skincare ingredients (rice water, mugwort, yuja citron, seafoam algae, clay). NOT the pink/mint K-beauty cliché.

```css
--rice:      #F3F2ED;  /* base background */
--rice-deep: #EBEAE2;  /* alternate panels */
--moss:      #2E4636;  /* primary dark (mugwort green) */
--moss-soft: #3D5847;
--ink:       #23281F;  /* text, near-black green */
--yuja:      #F2B63C;  /* citron accent — CTAs and highlights ONLY, use sparingly */
--yuja-deep: #DB9F23;
--seafoam:   #C9DCD2;  /* soft cool panel */
--petal:     #E8D5CE;  /* warm clay panel */
--line:      #D9D7CC;
--muted:     #6F7468;
```

### 2.4 Typography (Google Fonts)
- **Display:** Young Serif — headlines, accordion summaries, footer wordmark. Weight 400 only, used with restraint.
- **Body/UI:** Schibsted Grotesk — 400/500/600/700.
- **Utility:** Spline Sans Mono — step numbers, prices, SKUs, eyebrows, badges, counts. This is the "apothecary label" voice.

Never use Cormorant, Playfair, Inter, Poppins, or Montserrat.

Type scale: `.t-hero` clamp(2.6rem→5.2rem), `.t-1` clamp(2rem→3.2rem), `.t-2` clamp(1.4rem→1.9rem), body 16px / 1.65.

### 2.5 Cultural grounding
Section eyebrows pair a Korean word with English in mono caps, e.g. `피부 SKIN`, `성분 THE INDEX`, `후기 FROM REAL SKIN`. Footer carries an oversized, low-contrast Young Serif wordmark: **피부는 연습** ("skin is practice"). Korean is an accent, never decoration overload — one Hangul element per section maximum.

### 2.6 Layout & motion rules
- Asymmetric editorial layouts. Hero is a split grid (copy left ~1.1fr, product stage right ~0.9fr), never centered.
- Radii: 4 / 10 / 18px + pill. Max container 1280px, gutter clamp(20px→56px).
- Motion budget: ONE orchestrated signature interaction (hero stage swap), subtle scroll-reveals (`opacity` + 24px translateY, IntersectionObserver), hover micro-interactions on cards (image scale 1.05 + −1.5° rotate, quick-add slides up, wishlist fades in). Nothing else. Respect `prefers-reduced-motion` globally.
- No Three.js, no canvas, no particles, no parallax.
- Accessibility floor: skip link, visible `:focus-visible` outlines, aria-labels on icon buttons, `aria-live` on the hero stage and toast, semantic headings.

---

## 3. THE FIVE PERSONAS (drive SEO + page content)

Document these in code comments inside `inc/seo.php`. Every meta description maps to one persona's search intent.

1. **The Routine Builder** (25–34, new to K-beauty, wants a guided start)
   - Intent: "korean skincare routine south africa", "k-beauty routine order", "skincare routine for beginners"
   - Served by: homepage hero + routine rail, routine-step categories. Homepage meta description targets this persona.

2. **The Ingredient Analyst** (researches actives before buying)
   - Intent: "snail mucin serum south africa", "niacinamide for pores", "centella asiatica products"
   - Served by: product pages (actives listed first in meta description, Product schema with brand/offer/aggregateRating), homepage Ingredient Index section.

3. **The Busy Minimalist** (35–45 professional, wants efficiency and clarity)
   - Intent: "simple korean skincare routine", "quick skincare routine"
   - Served by: concern tiles on homepage (skip the routine, go to the fix), category pages with concise meta copy, fast filtering, no-fluff toolbar.

4. **The Gift Shopper** (buying for someone else, needs curation + trust)
   - Intent: "k-beauty gift set south africa", "skincare gifts for her"
   - Served by: best-sellers section, named-and-located customer reviews, About page trust narrative, Organization schema.

5. **The Sensitive-Skin Sceptic** (reactive skin, burnt before, needs disclosure)
   - Intent: "fragrance free korean skincare", "sensitive skin k-beauty"
   - Served by: FAQ page with FAQPage schema, skin-type taxonomy archive, full-disclosure accordion on product pages, patch-test guidance, sourcing/batch-verification content.

---

## 4. PAGES & CUSTOMER JOURNEYS

### 4.1 Homepage (`front-page.php`) — section order is the journey
1. **Hero** — split grid. H1: "Skin is a *practice,* not a product." Lead copy explains routine-ordered shopping. CTAs: "Shop all products" (solid moss) + "Build my routine" (outline, anchors to rail). Right: interactive product stage with step tag, product meta bar, price.
2. **Routine Rail** (the signature) — drives the hero stage on hover, links to categories on click.
3. **Shop by concern** — 4 tiles (Dehydrated & dull / Breakouts & texture / Fine lines & firmness / Sensitive & reactive) in seafoam/petal/moss/rice-deep, linking to `skin_concern` and `skin_type` taxonomy archives. (Persona 3's shortcut.)
4. **Best sellers** — 4 featured products via `wc_get_products(['featured'=>true])`, fallback to latest. (Persona 4.)
5. **Sourcing split** — moss media panel + white body: "Every batch verified. Every ingredient listed." CTA → About. (Persona 5.)
6. **Ingredient Index** — editorial list rows (mono number / Young Serif ingredient name / what-it's-for / arrow): Snail mucin, Centella asiatica, Niacinamide, Hyaluronic acid, Rice extract. Rows link to product search. (Persona 2.)
7. **Reviews** — 3 cards with SA names + cities + persona hints (Sandton routine builder, Cape Town sensitive skin, Pretoria gift shopper).
8. **Newsletter** — moss panel, giant low-opacity 균형 glyph behind, "One email a month. Skin science, no noise."

### 4.2 Shop & category (`archive-product.php`)
Journey: land → orient (breadcrumb + page hero + routine rail) → narrow (sidebar filters: Step/categories, Skin type, Concern, Price widget) → decide (product grid). Mobile: filters become a slide-in drawer with scrim. Toolbar: filter toggle, result count (mono), WC ordering select. Empty state suggests searching by ingredient.

### 4.3 Single product (`single-product.php`)
Journey: orient (breadcrumb + STEP badge) → assess (brand, title, stars, price, short-desc callout with yuja left border, ACTIVES chips, SKIN FIT chips, vegan/cruelty-free marks) → commit (sticky buy panel, qty + add to cart, 3 assurance bullets) → reassure (accordion: About / How it fits your routine [7-step visual with active step highlighted] / Full ingredient disclosure / Reviews) → continue ("Continue the routine" — 4 related products). Accordions (`<details>`), not tabs — scannable in one scroll.

### 4.4 Static pages
- **About** (`page-about.php`, template name "About") — trust page. Hero: "We read the back of the bottle so you don't have to." Origin story (Joburg team, direct Seoul relationships, Korean-language sourcing), 4 numbered principles (Direct sourcing only / Batch transparency / Full disclosure / Routine over revenue), Seoul→Joburg supply-line panel.
- **Contact** (`page-contact.php`, template "Contact") — "Talk to a person about your skin." Channels column (WhatsApp, email, hours SAST, a 한국어 상담 block) + form (name, email, topic select, message). Honest microcopy: reply within one working day.
- **Help & FAQ** (`page-faq.php`, template "Help & FAQ") — 7 Q&As from a single PHP function `glow_faq_items()` that ALSO feeds the FAQPage schema, so page and schema can never drift. Topics: authenticity, fragrance-free filtering, patch testing, delivery times by region, returns (incl. reaction policy), routine order, shipping scope. Plus a sensitive-skin split panel.
- **404** (`404.php`) — rescue page: "This page evaporated like a bad toner." Product search form, the routine rail, 4 customer favourites.
- **page.php / index.php** — clean editorial fallbacks.

### 4.5 Header & footer
- Header: notice bar (free shipping over R500 · batch-verified · 한국 화장품 정품), sticky header with shadow on scroll. Grid: brand left (Young Serif "Glow" + 글로우), nav center-left (Shop all / The routine / Ingredients / About / Help), utilities right (search, account, cart with live mono count via WC fragments). Mobile: full-screen moss overlay menu, Young Serif links with Hangul tags.
- Footer: ink background. Brand + 3 link columns (Shop / Learn / Support), bottom bar with © + payment badges (PayFast, Visa, Mastercard, EFT), then the oversized 피부는 연습 wordmark.

---

## 5. TECHNICAL SPEC

### 5.1 File structure
```
kbeauty-theme/
├── style.css                 # header + full design system (tokens, base, components)
├── functions.php             # setup, enqueue, routine steps, taxonomies, AJAX, helpers
├── inc/seo.php               # persona docs, meta, OG, JSON-LD, glow_faq_items()
├── header.php / footer.php
├── front-page.php / index.php / page.php
├── page-about.php / page-contact.php / page-faq.php / 404.php
├── archive-product.php / single-product.php
├── woocommerce/content-product.php   # product card
├── css/woocommerce.css       # WC-only styles, enqueued conditionally
├── js/main.js                # header scroll, reveals, quick-add AJAX, toast,
│                             # wishlist toggle, mobile menu, filter drawer,
│                             # PDP thumbs, newsletter/contact form handlers
├── images/products/*.svg     # 20 bundled product visuals
├── dummy-products.php        # WP-CLI importer: wp eval-file dummy-products.php
└── screenshot.png            # 1200×900 preview matching the real design
```

### 5.2 functions.php requirements
- Theme supports: title-tag, post-thumbnails, custom-logo, HTML5, woocommerce + gallery zoom/lightbox/slider. Menus: primary, footer. Image size `glow-card` 600×600 crop.
- Enqueue: Google Fonts (Young Serif; Schibsted Grotesk 400–700 + italic; Spline Sans Mono 400–600), style.css, css/woocommerce.css (only if WC active), js/main.js with localized `glowData` (ajaxUrl, nonce).
- `glow_routine_steps()` and `glow_routine_rail($linked)` render the signature.
- Taxonomies: `skin_concern` (slug `concern`), `skin_type` (slug `skin-type`), both public with admin columns.
- Product meta keys (MUST match importer): `_product_brand`, `_skin_types`, `_key_ingredients`, `_product_routine_step`, `_is_vegan`, `_is_cruelty_free`. Helper `glow_meta($id,$key)`.
- WC adjustments: remove default content wrappers, sidebar, sale flash; `woocommerce_show_page_title` false; 12 per page; breadcrumb delimiter `→`.
- AJAX `glow_quick_add` (nonce-checked) returning new cart count; cart-count fragment filter.
- Helpers: `glow_product_badges($product)` (Sale yuja / Best seller ink / Vegan moss / New seafoam if <30 days and not featured), `glow_stars($rating)`.
- NO custom `do_action` hooks. NO references to any AI plugin anywhere in code or comments.

### 5.3 SEO spec (inc/seo.php)
- `wp_head` priority 1: meta description + keywords switched by context (front page→Persona 1 copy; product→"Key actives: …" from `_key_ingredients` + trimmed excerpt; category→Persona 3 copy with term name; about→Persona 4; faq/help→Persona 5; contact; search). OG title/type/url/description/image + twitter summary_large_image.
- `wp_head` priority 2 JSON-LD: Organization (areaServed ZA, availableLanguage English+Korean), WebSite with SearchAction targeting `?s={search_term_string}&post_type=product`, Product (name, description, sku, brand from meta, image, Offer with price/currency/availability/url, aggregateRating when reviews exist), BreadcrumbList on products + category archives, FAQPage on the FAQ template built from `glow_faq_items()`.

### 5.4 Dummy data
20 products across the 7 routine categories + sheet masks/lips, realistic SA Rand pricing, brands (COSRX, Laneige, Klairs, Innisfree, Missha, Some By Mi, Etude, Banila Co, Dr.Jart+, AHC, Mediheal), full meta per product, synthetic ratings. Importer runs via `wp eval-file dummy-products.php`. SVG visuals bundled in `images/products/` (the homepage hero stage references these theme assets directly so the signature works before any media upload).

### 5.5 Quality bar / acceptance criteria
- [ ] Activating the theme with WooCommerce + imported dummy data produces a complete, polished store with zero placeholder text.
- [ ] Hero stage swaps correctly for all 7 rail steps; rail links resolve to category archives.
- [ ] Quick-add updates the cart count without reload and shows a toast.
- [ ] Mobile (≤900px): drawer filters, overlay menu, 2-col product grid, no horizontal scroll at 360px.
- [ ] Keyboard-only navigation works end to end; reduced-motion disables all transitions.
- [ ] Rich Results test passes for Product, BreadcrumbList, FAQPage, WebSite.
- [ ] Nothing in the rendered pages reads as templated: no centered-stat hero, no gradient orbs, no generic "Why choose us" 3-icon row, no lorem ipsum.
- [ ] Copy is specific and human throughout (e.g. "This page evaporated like a bad toner", "The ones people reorder") — never "Welcome to our store" or "Quality products at great prices".

### 5.6 Installation (document in output)
1. Zip theme folder → WP Admin → Appearance → Themes → Upload → Activate.
2. Ensure WooCommerce active. Run `wp eval-file wp-content/themes/kbeauty-theme/dummy-products.php`.
3. Settings → Reading → static front page (create "Home").
4. Create pages About, Contact, Help (slug `help` or `faq`) and assign their templates.
5. Optionally upload `images/products/*.svg` to Media and attach as product images.

---

## 6. VOICE & COPY RULES

Write all interface copy from the customer's side of the screen. Specific beats clever; clever beats generic. Active voice, sentence case, plain verbs. Buttons say what happens ("Add to bag", "Send message", not "Submit"). Errors and empty states give direction, not apology. The brand voice is a knowledgeable friend who reads ingredient labels — confident, a little dry, never breathless. South African context is real, not decorative: Rand pricing, SAST hours, province-level shipping times, PayFast.
