<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * توليد صور placeholder ملوّنة لكل المنتجات الحالية
 * وتحديث حقل image ليصير ["filename.jpg"]
 */
class ProductImagesSeeder extends Seeder
{
    // لون خلفية + لون نص لكل تصنيف رئيسي
    private array $categoryColors = [
        1  => ['bg' => [45, 35, 20],   'text' => [240, 220, 180], 'label' => 'دروع تكريمية'],
        2  => ['bg' => [20, 40, 55],   'text' => [100, 200, 240], 'label' => 'لافتات'],
        3  => ['bg' => [35, 20, 45],   'text' => [200, 160, 240], 'label' => 'بطاقات'],
        4  => ['bg' => [45, 15, 15],   'text' => [240, 120, 100], 'label' => 'ملصقات'],
        5  => ['bg' => [45, 35, 40],   'text' => [240, 180, 210], 'label' => 'أعراس'],
        6  => ['bg' => [15, 40, 30],   'text' => [100, 220, 160], 'label' => 'مطبوعات'],
        7  => ['bg' => [10, 25, 45],   'text' => [80,  160, 240], 'label' => 'قارمات'],
        8  => ['bg' => [40, 30, 10],   'text' => [240, 200, 80],  'label' => 'هدايا'],
    ];

    // ألوان افتراضية للتصنيفات الفرعية
    private array $defaultColor = ['bg' => [30, 30, 40], 'text' => [180, 200, 220], 'label' => 'منتج'];

    public function run(): void
    {
        // إنشاء المجلد
        Storage::disk('public')->makeDirectory('product');
        $dir = storage_path('app/public/product');

        $products = DB::table('products')->get();

        foreach ($products as $product) {
            // تحديد التصنيف الرئيسي
            $catIds = json_decode($product->category_ids, true) ?? [];
            $rootCatId = null;
            foreach ($catIds as $cat) {
                $id = (int)($cat['id'] ?? $cat);
                // ابحث عن التصنيف الرئيسي (parent_id = 0)
                $catRow = DB::table('categories')->where('id', $id)->first();
                if ($catRow && (int)$catRow->parent_id === 0) {
                    $rootCatId = $id;
                    break;
                }
            }
            // إذا لم نجد تصنيف رئيسي، خذ parent من الفرعي
            if (!$rootCatId && !empty($catIds)) {
                $firstId = (int)(reset($catIds)['id'] ?? reset($catIds));
                $catRow  = DB::table('categories')->where('id', $firstId)->first();
                if ($catRow && (int)$catRow->parent_id > 0) {
                    $parent = DB::table('categories')->where('id', $catRow->parent_id)->first();
                    $rootCatId = $parent ? (int)$parent->id : null;
                }
            }

            $colors = $this->categoryColors[$rootCatId] ?? $this->defaultColor;
            $filename = 'product_' . $product->id . '.jpg';
            $filepath = $dir . '/' . $filename;

            // ولّد الصورة
            $this->generateProductImage(
                $filepath,
                $product->name,
                number_format($product->price, 0) . ' ₪',
                $colors['bg'],
                $colors['text']
            );

            // حدّث حقل image
            DB::table('products')
                ->where('id', $product->id)
                ->update(['image' => json_encode([$filename])]);
        }

        $count = $products->count();
        $this->command->info("✅ تم توليد وتحديث صور $count منتج في storage/app/public/product/");
    }

    /**
     * توليد صورة JPG بسيطة بـ GD
     * 400×400 px — خلفية داكنة + اسم المنتج + السعر
     */
    private function generateProductImage(
        string $path,
        string $productName,
        string $price,
        array $bgColor,
        array $textColor
    ): void {
        $w = 400; $h = 400;
        $img = imagecreatetruecolor($w, $h);

        // ألوان
        [$br, $bg, $bb] = $bgColor;
        [$tr, $tg, $tb] = $textColor;
        $bg_c    = imagecolorallocate($img, $br, $bg, $bb);
        $text_c  = imagecolorallocate($img, $tr, $tg, $tb);
        $accent  = imagecolorallocate($img, min(255,$tr+40), min(255,$tg+40), min(255,$tb+40));
        $dim_c   = imagecolorallocate($img, min(255,$br+25), min(255,$bg+25), min(255,$bb+25));
        $border_c= imagecolorallocate($img, min(255,$br+45), min(255,$bg+45), min(255,$bb+45));

        // خلفية
        imagefilledrectangle($img, 0, 0, $w-1, $h-1, $bg_c);

        // إطار داخلي
        imagerectangle($img, 12, 12, $w-13, $h-13, $border_c);
        imagerectangle($img, 14, 14, $w-15, $h-15, $dim_c);

        // شريط لوني علوي
        imagefilledrectangle($img, 12, 12, $w-13, 70, $dim_c);

        // مستطيل السعر
        imagefilledroundedRect($img, $w/2 - 60, $h - 80, $w/2 + 60, $h - 44, 8, $accent);

        // أيقونة مطبعة (مربع بسيط كـ placeholder)
        imagefilledrectangle($img, $w/2-40, 90, $w/2+40, 200, $dim_c);
        imagerectangle($img, $w/2-40, 90, $w/2+40, 200, $border_c);
        // خطوط أفقية تمثل نص مطبوع
        for ($y = 110; $y < 190; $y += 18) {
            imageline($img, $w/2-28, $y, $w/2+28, $y, $border_c);
        }

        // كتابة اسم المنتج (نبسطّه لأن imagettftext غير متوفر دائماً)
        $this->drawTextWrapped($img, $productName, $text_c, $w, 220, 5);

        // السعر
        $this->drawTextWrapped($img, $price, $text_c, $w, $h - 70, 3);

        imagejpeg($img, $path, 88);
        imagedestroy($img);
    }

    /** رسم نص بسيط مع wrap محدود */
    private function drawTextWrapped($img, string $text, $color, int $w, int $startY, int $fontSize = 4): void
    {
        $charW   = imagefontwidth($fontSize);
        $maxChars= (int)(($w - 60) / $charW);
        // قسّم النص
        $lines = [];
        $words = explode(' ', $text);
        $line  = '';
        foreach ($words as $word) {
            if (strlen($line . ' ' . $word) > $maxChars && $line !== '') {
                $lines[] = $line;
                $line = $word;
            } else {
                $line = $line === '' ? $word : "$line $word";
            }
        }
        if ($line !== '') $lines[] = $line;

        $lineH = imagefontheight($fontSize) + 4;
        $totalH = count($lines) * $lineH;
        $y = $startY - $totalH / 2;

        foreach ($lines as $l) {
            $x = (int)(($w - strlen($l) * $charW) / 2);
            imagestring($img, $fontSize, $x, (int)$y, $l, $color);
            $y += $lineH;
        }
    }
}

// Helper function خارج الـ class لأن GD لا تدعمه built-in
function imagefilledroundedRect($img, $x1, $y1, $x2, $y2, $r, $color): void
{
    imagefilledrectangle($img, $x1+$r, $y1, $x2-$r, $y2, $color);
    imagefilledrectangle($img, $x1, $y1+$r, $x2, $y2-$r, $color);
    imagefilledellipse($img, $x1+$r, $y1+$r, $r*2, $r*2, $color);
    imagefilledellipse($img, $x2-$r, $y1+$r, $r*2, $r*2, $color);
    imagefilledellipse($img, $x1+$r, $y2-$r, $r*2, $r*2, $color);
    imagefilledellipse($img, $x2-$r, $y2-$r, $r*2, $r*2, $color);
}
