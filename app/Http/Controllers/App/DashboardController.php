<?php

/**
 * Lunasin - Personal Debt & Credit Management Application
 *
 * Copyright (c) 2025 Fahmi Fauzi Rahman
 * GitHub: https://github.com/ffrz
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
 * USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package   Lunasin
 * @author    Fahmi Fauzi Rahman
 * @license   MIT
 */

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Party;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedPeriod = $request->query('month', 'this_month');
        $startDate = null;
        $endDate = null;

        switch ($selectedPeriod) {
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
                $endDate = Carbon::now()->endOfWeek(Carbon::SUNDAY);
                break;
            case 'prev_week':
                $startDate = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);
                $endDate = Carbon::now()->subWeek()->endOfWeek(Carbon::SUNDAY);
                break;
            case 'prev_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'prev_2month':
                $startDate = Carbon::now()->subMonths(2)->startOfMonth();
                $endDate = Carbon::now()->subMonths(2)->endOfMonth();
                break;
            case 'prev_3month':
                $startDate = Carbon::now()->subMonths(3)->startOfMonth();
                $endDate = Carbon::now()->subMonths(3)->endOfMonth();
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
        }

        $transactionsInPeriodQuery = Transaction::whereBetween('datetime', [$startDate, $endDate]);
        $totalDebt = Party::where('balance', '>', 0)->sum('balance');
        $totalReceivable = abs(Party::where('balance', '<', 0)->sum('balance'));
        $totalTransactions = $transactionsInPeriodQuery->clone()->count();

        $totalParties = Party::count();
        $totalBalance = -Party::sum('balance');
        $topDebtors = Party::where('balance', '<', 0)
            ->orderBy('balance', 'asc')
            ->limit(5)
            ->get(['name', 'balance as value'])
            ->map(function ($debtor) {
                $debtor->value = abs($debtor->value);
                return $debtor;
            });

        $topCreditors = Party::where('balance', '>', 0)
            ->orderBy('balance', 'desc')
            ->limit(5)
            ->get(['name', 'balance as value']);

        $chartStartDate = Carbon::now()->subMonths(5)->startOfMonth();
        $chartEndDate = Carbon::now()->endOfMonth();

        $monthlyTransactionsData = Transaction::selectRaw('DATE_FORMAT(datetime, "%b") as month_label, SUM(amount) as total_amount')
            ->whereBetween('datetime', [$chartStartDate, $chartEndDate])
            ->groupBy('month_label')
            ->orderByRaw('MIN(datetime)')
            ->get();

        $allMonths = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthKey = Carbon::now()->subMonths($i)->startOfMonth()->format('M');
            $allMonths[$monthKey] = 0;
        }

        foreach ($monthlyTransactionsData as $item) {
            $allMonths[$item->month_label] = -$item->total_amount;
        }

        $monthlyLabels = array_keys($allMonths);
        $monthlyData = array_values($allMonths);

        $transactionCategoryDistribution = $transactionsInPeriodQuery->clone()
            ->join('transaction_categories', 'transactions.category_id', '=', 'transaction_categories.id')
            ->selectRaw('transaction_categories.name, COUNT(transactions.id) as value')
            ->groupBy('transaction_categories.name')
            ->get();

        $dashboardData = [
            'summary' => [
                'balance' => $totalBalance,
                'totalParties' => $totalParties,
                'totalDebt' => $totalDebt,
                'totalReceivable' => $totalReceivable,
                'totalTransactions' => $totalTransactions,
            ],
            'topDebtors' => $topDebtors,
            'topCreditors' => $topCreditors,
            'monthlyTransactions' => [
                'labels' => $monthlyLabels,
                'data' => $monthlyData,
            ],
            'transactionCategoryDistribution' => $transactionCategoryDistribution,
        ];

        return inertia('app/dashboard/Index', [
            'data' => $dashboardData,
            'month' => $request->query('month', 'this_month'),
        ]);
    }

    /**
     * This method exists here for testing purpose only.
     */
    public function test()
    {
        return inertia('app/dashboard/Test');
    }
}
