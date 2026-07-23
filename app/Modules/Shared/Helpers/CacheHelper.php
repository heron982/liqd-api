<?php

namespace App\Modules\Shared\Helpers;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    public static function remember(string $key, int $ttlSeconds, callable $callback): mixed
    {
        return Cache::remember($key, $ttlSeconds, $callback);
    }

    public static function put(string $key, mixed $value, int $ttlSeconds): void
    {
        Cache::put($key, $value, $ttlSeconds);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::get($key, $default);
    }

    public static function forget(string $key): void
    {
        Cache::forget($key);
    }
}
