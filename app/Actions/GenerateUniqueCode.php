<?php

namespace App\Actions;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class GenerateUniqueCode
{
    public static function for(string $table, string $column = 'code', int $length = 8): string
    {
        do {
            $code = str_pad((string) random_int(0, (10 ** $length) - 1), $length, '0', STR_PAD_LEFT);
        } while (DB::table($table)->where($column, $code)->exists());

        return $code;
    }
}
