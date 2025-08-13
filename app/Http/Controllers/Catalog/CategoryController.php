<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::with('categoryCreater')->latest()->get();

        $bladeToReload = $request->query('bladeFileToReload');
        switch ($bladeToReload) {
            case 'reloadCategoryComponent':
                return view('inventory.category.category-componenet', [
                    'categories' => $categories,
                ]);
            default:
                return view('inventory.category-index', [
                    'categories' => $categories,
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
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['created_by'] = Auth::user()->id;
        $validated['tenant_id'] = 1;

        Category::create($validated);

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadCategoryComponent',
            'refresh' => false,
            'message' => __('auth._created'),
            'redirect' => route('category.index'),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        
        $request->validate([
            'name' => [
                'required',
                'max:255',
                Rule::unique('categories', 'name')->ignore($category->id),
            ],
        ]);

        // \Log::info($uOM);


        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => Auth::user()->id,
        ]);

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadCategoryComponent',
            'refresh' => false,
            'message' => __('auth._updated'),
            'redirect' => route('category.index'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        
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
            'componentId' => 'reloadCategoryComponent',
            'refresh' => false,
            'message' => __('auth._deleted'),
            'redirect' => route('category.index'),
        ]);
    }

    public function changeCategoryStatus(Request $request, $id) 
    {
        // Validate the request data for status
        $validated = $request->validate([
            'status' => 'required|in:1,0',  // Ensures only 'active' or 'inactive' are allowed
        ]);
        
        $category = Category::find($id);
    
        if ($category) {
            $category->is_active = $validated['status']; 
            if ($category->save()) {  // Save the user object
                return response()->json([
                    'success' => true,
                    'reload' => true,
                    'refresh' => false,
                    'componentId' => 'reloadCategoryComponent',
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
