<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\Employee;
use Illuminate\Support\Facades\{ DB };
use Carbon\Carbon;

class ExpenseReportsController extends Controller
{

    // Expense Summary Report
public function summary(Request $request)
{
    $user = auth()->user();
    $tenantId = $user->tenant_id;
    
    if (!$tenantId) {
        return redirect()->back()->with('error', __('accounting.invalid_tenant'));
    }
    
    // Get filter parameters
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
    $categoryId = $request->get('category_id');
    $vendorName = $request->get('vendor_name');
    $paymentMethodId = $request->get('payment_method_id');
    $paymentStatus = $request->get('payment_status');
    $employeeId = $request->get('employee_id');
    $requiresReceipt = $request->get('requires_receipt');
    $isRecurring = $request->get('is_recurring');
    
    // Validate dates
    try {
        $startDate = Carbon::parse($startDate)->format('Y-m-d');
        $endDate = Carbon::parse($endDate)->format('Y-m-d');
        
        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }
    } catch (\Exception $e) {
        $startDate = now()->startOfMonth()->format('Y-m-d');
        $endDate = now()->endOfMonth()->format('Y-m-d');
    }
    
    // Build main query
    $query = Expense::with(['category', 'paymentMethod', 'employee'])
        ->where('tenant_id', $tenantId)
        ->whereBetween('date', [$startDate, $endDate]);
    
    // Apply filters
    if ($categoryId && is_numeric($categoryId)) {
        $query->where('category_id', (int)$categoryId);
    }
    
    if ($vendorName && strlen(trim($vendorName)) >= 2) {
        $query->where('vendor_name', 'like', '%' . trim($vendorName) . '%');
    }
    
    if ($paymentMethodId && is_numeric($paymentMethodId)) {
        $query->where('payment_method_id', (int)$paymentMethodId);
    }
    
    if ($paymentStatus && in_array($paymentStatus, ['pending', 'paid', 'reimbursed'])) {
        $query->where('payment_status', $paymentStatus);
    }
    
    if ($employeeId && is_numeric($employeeId)) {
        $query->where('employee_id', (int)$employeeId);
    }
    
    if ($requiresReceipt && in_array($requiresReceipt, ['0', '1'])) {
        $query->whereHas('category', function($q) use ($requiresReceipt) {
            $q->where('requires_receipt', (bool)$requiresReceipt);
        });
    }
    
    if ($isRecurring && in_array($isRecurring, ['0', '1'])) {
        $query->where('is_recurring', (bool)$isRecurring);
    }
    
    // Get summary statistics
    $summary = [
        'total_expenses' => $query->count(),
        'total_amount' => (float)($query->sum('total_amount') ?? 0),
        'total_tax' => (float)($query->sum('tax_amount') ?? 0),
        'avg_expense' => (float)($query->avg('total_amount') ?? 0),
        'max_expense' => (float)($query->max('total_amount') ?? 0),
        'min_expense' => (float)($query->min('total_amount') ?? 0),
    ];
    
    // Get daily breakdown
    $dailyBreakdown = DB::table('expenses')
        ->select(
            DB::raw('DATE(date) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_amount) as total'),
            DB::raw('SUM(tax_amount) as tax'),
            DB::raw('AVG(total_amount) as average')
        )
        ->where('tenant_id', $tenantId)
        ->whereBetween('date', [$startDate, $endDate])
        ->when($categoryId, function($q) use ($categoryId) {
            return $q->where('category_id', $categoryId);
        })
        ->when($paymentMethodId, function($q) use ($paymentMethodId) {
            return $q->where('payment_method_id', $paymentMethodId);
        })
        ->when($paymentStatus, function($q) use ($paymentStatus) {
            return $q->where('payment_status', $paymentStatus);
        })
        ->when($employeeId, function($q) use ($employeeId) {
            return $q->where('employee_id', $employeeId);
        })
        ->when($isRecurring, function($q) use ($isRecurring) {
            return $q->where('is_recurring', $isRecurring);
        })
        ->groupBy(DB::raw('DATE(date)'))
        ->orderBy('date', 'desc')
        ->get();
    
    // Get top expenses - use the same query but with order by amount
    $topExpenses = $query->orderBy('total_amount', 'desc')->take(10)->get();
    
    // Get filter options
    $categories = ExpenseCategory::where('tenant_id', $tenantId)
        ->where('is_active', true)
        ->orderBy('name')
        ->get();
    
    $paymentMethods = PaymentMethod::where('tenant_id', $tenantId)
        ->where('is_active', true)
        ->orderBy('name')
        ->get();
    
    $employees = Employee::where('tenant_id', $tenantId)
        ->where('is_active', true)
        ->orderBy('first_name')
        ->get();
    
