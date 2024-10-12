<?php

namespace App\Filters\Book;

use App\Filters\BaseFilters;
use App\Filters\Book\BookSearchBy;
use App\Filters\Book\CategoryId;

class BookFilters extends BaseFilters
{
    public function __construct(public $filters = null)
    {
        $this->filters = $filters ?? [
            CategoryId::class,
            PublishedAt::class,
            BookSearchBy::class,
        ];
    }

    protected function getFilters(): array
    {
        return $this->filters;
    }
}