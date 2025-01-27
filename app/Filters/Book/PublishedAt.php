<?php

namespace App\Filters\Book;

use App\Interfaces\Filters\PipeLineInterface;
use Closure;

class PublishedAt implements PipeLineInterface {
    public function handle(array $content, Closure $next) {
        $tableName = $content['queryBuilder']->getModel()->getTable();

        if (isset($content['params']['publish_start_date'])) {
            $content['queryBuilder']->whereDate("{$tableName}.published_at", '>=', $content['params']['publish_start_date']);
        }

        if (isset($content['params']['publish_end_date'])) {
            $content['queryBuilder']->whereDate("{$tableName}.published_at", '<=', $content['params']['publish_end_date']);
        }

        return $next($content);
    }
}
