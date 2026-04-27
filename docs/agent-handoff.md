# Agent Handoff: ferma-dv.ru hotfix session

Last updated: 2026-04-27

## Context

- Project: WordPress + WooCommerce custom theme.
- Active theme path: `wp-content/themes/theme`.
- Architecture target: business logic should live in `inc/*` and dedicated modules, avoid adding new core behavior in `functions.php` unless unavoidable.

## Production issue set (resolved in this session)

1. Catalog add-to-cart caused page jump/reload instead of AJAX add.
2. Add-to-cart had to require delivery/pickup selection first.
3. Delivery/pickup selection had to affect stock/store context correctly.
4. Checkout mobile inline popup could hide behind footer.
5. Checkout could show false "enter correct address and choose delivery time" error.

## Final behavior expected

- On category/listing pages, add-to-cart works via AJAX (no navigation to `?add-to-cart=...`).
- If delivery/pickup is not selected, delivery modal appears and cart add is blocked.
- After selecting delivery or pickup, modal should not loop and add-to-cart should proceed.
- Pickup options are now aligned to current active locations:
  - `Эгершельд, Верхнепортовая, 41в`
  - `Реми-Сити (ул. Народный пр-т, 20)`
  - `ул. Тимирязева,31 строение 1 (район Спутник)`

## Key files touched

- `wp-content/themes/theme/assets/js/catalog-qty23.js`
- `wp-content/themes/theme/footer-home.php`
- `wp-content/themes/theme/includes/add2cart/ferma_validate_add_cart_item.php`
- `wp-content/themes/theme/inc/catalog/loop-add-to-cart.php`
- `wp-content/themes/theme/inc/checkout/delivery-addressing.php`
- `wp-content/themes/theme/inc/checkout/validation.php`
- `wp-content/themes/theme/inc/frontend/assets.php`
- `wp-content/themes/theme/header-home.php`

## Commit chain from this hotfix session

- `5a714d4` Prevent catalog add-to-cart page jump and deduplicate handlers.
- `fc1ba72` Fix add-to-cart delivery gate, stock filtering IDs, and checkout delivery validation.
- `287638a` Fix add-to-cart jump and enforce delivery gate by cookie state.
- `447b055` Update pickup modal address text for Egersheld location.
- `cd29539` Enforce delivery selection before add-to-cart and force catalog AJAX buttons.
- `c15e897` Align add-to-cart gating with inc architecture and runtime delivery context.
- `e124103` Harden add-to-cart click interception and delivery modal fallback handling.
- `7a4e037` Allow WooCommerce AJAX add-to-cart handler to receive click events.
- `ddd8568` Align catalog add-to-cart click flow with single product Woo AJAX behavior.
- `b3b4558` Normalize loop add-to-cart links to Woo AJAX format before click.
- `9971835` Add robust AJAX fallback for catalog add-to-cart loop buttons.
- `cc668fa` Allow delivery context fallback when mode cookie is missing after selection.
- `498fa3e` Treat billing_delivery as valid delivery context for cart gating.
- `d935290` Handle listing add-to-cart fallback by href pattern, not class only.
- `70d3b86` Stabilize catalog add-to-cart fallback handlers for maintainability.
- `22ce9a8` Fix add-to-cart toast title extraction and cart-anchor positioning fallback.
- `495c9ef` Fix single-product add-to-cart toast title fallback.
- `676b9c2` Prevent footer inline script crash on category pages.
- `92bd8c9` Align archive product header/footer with single-product baseline.
- `18e5272` Prevent header delivery button wrapping on archive/category pages.
- `25d5ea6` Normalize archive header logo spacing and phone link color.
- `ffb6cf1` Apply header fixes on `.header__product` selectors (body-class-independent).
- `73f70d5` Restore WooCommerce archive body classes in `header-home.php`.
- `d8105b7` Force-disable legacy `style.css` on catalog/archive and harden menu link colors.

## Latest update (2026-04-27)

- Changed: `wp-content/themes/theme/assets/js/catalog-qty23.js`
  - Reason: toast title sometimes showed delivery modal text instead of product name; toast anchor did not always lock to header cart icon.
- Commit: `22ce9a8`
- Status: fixed in code, pending user visual confirmation on product page toast content/position.
- Next verification:
  1. Open single product, click `В корзину`, verify toast title is product name.
  2. Verify toast appears next to header cart icon (desktop and mobile header layouts).

## Latest update (2026-04-27, follow-up)

- Changed: `wp-content/themes/theme/assets/js/catalog-qty23.js`
  - Reason: on single-product pages toast title still fell back to generic `Товар` because heading was outside cart container.
- Commit: `495c9ef`
- Status: fixed in code, pending user verification.
- Next verification:
  1. Open any single product page, click `В корзину`.
  2. Confirm toast title equals actual product name (not `Товар`).

## Latest update (2026-04-27, category UI follow-up)

