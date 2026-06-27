#!/bin/bash
# ============================================
# Elite Vape - إنشاء حزمة الرفع الكاملة (كل الملفات)
# للاستضافة بعد مسح كل شيء
# ============================================

set -e
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"
DEPLOY_DIR="${SCRIPT_DIR}/elitevape_full_upload"
ZIP_NAME="elitevape_full_upload_$(date +%Y%m%d).zip"

echo "=== إنشاء حزمة الرفع الكاملة ==="

# حذف المجلد السابق
rm -rf "$DEPLOY_DIR"
mkdir -p "$DEPLOY_DIR"

# نسخ الملفات (استثناء الملفات غير المطلوبة)
echo "نسخ ملفات المشروع..."
if command -v rsync &>/dev/null; then
  rsync -a \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='.env' \
    --exclude='.env.backup' \
    --exclude='.env.production' \
    --exclude='elitevape_deploy' \
    --exclude='elitevape_full_upload' \
    --exclude='storage/debugbar' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='storage/logs/*' \
    --exclude='storage/backups' \
    --exclude='.cursor' \
    --exclude='.idea' \
    --exclude='.vscode' \
    --exclude='*.log' \
    --exclude='.DS_Store' \
    . "$DEPLOY_DIR/"
else
  # بديل بدون rsync: استخدام cp
  for dir in app bootstrap config database public resources routes storage tests Modules vendor; do
    [ -d "$dir" ] && cp -r "$dir" "$DEPLOY_DIR/"
  done
  for f in artisan composer.json composer.lock package.json .env.example .env.hosting.example; do
    [ -f "$f" ] && cp "$f" "$DEPLOY_DIR/"
  done
  [ -d docs ] && cp -r docs "$DEPLOY_DIR/"
  # تنظيف storage
  rm -rf "$DEPLOY_DIR/storage/debugbar" "$DEPLOY_DIR/storage/logs"/* "$DEPLOY_DIR/storage/framework/sessions"/* 2>/dev/null || true
fi

# نسخ قاعدة البيانات
if [ -f "elitevape_database_import.sql" ]; then
  cp elitevape_database_import.sql "$DEPLOY_DIR/"
  echo "تم نسخ elitevape_database_import.sql"
elif [ -f "/Users/baitpait/Downloads/database_backup_anagmgxt_elitevape_2026-03-15_14-37-09.sql" ]; then
  cp /Users/baitpait/Downloads/database_backup_anagmgxt_elitevape_2026-03-15_14-37-09.sql "$DEPLOY_DIR/elitevape_database_import.sql"
  echo "تم نسخ نسخة الاحتياطية كـ elitevape_database_import.sql"
else
  echo "تحذير: لم يُعثر على ملف قاعدة البيانات. ارفع elitevape_database_import.sql يدوياً."
fi

# نسخ ملفات التوثيق
cp .env.hosting.example "$DEPLOY_DIR/" 2>/dev/null || true
cp DEPLOYMENT_HOSTING.md "$DEPLOY_DIR/" 2>/dev/null || true

# إنشاء تعليمات الرفع
cat > "$DEPLOY_DIR/تعليمات_الرفع.txt" << 'EOF'
============================================
Elite Vape - رفع كامل على الاستضافة
============================================

1. ارفع كل محتويات هذا المجلد إلى الاستضافة
   (مثلاً: public_html أو المجلد الرئيسي للموقع)

2. ضبط Document Root ليشير إلى مجلد public
   مثال: public_html/elitevape/public

3. استيراد قاعدة البيانات (phpMyAdmin):
   - أنشئ قاعدة بيانات: anagmgxt_elitevape
   - استورد: elitevape_database_import.sql

4. إنشاء ملف .env:
   - انسخ .env.hosting.example إلى .env
   - عدّل: DB_PASSWORD و APP_KEY
   - APP_KEY: php artisan key:generate أو من https://generate-random.org/laravel-key-generator

5. صلاحيات المجلدات:
   chmod -R 775 storage bootstrap/cache

6. رابط التخزين (إن لزم):
   php artisan storage:link
   أو انسخ storage/app/public إلى public/storage

7. مسح الكاش:
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear

راجع DEPLOYMENT_HOSTING.md للتفاصيل
EOF

echo ""
echo "=== تم إنشاء الحزمة ==="
echo "المجلد: $DEPLOY_DIR"
du -sh "$DEPLOY_DIR" 2>/dev/null || echo "تم"

# إنشاء ZIP
echo ""
echo "إنشاء أرشيف ZIP..."
cd "$(dirname "$DEPLOY_DIR")"
zip -r "$ZIP_NAME" "$(basename "$DEPLOY_DIR")" -x "*.DS_Store" -x "*node_modules*" 2>/dev/null && {
  echo "تم: $ZIP_NAME"
  ls -lh "$ZIP_NAME" 2>/dev/null
} || echo "لم يتم إنشاء ZIP (تحقق من وجود zip)"

echo ""
echo "ارفع المجلد أو الملف ZIP إلى الاستضافة"
