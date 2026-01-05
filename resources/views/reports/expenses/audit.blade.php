<x-app-layout>
    @section('title', 'Expense Audit Report')
    @section('content')
    
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <!-- Toolbar -->
    </div>
    
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                
                <!-- Filters -->
                <div class="collapse show" id="filterSection">
                    <div class="card mb-8">
                        <div class="card-header">
                            <h3 class="card-title">Audit Filters</h3>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('reports.expenses.audit') }}" id="filterForm">
                                <div class="row g-5">
                                    <div class="col-md-3">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">End Date</label>
                                        <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Audit Type</label>
                                        <select class="form-select" name="audit_type">
                                            @foreach($auditTypes as $key => $name)
                                                <option value="{{ $key }}" {{ $auditType == $key ? 'selected' : '' }}>
                                                    {{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Employee</label>
                                        <select class="form-select" name="employee_id">
                                            <option value="">All Employees</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}" {{ $employeeId == $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                                        <button type="button" class="btn btn-light" onclick="resetFilters()">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Audit Statistics -->
                <div class="row g-5 g-xl-8 mb-8">
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-body text-center py-5">
                                <span class="fs-2hx fw-bold text-gray-800 d-block">{{ $auditStats['total_items'] }}</span>
                                <span class="text-gray-500 fw-semibold fs-6">Audit Items</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-body text-center py-5">
                                <span class="fs-2hx fw-bold text-gray-800 d-block">{{ $auditStats['missing_receipts'] }}</span>
                                <span class="text-gray-500 fw-semibold fs-6">Missing Receipts</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-body text-center py-5">
                                <span class="fs-2hx fw-bold text-gray-800 d-block">{{ $auditStats['unapproved'] }}</span>
                                <span class="text-gray-500 fw-semibold fs-6">Unapproved</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-body text-center py-5">
                                <span class="fs-2hx fw-bold text-gray-800 d-block">{{ $auditStats['high_value'] }}</span>
                                <span class="text-gray-500 fw-semibold fs-6">High Value</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Audit Items Table -->
                <div class="card mb-8">
                    <div class="card-header">
                        <h3 class="card-title">Audit Findings</h3>
                        <div class="card-toolbar">
                            <span class="text-gray-500 fs-7">{{ $auditTypes[$auditType] }} - {{ $auditItems->count() }} items</span>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-dashed gy-4 align-middle gs-0">
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="min-w-100px">Expense #</th>
                                        <th class="min-w-120px">Date</th>
                                        <th class="min-w-200px">Description</th>
                                        <th class="min-w-150px">Category</th>
                                        <th class="min-w-150px">Employee</th>
                                        <th class="min-w-120px text-end">Amount</th>
                                        <th class="min-w-150px">Issues</th>
                                        <th class="min-w-120px">Status</th>
                                        <th class="min-w-100px">Age</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($auditItems as $expense)
                                    @php
                                        $issues = [];
                                        if ($expense->category->requires_receipt && empty($expense->receipt_url)) {
                                            $issues[] = 'Missing Receipt';
                                        }
                                        if ($expense->category->requires_approval && !$expense->approved_at) {
                                            $issues[] = 'Unapproved';
                                        }
                                        if ($expense->total_amount >= 1000) {
                                            $issues[] = 'High Value';
                                        }
                                        $age = $expense->created_at->diffInDays(now());
                                    @endphp
                                    <tr>
                                        <td>{{ $expense->expense_number }}</td>
                                        <td>{{ $expense->date->format('M d, Y') }}</td>
                                        <td>{{ Str::limit($expense->description, 40) }}</td>
                                        <td>{{ $expense->category->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($expense->employee)
                                                {{ $expense->employee->first_name }} {{ $expense->employee->last_name }}
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-end">${{ number_format($expense->total_amount, 2) }}</td>
                                        <td>
                                            @foreach($issues as $issue)
                                                @php
                                                    $colors = [
                                                        'Missing Receipt' => 'danger',
                                                        'Unapproved' => 'warning',
                                                        'High Value' => 'primary'
                                                    ];
                                                @endphp
                                                <span class="badge badge-light-{{ $colors[$issue] ?? 'secondary' }} me-1 mb-1">
                                                    {{ $issue }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @switch($expense->payment_status)
                                                @case('pending')
                                                    <span class="badge badge-light-warning">Pending</span>
                                                    @break
                                                @case('paid')
                                                    <span class="badge badge-light-success">Paid</span>
                                                    @break
                                                @case('reimbursed')
                                                    <span class="badge badge-light-info">Reimbursed</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <span class="fs-7 text-gray-600">{{ $age }} days</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Audit by Category -->
                <div class="card mb-8">
                    <div class="card-header">
                        <h3 class="card-title">Audit Findings by Category</h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-dashed gy-4 align-middle gs-0">
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="min-w-200px">Category</th>
                                        <th class="min-w-100px text-end">Total Items</th>
                                        <th class="min-w-120px text-end">Total Amount</th>
                                        <th class="min-w-100px text-end">Missing Receipts</th>
                                        <th class="min-w-100px text-end">Unapproved</th>
                                        <th class="min-w-100px text-end">Avg Amount</th>
                                        <th class="min-w-150px">Compliance Rate</th>
                                        <th class="min-w-100px">Risk Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($byCategory as $category)
                                    <tr>
                                        <td>{{ $category['category'] }}</td>
                                        <td class="text-end">{{ number_format($category['count']) }}</td>
                                        <td class="text-end">${{ number_format($category['total_amount'], 2) }}</td>
                                        <td class="text-end">{{ number_format($category['missing_receipts']) }}</td>
                                        <td class="text-end">{{ number_format($category['unapproved']) }}</td>
                                        <td class="text-end">${{ number_format($category['avg_amount'], 2) }}</td>
                                        <td>
                                            @php
                                                $complianceRate = $category['count'] > 0 ? 
                                                    (($category['count'] - $category['missing_receipts'] - $category['unapproved']) / $category['count']) * 100 : 100;
                                            @endphp
                                            <div class="d-flex align-items-center">
                                                <div class="progress h-8px w-100 me-3">
                                                    <div class="progress-bar bg-success" style="width: {{ $complianceRate }}%"></div>
                                                </div>
                                                <span class="fs-7 fw-bold">{{ number_format($complianceRate, 0) }}%</span>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                if ($complianceRate < 60) {
                                                    $risk = 'High Risk';
                                                    $color = 'danger';
                                                } elseif ($complianceRate < 80) {
                                                    $risk = 'Medium Risk';
                                                    $color = 'warning';
                                                } elseif ($complianceRate < 95) {
                                                    $risk = 'Low Risk';
                                                    $color = 'primary';
                                                } else {
                                                    $risk = 'Compliant';
                                                    $color = 'success';
                                                }
                                            @endphp
                                            <span class="badge badge-light-{{ $color }}">{{ $risk }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Employee Compliance -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Employee Compliance Report</h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-dashed gy-4 align-middle gs-0">
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="min-w-200px">Employee</th>
                                        <th class="min-w-100px">Employee ID</th>
                                        <th class="min-w-100px text-end">Total Expenses</th>
                                        <th class="min-w-100px text-end">Missing Receipts</th>
                                        <th class="min-w-100px text-end">Unapproved</th>
                                        <th class="min-w-100px text-end">Compliance Rate</th>
                                        <th class="min-w-120px text-end">Avg Expense</th>
                                        <th class="min-w-100px">Compliance Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employeeCompliance as $employee)
                                    <tr>
                                        <td>{{ $employee->employee_name }}</td>
                                        <td>{{ $employee->employee_id }}</td>
                                        <td class="text-end">{{ number_format($employee->total_expenses) }}</td>
                                        <td class="text-end">{{ number_format($employee->missing_receipts) }}</td>
                                        <td class="text-end">{{ number_format($employee->unapproved) }}</td>
                                        <td class="text-end">
                                            @php
                                                $complianceRate = $employee->total_expenses > 0 ? 
                                                    (($employee->total_expenses - $employee->missing_receipts - $employee->unapproved) / $employee->total_expenses) * 100 : 100;
                                            @endphp
                                            {{ number_format($complianceRate, 1) }}%
                                        </td>
                                        <td class="text-end">${{ number_format($employee->avg_expense, 2) }}</td>
                                        <td>
                                            @php
                                                if ($complianceRate < 70) {
                                                    $status = 'Needs Improvement';
                                                    $color = 'danger';
                                                } elseif ($complianceRate < 85) {
                                                    $status = 'Satisfactory';
                                                    $color = 'warning';
                                                } elseif ($complianceRate < 95) {
                                                    $status = 'Good';
                                                    $color = 'primary';
                                                } else {
                                                    $status = 'Excellent';
                                                    $color = 'success';
                                                }
                                            @endphp
                                            <span class="badge badge-light-{{ $color }}">{{ $status }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        function resetFilters() {
            document.getElementById('filterForm').reset();
            window.location.href = "{{ route('reports.expenses.audit') }}";
        }
    </script>
    @endpush
    
    @endsection
</x-app-layout>