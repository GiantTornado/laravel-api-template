<?php

namespace App\Helpers;

class StringHelper {
    public static function doesContainDigits($str) {
        return preg_match('~[0-9]+~', $str);
    }
}