    return view('reports.expenses.summary', [
        'summary' => $summary,
        'dailyBreakdown' => $dailyBreakdown,
        'topExpenses' => $topExpenses,
        'categories' => $categories,
        'paymentMethods' => $paymentMethods,
        'employees' => $employees,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'categoryId' => $categoryId,
        'vendorName' => $vendorName,
        'paymentMethodId' => $paymentMethodId,
        'paymentStatus' => $paymentStatus,
        'employeeId' => $employeeId,
        'requiresReceipt' => $requiresReceipt,
        'isRecurring' => $isRecurring,
    ]);
}
    
    // Expenses by Category
    public function byCategory(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        $categoryBreakdown = Expense::where('expenses.tenant_id', $tenantId)
            ->whereBetween('expenses.date', [$startDate, $endDate])
            ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->select(
                'expense_categories.name as category_name',
                'expense_categories.code as category_code',
                DB::raw('COUNT(*) as expense_count'),
                DB::raw('SUM(expenses.amount) as total_amount'),
                DB::raw('SUM(expenses.tax_amount) as total_tax'),
                DB::raw('SUM(expenses.total_amount) as grand_total'),
                DB::raw('AVG(expenses.total_amount) as average_amount'),
                DB::raw('MAX(expenses.total_amount) as max_amount'),
                DB::raw('MIN(expenses.total_amount) as min_amount')
            )
            ->groupBy('expense_categories.id', 'expense_categories.name', 'expense_categories.code')
            ->orderBy('grand_total', 'desc')
            ->get();
        
        $totalExpenses = $categoryBreakdown->sum('grand_total');
        
        // Monthly trend by category
        $monthlyTrend = Expense::where('expenses.tenant_id', $tenantId)
            ->whereBetween('expenses.date', [$startDate, $endDate])
            ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->select(
                DB::raw('YEAR(expenses.date) as year'),
                DB::raw('MONTH(expenses.date) as month'),
                'expense_categories.name as category_name',
                DB::raw('SUM(expenses.total_amount) as monthly_total')
            )
            ->groupBy('year', 'month', 'expense_categories.name')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->groupBy('category_name');
        
        $categories = ExpenseCategory::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('reports.expenses.by-category', compact(
            'categoryBreakdown',
            'totalExpenses',
            'monthlyTrend',
            'categories',
            'startDate',
            'endDate'
        ));
    }
    
    // Expenses by Vendor
    public function byVendor(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $vendorFilter = $request->get('vendor_name');
        
        $query = Expense::where('tenant_id', $tenantId)
            ->whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('vendor_name')
            ->where('vendor_name', '!=', '');
        
        if ($vendorFilter) {
            $query->where('vendor_name', 'like', "%{$vendorFilter}%");
        }
        
        $vendorBreakdown = $query->select(
                'vendor_name',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(total_amount) as grand_total'),
                DB::raw('AVG(total_amount) as average_transaction'),
                DB::raw('MAX(total_amount) as largest_transaction'),
                DB::raw('MIN(total_amount) as smallest_transaction'),
                DB::raw('COUNT(DISTINCT category_id) as categories_used')
            )
            ->groupBy('vendor_name')
            ->orderBy('grand_total', 'desc')
            ->get();
        
        // Vendor payment methods
        $vendorPaymentMethods = $query->clone()
            ->join('payment_methods', 'expenses.payment_method_id', '=', 'payment_methods.id')
            ->select(
                'vendor_name',
                'payment_methods.name as payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(expenses.total_amount) as total')
            )
            ->groupBy('vendor_name', 'payment_methods.name')
            ->get()
            ->groupBy('vendor_name');
        
        // Top vendors by month
        $monthlyVendorActivity = $query->clone()
            ->select(
                DB::raw('YEAR(date) as year'),
                DB::raw('MONTH(date) as month'),
                'vendor_name',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as monthly_total')
            )
            ->groupBy('year', 'month', 'vendor_name')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('monthly_total', 'desc')
            ->get();
        
        $uniqueVendors = Expense::where('tenant_id', $tenantId)
            ->whereNotNull('vendor_name')
            ->where('vendor_name', '!=', '')
            ->distinct('vendor_name')
            ->pluck('vendor_name')
            ->sort()
            ->values();
        
        return view('reports.expenses.by-vendor', compact(
            'vendorBreakdown',
            'vendorPaymentMethods',
            'monthlyVendorActivity',
            'uniqueVendors',
            'startDate',
            'endDate',
            'vendorFilter'
        ));
    }
    
    // Employee Expenses Report
    public function byEmployee(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $employeeId = $request->get('employee_id');
        $requiresApproval = $request->get('requires_approval');
        
        $query = Expense::where('expenses.tenant_id', $tenantId)
            ->whereBetween('expenses.date', [$startDate, $endDate])
            ->join('employees', 'expenses.employee_id', '=', 'employees.id')
            ->whereNotNull('employee_id');
        
        if ($employeeId) {
            $query->where('expenses.employee_id', $employeeId);
        }
        
        if ($requiresApproval !== null) {
            $query->whereHas('category', function($q) use ($requiresApproval) {
                $q->where('requires_approval', $requiresApproval);
            });
        }
        
        $employeeBreakdown = $query->select(
                'employees.first_name',
                'employees.last_name',
                'employees.employee_id',
                'employees.department',
                DB::raw('CONCAT(employees.first_name, " ", employees.last_name) as employee_name'),
                DB::raw('COUNT(*) as expense_count'),
                DB::raw('SUM(expenses.amount) as total_amount'),
                DB::raw('SUM(expenses.tax_amount) as total_tax'),
                DB::raw('SUM(expenses.total_amount) as grand_total'),
                DB::raw('AVG(expenses.total_amount) as average_expense'),
                DB::raw('MAX(expenses.total_amount) as max_expense'),
                DB::raw('COUNT(CASE WHEN expenses.payment_status = "pending" THEN 1 END) as pending_count'),
                DB::raw('COUNT(CASE WHEN expenses.payment_status = "paid" THEN 1 END) as paid_count'),
                DB::raw('COUNT(CASE WHEN expenses.payment_status = "reimbursed" THEN 1 END) as reimbursed_count')
            )
            ->groupBy(
                'employees.id',
                'employees.first_name',
                'employees.last_name',
                'employees.employee_id',
                'employees.department'
            )
            ->orderBy('grand_total', 'desc')
            ->get();
        
        // Employee monthly spending
        $monthlySpending = $query->clone()
            ->select(
                DB::raw('YEAR(expenses.date) as year'),
                DB::raw('MONTH(expenses.date) as month'),
                'employees.first_name',
                'employees.last_name',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(expenses.total_amount) as monthly_total')
            )
            ->groupBy('year', 'month', 'employees.id', 'employees.first_name', 'employees.last_name')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->groupBy(function($item) {
                return $item->first_name . ' ' . $item->last_name;
            });
        
        // Categories by employee
        $employeeCategories = $query->clone()
            ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->select(
                'employees.first_name',
                'employees.last_name',
                'expense_categories.name as category_name',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(expenses.total_amount) as total')
            )
            ->groupBy('employees.id', 'employees.first_name', 'employees.last_name', 'expense_categories.name')
            ->orderBy('total', 'desc')
            ->get()
            ->groupBy(function($item) {
                return $item->first_name . ' ' . $item->last_name;
            });
        
        $employees = Employee::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();
        
        return view('reports.expenses.by-employee', compact(
            'employeeBreakdown',
            'monthlySpending',
            'employeeCategories',
            'employees',
            'startDate',
            'endDate',
            'employeeId',
            'requiresApproval'
        ));
    }
    
    // Budget vs Actual Report
    public function budgetVsActual(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        
        // Get budgeted categories
        $budgetedCategories = ExpenseCategory::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->whereNotNull('budget_monthly')
            ->orWhereNotNull('budget_annual')
            ->orderBy('name')
            ->get();
        
        $budgetData = [];
        $totalBudgetMonthly = 0;
        $totalBudgetAnnual = 0;
        $totalActualMonthly = 0;
        $totalActualAnnual = 0;
        
        foreach ($budgetedCategories as $category) {
            // Get actual expenses for the period
            $monthlyActual = Expense::where('tenant_id', $tenantId)
                ->where('category_id', $category->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->sum('total_amount');
            
            $annualActual = Expense::where('tenant_id', $tenantId)
                ->where('category_id', $category->id)
                ->whereYear('date', $year)
                ->sum('total_amount');
            
            $budgetMonthly = $category->budget_monthly ?? 0;
            $budgetAnnual = $category->budget_annual ?? ($budgetMonthly * 12);
            
            $varianceMonthly = $budgetMonthly - $monthlyActual;
            $varianceAnnual = $budgetAnnual - $annualActual;
            
            $budgetData[] = [
                'category' => $category,
                'budget_monthly' => $budgetMonthly,
                'actual_monthly' => $monthlyActual,
                'variance_monthly' => $varianceMonthly,
                'variance_percentage_monthly' => $budgetMonthly > 0 ? ($varianceMonthly / $budgetMonthly) * 100 : 0,
                
                'budget_annual' => $budgetAnnual,
                'actual_annual' => $annualActual,
                'variance_annual' => $varianceAnnual,
                'variance_percentage_annual' => $budgetAnnual > 0 ? ($varianceAnnual / $budgetAnnual) * 100 : 0,
            ];
            
            $totalBudgetMonthly += $budgetMonthly;
            $totalBudgetAnnual += $budgetAnnual;
            $totalActualMonthly += $monthlyActual;
            $totalActualAnnual += $annualActual;
        }
        
        // Summary
        $summary = [
            'total_budget_monthly' => $totalBudgetMonthly,
            'total_actual_monthly' => $totalActualMonthly,
            'total_variance_monthly' => $totalBudgetMonthly - $totalActualMonthly,
            
            'total_budget_annual' => $totalBudgetAnnual,
            'total_actual_annual' => $totalActualAnnual,
            'total_variance_annual' => $totalBudgetAnnual - $totalActualAnnual,
            
            'under_budget_count' => collect($budgetData)->where('variance_monthly', '>', 0)->count(),
            'over_budget_count' => collect($budgetData)->where('variance_monthly', '<', 0)->count(),
            'on_budget_count' => collect($budgetData)->where('variance_monthly', '==', 0)->count(),
        ];
        
        // Monthly trend for each category
        $monthlyTrends = [];
        foreach ($budgetedCategories as $category) {
            $monthlyData = Expense::where('tenant_id', $tenantId)
                ->where('category_id', $category->id)
                ->whereYear('date', $year)
                ->select(
                    DB::raw('MONTH(date) as month'),
                    DB::raw('SUM(total_amount) as actual')
                )
                ->groupBy(DB::raw('MONTH(date)'))
                ->orderBy('month')
                ->get()
                ->keyBy('month');
            
            $trend = [];
            for ($m = 1; $m <= 12; $m++) {
                $trend[$m] = [
                    'month' => $m,
                    'budget' => $category->budget_monthly ?? 0,
                    'actual' => $monthlyData[$m]->actual ?? 0,
                    'variance' => ($category->budget_monthly ?? 0) - ($monthlyData[$m]->actual ?? 0)
                ];
            }
            
            $monthlyTrends[$category->id] = $trend;
        }
        
        $years = range(date('Y') - 5, date('Y'));
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        
        return view('reports.expenses.budget-vs-actual', compact(
            'budgetData',
            'summary',
            'monthlyTrends',
            'years',
            'months',
            'year',
            'month'
        ));
    }
    
    // Export functions for each report
    public function exportSummary(Request $request)
    {
        $data = $this->summary($request);
        // Add export logic here
        return response()->streamDownload(function() use ($data) {
            echo "Expense Summary Report\n";
            echo "Generated: " . date('Y-m-d H:i:s') . "\n\n";
            // Export data in CSV format
        }, 'expense-summary-' . date('Y-m-d') . '.csv');
    }


    // Expenses by Payment Method
    public function byPaymentMethod(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $paymentMethodId = $request->get('payment_method_id');
        
        $query = Expense::where('expenses.tenant_id', $tenantId)
            ->whereBetween('expenses.date', [$startDate, $endDate])
            ->join('payment_methods', 'expenses.payment_method_id', '=', 'payment_methods.id');
        
        if ($paymentMethodId) {
            $query->where('payment_methods.id', $paymentMethodId);
        }
        
        $methodBreakdown = $query->select(
                'payment_methods.id',
                'payment_methods.name as method_name',
                'payment_methods.type as method_type',
                'payment_methods.is_active',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(expenses.amount) as total_amount'),
                DB::raw('SUM(expenses.tax_amount) as total_tax'),
                DB::raw('SUM(expenses.total_amount) as grand_total'),
                DB::raw('AVG(expenses.total_amount) as average_transaction'),
                DB::raw('MAX(expenses.total_amount) as max_transaction'),
                DB::raw('MIN(expenses.total_amount) as min_transaction'),
                DB::raw('COUNT(DISTINCT expenses.category_id) as categories_used'),
                DB::raw('COUNT(DISTINCT expenses.vendor_name) as vendors_used')
            )
            ->groupBy(
                'payment_methods.id',
                'payment_methods.name',
                'payment_methods.type',
                'payment_methods.is_active'
            )
            ->orderBy('grand_total', 'desc')
            ->get();
        
        // Monthly trend by payment method
        $monthlyTrend = Expense::where('expenses.tenant_id', $tenantId)
            ->whereBetween('expenses.date', [$startDate, $endDate])
            ->join('payment_methods', 'expenses.payment_method_id', '=', 'payment_methods.id')
            ->select(
                DB::raw('YEAR(expenses.date) as year'),
                DB::raw('MONTH(expenses.date) as month'),
                'payment_methods.name as method_name',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(expenses.total_amount) as monthly_total')
            )
            ->groupBy('year', 'month', 'payment_methods.name')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->groupBy('method_name');
        
        // Payment method by category
        $methodByCategory = Expense::where('expenses.tenant_id', $tenantId)
            ->whereBetween('expenses.date', [$startDate, $endDate])
            ->join('payment_methods', 'expenses.payment_method_id', '=', 'payment_methods.id')
            ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->select(
                'payment_methods.name as method_name',
                'expense_categories.name as category_name',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(expenses.total_amount) as total_amount')
            )
            ->groupBy('payment_methods.name', 'expense_categories.name')
            ->orderBy('method_name')
            ->orderBy('total_amount', 'desc')
            ->get()
            ->groupBy('method_name');
        
        $paymentMethods = PaymentMethod::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('reports.expenses.by-payment-method', compact(
            'methodBreakdown',
            'monthlyTrend',
            'methodByCategory',
            'paymentMethods',
            'startDate',
            'endDate',
            'paymentMethodId'
        ));
    }
    
    // Recurring Expenses Report
    public function recurring(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        $frequency = $request->get('frequency');
        $categoryId = $request->get('category_id');
        $status = $request->get('status', 'active'); // active, upcoming, overdue
        
        $query = Expense::where('tenant_id', $tenantId)
            ->where('is_recurring', true)
            ->with(['category', 'paymentMethod']);
        
        if ($frequency) {
            $query->where('recurring_frequency', $frequency);
        }
        
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        // Status filtering
        $today = Carbon::today();
        if ($status === 'active') {
            $query->where('next_recurring_date', '>=', $today)
                  ->orWhereNull('next_recurring_date');
        } elseif ($status === 'upcoming') {
            $nextWeek = $today->copy()->addWeek();
            $query->whereBetween('next_recurring_date', [$today, $nextWeek]);
        } elseif ($status === 'overdue') {
            $query->where('next_recurring_date', '<', $today);
        }
        
        $recurringExpenses = $query->orderBy('next_recurring_date', 'asc')
            ->get();
        
        // Group by frequency
        $byFrequency = $recurringExpenses->groupBy('recurring_frequency')->map(function($items) {
            return [
                'count' => $items->count(),
                'total_monthly' => $items->sum('total_amount'),
                'total_annual' => $items->sum(function($item) {
                    $multiplier = match($item->recurring_frequency) {
                        'weekly' => 4.33 * 12, // Approximate weeks in year
                        'monthly' => 12,
                        'quarterly' => 4,
                        'annually' => 1,
                        default => 12
                    };
                    return $item->total_amount * $multiplier;
                })
            ];
        });
        
        // Upcoming expenses in next 30 days
        $upcomingNext30Days = $recurringExpenses->filter(function($expense) use ($today) {
            if (!$expense->next_recurring_date) return false;
            $nextDate = Carbon::parse($expense->next_recurring_date);
            return $nextDate->between($today, $today->copy()->addDays(30));
        })->sortBy('next_recurring_date');
        
        // Annual projection
        $annualProjection = $recurringExpenses->sum(function($expense) {
            $multiplier = match($expense->recurring_frequency) {
                'weekly' => 52,
                'monthly' => 12,
                'quarterly' => 4,
                'annually' => 1,
                default => 12
            };
            return $expense->total_amount * $multiplier;
        });
        
        // Historical recurring expense execution
        $historicalExecutions = Expense::where('tenant_id', $tenantId)
            ->whereNotNull('original_recurring_id')
            ->whereBetween('date', [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear()
            ])
            ->select(
                DB::raw('MONTH(date) as month'),
                DB::raw('COUNT(*) as execution_count'),
                DB::raw('SUM(total_amount) as monthly_total')
            )
            ->groupBy(DB::raw('MONTH(date)'))
            ->orderBy('month')
            ->get()
            ->keyBy('month');
        
        $categories = ExpenseCategory::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $frequencies = [
            'weekly' => 'Weekly',
            'monthly' => 'Monthly', 
            'quarterly' => 'Quarterly',
            'annually' => 'Annually'
        ];
        
        return view('reports.expenses.recurring', compact(
            'recurringExpenses',
            'byFrequency',
            'upcomingNext30Days',
            'annualProjection',
            'historicalExecutions',
            'categories',
            'frequencies',
            'frequency',
            'categoryId',
            'status'
        ));
    }
    
    // Expense Trends Report
    public function trends(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        $period = $request->get('period', 'monthly'); // monthly, quarterly, yearly
        $year = $request->get('year', date('Y'));
        $categoryId = $request->get('category_id');
        
        $query = Expense::where('tenant_id', $tenantId);
        
        if ($period === 'monthly') {
            $startDate = Carbon::create($year, 1, 1)->startOfYear();
            $endDate = Carbon::create($year, 12, 31)->endOfYear();
            
            $query->whereBetween('date', [$startDate, $endDate]);
            
            $monthlyTrend = $query->clone()
                ->select(
                    DB::raw('YEAR(date) as year'),
                    DB::raw('MONTH(date) as month'),
                    DB::raw('COUNT(*) as expense_count'),
                    DB::raw('SUM(amount) as total_amount'),
                    DB::raw('SUM(tax_amount) as total_tax'),
                    DB::raw('SUM(total_amount) as grand_total'),
                    DB::raw('AVG(total_amount) as average_amount')
                )
                ->groupBy(DB::raw('YEAR(date), MONTH(date)'))
                ->orderBy('year')
                ->orderBy('month')
                ->get();
            
            // Previous year comparison
            $prevYear = $year - 1;
            $prevYearTrend = Expense::where('tenant_id', $tenantId)
                ->whereBetween('date', [
                    Carbon::create($prevYear, 1, 1)->startOfYear(),
                    Carbon::create($prevYear, 12, 31)->endOfYear()
                ])
                ->select(
                    DB::raw('MONTH(date) as month'),
                    DB::raw('SUM(total_amount) as monthly_total')
                )
                ->groupBy(DB::raw('MONTH(date)'))
                ->orderBy('month')
                ->get()
                ->keyBy('month');
            
            $trendData = [];
            for ($month = 1; $month <= 12; $month++) {
                $currentMonth = $monthlyTrend->firstWhere('month', $month);
                $previousMonth = $prevYearTrend->get($month);
                
                $trendData[$month] = [
                    'month' => $month,
                    'current_year' => $currentMonth ? $currentMonth->grand_total : 0,
                    'previous_year' => $previousMonth ? $previousMonth->monthly_total : 0,
                    'growth' => $previousMonth ? 
                        (($currentMonth->grand_total - $previousMonth->monthly_total) / $previousMonth->monthly_total) * 100 : 0,
                    'expense_count' => $currentMonth ? $currentMonth->expense_count : 0,
                ];
            }
            
            // Category trends within the year
            $categoryTrends = Expense::where('tenant_id', $tenantId)
                ->whereBetween('date', [$startDate, $endDate])
                ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
                ->select(
                    'expense_categories.name as category_name',
                    DB::raw('MONTH(date) as month'),
                    DB::raw('SUM(total_amount) as monthly_total')
                )
                ->groupBy('expense_categories.name', DB::raw('MONTH(date)'))
                ->orderBy('expense_categories.name')
                ->orderBy('month')
                ->get()
                ->groupBy('category_name');
            
        } elseif ($period === 'quarterly') {
            // Quarterly trend logic
            $quarters = [];
            for ($quarter = 1; $quarter <= 4; $quarter++) {
                $startMonth = ($quarter - 1) * 3 + 1;
                $endMonth = $startMonth + 2;
                
                $quarterTotal = Expense::where('tenant_id', $tenantId)
                    ->whereYear('date', $year)
                    ->whereBetween(DB::raw('MONTH(date)'), [$startMonth, $endMonth])
                    ->sum('total_amount');
                
                $quarters[$quarter] = [
                    'quarter' => $quarter,
                    'total' => $quarterTotal,
                    'start_month' => $startMonth,
                    'end_month' => $endMonth
                ];
            }
            
            $trendData = $quarters;
            
        } elseif ($period === 'yearly') {
            // Yearly trend (last 5 years)
            $currentYear = date('Y');
            $years = range($currentYear - 4, $currentYear);
            
            $yearlyTrend = [];
            foreach ($years as $yearItem) {
                $yearTotal = Expense::where('tenant_id', $tenantId)
                    ->whereYear('date', $yearItem)
                    ->sum('total_amount');
                
                $yearlyTrend[$yearItem] = [
                    'year' => $yearItem,
                    'total' => $yearTotal,
                    'expense_count' => Expense::where('tenant_id', $tenantId)
                        ->whereYear('date', $yearItem)
                        ->count(),
                    'average' => Expense::where('tenant_id', $tenantId)
                        ->whereYear('date', $yearItem)
                        ->avg('total_amount')
                ];
            }
            
            $trendData = $yearlyTrend;
        }
        
        // Moving averages and growth rates
        if ($period === 'monthly') {
            $monthlyTotals = collect($trendData)->pluck('current_year');
            
            // 3-month moving average
            $movingAverages = [];
            for ($i = 2; $i < count($monthlyTotals); $i++) {
                $movingAverages[$i + 1] = (
                    $monthlyTotals[$i - 2] + 
                    $monthlyTotals[$i - 1] + 
                    $monthlyTotals[$i]
                ) / 3;
            }
            
            // Month-over-month growth
            $momGrowth = [];
            for ($i = 1; $i < count($monthlyTotals); $i++) {
                if ($monthlyTotals[$i - 1] > 0) {
                    $momGrowth[$i + 1] = (($monthlyTotals[$i] - $monthlyTotals[$i - 1]) / $monthlyTotals[$i - 1]) * 100;
                }
            }
        }
        
        $categories = ExpenseCategory::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        $years = range(date('Y') - 5, date('Y'));
        
        return view('reports.expenses.trends', compact(
            'trendData',
            'period',
            'year',
            'categoryId',
            'categories',
            'years',
            'categoryTrends',
            'movingAverages',
            'momGrowth'
        ));
    }
    
    // Tax Report
    public function taxReport(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $categoryId = $request->get('category_id');
        $taxType = $request->get('tax_type'); // taxable, non-taxable, all
        
        $query = Expense::where('tenant_id', $tenantId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with(['category']);
        
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        if ($taxType === 'taxable') {
            $query->where('tax_amount', '>', 0);
        } elseif ($taxType === 'non-taxable') {
            $query->where('tax_amount', '=', 0);
        }
        
        // Tax summary
        $taxSummary = [
            'total_expenses' => $query->count(),
            'total_amount' => $query->sum('amount'),
            'total_tax' => $query->sum('tax_amount'),
            'total_with_tax' => $query->sum('total_amount'),
            'taxable_expenses' => $query->clone()->where('tax_amount', '>', 0)->count(),
            'non_taxable_expenses' => $query->clone()->where('tax_amount', '=', 0)->count(),
            'avg_tax_rate' => $query->where('tax_amount', '>', 0)
                ->avg(DB::raw('(tax_amount / amount) * 100'))
        ];
        
        // Tax by category
        $taxByCategory = Expense::where('expenses.tenant_id', $tenantId)
            ->whereBetween('expenses.date', [$startDate, $endDate])
            ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->select(
                'expense_categories.name as category_name',
                DB::raw('COUNT(*) as expense_count'),
                DB::raw('SUM(expenses.amount) as subtotal'),
                DB::raw('SUM(expenses.tax_amount) as tax_total'),
                DB::raw('SUM(expenses.total_amount) as grand_total'),
                DB::raw('AVG((expenses.tax_amount / expenses.amount) * 100) as avg_tax_rate'),
                DB::raw('COUNT(CASE WHEN expenses.tax_amount > 0 THEN 1 END) as taxable_count'),
                DB::raw('COUNT(CASE WHEN expenses.tax_amount = 0 THEN 1 END) as non_taxable_count')
            )
            ->groupBy('expense_categories.name')
            ->orderBy('tax_total', 'desc')
            ->get();
        
        // Monthly tax breakdown
        $monthlyTax = Expense::where('tenant_id', $tenantId)
            ->whereBetween('date', [$startDate, $endDate])
            ->select(
                DB::raw('YEAR(date) as year'),
                DB::raw('MONTH(date) as month'),
                DB::raw('SUM(amount) as monthly_subtotal'),
                DB::raw('SUM(tax_amount) as monthly_tax'),
                DB::raw('SUM(total_amount) as monthly_total'),
                DB::raw('COUNT(*) as expense_count'),
                DB::raw('AVG((tax_amount / amount) * 100) as avg_monthly_tax_rate')
            )
            ->groupBy(DB::raw('YEAR(date), MONTH(date)'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        
        // Top tax expenses
        $topTaxExpenses = Expense::where('tenant_id', $tenantId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('tax_amount', '>', 0)
            ->with(['category', 'paymentMethod'])
            ->orderBy('tax_amount', 'desc')
            ->take(20)
            ->get();
        
        // Tax rate distribution
        $taxRateDistribution = Expense::where('tenant_id', $tenantId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('tax_amount', '>', 0)
            ->where('amount', '>', 0)
            ->select(
                DB::raw('ROUND((tax_amount / amount) * 100, 0) as tax_rate_percent'),
                DB::raw('COUNT(*) as expense_count'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(tax_amount) as total_tax')
            )
            ->groupBy(DB::raw('ROUND((tax_amount / amount) * 100, 0)'))
            ->orderBy('tax_rate_percent')
            ->get();
        
        $categories = ExpenseCategory::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('reports.expenses.tax-report', compact(
            'taxSummary',
            'taxByCategory',
            'monthlyTax',
            'topTaxExpenses',
            'taxRateDistribution',
            'categories',
            'startDate',
            'endDate',
            'categoryId',
            'taxType'
        ));
    }
    
    // Expense Audit Report
    public function audit(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        
        $startDate = $request->get('start_date', Carbon::now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $auditType = $request->get('audit_type', 'all'); // all, missing_receipts, unapproved, high_value
        $employeeId = $request->get('employee_id');
        
        $query = Expense::where('expenses.tenant_id', $tenantId)
            ->whereBetween('expenses.date', [$startDate, $endDate])
            ->with(['category', 'paymentMethod', 'employee', 'approver']);
        
        // Apply audit type filters
        switch ($auditType) {
            case 'missing_receipts':
                $query->whereHas('category', function($q) {
                    $q->where('requires_receipt', true);
                })->where(function($q) {
                    $q->whereNull('receipt_url')
                      ->orWhere('receipt_url', '');
                });
                break;
                
            case 'unapproved':
                $query->whereHas('category', function($q) {
                    $q->where('requires_approval', true);
                })->whereNull('approved_at');
                break;
                
            case 'high_value':
                $threshold = $request->get('threshold', 1000);
                $query->where('total_amount', '>=', $threshold);
                break;
                
            case 'late_submissions':
                $query->where(DB::raw('DATEDIFF(created_at, date)'), '>', 7);
                break;
                
            case 'policy_violations':
                // Example: Expenses without receipt when required
                $query->whereHas('category', function($q) {
                    $q->where('requires_receipt', true);
                })->where(function($q) {
                    $q->whereNull('receipt_url')
                      ->orWhere('receipt_url', '');
                });
                break;
        }
        
        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }
        
        $auditItems = $query->orderBy('date', 'desc')
            ->get();
        
        // Audit statistics
        $auditStats = [
            'total_items' => $auditItems->count(),
            'total_amount' => $auditItems->sum('total_amount'),
            'missing_receipts' => $auditItems->filter(function($item) {
                return $item->category->requires_receipt && empty($item->receipt_url);
            })->count(),
            'unapproved' => $auditItems->filter(function($item) {
                return $item->category->requires_approval && !$item->approved_at;
            })->count(),
            'high_value' => $auditItems->filter(function($item) use ($request) {
                $threshold = $request->get('threshold', 1000);
                return $item->total_amount >= $threshold;
            })->count(),
            'average_age_days' => $auditItems->avg(function($item) {
                return Carbon::parse($item->created_at)->diffInDays(Carbon::today());
            }),
        ];
        
        // Group by category for analysis
        $byCategory = $auditItems->groupBy('category.name')->map(function($items, $category) {
            return [
                'category' => $category,
                'count' => $items->count(),
                'total_amount' => $items->sum('total_amount'),
                'missing_receipts' => $items->filter(function($item) {
                    return $item->category->requires_receipt && empty($item->receipt_url);
                })->count(),
                'unapproved' => $items->filter(function($item) {
                    return $item->category->requires_approval && !$item->approved_at;
                })->count(),
                'avg_amount' => $items->avg('total_amount')
            ];
        })->sortByDesc('count')->values();
        
        // Monthly audit trend
        $monthlyAudit = Expense::where('tenant_id', $tenantId)
            ->whereBetween('date', [$startDate, $endDate])
            ->select(
                DB::raw('YEAR(date) as year'),
                DB::raw('MONTH(date) as month'),
                DB::raw('COUNT(*) as total_expenses'),
                DB::raw('COUNT(CASE WHEN receipt_url IS NULL OR receipt_url = "" THEN 1 END) as missing_receipts'),
                DB::raw('COUNT(CASE WHEN approved_at IS NULL THEN 1 END) as unapproved'),
                DB::raw('SUM(total_amount) as monthly_total')
            )
            ->groupBy(DB::raw('YEAR(date), MONTH(date)'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
        
        // Employee compliance
        $employeeCompliance = Expense::where('expenses.tenant_id', $tenantId)
            ->whereBetween('expenses.date', [$startDate, $endDate])
            ->whereNotNull('employee_id')
            ->join('employees', 'expenses.employee_id', '=', 'employees.id')
            ->select(
                'employees.first_name',
                'employees.last_name',
                'employees.employee_id',
                DB::raw('CONCAT(employees.first_name, " ", employees.last_name) as employee_name'),
                DB::raw('COUNT(*) as total_expenses'),
                DB::raw('COUNT(CASE WHEN (expenses.receipt_url IS NULL OR expenses.receipt_url = "") 
                         AND expense_categories.requires_receipt = 1 THEN 1 END) as missing_receipts'),
                DB::raw('COUNT(CASE WHEN expenses.approved_at IS NULL 
                         AND expense_categories.requires_approval = 1 THEN 1 END) as unapproved'),
                DB::raw('AVG(expenses.total_amount) as avg_expense')
            )
            ->join('expense_categories', 'expenses.category_id', '=', 'expense_categories.id')
            ->groupBy(
                'employees.id',
                'employees.first_name',
                'employees.last_name',
                'employees.employee_id'
            )
            ->orderBy('missing_receipts', 'desc')
            ->get();
        
        $employees = Employee::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();
        
        $auditTypes = [
            'all' => 'All Items',
            'missing_receipts' => 'Missing Receipts',
            'unapproved' => 'Unapproved Expenses',
            'high_value' => 'High Value Expenses',
            'late_submissions' => 'Late Submissions',
            'policy_violations' => 'Policy Violations'
        ];
        
        return view('reports.expenses.audit', compact(
            'auditItems',
            'auditStats',
            'byCategory',
            'monthlyAudit',
            'employeeCompliance',
            'employees',
            'startDate',
            'endDate',
            'auditType',
            'employeeId',
            'auditTypes'
        ));
    }
    
    // Export functions for each report
    public function exportByCategory(Request $request)
    {
        $data = $this->byCategory($request);
        // Export logic here
        return response()->streamDownload(function() use ($data) {
            // CSV export implementation
        }, 'expense-by-category-' . date('Y-m-d') . '.csv');
    }

}
