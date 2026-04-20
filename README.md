# Ferma DV Custom Code

This repository is the clean deployment source for Ferma DV custom code only.

## What belongs here

- `wp-content/themes/theme/`
- `wp-content/plugins/woocommerce-mysklad-sync/`
- `wp-content/plugins/wms-store/`
- `wp-content/plugins/wms-addon-groop/`
- `yandexeda/`
- `update_green_friday.php`
- `ferma_fasovka_sync_once.php`

## What does not belong here

- WordPress core (`wp-admin/`, `wp-includes/`, root `wp-*.php`)
- third-party plugins that are not maintained in-house
- uploads, caches, backups, logs
- generated integration outputs such as `kuper/*.txt`, `kuper/*.xml`, `green-friday.json`, `llms.txt`

## Deploy model

GitHub Actions uploads only the custom code paths and syncs them into the live WordPress tree on the server. The server keeps WordPress core, vendor plugins, media, caches and generated files outside of git control.

Required GitHub secrets:

- `SERVER_HOST`
- `SERVER_USER`
- `SERVER_PORT`
- `SERVER_SSH_KEY`
- `SERVER_PATH`

## Migration notes

- Review [docs/CUSTOM_SCOPE.md](docs/CUSTOM_SCOPE.md) before the first export.
- Run the export script from the legacy full-site repository to generate this repo skeleton.
- Before the first deploy, run the production reconciliation steps from [docs/SERVER_RECONCILIATION.md](docs/SERVER_RECONCILIATION.md).
