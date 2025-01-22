<?php

namespace App\Helpers;

use Closure;
use Exception;
use Illuminate\Support\Facades\Cache;

class CacheHelper {
    public static function cacheWithFallback(string $key, int $cacheTTL, Closure $callback) {
        try {
            return Cache::remember($key, $cacheTTL, $callback);
        } catch (Exception $e) {
            return $callback();
        }
    }
}
