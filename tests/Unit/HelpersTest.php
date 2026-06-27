<?php

namespace Tests\Unit;

use App\CentralLogics\Helpers;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    public function test_combinations_generates_cartesian_product()
    {
        $input = [
            'size' => ['S', 'M'],
            'color' => ['Red', 'Blue'],
        ];

        $result = Helpers::combinations($input);

        $this->assertCount(4, $result);
        $this->assertContains(['size' => 'S', 'color' => 'Red'], $result);
        $this->assertContains(['size' => 'S', 'color' => 'Blue'], $result);
        $this->assertContains(['size' => 'M', 'color' => 'Red'], $result);
        $this->assertContains(['size' => 'M', 'color' => 'Blue'], $result);
    }

    public function test_variation_price_returns_base_price_when_no_variation()
    {
        $product = [
            'price' => 100,
            'variations' => json_encode([
                ['type' => 'size-S', 'price' => 110, 'stock' => 10],
                ['type' => 'size-M', 'price' => 120, 'stock' => 5],
            ]),
        ];

        $price = Helpers::variation_price($product, json_encode([]));
        $this->assertSame(100, $price);
    }

    public function test_variation_price_returns_matched_variation_price()
    {
        $product = [
            'price' => 100,
            'variations' => json_encode([
                ['type' => 'size-S', 'price' => 110, 'stock' => 10],
                ['type' => 'size-M', 'price' => 120, 'stock' => 5],
            ]),
        ];

        $variation = json_encode([['type' => 'size-M']]);
        $price = Helpers::variation_price($product, $variation);

        $this->assertSame(120, $price);
    }

    public function test_product_data_formatting_single_data_parses_and_casts_fields()
    {
        $data = [
            'category_ids' => json_encode([['id' => 1, 'position' => 1]]),
            'image' => json_encode(['a.png', 'b.png']),
            'attributes' => json_encode([1, 2]),
            'choice_options' => json_encode([
                ['name' => 'size', 'options' => ['S', 'M']],
            ]),
            'variations' => json_encode([
                ['type' => 'size-S', 'price' => '9.99', 'stock' => '3'],
                ['type' => 'size-M', 'price' => '10.50', 'stock' => '7'],
            ]),
            'translations' => [
                (object)['key' => 'name', 'value' => 'Product Name'],
                (object)['key' => 'description', 'value' => 'Product Desc'],
            ],
        ];

        $formatted = Helpers::product_data_formatting($data, false);

        $this->assertIsArray($formatted['category_ids']);
        $this->assertIsArray($formatted['image']);
        $this->assertIsArray($formatted['attributes']);
        $this->assertIsArray($formatted['choice_options']);
        $this->assertIsArray($formatted['variations']);
        $this->assertSame('Product Name', $formatted['name']);
        $this->assertSame('Product Desc', $formatted['description']);
        $this->assertSame('size-S', $formatted['variations'][0]['type']);
        $this->assertSame(9.99, $formatted['variations'][0]['price']);
        $this->assertSame(3, $formatted['variations'][0]['stock']);
        $this->assertSame(10.50, $formatted['variations'][1]['price']);
        $this->assertSame(7, $formatted['variations'][1]['stock']);
    }

    public function test_product_formatter_decodes_json_like_fields_to_arrays()
    {
        $product = [
            'image' => json_encode(['x.png']),
            'variations' => json_encode([['type' => 't', 'price' => 1, 'stock' => 1]]),
            'attributes' => json_encode([1, 2, 3]),
            'category_ids' => json_encode([['id' => 2, 'position' => 0]]),
            'choice_options' => json_encode([['name' => 'color', 'options' => ['r']]]),
        ];

        $out = Helpers::product_formatter($product);

        $this->assertIsArray($out['image']);
        $this->assertIsArray($out['variations']);
        $this->assertIsArray($out['attributes']);
        $this->assertIsArray($out['category_ids']);
        $this->assertIsArray($out['choice_options']);
    }

    public function test_tax_calculate_percent_vs_amount()
    {
        $productPercent = ['tax_type' => 'percent', 'tax' => 10];
        $productFlat    = ['tax_type' => 'amount', 'tax' => 15.5];

        $this->assertSame(5.0, Helpers::tax_calculate($productPercent, 50));
        $this->assertSame(15.5, Helpers::tax_calculate($productFlat, 50));
    }

    public function test_discount_calculate_percent_vs_amount()
    {
        $productPercent = ['discount_type' => 'percent', 'discount' => 20];
        $productFlat    = ['discount_type' => 'amount', 'discount' => 7.25];

        $this->assertSame(10.0, Helpers::discount_calculate($productPercent, 50));
        $this->assertSame(7.25, Helpers::discount_calculate($productFlat, 50));
    }

    public function test_trim_words_limits_text_and_sets_flag()
    {
        $text = 'one two three four five six';
        $limited = Helpers::trimWords($text, 3);

        $this->assertSame('one two three...', $limited['text']);
        $this->assertTrue($limited['isTruncated']);

        $nolimit = Helpers::trimWords($text, 10);
        $this->assertSame('one two three four five six', $nolimit['text']);
        $this->assertFalse($nolimit['isTruncated']);
    }

    public function test_remove_invalid_characters_replaces_with_spaces()
    {
        $input = "he'llo, wo\"rld;<bad>?";
        $output = Helpers::remove_invalid_charcaters($input);
        $this->assertSame('he llo  wo rld  bad  ', $output);
    }

    public function test_get_language_name_returns_human_readable_or_key()
    {
        $this->assertSame('English', Helpers::get_language_name('en'));
        $this->assertSame('Unknown-Lang', Helpers::get_language_name('Unknown-Lang'));
    }

    public function test_normalize_store_google_maps_url_strips_tracking_on_at_coordinates_link()
    {
        $long = 'https://www.google.com/maps/@31.5055409,35.0367053,16z?entry=ttu&g_ep=EgoyMDI2MDMxOC4xIKXMDSoASAFQAw%3D%3D';
        $this->assertSame(
            'https://www.google.com/maps/@31.5055409,35.0367053,16z',
            Helpers::normalizeStoreGoogleMapsUrl($long)
        );
    }

    public function test_normalize_store_google_maps_url_leaves_place_and_short_links_unchanged()
    {
        $place = 'https://www.google.com/maps/place/Foo/@31.5,35.0,17z/data=!3m1!4b1';
        $this->assertSame($place, Helpers::normalizeStoreGoogleMapsUrl($place));

        $short = 'https://maps.app.goo.gl/abc123';
        $this->assertSame($short, Helpers::normalizeStoreGoogleMapsUrl($short));
    }

    public function test_normalize_store_google_maps_url_empty_trim()
    {
        $this->assertSame('', Helpers::normalizeStoreGoogleMapsUrl(''));
        $this->assertSame('', Helpers::normalizeStoreGoogleMapsUrl('   '));
    }

    public function test_google_maps_store_url_to_embed_src_from_at_coordinates()
    {
        $url = 'https://www.google.com/maps/@31.5055409,35.0367053,16z?entry=ttu';
        $src = Helpers::googleMapsStoreUrlToEmbedSrc($url);
        $this->assertIsString($src);
        $this->assertStringContainsString('https://www.openstreetmap.org/export/embed.html', $src);
        $this->assertStringContainsString('bbox=', $src);
        $this->assertStringContainsString('layer=mapnik', $src);
        $this->assertStringContainsString('marker=', $src);
    }

    public function test_open_street_map_preview_embed_matches_expected_host()
    {
        $src = Helpers::openStreetMapPreviewEmbedFromLatLngZoom(31.5, 35.0, 16);
        $this->assertStringStartsWith('https://www.openstreetmap.org/export/embed.html?', $src);
    }

    public function test_google_maps_store_url_to_embed_src_returns_null_for_short_link()
    {
        $this->assertNull(Helpers::googleMapsStoreUrlToEmbedSrc('https://maps.app.goo.gl/abc'));
    }
}
