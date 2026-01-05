<x-app-layout>
    @section('title', 'Employee Expenses Report')
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
                            <h3 class="card-title">Filter Options</h3>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('reports.expenses.by-employee') }}" id="filterForm">
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
                                    <div class="col-md-3">
                                        <label class="form-label">Requires Approval</label>
                                        <select class="form-select" name="requires_approval">
                                            <option value="">All</option>
                                            <option value="1" {{ $requiresApproval === '1' ? 'selected' : '' }}>Requires Approval</option>
                                            <option value="0" {{ $requiresApproval === '0' ? 'selected' : '' }}>No Approval Required</option>
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
                
                <!-- Employee Summary -->
                <div class="row g-5 g-xl-8 mb-8">
                    @foreach($employeeBreakdown as $employee)
                    <div class="col-xl-4">
                        <div class="card card-flush h-md-100">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>{{ $employee->first_name }} {{ $employee->last_name }}</h2>
                                </div>
                                <div class="card-toolbar">
                                    <span class="badge badge-light-primary">{{ $employee->employee_id }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-column mb-5">
                                    <span class="fs-2hx fw-bold text-gray-800">${{ number_format($employee->grand_total, 2) }}</span>
                                    <span class="text-gray-500">Total Expenses</span>
                                </div>
                                <div class="d-flex flex-stack">
                                    <span class="text-gray-500">Department</span>
                                    <span class="fw-bold">{{ $employee->department ?? 'N/A' }}</span>
                                </div>
                                <div class="d-flex flex-stack mt-2">
                                    <span class="text-gray-500">Expense Count</span>
                                    <span class="fw-bold">{{ number_format($employee->expense_count) }}</span>
                                </div>
                                <div class="d-flex flex-stack mt-2">
                                    <span class="text-gray-500">Average Expense</span>
                                    <span class="fw-bold">${{ number_format($employee->average_expense, 2) }}</span>
                                </div>
                                <div class="d-flex flex-stack mt-2">
                                    <span class="text-gray-500">Status</span>
                                    <div>
                                        <span class="badge badge-light-success me-1">{{ $employee->paid_count }} Paid</span>
                                        <span class="badge badge-light-warning">{{ $employee->pending_count }} Pending</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Employee Breakdown Table -->
                <div class="card mb-8">
                    <div class="card-header">
                        <h3 class="card-title">Employee Expense Details</h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-dashed gy-4 align-middle gs-0">
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="min-w-200px">Employee</th>
                                        <th class="min-w-100px text-end">Employee ID</th>
                                        <th class="min-w-150px">Department</th>
                                        <th class="min-w-100px text-end">Expenses</th>
                                        <th class="min-w-120px text-end">Total Amount</th>
                                        <th class="min-w-120px text-end">Tax Amount</th>
                                        <th class="min-w-120px text-end">Average</th>
                                        <th class="min-w-150px">Payment Status</th>
                                        <th class="min-w-100px">Compliance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employeeBreakdown as $employee)
                                    <tr>
                                        <td>
                                            <span class="fs-6 fw-bold text-gray-800">{{ $employee->employee_name }}</span>
                                        </td>
                                        <td class="text-end">{{ $employee->employee_id }}</td>
                                        <td>{{ $employee->department ?? 'N/A' }}</td>
                                        <td class="text-end">{{ number_format($employee->expense_count) }}</td>
                                        <td class="text-end">${{ number_format($employee->total_amount, 2) }}</td>
                                        <td class="text-end">${{ number_format($employee->total_tax, 2) }}</td>
                                        <td class="text-end">${{ number_format($employee->average_expense, 2) }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex align-items-center">
                                                    <span class="bullet bullet-success bullet-sm me-2"></span>
                                                    <span class="fs-7">Paid: {{ $employee->paid_count }}</span>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <span class="bullet bullet-warning bullet-sm me-2"></span>
                                                    <span class="fs-7">Pending: {{ $employee->pending_count }}</span>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <span class="bullet bullet-info bullet-sm me-2"></span>
                                                    <span class="fs-7">Reimbursed: {{ $employee->reimbursed_count }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $pendingRate = $employee->expense_count > 0 ? ($employee->pending_count / $employee->expense_count) * 100 : 0;
                                                if ($pendingRate > 50) {
                                                    $color = 'danger';
                                                    $status = 'Low';
                                                } elseif ($pendingRate > 25) {
                                                    $color = 'warning';
                                                    $status = 'Medium';
                                                } else {
                                                    $color = 'success';
                                                    $status = 'High';
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
                
                <!-- Employee Categories -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Expense Categories by Employee</h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-dashed gy-4 align-middle gs-0">
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="min-w-200px">Employee</th>
                                        <th class="min-w-200px">Category</th>
                                        <th class="min-w-100px text-end">Count</th>
                                        <th class="min-w-120px text-end">Total Amount</th>
                                        <th class="min-w-100px text-end">% of Employee</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employeeCategories as $employeeName => $categories)
                                        @foreach($categories as $category)
                                        <tr>
                                            <td>{{ $employeeName }}</td>
                                            <td>{{ $category->category_name }}</td>
                                            <td class="text-end">{{ number_format($category->count) }}</td>
                                            <td class="text-end">${{ number_format($category->total, 2) }}</td>
                                            <td class="text-end">
                                                @php
                                                    $employeeTotal = $employeeBreakdown->firstWhere('employee_name', $employeeName)->grand_total ?? 1;
                                                    $percentage = ($category->total / $employeeTotal) * 100;
                                                @endphp
                                                {{ number_format($percentage, 1) }}%
                                            </td>
                                        </tr>
                                        @endforeach
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
            window.location.href = "{{ route('reports.expenses.by-employee') }}";
        }
    </script>
    @endpush
    
    @endsection
</x-app-layout>