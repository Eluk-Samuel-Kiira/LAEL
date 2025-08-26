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
use App\Models\Payment;
use App\Models\InventoryLog;
use App\Models\Customer;

class POSController extends Controller
{

    public function index(Request $request)
    {
        Artisan::call('optimize:clear');
        $user = Auth::user();
        
        // fetch departments assigned to this user
        $user_departments = $user->departments()->get();
        $departments_id = $user_departments->pluck('id')->toArray();

        // Fetch only products allocated to those departments (with variants AND their inventory)
        $products = Product::with(['variants.inventory', 'departments'])
            ->whereHas('departments', function ($query) use ($departments_id) {
                $query->whereIn('departments.id', $departments_id);
            })
            ->where('is_active', 1)
            ->latest()
            ->get();
            
        // \Log::info($products->toArray());
        return view('orders.pos-index', compact('user_departments', 'products'));
    }

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
                'balance_due' => $cartData['total'],
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
                    'total_price' => $item['price'] * $item['quantity'],
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
            ]);

        } catch (\Exception $e) {
            \Log::error('Order processing failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Order processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

}

