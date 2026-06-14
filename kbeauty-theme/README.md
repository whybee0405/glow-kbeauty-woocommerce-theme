# Glow K-Beauty — WooCommerce Theme

An editorial WooCommerce theme for a South African K-beauty store, organised
around the 7-step Korean skincare routine. The routine **is** the navigation:
a signature Routine Rail drives wayfinding on the homepage, the shop archives
and the 404 page, and on the homepage it swaps the hero product stage live.

- **Version:** 2.0.0 · **Author:** CloudIA · **Text domain:** `glow-kbeauty`
- **Requires:** WordPress 6.0+, PHP 7.4+, WooCommerce 7.0+

## Installation

1. **Install the theme.** Zip the `kbeauty-theme` folder, then in WP Admin go
   to *Appearance → Themes → Add New → Upload Theme*, upload the zip and
   **Activate**.
2. **Ensure WooCommerce is active**, then import the bundled dummy data
   (20 products, categories, skin types, concerns, reviews):

   ```
   wp eval-file wp-content/themes/kbeauty-theme/dummy-products.php
   ```

   The importer is idempotent — re-running it updates rather than duplicates.
3. **Set the static front page.** Create an empty page called "Home", then
   under *Settings → Reading* choose "A static page" and select it. The theme's
   `front-page.php` takes over from there.
4. **Create the static pages** and assign their templates under
   *Page Attributes → Template*:
   - **About** → template "About"
   - **Contact** → template "Contact"
   - **Help** (slug `help` or `faq`) → template "Help & FAQ"
5. **Optional:** upload the SVGs in `images/products/` to the Media Library and
   attach them as product images. Until you do, product cards and the homepage
   hero automatically fall back to the bundled theme SVGs, so the store looks
   finished out of the box.

Flush permalinks once after setup (*Settings → Permalinks → Save*) so the
`/concern/…` and `/skin-type/…` archives resolve.

## What's inside

| Path | Purpose |
| --- | --- |
| `style.css` | Theme header + the full design system (tokens, base, components) |
| `functions.php` | Setup, enqueues, `glow_routine_steps()`, taxonomies, AJAX, helpers |
| `inc/seo.php` | Persona documentation, meta descriptions, Open Graph, JSON-LD, `glow_faq_items()` |
| `front-page.php` | The homepage journey: hero + stage, rail, concerns, best sellers, sourcing, ingredient index, reviews, newsletter |
| `archive-product.php` | Shop/category/skin-type/concern archives with sidebar filters and mobile drawer |
| `single-product.php` | PDP: step badge, actives/skin-fit chips, sticky buy panel, accordions, "Continue the routine" |
| `woocommerce/content-product.php` | Product card with badges, quick-add and wishlist |
| `css/woocommerce.css` | Shop-only styles, enqueued only when WooCommerce is active |
| `js/main.js` | Hero stage swap, reveals, quick-add AJAX + toast, wishlist, drawers, forms |
| `dummy-products.php` | WP-CLI importer for the full demo catalogue |
| `images/products/*.svg` | 20 bundled product visuals + fallback |

## Design system, in brief

- **Palette** drawn from Korean skincare ingredients: rice `#F3F2ED`, mugwort
  moss `#2E4636`, yuja citron `#F2B63C` (CTAs and highlights only), seafoam
  `#C9DCD2`, clay petal `#E8D5CE`.
- **Type:** Young Serif (display), Schibsted Grotesk (body/UI), Spline Sans
  Mono (step numbers, prices, SKUs, eyebrows).
- **Motion budget:** one signature interaction (the hero stage swap), subtle
  scroll reveals, card hover micro-interactions. `prefers-reduced-motion`
  disables all of it.

## SEO

Every meta description maps to one of five documented customer personas (see
the docblock in `inc/seo.php`). JSON-LD ships for Organization, WebSite (with
SearchAction), Product (brand, offer, aggregateRating), BreadcrumbList and
FAQPage — the FAQ schema is generated from the same `glow_faq_items()`
function that renders the Help page, so the two can never drift.
