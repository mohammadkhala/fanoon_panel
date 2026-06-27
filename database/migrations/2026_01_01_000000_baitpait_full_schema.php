<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * BaitPait full schema migration.
 *
 * Consolidates all tables from database_v7.7.sql and BaitPait-specific migrations.
 * Excludes: delivery_men, d_m_reviews, delivery_histories, dc_conversations,
 * order_delivery_histories, newsletters, track_deliverymen, soft_credentials,
 * wallet_transactions, user_accounts.
 *
 * Orders: no delivery_man_id. Messages: no deliveryman_id.
 * Compatible with MySQL and SQLite.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        // --- Tables with no foreign key dependencies ---
        $this->createFailedJobsTable();
        $this->createUsersTable();
        $this->createPasswordResetsTable();
        $this->createAdminsTable();
        $this->createAttributesTable();
        $this->createBranchesTable();
        $this->createBusinessSettingsTable();
        $this->createCategoriesTable();
        $this->createConversationsTable();
        $this->createCouponsTable();
        $this->createCurrenciesTable();
        $this->createEmailVerificationsTable();
        $this->createFlashSalesTable();
        $this->createGuestUsersTable();
        $this->createLoginSetupsTable();
        $this->createNotificationsTable();
        $this->createPhoneVerificationsTable();
        $this->createSocialMediasTable();
        $this->createTranslationsTable();

        // --- BaitPait: cities, user_types (no FKs) ---
        $this->createCitiesTable();
        $this->createUserTypesTable();

        // --- Tables depending on branches ---
        $this->createDeliveryChargeSetupsTable();
        $this->createDeliveryChargeByAreasTable();
        $this->createAreasTable();

        // --- customer_addresses (user_id, area_id) ---
        $this->createCustomerAddressesTable();

        // --- products ---
        $this->createProductsTable();

        // --- orders (user_id, delivery_address_id, branch_id) ---
        $this->createOrdersTable();

        // --- Tables depending on orders/products ---
        $this->createOrderDetailsTable();
        $this->createFlashSaleProductsTable();
        $this->createOrderAreasTable();

        // --- BaitPait: shipping_companies, order_shipments ---
        $this->createShippingCompaniesTable();
        $this->createOrderShipmentsTable();

        // --- BaitPait: product_user_type_* ---
        $this->createProductUserTypeDiscountsTable();
        $this->createProductUserTypePricesTable();

        // --- BaitPait: loyalty_* ---
        $this->createLoyaltyTables();

        // --- messages (conversation_id, customer_id; no deliveryman_id) ---
        $this->createMessagesTable();

        // --- reviews, wishlists ---
        $this->createReviewsTable();
        $this->createWishlistsTable();

        // --- OAuth ---
        $this->createOAuthTables();

        // --- BaitPait: contact_us ---
        $this->createContactUsTable();

        // --- Minimal seed data ---
        $this->seedMinimalData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'contact_us',
            'oauth_personal_access_clients',
            'oauth_refresh_tokens',
            'oauth_access_tokens',
            'oauth_auth_codes',
            'oauth_clients',
            'wishlists',
            'reviews',
            'messages',
            'loyalty_point_logs',
            'loyalty_points',
            'product_user_type_prices',
            'product_user_type_discounts',
            'order_shipments',
            'shipping_companies',
            'order_areas',
            'flash_sale_products',
            'order_details',
            'orders',
            'products',
            'customer_addresses',
            'areas',
            'delivery_charge_by_areas',
            'delivery_charge_setups',
            'user_types',
            'cities',
            'translations',
            'social_medias',
            'phone_verifications',
            'login_setups',
            'guest_users',
            'flash_sales',
            'email_verifications',
            'currencies',
            'coupons',
            'conversations',
            'categories',
            'business_settings',
            'branches',
            'attributes',
            'admins',
            'password_resets',
            'users',
            'failed_jobs',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }

    private function createFailedJobsTable(): void
    {
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    private function createUsersTable(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('f_name', 100)->nullable();
            $table->string('l_name', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('image', 100)->nullable();
            $table->boolean('is_phone_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 100);
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->string('email_verification_token', 255)->nullable();
            $table->string('phone', 255)->nullable();
            $table->string('cm_firebase_token', 255)->nullable();
            $table->string('temporary_token', 255)->nullable();
            $table->unsignedTinyInteger('login_hit_count')->default(0);
            $table->boolean('is_temp_blocked')->default(false);
            $table->timestamp('temp_block_time')->nullable();
            $table->string('login_medium', 255)->default('general');
        });
    }

    private function createPasswordResetsTable(): void
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email_or_phone', 255);
            $table->string('token', 255);
            $table->timestamp('created_at')->nullable();
            $table->unsignedTinyInteger('otp_hit_count')->default(0);
            $table->boolean('is_temp_blocked')->default(false);
            $table->timestamp('temp_block_time')->nullable();
            $table->index('email_or_phone');
        });
    }

    private function createAdminsTable(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('f_name', 100)->nullable();
            $table->string('l_name', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->unique();
            $table->string('image', 100)->nullable();
            $table->string('password', 100);
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->string('fcm_token', 255)->nullable();
        });
    }

    private function createAttributesTable(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->timestamps();
        });
    }

    private function createBranchesTable(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id')->nullable();
            $table->string('name', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('password', 255)->nullable();
            $table->string('latitude', 255)->nullable();
            $table->string('longitude', 255)->nullable();
            $table->text('address')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->unsignedInteger('coverage')->default(1);
            $table->string('remember_token', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->string('phone', 255)->nullable();
        });
    }

    private function createBusinessSettingsTable(): void
    {
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 255)->nullable();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    private function createCategoriesTable(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->unsignedInteger('position')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->string('image', 255)->default('def.png');
            $table->string('banner_image', 255)->nullable();
            $table->unsignedTinyInteger('is_featured')->default(0);
        });
    }

    private function createConversationsTable(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('message')->nullable();
            $table->text('reply')->nullable();
            $table->timestamps();
            $table->boolean('checked')->default(false);
            $table->string('image', 255)->nullable();
            $table->text('attachment')->nullable();
            $table->boolean('is_reply')->default(false);
        });
    }

    private function createCouponsTable(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->nullable();
            $table->string('code', 15)->nullable();
            $table->date('start_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->decimal('min_purchase', 8, 2)->default(0);
            $table->decimal('max_discount', 8, 2)->default(0);
            $table->decimal('discount', 8, 2)->default(0);
            $table->string('discount_type', 15)->default('percentage');
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->string('coupon_type', 255)->default('default');
            $table->unsignedInteger('limit')->nullable();
        });
    }

    private function createCurrenciesTable(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('country', 255)->nullable();
            $table->string('currency_code', 255)->nullable();
            $table->string('currency_symbol', 255)->nullable();
            $table->decimal('exchange_rate', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    private function createEmailVerificationsTable(): void
    {
        Schema::create('email_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255)->nullable();
            $table->string('token', 255)->nullable();
            $table->timestamps();
            $table->unsignedTinyInteger('otp_hit_count')->default(0);
            $table->boolean('is_temp_blocked')->default(false);
            $table->timestamp('temp_block_time')->nullable();
        });
    }

    private function createFlashSalesTable(): void
    {
        Schema::create('flash_sales', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->unsignedTinyInteger('status')->default(0);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->string('image', 255)->nullable();
            $table->timestamps();
        });
    }

    private function createGuestUsersTable(): void
    {
        Schema::create('guest_users', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 255)->nullable();
            $table->string('fcm_token', 255)->nullable();
            $table->string('language_code', 255)->default('en');
            $table->timestamps();
        });
    }

    private function createLoginSetupsTable(): void
    {
        Schema::create('login_setups', function (Blueprint $table) {
            $table->id();
            $table->string('key', 255)->nullable();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    private function createNotificationsTable(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('image', 50)->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    private function createPhoneVerificationsTable(): void
    {
        Schema::create('phone_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 255)->nullable();
            $table->string('token', 255)->nullable();
            $table->timestamps();
            $table->unsignedTinyInteger('otp_hit_count')->default(0);
            $table->boolean('is_temp_blocked')->default(false);
            $table->timestamp('temp_block_time')->nullable();
        });
    }

    private function createSocialMediasTable(): void
    {
        Schema::create('social_medias', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('link', 255);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    private function createTranslationsTable(): void
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('translationable_type');
            $table->unsignedBigInteger('translationable_id')->index();
            $table->string('locale')->index();
            $table->string('key', 255)->nullable();
            $table->text('value')->nullable();
        });
    }

    private function createCitiesTable(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->json('names')->nullable();
        });
    }

    private function createUserTypesTable(): void
    {
        Schema::create('user_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    private function createDeliveryChargeSetupsTable(): void
    {
        Schema::create('delivery_charge_setups', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('branch_id');
            $table->string('delivery_charge_type', 255)->default('distance')->comment('area/distance');
            $table->double('delivery_charge_per_kilometer')->default(0);
            $table->double('minimum_delivery_charge')->default(0);
            $table->double('minimum_distance_for_free_delivery')->default(0);
            $table->double('fixed_delivery_charge')->default(0);
            $table->timestamps();
            $table->index('branch_id');
        });
    }

    private function createDeliveryChargeByAreasTable(): void
    {
        Schema::create('delivery_charge_by_areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('branch_id');
            $table->string('area_name', 255);
            $table->double('delivery_charge')->default(0);
            $table->timestamps();
            $table->index('branch_id');
        });
    }

    private function createAreasTable(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id')->index();
            $table->string('name_en', 100);
            $table->string('name_ar', 100)->nullable();
            $table->json('names')->nullable();
            $table->double('delivery_charge')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    private function createCustomerAddressesTable(): void
    {
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('address_type', 100);
            $table->string('contact_person_number', 20);
            $table->string('floor', 10)->nullable();
            $table->string('house', 50)->nullable();
            $table->string('road', 50)->nullable();
            $table->string('address', 250)->nullable();
            $table->string('latitude', 255)->nullable();
            $table->string('longitude', 255)->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('is_guest')->default(false);
            $table->string('contact_person_name', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->unsignedBigInteger('area_id')->nullable();
        });
    }

    private function createProductsTable(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->text('description')->nullable();
            $table->text('image')->nullable();
            $table->double('price')->default(0);
            $table->text('variations')->nullable();
            $table->decimal('tax', 8, 2)->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->string('attributes', 255)->nullable();
            $table->string('category_ids', 255)->nullable();
            $table->text('choice_options')->nullable();
            $table->decimal('discount', 8, 2)->default(0);
            $table->string('discount_type', 20)->default('percent');
            $table->string('tax_type', 20)->default('percent');
            $table->string('unit', 255)->default('pc');
            $table->unsignedBigInteger('total_stock')->default(0);
            $table->unsignedInteger('min_order_qty')->default(1);
            $table->unsignedInteger('minimum_stock_alert')->nullable();
        });
    }

    private function createOrdersTable(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('is_guest')->default(false);
            $table->decimal('order_amount', 8, 2)->default(0);
            $table->decimal('coupon_discount_amount', 8, 2)->default(0);
            $table->string('coupon_discount_title', 255)->nullable();
            $table->string('payment_status', 255)->default('unpaid');
            $table->string('order_status', 255)->default('pending');
            $table->decimal('total_tax_amount', 8, 2)->default(0);
            $table->string('payment_method', 30)->nullable();
            $table->string('transaction_reference', 255)->nullable();
            $table->unsignedBigInteger('delivery_address_id')->nullable();
            $table->timestamps();
            $table->boolean('checked')->default(false);
            $table->decimal('delivery_charge', 8, 2)->default(0);
            $table->text('order_note')->nullable();
            $table->string('coupon_code', 255)->nullable();
            $table->string('order_type', 255)->default('delivery');
            $table->unsignedBigInteger('branch_id')->default(1);
            $table->string('callback', 255)->nullable();
            $table->decimal('extra_discount', 8, 2)->default(0);
            $table->text('delivery_address')->nullable();
            $table->decimal('bring_change_amount', 24, 8)->default(0)->nullable();
            $table->decimal('paid_amount', 24, 8)->default(0)->nullable();
            $table->unsignedInteger('loyalty_points_used')->default(0);
            $table->decimal('loyalty_discount_amount', 12, 2)->default(0);
            $table->json('additional_payment_method')->nullable();
        });
    }

    private function createOrderDetailsTable(): void
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->decimal('price', 8, 2)->default(0);
            $table->text('product_details')->nullable();
            $table->string('variation', 255)->nullable();
            $table->decimal('discount_on_product', 8, 2)->nullable();
            $table->string('discount_type', 20)->default('amount');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('tax_amount', 8, 2)->default(1);
            $table->timestamps();
            $table->string('variant', 255)->nullable();
            $table->string('unit', 255)->default('pc');
            $table->boolean('is_stock_decreased')->default(true);
        });
    }

    private function createFlashSaleProductsTable(): void
    {
        Schema::create('flash_sale_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flash_sale_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->timestamps();
        });
    }

    private function createOrderAreasTable(): void
    {
        Schema::create('order_areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('branch_id')->index();
            $table->unsignedBigInteger('area_id')->index();
            $table->float('distance')->default(0);
            $table->timestamps();
        });
    }

    private function createShippingCompaniesTable(): void
    {
        Schema::create('shipping_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->nullable()->unique();
            $table->string('api_url', 500)->nullable();
            $table->text('api_key')->nullable();
            $table->string('api_secret', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    private function createOrderShipmentsTable(): void
    {
        Schema::create('order_shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('shipping_company_id')->index();
            $table->string('tracking_number', 100)->nullable();
            $table->string('status', 50)->default('pending')->comment('pending, shipped, in_transit, delivered, failed');
            $table->text('notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('shipping_company_id')->references('id')->on('shipping_companies')->onDelete('restrict');
        });
    }

    private function createProductUserTypeDiscountsTable(): void
    {
        Schema::create('product_user_type_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('user_type_id')->constrained('user_types')->cascadeOnDelete();
            $table->decimal('discount', 24, 2)->default(0);
            $table->string('discount_type', 20)->default('percent');
            $table->timestamps();
            $table->unique(['product_id', 'user_type_id']);
        });
    }

    private function createProductUserTypePricesTable(): void
    {
        Schema::create('product_user_type_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('user_type_id')->constrained('user_types')->cascadeOnDelete();
            $table->decimal('price', 24, 2);
            $table->timestamps();
            $table->unique(['product_id', 'user_type_id']);
        });
    }

    private function createLoyaltyTables(): void
    {
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('points')->default(0);
            $table->string('level', 20)->default('bronze');
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->timestamps();
            $table->unique('user_id');
        });

        Schema::create('loyalty_point_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('points');
            $table->string('type', 30);
            $table->string('description')->nullable();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    private function createMessagesTable(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->text('message')->nullable();
            $table->text('attachment')->nullable();
            $table->timestamps();
        });
    }

    private function createReviewsTable(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('user_id');
            $table->mediumText('comment')->nullable();
            $table->string('attachment', 255)->nullable();
            $table->unsignedInteger('rating')->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('order_id')->nullable();
        });
    }

    private function createWishlistsTable(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->timestamps();
        });
    }

    private function createOAuthTables(): void
    {
        Schema::create('oauth_auth_codes', function (Blueprint $table) {
            $table->string('id', 100)->primary();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('client_id');
            $table->text('scopes')->nullable();
            $table->boolean('revoked');
            $table->dateTime('expires_at')->nullable();
            $table->index('user_id');
        });

        Schema::create('oauth_access_tokens', function (Blueprint $table) {
            $table->string('id', 100)->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('client_id');
            $table->string('name', 255)->nullable();
            $table->text('scopes')->nullable();
            $table->boolean('revoked');
            $table->timestamps();
            $table->dateTime('expires_at')->nullable();
            $table->index('user_id');
        });

        Schema::create('oauth_refresh_tokens', function (Blueprint $table) {
            $table->string('id', 100)->primary();
            $table->string('access_token_id', 100);
            $table->boolean('revoked');
            $table->dateTime('expires_at')->nullable();
            $table->index('access_token_id');
        });

        Schema::create('oauth_clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name', 255);
            $table->string('secret', 100)->nullable();
            $table->string('provider', 255)->nullable();
            $table->text('redirect');
            $table->boolean('personal_access_client');
            $table->boolean('password_client');
            $table->boolean('revoked');
            $table->timestamps();
            $table->index('user_id');
        });

        Schema::create('oauth_personal_access_clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->timestamps();
        });
    }

    private function createContactUsTable(): void
    {
        Schema::create('contact_us', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('email', 191);
            $table->string('phone', 50)->nullable();
            $table->string('subject', 255)->nullable();
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    private function seedMinimalData(): void
    {
        if (DB::table('business_settings')->count() > 0) {
            return;
        }

        $now = now();
        DB::table('business_settings')->insert([
            ['key' => 'store_name', 'value' => 'Business Name', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'currency', 'value' => 'ILS', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'delivery_charge', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'cash_on_delivery', 'value' => json_encode(['status' => 1]), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'maintenance_duration_setup', 'value' => json_encode(['maintenance_duration' => 'until_change', 'start_date' => null, 'end_date' => null]), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'maintenance_message_setup', 'value' => json_encode(['business_number' => 1, 'business_email' => 1, 'maintenance_message' => "We're Working On Something Special!", 'message_body' => 'Our system is currently undergoing maintenance. Please check back soon.']), 'created_at' => $now, 'updated_at' => $now],
            // أساسية
            ['key' => 'pagination_limit', 'value' => '10', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'language', 'value' => json_encode(['ar', 'en']), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'logo', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'app_logo', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'fav_icon', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            // FCM — قيم افتراضية تسويقية (رسائل المندوب محذوفة من الواجهة لكن تبقى في DB)
            ['key' => 'push_notification_key', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'order_pending_message', 'value' => json_encode(['status' => 0, 'message' => 'شكراً لثقتك! استلمنا طلبك ونجهّزه خصيصاً لك — سنؤكد لك فور الجاهزية']), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'order_confirmation_msg', 'value' => json_encode(['status' => 0, 'message' => 'خبر سار! طلبك مؤكد ونحضّره بعناية. سنصل إليك في الموعد — ننتظر رأيك']), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'order_processing_message', 'value' => json_encode(['status' => 0, 'message' => 'طلبك بين أيدينا الآن ونعطيه كل الاهتمام. سنخبرك عند خروجه للتوصيل']), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'out_for_delivery_message', 'value' => json_encode(['status' => 0, 'message' => 'طلبك في الطريق إليك! شكراً لصبرك — سنصل قريباً ونتمنى تجربة رائعة']), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'order_delivered_message', 'value' => json_encode(['status' => 0, 'message' => 'تم التوصيل بنجاح! شكراً لاختيارك لنا. رأيك يهمنا — شاركنا تجربتك']), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'delivery_boy_assign_message', 'value' => json_encode(['status' => 0, 'message' => '']), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'delivery_boy_start_message', 'value' => json_encode(['status' => 0, 'message' => '']), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'delivery_boy_delivered_message', 'value' => json_encode(['status' => 0, 'message' => '']), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'customer_notify_message', 'value' => json_encode(['status' => 0, 'message' => '']), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'returned_message', 'value' => json_encode(['status' => 0, 'message' => 'شكراً لتواصلك. استلمنا طلب الإرجاع وفريقنا سيتواصل معك خلال 24 ساعة']), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'failed_message', 'value' => json_encode(['status' => 0, 'message' => 'نعتذر عن الإزعاج. واجهنا صعوبة — سنتواصل معك فوراً لترتيب أفضل حل. ثقتك تهمنا']), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'canceled_message', 'value' => json_encode(['status' => 0, 'message' => 'تم تنفيذ طلب الإلغاء. نأمل خدمتك مجدداً — نحن هنا لخدمتك عند حاجتك']), 'created_at' => $now, 'updated_at' => $now],
            // إعدادات التطبيق
            ['key' => 'play_store_config', 'value' => json_encode(['status' => 0, 'link' => '', 'min_version' => '0']), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'app_store_config', 'value' => json_encode(['status' => 0, 'link' => '', 'min_version' => '0']), 'created_at' => $now, 'updated_at' => $now],
            // إعدادات المتجر
            ['key' => 'phone', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'email_address', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'address', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'country', 'value' => 'PS', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'minimum_order_value', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'self_pickup', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'currency_symbol_position', 'value' => 'right', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'guest_checkout', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'maintenance_mode', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
            // الطرف الثالث — Google فقط (Facebook و Apple معطّلان)
            ['key' => 'cookies', 'value' => json_encode([
                'status' => 0,
                'text' => [
                    'ar' => 'نستخدم ملفات تعريف الارتباط لتحسين تجربتك على الموقع وتذكر تفضيلاتك. يمكنك تعطيلها من إعدادات المتصفح. بمتابعة التصفح، فإنك توافق على استخدامنا لملفات تعريف الارتباط.',
                    'en' => 'We use cookies to improve your site experience and remember your preferences. You can disable them from your browser settings. By continuing to browse, you agree to our use of cookies.',
                ],
            ]), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'google_social_login', 'value' => '1', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'facebook_social_login', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'apple_login', 'value' => json_encode(['login_medium' => '', 'client_id' => '', 'team_id' => '', 'key_id' => '', 'service_file' => '']), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'whatsapp', 'value' => json_encode(['status' => 0, 'number' => '']), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'telegram', 'value' => json_encode(['status' => 0, 'user_name' => '']), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'messenger', 'value' => json_encode(['status' => 0, 'user_name' => '']), 'created_at' => $now, 'updated_at' => $now],
            // إضافية
            ['key' => 'terms_and_conditions', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'privacy_policy', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'about_us', 'value' => '', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'mail_config', 'value' => json_encode(['status' => 0, 'name' => 'Elite Vape', 'host' => 'smtp.gmail.com', 'driver' => 'smtp', 'port' => '587', 'username' => '', 'email_id' => '', 'encryption' => 'tls', 'password' => '']), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'firebase_otp_verification', 'value' => json_encode(['status' => 0, 'web_api_key' => '']), 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'otp_resend_time', 'value' => '60', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'loyalty_points_enabled', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'loyalty_amount_for_one_point', 'value' => '10', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'loyalty_points_per_amount', 'value' => '1', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'loyalty_point_redemption_value', 'value' => '0.5', 'created_at' => $now, 'updated_at' => $now],
        ]);

        if (DB::table('login_setups')->count() > 0) {
            return;
        }

        DB::table('login_setups')->insert([
            ['key' => 'email_verification', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'phone_verification', 'value' => '0', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'login_options', 'value' => '{"manual_login":1,"otp_login":0,"social_media_login":0}', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'social_media_for_login', 'value' => '{"google":0,"facebook":0,"apple":0}', 'created_at' => now(), 'updated_at' => now()],
        ]);

        if (DB::table('currencies')->count() > 0) {
            return;
        }

        DB::table('currencies')->insert([
            ['country' => 'Israeli Shekel', 'currency_code' => 'ILS', 'currency_symbol' => '₪', 'exchange_rate' => 1.00, 'created_at' => now(), 'updated_at' => now()],
            ['country' => 'US Dollar', 'currency_code' => 'USD', 'currency_symbol' => '$', 'exchange_rate' => 1.00, 'created_at' => now(), 'updated_at' => now()],
            ['country' => 'British Pound Sterling', 'currency_code' => 'GBP', 'currency_symbol' => '£', 'exchange_rate' => 1.00, 'created_at' => now(), 'updated_at' => now()],
            ['country' => 'Euro', 'currency_code' => 'EUR', 'currency_symbol' => '€', 'exchange_rate' => 1.00, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // BaitPait: إنشاء المشرف الافتراضي — يضمن وجوده بعد migrate أو migrate:fresh
        if (!DB::table('admins')->where('email', 'info@baitpait.com')->exists()) {
            DB::table('admins')->insert([
                'id' => 1,
                'f_name' => 'Bait Pait',
                'l_name' => 'Admin',
                'phone' => null,
                'email' => 'info@baitpait.com',
                'image' => null,
                'password' => bcrypt('100200300'),
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
                'fcm_token' => null,
            ]);
        }
    }
};
