<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class CheckoutExitOfferSettings
{
    private const SETTINGS_PATH = 'app/settings/checkout-exit-offer.json';

    public static function enabled(): bool
    {
        $stored = self::readStoredSettings();

        if (is_array($stored) && array_key_exists('enabled', $stored)) {
            return (bool) $stored['enabled'];
        }

        return true;
    }

    public static function setEnabled(bool $enabled): bool
    {
        $path = storage_path(self::SETTINGS_PATH);
        $directory = dirname($path);

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        File::put($path, json_encode([
            'enabled' => $enabled,
            'updated_at' => now()->toIso8601String(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $enabled;
    }

    private static function readStoredSettings(): ?array
    {
        $path = storage_path(self::SETTINGS_PATH);

        if (! File::exists($path)) {
            return null;
        }

        $decoded = json_decode((string) File::get($path), true);

        return is_array($decoded) ? $decoded : null;
    }
}
