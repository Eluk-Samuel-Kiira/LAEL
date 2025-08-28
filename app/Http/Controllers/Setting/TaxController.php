<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Tax;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $taxes = Tax::latest()->get();

        $bladeToReload = $request->query('bladeFileToReload');
        switch ($bladeToReload) {
            case 'reloadTaxComponent':
                return view('promotion.tax.component', [
                    'taxes' => $taxes,
                ]);
            default:
                return view('promotion.tax-index', [
                    'taxes' => $taxes,
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
        $request->validate([
            'name' => 'required|string|max:55|unique:taxes,name',
            'code' => 'required|string|max:6|min:5',
            'rate' => 'required',
            'type' => 'required',
        ]);

        $tax = Tax::create([
            'name' => $request->name,
            'code' => $request->code,
            'rate' => $request->rate,
            'type' => $request->type,
            'created_by' => Auth::user()->id,
            'tenant_id' => 1,
        ]);

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadTaxComponent',
            'refresh' => false,
            'message' => __('auth._created'),
            'redirect' => route('tax.index'),
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
        $tax = Tax::find($id);
        $request->validate([
            'name' => [
                'required',
                'max:255',
                Rule::unique('taxes', 'name')->ignore($tax->id),
            ],
            'code' => 'required|string|max:6|min:5',
            'rate' => 'required',
            'type' => 'required',
        ]);

        $tax->update([
            'name' => $request->name,
            'code' => $request->code,
            'rate' => $request->rate,
            'type' => $request->type,
            'created_by' => Auth::user()->id,
            'tenant_id' => 1,
        ]);

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadTaxComponent',
            'refresh' => false,
            'message' => __('auth._updated'),
            'redirect' => route('tax.index'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tax = Tax::find($id);
        if ($tax->is_active === 1) {
            return response()->json([
                'success' => false,
                'message' => __('auth.still_active'),
            ]);
        }

        $tax->delete();
        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadTaxComponent',
            'refresh' => false,
            'message' => __('auth._updated'),
            'redirect' => route('tax.index'),
        ]);
    }

    public function updateTaxtatus(Request $request, $id) 
    {
        // \Log::info($id);
        // Validate the request data for status
        $validated = $request->validate([
            'status' => 'required|in:1,0',  // Ensures only 'active' or 'inactive' are allowed
        ]);
        
        $tax = Tax::find($id);
    
        if ($tax) {
            $tax->is_active = $validated['status']; 
            if ($tax->save()) {  // Save the user object
                return response()->json([
                    'success' => true,
                    'reload' => true,
                    'refresh' => false,
                    'componentId' => 'reloadTaxComponent',
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
