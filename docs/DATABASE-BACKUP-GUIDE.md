# دليل النسخ الاحتياطي لقاعدة البيانات — Elite Vape

---

## 1. الطريقة السريعة (سكربت)

```bash
cd elitevapeDB
chmod +x scripts/backup-database.sh
./scripts/backup-database.sh
```

**المخرجات:** ملف في `storage/backups/elitevape_{db_name}_{timestamp}.sql.gz`

---

## 2. الطريقة اليدوية (mysqldump)

```bash
mysqldump -h 127.0.0.1 -u root -p hexacom_test \
  --single-transaction \
  --routines \
  --triggers \
  > backup_$(date +%Y%m%d).sql
```

ثم للضغط:
```bash
gzip backup_20260307.sql
```

---

## 3. من cPanel / phpMyAdmin

1. ادخل إلى **phpMyAdmin**
2. اختر قاعدة البيانات
3. تبويب **Export**
4. اختر **Quick** أو **Custom**
5. صيغة **SQL**
6. **Go** لتحميل الملف

---

## 4. استعادة النسخة الاحتياطية

```bash
# فك الضغط أولاً
gunzip elitevape_hexacom_test_20260307_120000.sql.gz

# استعادة
mysql -h 127.0.0.1 -u root -p hexacom_test < elitevape_hexacom_test_20260307_120000.sql
```

---

## 5. جدول زمني موصى به

| التوقيت | الإجراء |
|---------|---------|
| قبل أي migrate | نسخة احتياطية |
| قبل رفع تحديثات | نسخة احتياطية |
| أسبوعياً | نسخة احتياطية تلقائية (cron) |

---

## 6. Cron للنسخ التلقائي (اختياري)

```bash
# كل يوم الساعة 2 صباحاً
0 2 * * * cd /path/to/elitevapeDB && ./scripts/backup-database.sh
```

---

## 7. ملاحظات

- **storage/backups/** يُفضّل إضافته إلى `.gitignore` لعدم رفع النسخ إلى Git
- احتفظ بنسخة خارج السيرفر (محرك خارجي، سحابة)
- قبل استعادة: اعمل نسخة من القاعدة الحالية أولاً
