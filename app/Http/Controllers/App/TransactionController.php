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
use App\Models\TransactionCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionController extends Controller
{

    public function index()
    {
        return inertia('app/transaction/Index', [
            'parties' => Party::query()->orderBy('name', 'asc')->get(),
            'categories' => TransactionCategory::query()->orderBy('name', 'asc')->get()
        ]);
    }

    public function data(Request $request)
    {
        $orderBy = $request->get('order_by', 'id');
        $orderType = $request->get('order_type', 'desc');
        $filter = $request->get('filter', []);

        $q = Transaction::with(['party:id,name', 'category:id,name']);

        if (!empty($filter['search'])) {
            $q->where(function ($q) use ($filter) {
                $q->orWhere('notes', 'like', '%' . $filter['search'] . '%');
            });
        }

        if (!empty($filter['year']) && $filter['year'] !== 'all') {
            $q->whereYear('datetime', $filter['year']);

            if (!empty($filter['month']) && $filter['month'] !== 'all') {
                $q->whereMonth('datetime', $filter['month']);
            }
        }

        if (!empty($filter['category_id']) && $filter['category_id'] !== 'all') {
            $q->where('category_id', '=', $filter['category_id']);
        }

        if (!empty($filter['party_id']) && $filter['party_id'] !== 'all') {
            $q->where('party_id', '=', $filter['party_id']);
        }

        if (!empty($filter['type']) && $filter['type'] !== 'all') {
            $q->where('type', '=', $filter['type']);
        }

        $q->orderBy($orderBy, $orderType);

        $items = $q->paginate($request->get('per_page', 10))->withQueryString();

        return response()->json($items);
    }

    public function editor($id = 0)
    {
        $item = $id ? Transaction::findOrFail($id) : new Transaction(['datetime' => Carbon::now()]);
        $item->amount = abs($item->amount);
        return inertia('app/transaction/Editor', [
            'data' => $item,
            'parties' => Party::query()->orderBy('name', 'asc')->get(),
            'categories' => TransactionCategory::query()->orderBy('name', 'asc')->get()
        ]);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'id'          => 'nullable|exists:transactions,id', // tambahkan ini
            'party_id'    => 'required|exists:parties,id',
            'category_id' => 'required|exists:transaction_categories,id',
            'datetime'    => 'required|date',
            'type'        => 'required|in:' . implode(',', array_keys(Transaction::Types)),
            'amount'      => 'required|numeric|min:0.01',
            'notes'       => 'nullable|string|max:255',
        ]);

        $validated['notes'] = $validated['notes'] ?? '';
        $normalizedAmount = abs($validated['amount']);

        if (Transaction::isPositiveTransaction($validated['type'])) {
            $normalizedAmount = +$normalizedAmount;
        } elseif (Transaction::isNegativeTransaction($validated['type'])) {
            $normalizedAmount = -$normalizedAmount;
        }

        DB::beginTransaction();
        try {
            $party = Party::findOrFail($validated['party_id']);

            if (!empty($validated['id'])) {
                // Mode edit
                $existingTx = Transaction::findOrFail($validated['id']);

                // Rollback saldo dari transaksi lama
                $party->balance -= $existingTx->amount;

                // Kalkulasi transaksi baru
                if ($validated['type'] === Transaction::Type_Adjustment) {
                    $adjustmentDelta = $normalizedAmount - $party->balance;

                    if (abs($adjustmentDelta) < 1e-6) {
                        DB::rollBack();
                        return back()->withErrors(['error' => 'Tidak ada perubahan saldo untuk disesuaikan.']);
                    }

                    $validated['amount'] = $adjustmentDelta;
                    $party->balance = $normalizedAmount;
                } else {
                    $validated['amount'] = $normalizedAmount;
                    $party->balance += $normalizedAmount;
                }

                $existingTx->fill($validated);
                $existingTx->save();

                $party->save();

                DB::commit();
                return redirect(route('app.transaction.index'))
                    ->with('success', "Transaksi $existingTx->id telah diperbarui.");
            } else {
                // Mode create
                if ($validated['type'] === Transaction::Type_Adjustment) {
                    $adjustmentDelta = $normalizedAmount - $party->balance;

                    if (abs($adjustmentDelta) < 1e-6) {
                        DB::rollBack();
                        return back()->withErrors(['error' => 'Tidak ada perubahan saldo untuk disesuaikan.']);
                    }

                    $validated['amount'] = $adjustmentDelta;
                    $party->balance = $normalizedAmount;
                } else {
                    $validated['amount'] = $normalizedAmount;
                    $party->balance += $normalizedAmount;
                }

                $transaction = new Transaction();
                $transaction->fill($validated);
                $transaction->save();

                $party->save();

                DB::commit();
                return redirect(route('app.transaction.index'))
                    ->with('success', "Transaksi $transaction->id telah disimpan.");
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withErrors(['error' => 'Gagal menyimpan transaksi.']);
        }
    }


    public function delete($id)
    {
        $item = Transaction::findOrFail($id);

        DB::beginTransaction();
        try {
            $party = $item->party;
            $party->balance -= $item->amount; // bisa positif atau negatif
            $party->save();

            $item->delete();

            DB::commit();

            return response()->json([
                'message' => "Transaksi #$item->id telah dihapus."
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return response()->json([
                'message' => 'Gagal menghapus transaksi.'
            ], 500);
        }
    }

    /**
     * Mengekspor daftar kategori ke dalam format PDF atau Excel.
     */
    public function export(Request $request)
    {
        $items = Transaction::orderBy('datetime', 'desc')->get();
        $title = 'Daftar Transaksi';
        $filename = $title . ' - ' . env('APP_NAME') . Carbon::now()->format('dmY_His');

        if ($request->get('format') == 'pdf') {
            $pdf = Pdf::loadView('export.transaction-list-pdf', compact('items', 'title'));
            return $pdf->download($filename . '.pdf');
        }

        if ($request->get('format') == 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Tambahkan header
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Waktu');
            $sheet->setCellValue('C1', 'Pihak');
            $sheet->setCellValue('D1', 'Jenis');
            $sheet->setCellValue('E1', 'Kategori');
            $sheet->setCellValue('F1', 'Jumlah');
            $sheet->setCellValue('G1', 'Catatan');

            // Tambahkan data ke Excel
            $row = 2;
            foreach ($items as $item) {
                $sheet->setCellValue('A' . $row, $item->id);
                $sheet->setCellValue('B' . $row, $item->datetime);
                $sheet->setCellValue('C' . $row, $item->party?->name);
                $sheet->setCellValue('D' . $row, Transaction::Types[$item->type]);
                $sheet->setCellValue('E' . $row, $item->category?->name);
                $sheet->setCellValue('F' . $row, $item->amount);
                $sheet->setCellValue('G' . $row, $item->notes);
                $row++;
            }

            // Kirim ke memori tanpa menyimpan file
            $response = new StreamedResponse(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            });

            // Atur header response untuk download
            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '.xlsx"');

            return $response;
        }

        return abort(400, 'Format tidak didukung');
    }
}
