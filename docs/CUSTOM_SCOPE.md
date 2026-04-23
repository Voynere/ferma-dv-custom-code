# Custom code scope

## Included by default

These paths are treated as the initial source of truth for the new custom-code repository:

- `wp-content/themes/theme/`
- `wp-content/plugins/woocommerce-mysklad-sync/`
- `wp-content/plugins/wms-store/`
- `wp-content/plugins/wms-addon-groop/`
- `yandexeda/`
- `update_green_friday.php`

## Manual review paths

These files are custom or semi-custom, but should be reviewed before inclusion:

- `moysklad.php`
- `check_bal.php`
- `cjfuns.php`

Rationale:

- `moysklad.php` is custom integration code, but it lives at the project root and should be verified against the active code path before moving into the new repository.
- `check_bal.php` is effectively disabled and should only be carried over if it is still needed operationally.
- `cjfuns.php` is empty and should usually be left behind unless a deployment script expects it.

## Explicitly excluded from the new repository

- WordPress core: `wp-admin/`, `wp-includes/`, root `wp-*.php`
- vendor / marketplace plugins not maintained in-house
- environment-specific files: `wp-config.php`, `.htaccess`
- uploads, caches, backups, logs
- generated artifacts:
  - `wp-content/themes/theme/includes/kuper/*.txt`
  - `wp-content/themes/theme/includes/kuper/*.xml`
  - `wp-content/themes/theme/kilbil.txt`
  - `green-friday.json`
  - `llms.txt`
  - `llms-full.txt`

## Notes for future expansion

If you add new custom plugins later, include the full plugin directory in the repository and append the deploy workflow to sync that directory explicitly.
