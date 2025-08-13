<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryItems;
use App\Models\ProductVariant;

class InventoryItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $items = InventoryItems::with('variant','itemCreater')->latest()->get();

        $bladeToReload = $request->query('bladeFileToReload');
        switch ($bladeToReload) {
            case 'reloadItemComponent':
                return view('store.inventory-items.component', [
                    'items' => $items,
                ]);
            default:
                return view('store.items-index', [
                    'items' => $items,
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
        //
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
