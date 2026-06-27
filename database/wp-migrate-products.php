<?php

declare(strict_types=1);

/**
 * Business Purpose:
 * Migrate WooCommerce categories and products into the local application schema
 * while preserving original image file names for later physical copy/sync.
 *
 * Usage:
 * php database/wp-migrate-products.php
 */

/**
 * Business Purpose:
 * Build a PDO connection with safe defaults for consistent migration behavior.
 */
function db(string $database): PDO
{
    return new PDO(
        "mysql:host=127.0.0.1;port=3306;dbname={$database};charset=utf8mb4",
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
}

/**
 * Business Purpose:
 * Read WooCommerce product categories with parent hierarchy.
 */
function getWpCategories(PDO $wp): array
{
    $sql = <<<SQL
        SELECT
            t.term_id AS wp_id,
            t.name,
            t.slug,
            tt.parent AS wp_parent_id
        FROM wpos_terms t
        INNER JOIN wpos_term_taxonomy tt
            ON tt.term_id = t.term_id
        WHERE tt.taxonomy = 'product_cat'
        ORDER BY tt.parent ASC, t.term_id ASC
    SQL;

    return $wp->query($sql)->fetchAll();
}

/**
 * Business Purpose:
 * Insert categories into target table and return WP->target ID map.
 */
function migrateCategories(PDO $target, array $wpCategories): array
{
    $map = [];
    $pending = $wpCategories;

    $findStmt = $target->prepare('SELECT id FROM categories WHERE name = ? LIMIT 1');
    $insertStmt = $target->prepare(
        'INSERT INTO categories (name, parent_id, position, status, image, is_featured, created_at, updated_at)
         VALUES (?, ?, ?, 1, ?, 0, NOW(), NOW())'
    );

    while (count($pending) > 0) {
        $progress = false;
        $next = [];

        foreach ($pending as $cat) {
            $wpId = (int) $cat['wp_id'];
            $wpParentId = (int) $cat['wp_parent_id'];

            if ($wpParentId !== 0 && !isset($map[$wpParentId])) {
                $next[] = $cat;
                continue;
            }

            $parentId = $wpParentId === 0 ? 0 : $map[$wpParentId];

            $findStmt->execute([$cat['name']]);
            $existingId = $findStmt->fetchColumn();
            if ($existingId !== false) {
                $map[$wpId] = (int) $existingId;
                $progress = true;
                continue;
            }

            $imageName = $cat['slug'] !== '' ? $cat['slug'] . '.png' : 'def.png';
            $position = $parentId === 0 ? 0 : 1;

            $insertStmt->execute([
                $cat['name'],
                $parentId,
                $position,
                $imageName,
            ]);

            $map[$wpId] = (int) $target->lastInsertId();
            $progress = true;
        }

        if (!$progress) {
            throw new RuntimeException('Category hierarchy could not be resolved.');
        }

        $pending = $next;
    }

    return $map;
}

/**
 * Business Purpose:
 * Read WooCommerce products with pricing, stock, image filename, and category links.
 */
function getWpProducts(PDO $wp): array
{
    $sql = <<<SQL
        SELECT
            p.ID AS wp_id,
            p.post_title,
            p.post_content,
            COALESCE(
                NULLIF(
                    (SELECT pm.meta_value
                     FROM wpos_postmeta pm
                     WHERE pm.post_id = p.ID AND pm.meta_key = '_price'
                     LIMIT 1),
                    ''
                ),
                NULLIF(
                    (SELECT pm.meta_value
                     FROM wpos_postmeta pm
                     WHERE pm.post_id = p.ID AND pm.meta_key = '_regular_price'
                     LIMIT 1),
                    ''
                ),
                '0'
            ) AS price,
            COALESCE(
                NULLIF(
                    (SELECT pm.meta_value
                     FROM wpos_postmeta pm
                     WHERE pm.post_id = p.ID AND pm.meta_key = '_stock'
                     LIMIT 1),
                    ''
                ),
                '0'
            ) AS stock,
            (
                SELECT pm_file.meta_value
                FROM wpos_postmeta pm_thumb
                INNER JOIN wpos_postmeta pm_file
                    ON pm_file.post_id = pm_thumb.meta_value
                    AND pm_file.meta_key = '_wp_attached_file'
                WHERE pm_thumb.post_id = p.ID
                    AND pm_thumb.meta_key = '_thumbnail_id'
                LIMIT 1
            ) AS attached_file,
            (
                SELECT GROUP_CONCAT(DISTINCT CAST(t.term_id AS CHAR) ORDER BY t.term_id SEPARATOR ',')
                FROM wpos_term_relationships tr
                INNER JOIN wpos_term_taxonomy tt
                    ON tt.term_taxonomy_id = tr.term_taxonomy_id
                    AND tt.taxonomy = 'product_cat'
                INNER JOIN wpos_terms t
                    ON t.term_id = tt.term_id
                WHERE tr.object_id = p.ID
            ) AS wp_category_ids
        FROM wpos_posts p
        WHERE p.post_type = 'product'
            AND p.post_status IN ('publish', 'private')
        ORDER BY p.ID ASC
    SQL;

    return $wp->query($sql)->fetchAll();
}

/**
 * Business Purpose:
 * Convert file path to only the image name to preserve media naming convention.
 */
function extractImageName(?string $attachedFile): ?string
{
    if ($attachedFile === null || $attachedFile === '') {
        return null;
    }

    return basename($attachedFile);
}

/**
 * Business Purpose:
 * Convert category IDs to target category_ids JSON structure expected by app.
 */
function buildCategoryIdsJson(string $wpCategoryCsv, array $categoryMap, array $wpParentMap): ?string
{
    if ($wpCategoryCsv === '') {
        return null;
    }

    $wpIds = array_filter(array_map('intval', explode(',', $wpCategoryCsv)));
    if ($wpIds === []) {
        return null;
    }

    $selectedWpId = null;
    foreach ($wpIds as $wpId) {
        $parentWpId = (int) ($wpParentMap[$wpId] ?? 0);
        if ($parentWpId !== 0) {
            $selectedWpId = $wpId;
            break;
        }
    }

    if ($selectedWpId === null) {
        $selectedWpId = $wpIds[0];
    }

    if (!isset($categoryMap[$selectedWpId])) {
        return null;
    }

    $selectedParentWpId = (int) ($wpParentMap[$selectedWpId] ?? 0);
    if ($selectedParentWpId !== 0 && isset($categoryMap[$selectedParentWpId])) {
        return json_encode(
            [
                ['id' => (string) $categoryMap[$selectedParentWpId], 'position' => 1],
                ['id' => (string) $categoryMap[$selectedWpId], 'position' => 2],
            ],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    return json_encode(
        [
            ['id' => (string) $categoryMap[$selectedWpId], 'position' => 1],
        ],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
}

/**
 * Business Purpose:
 * Insert products once, skip duplicates by exact name for safe repeated runs.
 */
function migrateProducts(PDO $target, array $wpProducts, array $categoryMap, array $wpParentMap): array
{
    $findStmt = $target->prepare('SELECT id FROM products WHERE name = ? LIMIT 1');
    $insertStmt = $target->prepare(
        'INSERT INTO products
            (name, description, image, price, tax, status, category_ids, discount, discount_type, tax_type, unit, total_stock, min_order_qty, created_at, updated_at)
         VALUES
            (?, ?, ?, ?, 0, 1, ?, 0, "percent", "percent", "pc", ?, 1, NOW(), NOW())'
    );

    $inserted = 0;
    $skipped = 0;
    $images = [];

    foreach ($wpProducts as $product) {
        $name = trim((string) $product['post_title']);
        if ($name === '') {
            $skipped++;
            continue;
        }

        $findStmt->execute([$name]);
        if ($findStmt->fetchColumn() !== false) {
            $skipped++;
            continue;
        }

        $imageName = extractImageName($product['attached_file'] ?? null);
        $imageJson = $imageName !== null ? json_encode([$imageName], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;
        $categoryIdsJson = buildCategoryIdsJson(
            (string) ($product['wp_category_ids'] ?? ''),
            $categoryMap,
            $wpParentMap
        );

        $price = is_numeric($product['price']) ? (float) $product['price'] : 0.0;
        $stock = is_numeric($product['stock']) ? max(0, (int) $product['stock']) : 0;

        $insertStmt->execute([
            $name,
            (string) ($product['post_content'] ?? ''),
            $imageJson,
            $price,
            $categoryIdsJson,
            $stock,
        ]);

        if ($imageName !== null) {
            $images[$imageName] = true;
        }

        $inserted++;
    }

    return [
        'inserted' => $inserted,
        'skipped' => $skipped,
        'image_names' => array_keys($images),
    ];
}

$wpDb = db('wp262_migrate');
$targetDb = db('hexacom_test');

$targetDb->beginTransaction();

try {
    $wpCategories = getWpCategories($wpDb);
    $categoryMap = migrateCategories($targetDb, $wpCategories);
    $wpParentMap = [];
    foreach ($wpCategories as $wpCategory) {
        $wpParentMap[(int) $wpCategory['wp_id']] = (int) $wpCategory['wp_parent_id'];
    }

    $wpProducts = getWpProducts($wpDb);
    $result = migrateProducts($targetDb, $wpProducts, $categoryMap, $wpParentMap);

    $targetDb->commit();

    $imageListPath = __DIR__ . '/wp-migrated-image-names.txt';
    file_put_contents($imageListPath, implode(PHP_EOL, $result['image_names']) . PHP_EOL);

    echo 'Categories source: ' . count($wpCategories) . PHP_EOL;
    echo 'Products source: ' . count($wpProducts) . PHP_EOL;
    echo 'Products inserted: ' . $result['inserted'] . PHP_EOL;
    echo 'Products skipped (duplicate/empty): ' . $result['skipped'] . PHP_EOL;
    echo 'Image names exported to: ' . $imageListPath . PHP_EOL;
} catch (Throwable $e) {
    $targetDb->rollBack();
    fwrite(STDERR, 'Migration failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
