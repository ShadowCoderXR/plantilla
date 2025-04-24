<?php

namespace App\Helpers;

class Util
{
    public static function slugify(string $string): string
    {
        $map = [
            'Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U',
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u',
        ];
        $string = strtr($string, $map);
        $string = preg_replace('/[^A-Za-z0-9]+/', '_', $string);
        $string = preg_replace('/_+/', '_', $string);
        return strtolower(trim($string, '_'));
    }
}
