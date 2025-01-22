<?php

namespace App\Exceptions;

use Exception;

class InactiveFeatureException extends Exception {
    protected $code = 403;

    protected $message = 'Feature is inactive!';
}
