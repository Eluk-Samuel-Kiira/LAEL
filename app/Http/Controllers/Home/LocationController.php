<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $locations = Location::with('locationCreater','locationManager')->latest()->get();
        
        $bladeToReload = $request->query('bladeFileToReload');
        switch ($bladeToReload) {
            case 'locationIndexTable':
                return view('unit-of-measure.location.component', [
                    'locations' => $locations,
                ]);
            default:
                return view('unit-of-measure.location-index', [
                    'locations' => $locations,
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
            'name' => 'required|string|max:55|unique:locations,name',
            'address' => 'required|string|max:55',
            'manager_id' => 'required|exists:users,id',
        ]);

        $location = Location::create([
            'name' => $request->name,
            'address' => $request->address,
            'created_by' => Auth::user()->id,
            'manager_id' => $request->manager_id,
            'tenant_id' => 1,
        ]);

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'locationIndexTable',
            'refresh' => false,
            'message' => __('auth._created'),
            'redirect' => route('locations.index'),
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
        $location = Location::find($id);
        $request->validate([
            'name' => [
                'required',
                'max:255',
                Rule::unique('locations', 'name')->ignore($location->id),
            ],
            'manager_id' => 'required|exists:users,id',
            'address' => 'required|string|max:55',
        ]);

        $location->update([
            'name' => $request->name,
            'address' => $request->address,
            'created_by' => Auth::user()->id,
            'manager_id' => $request->manager_id,
            'tenant_id' => 1,
        ]);

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'locationIndexTable',
            'refresh' => false,
            'message' => __('auth._updated'),
            'redirect' => route('locations.index'),
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $location = Location::find($id);
        if ($location->is_active === 1) {
            return response()->json([
                'success' => false,
                'message' => __('auth.still_active'),
            ]);
        }

        $location->delete();
        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'locationIndexTable',
            'refresh' => false,
            'message' => __('auth._deleted'),
            'redirect' => route('locations.index'),
        ]);
    }

        
    public function updatePrimaryStatus(Request $request, $id) 
    {
        // Validate the request data for status
        $validated = $request->validate([
            'status' => 'required|in:1,0',  // Ensures only 'active' or 'inactive' are allowed
        ]);
        
        $location = Location::find($id);
    
        if ($location) {
            $location->is_primary = $validated['status']; 
            if ($location->save()) { 
                return response()->json([
                    'success' => true,
                    'reload' => true,
                    'componentId' => 'locationIndexTable',
                    'refresh' => false,
                    'message' => __('auth._updated'),
                    'redirect' => route('locations.index'),
                ]);
            }
        }
    
        // If user not found or status update failed
        return response()->json([
            'success' => false,
            'message' => __('auth._not_found'),
        ]);
    }
    
    public function updateLocationStatus(Request $request, $id) 
    {
        // Validate the request data for status
        $validated = $request->validate([
            'status' => 'required|in:1,0',  // Ensures only 'active' or 'inactive' are allowed
        ]);
        
        $location = Location::find($id);
    
        if ($location) {
            $location->is_active = $validated['status']; 
            if ($location->save()) { 
                return response()->json([
                    'success' => true,
                    'reload' => true,
                    'componentId' => 'locationIndexTable',
                    'refresh' => false,
                    'message' => __('auth._updated'),
                    'redirect' => route('locations.index'),
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
