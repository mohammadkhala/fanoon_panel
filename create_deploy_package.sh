#!/bin/bash
# ============================================
# Elite Vape - إنشاء حزمة الرفع الكاملة
# يشمل: الملفات + قاعدة البيانات + صور المنتجات والإعدادات
# ============================================
# لتصدير قاعدة البيانات الحالية (إن وُجدت): 
#   EXPORT_DB=1 ./create_deploy_package.sh
# أو: mysqldump -u root hexacom_test > elitevape_database_export.sql
# ============================================

set -e
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"
DEPLOY_DIR="${SCRIPT_DIR}/elitevape_deploy"

# حذف المجلد السابق إن وجد
rm -rf "$DEPLOY_DIR"
mkdir -p "$DEPLOY_DIR"

# نسخ الملفات (استثناء node_modules, .git - يتضمن vendor للعمل)
echo "نسخ ملفات المشروع..."
rsync -a --exclude='node_modules' \
  --exclude='.git' \
  --exclude='.env' \
  --exclude='.env.backup' \
  --exclude='.env.production' \
  --exclude='elitevape_deploy' \
  --exclude='storage/debugbar' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  --exclude='storage/logs/*' \
  --exclude='.cursor' \
  --exclude='.idea' \
  --exclude='.vscode' \
  --exclude='*.log' \
  . "$DEPLOY_DIR/"

# إنشاء هيكل التخزين مع الصور
echo "إنشاء مجلدات التخزين والصور..."
mkdir -p "$DEPLOY_DIR/storage/app/public/product"
mkdir -p "$DEPLOY_DIR/storage/app/public/category"
mkdir -p "$DEPLOY_DIR/storage/app/public/category/banner"
mkdir -p "$DEPLOY_DIR/storage/app/public/ecommerce"
mkdir -p "$DEPLOY_DIR/storage/app/public/banner"
mkdir -p "$DEPLOY_DIR/storage/app/public/profile"
mkdir -p "$DEPLOY_DIR/storage/app/public/review"
mkdir -p "$DEPLOY_DIR/storage/app/public/notification"
mkdir -p "$DEPLOY_DIR/storage/app/public/conversation"
mkdir -p "$DEPLOY_DIR/storage/app/public/delivery-man"
mkdir -p "$DEPLOY_DIR/storage/app/public/branch"
mkdir -p "$DEPLOY_DIR/storage/app/public/admin"
mkdir -p "$DEPLOY_DIR/storage/app/public/flash-sale"
mkdir -p "$DEPLOY_DIR/storage/app/public/payment_modules/gateway_image"

# استخدام placeholder.png أو img2.jpg كصورة افتراضية للمنتجات والتصنيفات
SRC_PNG="${SCRIPT_DIR}/public/assets/admin/img/placeholder.png"
SRC_JPG="${SCRIPT_DIR}/public/assets/admin/img/160x160/img2.jpg"
[ ! -f "$SRC_PNG" ] && SRC_PNG=""

# نسخ الصور المطلوبة في قاعدة البيانات (صورة placeholder لكل ملف)
for f in "product/2023-11-24-6561bc4d40ec5.png" \
         "category/2023-11-24-6561bc0f3b44f.png" \
         "category/2023-11-24-6561bc0f3c791.png" \
         "category/banner/2023-11-24-6561bc0f3c791.png" \
         "category/def.png" \
         "category/banner/def.png" \
         "ecommerce/2021-06-12-60c493426bd7a.png"; do
  dst="$DEPLOY_DIR/storage/app/public/$f"
  if [[ "$f" == *.png ]] && [ -f "$SRC_PNG" ]; then
    cp "$SRC_PNG" "$dst"
  elif [ -f "$SRC_JPG" ]; then
    cp "$SRC_JPG" "$dst"
  fi
done

# إنشاء ملف .gitignore داخل storage/app/public
echo "*\n!.gitignore" > "$DEPLOY_DIR/storage/app/public/.gitignore"

# نسخ قاعدة البيانات والملفات المساعدة
if [ -f "$SCRIPT_DIR/elitevape_database_import.sql" ]; then
  cp "$SCRIPT_DIR/elitevape_database_import.sql" "$DEPLOY_DIR/"
