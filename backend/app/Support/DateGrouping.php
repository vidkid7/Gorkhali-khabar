<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

final class DateGrouping
{
    public static function day(string $column = 'created_at'): string
    {
        return match (DB::connection()->getDriverName()) {
            'mysql', 'mariadb' => "DATE({$column})",
            'sqlite' => "date({$column})",
            default => "date_trunc('day', {$column})",
        };
    }

    public static function hour(string $column = 'created_at'): string
    {
        return match (DB::connection()->getDriverName()) {
            'mysql', 'mariadb' => "HOUR({$column})",
            'sqlite' => "CAST(strftime('%H', {$column}) AS INTEGER)",
            default => "extract(hour from {$column})",
        };
    }
}
