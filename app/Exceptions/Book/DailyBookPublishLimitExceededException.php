<?php

namespace App\Exceptions\Book;

use Exception;

class DailyBookPublishLimitExceededException extends Exception {
    protected $code = 422;

    protected $message = 'The daily limit for publishing books has been exceeded!';
}
