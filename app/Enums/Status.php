<?php

namespace App\Enums;

enum Status:string
{
    case rec = "W3C Recommendation";
    case pr = "W3C Proposed Recommendation";
    case cr = "W3C Candidate Recommendation";
    case wd = "W3C Working Draft";
    case ls = "WHATWG Living Standard";
    case other = "Other";
    case unoff = "Unofficial / Note";

    public static function names(): array
    {
       return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
       return array_column(self::cases(), 'value');
    }
}