else
  echo "تحذير: elitevape_database_import.sql غير موجود. شغّل: EXPORT_DB=1 ./create_deploy_package.sh"
fi
cp "$SCRIPT_DIR/.env.hosting.example" "$DEPLOY_DIR/"
cp "$SCRIPT_DIR/DEPLOYMENT_HOSTING.md" "$DEPLOY_DIR/"

# تصدير قاعدة البيانات المحلية إن طُلب (EXPORT_DB=1)
if [ "$EXPORT_DB" = "1" ] && [ -f "$SCRIPT_DIR/.env" ]; then
  DB_NAME=$(grep -E "^DB_DATABASE=" "$SCRIPT_DIR/.env" | cut -d= -f2 | tr -d '"' | tr -d "'")
  DB_USER=$(grep -E "^DB_USERNAME=" "$SCRIPT_DIR/.env" | cut -d= -f2 | tr -d '"' | tr -d "'")
  DB_PASS=$(grep -E "^DB_PASSWORD=" "$SCRIPT_DIR/.env" | cut -d= -f2- | tr -d '"' | tr -d "'")
  DB_NAME="${DB_NAME:-hexacom_test}"
  DB_USER="${DB_USER:-root}"
  if command -v mysqldump >/dev/null 2>&1 && [ -n "$DB_NAME" ]; then
    echo "تصدير قاعدة البيانات: $DB_NAME ..."
    TMP_DB="/tmp/elitevape_db_export_$$.sql"
    if [ -n "$DB_PASS" ]; then
      mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$TMP_DB" 2>/dev/null || true
    else
      mysqldump -u "$DB_USER" "$DB_NAME" > "$TMP_DB" 2>/dev/null || true
    fi
    if [ -s "$TMP_DB" ]; then
      {
        echo "CREATE DATABASE IF NOT EXISTS \`anagmgxt_elitevape\` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
        echo "USE \`anagmgxt_elitevape\`;"
        echo ""
        cat "$TMP_DB"
      } > "$DEPLOY_DIR/elitevape_database_import.sql"
      rm -f "$TMP_DB"
      echo "تم تصدير قاعدة البيانات بنجاح (مع المنتجات والتصنيفات)."
    fi
  fi
fi

# إنشاء public/storage كنسخة من storage/app/public (للرفع بدون شيل)
echo "إنشاء public/storage للوصول المباشر..."
mkdir -p "$DEPLOY_DIR/public/storage"
cp -r "$DEPLOY_DIR/storage/app/public/"* "$DEPLOY_DIR/public/storage/" 2>/dev/null || true

# إنشاء ملف README للرفع
cat > "$DEPLOY_DIR/رفع_الملفات.txt" << 'EOF'
============================================
Elite Vape - تعليمات الرفع اليدوي (بدون شيل)
============================================

1. ارفع محتويات هذا المجلد إلى الاستضافة (public_html أو elitevape)

2. ضبط جذر الموقع (Document Root) ليشير إلى مجلد public

3. استيراد قاعدة البيانات:
   - phpMyAdmin → اختر anagmgxt_elitevape → استيراد → elitevape_database_import.sql

4. إنشاء ملف .env:
   - انسخ .env.hosting.example إلى .env
   - عدّل DB_PASSWORD="56_qc%N7mp[0" و APP_KEY

5. الصور:
   - صور المنتجات والتصنيفات مضمّنة في storage/app/public و public/storage
   - لا حاجة لأمر storage:link — الصور ستظهر مباشرة

6. راجع DEPLOYMENT_HOSTING.md للتفاصيل الكاملة
EOF

echo ""
echo "=== تم إنشاء الحزمة بنجاح ==="
echo "المجلد: $DEPLOY_DIR"
echo "الحجم: $(du -sh "$DEPLOY_DIR" | cut -f1)"
echo ""
echo "لإنشاء أرشيف ZIP:"
echo "  cd $(dirname "$DEPLOY_DIR") && zip -r elitevape_deploy.zip elitevape_deploy"
echo ""
