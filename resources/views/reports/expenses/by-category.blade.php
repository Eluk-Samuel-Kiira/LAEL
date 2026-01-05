<x-app-layout>
    @section('title', 'Expenses by Category')
    @section('content')
    
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">
                    Expenses by Category
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('reports.expenses.summary') }}" class="text-muted text-hover-primary">
                            Reports
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">Expense Reports</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">By Category</li>
                </ul>
            </div>
            
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div class="dropdown">
                    <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ki-duotone ki-file-down fs-2"></i> Export
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('reports.expenses.export.by-category', request()->query()) }}">
                                <i class="ki-duotone ki-file-excel fs-2 me-2 text-success"></i>
                                Export to Excel
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="exportToCSV()">
                                <i class="ki-duotone ki-file-csv fs-2 me-2 text-primary"></i>
                                Export to CSV
                            </a>
                        </li>
                    </ul>
                </div>
                
                <button class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#filterSection">
                    <i class="ki-duotone ki-filter fs-2"></i> Filters
                </button>
            </div>
        </div>
    </div>
    
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                
                <!-- Filters -->
                <div class="collapse {{ request()->hasAny(['start_date', 'end_date']) ? 'show' : '' }}" id="filterSection">
                    <div class="card mb-8">
                        <div class="card-header">
                            <h3 class="card-title">Filter Options</h3>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('reports.expenses.by-category') }}" id="filterForm">
                                <div class="row g-5">
                                    <div class="col-md-4">
                                        <label class="form-label">Start Date</label>
                                        <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">End Date</label>
                                        <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Category</label>
                                        <select class="form-select" name="category_id">
                                            <option value="">All Categories</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
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
                
                <!-- Summary Cards -->
                <div class="row g-5 g-xl-8 mb-8">
                    <div class="col-xl-4">
                        <div class="card card-flush h-md-100">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Total Categories</h2>
                                </div>
                            </div>
                            <div class="card-body pt-1">
                                <div class="d-flex flex-column text-center my-7">
                                    <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1">
                                        {{ $categoryBreakdown->count() }}
                                    </span>
                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Active Categories</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-4">
                        <div class="card card-flush h-md-100">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Total Expenses</h2>
                                </div>
                            </div>
                            <div class="card-body pt-1">
                                <div class="d-flex flex-column text-center my-7">
                                    <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1">
                                        ${{ number_format($totalExpenses, 2) }}
                                    </span>
                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Across All Categories</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-4">
                        <div class="card card-flush h-md-100">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>Average per Category</h2>
                                </div>
                            </div>
                            <div class="card-body pt-1">
                                <div class="d-flex flex-column text-center my-7">
                                    <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1">
                                        ${{ number_format($categoryBreakdown->avg('grand_total'), 2) }}
                                    </span>
                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Mean Category Total</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Category Breakdown Table -->
                <div class="card mb-8">
                    <div class="card-header">
                        <h3 class="card-title">Category Breakdown</h3>
                        <div class="card-toolbar">
                            <span class="text-gray-500 fs-7">{{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</span>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-dashed gy-4 align-middle gs-0" id="categoryTable">
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="min-w-200px">Category</th>
                                        <th class="min-w-100px text-end">Code</th>
                                        <th class="min-w-100px text-end">Expenses</th>
                                        <th class="min-w-120px text-end">Total Amount</th>
                                        <th class="min-w-120px text-end">Tax Amount</th>
                                        <th class="min-w-120px text-end">Grand Total</th>
                                        <th class="min-w-100px text-end">Average</th>
                                        <th class="min-w-100px text-end">% of Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categoryBreakdown as $category)
                                    @php
                                        $percentage = $totalExpenses > 0 ? ($category->grand_total / $totalExpenses) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="fs-7 fw-bold text-gray-800">{{ $category->category_name }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge badge-light-primary">{{ $category->category_code }}</span>
                                        </td>
                                        <td class="text-end">{{ number_format($category->expense_count) }}</td>
                                        <td class="text-end">${{ number_format($category->total_amount, 2) }}</td>
                                        <td class="text-end">${{ number_format($category->total_tax, 2) }}</td>
                                        <td class="text-end">
                                            <span class="fs-6 fw-bold text-primary">${{ number_format($category->grand_total, 2) }}</span>
                                        </td>
                                        <td class="text-end">${{ number_format($category->average_amount, 2) }}</td>
                                        <td class="text-end">
                                            <div class="d-flex align-items-center justify-content-end">
                                                <span class="fs-7 fw-bold me-2">{{ number_format($percentage, 1) }}%</span>
                                                <div class="progress h-6px w-50px">
                                                    <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="fs-6 fw-bold text-gray-700">
                                        <td>Total</td>
                                        <td class="text-end">-</td>
                                        <td class="text-end">{{ number_format($categoryBreakdown->sum('expense_count')) }}</td>
                                        <td class="text-end">${{ number_format($categoryBreakdown->sum('total_amount'), 2) }}</td>
                                        <td class="text-end">${{ number_format($categoryBreakdown->sum('total_tax'), 2) }}</td>
                                        <td class="text-end">${{ number_format($totalExpenses, 2) }}</td>
                                        <td class="text-end">${{ number_format($categoryBreakdown->avg('average_amount'), 2) }}</td>
                                        <td class="text-end">100%</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Monthly Trends by Category -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Monthly Trends by Category</h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-dashed gy-4 align-middle gs-0">
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="min-w-150px">Category</th>
                                        @php
                                            $months = [];
                                            for($m = 1; $m <= 12; $m++) {
                                                $months[] = DateTime::createFromFormat('!m', $m)->format('M');
                                            }
                                        @endphp
                                        @foreach($months as $month)
                                            <th class="min-w-80px text-end">{{ $month }}</th>
                                        @endforeach
                                        <th class="min-w-100px text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categoryTrends as $categoryName => $monthlyData)
                                    <tr>
                                        <td>{{ $categoryName }}</td>
                                        @php
                                            $categoryTotal = 0;
                                            $monthlyTotals = $monthlyData->keyBy('month');
                                        @endphp
                                        @for($m = 1; $m <= 12; $m++)
                                            @php
                                                $monthTotal = $monthlyTotals->get($m)->monthly_total ?? 0;
                                                $categoryTotal += $monthTotal;
                                            @endphp
                                            <td class="text-end">
                                                @if($monthTotal > 0)
                                                    <span class="fs-7 text-gray-700">${{ number_format($monthTotal, 0) }}</span>
                                                @else
                                                    <span class="fs-7 text-gray-400">-</span>
                                                @endif
                                            </td>
                                        @endfor
                                        <td class="text-end">
                                            <span class="fs-7 fw-bold text-primary">${{ number_format($categoryTotal, 0) }}</span>
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
            window.location.href = "{{ route('reports.expenses.by-category') }}";
        }
        
        function exportToCSV() {
            // Implement CSV export
        }
    </script>
    @endpush
    
    @endsection
</x-app-layout>