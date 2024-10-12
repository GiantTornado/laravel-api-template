<?php

namespace App\Filters\Book;

use App\Interfaces\Filters\PipeLineInterface;
use Closure;

class CategoryId implements PipeLineInterface
{
    public function handle(array $content, Closure $next)
    {
        if (isset($content['params']['categoryIds'])) {
            $tableName = $content['queryBuilder']->getModel()->getTable();
            $content['queryBuilder']->whereIn("$tableName.category_id", $content['params']['categoryIds']);
        }

        return $next($content);
    }
}