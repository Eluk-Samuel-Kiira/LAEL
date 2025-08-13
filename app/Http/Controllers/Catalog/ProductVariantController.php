<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
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
        dd($request);
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
        $product = ProductVariant::find($id);
        
        $data = $request->validate([
            'name' => [
                'required',
                'max:100',
                Rule::unique('product_variants', 'name')->ignore($product->id),
            ],
            'product_id' => 'required|exists:products,id',
            'sku' => 'required|string|max:50|unique:products,sku,NULL,id,tenant_id,',
            'barcode' => 'required|string',
            'price' => 'required|',
            'cost_price' => 'required|string',
            'weight' => 'required|string',
            'weight_unit' => 'required|integer',
        ]);

        $data['created_by'] = Auth::id();
        $data['tenant_id'] = 1;


        $product->update($data);

        return response()->json([
            'success' => true,
            'reload' => true,
            'refresh' => false,
            'componentId' => 'reloadVariantComponent',
            'message' => __('auth._updated'),
            'redirect' => route('products.show', $data['product_id']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
        $product = ProductVariant::find($id);
        if ($product->is_active === 1) {
            return response()->json([
                'success' => false,
                'message' => __('auth.still_active'),
            ]);
        }

        $product->delete();
        return response()->json([
            'success' => true,
            'reload' => true,
            'refresh' => false,
            'componentId' => 'reloadVariantComponent',
            'message' => __('auth._deleted'),
            'redirect' => route('products.show', $product['product_id']),
        ]);
    }

    public function changeVariantStatus(Request $request, $id) 
    {
        // \Log::info($id);
        // Validate the request data for status
        $validated = $request->validate([
            'status' => 'required|in:1,0',  // Ensures only 'active' or 'inactive' are allowed
        ]);
        
        $product = ProductVariant::find($id);
    
        if ($product) {
            $product->is_active = $validated['status']; 
            if ($product->save()) {  // Save the user object
                return response()->json([
                    'success' => true,
                    'reload' => true,
                    'refresh' => false,
                    'componentId' => 'reloadProductComponent',
                    'message' => __('auth._updated'),
                ]);
            }
        }
    
        // If user not found or status update failed
        return response()->json([
            'success' => false,
            'message' => __('auth._not_found'),
        ]);
    }
}
