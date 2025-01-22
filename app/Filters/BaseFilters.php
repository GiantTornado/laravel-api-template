<?php

namespace App\Filters;

use Illuminate\Pipeline\Pipeline;

abstract class BaseFilters {
    abstract protected function getFilters(): array;

    public function filter($contents) {
        $results = app(Pipeline::class)
            ->send($contents)
            ->through($this->getFilters())
            ->then(fn ($contents) => $contents['queryBuilder']);

        return $results;
    }
}
