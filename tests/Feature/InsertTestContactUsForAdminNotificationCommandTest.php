<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\ContactUs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InsertTestContactUsForAdminNotificationCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function artisan_command_creates_unread_contact_messages_visible_in_get_store_data(): void
    {
        Cache::flush();
        $admin = Admin::unguarded(fn () => Admin::create([
            'f_name' => 'T',
            'l_name' => 'A',
            'phone' => '0599999992',
            'email' => 'admin_contact_cmd@test.com',
            'password' => Hash::make('password'),
        ]));

        $this->artisan('dev:admin-notification-test-contact-us', [
            '--count' => '2',
        ])->assertSuccessful();

        $this->assertSame(2, ContactUs::unread()->count());

        $response = $this->actingAs($admin, 'admin')
            ->getJson(route('admin.get-store-data'));

        $response->assertOk();
        $this->assertGreaterThanOrEqual(2, $response->json('data.new_contact_us'));
    }
}
