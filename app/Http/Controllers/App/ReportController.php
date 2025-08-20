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

use App\Helpers\PeriodHelper;
use App\Http\Controllers\Controller;
use App\Models\Party;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return inertia('app/report/Index');
    }

    public function transactionDetailByType(Request $request, string $type)
    {
        $user = Auth::user();
        $period = $request->get('period', 'this_month'); // default this_month
        [$start, $end] = PeriodHelper::resolve(
            $period,
            $request->get('start_date'),
            $request->get('end_date')
        );

        $q = Transaction::with(['category:id,name', 'party:id,name'])
            ->where('user_id', $user->id);

        // tentukan filter berdasarkan tipe
        if ($type === 'receivable') {
            $q->where('amount', '<', 0);
            $titleText = 'Laporan Rincian Transaksi Piutang';
        } elseif ($type === 'payable') {
            $q->where('amount', '>', 0);
            $titleText = 'Laporan Rincian Transaksi Utang';
        } else {
            abort(400, 'Invalid transaction type.');
        }

        if ($start && $end) {
            $q->whereBetween('datetime', [$start, $end]);
        }

        $items = $q->orderBy('datetime', 'desc')->get();

        [$title] = $this->resolveTitle($titleText);

        return $this->generatePdfReport(
            'report.transaction-detail',
            'portrait',
            compact('items', 'user', 'title', 'period', 'start', 'end'),
            'pdf'
        );
    }

    public function receivablesDetail(Request $request)
    {
        return $this->transactionDetailByType($request, 'receivable');
    }

    public function payablesDetail(Request $request)
    {
        return $this->transactionDetailByType($request, 'payable');
    }

    /**
     * Helper function to get generate report based on receivable or payable.
     *
     * @param Request $request
     * @param string $type 'receivable' or 'payable'
     * @return \Illuminate\Support\Collection
     */
    private function transactionByCategoriesRecap(Request $request, string $type)
    {
        $user = Auth::user();
        $period = $request->get('period', 'this_month'); // Default to 'this_month'
        [$start_date, $end_date] = PeriodHelper::resolve(
            $period,
            $request->get('start_date'),
            $request->get('end_date')
        );

        $query = DB::table('transactions')
            ->join('transaction_categories', 'transactions.category_id', '=', 'transaction_categories.id')
            ->select('transaction_categories.name as category_name', DB::raw('SUM(transactions.amount) as total_amount'))
            ->where('transactions.user_id', $user->id)
            ->whereBetween('transactions.datetime', [$start_date, $end_date])
            ->groupBy('transactions.category_id', 'transaction_categories.name')
            ->orderBy('transaction_categories.name', 'asc');

        if ($type === 'receivable') {
            $query->where('transactions.amount', '<', 0); // Filter for receivables (negative amounts)
            [$title] = $this->resolveTitle('Laporan Rekapitulasi Piutang Berdasarkan Kategori');
        } elseif ($type === 'payable') {
            $query->where('transactions.amount', '>', 0); // Filter for payables (positive amounts)
            [$title] = $this->resolveTitle('Laporan Rekapitulasi Utang Berdasarkan Kategori');
        } else {
            abort(400, 'Invalid recap type.');
        }

        $items = $query->get();

        // Ensure total_amount is positive for receivables in the report display
        if ($type === 'receivable') {
            $items->each(function ($item) {
                $item->total_amount = abs($item->total_amount);
            });
        }

        return $this->generatePdfReport(
            'report.transaction-categories-recap',
            'portrait',
            compact('items', 'user', 'period', 'start_date', 'end_date', 'title'),
            'pdf'
        );
    }

    public function receivablesByCategoriesRecap(Request $request)
    {
        return $this->transactionByCategoriesRecap($request, 'receivable');
    }

    public function payablesByCategoriesRecap(Request $request)
    {
        return $this->transactionByCategoriesRecap($request, 'payable');
    }

    public function partiesPayables(Request $request)
    {
        $user = Auth::user();

        $items = Party::where('active', '=', true)
            ->where('balance', '>', '0')
            ->orderBy('name', 'asc')
            ->get();

        [$title] = $this->resolveTitle('Laporan Daftar Utang');

        return $this->generatePdfReport('report.parties-payables-receivables', 'portrait', compact(
            'items',
            'user',
            'title',
        ), 'pdf');
    }

    public function partiesReceivables(Request $request)
    {
        $user = Auth::user();

        $items = Party::where('active', '=', true)
            ->where('balance', '<', '0')
            ->orderBy('name', 'asc')
            ->get();

        [$title] = $this->resolveTitle('Laporan Piutang');

        return $this->generatePdfReport('report.parties-payables-receivables', 'portrait', compact(
            'items',
            'user',
            'title',
        ), 'pdf');
    }

    public function globalBalance(Request $request) {}

    protected function resolveTitle(string $baseTitle, $party_id = 'all'): array
    {
        $title = '';
        $party = null;

        if ($party_id !== 'all') {
            $party = Party::find($party_id);
            $title = "$baseTitle - $party->name ($party->username)";
        } else {
            $title = "$baseTitle - Semua Pihak";
        }

        return [$title, $party];
    }

    protected function generatePdfReport($view, $orientation, $data, $response = 'pdf')
    {
        $filename = env('APP_NAME') . ' - ' . $data['title'];

        if (isset($data['start_date']) || isset($data['end_date'])) {
            if (empty($data['subtitles'])) {
                $data['subtitles'] = [];
            }
            $data['subtitles'][] = 'Periode ' . format_date($data['start_date']) . ' s/d ' . format_date($data['end_date']);
        }

        if ($response == 'pdf') {
            return Pdf::loadView($view, $data)
                ->setPaper('a4', $orientation)
                ->download($filename . '.pdf');
        }

        if ($response == 'html') {
            return view($view, $data);
        }

        throw new Exception('Unknown response type!');
    }
}