- Changed: `wp-content/themes/theme/footer-home.php`
  - Reason: inline script used `document.querySelector(...).innerHTML` without null checks and could throw on category pages, breaking subsequent frontend init (header/catalog UI looked broken).
- Commit: `676b9c2`
- Status: fixed in code, pending visual confirmation on category pages.
- Next verification:
  1. Open any product category page in incognito.
  2. Confirm header and catalog styles/behavior load normally (no broken layout).

## Latest update (2026-04-27, template alignment follow-up)

- Changed: `wp-content/themes/theme/woocommerce/archive-product.php`
  - Reason: category pages used `get_header('shop')/get_footer('shop')` while single-product baseline used `home` wrappers; mismatch could produce broken header/style composition.
- Commit: `92bd8c9`
- Status: fixed in code, pending user confirmation.
- Next verification:
  1. Open product category page and compare header with single product page.
  2. Confirm menu/search/cart/header blocks render identically to single baseline.

## Latest update (2026-04-27, header alignment fine-tune)

- Changed: `wp-content/themes/theme/inc/frontend/assets.php`
  - Reason: delivery-address button text in category/archive headers wrapped to multiple lines and visually broke the top controls row.
- Commit: `18e5272`
- Status: fixed in code, pending screenshot confirmation.
- Next verification:
  1. Open category page with long pickup text (`Самовывоз: ...`).
  2. Confirm delivery control stays single-line (ellipsis allowed) and header controls stay aligned.

## Latest update (2026-04-27, typography/color follow-up)

- Changed: `wp-content/themes/theme/inc/frontend/assets.php`
  - Reason: category/archive header still differed from single baseline (logo line spacing + blue tel link color).
- Commit: `25d5ea6`
- Status: fixed in code, pending screenshot confirmation.
- Next verification:
  1. Open product category page and compare logo block spacing with single product page.
  2. Confirm phone link color is dark (not blue) and header row remains aligned.

## Latest update (2026-04-27, cached-body-class follow-up)

- Changed: `wp-content/themes/theme/inc/frontend/assets.php`
  - Reason: live category response had body classes without taxonomy markers; previous CSS rules targeting `body.tax-product_cat` did not consistently apply.
- Commit: `ffb6cf1`
- Status: fixed in code, pending user verification.
- Next verification:
  1. Reload category page (`Ctrl+F5`) and compare header to single-product baseline.
  2. Confirm logo subtitle spacing and tel-link color remain corrected even when body lacks taxonomy classes.

## Latest update (2026-04-27, menu/cards parity follow-up)

- Changed: `wp-content/themes/theme/header-home.php`
  - Added forced classes for WooCommerce catalog contexts passed to `body_class()`:
    - `archive`, `tax-product_cat`, `woocommerce-archive`
    - `post-type-archive`, `post-type-archive-product` (for product post-type archive)
  - Purpose: recover style rules that depend on standard Woo body classes for menu links and product-card listing layout.
- Commit: `73f70d5`
- Status: fixed in code, pending user verification on live category pages.

## Latest update (2026-04-27, residual category visual drift)

- Changed: `wp-content/themes/theme/inc/frontend/assets.php`
  - Added final late safeguard (`priority 200000`) to dequeue+deregister `theme-style-single` on catalog/archive pages.
  - Added explicit fallback colors for:
    - `.header__desktop-menu nav ul li a`
    - `.catalog-menu__list li a`
    - `.catalog-menu__new-title`
    under `.header.header__product`.
  - Purpose: eliminate blue-link and mixed-card-layout regressions caused by late legacy stylesheet injection.
- Commit: `d8105b7`
- Status: pushed to `main`; pending live verification with hard refresh.

## Known caution

- `catalog-qty23.js` now contains strong fallback logic to survive inconsistent legacy markup.
- If further cleanup is needed, refactor carefully and only with live smoke checks on:
  - no-address flow
  - delivery selected flow
  - pickup selected flow
  - category add-to-cart on multiple categories

## Quick smoke checklist

1. Incognito: open category page, click `В корзину` without selection -> modal appears.
2. Select pickup, click `В корзину` -> item added via AJAX, no reload.
3. Incognito fresh: select delivery, click `В корзину` -> item added, modal does not loop.
4. Mobile checkout: trigger validation error -> inline notice visible above footer.

## Suggested starter prompt for next chat

`Read docs/agent-handoff.md first, then verify current behavior on category add-to-cart flow (guest incognito). If regression exists, patch only in inc modules / dedicated JS, keep functions.php minimal, and provide commit-by-commit rollback notes.`

## Update protocol

- After every substantial code change, update this handoff file in the same session.
- Always append:
  - what changed (paths + 1-line reason),
  - commit hash(es),
  - current behavior status (fixed/regressed/unknown),
  - next verification step.
- If there are multiple hotfix commits in one session, keep them in chronological order.
- Do not close a production bug-fix session without synchronizing this file.

