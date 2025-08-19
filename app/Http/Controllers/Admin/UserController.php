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

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function index()
    {
        return inertia('app/user/Index');
    }

    public function detail($id = 0)
    {
        // tambahkan jumlah client yang ditangani oleh user ini
        return inertia('app/user/Detail', [
            'data' => User::findOrFail($id),
        ]);
    }

    public function data(Request $request)
    {
        $orderBy = $request->get('order_by', 'name');
        $orderType = $request->get('order_type', 'asc');
        $filter = $request->get('filter', []);

        // tambahkan jumlah client yang ditangani oleh user ini
        $q = User::query();
        $q->orderBy($orderBy, $orderType);

        if (!empty($filter['role'] && $filter['role'] != 'all')) {
            $q->where('role', '=', $filter['role']);
        }

        if (!empty($filter['status']) && ($filter['status'] == 'active' || $filter['status'] == 'inactive')) {
            $q->where('active', '=', $filter['status'] == 'active' ? true : false);
        }

        if (!empty($filter['search'])) {
            $q->where(function ($query) use ($filter) {
                $query->where('name', 'like', '%' . $filter['search'] . '%')
                    ->orWhere('email', 'like', '%' . $filter['search'] . '%');
            });
        }

        $users = $q->paginate($request->get('per_page', 10))->withQueryString();

        return response()->json($users);
    }

    public function duplicate($id)
    {
        $user = User::findOrFail($id);
        $user->id = null;
        $user->created_at = null;
        return inertia('app/user/Editor', [
            'data' => $user
        ]);
    }

    public function editor($id = 0)
    {
        $user = $id ? User::findOrFail($id) : new User();

        if (!$id) {
            $user->active = true;
            $user->admin = true;
        } else if ($user == Auth::user()) {
            return redirect(route('app.user.index'))->with('warning', 'TIdak dapat mengubah akun anda sendiri.');
        }

        return inertia('app/user/Editor', [
            'data' => $user,
        ]);
    }

    public function save(Request $request)
    {
        $rules = [
            'name' => 'required|max:255',
            'password' => 'required|min:5|max:40',
            'role' => 'required',
        ];

        $user = null;
        $message = '';
        $fields = ['name', 'email', 'role', 'active'];
        $password = $request->get('password');
        if (!$request->id) {
            // email harus unik
            $rules['email'] = "required|alpha_num|max:255|unique:users,email,NULL,id";
            $request->validate($rules);
            $user = new User();
        } else {
            // email harus unik, exclude id
            $rules['email'] = "required|alpha_num|max:255|unique:users,email,{$request->id},id";
            if (empty($request->get('password'))) {
                // kalau password tidak diisi, skip validation dan jangan update password
                unset($rules['password']);
                unset($fields['password']);
            }
            $request->validate($rules);
            $user = User::findOrFail($request->id);
        }

        if (!empty($password)) {
            $user->password = Hash::make($password);
        }
        $user->fill($request->only($fields));
        $user->save();

        $message = "Pengguna {$user->email} telah " . ($request->id ? 'diperbarui' : 'ditambahkan') . '.';

        return redirect(route('app.user.index'))->with('success', $message);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);

        if ($user->id == Auth::user()->id) {
            return response()->json([
                'message' => 'Tidak dapat menghapus akun sendiri!'
            ], 409);
        }

        $user->delete();

        return response()->json([
            'message' => "Pengguna {$user->email} telah dihapus!"
        ]);
    }

    /**
     * Mengekspor daftar pengguna ke dalam format PDF atau Excel.
     */
    public function export(Request $request)
    {
        $items = User::orderBy('name', 'asc')->get();
        $title = 'Daftar Pengguna';
        $filename = $title . ' - ' . env('APP_NAME') . Carbon::now()->format('dmY_His');

        if ($request->get('format') == 'pdf') {
            $pdf = Pdf::loadView('export.user-list-pdf', compact('items', 'title'));
            return $pdf->download($filename . '.pdf');
        }

        if ($request->get('format') == 'excel') {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Tambahkan header
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Username');
            $sheet->setCellValue('C1', 'Nama Lengkap');
            $sheet->setCellValue('D1', 'Status');

            // Tambahkan data ke Excel
            $row = 2;
            foreach ($items as $num => $item) {
                $sheet->setCellValue('A' . $row, $num + 1);
                $sheet->setCellValue('B' . $row, $item->email);
                $sheet->setCellValue('C' . $row, $item->name);
                $sheet->setCellValue('D' . $row, $item->active ? 'Aktif' : 'Tidak Aktif');
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
