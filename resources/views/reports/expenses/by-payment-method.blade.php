<x-app-layout>
    @section('title', 'Expenses by Payment Method')
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
                            <form method="GET" action="{{ route('reports.expenses.by-payment-method') }}" id="filterForm">
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
                                        <label class="form-label">Payment Method</label>
                                        <select class="form-select" name="payment_method_id">
                                            <option value="">All Methods</option>
                                            @foreach($paymentMethods as $method)
                                                <option value="{{ $method->id }}" {{ $paymentMethodId == $method->id ? 'selected' : '' }}>
                                                    {{ $method->name }}
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
                
                <!-- Payment Method Cards -->
                <div class="row g-5 g-xl-8 mb-8">
                    @foreach($methodBreakdown as $method)
                    <div class="col-xl-4">
                        <div class="card card-flush h-md-100">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>{{ $method->method_name }}</h2>
                                </div>
                                <div class="card-toolbar">
                                    <span class="badge badge-light-{{ $method->is_active ? 'success' : 'danger' }}">
                                        {{ $method->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-column mb-5">
                                    <span class="fs-2hx fw-bold text-gray-800">${{ number_format($method->grand_total, 2) }}</span>
                                    <span class="text-gray-500">Total Amount</span>
                                </div>
                                <div class="d-flex flex-stack">
                                    <span class="text-gray-500">Transactions</span>
                                    <span class="fw-bold">{{ number_format($method->transaction_count) }}</span>
                                </div>
                                <div class="d-flex flex-stack mt-2">
                                    <span class="text-gray-500">Average</span>
                                    <span class="fw-bold">${{ number_format($method->average_transaction, 2) }}</span>
                                </div>
                                <div class="d-flex flex-stack mt-2">
                                    <span class="text-gray-500">Categories</span>
                                    <span class="fw-bold">{{ $method->categories_used }}</span>
                                </div>
                                <div class="d-flex flex-stack mt-2">
                                    <span class="text-gray-500">Vendors</span>
                                    <span class="fw-bold">{{ $method->vendors_used }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Payment Method Table -->
                <div class="card mb-8">
                    <div class="card-header">
                        <h3 class="card-title">Payment Method Analysis</h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-dashed gy-4 align-middle gs-0">
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="min-w-200px">Payment Method</th>
                                        <th class="min-w-100px">Type</th>
                                        <th class="min-w-100px text-end">Transactions</th>
                                        <th class="min-w-120px text-end">Total Amount</th>
                                        <th class="min-w-120px text-end">Tax Amount</th>
                                        <th class="min-w-120px text-end">Avg Transaction</th>
                                        <th class="min-w-120px text-end">Max Transaction</th>
                                        <th class="min-w-100px text-end">% of Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $grandTotal = $methodBreakdown->sum('grand_total'); @endphp
                                    @foreach($methodBreakdown as $method)
                                    @php $percentage = $grandTotal > 0 ? ($method->grand_total / $grandTotal) * 100 : 0; @endphp
                                    <tr>
                                        <td>
                                            <span class="fs-6 fw-bold text-gray-800">{{ $method->method_name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-primary">{{ ucfirst($method->method_type) }}</span>
                                        </td>
                                        <td class="text-end">{{ number_format($method->transaction_count) }}</td>
                                        <td class="text-end">${{ number_format($method->total_amount, 2) }}</td>
                                        <td class="text-end">${{ number_format($method->total_tax, 2) }}</td>
                                        <td class="text-end">${{ number_format($method->average_transaction, 2) }}</td>
                                        <td class="text-end">${{ number_format($method->max_transaction, 2) }}</td>
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
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Method by Category -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Payment Methods by Category</h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-dashed gy-4 align-middle gs-0">
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="min-w-200px">Payment Method</th>
                                        <th class="min-w-200px">Category</th>
                                        <th class="min-w-100px text-end">Transactions</th>
                                        <th class="min-w-120px text-end">Total Amount</th>
                                        <th class="min-w-100px text-end">% of Method</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($methodByCategory as $methodName => $categories)
                                        @foreach($categories as $category)
                                        <tr>
                                            <td>{{ $methodName }}</td>
                                            <td>{{ $category->category_name }}</td>
                                            <td class="text-end">{{ number_format($category->transaction_count) }}</td>
                                            <td class="text-end">${{ number_format($category->total_amount, 2) }}</td>
                                            <td class="text-end">
                                                @php
                                                    $methodTotal = $methodBreakdown->firstWhere('method_name', $methodName)->grand_total ?? 1;
                                                    $percentage = ($category->total_amount / $methodTotal) * 100;
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
            window.location.href = "{{ route('reports.expenses.by-payment-method') }}";
        }
    </script>
    @endpush
    
    @endsection
</x-app-layout>