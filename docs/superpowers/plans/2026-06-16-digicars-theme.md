# Digicars Theme Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a standalone, production-ready WooCommerce theme for Digicars Group — a multi-brand "phygital" SA car marketplace — in `digicars-theme/`, with an AI-Concierge signature, a no-checkout enquiry/finance funnel, generated imagery, and a Helix-ready integration seam.

**Architecture:** Vehicles are WooCommerce products (so Helix can embed catalogue + attributes), but cart/checkout is fully disabled — CTAs are Enquire / Check affordability / Apply for finance / Compare. A self-contained "Concierge" discovery feature (guided chips → deterministic match over vehicle meta/taxonomies via theme AJAX) is the homepage signature, isolated behind one documented contract so Helix can later power it with no theme dependency and no custom `do_action` hooks. File structure mirrors `kbeauty-theme/`.

**Tech Stack:** WordPress + WooCommerce, PHP, vanilla JS (no framework), CSS custom properties. Google Fonts: Archivo Expanded, Hanken Grotesk, JetBrains Mono. Imagery via higgsfield / replicate MCP. Verification via `php -l`, a `preview-digicars.php` WP-stub harness, and Playwright screenshots.

**Reference spec:** `docs/superpowers/specs/2026-06-16-digicars-theme-design.md` (read it before starting).

---

## Verification model (read first)

There is no PHP test runtime in this project. Each code task is verified by:
1. **Lint:** `php -l <file>` → expect `No syntax errors detected`.
2. **Harness render:** load the relevant template through `preview-digicars.php` (Task 1.3) and confirm no PHP warnings/notices in output.
3. **Visual milestone:** at phase ends, screenshot via Playwright (`mcp__playwright__browser_navigate` + `browser_take_screenshot`) and Read the image to confirm layout/brand.
4. **Commit** after each task.

If `php` is not on PATH, install/locate it first; do not skip lint.

---

## Shared contracts (LOCK THESE — every task must match exactly)

**Text domain / slug:** `digicars`  ·  **Folder:** `digicars-theme/`  ·  **Version:** `1.0.0`

**Vehicle attribute schema for Helix (the embedding substrate).** kbeauty's attributes do **not**
transfer; this is a vehicle-specific set. Every `_vehicle_*` meta MUST be registered with
`register_post_meta(..., ['show_in_rest'=>true, 'single'=>true, ...])` so Helix can read it via the
WooCommerce/WP REST API, and the seven core facets are **also** registered as WC global product
attributes (`pa_make`, `pa_model`, `pa_body`, `pa_condition`, `pa_fuel`, `pa_transmission`,
`pa_drivetrain`) for native filtering + REST exposure. Grouped:

- **Identity:** `_vehicle_make`, `_vehicle_model`, `_vehicle_variant`, `_vehicle_year`,
  `_vehicle_body_type`, `_vehicle_condition` (new|demo|used), `_vehicle_stock_no`, `_vehicle_vin` (optional).
- **Pricing & finance:** `_vehicle_price`, `_vehicle_monthly_from`, `_vehicle_availability` (in_stock|in_transit|sold).
- **Powertrain:** `_vehicle_fuel`, `_vehicle_transmission`, `_vehicle_drivetrain`,
  `_vehicle_engine` (capacity/desc), `_vehicle_power_kw`, `_vehicle_fuel_economy` (L/100km or kWh/100km),
  `_vehicle_co2`, `_vehicle_range_km` (EV/hybrid), `_vehicle_battery_kwh` (EV).
- **Condition/history (used/demo):** `_vehicle_mileage`, `_vehicle_previous_owners`,
  `_vehicle_service_history`, `_vehicle_service_plan`, `_vehicle_warranty`.
- **Practicality:** `_vehicle_doors`, `_vehicle_seats`, `_vehicle_boot_litres`,
  `_vehicle_towing_capacity`, `_vehicle_colour`.
- **Safety:** `_vehicle_safety_rating` (NCAP), `_vehicle_features` (structured list: comfort/tech/safety).
- **Location:** `_vehicle_dealer`, `_vehicle_province`.
- **Helix semantic fields (the differentiators — kbeauty had no equivalent):**
  - `_vehicle_lifestyle_tags` — explicit use-case tags (`family, commuter, first-car, off-road,
    fleet, luxury, performance, eco`) that power Concierge chips **and** give Helix strong intent signal.
  - `_vehicle_ai_summary` — a clean 2–4 sentence natural-language description in customer terms
    (who it suits, strengths, finance angle). This is the primary text Helix embeds; keep it factual
    and persona-aware, never marketing fluff. Auto-buildable from the structured fields if absent.

