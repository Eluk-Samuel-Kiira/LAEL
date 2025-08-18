<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UnitOfMeasure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UnitOfMeasureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $uoms = UnitOfMeasure::latest()->get();
        
        $bladeToReload = $request->query('bladeFileToReload');
        switch ($bladeToReload) {
            case 'uomIndexTable':
                return view('unit-of-measure.partials.uom-component', [
                    'uoms' => $uoms,
                ]);
            default:
                return view('unit-of-measure.index', [
                    'uoms' => $uoms,
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
        // \Log::info($request);
        $request->validate([
            'name' => 'required|string|max:255|unique:unit_of_measures,name',
            'symbol' => 'required',
        ]);

        $uom = UnitOfMeasure::create([
            'name' => $request->name,
            'symbol' => $request->symbol,
            'created_by' => Auth::user()->id
        ]);

            return response()->json([
                'success' => true,
                'reload' => true,
                'componentId' => 'uomIndexTable',
                'refresh' => false,
                'message' => __('auth._created'),
                'redirect' => route('uom.index'),
            ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(UnitOfMeasure $unitOfMeasure)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UnitOfMeasure $unitOfMeasure)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $uOM = UnitOfMeasure::find($id);
        \Log::info($uOM);
        
        $request->validate([
            'name' => [
                'required',
                'max:255',
                Rule::unique('unit_of_measures', 'name')->ignore($uOM->id),
            ],
            'symbol' => 'required',
        ]);

        // \Log::info($uOM);


        $uOM->update([
            'name' => $request->name,
            'symbol' => $request->symbol,
            'created_by' => Auth::user()->id
        ]);

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'uomIndexTable',
            'refresh' => false,
            'message' => __('auth._updated'),
            'redirect' => route('uom.index'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $unitOfMeasure = UnitOfMeasure::find($id);
        if ($unitOfMeasure->isActive === 1) {
            return response()->json([
                'success' => false,
                'message' => __('auth.still_active'),
            ]);
        }

        $unitOfMeasure->delete();
        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'uomIndexTable',
            'refresh' => false,
            'message' => __('auth._deleted'),
            'redirect' => route('uom.index'),
        ]);
    }

    public function changeUOMStatus(Request $request, $id) 
    {
        // Validate the request data for status
        $validated = $request->validate([
            'status' => 'required|in:1,0',  // Ensures only 'active' or 'inactive' are allowed
        ]);
    
        // Find the user by ID
        $uom = UnitOfMeasure::find($id);
    
        if ($uom) {
            $uom->isActive = $validated['status']; 
            if ($uom->save()) {  // Save the user object
                return response()->json([
                    'success' => true,
                    'reload' => true,
                    'refresh' => false,
                    'componentId' => 'uomIndexTable',
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
