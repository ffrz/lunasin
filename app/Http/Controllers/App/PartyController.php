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

namespace App\Http\Controllers\App;

use App\Helpers\JsonResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Party;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Nette\NotImplementedException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PartyController extends Controller
{
    public function index()
    {
        return inertia('app/party/Index');
    }

    public function detail($id = 0)
    {
        return inertia('app/party/Detail', [
            'data' => Party::with([
                'created_by_user:id,name',
                'updated_by_user:id,name',
            ])->findOrFail($id),
        ]);
    }

    public function data(Request $request)
    {
        $orderBy = $request->get('order_by', 'name');
        $orderType = $request->get('order_type', 'asc');
        $filter = $request->get('filter', []);

        $q = Party::query();

        if (!empty($filter['search'])) {
            $q->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%' . $filter['search'] . '%');
                $q->orWhere('phone', 'like', '%' . $filter['search'] . '%');
                $q->orWhere('address', 'like', '%' . $filter['search'] . '%');
                $q->orWhere('notes', 'like', '%' . $filter['search'] . '%');
            });
        }

        if (!empty($filter['status']) && (in_array($filter['status'], ['active', 'inactive']))) {
            $q->where('active', '=', $filter['status'] == 'active' ? true : false);
        }

        if (!empty($filter['type']) && (in_array($filter['type'], array_keys(Party::Types)))) {
            $q->where('type', '=', $filter['type']);
        }

        $q->orderBy($orderBy, $orderType);

        $items = $q->paginate($request->get('per_page', 10))->withQueryString();

        return response()->json($items);
    }

    public function duplicate($id)
    {
        $item = Party::findOrFail($id);
        $item->id = null;
        $item->created_at = null;
        return inertia('app/party/Editor', [
            'data' => $item,
        ]);
    }

    public function editor($id = 0)
    {
        $item = $id ? Party::findOrFail($id) : new Party(['active' => true]);
        return inertia('app/party/Editor', [
            'data' => $item,
        ]);
    }

    public function save(Request $request)
    {
        $validated =  $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'nullable|string|max:50',
            'type'    => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'active'  => 'required|boolean',
            'notes'   => 'nullable|string',
        ]);

        $item = !$request->id ? new Party() : Party::findOrFail($request->post('id', 0));
        $item->fill($validated);
        $item->save();

        if ($request->response == 'json') {
            return response()->json([
                'data' => $item,
                'message' => 'Pihak telah disimpan.',
            ]);
        }

        return redirect(route('app.party.detail', ['id' => $item->id]))->with('success', "Pelanggan $item->name telah disimpan.");
    }

    public function delete($id)
    {
        $item = Party::findOrFail($id);
        try {
            if (Party::isUsedInTransaction($id)) {
                return JsonResponseHelper::error($item->name . ' tidak dapat dihapus karena sudah dipakai di transaksi.', 402);
            }

            $item->delete();

            return JsonResponseHelper::success(
                $item,
                "Pihak $item->name telah dihapus."
            );
        } catch (Exception $e) {
            return JsonResponseHelper::error('Rekaman tidak dapat dihapus.', 402, $e);
        }
    }

    /**
     * Mengekspor daftar pihak ke dalam format PDF atau Excel.
     */
    public function export(Request $request)
    {
        $items = Party::orderBy('name', 'asc')->get();

        $title = 'Daftar Pihak';
        $filename = $title . ' - ' . env('APP_NAME') . Carbon::now()->format('dmY_His');

        if ($request->get('format') == 'pdf') {
            $pdf = Pdf::loadView('export.party-list-pdf', compact('items', 'title'))
                ->setPaper('a4', 'landscape');
            return $pdf->download($filename . '.pdf');
        }

        if ($request->get('format') == 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Tambahkan header
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Jenis');
            $sheet->setCellValue('C1', 'Nama');
            $sheet->setCellValue('D1', 'Telepon');
            $sheet->setCellValue('E1', 'Alamat');
            $sheet->setCellValue('F1', 'Status');
            $sheet->setCellValue('G1', 'Utang / Piutang (Rp)');
            $sheet->setCellValue('H1', 'Catatan');

            // Tambahkan data ke Excel
            $row = 2;
            foreach ($items as $num => $item) {
                $sheet->setCellValue('A' . $row, $num + 1);
                $sheet->setCellValue('B' . $row, $item->type);
                $sheet->setCellValue('C' . $row, $item->name);
                $sheet->setCellValue('D' . $row, $item->phone);
                $sheet->setCellValue('E' . $row, $item->address);
                $sheet->setCellValue('F' . $row, $item->active ? 'Aktif' : 'Tidak Aktif');
                $sheet->setCellValue('G' . $row, $item->balance);
                $sheet->setCellValue('H' . $row, $item->notes);
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
