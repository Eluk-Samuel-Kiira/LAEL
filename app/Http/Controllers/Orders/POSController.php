<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class POSController extends Controller
{

    public function index(Request $request)
    {
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

}
