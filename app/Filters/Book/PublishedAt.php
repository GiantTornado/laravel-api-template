<?php

namespace App\Filters\Book;

use App\Interfaces\Filters\PipeLineInterface;
use Closure;

class PublishedAt implements PipeLineInterface
{
    public function handle(array $content, Closure $next)
    {
        $tableName = $content['queryBuilder']->getModel()->getTable();

        if (isset($content['params']['publishStartDate'])) {
            $content['queryBuilder']->whereDate("{$tableName}.published_at", '>=', $content['params']['publishStartDate']);
        }

        if (isset($content['params']['publishEndDate'])) {
            $content['queryBuilder']->whereDate("{$tableName}.published_at", '<=', $content['params']['publishEndDate']);
        }

        return $next($content);
    }
}