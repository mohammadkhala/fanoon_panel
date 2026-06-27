<?php

namespace Tests\Feature;

use App\CentralLogics\Helpers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationAutoEmailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\BaitPaitSeeder::class);
    }

    public function test_helpers_generate_email_uses_config_domain(): void
    {
        config(['app.auto_email_domain' => 'example-store.test']);
        config(['app.url' => 'http://ignored.test']);

        $email = Helpers::generateAutoCustomerEmail('05991234567');
        $this->assertSame('u05991234567@example-store.test', $email);
    }

    public function test_helpers_generate_email_falls_back_to_app_url_host(): void
    {
        config(['app.auto_email_domain' => null]);
        config(['app.url' => 'https://www.shop.example:8443/path']);

        $email = Helpers::generateAutoCustomerEmail('0599000111');
        $this->assertSame('u0599000111@shop.example', $email);
    }

    public function test_registration_without_email_uses_auto_email(): void
    {
        config(['app.auto_email_domain' => 'auto-mail.test']);

        $response = $this->postJson('/api/v1/auth/registration', [
            'f_name' => 'Auto',
            'l_name' => 'User',
            'phone' => '05991112233',
            'password' => 'secret12',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'phone' => '05991112233',
            'email' => 'u05991112233@auto-mail.test',
        ]);
    }
}
