<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Illuminate\Support\Facades\Artisan;
use App\Models\InventoryItems;
use App\Models\ProductVariant;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str; 
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\InventoryLog;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\OrderTax;

class POSController extends Controller
{
    // POSController.php
    public function index(Request $request)
    {
        // Consider removing this in production; it's heavy for every request
        Artisan::call('optimize:clear');

        $user = Auth::user();

        $user_departments = $user->departments()->get();
        $departments_id = $user_departments->pluck('id')->toArray();

        $now = now();

        $products = Product::with([
                'departments',
                'taxes' => fn($q) => $q->where('is_active', 1),
                'promotions' => fn($q) => $q
                    ->where('is_active', 1)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now),
                'variants.inventory',
                'variants.variantTaxes' => fn($q) => $q->where('is_active', 1),
                'variants.variantPromotions' => fn($q) => $q
                    ->where('is_active', 1)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now),
            ])
            ->whereHas('departments', fn($q) => $q->whereIn('departments.id', $departments_id))
            ->where('is_active', 1)
            ->latest()
            ->get();


        // Compute applicable taxes per variant following your rules
        foreach ($products as $product) {
            foreach ($product->variants as $variant) {
                /** ---------------- Taxes ---------------- */
                $applicableTaxes = collect();
                if ((int)$variant->is_taxable === 0) {
                    $applicableTaxes = collect();
                } else {
                    if ((int)$product->is_taxable === 1 && $product->taxes->isNotEmpty()) {
                        $applicableTaxes = $product->taxes->keyBy('id');
                    }
                    if ($variant->variantTaxes->isNotEmpty()) {
                        foreach ($variant->variantTaxes as $vtax) {
                            $applicableTaxes[$vtax->id] = $vtax; // override if same id
                        }
                    }
                }

                $variant->applicable_taxes = $applicableTaxes->map(function ($t) {
                    return [
                        'id'   => (int)$t->id,
                        'name' => $t->name,
                        'rate' => (float)$t->rate,
                        'type' => $t->type,
                    ];
                })->values();

                /** ---------------- Promotions ---------------- */
                $applicablePromos = collect();

                // Start with product-level promos
                if ($product->promotions->isNotEmpty()) {
                    $applicablePromos = $product->promotions->keyBy('id');
                }

                // Merge variant promos (override if same id)
                if ($variant->variantPromotions->isNotEmpty()) {
                    foreach ($variant->variantPromotions as $vpromo) {
                        $applicablePromos[$vpromo->id] = $vpromo;
                    }
                }

                $variant->applicable_promotions = $applicablePromos->map(function ($p) {
                    return [
                        'id'          => (int)$p->id,
                        'name'        => $p->name,
                        'type'        => $p->discount_type,   // 'percentage' | 'fixed' | 'buy_x_get_y'
                        'value'       => (float)$p->discount_value,
                        'start_date'  => $p->start_date,
                        'end_date'    => $p->end_date,
                    ];
                })->values();
            }
        }

        // \Log::info($products->toArray());

        return view('orders.pos-index', compact('user_departments', 'products'));
    }


    // public function index(Request $request)
    // {
    //     Artisan::call('optimize:clear');
    //     $user = Auth::user();
        
    //     // fetch departments assigned to this user
    //     $user_departments = $user->departments()->get();
    //     $departments_id = $user_departments->pluck('id')->toArray();

    //     // Fetch only products allocated to those departments (with variants AND their inventory)
    //     $products = Product::with(['variants.inventory', 'departments'])
    //         ->whereHas('departments', function ($query) use ($departments_id) {
    //             $query->whereIn('departments.id', $departments_id);
    //         })
    //         ->where('is_active', 1)
    //         ->latest()
    //         ->get();
            
    //     // \Log::info($products->toArray());
    //     return view('orders.pos-index', compact('user_departments', 'products'));
    // }

    public function processPayment(Request $request)
    {
        try {
            $cartData = json_decode($request->cart_data, true);
            $user = Auth::user();

            // Determine customer details
            $customerId = null;
            $customerName = null;

            if (isset($cartData['customer'])) {
                if ($cartData['customer']['type'] === 'existing') {
                    $customerId = $cartData['customer']['id'];
                    
                    $customer = Customer::find($customerId);
                    $customerName = $customer ? trim($customer->first_name . ' ' . $customer->last_name) : null;
                } elseif ($cartData['customer']['type'] === 'new') {
                    $customerName = $cartData['customer']['name'];
                }
            }
            // Generate order number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            
            $order = Order::create([
                'tenant_id' => 1,
                'customer_id' => $customerId,     
                'customer_name' => $customerName, 
                'location_id' => 1,
                'department_id' => 1,
                'order_number' => $orderNumber,
                'type' => 'sale',
                'status' => 'confirmed',
                'subtotal' => $cartData['subtotal'],
                'discount_total' => $cartData['discount'],
                'tax_total' => $cartData['tax'],
                'total' => $cartData['total'],
                'paid_amount' => 0, // Will be updated after payment
                'balance_due' => 0,
                'source' => 'pos',
                'created_by' => $user->id,
            ]);

            // \Log::info($order);

            // Create order items and update inventory
            foreach ($cartData['items'] as $item) {
                // Get product variant with inventory
                $variant = ProductVariant::with('inventory')->find($item['variant_id']);
                
                if (!$variant) {
                    continue;
                }

                // Create order item
                $orderItem = $order->orderItems()->create([
                    'product_id' => $variant->product_id,
                    'variant_id' => $variant->id,
                    'item_name' => $item['name'],
                    'sku' => $variant->sku,
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'tax_amount' => $item['tax_total'],
                    'discount' => $item['discount'],
                    'total_price' => $item['total'],
                    'inventory_data' => json_encode([
                        'initial_stock' => $variant->inventory->quantity_on_hand,
                        'current_stock' => $variant->inventory->quantity_on_hand - $item['quantity']
                    ])
                ]);
            }


            return response()->json([
                'success' => true,
                'message' => __('pagination.order_placed'),
                'order_number' => $orderNumber,
                'customerName' => $customerName,
                'order_id' => $order['id'],
            ]);

        } catch (\Exception $e) {
            \Log::error('Order processing failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Order processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    
    public function completePayment(Request $request) 
    {
        // \Log::info($request->all());
        $cartData = $request->items; 
        $paymentDetails = $request->payment_details;  

        $order = Order::findOrFail($request->order_id);
        $order->update([
            'paid_amount' => $paymentDetails['amount_tendered'] ?? 0,
            'balance_due' => $paymentDetails['change_due'] ?? 0,
            'notes'       => __('pagination.payment_completed'),
            'status' => 'completed',
        ]);

        // Payment Details - FIXED: Access payment_method from $request, not $cartData
        $transactionId = ($request->payment_method === 'cash') 
                            ? (string) Str::uuid() 
                            : null;

        // --- Update or create Payment ---
        OrderPayment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'amount'          => $request->total ?? 0,
                'payment_method'  => $request->payment_method, // Fixed this line too
                'transaction_id'  => $transactionId,
                'status'          => 'completed',
                'notes'           => __('pagination.payment_completed'),
                'processed_at'    => now(),
                'processed_by'    => auth()->id(),
            ]
        );

        // --- Update or create total Tax ---
        OrderTax::updateOrCreate(
            [
                'order_id' => $order->id,
            ],
            [
                'tax_name'   => 'VAT',
                'tax_rate'    => (($request->total - $request->subtotal) / $request->total) * 100,
                'tax_amount'  => $request->tax,
                'is_compound' => 1,
                'created_by'  => auth()->id(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => __('pagination.payment_completed'),
        ]);
    }

}

