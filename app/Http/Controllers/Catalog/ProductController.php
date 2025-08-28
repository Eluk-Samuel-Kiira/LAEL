<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::with('category','productCreater')->latest()->get();

        $bladeToReload = $request->query('bladeFileToReload');
        switch ($bladeToReload) {
            case 'reloadProductComponent':
                return view('inventory.product.component', [
                    'products' => $products,
                ]);
            default:
                return view('inventory.product-index', [
                    'products' => $products,
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
            'category_id' => 'nullable|exists:product_categories,id',
            'sku' => 'required|string|max:50|unique:products,sku',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'type' => 'required|in:physical,digital,service,composite',
            'is_taxable' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['created_by'] = Auth::id();
        $data['tenant_id'] = 1;

        Product::create($data);

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadProductComponent',
            'refresh' => false,
            'message' => __('auth._created'),
            'redirect' => route('products.index'),
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
       // Fetch product and its variants in one query
        $product_variants = Product::with('variants')->where('id', $id)->firstOrFail();
        
        $bladeToReload = $request->query('bladeFileToReload');
        switch ($bladeToReload) {
            case 'reloadVariantComponent':
                return view('inventory.product-variant.component', compact('product_variants'));
            default:
                return view('inventory.product-variant.index', compact('product_variants'));
        }


        
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
        $product = Product::find($id);
        $data = $request->validate([
            'name' => [
                'required',
                'max:100',
                Rule::unique('products', 'name')->ignore($product->id),
            ],
            'category_id' => 'nullable|exists:product_categories,id',
            'sku' => 'required|string|max:50|unique:products,sku',
            'description' => 'nullable|string',
            'type' => 'required|in:physical,digital,service,composite',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['created_by'] = Auth::id();
        $data['tenant_id'] = 1;


        $product->update($data);

        return response()->json([
            'success' => true,
            'reload' => true,
            'refresh' => false,
            'componentId' => 'reloadProductComponent',
            'message' => __('auth._updated'),
            'redirect' => route('products.index'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
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
            'componentId' => 'reloadProductComponent',
            'refresh' => false,
            'message' => __('auth._deleted'),
            'redirect' => route('products.index'),
        ]);
    }

    
    public function changeProductStatus(Request $request, $id) 
    {
        // Validate the request data for status
        $validated = $request->validate([
            'status' => 'required|in:1,0',  // Ensures only 'active' or 'inactive' are allowed
        ]);
        
        $product = Product::find($id);
    
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

    
    public function changeProductTaxStatus(Request $request, $id) 
    {
        // Validate the request data for status
        $validated = $request->validate([
            'status' => 'required|in:1,0',  // Ensures only 'active' or 'inactive' are allowed
        ]);
        
        $product = Product::find($id);
    
        if ($product) {
            $product->is_taxable = $validated['status']; 
            $product->save();
            if ($validated['status'] == 1) {  
                $message = __('pagination.taxable_now');
            } else {
                $message = __('pagination.not_taxable');
            }
            return response()->json([
                'success' => true,
                'reload' => true,
                'refresh' => false,
                'componentId' => 'reloadProductComponent',
                'message' => $message,
            ]);
        }
    
        // If user not found or status update failed
        return response()->json([
            'success' => false,
            'message' => __('auth._not_found'),
        ]);
    }

    public function uploadProductImage(Request $request)
    {
        // Validate the request to ensure the file exists
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', 
            'product_id' => 'required|integer',
        ]);

        if ($request->hasFile('photo')) {
            $product = Product::find($request['product_id']);

            $path = $request->file('photo')->store('products', 'public');
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

    public function updateLocationsDepartments(Request $request, Product $product)
    {
        // \Log::info($request->all());
        $validated = $request->validate([
            'departments' => ['nullable', 'array'],
            'departments.*' => ['exists:departments,id'],
            'locations' => ['nullable', 'array'],
            'locations.*' => ['exists:locations,id'],
        ]);

        // Sync departments
        $product->departments()->sync($validated['departments'] ?? []);

        // Sync locations
        $product->locations()->sync($validated['locations'] ?? []);

        session()->flash('toast', [
            'type' => 'success',
            'message' => __('auth._updated'),
        ]);

        return redirect()->back();
    }


}
