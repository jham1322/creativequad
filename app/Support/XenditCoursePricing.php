<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class XenditCoursePricing
{
    public const DISPLAY_PRICE = 599.00;

    private const SETTINGS_PATH = 'app/settings/xendit-course-pricing.json';

    public static function displayPrice(): float
    {
        return self::DISPLAY_PRICE;
    }

    public static function paymentPrice(): float
    {
        $stored = self::readStoredPrice();

        if ($stored !== null) {
            return $stored;
        }

        return max(1, (float) config('services.xendit.course_price', self::DISPLAY_PRICE));
    }

    public static function setPaymentPrice(float $amount): float
    {
        $normalized = max(1, round($amount, 2));
        $path = storage_path(self::SETTINGS_PATH);
        $directory = dirname($path);

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        File::put($path, json_encode([
            'payment_price' => $normalized,
            'updated_at' => now()->toIso8601String(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $normalized;
    }

    private static function readStoredPrice(): ?float
    {
        $path = storage_path(self::SETTINGS_PATH);

        if (! File::exists($path)) {
            return null;
        }

        $decoded = json_decode((string) File::get($path), true);
        $amount = is_array($decoded) ? ($decoded['payment_price'] ?? null) : null;

        if (! is_numeric($amount)) {
            return null;
        }

        return max(1, round((float) $amount, 2));
    }
}
