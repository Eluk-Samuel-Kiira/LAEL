<x-app-layout>
    @section('title', 'Tax Report')
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
                            <h3 class="card-title">Tax Report Filters</h3>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('reports.expenses.tax-report') }}" id="filterForm">
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
                                        <label class="form-label">Tax Type</label>
                                        <select class="form-select" name="tax_type">
                                            <option value="all" {{ $taxType == 'all' ? 'selected' : '' }}>All Expenses</option>
                                            <option value="taxable" {{ $taxType == 'taxable' ? 'selected' : '' }}>Taxable Only</option>
                                            <option value="non-taxable" {{ $taxType == 'non-taxable' ? 'selected' : '' }}>Non-taxable Only</option>
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
                
                <!-- Tax Summary -->
                <div class="row g-5 g-xl-8 mb-8">
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-body text-center py-5">
                                <span class="fs-2hx fw-bold text-gray-800 d-block">${{ number_format($taxSummary['total_tax'], 2) }}</span>
                                <span class="text-gray-500 fw-semibold fs-6">Total Tax</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-body text-center py-5">
                                <span class="fs-2hx fw-bold text-gray-800 d-block">{{ number_format($taxSummary['taxable_expenses']) }}</span>
                                <span class="text-gray-500 fw-semibold fs-6">Taxable Expenses</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-body text-center py-5">
                                <span class="fs-2hx fw-bold text-gray-800 d-block">{{ number_format($taxSummary['avg_tax_rate'], 1) }}%</span>
                                <span class="text-gray-500 fw-semibold fs-6">Average Tax Rate</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-body text-center py-5">
                                <span class="fs-2hx fw-bold text-gray-800 d-block">${{ number_format($taxSummary['total_with_tax'], 2) }}</span>
                                <span class="text-gray-500 fw-semibold fs-6">Total with Tax</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tax by Category -->
                <div class="card mb-8">
                    <div class="card-header">
                        <h3 class="card-title">Tax Analysis by Category</h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-dashed gy-4 align-middle gs-0">
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="min-w-200px">Category</th>
                                        <th class="min-w-100px text-end">Total Expenses</th>
                                        <th class="min-w-100px text-end">Taxable</th>
                                        <th class="min-w-100px text-end">Non-taxable</th>
                                        <th class="min-w-120px text-end">Subtotal</th>
                                        <th class="min-w-120px text-end">Tax Amount</th>
                                        <th class="min-w-120px text-end">Grand Total</th>
                                        <th class="min-w-100px text-end">Avg Tax Rate</th>
                                        <th class="min-w-150px">Tax Distribution</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($taxByCategory as $category)
                                    <tr>
                                        <td>{{ $category->category_name }}</td>
                                        <td class="text-end">{{ number_format($category->expense_count) }}</td>
                                        <td class="text-end">{{ number_format($category->taxable_count) }}</td>
                                        <td class="text-end">{{ number_format($category->non_taxable_count) }}</td>
                                        <td class="text-end">${{ number_format($category->subtotal, 2) }}</td>
                                        <td class="text-end">
                                            <span class="fs-6 fw-bold text-primary">${{ number_format($category->tax_total, 2) }}</span>
                                        </td>
                                        <td class="text-end">${{ number_format($category->grand_total, 2) }}</td>
                                        <td class="text-end">{{ number_format($category->avg_tax_rate, 1) }}%</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $taxablePercent = $category->expense_count > 0 ? 
                                                        ($category->taxable_count / $category->expense_count) * 100 : 0;
                                                @endphp
                                                <div class="progress h-8px w-100 me-3">
                                                    <div class="progress-bar bg-success" style="width: {{ $taxablePercent }}%"></div>
                                                    <div class="progress-bar bg-light" style="width: {{ 100 - $taxablePercent }}%"></div>
                                                </div>
                                                <span class="fs-7 fw-bold">{{ number_format($taxablePercent, 0) }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Monthly Tax Breakdown -->
                <div class="card mb-8">
                    <div class="card-header">
                        <h3 class="card-title">Monthly Tax Summary</h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-dashed gy-4 align-middle gs-0">
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="min-w-100px">Month</th>
                                        <th class="min-w-100px text-end">Expenses</th>
                                        <th class="min-w-120px text-end">Subtotal</th>
                                        <th class="min-w-120px text-end">Tax Amount</th>
                                        <th class="min-w-120px text-end">Total</th>
                                        <th class="min-w-100px text-end">Avg Tax Rate</th>
                                        <th class="min-w-150px">Tax Trend</th>
                                        <th class="min-w-100px">Tax Efficiency</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthlyTax as $month)
                                    <tr>
                                        <td>
                                            @php
                                                $monthName = DateTime::createFromFormat('!m', $month->month)->format('M');
                                            @endphp
                                            <span class="fs-7 fw-bold text-gray-800">{{ $monthName }} {{ $month->year }}</span>
                                        </td>
                                        <td class="text-end">{{ number_format($month->expense_count) }}</td>
                                        <td class="text-end">${{ number_format($month->monthly_subtotal, 2) }}</td>
                                        <td class="text-end">
                                            <span class="fs-6 fw-bold text-primary">${{ number_format($month->monthly_tax, 2) }}</span>
                                        </td>
                                        <td class="text-end">${{ number_format($month->monthly_total, 2) }}</td>
                                        <td class="text-end">{{ number_format($month->avg_monthly_tax_rate, 1) }}%</td>
                                        <td>
                                            @php
                                                $taxRatio = $month->monthly_subtotal > 0 ? 
                                                    ($month->monthly_tax / $month->monthly_subtotal) * 100 : 0;
                                                if ($taxRatio > 20) {
                                                    $trend = 'High Tax';
                                                    $color = 'danger';
                                                } elseif ($taxRatio > 10) {
                                                    $trend = 'Moderate Tax';
                                                    $color = 'warning';
                                                } elseif ($taxRatio > 5) {
                                                    $trend = 'Normal Tax';
                                                    $color = 'primary';
                                                } else {
                                                    $trend = 'Low Tax';
                                                    $color = 'success';
                                                }
                                            @endphp
                                            <span class="badge badge-light-{{ $color }}">{{ $trend }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $taxEfficiency = $month->monthly_tax > 0 ? 
                                                    ($month->monthly_subtotal / $month->monthly_tax) : 0;
                                                if ($taxEfficiency > 15) {
                                                    $efficiency = 'High';
                                                    $effColor = 'success';
                                                } elseif ($taxEfficiency > 10) {
                                                    $efficiency = 'Good';
                                                    $effColor = 'primary';
                                                } elseif ($taxEfficiency > 5) {
                                                    $efficiency = 'Average';
                                                    $effColor = 'warning';
                                                } else {
                                                    $efficiency = 'Low';
                                                    $effColor = 'danger';
                                                }
                                            @endphp
                                            <span class="badge badge-light-{{ $effColor }}">{{ $efficiency }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Top Tax Expenses -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top 20 Tax Expenses</h3>
                        <div class="card-toolbar">
                            <span class="text-gray-500 fs-7">Highest Tax Amounts</span>
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
                                        <th class="min-w-150px">Payment Method</th>
                                        <th class="min-w-120px text-end">Amount</th>
                                        <th class="min-w-120px text-end">Tax Amount</th>
                                        <th class="min-w-100px text-end">Tax Rate</th>
                                        <th class="min-w-120px text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topTaxExpenses as $expense)
                                    <tr>
                                        <td>{{ $expense->expense_number }}</td>
                                        <td>{{ $expense->date->format('M d, Y') }}</td>
                                        <td>{{ Str::limit($expense->description, 40) }}</td>
                                        <td>{{ $expense->category->name ?? 'N/A' }}</td>
                                        <td>{{ $expense->paymentMethod->name ?? 'N/A' }}</td>
                                        <td class="text-end">${{ number_format($expense->amount, 2) }}</td>
                                        <td class="text-end">
                                            <span class="fs-6 fw-bold text-primary">${{ number_format($expense->tax_amount, 2) }}</span>
                                        </td>
                                        <td class="text-end">
                                            @php
                                                $taxRate = $expense->amount > 0 ? ($expense->tax_amount / $expense->amount) * 100 : 0;
                                            @endphp
                                            {{ number_format($taxRate, 1) }}%
                                        </td>
                                        <td class="text-end">${{ number_format($expense->total_amount, 2) }}</td>
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
            window.location.href = "{{ route('reports.expenses.tax-report') }}";
        }
    </script>
    @endpush
    
    @endsection
</x-app-layout>