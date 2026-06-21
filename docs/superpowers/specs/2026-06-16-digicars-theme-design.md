# Digicars Theme — Design Spec

**Date:** 2026-06-16
**Status:** Approved (brainstorming) — ready for implementation plan
**Author:** CloudIA

A standalone WooCommerce theme for **Digicars Group**, a multi-brand "phygital" automotive
marketplace in South Africa. Mirrors the architectural approach of the existing
`kbeauty-theme/` so the same Helix AI plugin can plug in later via standard WooCommerce hooks.
Built into a new, isolated folder `digicars-theme/` — do **not** mix with the kbeauty file tree.

---

## 1. Business understanding (from digicars.co.za research)

- **Model:** Multi-brand automotive online marketplace, "phygital" — research, check
  affordability, secure finance and enquire online; fulfilment via physical dealerships.
  Tagline: *"Fueled by passion. Driven by technology."* / *"Digital First Automotive Showroom."*
- **Inventory:** New, Demo, and Certified Pre-Owned vehicles across 10+ OEMs (Chery, Ford,
  Jaecoo, Mahindra, Mitsubishi, Nissan, Omoda, Renault, Tata, Volkswagen, GWM, ICAUR, plus
  multi-brand used stock e.g. Suzuki, VW).
- **Funnel (critical):** Browse → check affordability ("From R X pm*") → **Enquire / Apply for
  finance** → dealer. **No online checkout.** Clicking a vehicle opens an enquiry lead modal.
- **Catalogue facts:** ~262 vehicles, 12/page, condition tabs (All/New/Used/Demo); filters for
  make+model, body type, price, monthly repayment, year, mileage, transmission, fuel, province,
  dealer; Compare tool; sort; pagination (`?PageNumber=N`).
- **Existing tech signalling:** robotic assistant "Cherry", AI photo booth, racing simulator,
  bespoke CRM, an existing "Cue" chat widget. The brand is explicitly digital-innovation-first.
- **Current visual:** vivid orange + white + charcoal, clean modern sans-serif, body-type line
  icons. Competent but generic-corporate — this rebuild **elevates** it.

### Scope decisions (locked)
1. **Vehicles only** — mirror Digicars exactly. No spares/parts. No cart/checkout anywhere.
2. **Signature = AI Concierge as hero** — conversational discovery is the centerpiece.
3. **Visual = elevate the orange + charcoal** into a premium digital-first system.
4. **Imagery = full generated set** — hero/brand, body-type icon system, placeholder vehicle
   renders, textures/backgrounds/brand marks.

---

## 2. Concept & positioning

**"The showroom that thinks."** Discovery is finance-led and need-led, not checkout-led. A
**Concierge-first discovery layer** sits on top of a rich faceted catalogue: the catalogue is the
workhorse, the Concierge is the signature interaction.

- Theme name **Digicars** · slug/text-domain `digicars` · folder `digicars-theme/` ·
  version `1.0.0` · author CloudIA.
- **Anti-slop guardrails:** no floating gradient orbs, no centered-stat hero, no glassmorphism,
  no generic "Why choose us" 3-icon row, no stock dealership clichés, no lorem ipsum.

---

## 3. Design system (non-negotiable)

### 3.1 Palette — elevated orange + charcoal
```css
--signal:     #F4561D;  /* Digicars orange — CTAs, active states, accents ONLY, sparingly */
--signal-deep:#D8410D;
--carbon:     #14161A;  /* near-black — primary dark surfaces & text */
--carbon-soft:#20242B;
--slate:      #5A626E;  /* muted text */
--paper:      #F6F6F4;  /* off-white base background */
--paper-deep: #ECECE8;  /* alternate panels */
--line:       #DEDEDA;  /* hairlines */
--volt:       #2B6FF0;  /* electric blue — AI/data/EV signalling ONLY, used sparingly */
```

### 3.2 Typography (Google Fonts)
- **Display:** Archivo Expanded (700/800) — wide, engineered, automotive-strong.
- **Body/UI:** Hanken Grotesk (400/500/600/700).
- **Data/telemetry voice:** JetBrains Mono — prices, "From R X pm", spec readouts, stock
  numbers, result counts, eyebrows, the Concierge readout.
- **Never use** Inter, Poppins, Montserrat, or the kbeauty trio (Young Serif, Schibsted Grotesk,
  Spline Sans Mono) — the two themes must not look related.
