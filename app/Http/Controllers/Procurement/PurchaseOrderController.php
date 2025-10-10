<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ Supplier, PurchaseOrder };


class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $purchaseOrders = PurchaseOrder::with(['items', 'supplier', 'location', 'creator'])->latest()->get();

        \Log::info($purchaseOrders);
        $bladeToReload = $request->query('bladeFileToReload');
        switch ($bladeToReload) {
            case 'reloadPurchasesComponent':
                return view('procurement.purchase-order.component', [
                    'purchaseOrders' => $purchaseOrders,
                ]);
            default:
                return view('procurement.po-index', [
                    'purchaseOrders' => $purchaseOrders,
                ]);
        }
    }
}
