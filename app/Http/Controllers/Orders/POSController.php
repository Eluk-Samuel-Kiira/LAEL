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

        return view('orders.pos-index', compact('user_departments'));
    }

}
