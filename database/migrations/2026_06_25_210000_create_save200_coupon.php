<?php

use App\Models\Coupon;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Coupon::query()->updateOrCreate(
            ['code' => 'SAVE200'],
            [
                'discount_amount' => 200,
                'is_active' => true,
            ],
        );
    }

    public function down(): void
    {
        Coupon::query()
            ->where('code', 'SAVE200')
            ->delete();
    }
};
