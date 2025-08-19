<?php

/**
 * MIT License
 * 
 * Copyright (c) 2025 Fahmi Fauzi Rahman
 * GitHub: https://github.com/ffrz
 * Email: fahmifauzirahman@gmail.com
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
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
        $selectedPeriod = $request->query('year', 'this_year');
        $startDate = null;
        $endDate = null;

        switch ($selectedPeriod) {
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'prev_year':
                $startDate = Carbon::now()->subYear(1)->startOfYear();
                $endDate = Carbon::now()->subYear(1)->endOfYear();
                break;
            case 'prev_2_year':
                $startDate = Carbon::now()->subYears(2)->startOfYear();
                $endDate = Carbon::now()->subYears(2)->endOfYear();
                break;
            case 'prev_3_year':
                $startDate = Carbon::now()->subYears(3)->startOfYear();
                $endDate = Carbon::now()->subYears(3)->endOfYear();
                break;
            case 'prev_4_year':
                $startDate = Carbon::now()->subYears(4)->startOfYear();
                $endDate = Carbon::now()->subYears(4)->endOfYear();
                break;
            case 'prev_5_year':
                $startDate = Carbon::now()->subYears(5)->startOfYear();
                $endDate = Carbon::now()->subYears(5)->endOfYear();
                break;
            default:
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
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

        // chart Januari - Desember pada tahun terpilih
        $monthlyTransactionsData = Transaction::selectRaw('MONTH(datetime) as month_number, SUM(amount) as total_amount')
            ->whereBetween('datetime', [$startDate, $endDate])
            ->groupBy('month_number')
            ->orderBy('month_number')
            ->get();

        $allMonths = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthKey = Carbon::createFromDate(null, $i, 1)->format('M');
            $allMonths[$monthKey] = 0;
        }

        foreach ($monthlyTransactionsData as $item) {
            $monthKey = Carbon::createFromDate(null, $item->month_number, 1)->format('M');
            $allMonths[$monthKey] = -$item->total_amount;
        }

        $monthlyLabels = array_keys($allMonths);
        $monthlyData = array_values($allMonths);

        $transactionCategoryDistribution = $transactionsInPeriodQuery->clone()
            ->join('transaction_categories', 'transactions.category_id', '=', 'transaction_categories.id')
            ->selectRaw('transaction_categories.name, ABS(SUM(transactions.amount)) as value')
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
            'year' => $request->query('year', 'this_year'),
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
