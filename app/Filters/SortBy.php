<?php

namespace App\Filters;

use App\Interfaces\Filters\PipeLineInterface;
use Closure;

class SortBy implements PipeLineInterface {
    public function handle(array $content, Closure $next) {
        if (isset($content['params']['sortBy'])) {
            $content['queryBuilder']->orderBy($content['params']['sortBy'], $content['params']['sortOrder'] ?? 'asc');
        }

        return $next($content);
    }
}
