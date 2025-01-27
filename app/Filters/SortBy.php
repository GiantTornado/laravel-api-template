<?php

namespace App\Filters;

use App\Interfaces\Filters\PipeLineInterface;
use Closure;

class SortBy implements PipeLineInterface {
    public function handle(array $content, Closure $next) {
        if (isset($content['params']['sort_by'])) {
            $content['queryBuilder']->orderBy($content['params']['sort_by'], $content['params']['sort_order'] ?? 'asc');
        }

        return $next($content);
    }
}
