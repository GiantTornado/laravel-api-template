<?php

namespace App\Exceptions\Book;

use Exception;

class BookNotFoundException extends Exception {
    protected $code = 404;

    protected $message = 'Book not found!';
}
