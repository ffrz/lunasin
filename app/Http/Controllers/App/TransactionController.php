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

use App\Helpers\ImageUploaderHelper;
use App\Http\Controllers\Controller;
use App\Models\Party;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TransactionController extends Controller
{

    public function index()
    {
        return inertia('app/transaction/Index', [
            'parties' => Party::query()->orderBy('name', 'asc')->get(),
            'categories' => TransactionCategory::query()->orderBy('name', 'asc')->get()
        ]);
    }

    public function detail($id = 0)
    {
        $item = Transaction::with(['party:id,name', 'category:id,name'])->findOrFail($id);
        return inertia('app/transaction/Detail', [
            'data' => $item,
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
        return $this->_showEditor($item);
    }

    public function duplicate($id = 0)
    {
        $original = Transaction::findOrFail($id);
        $item = $original->replicate();
        $item->datetime = Carbon::now();
        $item->amount = abs($original->amount);
        return $this->_showEditor($item);
    }

    private function _showEditor($item)
    {
        return inertia('app/transaction/Editor', [
            'data' => $item,
            'parties' => Party::query()->orderBy('name', 'asc')->get(),
            'categories' => TransactionCategory::query()->orderBy('name', 'asc')->get()
        ]);
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'id'          => 'nullable|exists:transactions,id',
            'party_id'    => 'required|exists:parties,id',
            'category_id' => 'required|exists:transaction_categories,id',
            'datetime'    => 'required|date',
            'type'        => 'required|in:' . implode(',', array_keys(Transaction::Types)),
            'amount'      => 'required|numeric|min:0.01',
            'notes'       => 'nullable|string|max:255',
            'image'       => 'nullable|image|max:5120',
            'image_path'  => 'nullable|string',
        ]);

        $validated['notes'] = $validated['notes'] ?? '';
        $normalizedAmount = abs($validated['amount']);

        if (Transaction::isPositiveTransaction($validated['type'])) {
            $normalizedAmount = +$normalizedAmount;
        } elseif (Transaction::isNegativeTransaction($validated['type'])) {
            $normalizedAmount = -$normalizedAmount;
        }

        DB::beginTransaction();
        $newlyUploadedImagePath = null;

        try {
            $party = Party::findOrFail($validated['party_id']);

            $isUpdating = !empty($validated['id']);
            $transaction = $isUpdating ? Transaction::findOrFail($validated['id']) : new Transaction();

            // Simpan path gambar lama sebelum potensi perubahan untuk cleanup jika rollback
            $originalImagePath = $transaction->image_path;

            // --- Logika Update Saldo dan Transaksi Database ---
            if ($isUpdating) {
                // Mode edit
                // Rollback saldo dari transaksi lama
                $party->balance -= $transaction->amount;

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
            }

            unset($validated['image']);
            $validated['image_path'] = '';

            $transaction->fill($validated);
            $transaction->save();

            // --- Logika Penanganan Gambar (SETELAH data transaksi disimpan ke DB) ---
            if ($request->hasFile('image')) {
                // Unggah dan resize gambar baru, hapus gambar lama melalui helper
                $newlyUploadedImagePath = ImageUploaderHelper::uploadAndResize(
                    $request->file('image'),
                    'transactions', // Direktori target
                    $originalImagePath // Path gambar lama untuk dihapus
                );
            } else if ($isUpdating && empty($validated['image_path']) && $originalImagePath) {
                // Jika tidak ada file baru diunggah, tapi path gambar di frontend kosong (berarti user ingin menghapus gambar lama)
                ImageUploaderHelper::deleteImage($originalImagePath);
                $newlyUploadedImagePath = null; // Set path gambar di DB menjadi null
            } else {
                // Jika tidak ada gambar baru dan tidak ada penghapusan, pertahankan path gambar asli
                $newlyUploadedImagePath = $originalImagePath;
            }

            // Perbarui path gambar di model transaksi jika ada perubahan
            if ($transaction->image_path !== $newlyUploadedImagePath) {
                $transaction->image_path = $newlyUploadedImagePath;
                $transaction->save(); // Simpan kembali transaksi untuk memperbarui image_path di DB
            }

            $party->save(); // Simpan perubahan saldo pihak

            DB::commit(); // Commit transaksi database

            return redirect(route('app.transaction.index'))
                ->with('success', "Transaksi $transaction->id telah " . ($isUpdating ? 'diperbarui' : 'disimpan') . ".");
        } catch (\Throwable $e) {
            DB::rollBack(); // Rollback transaksi database jika ada error

            // Jika ada gambar baru yang berhasil di-upload, tapi transaksi DB gagal,
            // maka hapus gambar yang baru di-upload tersebut untuk konsistensi.
            if ($newlyUploadedImagePath && file_exists(public_path($newlyUploadedImagePath))) {
                ImageUploaderHelper::deleteImage($newlyUploadedImagePath);
            }

            throw $e;

            // report($e); // Laporkan exception
            // return back()->withErrors(['error' => 'Gagal menyimpan transaksi.']);
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
