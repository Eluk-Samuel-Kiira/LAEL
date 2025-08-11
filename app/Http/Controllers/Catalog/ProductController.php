<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
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
            'sku' => 'required|string|max:50|unique:products,sku,NULL,id,tenant_id,' . $request->tenant_id,
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
