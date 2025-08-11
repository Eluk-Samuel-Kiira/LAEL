<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;


class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products_categories = ProductCategory::with('productCategoryCreater','parentCategory')->latest()->get();

        $bladeToReload = $request->query('bladeFileToReload');
        switch ($bladeToReload) {
            case 'reloadProductCategoryComponent':
                return view('inventory.product-category.component', [
                    'products_categories' => $products_categories,
                ]);
            default:
                return view('inventory.product-category-index', [
                    'products_categories' => $products_categories,
                ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('inventory.product-category.create', [
            
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:product_categories,name',
                'parent_category_id' => 'required|integer',
                'description' => 'nullable|string',
                'is_active' => 'required|in:1,0',
                'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $validated['slug'] = Str::slug($validated['name']);
            $validated['created_by'] = Auth::id();
            $validated['tenant_id'] = 1;

            // If file uploaded, store and set image_url
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('categories', 'public');
                $validated['image_url'] = $path; // store relative path
            }

            // Remove 'photo' from array before saving if not needed in DB
            unset($validated['photo']);

            ProductCategory::create($validated);

            return response()->json([
                'success' => true,
                'message' => __('auth._created'),
                'redirect' => route('product-category.index')
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $productCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductCategory $productCategory)
    {
        return view('inventory.product-category.edit', compact('productCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $category = ProductCategory::find($id);

            $validated = $request->validate([
                'name' => [
                    'required',
                    'max:255',
                    Rule::unique('product_categories', 'name')->ignore($category->id),
                ],
                'parent_category_id' => 'required|integer',
                'description' => 'nullable|string',
                'is_active' => 'required|in:1,0',
                'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:4048',
            ]);

            $validated['slug'] = Str::slug($validated['name']);
            $validated['updated_by'] = Auth::id(); // optional: track who updated
            $validated['tenant_id'] = 1;

            // If file uploaded, store and set image_url
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('categories', 'public');
                $validated['image_url'] = $path; // store relative path
            } else {
                // Keep existing image_url if no new file uploaded
                $validated['image_url'] = $category->image_url;
            }

            // Remove 'photo' field if exists (not needed in DB)
            unset($validated['photo']);

            // Update the category record
            $category->update($validated);

            return redirect()->route('product-category.index')
                            ->with('success', __('auth._updated'));

        } catch (ValidationException $e) {
            return redirect()->back()
                            ->withErrors($e->errors())
                            ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        \Log::info($id);
        $category = ProductCategory::find($id);
        if ($category->is_active === 1) {
            return response()->json([
                'success' => false,
                'message' => __('auth.still_active'),
            ]);
        }

        $category->delete();
        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadProductCategoryComponent',
            'refresh' => false,
            'message' => __('auth._deleted'),
            'redirect' => route('product-category.index'),
        ]);
    }
}
