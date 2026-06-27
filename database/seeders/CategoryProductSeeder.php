<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductTemplate;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoryProductSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            [
                'name' => 'دروع تكريمية',
                'icon' => '🏆',
                'description' => 'دروع وجوائز تكريمية فاخرة بخامات متعددة',
                'subcategories' => [
                    [
                        'name' => 'دروع خشبية',
                        'icon' => '🪵',
                        'products' => [
                            ['name' => 'درع خشبي كلاسيكي', 'retail' => 45, 'wholesale' => 30, 'badge' => 'الأكثر طلباً',
                             'templates' => ['نموذج كلاسيكي', 'نموذج أنيق', 'نموذج مؤسسي']],
                            ['name' => 'درع خشبي مزخرف', 'retail' => 60, 'wholesale' => 42,
                             'templates' => ['زخرفة عربية', 'زخرفة هندسية', 'زخرفة بسيطة']],
                            ['name' => 'لوح خشبي تكريمي', 'retail' => 55, 'wholesale' => 38, 'badge' => 'جديد',
                             'templates' => ['لوح أفقي', 'لوح عمودي', 'لوح مربع']],
                        ],
                    ],
                    [
                        'name' => 'دروع كريستالية',
                        'icon' => '💎',
                        'products' => [
                            ['name' => 'درع كريستال مقطّع', 'retail' => 95, 'wholesale' => 65, 'badge' => 'مميز',
                             'templates' => ['تقطيع بلوري', 'تقطيع ماسي', 'تقطيع ثلاثي']],
                            ['name' => 'درع كريستال ملوّن', 'retail' => 110, 'wholesale' => 75,
                             'templates' => ['لون أزرق', 'لون ذهبي', 'لون شفاف']],
                            ['name' => 'كأس كريستال', 'retail' => 80, 'wholesale' => 55,
                             'templates' => ['كأس كلاسيك', 'كأس حديث', 'كأس رياضي']],
                        ],
                    ],
                    [
                        'name' => 'دروع زجاجية',
                        'icon' => '🪟',
                        'products' => [
                            ['name' => 'درع زجاج شفاف', 'retail' => 70, 'wholesale' => 48,
                             'templates' => ['مستطيل', 'دائري', 'مخصص']],
                            ['name' => 'درع زجاج ملوّن', 'retail' => 85, 'wholesale' => 58, 'badge' => 'حصري',
                             'templates' => ['أزرق رويال', 'أخضر زمردي', 'ذهبي فاخر']],
                        ],
                    ],
                    [
                        'name' => 'دروع معدنية',
                        'icon' => '⚙️',
                        'products' => [
                            ['name' => 'درع ألومنيوم', 'retail' => 65, 'wholesale' => 45,
                             'templates' => ['مصقول', 'مشطوف', 'ملون']],
                            ['name' => 'لوح نحاسي', 'retail' => 90, 'wholesale' => 62,
                             'templates' => ['نحاس طبيعي', 'نحاس مطلي', 'نحاس مخرم']],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'شوادر ولافتات',
                'icon' => '🎌',
                'description' => 'شوادر ولافتات إعلانية للمعارض والفعاليات',
                'subcategories' => [
                    [
                        'name' => 'رول أب',
                        'icon' => '📜',
                        'products' => [
                            ['name' => 'رول أب 60×160 سم', 'retail' => 55, 'wholesale' => 38, 'badge' => 'الأكثر طلباً',
                             'templates' => ['تصميم احترافي', 'تصميم إبداعي', 'تصميم بسيط']],
                            ['name' => 'رول أب 85×200 سم', 'retail' => 75, 'wholesale' => 52,
                             'templates' => ['كبير عمودي', 'كبير مؤسسي', 'كبير إعلاني']],
                            ['name' => 'رول أب مزدوج الوجه', 'retail' => 110, 'wholesale' => 75, 'badge' => 'مميز',
                             'templates' => ['وجهان متطابقان', 'وجهان مختلفان', 'وجهان تكاملان']],
                        ],
                    ],
                    [
                        'name' => 'شادر قماش',
                        'icon' => '🏳️',
                        'products' => [
                            ['name' => 'شادر قماش كتاني', 'retail' => 120, 'wholesale' => 85,
                             'templates' => ['طباعة كاملة', 'طباعة جزئية', 'طباعة شفافة']],
                            ['name' => 'شادر mesh شبكي', 'retail' => 95, 'wholesale' => 65,
                             'templates' => ['ميش خارجي', 'ميش معرض', 'ميش علامة']],
                        ],
                    ],
                    [
                        'name' => 'X-Banner',
                        'icon' => '✖️',
                        'products' => [
                            ['name' => 'إكس بانر 60×160 سم', 'retail' => 45, 'wholesale' => 30,
                             'templates' => ['تصميم إكس بسيط', 'تصميم إكس إبداعي', 'تصميم إكس مؤسسي']],
                            ['name' => 'إكس بانر صغير 40×120 سم', 'retail' => 35, 'wholesale' => 24,
                             'templates' => ['صغير عرض', 'صغير طاولة', 'صغير معرض']],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'بطاقات وكروت',
                'icon' => '💼',
                'description' => 'كروت أعمال وبطاقات تعريفية ودعوات احترافية',
                'subcategories' => [
                    [
                        'name' => 'كروت أعمال',
                        'icon' => '🪪',
                        'products' => [
                            ['name' => 'كرت بيزنس عادي', 'retail' => 25, 'wholesale' => 15, 'badge' => 'الأكثر طلباً',
                             'templates' => ['كلاسيكي', 'عصري', 'أنيق']],
                            ['name' => 'كرت بيزنس مقوى فاخر', 'retail' => 45, 'wholesale' => 30, 'badge' => 'مميز',
                             'templates' => ['ورق مقوى', 'ورق لؤلؤي', 'ورق ذهبي']],
                            ['name' => 'كرت بيزنس UV لامع', 'retail' => 55, 'wholesale' => 38, 'badge' => 'حصري',
                             'templates' => ['UV كامل', 'UV جزئي', 'UV مخصص']],
                        ],
                    ],
                    [
                        'name' => 'بطاقات دعوة',
                        'icon' => '💌',
                        'products' => [
                            ['name' => 'دعوة رسمية A5', 'retail' => 8, 'wholesale' => 5,
                             'templates' => ['دعوة رسمية كلاسيك', 'دعوة رسمية حديثة', 'دعوة رسمية بسيطة']],
                            ['name' => 'دعوة مؤتمر وفعالية', 'retail' => 12, 'wholesale' => 8,
                             'templates' => ['مؤتمر رسمي', 'فعالية إبداعية', 'ورشة عمل']],
                            ['name' => 'دعوة حفل وعشاء', 'retail' => 10, 'wholesale' => 6,
                             'templates' => ['حفل فاخر', 'عشاء رسمي', 'استقبال']],
                        ],
                    ],
                    [
                        'name' => 'كروت تهنئة',
                        'icon' => '🎉',
                        'products' => [
                            ['name' => 'كرت تهنئة عيد ميلاد', 'retail' => 5, 'wholesale' => 3,
                             'templates' => ['عيد ميلاد ملوّن', 'عيد ميلاد بسيط', 'عيد ميلاد فاخر']],
                            ['name' => 'كرت تهنئة عامة', 'retail' => 5, 'wholesale' => 3,
                             'templates' => ['تهنئة مؤسسية', 'تهنئة شخصية', 'تهنئة دينية']],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'ليبلات وملصقات',
                'icon' => '🏷️',
                'description' => 'ملصقات وليبلات للمنتجات والعلامات التجارية',
                'subcategories' => [
                    [
                        'name' => 'ملصقات دائرية',
                        'icon' => '⭕',
                        'products' => [
                            ['name' => 'ملصق دائري 5 سم', 'retail' => 15, 'wholesale' => 10,
                             'templates' => ['دائري بسيط', 'دائري ملوّن', 'دائري شعار']],
                            ['name' => 'ملصق دائري 10 سم', 'retail' => 22, 'wholesale' => 15,
                             'templates' => ['منتج كبير', 'علبة كبيرة', 'هدية كبيرة']],
                        ],
                    ],
                    [
                        'name' => 'ملصقات مستطيلة',
                        'icon' => '▬',
                        'products' => [
                            ['name' => 'ليبل مستطيل صغير', 'retail' => 12, 'wholesale' => 8,
                             'templates' => ['ليبل منتج', 'ليبل سعر', 'ليبل باركود']],
                            ['name' => 'ليبل مستطيل كبير', 'retail' => 18, 'wholesale' => 12, 'badge' => 'الأكثر طلباً',
                             'templates' => ['ليبل عبوة', 'ليبل مخزن', 'ليبل شحن']],
                        ],
                    ],
                    [
                        'name' => 'ملصقات خاصة',
                        'icon' => '✨',
                        'products' => [
                            ['name' => 'ملصق شفاف', 'retail' => 20, 'wholesale' => 14, 'badge' => 'مميز',
                             'templates' => ['شفاف بسيط', 'شفاف ملوّن', 'شفاف مخصص']],
                            ['name' => 'ملصق هولوغرامي', 'retail' => 30, 'wholesale' => 20, 'badge' => 'حصري',
                             'templates' => ['هولوغرام أمان', 'هولوغرام فضي', 'هولوغرام ذهبي']],
                            ['name' => 'ليبل كرافت ورقي', 'retail' => 10, 'wholesale' => 7,
                             'templates' => ['كرافت طبيعي', 'كرافت مكتوب', 'كرافت مرسوم']],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'مستلزمات الأعراس',
                'icon' => '💒',
                'description' => 'طباعة فاخرة لحفلات الزفاف والمناسبات الخاصة',
                'subcategories' => [
                    [
                        'name' => 'بطاقات الدعوة',
                        'icon' => '💍',
                        'products' => [
                            ['name' => 'دعوة زفاف كلاسيكية', 'retail' => 15, 'wholesale' => 10, 'badge' => 'الأكثر طلباً',
                             'templates' => ['رومانسي كلاسيك', 'أبيض وذهبي', 'ورود طبيعية']],
                            ['name' => 'دعوة زفاف عصرية', 'retail' => 18, 'wholesale' => 12, 'badge' => 'جديد',
                             'templates' => ['مينيمال أبيض', 'بوهيمي', 'تجريدي']],
                            ['name' => 'دعوة زفاف فاخرة', 'retail' => 25, 'wholesale' => 18, 'badge' => 'مميز',
                             'templates' => ['ذهبي فاخر', 'ورق مقوى مزخرف', 'طي إبداعي']],
                        ],
                    ],
                    [
                        'name' => 'ديكور الحفل',
                        'icon' => '🌸',
                        'products' => [
                            ['name' => 'بطاقة رقم الطاولة', 'retail' => 8, 'wholesale' => 5,
                             'templates' => ['رقم كلاسيكي', 'رقم ذهبي', 'رقم ورود']],
                            ['name' => 'بطاقة المنيو', 'retail' => 12, 'wholesale' => 8,
                             'templates' => ['منيو أنيق', 'منيو بسيط', 'منيو ملوّن']],
                            ['name' => 'لافتة الترحيب', 'retail' => 45, 'wholesale' => 32,
                             'templates' => ['ترحيب كلاسيك', 'ترحيب عصري', 'ترحيب ورود']],
                        ],
                    ],
                    [
                        'name' => 'هدايا الأعراس',
                        'icon' => '🎁',
                        'products' => [
                            ['name' => 'بطاقة شكر للضيوف', 'retail' => 6, 'wholesale' => 4,
                             'templates' => ['شكر رسمي', 'شكر بسيط', 'شكر مع صورة']],
                            ['name' => 'غلاف هدية مطبوع', 'retail' => 10, 'wholesale' => 7,
                             'templates' => ['غلاف ذهبي', 'غلاف ورود', 'غلاف مخصص']],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'مطبوعات تسويقية',
                'icon' => '📋',
                'description' => 'فلايرات وبوسترات وبروشورات للتسويق والإعلان',
                'subcategories' => [
                    [
                        'name' => 'فلايرات',
                        'icon' => '📄',
                        'products' => [
                            ['name' => 'فلاير A5', 'retail' => 18, 'wholesale' => 12, 'badge' => 'الأكثر طلباً',
                             'templates' => ['عرض وخصم', 'افتتاح محل', 'خدمة ومنتج']],
                            ['name' => 'فلاير A4', 'retail' => 25, 'wholesale' => 17,
                             'templates' => ['A4 تسويقي', 'A4 حدث', 'A4 مؤسسي']],
                            ['name' => 'فلاير ثلاثي الطي', 'retail' => 35, 'wholesale' => 24, 'badge' => 'مميز',
                             'templates' => ['طي ثلاثي خدمات', 'طي ثلاثي منتجات', 'طي ثلاثي مؤسسي']],
                        ],
                    ],
                    [
                        'name' => 'بوسترات',
                        'icon' => '🖼️',
                        'products' => [
                            ['name' => 'بوستر A3', 'retail' => 30, 'wholesale' => 20,
                             'templates' => ['بوستر إعلاني', 'بوستر حدث', 'بوستر منتج']],
                            ['name' => 'بوستر A2', 'retail' => 50, 'wholesale' => 35,
                             'templates' => ['A2 معرض', 'A2 إعلاني', 'A2 مؤسسي']],
                            ['name' => 'بوستر A1 كبير', 'retail' => 85, 'wholesale' => 58, 'badge' => 'مميز',
                             'templates' => ['A1 واجهة', 'A1 معرض', 'A1 خارجي']],
                        ],
                    ],
                    [
                        'name' => 'بروشورات',
                        'icon' => '📚',
                        'products' => [
                            ['name' => 'بروشور ثنائي الطي', 'retail' => 40, 'wholesale' => 28,
                             'templates' => ['ثنائي خدمات', 'ثنائي منتجات', 'ثنائي مؤسسي']],
                            ['name' => 'بروشور ثلاثي الطي', 'retail' => 50, 'wholesale' => 35, 'badge' => 'الأكثر طلباً',
                             'templates' => ['ثلاثي شركة', 'ثلاثي عروض', 'ثلاثي معلومات']],
                            ['name' => 'كتالوج منتجات', 'retail' => 120, 'wholesale' => 85, 'badge' => 'جديد',
                             'templates' => ['كتالوج A4', 'كتالوج A5', 'كتالوج مربع']],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'قارمات وإعلانات',
                'icon' => '🚩',
                'description' => 'لافتات خارجية وداخلية وعلامات تجارية',
                'subcategories' => [
                    [
                        'name' => 'قارمات تجارية',
                        'icon' => '🏪',
                        'products' => [
                            ['name' => 'قارمة أكريليك', 'retail' => 180, 'wholesale' => 125, 'badge' => 'مميز',
                             'templates' => ['أكريليك مضيء', 'أكريليك مطفي', 'أكريليك مزخرف']],
                            ['name' => 'قارمة PVC', 'retail' => 95, 'wholesale' => 65, 'badge' => 'الأكثر طلباً',
                             'templates' => ['PVC بسيط', 'PVC ملوّن', 'PVC مزخرف']],
                            ['name' => 'قارمة LED مضيئة', 'retail' => 350, 'wholesale' => 240, 'badge' => 'حصري',
                             'templates' => ['LED ناعم', 'LED ساطع', 'LED ملوّن']],
                        ],
                    ],
                    [
                        'name' => 'لافتات',
                        'icon' => '📌',
                        'products' => [
                            ['name' => 'لافتة خارجية ألومنيوم', 'retail' => 140, 'wholesale' => 97,
                             'templates' => ['ألومنيوم مصقول', 'ألومنيوم مطفي', 'ألومنيوم ملوّن']],
                            ['name' => 'لافتة داخلية فوم', 'retail' => 65, 'wholesale' => 45,
                             'templates' => ['فوم بسيط', 'فوم مؤطّر', 'فوم مشكّل']],
                        ],
                    ],
                    [
                        'name' => 'أعلام ورايات',
                        'icon' => '🏴',
                        'products' => [
                            ['name' => 'علم طاولة', 'retail' => 35, 'wholesale' => 24,
                             'templates' => ['علم طاولة قياسي', 'علم طاولة دوار', 'علم طاولة راية']],
                            ['name' => 'علم طريق قماش', 'retail' => 75, 'wholesale' => 52,
                             'templates' => ['علم طريق عمودي', 'علم طريق أفقي', 'علم طريق مزدوج']],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'هدايا مطبوعة',
                'icon' => '🎁',
                'description' => 'هدايا مخصصة بطباعة عالية الجودة',
                'subcategories' => [
                    [
                        'name' => 'أكواب',
                        'icon' => '☕',
                        'products' => [
                            ['name' => 'كوب سيراميك مطبوع', 'retail' => 25, 'wholesale' => 17, 'badge' => 'الأكثر طلباً',
                             'templates' => ['كوب صورة', 'كوب نص', 'كوب تصميم']],
                            ['name' => 'كوب حراري Magic', 'retail' => 35, 'wholesale' => 24, 'badge' => 'جديد',
                             'templates' => ['magic صورة', 'magic نص', 'magic رسم']],
                            ['name' => 'مج زجاجي', 'retail' => 30, 'wholesale' => 21,
                             'templates' => ['زجاجي شفاف', 'زجاجي ملوّن', 'زجاجي مع شعار']],
                        ],
                    ],
                    [
                        'name' => 'قرطاسية مطبوعة',
                        'icon' => '📓',
                        'products' => [
                            ['name' => 'دفتر A5 مطبوع', 'retail' => 30, 'wholesale' => 21,
                             'templates' => ['غلاف صلب', 'غلاف ناعم', 'غلاف جلدي']],
                            ['name' => 'مفكرة سنوية', 'retail' => 45, 'wholesale' => 32, 'badge' => 'جديد',
                             'templates' => ['مفكرة مؤسسية', 'مفكرة شخصية', 'مفكرة إبداعية']],
                        ],
                    ],
                    [
                        'name' => 'إكسسوارات',
                        'icon' => '👜',
                        'products' => [
                            ['name' => 'حقيبة قماش مطبوعة', 'retail' => 28, 'wholesale' => 19,
                             'templates' => ['توتباق بسيط', 'توتباق شعار', 'توتباق تصميم']],
                            ['name' => 'إطار صورة مطبوع', 'retail' => 40, 'wholesale' => 28, 'badge' => 'مميز',
                             'templates' => ['إطار خشبي', 'إطار معدني', 'إطار أكريليك']],
                        ],
                    ],
                ],
            ],
        ];

        $catOrder = 0;
        foreach ($catalog as $catData) {
            $category = Category::create([
                'name' => $catData['name'],
                'slug' => Str::slug($catData['name']) . '-' . ($catOrder + 1),
                'icon' => $catData['icon'],
                'description' => $catData['description'],
                'sort_order' => $catOrder++,
                'is_active' => true,
            ]);

            $subOrder = 0;
            foreach ($catData['subcategories'] as $subData) {
                $subcategory = Subcategory::create([
                    'category_id' => $category->id,
                    'name' => $subData['name'],
                    'slug' => Str::slug($subData['name']) . '-' . $category->id . '-' . ($subOrder + 1),
                    'icon' => $subData['icon'],
                    'sort_order' => $subOrder++,
                    'is_active' => true,
                ]);

                $prodOrder = 0;
                foreach ($subData['products'] as $prodData) {
                    $product = Product::create([
                        'subcategory_id' => $subcategory->id,
                        'name' => $prodData['name'],
                        'slug' => Str::slug($prodData['name']) . '-' . $subcategory->id . '-' . ($prodOrder + 1),
                        'retail_price' => $prodData['retail'],
                        'wholesale_price' => $prodData['wholesale'],
                        'badge' => $prodData['badge'] ?? null,
                        'sort_order' => $prodOrder++,
                        'is_active' => true,
                    ]);

                    $tmplOrder = 0;
                    foreach ($prodData['templates'] as $tmplName) {
                        ProductTemplate::create([
                            'product_id' => $product->id,
                            'name' => $tmplName,
                            'sort_order' => $tmplOrder++,
                            'is_active' => true,
                        ]);
                    }
                }
            }
        }
    }
}
