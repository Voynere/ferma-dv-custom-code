# Server reconciliation before first deploy

Before the first deploy from the new custom-code repository, capture the current production custom code and compare it against the new repository output.

## 1. Back up the current production custom code

Run on the server:

```bash
SERVER_PATH="/var/www/www-root/data/www/ferma-dv.ru/public_html"
BACKUP_DIR="/root/ferma-custom-code-backup-$(date +%Y%m%d-%H%M%S)"

mkdir -p "$BACKUP_DIR/wp-content/themes" "$BACKUP_DIR/wp-content/plugins"
cp -a "$SERVER_PATH/wp-content/themes/theme" "$BACKUP_DIR/wp-content/themes/theme"
cp -a "$SERVER_PATH/wp-content/plugins/woocommerce-mysklad-sync" "$BACKUP_DIR/wp-content/plugins/woocommerce-mysklad-sync"
cp -a "$SERVER_PATH/wp-content/plugins/wms-store" "$BACKUP_DIR/wp-content/plugins/wms-store"
cp -a "$SERVER_PATH/wp-content/plugins/wms-addon-groop" "$BACKUP_DIR/wp-content/plugins/wms-addon-groop"

if [ -d "$SERVER_PATH/yandexeda" ]; then
  cp -a "$SERVER_PATH/yandexeda" "$BACKUP_DIR/yandexeda"
fi

for file in update_green_friday.php moysklad.php check_bal.php cjfuns.php; do
  if [ -f "$SERVER_PATH/$file" ]; then
    cp -a "$SERVER_PATH/$file" "$BACKUP_DIR/$file"
  fi
done
```

## 2. Capture git state from the legacy full-site clone

If the production site is still a git clone:

```bash
git -C "$SERVER_PATH" status --short > "$BACKUP_DIR/git-status.txt" || true
git -C "$SERVER_PATH" diff -- \
  wp-content/themes/theme \
  wp-content/plugins/woocommerce-mysklad-sync \
  wp-content/plugins/wms-store \
  wp-content/plugins/wms-addon-groop \
  yandexeda \
  update_green_friday.php \
  > "$BACKUP_DIR/custom-code.patch" || true
```

## 3. Compare production backup with the new repository

On your local machine, compare the backup with the exported custom-code repository before the first deploy. Any production-only fixes should be merged into the new repository first.

## 4. First deploy rule

Only run the first production deploy when:

- the backup is complete
- production-only changes have been reviewed
- generated files have been excluded from the new repository
- the custom-code repository passes a final manual diff check
