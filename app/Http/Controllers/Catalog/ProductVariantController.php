<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\Product;
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
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'variants' => 'required|array|min:1',
            'variants.*.name' => 'required|string|max:255|unique:product_variants,name',
            'variants.*.sku' => 'required|string|max:50|unique:product_variants,sku',
            'variants.*.barcode' => 'nullable|string|max:255|unique:product_variants,barcode',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.cost_price' => 'required|numeric|min:0',
            'variants.*.weight' => 'required|numeric|min:0',
            'variants.*.weight_unit' => 'required|exists:unit_of_measures,id',
            'variants.*.image' => 'required|image|mimes:jpg,jpeg,png,gif|max:4048',
        ]);

        foreach ($validated['variants'] as $variantData) {
            $variantData['product_id'] = $request->product_id;
            $variantData['created_by'] = Auth::id();
            $variantData['tenant_id'] = 1;

            if (isset($variantData['image'])) {
                $variantData['image_url'] = $variantData['image'];
                $path = $variantData['image_url']->store('variants', 'public');
                $variantData['image_url'] = $path;
            }

            ProductVariant::create($variantData);
        }


        session()->flash('toast', [
            'type' => 'success',
            'message' => __('auth._created'),
        ]);

        return redirect()->route('products.show', $request['product_id']);

    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return view('inventory.product-variant.create', compact('product'));
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

    public function uploadVariantImage(Request $request)
    {
        // Validate the request to ensure the file exists
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', 
            'variant_id' => 'required|integer',
        ]);

        if ($request->hasFile('image')) {
            $product = ProductVariant::find($request['variant_id']);

            $path = $request->file('image')->store('variants', 'public');
            $product->update(['image_url' => $path]);


            // Respond with success if it's an AJAX request
            return response()->json([
                'success' => true,
                'message' => __('auth._uploaded '),
            ]);
        }

        // Return an error response if something goes wrong upload_failed
        return response()->json([
            'success' => false,
            'message' => __('auth.upload_failed '),
        ]);
    }
}
