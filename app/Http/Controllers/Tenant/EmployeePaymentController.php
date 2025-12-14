<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ Employee, EmployeePayment };
use Illuminate\Support\Facades\{ Auth, Log };
use Illuminate\Validation\Rule;

class EmployeePaymentController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Build the query
        $query = EmployeePayment::with(['employee', 'tenant']);
        
        // If user is NOT super_admin, filter by tenant
        if (!$user->hasRole('super_admin')) {
            $query->scopeForTenant($query, $user->tenant_id);
        }
        
        $payments = $query->latest()->get();

        $bladeToReload = $request->query('bladeFileToReload');
        switch ($bladeToReload) {
            case 'reloadPaymentComponent':
                return view('department.employee-payment.component', [
                    'payments' => $payments,
                ]);
            default:
                return view('department.employee-payment-index', [
                    'payments' => $payments,
                ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payment_date' => 'required|date',
            'payment_type' => 'required|in:salary,allowance,bonus,overtime,advance,other',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|decimal:0,2',
            'payment_method' => 'required|in:cash,bank_transfer,check,digital_wallet',
            'reference_number' => 'nullable|string|max:100',
            'status' => 'required|in:pending,completed,failed,cancelled',
            'pay_period_start' => 'nullable|date',
            'pay_period_end' => 'nullable|date|after_or_equal:pay_period_start',
            'hours_worked' => 'nullable|numeric|min:0|decimal:0,2',
            'hourly_rate' => 'nullable|numeric|min:0|decimal:0,2',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if employee belongs to tenant
        $employee = Employee::where('id', $validated['employee_id'])
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => __('auth.employee_not_found'),
            ]);
        }

        // Calculate amount if hours and rate provided
        if ($request->payment_type === 'overtime' && $request->hours_worked && $request->hourly_rate) {
            $validated['amount'] = $request->hours_worked * $request->hourly_rate;
        }

        // Prepare breakdown data
        $breakdown = null;
        if ($request->has(['hours_worked', 'hourly_rate'])) {
            $breakdown = [
                'hours_worked' => $request->hours_worked,
                'hourly_rate' => $request->hourly_rate,
                'calculated_amount' => $validated['amount']
            ];
        }

        $payment = EmployeePayment::create([
            'employee_id' => $validated['employee_id'],
            'tenant_id' => $tenantId,
            'payment_date' => $validated['payment_date'],
            'payment_type' => $validated['payment_type'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'status' => $validated['status'],
            'pay_period_start' => $validated['pay_period_start'] ?? null,
            'pay_period_end' => $validated['pay_period_end'] ?? null,
            'hours_worked' => $validated['hours_worked'] ?? null,
            'hourly_rate' => $validated['hourly_rate'] ?? null,
            'breakdown' => $breakdown,
            'notes' => $validated['notes'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadPaymentComponent',
            'message' => __('auth.payment_created'),
            'redirect' => route('employee-payment.index'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        $payment = EmployeePayment::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => __('auth.payment_not_found'),
            ]);
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payment_date' => 'required|date',
            'payment_type' => 'required|in:salary,allowance,bonus,overtime,advance,other',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|decimal:0,2',
            'payment_method' => 'required|in:cash,bank_transfer,check,digital_wallet',
            'reference_number' => 'nullable|string|max:100',
            'status' => 'required|in:pending,completed,failed,cancelled',
            'pay_period_start' => 'nullable|date',
            'pay_period_end' => 'nullable|date|after_or_equal:pay_period_start',
            'hours_worked' => 'nullable|numeric|min:0|decimal:0,2',
            'hourly_rate' => 'nullable|numeric|min:0|decimal:0,2',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if employee belongs to tenant
        $employee = Employee::where('id', $validated['employee_id'])
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => __('auth.employee_not_found'),
            ]);
        }

        // Recalculate amount if hours and rate changed
        if ($request->payment_type === 'overtime' && $request->hours_worked && $request->hourly_rate) {
            $validated['amount'] = $request->hours_worked * $request->hourly_rate;
        }

        // Update breakdown
        $breakdown = null;
        if ($request->has(['hours_worked', 'hourly_rate'])) {
            $breakdown = [
                'hours_worked' => $request->hours_worked,
                'hourly_rate' => $request->hourly_rate,
                'calculated_amount' => $validated['amount']
            ];
        }

        $payment->update([
            'employee_id' => $validated['employee_id'],
            'payment_date' => $validated['payment_date'],
            'payment_type' => $validated['payment_type'],
            'description' => $validated['description'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'status' => $validated['status'],
            'pay_period_start' => $validated['pay_period_start'] ?? null,
            'pay_period_end' => $validated['pay_period_end'] ?? null,
            'hours_worked' => $validated['hours_worked'] ?? null,
            'hourly_rate' => $validated['hourly_rate'] ?? null,
            'breakdown' => $breakdown,
            'notes' => $validated['notes'] ?? null,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadPaymentComponent',
            'message' => __('auth.payment_updated'),
            'redirect' => route('employee-payment.index'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        $payment = EmployeePayment::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => __('auth.payment_not_found'),
            ]);
        }

        // Check if payment is completed and user doesn't have permission
        if ($payment->status === 'completed' && !$user->can('delete completed payment')) {
            return response()->json([
                'success' => false,
                'message' => __('auth.cannot_delete_completed_payment'),
            ]);
        }

        $payment->delete();

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadPaymentComponent',
            'message' => __('auth.payment_deleted'),
            'redirect' => route('employee-payment.index'),
        ]);
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        $request->validate([
            'status' => 'required|in:pending,completed,failed,cancelled',
        ]);

        $payment = EmployeePayment::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => __('auth.payment_not_found'),
            ]);
        }

        // Prevent changing from completed/failed/cancelled to pending
        if ($request->status === 'pending' && 
            in_array($payment->status, ['completed', 'failed', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => __('auth.cannot_revert_completed_payment'),
            ]);
        }

        $payment->update([
            'status' => $request->status,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('auth.status_updated'),
            'reload' => true,
            'componentId' => 'reloadPaymentComponent',
            'redirect' => route('employee-payment.index'),
        ]);
    }
}
