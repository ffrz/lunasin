<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\TransactionCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionCategoryController extends Controller
{
    public function index()
    {
        allowed_roles([User::Role_Admin]);
        return inertia('app/transaction-category/Index');
    }

    public function data(Request $request)
    {
        $orderBy = $request->get('order_by', 'date');
        $orderType = $request->get('order_type', 'desc');
        $filter = $request->get('filter', []);

        $q = TransactionCategory::query();

        if (!empty($filter['search'])) {
            $q->where(function ($q) use ($filter) {
                $q->where('name', 'like', '%' . $filter['search'] . '%');
            });
        }

        $q->orderBy($orderBy, $orderType);

        $items = $q->paginate($request->get('per_page', 10))->withQueryString();

        return response()->json($items);
    }

    public function duplicate($id)
    {
        allowed_roles([User::Role_Admin]);
        $item = TransactionCategory::findOrFail($id);
        $item->id = null;
        return inertia('app/transaction-category/Editor', [
            'data' => $item
        ]);
    }

    public function editor($id = 0)
    {
        allowed_roles([User::Role_Admin]);
        $item = $id ? TransactionCategory::findOrFail($id) : new TransactionCategory();
        return inertia('app/transaction-category/Editor', [
            'data' => $item,
        ]);
    }

    public function save(Request $request)
    {
        $item = $request->id ? TransactionCategory::findOrFail($request->id) : new TransactionCategory();

        $validated = $request->validate([
            'name' => [
                'required',
                'max:255',
                Rule::unique('transaction_categories', 'name')->ignore($item->id),
            ],
            'description' => 'nullable|max:1000',
        ]);

        $item->fill([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
        ]);

        $item->save();

        $messageKey = $request->id ? 'transaction-category-updated' : 'transaction-category-created';

        return redirect()
            ->route('app.transaction-category.index')
            ->with('success', __("messages.$messageKey", ['name' => $item->name]));
    }

    public function delete($id)
    {
        allowed_roles([User::Role_Admin]);

        $item = TransactionCategory::findOrFail($id);
        $item->delete();

        return response()->json([
            'message' => __('messages.transaction-category-deleted', ['name' => $item->name])
        ]);
    }
}
