<x-app-layout>
    @section('title', 'Expense Trends Analysis')
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
                            <h3 class="card-title">Analysis Period</h3>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('reports.expenses.trends') }}" id="filterForm">
                                <div class="row g-5">
                                    <div class="col-md-3">
                                        <label class="form-label">Period</label>
                                        <select class="form-select" name="period" onchange="this.form.submit()">
                                            <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                            <option value="quarterly" {{ $period == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                            <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Year</label>
                                        <select class="form-select" name="year" onchange="this.form.submit()">
                                            @foreach($years as $y)
                                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
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
                                        <label class="form-label">&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">Apply</button>
                                            <button type="button" class="btn btn-light" onclick="resetFilters()">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                @if($period == 'monthly')
                <!-- Monthly Trends -->
                <div class="row g-5 g-xl-8 mb-8">
                    <div class="col-xl-12">
                        <div class="card card-flush h-md-100">
                            <div class="card-header">
                                <h3 class="card-title">Monthly Expense Trends - {{ $year }}</h3>
                            </div>
                            <div class="card-body pt-0">
                                <div class="table-responsive">
                                    <table class="table table-row-bordered table-row-dashed gy-4 align-middle gs-0">
                                        <thead>
                                            <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                                <th class="min-w-100px">Month</th>
                                                <th class="min-w-120px text-end">{{ $year }} Total</th>
                                                <th class="min-w-120px text-end">{{ $year-1 }} Total</th>
                                                <th class="min-w-120px text-end">Growth</th>
                                                <th class="min-w-100px text-end">Expense Count</th>
                                                <th class="min-w-120px text-end">Average Amount</th>
                                                <th class="min-w-150px">Trend</th>
                                                <th class="min-w-100px">YoY Change</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($trendData as $month => $data)
                                            <tr>
                                                <td>
                                                    <span class="fs-7 fw-bold text-gray-800">
                                                        {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="fs-6 fw-bold text-primary">
                                                        ${{ number_format($data['current_year'], 2) }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="fs-6 text-gray-600">
                                                        ${{ number_format($data['previous_year'], 2) }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    @php
                                                        $growth = $data['growth'];
                                                        $growthColor = $growth >= 0 ? 'text-success' : 'text-danger';
                                                        $growthIcon = $growth >= 0 ? 'ki-arrow-up' : 'ki-arrow-down';
                                                    @endphp
                                                    <span class="{{ $growthColor }}">
                                                        <i class="ki-duotone {{ $growthIcon }} fs-2 me-1"></i>
                                                        {{ number_format(abs($growth), 1) }}%
                                                    </span>
                                                </td>
                                                <td class="text-end">{{ number_format($data['expense_count']) }}</td>
                                                <td class="text-end">
                                                    @php
                                                        $avg = $data['expense_count'] > 0 ? $data['current_year'] / $data['expense_count'] : 0;
                                                    @endphp
                                                    ${{ number_format($avg, 2) }}
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @php
                                                            $maxValue = max(max(array_column($trendData, 'current_year')), 1);
                                                            $barWidth = ($data['current_year'] / $maxValue) * 100;
                                                        @endphp
                                                        <div class="progress h-8px w-100 me-3">
                                                            <div class="progress-bar bg-primary" style="width: {{ $barWidth }}%"></div>
                                                        </div>
                                                        <span class="fs-7 fw-bold">{{ number_format($barWidth, 0) }}%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($data['growth'] > 10)
                                                        <span class="badge badge-light-danger">High Increase</span>
                                                    @elseif($data['growth'] > 0)
                                                        <span class="badge badge-light-warning">Moderate Increase</span>
                                                    @elseif($data['growth'] < -10)
                                                        <span class="badge badge-light-success">High Decrease</span>
                                                    @elseif($data['growth'] < 0)
                                                        <span class="badge badge-light-info">Moderate Decrease</span>
                                                    @else
                                                        <span class="badge badge-light-primary">Stable</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="fw-bold">
                                            @php
                                                $currentYearTotal = collect($trendData)->sum('current_year');
                                                $previousYearTotal = collect($trendData)->sum('previous_year');
                                                $totalGrowth = $previousYearTotal > 0 ? 
                                                    (($currentYearTotal - $previousYearTotal) / $previousYearTotal) * 100 : 0;
                                                $totalExpenseCount = collect($trendData)->sum('expense_count');
                                            @endphp
                                            <tr>
                                                <td>Total</td>
                                                <td class="text-end">${{ number_format($currentYearTotal, 2) }}</td>
                                                <td class="text-end">${{ number_format($previousYearTotal, 2) }}</td>
                                                <td class="text-end">
                                                    @php
                                                        $totalGrowthColor = $totalGrowth >= 0 ? 'text-success' : 'text-danger';
                                                        $totalGrowthIcon = $totalGrowth >= 0 ? 'ki-arrow-up' : 'ki-arrow-down';
                                                    @endphp
                                                    <span class="{{ $totalGrowthColor }}">
                                                        <i class="ki-duotone {{ $totalGrowthIcon }} fs-2 me-1"></i>
                                                        {{ number_format(abs($totalGrowth), 1) }}%
                                                    </span>
                                                </td>
                                                <td class="text-end">{{ number_format($totalExpenseCount) }}</td>
                                                <td class="text-end">
                                                    @php
                                                        $totalAvg = $totalExpenseCount > 0 ? $currentYearTotal / $totalExpenseCount : 0;
                                                    @endphp
                                                    ${{ number_format($totalAvg, 2) }}
                                                </td>
                                                <td colspan="2"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Moving Averages -->
                <div class="card mb-8">
                    <div class="card-header">
                        <h3 class="card-title">Moving Averages & Growth Rates</h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-dashed gy-4 align-middle gs-0">
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="min-w-100px">Month</th>
                                        <th class="min-w-120px text-end">Monthly Total</th>
                                        <th class="min-w-120px text-end">3-Month Moving Avg</th>
                                        <th class="min-w-120px text-end">MoM Growth</th>
                                        <th class="min-w-150px">Growth Trend</th>
                                        <th class="min-w-120px text-end">Cumulative Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trendData as $month => $data)
                                    <tr>
                                        <td>{{ DateTime::createFromFormat('!m', $month)->format('F') }}</td>
                                        <td class="text-end">${{ number_format($data['current_year'], 2) }}</td>
                                        <td class="text-end">
                                            @if(isset($movingAverages[$month]))
                                                ${{ number_format($movingAverages[$month], 2) }}
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if(isset($momGrowth[$month]))
                                                @php
                                                    $growth = $momGrowth[$month];
                                                    $growthColor = $growth >= 0 ? 'text-success' : 'text-danger';
                                                    $growthIcon = $growth >= 0 ? 'ki-arrow-up' : 'ki-arrow-down';
                                                @endphp
                                                <span class="{{ $growthColor }}">
                                                    <i class="ki-duotone {{ $growthIcon }} fs-2 me-1"></i>
                                                    {{ number_format(abs($growth), 1) }}%
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($momGrowth[$month]))
                                                @php
                                                    $growth = $momGrowth[$month];
                                                    if ($growth > 20) {
                                                        $trend = 'Rapid Growth';
                                                        $color = 'danger';
                                                    } elseif ($growth > 10) {
                                                        $trend = 'Strong Growth';
                                                        $color = 'warning';
                                                    } elseif ($growth > 0) {
                                                        $trend = 'Moderate Growth';
                                                        $color = 'primary';
                                                    } elseif ($growth > -10) {
                                                        $trend = 'Slight Decline';
                                                        $color = 'info';
                                                    } else {
                                                        $trend = 'Significant Decline';
                                                        $color = 'success';
                                                    }
                                                @endphp
                                                <span class="badge badge-light-{{ $color }}">{{ $trend }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @php
                                                $cumulative = 0;
                                                for($m = 1; $m <= $month; $m++) {
                                                    $cumulative += $trendData[$m]['current_year'] ?? 0;
                                                }
                                            @endphp
                                            ${{ number_format($cumulative, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                @elseif($period == 'quarterly')
                <!-- Quarterly Trends -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quarterly Expense Trends - {{ $year }}</h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-dashed gy-4 align-middle gs-0">
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="min-w-100px">Quarter</th>
                                        <th class="min-w-150px">Period</th>
                                        <th class="min-w-120px text-end">Total Expenses</th>
                                        <th class="min-w-120px text-end">% of Annual</th>
                                        <th class="min-w-150px">Seasonal Trend</th>
                                        <th class="min-w-100px">Quarterly Growth</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($trendData as $quarter => $data)
                                    <tr>
                                        <td>
                                            <span class="fs-6 fw-bold text-gray-800">Q{{ $quarter }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $startMonth = DateTime::createFromFormat('!m', $data['start_month'])->format('M');
                                                $endMonth = DateTime::createFromFormat('!m', $data['end_month'])->format('M');
                                            @endphp
                                            <span class="fs-7 text-gray-600">{{ $startMonth }} - {{ $endMonth }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fs-6 fw-bold text-primary">${{ number_format($data['total'], 2) }}</span>
                                        </td>
                                        <td class="text-end">
                                            @php
                                                $annualTotal = collect($trendData)->sum('total');
                                                $percentage = $annualTotal > 0 ? ($data['total'] / $annualTotal) * 100 : 0;
                                            @endphp
                                            {{ number_format($percentage, 1) }}%
                                        </td>
                                        <td>
                                            @php
                                                $quarterAvg = $annualTotal / 4;
                                                $deviation = (($data['total'] - $quarterAvg) / $quarterAvg) * 100;
                                                
                                                if ($deviation > 30) {
                                                    $trend = 'Peak Season';
                                                    $color = 'danger';
                                                } elseif ($deviation > 10) {
                                                    $trend = 'High Season';
                                                    $color = 'warning';
                                                } elseif ($deviation > -10) {
                                                    $trend = 'Normal';
                                                    $color = 'primary';
                                                } elseif ($deviation > -30) {
                                                    $trend = 'Low Season';
                                                    $color = 'info';
                                                } else {
                                                    $trend = 'Off Season';
                                                    $color = 'success';
                                                }
                                            @endphp
                                            <span class="badge badge-light-{{ $color }}">{{ $trend }}</span>
                                            <span class="fs-7 text-gray-500 ms-2">{{ number_format($deviation, 1) }}% from average</span>
                                        </td>
                                        <td>
                                            @php
                                                $prevQuarter = $quarter > 1 ? $trendData[$quarter - 1]['total'] : 0;
                                                $growth = $prevQuarter > 0 ? (($data['total'] - $prevQuarter) / $prevQuarter) * 100 : 0;
                                                
                                                if ($growth > 20) {
                                                    $growthStatus = 'Strong Growth';
                                                    $growthColor = 'danger';
                                                } elseif ($growth > 5) {
                                                    $growthStatus = 'Moderate Growth';
                                                    $growthColor = 'warning';
                                                } elseif ($growth > -5) {
                                                    $growthStatus = 'Stable';
                                                    $growthColor = 'primary';
                                                } elseif ($growth > -20) {
                                                    $growthStatus = 'Moderate Decline';
                                                    $growthColor = 'info';
                                                } else {
                                                    $growthStatus = 'Sharp Decline';
                                                    $growthColor = 'success';
                                                }
                                            @endphp
                                            <span class="badge badge-light-{{ $growthColor }}">{{ $growthStatus }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                @elseif($period == 'yearly')
                <!-- Yearly Trends -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Year-over-Year Expense Trends</h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-dashed gy-4 align-middle gs-0">
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="min-w-100px">Year</th>
                                        <th class="min-w-120px text-end">Total Expenses</th>
                                        <th class="min-w-120px text-end">Expense Count</th>
                                        <th class="min-w-120px text-end">Average Expense</th>
                                        <th class="min-w-120px text-end">YoY Growth</th>
                                        <th class="min-w-150px">Growth Trend</th>
                                        <th class="min-w-120px text-end">CAGR (5 Years)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $years = array_keys($trendData);
                                        sort($years);
                                    @endphp
                                    @foreach($years as $yearItem)
                                    @php
                                        $data = $trendData[$yearItem];
                                        $prevYear = $yearItem - 1;
                                        $prevData = $trendData[$prevYear] ?? null;
                                        $growth = $prevData ? (($data['total'] - $prevData['total']) / $prevData['total']) * 100 : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="fs-6 fw-bold text-gray-800">{{ $yearItem }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fs-6 fw-bold text-primary">${{ number_format($data['total'], 2) }}</span>
                                        </td>
                                        <td class="text-end">{{ number_format($data['expense_count']) }}</td>
                                        <td class="text-end">${{ number_format($data['average'], 2) }}</td>
                                        <td class="text-end">
                                            @if($prevData)
                                                @php
                                                    $growthColor = $growth >= 0 ? 'text-success' : 'text-danger';
                                                    $growthIcon = $growth >= 0 ? 'ki-arrow-up' : 'ki-arrow-down';
                                                @endphp
                                                <span class="{{ $growthColor }}">
                                                    <i class="ki-duotone {{ $growthIcon }} fs-2 me-1"></i>
                                                    {{ number_format(abs($growth), 1) }}%
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($prevData)
                                                @if($growth > 15)
                                                    <span class="badge badge-light-danger">Rapid Growth</span>
                                                @elseif($growth > 5)
                                                    <span class="badge badge-light-warning">Steady Growth</span>
                                                @elseif($growth > -5)
                                                    <span class="badge badge-light-primary">Stable</span>
                                                @elseif($growth > -15)
                                                    <span class="badge badge-light-info">Moderate Decline</span>
                                                @else
                                                    <span class="badge badge-light-success">Sharp Decline</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($loop->last && count($years) >= 5)
                                                @php
                                                    $firstYear = reset($trendData);
                                                    $lastYear = end($trendData);
                                                    $periods = count($years) - 1;
                                                    $cagr = pow(($lastYear['total'] / $firstYear['total']), 1/$periods) - 1;
                                                @endphp
                                                {{ number_format($cagr * 100, 1) }}%
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
                
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        function resetFilters() {
            document.getElementById('filterForm').reset();
            window.location.href = "{{ route('reports.expenses.trends') }}";
        }
    </script>
    @endpush
    
    @endsection
</x-app-layout>