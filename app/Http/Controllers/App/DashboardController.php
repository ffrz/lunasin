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
        $selectedPeriod = $request->query('month', 'this_month'); // Menggunakan 'month' sebagai parameter umum untuk periode
        $currentUserId = auth()->id(); // Pastikan ID user yang sedang login tersedia

        $startDate = null;
        $endDate = null;

        // Menentukan rentang tanggal berdasarkan opsi filter yang dipilih
        switch ($selectedPeriod) {
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'this_week':
                // Perhatikan: Carbon::MONDAY atau Carbon::SUNDAY tergantung definisi awal minggu Anda
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
            default: // Default ke bulan ini jika opsi tidak dikenal
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
        }

        // Statistik Ringkasan (total utang, piutang, transaksi) akan difilter berdasarkan periode yang dipilih
        // Query builder untuk transaksi dengan filter tanggal
        $transactionsInPeriodQuery = Transaction::whereBetween('datetime', [$startDate, $endDate]);

        $totalDebt = $transactionsInPeriodQuery->clone()->where('type', Transaction::Type_Credit)->sum('amount');
        $totalReceivable = $transactionsInPeriodQuery->clone()->where('type', Transaction::Type_Debt)->sum('amount');
        $totalTransactions = $transactionsInPeriodQuery->clone()->count();

        // Data Ringkasan "Big Numbers" yang tidak bergantung pada filter periode tunggal:
        // Ini adalah status keseluruhan saat ini, bukan berdasarkan periode tertentu.
        $totalParties = Party::count();
        $totalBalance = Party::sum('balance');

        // // Data dummy untuk jatuh tempo (karena tidak ada kolom di tabel Transaction)
        // $receivableDue = 3; // Contoh: jumlah piutang yang sudah jatuh tempo
        // $debtDue = 5; // Contoh: jumlah utang yang sudah jatuh tempo

        // Top 5 Peminjam (Utang Terbesar) dan Top 5 Pemberi Pinjaman (Piutang Terbesar)
        // Data ini biasanya berdasarkan saldo saat ini, bukan filtered per periode.
        $topDebtors = Party::where('balance', '<', 0)
            ->orderBy('balance', 'asc') // Urutkan dari saldo paling negatif (utang terbesar)
            ->limit(5)
            ->get(['name', 'balance as value'])
            ->map(function ($debtor) {
                $debtor->value = abs($debtor->value); // Mengambil nilai absolut untuk tampilan
                return $debtor;
            });

        $topCreditors = Party::where('balance', '>', 0)
            ->orderBy('balance', 'desc') // Urutkan dari saldo paling positif (piutang terbesar)
            ->limit(5)
            ->get(['name', 'balance as value']);

        // Grafik Transaksi Bulanan: Tetap menampilkan 6 bulan terakhir sebagai tren, tidak terpengaruh filter periode tunggal
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
            $allMonths[$item->month_label] = $item->total_amount;
        }

        $monthlyLabels = array_keys($allMonths);
        $monthlyData = array_values($allMonths);

        // Distribusi Kategori Transaksi: Difilter berdasarkan periode yang dipilih
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
