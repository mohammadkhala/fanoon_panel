<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix whatsapp/telegram/messenger: ensure correct keys (number, user_name).
     * Old seed used 'value', view expects 'number' (whatsapp) and 'user_name' (telegram, messenger).
     */
    public function up(): void
    {
        foreach (['whatsapp', 'telegram', 'messenger'] as $key) {
            $row = DB::table('business_settings')->where('key', $key)->first();
            if (!$row) continue;

            $data = json_decode($row->value, true) ?? [];
            $fixed = match ($key) {
                'whatsapp' => ['status' => $data['status'] ?? 0, 'number' => $data['number'] ?? $data['value'] ?? ''],
                'telegram', 'messenger' => ['status' => $data['status'] ?? 0, 'user_name' => $data['user_name'] ?? $data['value'] ?? ''],
                default => $data,
            };

            DB::table('business_settings')->where('key', $key)->update([
                'value' => json_encode($fixed),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // لا تراجع — التعديل آمن
    }
};
