#!/bin/bash
# نسخة احتياطية لقاعدة بيانات Elite Vape
# الاستخدام: ./scripts/backup-database.sh

set -e
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
BACKUP_DIR="${PROJECT_DIR}/storage/backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# قراءة إعدادات DB من .env
if [ -f "${PROJECT_DIR}/.env" ]; then
    export $(grep -E '^DB_' "${PROJECT_DIR}/.env" | xargs)
else
    echo "خطأ: ملف .env غير موجود"
    exit 1
fi

# تنظيف القيم
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-hexacom_test}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD:-}"

mkdir -p "$BACKUP_DIR"
BACKUP_FILE="${BACKUP_DIR}/elitevape_${DB_DATABASE}_${TIMESTAMP}.sql"

echo "جاري إنشاء نسخة احتياطية..."
echo "قاعدة البيانات: $DB_DATABASE"
echo "الملف: $BACKUP_FILE"

DUMP_OPTS="--single-transaction --routines --triggers --set-gtid-purged=OFF"

if [ -n "$DB_PASSWORD" ]; then
    mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" \
        $DUMP_OPTS "$DB_DATABASE" > "$BACKUP_FILE"
else
    mysqldump -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" \
        $DUMP_OPTS "$DB_DATABASE" > "$BACKUP_FILE"
fi

# ضغط النسخة
gzip -f "$BACKUP_FILE"
echo "تم: ${BACKUP_FILE}.gz"
