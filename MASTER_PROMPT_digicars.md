# MASTER_PROMPT — Digicars WooCommerce Theme (v1)

You are a senior WordPress/WooCommerce theme developer and design lead. Build a complete, production-ready WooCommerce theme called **Digicars** from this specification alone. Do not deviate from the design system. Do not add features not specified here. When a decision is not covered, choose the option that best serves the customer journeys in Section 4. This theme is unrelated to any other theme in this repository — it must not look like, borrow tokens from, or share fonts with the kbeauty theme.

---

## 1. PROJECT BRIEF

Build an award-calibre WooCommerce theme for **Digicars Group**, a multi-brand "phygital" automotive marketplace in South Africa. The brand researches, checks affordability, secures finance and enquires online, then fulfils through physical dealerships. Tagline: *"Fueled by passion. Driven by technology."* / *"Digital First Automotive Showroom."* The theme must:

- Look designed by a human studio, not generated. Avoid all AI-design tells: no floating gradient orbs, no centered-stat hero, no glassmorphism, no generic "Why choose us" 3-icon row, no stock dealership clichés, no lorem ipsum.
- Be a **standard, clean WooCommerce theme** with no proprietary plugin hooks and **no custom `do_action` hooks** beyond WordPress/WooCommerce norms. A future integration plugin will plug in later via standard WooCommerce hooks; the theme must not know or care. **No AI branding, integration plugin name, or AI references anywhere in code or comments.**
- Be organised around the **customer journey**, not around what looks pretty. Every section must serve one of the five personas in Section 3.
- Model **vehicles as WooCommerce products** so a future integration can read the catalogue + attributes through the WooCommerce/WP REST API — but **cart and checkout are fully disabled**. Add-to-cart is replaced by **Enquire**, **Check affordability**, **Apply for finance**, and **Add to compare**. There is **no online checkout anywhere**.
- Ship the signature **AI Concierge** as the homepage hero: a conversational, finance-led discovery layer over a rich faceted catalogue. It works out of the box on the local catalogue and is isolated behind one documented AJAX contract so a future integration can power it without the theme depending on it.
- Ship with SEO baked in: persona-driven meta descriptions, Open Graph, and JSON-LD schema (AutoDealer/Organization, WebSite+SearchAction, Car/Vehicle, BreadcrumbList, FAQPage, BlogPosting/Article).
- Work out of the box with bundled dummy data (~22 vehicles + ~4 native blog posts, generated placeholder imagery).

**Locked identity:** Theme name **Digicars** · slug / text domain **`digicars`** · folder **`digicars-theme/`** · version **`1.0.0`** · author **CloudIA**.

**South African context is real, not decorative:** Rand (ZAR) pricing, monthly repayments ("From R X pm*"), SAST hours, province-level dealer info, local finance partners (WesBank, MFC, Absa, Standard Bank).

**Anti-slop guardrails (do not produce any of these):** floating gradient orbs · centered-stat hero · glassmorphism · generic "Why choose us" 3-icon row · stock dealership clichés · lorem ipsum.

---

## 2. DESIGN SYSTEM (non-negotiable)

### 2.1 Concept
**"The showroom that thinks."** Discovery is finance-led and need-led, not checkout-led. A **Concierge-first discovery layer** sits on top of a rich faceted catalogue: the catalogue is the workhorse, the Concierge is the signature interaction. The visual system **elevates** Digicars' orange + charcoal into a premium, digital-first identity.

### 2.2 The signature element: The Concierge
The homepage hero is a conversational match engine, not a static banner.

- A prompt input ("Tell us how you drive") plus guided chips: **Under R5k pm · Family SUV · First car · EV · Bakkie for work · Trade-in welcome**.
- Selecting chips / typing assembles a query → a live **match count** (mono) plus a small stage of matched vehicle cards updates beside it (the signature swap), with a **180ms fade/translate** transition.
- **Works out of the box** on the local catalogue: a deterministic mapping from chips/keywords to vehicle attributes + taxonomies, served by a theme AJAX endpoint.
- **Integration-ready seam:** the natural-language → catalogue-query step is isolated behind one documented AJAX/REST contract over standard WooCommerce product data. A future integration later powers the natural-language → query translation without the theme depending on it. The request/response shape never changes. **No custom `do_action` hooks. No AI branding hardcoded in code or comments.**
- The Concierge also appears on the **404 page** (rescue) and is mountable on the catalogue.

