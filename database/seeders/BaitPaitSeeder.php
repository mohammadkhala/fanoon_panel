<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Bait Pait initial data seeder.
 *
 * Creates:
 * - Admin: info@baitpait.com / 100200300 (لوحة المشرف فقط)
 * - Branch (id=1): Bait Pait — للطلبات فقط، تسجيل الدخول معطّل
 * - Passport OAuth clients (for API)
 *
 * No products, categories, or customers — user adds later.
 */
class BaitPaitSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdmin();
        $this->seedBranch();
        $this->seedPassportClients();
    }

    private function seedPassportClients(): void
    {
        if (DB::table('oauth_clients')->count() > 0) {
            return;
        }
        Artisan::call('passport:client', ['--personal' => true, '--name' => config('app.name') . ' Personal Access Client', '--no-interaction' => true]);
        Artisan::call('passport:client', ['--password' => true, '--name' => config('app.name') . ' Password Grant Client', '--provider' => 'users', '--no-interaction' => true]);
    }

    private function seedAdmin(): void
    {
        if (DB::table('admins')->where('email', 'info@baitpait.com')->exists()) {
            return;
        }

        DB::table('admins')->insert([
            'id' => 1,
            'f_name' => 'Bait Pait',
            'l_name' => 'Admin',
            'phone' => null,
            'email' => 'info@baitpait.com',
            'image' => null,
            'password' => bcrypt('100200300'),
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'fcm_token' => null,
        ]);
    }

    private function seedBranch(): void
    {
        if (DB::table('branches')->where('id', 1)->exists()) {
            $update = [
                'name' => 'Bait Pait',
                'email' => 'branch@baitpait.com',
                'password' => bcrypt('100200300'),
                'address' => null,
                'phone' => null,
                'status' => 1,
                'updated_at' => now(),
            ];
            if (Schema::hasColumn('branches', 'login_enabled')) {
                $update['login_enabled'] = false;
            }
            DB::table('branches')->where('id', 1)->update($update);
            return;
        }

        $branchData = [
            'id' => 1,
            'restaurant_id' => null,
            'name' => 'Bait Pait',
            'email' => 'branch@baitpait.com',
            'password' => bcrypt('100200300'),
            'latitude' => null,
            'longitude' => null,
            'address' => null,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            'coverage' => 500,
            'remember_token' => null,
            'image' => null,
            'phone' => null,
        ];
        if (Schema::hasColumn('branches', 'login_enabled')) {
            $branchData['login_enabled'] = false;
        }
        DB::table('branches')->insert($branchData);
    }
}
