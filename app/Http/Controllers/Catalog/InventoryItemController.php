<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryItems;
use App\Models\ProductVariant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class InventoryItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $items = InventoryItems::with('variant','itemCreater')->orderBy('department_id', 'asc')->latest()->get();

        $bladeToReload = $request->query('bladeFileToReload');
        switch ($bladeToReload) {
            case 'reloadItemComponent':
                return view('store.inventory-items.component', [
                    'items' => $items,
                ]);
            default:
                return view('store.items-index', [
                    'items' => $items,
                ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'quantity_on_hand' => 'required|integer|min:0',
            'quantity_allocated' => 'required|integer|min:0|lte:quantity_on_hand',
            'preferred_stock_level' => 'required|integer|min:0',
            'department_id' => 'required|integer',
            'batch_number' => 'nullable|string|max:50',
            'expiry_date' => 'nullable|date|after_or_equal:today',
            
            'variant_id' => [
                'required',
                'exists:product_variants,id',
                Rule::unique('inventory_items')->where(fn ($q) => $q
                    ->where('variant_id', $request->variant_id) 
                    ->where('batch_number', $request->batch_number))
            ],
        ], [
            'quantity_allocated.lte' => __('pagination.allocated_not_greater_than_at_hand'),
        ]);

        $data['created_by'] = Auth::id();
        $data['tenant_id']  = 1;

        if (empty($data['batch_number'])) {
            $data['batch_number'] = strtoupper('BTH-' . strtoupper(Str::random(6)));
        }

        InventoryItems::create($data);

        return response()->json([
            'success' => true,
            'reload' => true,
            'refresh' => false,
            'componentId' => 'reloadItemComponent',
            'message' => __('auth._created'),
            'redirect' => route('items.index'),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = InventoryItems::find($id);
        $validated = $request->validate([
            'department_id'       => ['required','exists:departments,id'],
            'expiry_date'         => ['nullable','date','after_or_equal:today'],
            'quantity_on_hand'    => ['required','integer','min:0'],
            'quantity_allocated'  => ['required','integer','min:0','lte:quantity_on_hand'],
        ], [
            'quantity_allocated.lte' => __('pagination.allocated_not_greater_than_at_hand'),
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'reload' => true,
            'refresh' => false,
            'componentId' => 'reloadItemComponent',
            'message' => __('auth._updated'),
            'redirect' => route('items.index'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = InventoryItems::find($id);

        $item->delete();

        return response()->json([
            'success' => true,
            'reload' => true,
            'refresh' => false,
            'componentId' => 'reloadItemComponent',
            'message' => __('auth._deleted'),
            'redirect' => route('items.index'),
        ]);
    }
}
