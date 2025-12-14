<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{ ExpenseCategory, Expense };
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{ Auth, Log, Storage };
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Build the query
        $query = Expense::with('tenant');
        
        // If user is NOT super_admin, filter by tenant
        if (!$user->hasRole('super_admin')) {
            $query->scopeForTenant($query, $user->tenant_id);
        }
        
        $expenses = $query->latest()->get();

        $bladeToReload = $request->query('bladeFileToReload');
        switch ($bladeToReload) {
            case 'reloadExpenseComponent':
                return view('procurement.expense.component', [
                    'expenses' => $expenses,
                ]);
            default:
                return view('procurement.expense-index', [
                    'expenses' => $expenses,
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
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        // Validation rules
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|decimal:0,2',
            'tax_amount' => 'nullable|numeric|min:0|decimal:0,2',
            'vendor_name' => 'required|string|max:200',
            'category_id' => 'required|exists:expense_categories,id',
            'employee_id' => 'nullable|exists:employees,id',
            'date' => 'required|date',
            'paid_date' => 'nullable|date|after_or_equal:date',
            'payment_method' => 'nullable|in:cash,card,transfer,check,credit',
            'payment_status' => 'required|in:pending,paid,reimbursed',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|required_if:is_recurring,true|in:weekly,monthly,quarterly,annually',
            'next_recurring_date' => 'nullable|date|after_or_equal:date',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120', // 5MB
        ]);

        // Check if category belongs to tenant
        $category = ExpenseCategory::where('id', $validated['category_id'])
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => __('auth.category_not_found'),
            ]);
        }

        // Generate unique expense number
        $expenseNumber = $this->generateExpenseNumber($tenantId);

        // Handle receipt upload
        $receiptUrl = null;
        if ($request->hasFile('receipt')) {
            $receiptUrl = $this->uploadReceipt($request->file('receipt'), $tenantId);
        }

        // Calculate total amount
        $amount = $validated['amount'];
        $taxAmount = $validated['tax_amount'] ?? 0;
        $totalAmount = $amount + $taxAmount;

        // Create the expense
        $expense = Expense::create([
            'tenant_id' => $tenantId,
            'expense_number' => $expenseNumber,
            'description' => $validated['description'],
            'amount' => $amount,
            'tax_amount' => $taxAmount,
            // 'total_amount' => $totalAmount, // Will be calculated automatically if using generated column
            'vendor_name' => $validated['vendor_name'],
            'category_id' => $validated['category_id'],
            'employee_id' => $validated['employee_id'] ?? null,
            'date' => $validated['date'],
            'paid_date' => $validated['paid_date'] ?? null,
            'payment_method' => $validated['payment_method'] ?? null,
            'payment_status' => $validated['payment_status'],
            'is_recurring' => $validated['is_recurring'] ?? false,
            'recurring_frequency' => $validated['recurring_frequency'] ?? null,
            'next_recurring_date' => $validated['next_recurring_date'] ?? null,
            'receipt_url' => $receiptUrl,
            'created_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadExpenseComponent',
            'refresh' => false,
            'message' => __('auth.expense_created'),
            'redirect' => route('expense.index'),
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
    public function update(Request $request, $id)
    {
        // \Log::info($request);
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        // Find the expense
        $expense = Expense::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => __('auth.expense_not_found'),
            ]);
        }

        // Check if expense is approved and user doesn't have permission to edit approved expenses
        if ($expense->approved_at && !$user->can('edit approved expense')) {
            return response()->json([
                'success' => false,
                'message' => __('auth.cannot_edit_approved_expense'),
            ]);
        }

        // Validation rules
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|decimal:0,2',
            'tax_amount' => 'nullable|numeric|min:0|decimal:0,2',
            'vendor_name' => 'required|string|max:200',
            'category_id' => 'required|exists:expense_categories,id',
            'employee_id' => 'nullable|exists:employees,id',
            'date' => 'required|date',
            'paid_date' => 'nullable|date|after_or_equal:date',
            'payment_method' => 'nullable|in:cash,card,transfer,check,credit',
            'payment_status' => 'required|in:pending,paid,reimbursed',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|required_if:is_recurring,true|in:weekly,monthly,quarterly,annually',
            'next_recurring_date' => 'nullable|date|after_or_equal:date',
            'receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        // Check if category belongs to tenant
        $category = ExpenseCategory::where('id', $validated['category_id'])
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => __('auth.category_not_found'),
            ]);
        }

        // Handle receipt upload if new file provided
        // $receiptUrl = $expense->receipt_url;
        // if ($request->hasFile('receipt')) {
        //     // Delete old receipt if exists
        //     if ($receiptUrl) {
        //         $this->deleteReceipt($receiptUrl);
        //     }
        //     $receiptUrl = $this->uploadReceipt($request->file('receipt'), $tenantId);
        // }

        // Calculate total amount
        $amount = $validated['amount'];
        $taxAmount = $validated['tax_amount'] ?? 0;
        $totalAmount = $amount + $taxAmount;

        // Update the expense
        $expense->update([
            'description' => $validated['description'],
            'amount' => $amount,
            'tax_amount' => $taxAmount,
            // 'total_amount' => $totalAmount, // Will be calculated automatically if using generated column
            'vendor_name' => $validated['vendor_name'],
            'category_id' => $validated['category_id'],
            'employee_id' => $validated['employee_id'] ?? null,
            'date' => $validated['date'],
            'paid_date' => $validated['paid_date'] ?? null,
            'payment_method' => $validated['payment_method'] ?? null,
            'payment_status' => $validated['payment_status'],
            'is_recurring' => $validated['is_recurring'] ?? false,
            'recurring_frequency' => $validated['recurring_frequency'] ?? null,
            'next_recurring_date' => $validated['next_recurring_date'] ?? null,
            'updated_at' => now(),
        ]);

        // Reset approval if significant changes were made
        if ($expense->approved_at && $this->hasSignificantChanges($expense, $validated)) {
            $expense->update([
                'approved_by' => null,
                'approved_at' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadExpenseComponent',
            'refresh' => false,
            'message' => __('auth.expense_updated'),
            'redirect' => route('expense.index'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        // Find the expense
        $expense = Expense::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => __('auth.expense_not_found'),
            ]);
        }

        // Check if expense is approved and user doesn't have permission to delete approved expenses
        if ($expense->approved_at && !$user->can('delete approved expense')) {
            return response()->json([
                'success' => false,
                'message' => __('auth.cannot_delete_approved_expense'),
            ]);
        }

        // Delete receipt file if exists
        if ($expense->receipt_url) {
            $this->deleteReceipt($expense->receipt_url);
        }

        // Delete the expense
        $expense->delete();

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadExpenseComponent',
            'refresh' => false,
            'message' => __('auth.expense_deleted'),
            'redirect' => route('expense.index'),
        ]);
    }

    public function updateExpenseStatus(Request $request, $id)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        $request->validate([
            'status' => 'required|in:pending,paid,reimbursed',
        ]);

        $expense = Expense::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => __('auth.expense_not_found'),
            ]);
        }

        // Check if trying to mark as paid but expense is not approved
        if (($request->status === 'paid' || $request->status === 'reimbursed') && !$expense->approved_at) {
            return response()->json([
                'success' => false,
                'message' => __('auth.cannot_pay_unapproved_expense'),
            ]);
        }

        // Check if trying to change from paid/reimbursed back to pending
        if ($request->status === 'pending' && ($expense->payment_status === 'paid' || $expense->payment_status === 'reimbursed')) {
            return response()->json([
                'success' => false,
                'message' => __('auth.cannot_revert_paid_status'),
            ]);
        }

        $updateData = [
            'payment_status' => $request->status,
            'updated_at' => now(),
        ];

        // Set paid date only when marking as paid or reimbursed
        if ($request->status === 'paid' || $request->status === 'reimbursed') {
            $updateData['paid_date'] = now();
            
            // If reimbursed, also set paid_by to current user
            if ($request->status === 'reimbursed') {
                $updateData['paid_by'] = $user->id;
            }
        } else {
            // If reverting to pending, clear paid date and paid_by
            $updateData['paid_date'] = null;
            $updateData['paid_by'] = null;
        }

        $expense->update($updateData);

        // Log the activity
        // activity()
        //     ->causedBy($user)
        //     ->performedOn($expense)
        //     ->log('updated payment status to ' . $request->status);

        return response()->json([
            'success' => true,
            'message' => __('auth.status_updated'),
            'reload' => true,
            'componentId' => 'reloadExpenseComponent',
            'redirect' => route('expense.index'),
        ]);
    }

    /**
     * Approve an expense
     */
    public function approve($id)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        $expense = Expense::where('id', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => __('auth.expense_not_found'),
            ]);
        }

        if ($expense->approved_at) {
            return response()->json([
                'success' => false,
                'message' => __('auth.expense_already_approved'),
            ]);
        }

        $expense->update([
            'approved_by' => $user->id,
            'approved_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'reload' => true,
            'componentId' => 'reloadExpenseComponent',
            'message' => __('auth.expense_approved'),
            'redirect' => route('expense.index'),
        ]);
    }

    /**
     * Generate unique expense number
     */
    private function generateExpenseNumber($tenantId)
    {
        $prefix = 'EXP-' . date('ym');
        $count = Expense::where('tenant_id', $tenantId)
            ->where('expense_number', 'like', $prefix . '-%')
            ->count() + 1;

        return $prefix . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Upload receipt file
     */
    public function updateReceipt(Request $request, $id)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        try {
            $request->validate([
                'receipt' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
                'description' => 'nullable|string|max:255',
            ], [
                'receipt.required' => __('pagination.please_select_file'),
                'receipt.file' => __('pagination.invalid_file'),
                'receipt.mimes' => __('pagination.invalid_file_type'),
                'receipt.max' => __('pagination.file_too_large'),
            ]);

            $expense = Expense::where('id', $id)
                ->where('tenant_id', $tenantId)
                ->first();

            if (!$expense) {
                session()->flash('toast', [
                    'type' => 'error',
                    'message' => __('auth.expense_not_found'),
                ]);
                return redirect()->route('expense.index');
            }

            // Delete old receipt if exists
            if ($expense->receipt_url) {
                Storage::disk('public')->delete($expense->receipt_url);
            }

            // Upload new receipt
            $path = $request->file('receipt')->store('receipts/tenant-' . $tenantId, 'public');

            $expense->update([
                'receipt_url' => $path,
                'description' => $request->description ?? $expense->description,
                'updated_at' => now(),
            ]);

            session()->flash('toast', [
                'type' => 'success',
                'message' => __('auth._updated'),
            ]);

            return redirect()->route('expense.index');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Get validation errors and flash them to session
            $errors = $e->validator->errors()->all();
            session()->flash('toast', [
                'type' => 'error',
                'message' => implode(', ', $errors),
            ]);
            
            // Redirect back with input to repopulate form
            return redirect()->back()
                ->withInput()
                ->withErrors($e->validator);
                
        } catch (\Exception $e) {
            // Handle other exceptions
            \Log::error('Receipt upload error: ' . $e->getMessage());
            
            session()->flash('toast', [
                'type' => 'error',
                'message' => __('auth.upload_error') . ': ' . $e->getMessage(),
            ]);
            
            return redirect()->route('expense.index');
        }
    }

    /**
     * Delete receipt file
     */
    private function deleteReceipt($receiptUrl)
    {
        if (Storage::disk('public')->exists($receiptUrl)) {
            Storage::disk('public')->delete($receiptUrl);
        }
    }

    /**
     * Check if expense has significant changes that require re-approval
     */
    private function hasSignificantChanges($expense, $newData)
    {
        $significantFields = ['amount', 'category_id', 'vendor_name', 'description'];
        
        foreach ($significantFields as $field) {
            if (isset($newData[$field]) && $expense->$field != $newData[$field]) {
                return true;
            }
        }
        
        return false;
    }

}
