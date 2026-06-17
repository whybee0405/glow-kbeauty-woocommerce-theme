# Digicars — WooCommerce theme

A digital-first WooCommerce theme for **Digi Cars Group**, a multi-brand "phygital" South
African automotive marketplace. Vehicles are WooCommerce products, but **cart and checkout are
disabled** — the funnel is enquiry + finance, not online purchase. The signature feature is the
**Concierge**, a guided discovery experience that runs on the local catalogue and is built to be
powered later by an external AI integration through the standard WooCommerce/WordPress REST API.

- **Theme slug / text domain:** `digicars`
- **Version:** 1.0.0
- **Requires:** WordPress 6.0+, PHP 7.4+, WooCommerce 8.0+

---

## What's included

- **Concierge homepage** — conversational, chip-driven discovery over the vehicle catalogue.
- **Faceted catalogue** (`archive-product.php`) — condition tabs, make/model, body type, price &
  monthly-repayment, year, mileage, transmission, fuel, province, dealer; compare; mobile drawer.
- **Vehicle detail** (`single-product.php`) — spec grid, affordability calculator, enquire /
  finance / test-drive CTAs, accordions, AI summary, similar vehicles.
- **Lead pages** — Finance, Sell / Trade-in, Book a Service, Find a Dealer, About, Compare.
- **Car Torque** — native WordPress blog (`home.php` / `single.php` / `archive.php`).
- **SEO** — persona-driven meta + JSON-LD (AutoDealer, WebSite + SearchAction, Car/Vehicle,
  BreadcrumbList, FAQPage, BlogPosting).
- **Bundled data** — 23 demo vehicles and 4 demo posts with full attributes and SVG imagery.

## Design system

- **Palette:** signal orange `#F4561D`, carbon `#14161A`, off-white `#F6F6F4`, electric blue
  `#2B6FF0` (AI/EV accents only).
- **Type:** Archivo Expanded (display), Hanken Grotesk (body), JetBrains Mono (data/telemetry).
- Motion respects `prefers-reduced-motion`; WCAG 2.1 AA targets throughout.

---

## Installation

1. **Install the theme.** Zip the `digicars-theme` folder, then in WP Admin →
   *Appearance → Themes → Add New → Upload Theme* → choose the zip → *Activate*.
2. **Activate WooCommerce.** The theme requires WooCommerce to be installed and active.
3. **Import demo vehicles** (optional but recommended) via WP-CLI:
   ```
   wp eval-file wp-content/themes/digicars-theme/dummy-products.php
   ```
   Idempotent — safe to re-run (keyed on vehicle stock number).
4. **Import demo blog posts** (optional):
   ```
   wp eval-file wp-content/themes/digicars-theme/dummy-posts.php
   ```
5. **Set the homepage.** Create a page named "Home", then *Settings → Reading → Your homepage
   displays → A static page → Homepage = Home*. (`front-page.php` renders automatically.)
6. **Create the Posts page for the blog.** Create a page named "Car Torque" and set it as
   *Settings → Reading → Posts page* so `home.php` styles the blog.
7. **Create the lead/utility pages** and assign their templates (Page → Page Attributes →
   Template):
   - "Finance" → **Finance**
   - "Sell your car" → **Sell / Trade-in**
   - "Book a Service" → **Book a Service**
   - "Find a Dealer" → **Find a Dealer**
   - "About" → **About**
   - "Compare" → **Compare**
8. **Set menus.** *Appearance → Menus*: build the Primary and Footer menus (the theme ships sane
   fallbacks if none are assigned).
9. **Permalinks.** Visit *Settings → Permalinks* and save once to flush rewrite rules for the
   custom taxonomies.

### Replacing the placeholder imagery

The theme ships with crisp SVG placeholders (body-type icons, vehicle silhouette, hero
composition). Replace real photography by setting product featured images and dropping a hero
image at `images/hero/` — no code changes needed; the templates prefer real images and fall back
to the SVGs.

---

## Integration seam (for a future AI layer)

The theme is built so an external AI plugin can power the Concierge without the theme depending on
it:

- Every vehicle attribute is stored as `_vehicle_*` post meta **registered with
  `show_in_rest => true`**, and the seven core facets are also registered as WooCommerce global
  product attributes (`pa_make`, `pa_model`, `pa_body`, `pa_condition`, `pa_fuel`,
  `pa_transmission`, `pa_drivetrain`) — so the full catalogue is readable over the WC/WP REST API.
- Each vehicle carries a natural-language `_vehicle_ai_summary` and `_vehicle_lifestyle_tags` for
  high-quality embeddings.
- The Concierge talks to a single, stable AJAX endpoint (`digicars_concierge_match`) with a fixed
  request/response shape. An integration can replace only the "natural language → catalogue query"
  step behind that contract.
- The theme defines **no custom action hooks** and contains **no third-party product branding**.

---

## Development notes

- `preview-digicars.php` (repo root, not shipped) is a WP-stub harness that renders templates to
  static HTML for visual checks: `php preview-digicars.php front-page.php`.
- All PHP passes `php -l`; all JS passes `node --check`.
