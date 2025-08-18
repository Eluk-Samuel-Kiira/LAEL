<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $currencies = Currency::latest()->get();
        
        $bladeToReload = $request->query('bladeFileToReload');
        switch ($bladeToReload) {
            case 'currencyIndexTable':
                return view('settings.currency.currency-component', [
                    'currencies' => $currencies,
                ]);
            default:
                return view('settings.currency-index', [
                    'currencies' => $currencies,
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
            'code' => 'required|alpha|size:3|unique:currencies,code',
            'name' => 'required|string|unique:currencies,name',
            'symbol' => 'required',
            'exchange_rate' => 'required',
        ]);

        $currency = Currency::create([
            'code' => $request->code,
            'name' => $request->name,
            'symbol' => $request->symbol,
            'exchange_rate' => $request->exchange_rate,
            'created_by' => Auth::user()->id
        ]);


        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'currencyIndexTable',
            'refresh' => false,
            'message' => __('auth._created'),
            'redirect' => route('currency.index'),
        ]);


    }

    /**
     * Display the specified resource.
     */
    public function show(Currency $currency)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Currency $currency)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'code' => [
                'required',
                'alpha',
                'size:3',
                Rule::unique('currencies', 'code')->ignore($currency->id),
            ],
            'name' => 'required|string',
            'symbol' => 'required',
            'exchange_rate' => 'required',

        ]);

        if ($currency->code === 'USD') {
            return response()->json([
                'success' => false,
                'message' => __('auth.default_currency'),
            ]);
        }

        $currency->update([
            'code' => $request->code,
            'name' => $request->name,
            'symbol' => $request->symbol,
            'exchange_rate' => $request->exchange_rate,
            'created_by' => Auth::user()->id
        ]);

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'currencyIndexTable',
            'refresh' => false,
            'message' => __('auth._updated'),
            'redirect' => route('currency.index'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Currency $currency)
    {
        
        if ($currency->isActive === 1 || $currency->name === 'USD') {
            return response()->json([
                'success' => false,
                'message' => __('auth._undeleletable'),
            ]);
        }

        $currency->delete();
        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'currencyIndexTable',
            'refresh' => false,
            'message' => __('auth._deleted'),
            'redirect' => route('currency.index'),
        ]);
        
    }

    public function changeCurrencyStatus(Request $request, $id) 
    {
        // Validate the request data for status
        $validated = $request->validate([
            'status' => 'required|in:1,0',  // Ensures only 'active' or 'inactive' are allowed
        ]);


        $currency = Currency::find($id);
        if ($currency->code === 'USD') {
            return response()->json([
                'success' => false,
                'message' => __('auth.default_currency'),
            ]);
        }
    
        // Check if the currency exists and update their status
        if ($currency) {
            $currency->isActive = $validated['status'];  // Directly update the status field
            if ($currency->save()) {  // Save the user object
                return response()->json([
                    'success' => true,
                    'reload' => true,
                    'refresh' => false,
                    'componentId' => 'currencyIndexTable',
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
