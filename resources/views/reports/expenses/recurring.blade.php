<x-app-layout>
    @section('title', 'Recurring Expenses Report')
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
                            <form method="GET" action="{{ route('reports.expenses.recurring') }}" id="filterForm">
                                <div class="row g-5">
                                    <div class="col-md-3">
                                        <label class="form-label">Frequency</label>
                                        <select class="form-select" name="frequency">
                                            <option value="">All Frequencies</option>
                                            @foreach($frequencies as $key => $name)
                                                <option value="{{ $key }}" {{ $frequency == $key ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Category</label>
                                        <select class="form-select" name="category_id">
                                            <option value="">All Categories</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" name="status">
                                            <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="upcoming" {{ $status == 'upcoming' ? 'selected' : '' }}>Upcoming (Next 7 days)</option>
                                            <option value="overdue" {{ $status == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                                            <button type="button" class="btn btn-light" onclick="resetFilters()">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Summary Cards -->
                <div class="row g-5 g-xl-8 mb-8">
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-body text-center py-5">
                                <span class="fs-2hx fw-bold text-gray-800 d-block">{{ $recurringExpenses->count() }}</span>
                                <span class="text-gray-500 fw-semibold fs-6">Recurring Expenses</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-body text-center py-5">
                                <span class="fs-2hx fw-bold text-gray-800 d-block">${{ number_format($annualProjection, 0) }}</span>
                                <span class="text-gray-500 fw-semibold fs-6">Annual Projection</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-body text-center py-5">
                                <span class="fs-2hx fw-bold text-gray-800 d-block">{{ $upcomingNext30Days->count() }}</span>
                                <span class="text-gray-500 fw-semibold fs-6">Upcoming (30 days)</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-body text-center py-5">
                                @php
                                    $totalMonthly = $recurringExpenses->sum('total_amount');
                                @endphp
                                <span class="fs-2hx fw-bold text-gray-800 d-block">${{ number_format($totalMonthly, 0) }}</span>
                                <span class="text-gray-500 fw-semibold fs-6">Monthly Total</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Frequency Breakdown -->
                <div class="card mb-8">
                    <div class="card-header">
                        <h3 class="card-title">Breakdown by Frequency</h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-dashed gy-4 align-middle gs-0">
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="min-w-150px">Frequency</th>
                                        <th class="min-w-100px text-end">Count</th>
                                        <th class="min-w-120px text-end">Monthly Total</th>
                                        < class="min-w-120px text-end">Annual Projection</th>
                                        <th class="min-w-100px text-end">Avg Amount</th>
                                        <th class="min-w-150px">Distribution</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($byFrequency as $freq => $data)
                                    <tr>
                                        <td>
                                            <span class="fs-6 fw-bold text-gray-800">{{ $frequencies[$freq] ?? ucfirst($freq) }}</span>
                                        </td>
                                        <td class="text-end">{{ number_format($data['count']) }}</td>
                                        <td class="text-end">${{ number_format($data['total_monthly'], 2) }}</td>
                                        <td class="text-end">${{ number_format($data['total_annual'], 2) }}</td>
                                        <td class="text-end">
                                            @php
                                                $avg = $data['count'] > 0 ? $data['total_monthly'] / $data['count'] : 0;
                                            @endphp
                                            ${{ number_format($avg, 2) }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress h-8px w-100 me-3">
                                                    @php
                                                        $totalCount = $recurringExpenses->count();
                                                        $percentage = $totalCount > 0 ? ($data['count'] / $totalCount) * 100 : 0;
                                                    @endphp
                                                    <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                                                </div>
                                                <span class="fs-7 fw-bold">{{ number_format($percentage, 1) }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Recurring Expenses List -->
                <div class="card mb-8">
                    <div class="card-header">
                        <h3 class="card-title">Recurring Expenses</h3>
                        <div class="card-toolbar">
                            <span class="text-gray-500 fs-7">Sorted by Next Due Date</span>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-dashed gy-4 align-middle gs-0">
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="min-w-100px">Expense #</th>
                                        <th class="min-w-200px">Description</th>
                                        <th class="min-w-150px">Category</th>
                                        <th class="min-w-150px">Vendor</th>
                                        <th class="min-w-100px">Frequency</th>
                                        <th class="min-w-120px">Next Due Date</th>
                                        <th class="min-w-120px">Last Executed</th>
                                        <th class="min-w-100px text-end">Amount</th>
                                        <th class="min-w-100px">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recurringExpenses as $expense)
                                    @php
                                        $today = \Carbon\Carbon::today();
                                        $nextDate = $expense->next_recurring_date ? \Carbon\Carbon::parse($expense->next_recurring_date) : null;
                                        
                                        if (!$nextDate) {
                                            $status = 'No Date';
                                            $color = 'secondary';
                                        } elseif ($nextDate->isPast()) {
                                            $status = 'Overdue';
                                            $color = 'danger';
                                        } elseif ($nextDate->diffInDays($today) <= 7) {
                                            $status = 'Due Soon';
                                            $color = 'warning';
                                        } else {
                                            $status = 'Active';
                                            $color = 'success';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $expense->expense_number }}</td>
                                        <td>{{ Str::limit($expense->description, 40) }}</td>
                                        <td>{{ $expense->category->name ?? 'N/A' }}</td>
                                        <td>{{ $expense->vendor_name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge badge-light-primary">{{ ucfirst($expense->recurring_frequency) }}</span>
                                        </td>
                                        <td>
                                            @if($nextDate)
                                                <span class="fs-7 text-gray-800">{{ $nextDate->format('M d, Y') }}</span>
                                                <span class="fs-8 text-gray-500 d-block">{{ $nextDate->diffForHumans() }}</span>
                                            @else
                                                <span class="fs-7 text-gray-500">Not set</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fs-7 text-gray-600">{{ $expense->date->format('M d, Y') }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fs-6 fw-bold text-primary">${{ number_format($expense->total_amount, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-{{ $color }}">{{ $status }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Upcoming Expenses -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Upcoming Expenses (Next 30 Days)</h3>
                    </div>
                    <div class="card-body pt-0">
                        @if($upcomingNext30Days->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-dashed gy-4 align-middle gs-0">
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="min-w-100px">Due Date</th>
                                        <th class="min-w-200px">Description</th>
                                        <th class="min-w-150px">Category</th>
                                        <th class="min-w-100px">Frequency</th>
                                        <th class="min-w-100px text-end">Amount</th>
                                        <th class="min-w-150px">Days Until Due</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingNext30Days as $expense)
                                    @php
                                        $dueDate = \Carbon\Carbon::parse($expense->next_recurring_date);
                                        $daysUntil = $dueDate->diffInDays(\Carbon\Carbon::today());
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="fs-7 fw-bold text-gray-800">{{ $dueDate->format('M d') }}</span>
                                            <span class="fs-8 text-gray-500 d-block">{{ $dueDate->format('D') }}</span>
                                        </td>
                                        <td>{{ Str::limit($expense->description, 50) }}</td>
                                        <td>{{ $expense->category->name ?? 'N/A' }}</td>
                                        <td>{{ ucfirst($expense->recurring_frequency) }}</td>
                                        <td class="text-end">${{ number_format($expense->total_amount, 2) }}</td>
                                        <td>
                                            @if($daysUntil == 0)
                                                <span class="badge badge-light-danger">Due Today</span>
                                            @elseif($daysUntil == 1)
                                                <span class="badge badge-light-warning">Tomorrow</span>
                                            @else
                                                <span class="fs-7 text-gray-600">in {{ $daysUntil }} days</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-10">
                            <i class="ki-duotone ki-check fs-2hx text-success mb-4"></i>
                            <h4 class="text-gray-800">No upcoming expenses in the next 30 days</h4>
                            <p class="text-gray-600">All recurring expenses are up to date</p>
                        </div>
                        @endif
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        function resetFilters() {
            document.getElementById('filterForm').reset();
            window.location.href = "{{ route('reports.expenses.recurring') }}";
        }
    </script>
    @endpush
    
    @endsection
</x-app-layout>