- Type scale: `.t-hero` clamp(2.6rem→5.2rem), `.t-1` clamp(2rem→3.2rem),
  `.t-2` clamp(1.4rem→1.9rem), body 16px / 1.6.

### 3.3 Layout & motion
- Asymmetric editorial layouts. Radii 4/10/18px + pill. Max container 1280px,
  gutter clamp(20px→56px).
- Motion budget: ONE orchestrated signature interaction (Concierge hero match), subtle
  scroll-reveals (opacity + 24px translateY via IntersectionObserver), hover micro-interactions
  on vehicle cards (image scale, compare/enquire affordances). Nothing else.
- No Three.js, no canvas particles, no parallax. Respect `prefers-reduced-motion` globally.
- Accessibility floor (WCAG 2.1 AA): skip link, visible `:focus-visible`, aria-labels on icon
  buttons, `aria-live` on the Concierge stage and toast, semantic headings, no horizontal scroll
  at 360px.

---

## 4. The signature: The Concierge

The homepage hero is a conversational match engine, not a static banner.

- Prompt input ("Tell us how you drive") + guided chips:
  *Under R5k pm · Family SUV · First car · EV · Bakkie for work · Trade-in welcome*.
- Selecting chips / typing assembles a query → a live **match count** (mono) + a small stage of
  matched vehicle cards updates beside it (the analogue of kbeauty's hero-stage swap),
  180ms fade/translate.
- **Works out of the box** on the local catalogue: deterministic mapping from chips/keywords to
  vehicle attributes + taxonomies, served by a theme AJAX endpoint.
- **Helix-ready seam:** the natural-language → catalogue-query step is isolated behind one
  documented AJAX/REST contract over standard WC product data. Helix later powers it without the
  theme depending on it. **No custom `do_action` hooks. No AI branding hardcoded.**

The Concierge also appears on the 404 page (rescue) and is mountable on the catalogue.

---

## 5. Vehicle-as-WooCommerce mapping (the Helix substrate)

Each vehicle = a WooCommerce product so Helix can read catalogue + attributes for embeddings —
but **cart/checkout is fully disabled**. Add-to-cart is replaced by: **Enquire**, **Check
affordability**, **Apply for finance**, **Add to compare**.

### 5.1 Vehicle attribute schema (the Helix embedding substrate)
kbeauty's attributes (actives, skin types, routine step) do **not** transfer — vehicles need a
vehicle-specific set. Every `_vehicle_*` meta is registered with `register_post_meta(...,
show_in_rest => true)` so Helix can read it through the WC/WP REST API, and the seven core facets
are also registered as WC global product attributes (`pa_make`, `pa_model`, `pa_body`,
`pa_condition`, `pa_fuel`, `pa_transmission`, `pa_drivetrain`) for native filtering + REST.

- **Identity:** make, model, variant, year, body_type, condition (new/demo/used), stock_no, vin.
- **Pricing & finance:** price, monthly_from, availability (in_stock/in_transit/sold).
- **Powertrain:** fuel, transmission, drivetrain, engine, power_kw, fuel_economy, co2,
  range_km (EV/hybrid), battery_kwh (EV).
- **Condition/history:** mileage, previous_owners, service_history, service_plan, warranty.
- **Practicality:** doors, seats, boot_litres, towing_capacity, colour.
- **Safety:** safety_rating (NCAP), features (structured comfort/tech/safety list).
- **Location:** dealer, province.
- **Helix semantic fields (no kbeauty equivalent):** `_vehicle_lifestyle_tags` (use-case tags —
  family, commuter, first-car, off-road, fleet, luxury, performance, eco — power Concierge chips
  and give Helix intent signal) and `_vehicle_ai_summary` (a clean 2–4 sentence natural-language
  description Helix embeds; auto-composed from structured fields when absent).

Helpers `digicars_meta($id,$key)` and `digicars_build_ai_summary($id)`.

### 5.2 Taxonomies
- `product_cat` = **body type** (primary browse — mirrors kbeauty's routine-step categories):
  SUV, Hatch, Sedan, Coupe, Convertible, Double Cab, Single Cab Bakkie, MPV, Minibus/Kombi.
  (Electric is handled by the `vehicle_fuel` taxonomy, not as a body type.)
- Custom public taxonomies: `vehicle_make`, `vehicle_condition`, `vehicle_fuel`,
  `vehicle_dealer` (with province). Single-source helper functions (e.g. `digicars_body_types()`,
  `digicars_makes()`) feed both UI and schema so they can never drift.

### 5.3 Affordability calculator
Inputs: price, deposit, term (months), interest rate, balloon %. Output: monthly "From R X pm".
Single JS module (`affordability.js`) used on vehicle detail + finance page; the `_monthly_from`
meta is precomputed for cards/catalogue.

---

## 6. Five personas (drive SEO + page content)

Documented in `inc/seo.php`; every meta description maps to one persona's search intent.

1. **First-Car Buyer** (budget-led) — "cheapest cars under R X", "first car finance South Africa".
   Served by Concierge, browse-by-budget, used inventory.
2. **Family Upgrader** (space/safety/SUV) — "best family SUV South Africa", "7 seater under R500k".
   Served by body-type browse, spec grids.
3. **Affordability / Finance Seeker** (monthly-repayment-led) — "cars under R3000 per month",
   "car finance South Africa". Served by affordability calculator, finance page, "From R X pm".
4. **Brand/Model Researcher** (specs/compare) — "Chery Tiggo 4 Pro specs", model comparisons.
   Served by vehicle detail spec grid, Compare page, Vehicle schema.
5. **Trade-in Upgrader** (sell + buy) — "sell my car instant offer", "trade in value".
   Served by Sell/Trade-in page, About trust narrative.

---

## 7. Pages & customer journeys

- **`front-page.php`** — Concierge hero → browse by body type → browse by budget / monthly →
  latest arrivals + featured → digital-first trust (condition/batch checks, phygital) → brands
  strip → finance/affordability teaser → reviews → Car Torque (blog teasers) → Concierge/contact CTA.
- **`archive-product.php`** — faceted catalogue: condition tabs, make+model, body, price, monthly,
  year, mileage, transmission, fuel, province, dealer; result count (mono), sort, Compare, mobile
  filter drawer with scrim, pagination. Empty state suggests asking the Concierge.
- **`single-product.php`** — gallery, condition badge, make/model/variant, price + "From R X pm",
  key spec grid (mono telemetry), affordability calculator, CTAs (Enquire / Apply for finance /
  Book a test drive / Add to compare), accordions (Overview / Specs / Features / Finance
  disclosure), "Keep looking" similar vehicles.
- **`page-finance.php`** (template "Finance") — affordability calculator + finance application
  lead form; generic SA finance partners (WesBank, MFC, Absa, Standard Bank).
- **`page-sell.php`** (template "Sell / Trade-in") — instant-offer-style lead form.
- **`page-service.php`** (template "Book a Service") — service booking lead form.
- **`page-dealers.php`** (template "Find a Dealer") — dealer cards by province/brand.
- **`page-about.php`** (template "About") — digital-first / phygital trust story.
- **`page-compare.php`** (template "Compare") — side-by-side spec comparison.
- **`404.php`** — Concierge rescue + search + favourites.
- **Car Torque** — blog via `index.php` / archive; clean editorial fallbacks in `page.php`.
- **Header:** notice bar, sticky header, brand left, nav (Cars in stock / Brands / Finance /
  Trade-in / Book a service / About), utilities right (search, compare count, Concierge launcher),
  mobile full-screen overlay menu.
- **Footer:** carbon background, brand + link columns (Browse / Services / Company / Contact),
  payment/trust badges, oversized low-contrast wordmark.

---

## 8. Technical structure (mirrors kbeauty)

```
digicars-theme/
├── style.css                 # header + full design system (tokens, base, components)
├── functions.php             # setup, enqueue, vehicle helpers, taxonomies, AJAX, disable cart
├── inc/seo.php               # persona docs, meta, OG, JSON-LD, digicars_faq_items()
├── header.php / footer.php
├── front-page.php / index.php / page.php / 404.php
├── page-finance.php / page-sell.php / page-service.php
├── page-dealers.php / page-about.php / page-compare.php
├── archive-product.php / single-product.php
├── woocommerce/content-product.php   # vehicle card
├── css/woocommerce.css       # WC-only styles, enqueued conditionally
├── js/main.js                # header scroll, reveals, mobile menu, filter drawer, compare, toast
├── js/concierge.js           # Concierge match engine (Helix-ready seam)
├── js/affordability.js       # monthly repayment calculator
├── images/                   # generated hero/brand, body-type icons, vehicle renders, textures
├── dummy-products.php        # WP-CLI importer: ~22 vehicles across conditions/brands/body types
└── screenshot.png            # 1200×900 preview matching the real design
```

### 8.1 functions.php
- Theme supports: title-tag, post-thumbnails, custom-logo, HTML5, woocommerce + gallery.
  Menus: primary, footer. Image size `digicars-card` (e.g. 640×420 crop).
- Enqueue Google Fonts (Archivo Expanded; Hanken Grotesk 400–700; JetBrains Mono), style.css,
  css/woocommerce.css (only if WC active), js with localized `digicarsData` (ajaxUrl, nonce).
- Single-source helpers: `digicars_body_types()`, `digicars_makes()`, `digicars_meta()`,
  `digicars_vehicle_badges()` (New/Demo/Used + EV/featured), `digicars_monthly_from()`.
- Taxonomies registered as in §5.2, with admin columns.
- **Disable cart/checkout:** remove add-to-cart, redirect/disable cart & checkout endpoints,
  `woocommerce_show_page_title` false, remove sale flash, 12 per page, breadcrumb delimiter `›`.
- AJAX `digicars_concierge_match` (nonce-checked) → matched vehicle IDs + count;
  `digicars_enquiry` lead handler. **No custom `do_action` hooks. No AI references in code.**

### 8.2 SEO (inc/seo.php)
- `wp_head` priority 1: persona-switched meta description + keywords (front→Persona 1/3;
  vehicle→make/model/year + key specs; body-type archive→Persona 2; finance→Persona 3;
  about→Persona 5; etc.). OG + twitter summary_large_image.
- `wp_head` priority 2 JSON-LD: **AutoDealer/Organization** (areaServed ZA), **WebSite** +
  SearchAction (`?s={search_term_string}&post_type=product`), **Car/Vehicle** (brand, model,
  modelDate, mileageFromOdometer, fuelType, vehicleTransmission, itemCondition, Offer with
  price/priceCurrency ZAR/availability/url), **BreadcrumbList** on vehicles + archives,
  **FAQPage** built from `digicars_faq_items()`.

### 8.3 Dummy data
~22 vehicles across New/Demo/Used, multiple brands and all body types, realistic SA Rand pricing
and monthly-from, full meta per vehicle, synthetic specs. Importer via
`wp eval-file dummy-products.php` (idempotent by stock number). Generated placeholder vehicle
renders bundled in `images/` so the catalogue and Concierge work before any real photos exist.

### 8.4 Generated imagery (via higgsfield / replicate MCP)
- Hero + brand imagery (digital-first showroom, signature hero stage).
- Body-type icon system (SUV, Hatch, Sedan, Coupe, Convertible, Double/Single Cab, MPV,
  Minibus, EV) replacing generic icons.
- Placeholder vehicle renders for the dummy inventory (neutral, brand-consistent).
- Textures / backgrounds / grid-tech motifs, favicon and logo lockups.

### 8.5 Acceptance criteria
- [ ] Activating with WooCommerce + imported dummy data produces a complete store, zero
      placeholder text, no cart/checkout reachable anywhere.
- [ ] Concierge returns correct matches for all guided chips and updates count + stage live;
      degrades gracefully with reduced-motion.
- [ ] Affordability calculator produces a sane monthly figure from price/deposit/term/balloon.
- [ ] Catalogue filters, Compare, sort and pagination all work; mobile (≤900px) uses drawer
      filters, overlay menu, 2-col grid, no horizontal scroll at 360px.
- [ ] Keyboard-only navigation works end to end.
- [ ] Rich Results valid for Car/Vehicle, BreadcrumbList, FAQPage, WebSite.
- [ ] Nothing reads as templated; copy is specific and human; SA context is real (Rand, SAST,
      province shipping, PayFast/finance partners).
- [ ] Helix integration seam documented; no custom `do_action` hooks; no AI branding in code.

### 8.6 Deliverables
1. `MASTER_PROMPT_digicars.md` — the full build spec (analogous to `MASTER_PROMPT_glow-kbeauty.md`).
2. The built `digicars-theme/` with generated assets and bundled dummy data.

---

## 9. Voice & copy rules
Write from the customer's side. Specific beats clever; clever beats generic. Active voice,
sentence case, plain verbs. Buttons say what happens ("Enquire now", "Check affordability",
"Apply for finance", never "Submit"). Errors and empty states give direction. Brand voice:
confident, technical, helpful — a knowledgeable friend who knows cars and finance. South African
context is real: Rand pricing, monthly repayments, SAST hours, province-level info, local finance
partners.
