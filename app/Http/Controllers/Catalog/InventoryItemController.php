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
        
        // Retrieve only those items that have its variants is active
        $items = InventoryItems::with(['variant', 'itemCreater', 'itemLocation'])
                ->whereHas('variant', function ($query) {
                $query->where('is_active', 1);
            })
            ->orderBy('department_id', 'asc')
            ->latest()
            ->get();


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
        // Fetch the variant first
        $variant = \DB::table('product_variants')
            ->where('id', $request->variant_id)
            ->first();

        $data = $request->validate([
            'quantity_on_hand'     => 'required|integer|min:0',
            'quantity_allocated'   => [
                    'required',
                    'integer',
                    'min:0',
                    // ✅ Custom rule to check DB stock
                    function ($attribute, $value, $fail) use ($variant) {
                        if ($value > $variant->overal_quantity_at_hand) {
                            $fail(__('pagination.allocated_not_greater_than_at_hand'));
                        }
                    }
                ],
            'preferred_stock_level'=> 'required|integer|min:0',
            'department_id'        => 'required|integer|exists:departments,id',
            'location_id'          => 'required|integer|exists:locations,id',
            'batch_number'         => 'nullable|string|max:50',
            'expiry_date'          => 'nullable|date|after_or_equal:today',
            'variant_id'           => [
                'required',
                'exists:product_variants,id',
                Rule::unique('inventory_items')->where(fn ($q) => $q
                    ->where('variant_id', $request->variant_id)
                    ->where('department_id', $request->department_id)
                    ->where('location_id', $request->location_id)
                ),
            ],
        ], [
            'variant_id.unique' => __('pagination.variant_already_exists_in_department_location'),
        ]);

        $data['created_by'] = Auth::id();
        $data['tenant_id']  = 1;

        if (empty($data['batch_number'])) {
            $data['batch_number'] = strtoupper('BTH-' . strtoupper(Str::random(6)));
        }

        InventoryItems::create($data);

        if ($variant) {
            \DB::table('product_variants')
                ->where('id', $data['variant_id'])
                ->update([
                    'overal_quantity_at_hand' => $variant->overal_quantity_at_hand - $data['quantity_allocated'],
                ]);
        }


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
        $item = InventoryItems::with('variant')->findOrFail($id);

        // Fetch live variant stock from DB
        $variant = $item->variant;

        // Validate request
        $validated = $request->validate([
            'department_id'       => ['required', 'exists:departments,id'],
            'location_id'         => ['required','integer','exists:locations,id'],
            'expiry_date'         => ['nullable','date','after_or_equal:today'],
            'quantity_on_hand'    => ['required','integer','min:0'],
            'quantity_allocated'  => [
                'required',
                'integer',
                'min:0',
                // ✅ Validate against actual variant stock
                function ($attribute, $value, $fail) use ($variant, $item) {
                    // Calculate available stock including current item's allocation
                    $available = $variant->overal_quantity_at_hand + $item->quantity_allocated;
                    if ($value > $available) {
                        $fail(__('pagination.allocated_not_greater_than_at_hand'));
                    }
                }
            ],
            // Uniqueness for variant + location + department
            Rule::unique('inventory_items')
                ->where(fn($q) => $q
                    ->where('variant_id', $item->variant_id)
                    ->where('department_id', $request->department_id)
                    ->where('location_id', $request->location_id)
                )
                ->ignore($item->id),
        ], [
            'inventory_items_variant_id_unique' => __('pagination.variant_already_exists_in_department_location'),
        ]);

        // Update inventory item
        $item->update($validated);

        // Adjust variant stock manually
        if ($variant) {
            // Add back old allocation, then subtract new allocation
            $variant->overal_quantity_at_hand = 
                ($variant->overal_quantity_at_hand + $item->quantity_allocated) - $validated['quantity_allocated'];

            $variant->save();
        }

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
