#!/bin/bash
# إنشاء أرشيف vendor فقط للرفع على السيرفر
cd "$(dirname "$0")"
echo "إنشاء vendor_only.zip..."
zip -r vendor_only.zip vendor -x '*.DS_Store'
echo "تم. الحجم: $(ls -lh vendor_only.zip | awk '{print $5}')"
echo "ارفع vendor_only.zip إلى السيرفر ثم: unzip -o vendor_only.zip"
