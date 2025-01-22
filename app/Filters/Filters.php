<?php

namespace App\Filters;

class Filters extends BaseFilters {
    public function __construct(public $filters = null) {
        $this->filters = $filters;
    }

    public static function apply($filters) {
        return new Filters($filters);
    }

    protected function getFilters(): array {
        return $this->filters;
    }
}