### 2.3 Palette — elevated orange + charcoal (NOT generic corporate orange)
Orange is **signal only** — CTAs, active states, accents, used sparingly. Volt blue is reserved exclusively for AI/data/EV signalling and is used sparingly.

```css
--signal:      #F4561D;  /* Digicars orange — CTAs, active states, accents ONLY, sparingly */
--signal-deep: #D8410D;
--carbon:      #14161A;  /* near-black — primary dark surfaces & text */
--carbon-soft: #20242B;
--slate:       #5A626E;  /* muted text */
--paper:       #F6F6F4;  /* off-white base background */
--paper-deep:  #ECECE8;  /* alternate panels */
--line:        #DEDEDA;  /* hairlines */
--volt:        #2B6FF0;  /* electric blue — AI/data/EV signalling ONLY, used sparingly */
```

### 2.4 Typography (Google Fonts)
- **Display:** **Archivo Expanded** (700/800) — wide, engineered, automotive-strong. Headlines, accordion summaries, footer wordmark.
- **Body/UI:** **Hanken Grotesk** (400/500/600/700).
- **Data/telemetry voice:** **JetBrains Mono** — prices, "From R X pm", spec readouts, stock numbers, result counts, eyebrows, the Concierge readout. This is the "telemetry" voice.
- **Never use** Inter, Poppins, Montserrat, or the kbeauty trio (Young Serif, Schibsted Grotesk, Spline Sans Mono). The two themes must not look related.

Type scale: `.t-hero` clamp(2.6rem→5.2rem), `.t-1` clamp(2rem→3.2rem), `.t-2` clamp(1.4rem→1.9rem), body 16px / 1.6.

### 2.5 Layout & motion rules
- Asymmetric editorial layouts. Radii **4 / 10 / 18px + pill**. Max container **1280px**, gutter clamp(20px→56px).
- Motion budget: **ONE** orchestrated signature interaction (the Concierge hero match), subtle scroll-reveals (`opacity` + 24px translateY via IntersectionObserver), hover micro-interactions on vehicle cards (image scale, compare/enquire affordances). Nothing else.
- No Three.js, no canvas particles, no parallax. Respect `prefers-reduced-motion` globally.
- **Accessibility floor (WCAG 2.1 AA):** skip link, visible `:focus-visible` outlines, aria-labels on icon buttons, `aria-live` on the Concierge stage and toast, semantic headings, no horizontal scroll at 360px.

---

## 3. THE FIVE PERSONAS (drive SEO + page content)

Document these in code comments inside `inc/seo.php`. Every meta description maps to one persona's search intent.

1. **First-Car Buyer** (budget-led)
   - Intent: "cheapest cars under R X", "first car finance South Africa".
   - Served by: the Concierge, browse-by-budget, used inventory.

2. **Family Upgrader** (space / safety / SUV)
   - Intent: "best family SUV South Africa", "7 seater under R500k".
   - Served by: body-type browse, spec grids.

3. **Affordability / Finance Seeker** (monthly-repayment-led)
   - Intent: "cars under R3000 per month", "car finance South Africa".
   - Served by: affordability calculator, finance page, "From R X pm".

4. **Brand/Model Researcher** (specs / compare)
   - Intent: "Chery Tiggo 4 Pro specs", model comparisons.
   - Served by: vehicle detail spec grid, Compare page, Car/Vehicle schema.

5. **Trade-in Upgrader** (sell + buy)
   - Intent: "sell my car instant offer", "trade in value".
   - Served by: Sell / Trade-in page, About trust narrative.

---

## 4. PAGES & CUSTOMER JOURNEYS

