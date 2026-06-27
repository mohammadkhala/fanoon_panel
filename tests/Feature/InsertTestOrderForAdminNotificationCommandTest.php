<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InsertTestOrderForAdminNotificationCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function artisan_command_creates_unchecked_order_visible_in_get_store_data(): void
    {
        Cache::flush();
        Admin::unguarded(fn () => Admin::create([
            'f_name' => 'T',
            'l_name' => 'A',
            'phone' => '0599999991',
            'email' => 'admin_cmd@test.com',
            'password' => Hash::make('password'),
        ]));
        $branch = Branch::unguarded(fn () => Branch::create([
            'name' => 'B',
            'email' => 'b_cmd@test.com',
            'password' => Hash::make('password'),
        ]));
        $user = User::unguarded(fn () => User::create([
            'f_name' => 'U',
            'l_name' => 'C',
            'phone' => '0598888881',
            'email' => 'user_cmd@test.com',
            'password' => Hash::make('password'),
        ]));

        $this->artisan('dev:admin-notification-test-order', [
            '--user' => (string) $user->id,
            '--branch' => (string) $branch->id,
            '--amount' => '50',
        ])->assertSuccessful();

        $response = $this->actingAs(Admin::where('email', 'admin_cmd@test.com')->first(), 'admin')
            ->getJson(route('admin.get-store-data'));

        $response->assertOk();
        $this->assertGreaterThanOrEqual(1, $response->json('data.new_order'));
    }
}
