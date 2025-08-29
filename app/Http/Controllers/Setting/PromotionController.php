<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Promotion;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $promotions = Promotion::latest()->get();
        // \Log::info($promotions);

        $bladeToReload = $request->query('bladeFileToReload');
        switch ($bladeToReload) {
            case 'reloadPromotionComponent':
                return view('promotion.promotion.component', [
                    'promotions' => $promotions,
                ]);
            default:
                return view('promotion.promotion-index', [
                    'promotions' => $promotions,
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
            'discount_type' => 'required|string|in:fixed_amount,percentage,buy_x_get_y',
            'discount_value' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $promotion = Promotion::create([
            'name' => $request->name,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'created_by' => Auth::user()->id,
            'tenant_id' => 1,
        ]);

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadPromotionComponent',
            'refresh' => false,
            'message' => __('auth._created'),
            'redirect' => route('promotion.index'),
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
        $promotion = Promotion::find($id);
        $request->validate([
            'name' => [
                'required',
                'max:255',
                Rule::unique('taxes', 'name')->ignore($promotion->id),
            ],
            'discount_type' => 'required|string|in:fixed_amount,percentage,buy_x_get_y',
            'discount_value' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $promotion->update([
            'name' => $request->name,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'created_by' => Auth::user()->id,
            'tenant_id' => 1,
        ]);

        
        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadPromotionComponent',
            'refresh' => false,
            'message' => __('auth._created'),
            'redirect' => route('promotion.index'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $promotion = Promotion::find($id);
        if ($promotion->is_active == 1) {
            return response()->json([
                'success' => false,
                'message' => __('auth.still_active'),
            ]);
        }

        $promotion->delete();
        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadPromotionComponent',
            'refresh' => false,
            'message' => __('auth._updated'),
            'redirect' => route('promotion.index'),
        ]);
    }

    public function updatePromotionStatus(Request $request, $id) 
    {
        // \Log::info($id);
        // Validate the request data for status
        $validated = $request->validate([
            'status' => 'required|in:1,0',  // Ensures only 'active' or 'inactive' are allowed
        ]);
        
        $promotion = Promotion::find($id);
    
        if ($promotion) {
            $promotion->is_active = $validated['status']; 
            if ($promotion->save()) {  // Save the user object
                return response()->json([
                    'success' => true,
                    'reload' => true,
                    'componentId' => 'reloadPromotionComponent',
                    'refresh' => false,
                    'message' => __('auth._updated'),
                    'redirect' => route('promotion.index'),
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
