<?php

namespace App\Helpers;

class Util
{
    public static function slugify(string $string): string
    {
        return trim(preg_replace(
            ['/[^a-z0-9_]/', '/_+/'],
            ['_', '_'],
            strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', str_replace(' ', '_', $string)))
        ), '_');
    }
}