**Taxonomies:** `product_cat` = body type; custom `vehicle_make`, `vehicle_condition`,
`vehicle_fuel`, `vehicle_dealer`.

**PHP helper signatures (single source of truth):**
- `digicars_body_types(): array` — slug => ['label','icon'] for SUV, Hatch, Sedan, Coupe, Convertible, Double Cab, Single Cab Bakkie, MPV, Minibus/Kombi.
- `digicars_makes(): array` — make slug => label.
- `digicars_meta(int $id, string $key): mixed`
- `digicars_monthly_from(float $price, float $deposit=0, int $term=72, float $rate=0.115, float $balloon=0): int`
- `digicars_vehicle_badges(WC_Product $p): array` — ['label','tone'] (New/Demo/Used/EV/Featured).
- `digicars_stars(float $rating): string`
- `digicars_faq_items(): array` — [ ['q'=>..,'a'=>..], ... ] (feeds FAQ page + FAQPage schema).
- `digicars_concierge_chips(): array` — chip slug => ['label','query'] mapping to meta/tax filters.

**AJAX actions (nonce `digicars_nonce`, localized object `digicarsData`):**
- `digicars_concierge_match` → returns `{count:int, ids:int[], cards_html:string}`.
- `digicars_enquiry` → returns `{ok:bool, message:string}`.

**Concierge query contract (the Helix seam — keep stable):**
JS posts `{ chips: string[], text: string, budget_monthly?: int }`; server maps to a
`WP_Query`/`wc_get_products` meta+tax query and returns the shape above. Helix later replaces
only the chips/text → query-args translation; request/response shape is unchanged.

**Disabled WC:** no add-to-cart, cart & checkout pages short-circuit, no sale flash, page title off.
**Forbidden:** custom `do_action` hooks; any AI/Helix branding or plugin name in code or comments.

---

## Phase 0 — Project setup

### Task 0.1: Initialise git so commits work

**Files:** repo root.

- [ ] **Step 1:** Run: `git init && git add -A && git commit -m "chore: snapshot before digicars theme build"`
- [ ] **Step 2:** Create `.gitignore` with `.playwright-mcp/` and `*-output.png` temp paths. Commit: `git add .gitignore && git commit -m "chore: add gitignore"`

### Task 0.2: Scaffold the theme folder tree

**Files:** Create empty `digicars-theme/` subtree per spec §8:
`style.css, functions.php, inc/seo.php, header.php, footer.php, front-page.php, index.php, page.php, 404.php, page-finance.php, page-sell.php, page-service.php, page-dealers.php, page-about.php, page-compare.php, archive-product.php, single-product.php, home.php, single.php, archive.php, searchform.php, woocommerce/content-product.php, css/woocommerce.css, js/main.js, js/concierge.js, js/affordability.js, images/.gitkeep, dummy-products.php`.

- [ ] **Step 1:** Create the directory tree and empty files.
- [ ] **Step 2:** Commit: `git add digicars-theme && git commit -m "chore: scaffold digicars-theme tree"`

---

## Phase 1 — Master prompt artifact

### Task 1.1: Author `MASTER_PROMPT_digicars.md`

**Files:** Create `MASTER_PROMPT_digicars.md` (repo root), structured like `MASTER_PROMPT_glow-kbeauty.md`: §1 Brief, §2 Design system, §3 Personas, §4 Pages & journeys, §5 Technical spec, §6 Voice. Fold in every Shared Contract above verbatim so the master prompt is self-sufficient.

- [ ] **Step 1:** Write the file using spec §1–§9 as source; ensure meta keys, taxonomies, helper signatures, AJAX actions and the Concierge contract appear exactly as locked above.
- [ ] **Step 2:** Self-check: no AI/Helix branding in the "code" rules; no `do_action`; ZAR + SA context present.
- [ ] **Step 3:** Commit: `git add MASTER_PROMPT_digicars.md && git commit -m "docs: add digicars master prompt"`

---

## Phase 2 — Generated imagery

