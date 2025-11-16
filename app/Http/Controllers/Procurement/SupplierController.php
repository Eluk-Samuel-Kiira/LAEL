<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        $suppliers = Supplier::latest()->get();

        $bladeToReload = $request->query('bladeFileToReload');
        switch ($bladeToReload) {
            case 'reloadSupplierComponent':
                return view('procurement.supplier.component', [
                    'suppliers' => $suppliers,
                ]);
            default:
                return view('procurement.supplier-index', [
                    'suppliers' => $suppliers,
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
            'name' => 'required|string|max:100',
            'contact_person' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'tax_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country_code' => 'nullable|string|size:2',
            'payment_terms' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id ?? 1;
        $validated['created_by'] = auth()->id();

        $supplier = Supplier::create($validated);

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadSupplierComponent',
            'refresh' => false,
            'message' => __('auth._created'),
            'redirect' => route('suppliers.index'),
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
    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'contact_person' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'tax_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country_code' => 'nullable|string|size:2',
            'payment_terms' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['tenant_id'] = 1;
        // $validated['created_by'] = auth()->id();

        $supplier->update($validated);

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadSupplierComponent',
            'refresh' => false,
            'message' => __('auth._updated'),
            'redirect' => route('suppliers.index'),
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json([
                'success' => false,
                'message' => __('passwords.not_found'),
            ], 404);
        }

        // Check if supplier has any purchase orders
        if ($supplier->purchaseOrders()->exists()) {
            return response()->json([
                'success' => false,
                'message' => __('passwords.has_related_orders'),
            ], 400);
        }

        if ($supplier->is_active === 1) {
            return response()->json([
                'success' => false,
                'message' => __('passwords.still_active'),
            ]);
        }

        $supplier->delete();

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadSupplierComponent',
            'refresh' => false,
            'message' => __('auth._deleted'),
            'redirect' => route('suppliers.index'),
        ]);
    }


    public function updateSupplierStatus(Request $request, $id) 
    {
        // \Log::info($id);
        // Validate the request data for status
        $validated = $request->validate([
            'status' => 'required|in:1,0',  // Ensures only 'active' or 'inactive' are allowed
        ]);
        
        $supplier = Supplier::find($id);
    
        if ($supplier) {
            $supplier->is_active = $validated['status']; 
            if ($supplier->save()) {  
                return response()->json([
                    'success' => true,
                    'reload' => true,
                    'refresh' => false,
                    'componentId' => 'reloadSupplierComponent',
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
