<x-app-layout>
    @section('title', __('accounting.transaction_ledger'))
    @section('content')
    
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-gray-900 fw-bold fs-3 flex-column justify-content-center my-0">{{__('accounting.transaction_ledger')}}</h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('accounting.transaction-ledger') }}" class="text-muted text-hover-primary">
                            {{ __('accounting.basic_accounting') }}
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-500 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">{{__('accounting.transaction_ledger')}}</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <button class="btn btn-sm btn-primary" onclick="exportToExcel()">
                    <i class="ki-duotone ki-file-down fs-2"></i> {{ __('accounting.export') }}
                </button>
            </div>
        </div>
    </div>
    
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl">
                
                <!-- Filters -->
                <div class="card mb-8">
                    <div class="card-body">
                        <form id="filterForm" method="GET" class="row g-5">
                            <!-- Date Range -->
                            <div class="col-md-3">
                                <label class="form-label">{{ __('accounting.start_date') }}</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('accounting.end_date') }}</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] }}">
                            </div>
                            
                            <!-- Payment Method -->
                            <div class="col-md-3">
                                <label class="form-label">{{ __('accounting.payment_method') }}</label>
                                <select name="payment_method_id" class="form-select">
                                    <option value="">{{ __('accounting.all_methods') }}</option>
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method->id }}" {{ $filters['payment_method_id'] == $method->id ? 'selected' : '' }}>
                                            {{ $method->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Transaction Type -->
                            <div class="col-md-3">
                                <label class="form-label">{{ __('accounting.transaction_type') }}</label>
                                <select name="transaction_type" class="form-select">
                                    <option value="">{{ __('accounting.all_types') }}</option>
                                    @foreach($transactionTypes as $type)
                                        <option value="{{ $type }}" {{ $filters['transaction_type'] == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Status -->
                            <div class="col-md-3">
                                <label class="form-label">{{ __('accounting.status') }}</label>
                                <select name="status" class="form-select">
                                    <option value="">{{ __('accounting.all_statuses') }}</option>
                                    <option value="COMPLETED" {{ $filters['status'] == 'COMPLETED' ? 'selected' : '' }}>COMPLETED</option>
                                    <option value="PENDING" {{ $filters['status'] == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                                    <option value="FAILED" {{ $filters['status'] == 'FAILED' ? 'selected' : '' }}>FAILED</option>
                                    <option value="CANCELLED" {{ $filters['status'] == 'CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
                                </select>
                            </div>
                            
                            <!-- Buttons -->
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="ki-duotone ki-filter fs-2"></i> {{ __('accounting.apply_filters') }}
                                </button>
                                <a href="{{ route('accounting.transaction-ledger') }}" class="btn btn-secondary">
                                    {{ __('accounting.reset') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Summary Stats -->
                <div class="row g-5 g-xl-8 mb-8">
                    <!-- Total Transactions -->
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>{{ __('accounting.total_transactions') }}</h2>
                                </div>
                            </div>
                            <div class="card-body pt-1">
                                <div class="d-flex flex-column text-center my-7">
                                    <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1">{{ $transactions->total() }}</span>
                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">{{ __('accounting.transactions') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Total Amount -->
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>{{ __('accounting.total_amount') }}</h2>
                                </div>
                            </div>
                            <div class="card-body pt-1">
                                <div class="d-flex flex-column text-center my-7">
                                    @php
                                        $totalAmount = $transactions->sum('amount');
                                    @endphp
                                    <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1">${{ number_format($totalAmount, 2) }}</span>
                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">{{ __('accounting.total_value') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Average Transaction -->
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>{{ __('accounting.average_transaction') }}</h2>
                                </div>
                            </div>
                            <div class="card-body pt-1">
                                <div class="d-flex flex-column text-center my-7">
                                    @php
                                        $averageAmount = $transactions->count() > 0 ? $totalAmount / $transactions->count() : 0;
                                    @endphp
                                    <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1">${{ number_format($averageAmount, 2) }}</span>
                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">{{ __('accounting.per_transaction') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Date Range -->
                    <div class="col-xl-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-header">
                                <div class="card-title">
                                    <h2>{{ __('accounting.date_range') }}</h2>
                                </div>
                            </div>
                            <div class="card-body pt-1">
                                <div class="d-flex flex-column text-center my-7">
                                    <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1">
                                        {{ \Carbon\Carbon::parse($filters['start_date'])->format('M d') }} - {{ \Carbon\Carbon::parse($filters['end_date'])->format('M d') }}
                                    </span>
                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">{{ __('accounting.selected_period') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Transactions Table -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('accounting.transaction_details') }}</h3>
                        <div class="card-toolbar">
                            <div class="d-flex align-items-center position-relative">
                                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <input type="text" id="searchTransactions" class="form-control form-control-solid w-250px ps-10" placeholder="{{ __('accounting.search_transactions') }}">
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-dashed gy-4 align-middle gs-0" id="transactionsTable">
                                <thead>
                                    <tr class="fs-7 fw-bold text-gray-500 border-bottom-0">
                                        <th class="min-w-150px">{{ __('accounting.date') }}</th>
                                        <th class="min-w-200px">{{ __('accounting.transaction_ref') }}</th>
                                        <th class="min-w-150px">{{ __('accounting.payment_method') }}</th>
                                        <th class="min-w-100px">{{ __('accounting.type') }}</th>
                                        <th class="min-w-100px">{{ __('accounting.category') }}</th>
                                        <th class="min-w-150px">{{ __('accounting.description') }}</th>
                                        <th class="min-w-100px text-end">{{ __('accounting.amount') }}</th>
                                        <th class="min-w-100px text-end">{{ __('accounting.balance_before') }}</th>
                                        <th class="min-w-100px text-end">{{ __('accounting.balance_after') }}</th>
                                        <th class="min-w-100px">{{ __('accounting.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                    <tr>
                                        <td>
                                            <span class="fs-7 fw-bold text-gray-800">{{ $transaction->transaction_date->format('M d, Y') }}</span>
                                            <span class="fs-8 text-gray-500 d-block">{{ $transaction->transaction_date->format('H:i') }}</span>
                                        </td>
                                        <td>
                                            <span class="fs-7 text-gray-600">{{ $transaction->transaction_ref }}</span>
                                            @if($transaction->receipt_number)
                                                <span class="fs-8 text-gray-500 d-block">{{ $transaction->receipt_number }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fs-7 text-gray-800">{{ $transaction->paymentMethod->name ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-{{ $transaction->transaction_type === 'DEPOSIT' ? 'success' : ($transaction->transaction_type === 'WITHDRAWAL' ? 'danger' : 'info') }}">
                                                {{ $transaction->transaction_type }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fs-7 text-gray-600">{{ $transaction->transaction_category }}</span>
                                        </td>
                                        <td>
                                            <span class="fs-7 text-gray-800">{{ Str::limit($transaction->description, 40) }}</span>
                                            @if($transaction->notes)
                                                <span class="fs-8 text-gray-500 d-block">{{ Str::limit($transaction->notes, 30) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <span class="fs-6 fw-bold {{ in_array($transaction->transaction_type, ['DEPOSIT', 'TRANSFER_IN', 'REFUND']) ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->paymentMethod->currency->symbol ?? '$' }}{{ number_format($transaction->amount, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fs-7 text-gray-600">
                                                {{ $transaction->paymentMethod->currency->symbol ?? '$' }}{{ number_format($transaction->balance_before, 2) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fs-7 text-gray-800 fw-bold">
                                                {{ $transaction->paymentMethod->currency->symbol ?? '$' }}{{ number_format($transaction->balance_after, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-{{ $transaction->status === 'COMPLETED' ? 'success' : ($transaction->status === 'PENDING' ? 'warning' : 'danger') }}">
                                                {{ $transaction->status }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-5">
                            <div class="d-flex align-items-center">
                                <span class="text-gray-700 fs-7">
                                    {{ __('accounting.showing') }} {{ $transactions->firstItem() ?? 0 }} {{ __('accounting.to') }} {{ $transactions->lastItem() ?? 0 }} {{ __('accounting.of') }} {{ $transactions->total() }} {{ __('accounting.entries') }}
                                </span>
                            </div>
                            <div>
                                {{ $transactions->links() }}
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        function exportToExcel() {
            // Implement Excel export functionality
            alert('Export to Excel functionality would be implemented here');
        }
        
        // Search functionality
        document.getElementById('searchTransactions').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#transactionsTable tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
    @endpush
    
    @endsection
</x-app-layout>