### 4.1 Homepage (`front-page.php`) — section order is the journey
1. **Concierge hero** (the signature) — conversational match engine: prompt input + guided chips → live match count + matched-card stage.
2. **Browse by body type** — tiles driven by `digicars_body_types()`, linking to `product_cat` body-type archives.
3. **Browse by budget / monthly** — entry points by price and by monthly repayment band. (Personas 1 & 3.)
4. **Latest arrivals + featured** — fresh stock and featured vehicles via `wc_get_products`.
5. **Digital-first trust** — condition/batch checks, the phygital story (online research + dealership fulfilment). No generic 3-icon row.
6. **Brands strip** — multi-brand OEM lockups from `digicars_makes()`.
7. **Finance / affordability teaser** — "From R X pm" framing + link to the finance page. (Persona 3.)
8. **Reviews** — named-and-located SA customer reviews.
9. **Car Torque** (blog teasers) — pull the 3 latest native posts via `WP_Query(['post_type'=>'post','posts_per_page'=>3])` rendered with `digicars_post_card()`, linking to the blog index (`home.php`); render nothing if no posts exist.
10. **Contact / Concierge CTA** — closing call to discover or get in touch.

### 4.2 Catalogue (`archive-product.php`)
Faceted catalogue: condition tabs (All / New / Used / Demo), make+model, body type, price, monthly repayment, year, mileage, transmission, fuel, province, dealer. Toolbar: result count (mono), sort select, Compare. Product grid via `woocommerce/content-product.php`. Pagination. Mobile filter drawer with scrim. Empty state suggests asking the Concierge. Concierge is mountable here.

### 4.3 Single vehicle (`single-product.php`)
Gallery, condition badge, make/model/variant, price + "From R X pm", key spec grid (mono telemetry: power_kw, fuel_economy/range_km, transmission, drivetrain, mileage, seats, safety_rating, service_plan/warranty), affordability calculator mount, CTAs (**Enquire** / **Apply for finance** / **Book a test drive** / **Add to compare** — never add-to-cart), accordions via `<details>` (Overview [renders `_vehicle_ai_summary`] / Specs / Features / Finance disclosure), `_vehicle_lifestyle_tags` as chips, and "Keep looking" — 4 similar vehicles (same body type or make).

### 4.4 Static pages
- **Finance** (`page-finance.php`, template "Finance") — affordability calculator + finance application lead form; generic SA finance partners (WesBank, MFC, Absa, Standard Bank).
- **Sell / Trade-in** (`page-sell.php`, template "Sell / Trade-in") — instant-offer-style trade-in lead form.
- **Book a Service** (`page-service.php`, template "Book a Service") — service booking lead form.
- **Find a Dealer** (`page-dealers.php`, template "Find a Dealer") — dealer cards by province / brand.
- **About** (`page-about.php`, template "About") — digital-first / phygital trust story. (Persona 5.)
- **Compare** (`page-compare.php`, template "Compare") — side-by-side spec comparison from compared vehicle IDs (localStorage).
- **404** (`404.php`) — Concierge rescue + search + favourites.
- **page.php / index.php** — clean editorial fallbacks.

All lead forms post to the `digicars_enquiry` AJAX action. **No checkout anywhere.**

### 4.5 Car Torque — the native WordPress blog
Car Torque is the brand blog, built on WordPress's **built-in posts, categories and tags** — content is created and managed from the standard WP Admin → Posts editor. **No custom post type;** the theme styles the native blog as "Car Torque".

- **`home.php` (blog landing)** — editorial "Car Torque" header + a dynamic grid of the latest posts via the main loop (`have_posts()`), each rendered with `digicars_post_card()` (featured image with `images/vehicles/_default.jpg` fallback, post category as mono eyebrow, title in Archivo Expanded, date + reading-time, excerpt, "Read" link). Native numbered pagination (`the_posts_pagination()`). Shown when a static page is assigned as the **Posts page** under Settings → Reading.
- **`single.php`** — single article: breadcrumb, category eyebrow, title, byline + date (mono), featured image, post content (`the_content()`), tag chips, prev/next post nav, and a "Keep reading" block of 3 related posts (same category via `WP_Query`). Single-column editorial read, no sidebar.
- **`archive.php`** — category/tag/date/author listings reusing `digicars_post_card()` in the same grid, with a dynamic archive title (`the_archive_title()`) and pagination.
- **`searchform.php`** — branded search form (used by header search + 404).
- **Seed posts** — ~4 native posts in a "Car Torque" category (model launches, NAMPO/industry news, buying guides), idempotent by slug, each with a placeholder featured image.

