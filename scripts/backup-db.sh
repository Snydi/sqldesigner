#!/bin/bash
# Backs up the PostgreSQL database from the running postgres container.
# Usage: ./scripts/backup-db.sh [backup_dir]
# Default backup_dir: /var/www/snydiagram/backups
# Keeps the last 7 daily backups.

set -euo pipefail

BACKUP_DIR="${1:-/var/www/snydiagram/backups}"
TIMESTAMP=$(date +"%Y-%m-%d_%H-%M-%S")
FILENAME="snydiagram_${TIMESTAMP}.sql.gz"
KEEP_DAYS=7
ENV_FILE="/var/www/snydiagram/backend/.env"

# Parse DB vars from .env without sourcing (safe with special chars in values)
get_env() { grep -m1 "^${1}=" "$ENV_FILE" | cut -d= -f2- | tr -d '\r'; }
DB_USER=$(get_env DB_USERNAME)
DB_NAME=$(get_env DB_DATABASE)
DB_PASS=$(get_env DB_PASSWORD)

mkdir -p "$BACKUP_DIR"

echo "[$(date)] Starting backup -> ${BACKUP_DIR}/${FILENAME}"

docker exec -e PGPASSWORD="$DB_PASS" postgres pg_dump \
    -U "$DB_USER" \
    "$DB_NAME" \
    | gzip > "${BACKUP_DIR}/${FILENAME}"

echo "[$(date)] Done: ${FILENAME} ($(du -sh "${BACKUP_DIR}/${FILENAME}" | cut -f1))"

find "$BACKUP_DIR" -name "snydiagram_*.sql.gz" -mtime "+${KEEP_DAYS}" -delete
echo "[$(date)] Pruned backups older than ${KEEP_DAYS} days"
