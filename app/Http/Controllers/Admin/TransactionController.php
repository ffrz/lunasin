<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Party;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{

    public function index()
    {
        return inertia('admin/transaction/Index', [
            'parties' => Party::query()->orderBy('name', 'asc')->get(),
            'categories' => TransactionCategory::query()->orderBy('name', 'asc')->get()
        ]);
    }

    public function data(Request $request)
    {
        $orderBy = $request->get('order_by', 'id');
        $orderType = $request->get('order_type', 'desc');
        $filter = $request->get('filter', []);

        $q = Transaction::with(['party', 'category']);

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
        if ($id != 0) {
            allowed_roles([User::Role_Admin]);
        }

        $item = $id ? Transaction::findOrFail($id) : new Transaction(['datetime' => Carbon::now()]);
        $item->amount = abs($item->amount);
        return inertia('admin/transaction/Editor', [
            'data' => $item,
            'parties' => Party::query()->orderBy('name', 'asc')->get(),
            'categories' => TransactionCategory::query()->orderBy('name', 'asc')->get()
        ]);
    }

    public function save(Request $request)
    {
        if ($request->id != 0) {
            allowed_roles([User::Role_Admin]);
        }

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
                return redirect(route('admin.transaction.index'))
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
                return redirect(route('admin.transaction.index'))
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
        allowed_roles([User::Role_Admin]);

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
}
