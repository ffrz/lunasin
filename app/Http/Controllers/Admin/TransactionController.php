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
        return inertia('admin/transaction/Index', []);
    }

    public function data(Request $request)
    {
        $orderBy = $request->get('order_by', 'id');
        $orderType = $request->get('order_type', 'desc');
        $filter = $request->get('filter', []);

        $q = Transaction::with('party');

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

        $q->orderBy($orderBy, $orderType);

        $items = $q->paginate($request->get('per_page', 10))->withQueryString();

        return response()->json($items);
    }

    public function editor($id = 0)
    {
        allowed_roles([User::Role_Admin]);
        $item = $id ? Transaction::findOrFail($id) : new Transaction(['datetime' => Carbon::now()]);
        return inertia('admin/transaction/Editor', [
            'data' => $item,
            'parties' => Party::where('active', '=', true)->orderBy('name', 'asc')->get(),
            'categories' => TransactionCategory::query()->orderBy('name', 'asc')->get()
        ]);
    }

    public function save(Request $request)
    {
        $route = 'admin.transaction.index';

        $validated = $request->validate([
            'party_id'    => 'required|exists:parties,id',
            'category_id'    => 'required|exists:transaction_categories,id',
            'datetime'      => 'required|date',
            'type'          => 'required|in:' . implode(',', array_keys(Transaction::Types)),
            'amount'        => 'required|numeric|min:0.01',
            'notes'         => 'nullable|string|max:255',
        ]);

        $validated['notes'] ?? '';

        $amount = $validated['amount'];
        if ($validated['type'] === Transaction::Type_Credit) {
            $amount = -$amount;
        }

        $transaction = new Transaction();
        $transaction->fill($validated);
        $transaction->save();

        $party = $transaction->party;
        $party->balance += $amount;
        $party->save();
        DB::commit();

        return redirect(route($route))
            ->with('success', "Transaksi $transaction->id telah disimpan.");
    }

    public function delete($id)
    {
        allowed_roles([User::Role_Admin]);

        $item = Transaction::findOrFail($id);

        DB::beginTransaction();
        $item->party->balance -= $item->amount;
        $item->party->save();
        $item->delete();

        DB::commit();

        return response()->json([
            'message' => "Transaksi #$item->id telah dihapus."
        ]);
    }
}
