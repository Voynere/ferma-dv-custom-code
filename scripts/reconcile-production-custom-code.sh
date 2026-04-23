#!/usr/bin/env bash
set -euo pipefail

SERVER_PATH="${1:-/var/www/www-root/data/www/ferma-dv.ru/public_html}"
BACKUP_DIR="${2:-/root/ferma-custom-code-backup-$(date +%Y%m%d-%H%M%S)}"

mkdir -p "$BACKUP_DIR/wp-content/themes" "$BACKUP_DIR/wp-content/plugins"

copy_if_exists() {
  local source_path="$1"
  local target_path="$2"

  if [ -e "$source_path" ]; then
    mkdir -p "$(dirname "$target_path")"
    cp -a "$source_path" "$target_path"
  fi
}

copy_if_exists "$SERVER_PATH/wp-content/themes/theme" "$BACKUP_DIR/wp-content/themes/theme"
copy_if_exists "$SERVER_PATH/wp-content/plugins/woocommerce-mysklad-sync" "$BACKUP_DIR/wp-content/plugins/woocommerce-mysklad-sync"
copy_if_exists "$SERVER_PATH/wp-content/plugins/wms-store" "$BACKUP_DIR/wp-content/plugins/wms-store"
copy_if_exists "$SERVER_PATH/wp-content/plugins/wms-addon-groop" "$BACKUP_DIR/wp-content/plugins/wms-addon-groop"
copy_if_exists "$SERVER_PATH/yandexeda" "$BACKUP_DIR/yandexeda"
copy_if_exists "$SERVER_PATH/update_green_friday.php" "$BACKUP_DIR/update_green_friday.php"
copy_if_exists "$SERVER_PATH/moysklad.php" "$BACKUP_DIR/moysklad.php"
copy_if_exists "$SERVER_PATH/check_bal.php" "$BACKUP_DIR/check_bal.php"
copy_if_exists "$SERVER_PATH/cjfuns.php" "$BACKUP_DIR/cjfuns.php"

git -C "$SERVER_PATH" status --short > "$BACKUP_DIR/git-status.txt" || true
git -C "$SERVER_PATH" diff -- \
  wp-content/themes/theme \
  wp-content/plugins/woocommerce-mysklad-sync \
  wp-content/plugins/wms-store \
  wp-content/plugins/wms-addon-groop \
  yandexeda \
  update_green_friday.php \
  > "$BACKUP_DIR/custom-code.patch" || true

echo "Backup written to $BACKUP_DIR"