Generate with higgsfield `generate_image` (fallback: replicate). Save final assets into
`digicars-theme/images/`. Keep prompts on-brand: orange (#F4561D) + carbon charcoal, clean,
digital-first, no clichéd stock dealership vibe. After each generation, import/download the file
to the images folder and verify by Reading it.

### Task 2.1: Hero + brand imagery
- [ ] **Step 1:** Generate signature hero image(s): a modern, minimal "digital-first showroom" scene / abstract automotive-tech composition on carbon with orange signal accents. Save `images/hero/hero-showroom.jpg` (+ 1–2 alternates).
- [ ] **Step 2:** Read the image to confirm quality/brand fit. Regenerate if off-brand.
- [ ] **Step 3:** Commit.

### Task 2.2: Body-type icon system
- [ ] **Step 1:** Produce a consistent line-icon set (SVG preferred; generate then trace/clean or author SVG directly) for the 9 body types in §Shared Contracts, plus an EV/charging marker. Save to `images/icons/body-<slug>.svg`.
- [ ] **Step 2:** Confirm uniform stroke weight, 24px grid, `currentColor` stroke so CSS can tint. Commit.

### Task 2.3: Placeholder vehicle renders
- [ ] **Step 1:** Generate ~6 neutral 3/4-view vehicle silhouette/render placeholders (one per body type family) on `--paper` background, plus one `_default` fallback. Save `images/vehicles/<body>-render.jpg` and `images/vehicles/_default.jpg`.
- [ ] **Step 2:** Confirm they read as brand-neutral placeholders (no real OEM logos). Commit.

### Task 2.4: Textures, marks, favicon
- [ ] **Step 1:** Generate/author: a subtle grid-tech background texture, the Digicars wordmark lockup (carbon + orange), `images/logo.svg`, `images/favicon.svg`. Save under `images/`.
- [ ] **Step 2:** Commit.

---

## Phase 3 — Theme foundation

### Task 3.1: `style.css` design system
**Files:** `digicars-theme/style.css`
- [ ] **Step 1:** Theme header block (Theme Name: Digicars, Version 1.0.0, Text Domain: digicars, Author: CloudIA). Then `:root` tokens from spec §3.1, font-face/utility classes, type scale (`.t-hero/.t-1/.t-2`), base reset, layout container (max 1280px, gutter clamp), radii, buttons (solid signal, outline), badges, hairlines, `prefers-reduced-motion` + `:focus-visible` rules.
- [ ] **Step 2:** `php -l` N/A (CSS); validate by harness render in Task 3.4. Commit.

### Task 3.2: `functions.php` core
**Files:** `digicars-theme/functions.php`
- [ ] **Step 1:** Theme supports (title-tag, post-thumbnails, custom-logo, html5, woocommerce + gallery), register menus (primary, footer), image size `digicars-card` 640×420 crop. Enqueue Google Fonts (Archivo Expanded, Hanken Grotesk 400–700, JetBrains Mono), `style.css`, `css/woocommerce.css` (only `if (class_exists('WooCommerce'))`), and the three JS files; `wp_localize_script` → `digicarsData = {ajaxUrl, nonce}`.
- [ ] **Step 2:** Register taxonomies `vehicle_make`, `vehicle_condition`, `vehicle_fuel`, `vehicle_dealer` (public, admin columns); rely on `product_cat` for body type.
- [ ] **Step 2b (Helix REST exposure):** Register **every** `_vehicle_*` meta key from §Shared Contracts via `register_post_meta('product', $key, ['show_in_rest'=>true,'single'=>true,'type'=>...,'auth_callback'=>...])` so Helix can read attributes through the WC/WP REST API. Also register the seven core facets as WC global product attributes (`pa_make`, `pa_model`, `pa_body`, `pa_condition`, `pa_fuel`, `pa_transmission`, `pa_drivetrain`) so they are filterable and REST-visible. Document this block as the Helix integration seam (no plugin name in code).
- [ ] **Step 3:** Implement all helper functions from §Shared Contracts (real bodies, not stubs): `digicars_body_types`, `digicars_makes`, `digicars_meta`, `digicars_monthly_from`, `digicars_vehicle_badges`, `digicars_stars`, `digicars_concierge_chips` (chips map to `_vehicle_lifestyle_tags` + facet queries), and `digicars_build_ai_summary(int $id): string` (composes `_vehicle_ai_summary` from structured fields when empty). (`digicars_faq_items` lives in inc/seo.php — Phase 6.)
- [ ] **Step 4:** Disable WC commerce: remove add-to-cart buttons, force-empty/redirect cart & checkout, remove sale flash, `add_filter('woocommerce_show_page_title','__return_false')`, products-per-page 12, breadcrumb delimiter `›`.
- [ ] **Step 5:** Register AJAX handlers `digicars_concierge_match` and `digicars_enquiry` (both nonce-checked) returning the locked shapes. `require inc/seo.php`.
- [ ] **Step 6:** `php -l digicars-theme/functions.php` → No syntax errors. Commit.

### Task 3.3: `header.php` + `footer.php`
**Files:** `digicars-theme/header.php`, `digicars-theme/footer.php`
- [ ] **Step 1:** Header: skip link, notice bar, sticky header (brand left, nav: Cars in stock / Brands / Finance / Trade-in / Book a service / About; utilities: search, compare count, Concierge launcher), mobile full-screen overlay menu. Footer: carbon bg, brand + link columns (Browse/Services/Company/Contact), trust + payment badges, oversized low-contrast wordmark.
- [ ] **Step 2:** `php -l` both → clean. Commit.

### Task 3.4: `preview-digicars.php` WP-stub harness
**Files:** Create `preview-digicars.php` (repo root) — adapt the existing `preview.php` pattern: stub WP/WC functions so `front-page.php`/templates render to static HTML for screenshotting.
- [ ] **Step 1:** Stub the WP/WC functions the templates call (echo helpers, `get_header`, `wc_get_products`, etc.), wire dummy vehicle data, render front-page to `preview-digicars.html`.
- [ ] **Step 2:** `php -l preview-digicars.php`; run `php preview-digicars.php > preview-digicars.html`; open via Playwright, screenshot, Read image. Commit.

---

## Phase 4 — Catalogue & vehicle pages

### Task 4.1: `woocommerce/content-product.php` (vehicle card)
**Files:** `digicars-theme/woocommerce/content-product.php`
- [ ] **Step 1:** Render card: condition badge (`digicars_vehicle_badges`), image (`digicars-card`, placeholder fallback `images/vehicles/_default.jpg`), Compare toggle, make (mono), price + "From R X pm*" (mono via `digicars_monthly_from`/`_monthly_from`), full title (year+make+model+variant), spec list (mileage | transmission | fuel), "Enquire now" CTA (opens enquiry modal — no add-to-cart).
- [ ] **Step 2:** `php -l` → clean; render a grid in harness; screenshot. Commit.

### Task 4.2: `archive-product.php` (faceted catalogue)
**Files:** `digicars-theme/archive-product.php`
- [ ] **Step 1:** Breadcrumb + page hero + Concierge mount; filter sidebar (condition tabs, make+model, body type, price + monthly toggle, year, mileage, transmission, fuel, province, dealer); toolbar (result count mono, sort select); product grid via `content-product.php`; pagination; mobile filter drawer + scrim; empty state → "Ask the Concierge".
- [ ] **Step 2:** `php -l`; harness render with dummy set; screenshot desktop + 360px. Commit.

### Task 4.3: `single-product.php` (vehicle detail)
**Files:** `digicars-theme/single-product.php`
- [ ] **Step 1:** Gallery, condition badge, make/model/variant, price + "From R X pm", key spec grid (mono telemetry: power_kw, fuel_economy/range_km, transmission, drivetrain, mileage, seats, safety_rating, service_plan/warranty), affordability calculator mount (Phase 5), CTAs (Enquire / Apply for finance / Book a test drive / Add to compare), accordions (`<details>`: Overview [renders `_vehicle_ai_summary`] / Specs / Features / Finance disclosure), `_vehicle_lifestyle_tags` as chips, "Keep looking" 4 similar vehicles (same body type or make).
- [ ] **Step 2:** `php -l`; harness render one vehicle; screenshot. Commit.

---

## Phase 5 — Interactive JS

### Task 5.1: `js/main.js`
**Files:** `digicars-theme/js/main.js`
- [ ] **Step 1:** Header scroll shadow, IntersectionObserver scroll-reveals, mobile overlay menu, filter drawer open/close + scrim, compare add/remove + live count (localStorage), enquiry modal open + AJAX `digicars_enquiry` submit, toast with `aria-live`. Guard everything behind `prefers-reduced-motion`.
- [ ] **Step 2:** Load harness page in Playwright; exercise menu/drawer/compare; check console has no errors. Commit.

### Task 5.2: `js/concierge.js` (signature)
**Files:** `digicars-theme/js/concierge.js`
- [ ] **Step 1:** Render chips from server-provided config, manage selected chips + free text, POST to `digicars_concierge_match`, update live match count (mono) + results stage with returned `cards_html`, 180ms fade/translate, `aria-live` on the stage. Keep the request/response shape exactly per the Concierge contract (Helix seam).
- [ ] **Step 2:** Harness/AJAX smoke: select chips → count + cards update; reduced-motion disables transition. Console clean. Commit.

### Task 5.3: `js/affordability.js`
**Files:** `digicars-theme/js/affordability.js`
- [ ] **Step 1:** Inputs price/deposit/term/rate/balloon → monthly using the same formula as PHP `digicars_monthly_from` (keep parity); live-update the "From R X pm" output; clamp inputs.
- [ ] **Step 2:** Verify a known case (e.g. price 300000, deposit 0, term 72, rate 0.115, balloon 0) matches PHP output. Commit.

---

## Phase 6 — SEO & schema

### Task 6.1: `inc/seo.php`
**Files:** `digicars-theme/inc/seo.php`
- [ ] **Step 1:** Persona doc comments (5 personas, spec §6). `digicars_faq_items()`. `wp_head` pri 1: persona-switched meta description + keywords + OG/twitter per context (front/vehicle/body-archive/finance/about/etc.).
- [ ] **Step 2:** `wp_head` pri 2 JSON-LD: AutoDealer/Organization (areaServed ZA), WebSite+SearchAction (`?s={search_term_string}&post_type=product`), Car/Vehicle (brand, model, modelDate, mileageFromOdometer, fuelType, vehicleTransmission, itemCondition, Offer ZAR), BreadcrumbList on vehicles + archives, FAQPage from `digicars_faq_items()`.
- [ ] **Step 3:** `php -l`; render front + a vehicle in harness, confirm valid JSON-LD blocks (paste into a JSON validator). Commit.

---

## Phase 7 — Dummy data

### Task 7.1: `dummy-products.php` importer
**Files:** `digicars-theme/dummy-products.php`
- [ ] **Step 1:** Define ~22 vehicles across New/Demo/Used, all body types, mixed brands (Chery, Omoda, Jaecoo, VW, Ford, Nissan, Renault, Mahindra, Suzuki, Tata, GWM), realistic ZAR prices + computed `_monthly_from`, and the **full Helix vehicle attribute schema** per §Shared Contracts (powertrain/efficiency/range, history/service-plan/warranty, practicality, safety rating, `_vehicle_lifestyle_tags`, and a written `_vehicle_ai_summary` per vehicle), plus synthetic ratings/reviews. Set `pa_*` global attributes to match. Insert idempotently keyed on `_vehicle_stock_no`; assign body-type `product_cat` + custom taxonomies; attach placeholder render from `images/vehicles/`.
- [ ] **Step 2:** `php -l`. (Real run is `wp eval-file` in a WP install; document it.) Commit.

---

## Phase 8 — Static pages & fallbacks

### Task 8.1: Finance, Sell/Trade-in, Service pages
**Files:** `page-finance.php`, `page-sell.php`, `page-service.php` (each with a Template Name header)
- [ ] **Step 1:** Finance: affordability calculator + application lead form + generic SA finance partners. Sell: instant-offer-style trade-in lead form. Service: booking lead form. All forms post to `digicars_enquiry` (no checkout).
- [ ] **Step 2:** `php -l` each; harness render; screenshot. Commit.

### Task 8.2: Dealers, About, Compare pages
**Files:** `page-dealers.php`, `page-about.php`, `page-compare.php`
- [ ] **Step 1:** Dealers: cards by province/brand. About: digital-first/phygital trust story. Compare: side-by-side spec table from compared vehicle IDs (localStorage).
- [ ] **Step 2:** `php -l` each; harness render; screenshot. Commit.

### Task 8.3: `404.php`, `index.php`, `page.php`
**Files:** `digicars-theme/404.php`, `index.php`, `page.php`
- [ ] **Step 1:** 404: Concierge rescue + search + favourites. index/page: clean editorial fallbacks.
- [ ] **Step 2:** `php -l` each; harness render 404. Commit.

### Task 8.4: Car Torque — native WordPress blog (dynamic)
**Files:** `digicars-theme/home.php` (blog index / Posts page), `digicars-theme/single.php` (single post), `digicars-theme/archive.php` (category/tag/date/author archives), `digicars-theme/searchform.php`; helper `digicars_post_card(WP_Post $p)` added to `functions.php`.

Uses WordPress's **built-in posts, categories and tags** — content is created and managed from the standard WP Admin → Posts editor. No custom post type; the theme just styles the native blog as "Car Torque".

- [ ] **Step 1: `home.php` (blog landing)** — editorial "Car Torque" header + a dynamic grid of the latest posts via the main loop (`have_posts()`), each rendered with `digicars_post_card()` (featured image with `images/vehicles/_default.jpg` fallback, post category as mono eyebrow, title in Archivo Expanded, date + reading-time, excerpt, "Read" link). Native numbered pagination (`the_posts_pagination()`). This template is shown when a static page is assigned as the **Posts page** under Settings → Reading.
- [ ] **Step 2: `single.php`** — single article: breadcrumb, category eyebrow, title, byline + date (mono), featured image, post content (`the_content()`), tag chips, prev/next post nav, and a "Keep reading" block of 3 related posts (same category via `WP_Query`). Sidebar omitted for an editorial single-column read.
- [ ] **Step 3: `archive.php`** — category/tag/date/author listings reusing `digicars_post_card()` in the same grid, with a dynamic archive title (`the_archive_title()`) and pagination.
- [ ] **Step 4: `searchform.php`** — branded search form (used by header search + 404).
- [ ] **Step 5: Seed demo posts** — extend `dummy-products.php` (or add `dummy-posts.php`) to insert ~4 native posts in a "Car Torque" category with titles matching the live site's tone (e.g. model launches, NAMPO/industry news, buying guides), idempotent by slug, each with a generated/placeholder featured image.
- [ ] **Step 6:** Add **BlogPosting/Article** JSON-LD for single posts in `inc/seo.php` (headline, datePublished, author, image, publisher = Organization), plus persona-aware meta description for blog contexts.
- [ ] **Step 7:** `php -l` home.php/single.php/archive.php/searchform.php; harness-render the blog index and one post; screenshot. Commit.

---

## Phase 9 — Homepage assembly & verification

### Task 9.1: `front-page.php`
**Files:** `digicars-theme/front-page.php`
- [ ] **Step 1:** Assemble section order (spec §7): Concierge hero → browse by body type → browse by budget/monthly → latest arrivals + featured → digital-first trust → brands strip → finance/affordability teaser → reviews → Car Torque teasers → contact/Concierge CTA. The **Car Torque** section is dynamic: pull the 3 latest native posts via `WP_Query(['post_type'=>'post','posts_per_page'=>3])` rendered with `digicars_post_card()`, linking to `home.php`; render nothing if no posts exist.
- [ ] **Step 2:** `php -l`; full-page harness render; Playwright full-page screenshot; Read image and check against design system (orange used sparingly, fonts correct, no slop patterns). Iterate until it matches. Commit.

### Task 9.2: Responsive + a11y pass
- [ ] **Step 1:** Screenshot at 360 / 768 / 1280 via Playwright; confirm no horizontal scroll at 360px, drawer/overlay work, focus-visible outlines present, reduced-motion kills transitions.
- [ ] **Step 2:** Fix issues; commit.

### Task 9.3: `screenshot.png` + packaging
**Files:** `digicars-theme/screenshot.png`, `digicars-theme.zip`
- [ ] **Step 1:** Produce 1200×900 `screenshot.png` from the rendered homepage. Zip the theme folder.
- [ ] **Step 2:** Write install steps into `digicars-theme/README.md` (upload+activate, ensure WooCommerce active, `wp eval-file dummy-products.php`, set static front page, create pages + assign templates). Commit.

---

## Self-review checklist (run after building, before sign-off)
- [ ] Every spec §-requirement maps to a task above (Concierge, no-checkout, meta keys, taxonomies, personas, all pages, schema, imagery, dummy data, deliverables).
- [ ] No `do_action` custom hooks; no AI/Helix branding anywhere in code.
- [ ] Helper signatures + meta keys + AJAX shapes identical across functions.php, templates, importer, seo.php, JS.
- [ ] PHP/JS monthly-repayment formula parity holds.
- [ ] All acceptance criteria in spec §8.5 pass.
