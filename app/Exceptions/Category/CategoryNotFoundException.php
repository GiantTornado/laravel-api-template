<?php

namespace App\Exceptions\Category;

use Exception;

class CategoryNotFoundException extends Exception {
    protected $code = 404;

    protected $message = 'Category not found!';
}
