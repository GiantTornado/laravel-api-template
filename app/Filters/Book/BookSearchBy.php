<?php

namespace App\Filters\Book;

use App\Interfaces\Filters\PipeLineInterface;
use Closure;

class BookSearchBy implements PipeLineInterface
{
    public function handle(array $content, Closure $next)
    {
        if (isset($content['params']['searchBy'])) {
            $tableName = $content['queryBuilder']->getModel()->getTable();
            $searchBy = $content['params']['searchBy'];
            $content['queryBuilder']->where(
                fn($query) =>
                $query->where("$tableName.title", 'like', "%$searchBy%")
                    ->orWhere("$tableName.description", 'like', "%$searchBy%")
                    ->orWhereHas('category', fn($categoryQuery) => $categoryQuery->where('name', 'like', "%$searchBy%"))
            );
        }

        return $next($content);
    }
}
