<?php

/**
 * MIT License
 * 
 * Copyright (c) 2025 Fahmi Fauzi Rahman
 * See LICENSE file in the project root for full license information.
 * 
 * GitHub: https://github.com/ffrz
 * Email: fahmifauzirahman@gmail.com
 */

namespace App\Helpers;

use Carbon\Carbon;

class PeriodHelper
{
    /**
     * Resolve predefined period into start & end Carbon dates.
     *
     * @param string $period
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array [Carbon|null $start, Carbon|null $end]
     */
    public static function resolve(string $period, ?string $startDate = null, ?string $endDate = null): array
    {
        $now = Carbon::now();

        switch ($period) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end   = $now->copy()->endOfDay();
                break;

            case 'yesterday':
                $start = $now->copy()->subDay()->startOfDay();
                $end   = $now->copy()->subDay()->endOfDay();
                break;

            case 'this_week':
                $start = $now->copy()->startOfWeek();
                $end   = $now->copy()->endOfWeek();
                break;

            case 'last_week':
                $start = $now->copy()->subWeek()->startOfWeek();
                $end   = $now->copy()->subWeek()->endOfWeek();
                break;

            case 'this_month':
                $start = $now->copy()->startOfMonth();
                $end   = $now->copy()->endOfMonth();
                break;

            case 'last_month':
                $start = $now->copy()->subMonth()->startOfMonth();
                $end   = $now->copy()->subMonth()->endOfMonth();
                break;

            case 'this_year':
                $start = $now->copy()->startOfYear();
                $end   = $now->copy()->endOfYear();
                break;

            case 'last_year':
                $start = $now->copy()->subYear()->startOfYear();
                $end   = $now->copy()->subYear()->endOfYear();
                break;

            case 'last_7_days':
                $start = $now->copy()->subDays(6)->startOfDay(); // termasuk hari ini
                $end   = $now->copy()->endOfDay();
                break;

            case 'last_30_days':
                $start = $now->copy()->subDays(29)->startOfDay();
                $end   = $now->copy()->endOfDay();
                break;

            case 'custom':
                $start = $startDate ? Carbon::parse($startDate)->startOfDay() : null;
                $end   = $endDate ? Carbon::parse($endDate)->endOfDay() : null;
                break;

            default:
                // fallback: seluruh waktu
                $start = null;
                $end   = null;
                break;
        }

        return [$start, $end];
    }
}
