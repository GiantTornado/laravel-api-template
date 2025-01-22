<?php

namespace App\Exceptions\User;

use Exception;

class UserNotFoundException extends Exception {
    protected $code = 404;

    protected $message = 'Category not found!';
}
