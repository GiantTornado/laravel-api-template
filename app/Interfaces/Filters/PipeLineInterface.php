<?php

namespace App\Interfaces\Filters;

use Closure;

interface PipeLineInterface {
    public function handle(array $content, Closure $next);
}