### 4.6 Header & footer
- **Header:** skip link, notice bar, sticky header with shadow on scroll. Brand left; nav (**Cars in stock / Brands / Finance / Trade-in / Book a service / About**); utilities right (search, compare count, Concierge launcher). Mobile: full-screen overlay menu.
- **Footer:** carbon background, brand + link columns (**Browse / Services / Company / Contact**), payment / trust badges, oversized low-contrast wordmark.

---

## 5. TECHNICAL SPEC

### 5.1 File structure
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
├── home.php / single.php / archive.php / searchform.php   # native "Car Torque" blog
├── woocommerce/content-product.php   # vehicle card
├── css/woocommerce.css       # WC-only styles, enqueued conditionally
├── js/main.js                # header scroll, reveals, mobile menu, filter drawer, compare, enquiry modal, toast
├── js/concierge.js           # Concierge match engine (integration-ready seam)
├── js/affordability.js       # monthly repayment calculator
├── images/                   # generated hero/brand, body-type icons, vehicle renders, textures
├── dummy-products.php        # WP-CLI importer: wp eval-file dummy-products.php (~22 vehicles + posts)
└── screenshot.png            # 1200×900 preview matching the real design
```

### 5.2 functions.php requirements
- **Theme supports:** title-tag, post-thumbnails, custom-logo, HTML5, woocommerce + gallery. Menus: **primary, footer**. Image size **`digicars-card`** (e.g. 640×420 crop).
- **Enqueue:** Google Fonts (Archivo Expanded 700/800; Hanken Grotesk 400–700; JetBrains Mono), `style.css`, `css/woocommerce.css` (only `if (class_exists('WooCommerce'))`), and the three JS files (`js/main.js`, `js/concierge.js`, `js/affordability.js`); `wp_localize_script` → **`digicarsData = {ajaxUrl, nonce}`**.

#### 5.2.1 Vehicle attribute schema (the integration embedding substrate) — LOCKED
This is a vehicle-specific set; kbeauty's attributes (actives, skin types, routine step) do **not** transfer. **Every `_vehicle_*` meta MUST be registered** with `register_post_meta('product', $key, ['show_in_rest'=>true, 'single'=>true, 'type'=>..., 'auth_callback'=>...])` so a future integration can read it via the WooCommerce/WP REST API. The **seven core facets are also** registered as WC global product attributes (`pa_make`, `pa_model`, `pa_body`, `pa_condition`, `pa_fuel`, `pa_transmission`, `pa_drivetrain`) for native filtering + REST exposure. Document this block as the integration seam — **with no plugin name in code.** Grouped:

- **Identity:** `_vehicle_make`, `_vehicle_model`, `_vehicle_variant`, `_vehicle_year`, `_vehicle_body_type`, `_vehicle_condition` (new|demo|used), `_vehicle_stock_no`, `_vehicle_vin` (optional).
- **Pricing & finance:** `_vehicle_price`, `_vehicle_monthly_from`, `_vehicle_availability` (in_stock|in_transit|sold).
- **Powertrain (+ efficiency/range/battery):** `_vehicle_fuel`, `_vehicle_transmission`, `_vehicle_drivetrain`, `_vehicle_engine` (capacity/desc), `_vehicle_power_kw`, `_vehicle_fuel_economy` (L/100km or kWh/100km), `_vehicle_co2`, `_vehicle_range_km` (EV/hybrid), `_vehicle_battery_kwh` (EV).
- **Condition/history (used/demo) (+ service-plan/warranty):** `_vehicle_mileage`, `_vehicle_previous_owners`, `_vehicle_service_history`, `_vehicle_service_plan`, `_vehicle_warranty`.
- **Practicality:** `_vehicle_doors`, `_vehicle_seats`, `_vehicle_boot_litres`, `_vehicle_towing_capacity`, `_vehicle_colour`.
- **Safety:** `_vehicle_safety_rating` (NCAP), `_vehicle_features` (structured list: comfort/tech/safety).
- **Location:** `_vehicle_dealer`, `_vehicle_province`.
- **Semantic fields (the differentiators — kbeauty had no equivalent):**
  - `_vehicle_lifestyle_tags` — explicit use-case tags (`family, commuter, first-car, off-road, fleet, luxury, performance, eco`) that power the Concierge chips **and** give a future integration strong intent signal.
  - `_vehicle_ai_summary` — a clean 2–4 sentence natural-language description in customer terms (who it suits, strengths, finance angle). This is the primary text a future integration embeds; keep it factual and persona-aware, never marketing fluff. Auto-buildable from the structured fields when absent.

#### 5.2.2 Taxonomies — LOCKED
- `product_cat` = **body type** (primary browse): SUV, Hatch, Sedan, Coupe, Convertible, Double Cab, Single Cab Bakkie, MPV, Minibus/Kombi. (Electric is handled by the `vehicle_fuel` taxonomy, not as a body type.)
- Custom public taxonomies (with admin columns): **`vehicle_make`, `vehicle_condition`, `vehicle_fuel`, `vehicle_dealer`** (dealer carries province).

Single-source helper functions feed both UI and schema so they can never drift.

#### 5.2.3 PHP helper signatures (single source of truth) — LOCKED
- `digicars_body_types(): array` — slug => ['label','icon'] for SUV, Hatch, Sedan, Coupe, Convertible, Double Cab, Single Cab Bakkie, MPV, Minibus/Kombi.
- `digicars_makes(): array` — make slug => label.
- `digicars_meta(int $id, string $key): mixed`
- `digicars_monthly_from(float $price, float $deposit=0, int $term=72, float $rate=0.115, float $balloon=0): int`
- `digicars_vehicle_badges(WC_Product $p): array` — ['label','tone'] (New/Demo/Used/EV/Featured).
- `digicars_stars(float $rating): string`
- `digicars_faq_items(): array` — `[ ['q'=>..,'a'=>..], ... ]` (feeds the FAQ page + FAQPage schema). Lives in `inc/seo.php`.
- `digicars_concierge_chips(): array` — chip slug => ['label','query'] mapping to meta/tax filters.
- `digicars_build_ai_summary(int $id): string` — composes `_vehicle_ai_summary` from structured fields when empty.
- `digicars_post_card(WP_Post $p): void` — renders the "Car Torque" blog card (featured image with `images/vehicles/_default.jpg` fallback, category mono eyebrow, Archivo Expanded title, date + reading-time, excerpt, "Read" link).

#### 5.2.4 Disabled WooCommerce commerce — LOCKED
- Remove add-to-cart buttons everywhere.
- Cart & checkout pages short-circuit (force-empty / redirect their endpoints).
- Remove sale flash.
- `add_filter('woocommerce_show_page_title','__return_false')`.
- Products per page **12**.
- Breadcrumb delimiter **›**.

#### 5.2.5 AJAX — LOCKED
Register both handlers nonce-checked (nonce **`digicars_nonce`**, localized object **`digicarsData`**):
- **`digicars_concierge_match`** → returns `{count:int, ids:int[], cards_html:string}`.
- **`digicars_enquiry`** → returns `{ok:bool, message:string}`.

**Concierge query contract (the integration seam — keep stable):** JS posts `{ chips: string[], text: string, budget_monthly?: int }`; the server maps to a `WP_Query` / `wc_get_products` meta+tax query and returns the shape above. A future integration later replaces only the chips/text → query-args translation; the request/response shape is unchanged.

`require` `inc/seo.php` from `functions.php`.

**Forbidden:** any custom `do_action` hooks; any AI / integration plugin name or AI references in code or comments.

### 5.3 SEO spec (inc/seo.php)
- **`wp_head` priority 1:** persona-switched meta description + keywords + OG/twitter per context (front → Persona 1/3; vehicle → make/model/year + key specs; body-type archive → Persona 2; finance → Persona 3; about → Persona 5; blog contexts → persona-aware). OG title/type/url/description/image + twitter `summary_large_image`.
- **`wp_head` priority 2 JSON-LD:**
  - **AutoDealer / Organization** (areaServed ZA).
  - **WebSite + SearchAction** targeting `?s={search_term_string}&post_type=product`.
  - **Car / Vehicle** (brand, model, modelDate, mileageFromOdometer, fuelType, vehicleTransmission, itemCondition, Offer with price/priceCurrency **ZAR**/availability/url).
  - **BreadcrumbList** on vehicles + archives.
  - **FAQPage** built from `digicars_faq_items()`.
  - **BlogPosting / Article** for single posts (headline, datePublished, author, image, publisher = Organization).

### 5.4 Affordability calculator
Inputs: price, deposit, term (months), interest rate, balloon %. Output: monthly "From R X pm". A single JS module (`js/affordability.js`) is used on the vehicle detail page + finance page; the `_vehicle_monthly_from` meta is precomputed for cards/catalogue. The JS formula **must keep parity** with PHP `digicars_monthly_from` (verify a known case: price 300000, deposit 0, term 72, rate 0.115, balloon 0 → identical output).

### 5.5 Dummy data
~22 vehicles across **New / Demo / Used**, all body types, mixed brands (Chery, Omoda, Jaecoo, VW, Ford, Nissan, Renault, Mahindra, Suzuki, Tata, GWM), realistic **ZAR** prices + computed `_vehicle_monthly_from`, and the **full vehicle attribute schema** per §5.2.1 (powertrain/efficiency/range, history/service-plan/warranty, practicality, safety rating, `_vehicle_lifestyle_tags`, and a written `_vehicle_ai_summary` per vehicle), plus synthetic ratings/reviews. Set `pa_*` global attributes to match. Insert **idempotently keyed on `_vehicle_stock_no`**; assign body-type `product_cat` + custom taxonomies; attach placeholder render from `images/vehicles/`. Also seed ~4 native "Car Torque" posts (idempotent by slug). Importer runs via `wp eval-file dummy-products.php`. Generated placeholder vehicle renders are bundled in `images/` so the catalogue and Concierge work before any real photos exist.

### 5.6 Generated imagery
Hero + brand imagery (digital-first showroom, the signature hero stage); a body-type icon system (SUV, Hatch, Sedan, Coupe, Convertible, Double/Single Cab, MPV, Minibus, EV) replacing generic icons; brand-neutral placeholder vehicle renders for the dummy inventory (no real OEM logos); textures / grid-tech motifs, favicon and logo lockups. Keep prompts on-brand: orange (#F4561D) + carbon charcoal, clean, digital-first, no clichéd stock dealership vibe.

### 5.7 Quality bar / acceptance criteria
- [ ] Activating with WooCommerce + imported dummy data produces a complete store, zero placeholder text, **no cart/checkout reachable anywhere**.
- [ ] The Concierge returns correct matches for all guided chips and updates count + stage live; degrades gracefully with reduced-motion.
- [ ] The affordability calculator produces a sane monthly figure from price/deposit/term/balloon, with JS↔PHP parity.
- [ ] Catalogue filters, Compare, sort and pagination all work; mobile (≤900px) uses drawer filters, overlay menu, 2-col grid, no horizontal scroll at 360px.
- [ ] Keyboard-only navigation works end to end.
- [ ] Rich Results valid for Car/Vehicle, BreadcrumbList, FAQPage, WebSite (and BlogPosting on posts).
- [ ] Nothing reads as templated; copy is specific and human; SA context is real (Rand, SAST, province info, local finance partners).
- [ ] Integration seam documented; **no custom `do_action` hooks; no AI branding in code.**

### 5.8 Installation (document in output)
1. Zip the `digicars-theme/` folder → WP Admin → Appearance → Themes → Upload → Activate.
2. Ensure WooCommerce is active. Run `wp eval-file wp-content/themes/digicars-theme/dummy-products.php`.
3. Settings → Reading → set a static front page ("Home") and a **Posts page** ("Car Torque").
4. Create pages Finance, Sell / Trade-in, Book a Service, Find a Dealer, About, Compare and assign their templates.
5. Optionally upload the bundled `images/` assets to Media.

---

## 6. VOICE & COPY RULES

Write all interface copy from the customer's side of the screen. Specific beats clever; clever beats generic. Active voice, sentence case, plain verbs. Buttons say what happens — **"Enquire now", "Check affordability", "Apply for finance"** — never "Submit". Errors and empty states give direction, not apology. The brand voice is confident, technical, helpful — a knowledgeable friend who knows cars and finance. South African context is real, not decorative: Rand pricing, monthly repayments, SAST hours, province-level dealer info, and local finance partners (WesBank, MFC, Absa, Standard Bank).
