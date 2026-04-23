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
- `ferma_fasovka_sync_once.config.sample.php` (template only; real secrets live in an untracked config file, see below)

## `ferma_fasovka_sync_once` config (MoySklad fasovka)

`ferma_fasovka_sync_once.php` is executed from the **WordPress root** (next to `wp-load.php`). It does **not** store credentials in git. It loads `ferma_fasovka_sync_once.config.php` from the **same directory**. That file is in `.gitignore` and must be created on every environment where the script is used.

1. Copy `ferma_fasovka_sync_once.config.sample.php` to `ferma_fasovka_sync_once.config.php` in the same folder as the script.
2. Edit `ferma_fasovka_sync_once.config.php` and set `FERMA_MS_BASIC_LOGIN`, `FERMA_MS_BASIC_PASSWORD`, and if you use bearer auth, `FERMA_MS_BEARER_TOKEN` — the file must `return` an associative array; the sample lists all keys.
3. Do not commit `ferma_fasovka_sync_once.config.php` (it stays only on the machine or server where you need it).

**Local WordPress (developer machine):** repeat steps 1–2 in your local site root if you run the script there (e.g. `https://ferma-dv.test/ferma_fasovka_sync_once.php`). The repo can stay without the real config if you do not run this script locally.

**Production server (recommended):** add repository secret **`FERMA_FASOVKA_SYNC_CONFIG_B64`** — the **base64** encoding of the entire `ferma_fasovka_sync_once.config.php` file (UTF-8 bytes, one line, no spaces). On each deploy, Actions writes the decoded file to `$SERVER_PATH/ferma_fasovka_sync_once.config.php` with mode `0600`. If the secret is empty, that step is skipped (use manual upload instead).

Encode locally, for example:

- **PowerShell:** `[Convert]::ToBase64String([IO.File]::ReadAllBytes("$PWD\ferma_fasovka_sync_once.config.php"))`
- **macOS / Linux:** `base64 -w0 < ferma_fasovka_sync_once.config.php` (GNU `base64`; on macOS without `-w0`, paste the single concatenated line into the secret)

**Production (manual):** alternatively, upload `ferma_fasovka_sync_once.config.php` once to the WordPress root via SFTP/SSH. Until the file exists on the server, the script URL returns HTTP 500.

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

Optional:

- `FERMA_FASOVKA_SYNC_CONFIG_B64` — base64 of `ferma_fasovka_sync_once.config.php` so production gets the file on every deploy without committing it to git.

## Migration notes

- Review [docs/CUSTOM_SCOPE.md](docs/CUSTOM_SCOPE.md) before the first export.
- Run the export script from the legacy full-site repository to generate this repo skeleton.
- Before the first deploy, run the production reconciliation steps from [docs/SERVER_RECONCILIATION.md](docs/SERVER_RECONCILIATION.md).
