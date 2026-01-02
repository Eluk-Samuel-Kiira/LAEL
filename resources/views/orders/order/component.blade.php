<div class="card-body py-4" id="ordersIndexTable">
    <div class="table-responsive">
        <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
            <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                    <th class="w-10px pe-2">
                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                            <input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                        </div>
                    </th>
                    <th class="min-w-125px">{{__('passwords.order_number')}}</th>
                    <th class="min-w-125px">{{__('passwords._customer')}}</th>
                    <th class="min-w-125px">{{__('passwords._type')}}</th>
                    <th class="min-w-125px">{{__('passwords._status')}}</th>
                    <th class="min-w-125px">{{__('passwords._source')}}</th>
                    <th class="min-w-125px">{{__('passwords._amount')}}</th>
                    <th class="min-w-125px">{{__('payments.payment_method')}}</th>
                    <th class="min-w-125px">{{__('auth._creater')}}</th>
                    <th class="text-end min-w-100px">{{__('auth._actions')}}</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold">
                @if (!empty($orders) && $orders->count() > 0)
                    @foreach ($orders as $order)
                        <tr data-role="{{ strtolower($order->id) }}" class="order-row">
                            <td>
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="1" />
                                </div>
                            </td>
                            <td>
                                <a href="javascript:void(0);" 
                                   class="toggle-items d-flex align-items-center text-gray-800 text-hover-primary fw-bold" 
                                   data-bs-toggle="collapse" 
                                   data-bs-target="#orderItems{{ $order->id }}"
                                   aria-expanded="false"
                                   aria-controls="orderItems{{ $order->id }}">
                                    <span class="toggle-icon svg-icon svg-icon-3 me-3 rotate-180">
                                        <i class="ki-duotone ki-down fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td>
                                <span class="text-gray-800 fw-bold">{{ $order->customer_name ?? __('passwords.none') }}</span>
                            </td>
                            <td>
                                <span class="badge badge-light-primary fw-bold fs-8 px-3 py-2">{{ ucwords($order->type) }}</span>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'confirmed' => 'primary',
                                        'processing' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        'refunded' => 'dark'
                                    ];
                                    $statusColor = $statusColors[strtolower($order->status)] ?? 'secondary';
                                @endphp
                                <span class="badge badge-light-{{ $statusColor }} fw-bold fs-8 px-3 py-2">
                                    <span class="bullet bullet-{{ $statusColor }} bullet-sm me-2"></span>
                                    {{ ucwords($order->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="text-gray-800 fw-bold">{{ ucwords($order->source) }}</span>
                            </td>
                            <td>
                                <span class="text-gray-800 fw-bold">{{ currencySymbol() }} {{ $order->total }}</span>
                            </td>
                            <td>
                                @if($order->payments && $order->payments->paymentMethod)
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 fw-bold fs-7 mb-1">{{ $order->payments->paymentMethod->name }}</span>
                                        @if($order->payments->paymentMethod->account_number)
                                            <small class="text-muted fs-8">{{ $order->payments->paymentMethod->account_number }}</small>
                                        @endif
                                        @if($order->payments->paymentMethod->type)
                                            <small class="badge badge-light-info fs-8 mt-1">{{ $order->payments->paymentMethod->type }}</small>
                                        @endif
                                    </div>
                                @elseif($order->payments)
                                    <span class="text-muted fs-8">No payment method</span>
                                @else
                                    <span class="text-muted fs-8">No payment</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-35px symbol-circle me-3">
                                        <div class="symbol-label bg-light-primary">
                                            <span class="fs-7 fw-bold text-primary">{{ substr($order->orderCreater->name ?? 'U', 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="text-gray-800 fw-bold fs-7">{{ $order->orderCreater->name ?? __('passwords.none') }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    @if ($order->status == 'confirmed' && $order->source == 'pos')
                                        <button 
                                            class="btn btn-sm btn-light-primary btn-active-light-primary d-flex align-items-center px-4 py-2" 
                                            data-bs-toggle="modal"
                                            data-bs-target="#completePayment{{$order->id}}">
                                            <i class="ki-duotone ki-dollar fs-4 me-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            <span class="fw-bold">{{__('pagination._complete')}}</span>
                                        </button>
                                    @endif
                                    <button 
                                        class="btn btn-sm btn-icon btn-light btn-active-light-primary"
                                        onclick="printOrder({{ $order->id }})"
                                        data-bs-toggle="tooltip"
                                        data-bs-title="{{ __('passwords._print') }}">
                                        <i class="ki-duotone ki-printer fs-3">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Expanded row -->
                        <tr class="collapse" id="orderItems{{ $order->id }}" data-bs-parent="#ordersIndexTable">
                            <td colspan="10" class="p-0 border-0">
                                <div class="order-details-container bg-light">
                                    <div class="order-details-content p-6 border border-dashed border-gray-300 border-top-0">
                                        
                                        <!-- Payment Information (if exists) -->
                                        @if($order->payments)
                                        <div class="card card-flush card-dashed mb-6">
                                            <div class="card-header border-0">
                                                <div class="card-title">
                                                    <h3 class="fw-bold text-gray-800">
                                                        <i class="ki-duotone ki-credit-card fs-2 me-2 text-primary">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                        {{ __('payments.payment_details') }}
                                                    </h3>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0">
                                                <div class="row g-5">
                                                    <div class="col-md-6">
                                                        <div class="d-flex flex-column gap-4">
                                                            <div>
                                                                <div class="text-muted fs-7 fw-semibold mb-1">{{ __('payments.payment_method') }}</div>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="symbol symbol-40px symbol-circle me-3">
                                                                        <div class="symbol-label bg-light-success">
                                                                            <i class="ki-duotone ki-credit-card fs-2 text-success">
                                                                                <span class="path1"></span>
                                                                                <span class="path2"></span>
                                                                                <span class="path3"></span>
                                                                            </i>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div class="fw-bold text-gray-800 fs-6">{{ $order->payments->paymentMethod->name ?? 'Not specified' }}</div>
                                                                        @if($order->payments->paymentMethod->account_number ?? false)
                                                                            <div class="text-muted fs-7">{{ $order->payments->paymentMethod->account_number }}</div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="text-muted fs-7 fw-semibold mb-1">{{ __('payments.transaction_id') }}</div>
                                                                <div class="fw-bold text-gray-800 fs-6">{{ $order->payments->transaction_id ?? 'N/A' }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="d-flex flex-column gap-4">
                                                            <div>
                                                                <div class="text-muted fs-7 fw-semibold mb-1">{{ __('payments.amount') }}</div>
                                                                <div class="fw-bold text-primary fs-2">{{ currencySymbol() }} {{ $order->payments->amount }}</div>
                                                            </div>
                                                            <div class="row g-3">
                                                                <div class="col-6">
                                                                    <div class="text-muted fs-7 fw-semibold mb-1">{{ __('auth._status') }}</div>
                                                                    <span class="badge badge-light-{{ $order->payments->status_color }} fw-bold px-3 py-2">
                                                                        <span class="bullet bullet-{{ $order->payments->status_color }} bullet-sm me-2"></span>
                                                                        {{ ucfirst($order->payments->status) }}
                                                                    </span>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="text-muted fs-7 fw-semibold mb-1">{{ __('auth.processed_at') }}</div>
                                                                    <div class="fw-bold text-gray-800 fs-7">{{ $order->payments->formatted_processed_at }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Items table -->
                                        <div class="card card-flush card-dashed mb-6">
                                            <div class="card-header border-0">
                                                <div class="card-title">
                                                    <h3 class="fw-bold text-gray-800">
                                                        <i class="ki-duotone ki-basket fs-2 me-2 text-primary">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        {{ __('passwords.order_items') }}
                                                    </h3>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0">
                                                <div class="table-responsive">
                                                    <table class="table table-row-dashed align-middle fs-6 gy-5">
                                                        <thead>
                                                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                                                <th class="ps-0">{{ __('passwords.item_name') }}</th>
                                                                <th>{{ __('passwords.sku') }}</th>
                                                                <th class="text-center">{{ __('passwords.quantity') }}</th>
                                                                <th class="text-end">{{ __('passwords.unit_price') }}</th>
                                                                <th class="text-end">{{ __('passwords.discount') }}</th>
                                                                <th class="text-end">{{ __('passwords.tax') }}</th>
                                                                <th class="text-end pe-0">{{ __('passwords.total') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="fw-semibold text-gray-600">
                                                            @foreach ($order->orderItems as $item)
                                                                <tr>
                                                                    <td class="ps-0">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="symbol symbol-45px me-3">
                                                                                <div class="symbol-label bg-light">
                                                                                    <i class="ki-duotone ki-abstract-41 fs-2 text-gray-600">
                                                                                        <span class="path1"></span>
                                                                                        <span class="path2"></span>
                                                                                    </i>
                                                                                </div>
                                                                            </div>
                                                                            <div>
                                                                                <div class="fw-bold text-gray-800">{{ $item->item_name }}</div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <span class="badge badge-light fw-bold">{{ $item->sku }}</span>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <span class="fw-bold">{{ $item->quantity }}</span>
                                                                    </td>
                                                                    <td class="text-end">{{ displayFormatedCurrency($item->unit_price) }} {{ currencySymbol() }}</td>
                                                                    <td class="text-end text-danger">-{{ displayFormatedCurrency($item->discount) }} {{ currencySymbol() }}</td>
                                                                    <td class="text-end">{{ displayFormatedCurrency($item->tax_amount) }} {{ currencySymbol() }}</td>
                                                                    <td class="text-end pe-0 fw-bold text-primary">{{ displayFormatedCurrency($item->total_price) }} {{ currencySymbol() }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Financial summary -->
                                        <div class="card card-flush card-dashed">
                                            <div class="card-header border-0">
                                                <div class="card-title">
                                                    <h3 class="fw-bold text-gray-800">
                                                        <i class="ki-duotone ki-chart-line fs-2 me-2 text-primary">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        {{ __('passwords.summary') }}
                                                    </h3>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0">
                                                <div class="row">
                                                    <div class="col-md-6 col-lg-4 offset-md-6 offset-lg-8">
                                                        <div class="bg-light rounded p-4">
                                                            <div class="d-flex flex-column gap-3">
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="text-muted fs-7">{{ __('passwords.subtotal') }}</span>
                                                                    <span class="fw-bold fs-6">{{ displayFormatedCurrency($order->subtotal) }} {{ currencySymbol() }}</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="text-muted fs-7">{{ __('passwords.discount') }}</span>
                                                                    <span class="fw-bold fs-6 text-danger">-{{ displayFormatedCurrency($order->discount_total) }} {{ currencySymbol() }}</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="text-muted fs-7">{{ __('passwords.tax') }}</span>
                                                                    <span class="fw-bold fs-6">{{ displayFormatedCurrency($order->tax_total) }} {{ currencySymbol() }}</span>
                                                                </div>
                                                                <div class="separator separator-dashed my-3"></div>
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="fw-bold text-gray-800 fs-5">{{ __('passwords.total') }}</span>
                                                                    <span class="fw-bold text-primary fs-3">{{ displayFormatedCurrency($order->total) }} {{ currencySymbol() }}</span>
                                                                </div>
                                                                @if($order->payments)
                                                                <div class="separator separator-dashed my-3"></div>
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="text-muted fs-7">{{ __('passwords.paid') }}</span>
                                                                    <span class="fw-bold fs-6 text-success">{{ displayFormatedCurrency($order->paid_amount) }} {{ currencySymbol() }}</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <span class="text-muted fs-7">{{ __('passwords.balance_due') }}</span>
                                                                    <span class="fw-bold fs-6 text-danger">{{ displayFormatedCurrency($order->balance_due) }} {{ currencySymbol() }}</span>
                                                                </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Complete Payment Modal -->
                        <div class="modal fade" id="completePayment{{$order->id}}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{__('pagination.cash_payment')}}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>{{ __('pagination.Payment_method_coming_soon') }}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" id="closeDeleteModal{{$order->id}}" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('auth._discard') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

{{-- Information to print --}}
@foreach ($orders as $order)
    <div id="printableOrder{{ $order->id }}" style="display:none;">
        <div style="width: 100%; max-width: 800px; margin: 0 auto; font-family: 'Inter', Arial, sans-serif; color: #181c32;">
            <!-- Header -->
            <div style="text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #009ef7;">
                <h1 style="color: #009ef7; margin-bottom: 10px;">{{ __('passwords.order_invoice') }}</h1>
                <h3 style="color: #3f4254; font-weight: 600;">Order #: {{ $order->order_number }}</h3>
            </div>
            
            <!-- Order Info -->
            <div style="display: flex; justify-content: space-between; margin-bottom: 30px;">
                <div style="flex: 1;">
                    <div style="margin-bottom: 10px;">
                        <strong style="color: #5e6278;">{{ __('passwords._customer') }}:</strong>
                        <div style="font-weight: bold; color: #181c32;">{{ $order->customer_name ?? __('passwords.none') }}</div>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <strong style="color: #5e6278;">{{ __('passwords.location') }}:</strong>
                        <div style="font-weight: bold; color: #181c32;">{{ $order->location->name ?? __('passwords.none') }}</div>
                    </div>
                    <div>
                        <strong style="color: #5e6278;">{{ __('passwords.department') }}:</strong>
                        <div style="font-weight: bold; color: #181c32;">{{ $order->department->name ?? __('passwords.none') }}</div>
                    </div>
                </div>
                <div style="flex: 1; text-align: right;">
                    <div style="margin-bottom: 10px;">
                        <strong style="color: #5e6278;">{{ __('auth._creater') }}:</strong>
                        <div style="font-weight: bold; color: #181c32;">{{ $order->orderCreater->name ?? __('passwords.none') }}</div>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <strong style="color: #5e6278;">{{ __('auth._status') }}:</strong>
                        <div style="font-weight: bold; color: #009ef7; text-transform: capitalize;">{{ $order->status }}</div>
                    </div>
                    <div>
                        <strong style="color: #5e6278;">{{ __('passwords._source') }}:</strong>
                        <div style="font-weight: bold; color: #181c32; text-transform: capitalize;">{{ $order->source }}</div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            @if($order->payments)
            <div style="background-color: #f8f9fa; border: 1px solid #e4e6ef; border-radius: 8px; padding: 20px; margin-bottom: 30px;">
                <h3 style="color: #009ef7; margin-bottom: 15px; border-bottom: 1px dashed #e4e6ef; padding-bottom: 10px;">
                    {{ __('payments.payment_details') }}
                </h3>
                <div style="display: flex; flex-wrap: wrap; gap: 30px;">
                    <div style="flex: 1; min-width: 250px;">
                        <div style="margin-bottom: 10px;">
                            <strong style="color: #5e6278;">{{ __('payments.payment_method') }}:</strong>
                            <div style="font-weight: bold; color: #181c32;">{{ $order->payments->paymentMethod->name ?? 'Not specified' }}</div>
                            @if($order->payments->paymentMethod->account_number ?? false)
                                <div style="color: #7e8299; font-size: 14px;">{{ $order->payments->paymentMethod->account_number }}</div>
                            @endif
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong style="color: #5e6278;">{{ __('payments.amount') }}:</strong>
                            <div style="font-weight: bold; color: #009ef7; font-size: 18px;">{{ currencySymbol() }} {{ $order->payments->amount }}</div>
                        </div>
                    </div>
                    <div style="flex: 1; min-width: 250px;">
                        <div style="margin-bottom: 10px;">
                            <strong style="color: #5e6278;">{{ __('payments.transaction_id') }}:</strong>
                            <div style="font-weight: bold; color: #181c32;">{{ $order->payments->transaction_id ?? 'N/A' }}</div>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <strong style="color: #5e6278;">{{ __('auth._status') }}:</strong>
                            <div style="font-weight: bold; color: #50cd89; text-transform: capitalize;">{{ $order->payments->status }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Items Table -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #009ef7; margin-bottom: 15px; border-bottom: 1px dashed #e4e6ef; padding-bottom: 10px;">
                    {{ __('passwords.order_items') }}
                </h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f8f9fa;">
                            <th style="border: 1px solid #e4e6ef; padding: 12px; text-align: left; color: #5e6278;">{{ __('passwords.item_name') }}</th>
                            <th style="border: 1px solid #e4e6ef; padding: 12px; text-align: left; color: #5e6278;">{{ __('passwords.sku') }}</th>
                            <th style="border: 1px solid #e4e6ef; padding: 12px; text-align: center; color: #5e6278;">{{ __('passwords.quantity') }}</th>
                            <th style="border: 1px solid #e4e6ef; padding: 12px; text-align: right; color: #5e6278;">{{ __('passwords.unit_price') }}</th>
                            <th style="border: 1px solid #e4e6ef; padding: 12px; text-align: right; color: #5e6278;">{{ __('passwords.discount') }}</th>
                            <th style="border: 1px solid #e4e6ef; padding: 12px; text-align: right; color: #5e6278;">{{ __('passwords.tax') }}</th>
                            <th style="border: 1px solid #e4e6ef; padding: 12px; text-align: right; color: #5e6278;">{{ __('passwords.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderItems as $item)
                            <tr>
                                <td style="border: 1px solid #e4e6ef; padding: 12px; color: #181c32;">{{ $item->item_name }}</td>
                                <td style="border: 1px solid #e4e6ef; padding: 12px; color: #181c32;">{{ $item->sku }}</td>
                                <td style="border: 1px solid #e4e6ef; padding: 12px; text-align: center; color: #181c32; font-weight: bold;">{{ $item->quantity }}</td>
                                <td style="border: 1px solid #e4e6ef; padding: 12px; text-align: right; color: #181c32;">{{ displayFormatedCurrency($item->unit_price) }} {{ currencySymbol() }}</td>
                                <td style="border: 1px solid #e4e6ef; padding: 12px; text-align: right; color: #f1416c;">-{{ displayFormatedCurrency($item->discount) }} {{ currencySymbol() }}</td>
                                <td style="border: 1px solid #e4e6ef; padding: 12px; text-align: right; color: #181c32;">{{ displayFormatedCurrency($item->tax_amount) }} {{ currencySymbol() }}</td>
                                <td style="border: 1px solid #e4e6ef; padding: 12px; text-align: right; color: #009ef7; font-weight: bold;">{{ displayFormatedCurrency($item->total_price) }} {{ currencySymbol() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary -->
            <div style="text-align: right;">
                <div style="display: inline-block; width: 300px;">
                    <h3 style="color: #009ef7; margin-bottom: 15px; text-align: left;">{{ __('passwords.summary') }}</h3>
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td style="padding: 8px 0; color: #5e6278;">{{ __('passwords.subtotal') }}:</td>
                                <td style="padding: 8px 0; text-align: right; font-weight: bold;">{{ displayFormatedCurrency($order->subtotal) }} {{ currencySymbol() }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 0; color: #5e6278;">{{ __('passwords.discount') }}:</td>
                                <td style="padding: 8px 0; text-align: right; color: #f1416c; font-weight: bold;">-{{ displayFormatedCurrency($order->discount_total) }} {{ currencySymbol() }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 0; color: #5e6278;">{{ __('passwords.tax') }}:</td>
                                <td style="padding: 8px 0; text-align: right; font-weight: bold;">{{ displayFormatedCurrency($order->tax_total) }} {{ currencySymbol() }}</td>
                            </tr>
                            <tr style="border-top: 2px dashed #e4e6ef;">
                                <td style="padding: 12px 0; font-weight: bold; color: #181c32;">{{ __('passwords.total') }}:</td>
                                <td style="padding: 12px 0; text-align: right; font-weight: bold; color: #009ef7; font-size: 20px;">{{ displayFormatedCurrency($order->total) }} {{ currencySymbol() }}</td>
                            </tr>
                            @if($order->payments)
                            <tr style="border-top: 1px dashed #e4e6ef;">
                                <td style="padding: 8px 0; color: #5e6278;">{{ __('passwords.paid') }}:</td>
                                <td style="padding: 8px 0; text-align: right; color: #50cd89; font-weight: bold;">{{ displayFormatedCurrency($order->paid_amount) }} {{ currencySymbol() }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px 0; color: #5e6278;">{{ __('passwords.balance_due') }}:</td>
                                <td style="padding: 8px 0; text-align: right; color: #f1416c; font-weight: bold;">{{ displayFormatedCurrency($order->balance_due) }} {{ currencySymbol() }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #e4e6ef; text-align: center; color: #7e8299; font-size: 14px;">
                <div>Printed on: {{ now()->format('M d, Y H:i') }}</div>
                <div style="margin-top: 5px;">Thank you for your business!</div>
            </div>
        </div>
    </div>
@endforeach

<style>
/* ============================================
   METRONIC UI CONSISTENT STYLING
   ============================================ */

/* Global Variables */
:root {
    --kt-primary: #009ef7;
    --kt-primary-light: #f1faff;
    --kt-success: #50cd89;
    --kt-success-light: #e8fff3;
    --kt-info: #00b2ff;
    --kt-info-light: #e8f7ff;
    --kt-warning: #ffc700;
    --kt-warning-light: #fff8dd;
    --kt-danger: #f1416c;
    --kt-danger-light: #fff5f8;
    --kt-dark: #181c32;
    --kt-light: #f8f9fa;
    --kt-gray-100: #f5f8fa;
    --kt-gray-200: #eff2f5;
    --kt-gray-300: #e4e6ef;
    --kt-gray-400: #b5b5c3;
    --kt-gray-500: #a1a5b7;
    --kt-gray-600: #7e8299;
    --kt-gray-700: #5e6278;
    --kt-gray-800: #3f4254;
    --kt-gray-900: #181c32;
}

/* Order Row Styling */
.order-row {
    transition: all 0.2s ease;
    border-bottom: 1px solid var(--kt-gray-200);
}

.order-row:hover {
    background-color: var(--kt-gray-100);
}

/* Toggle Button */
.toggle-items {
    text-decoration: none;
    transition: all 0.2s ease;
}

.toggle-items:hover {
    color: var(--kt-primary) !important;
}

.toggle-icon {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.toggle-items[aria-expanded="true"] .toggle-icon {
    transform: rotate(90deg);
}

.toggle-items[aria-expanded="true"] .toggle-icon i {
    color: var(--kt-primary);
}

/* Expanded Row Container */
.order-details-container {
    background-color: var(--kt-gray-100);
}

.order-details-content {
    animation: slideDown 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Card Styling */
.card-flush {
    border: 0;
    background-color: transparent;
}

.card-dashed {
    border: 1px dashed var(--kt-gray-300) !important;
    border-radius: 0.65rem;
}

.card-dashed .card-header {
    border-bottom: 1px dashed var(--kt-gray-300);
    background-color: transparent;
}

/* Badge Styling - Consistent with Metronic */
.badge {
    border-radius: 0.425rem;
    padding: 0.35rem 0.75rem;
    font-weight: 600;
    line-height: 1.5;
}

.badge-light-primary {
    background-color: var(--kt-primary-light);
    color: var(--kt-primary);
    border: 1px solid rgba(0, 158, 247, 0.2);
}

.badge-light-success {
    background-color: var(--kt-success-light);
    color: var(--kt-success);
    border: 1px solid rgba(80, 205, 137, 0.2);
}

.badge-light-info {
    background-color: var(--kt-info-light);
    color: var(--kt-info);
    border: 1px solid rgba(0, 178, 255, 0.2);
}

.badge-light-warning {
    background-color: var(--kt-warning-light);
    color: var(--kt-warning);
    border: 1px solid rgba(255, 199, 0, 0.2);
}

.badge-light-danger {
    background-color: var(--kt-danger-light);
    color: var(--kt-danger);
    border: 1px solid rgba(241, 65, 108, 0.2);
}

.badge-light-dark {
    background-color: #f5f8fa;
    color: var(--kt-dark);
    border: 1px solid rgba(24, 28, 50, 0.1);
}

/* Bullet Styling */
.bullet {
    display: inline-block;
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 50%;
}

.bullet-primary { background-color: var(--kt-primary); }
.bullet-success { background-color: var(--kt-success); }
.bullet-info { background-color: var(--kt-info); }
.bullet-warning { background-color: var(--kt-warning); }
.bullet-danger { background-color: var(--kt-danger); }
.bullet-dark { background-color: var(--kt-dark); }

/* Symbol Styling */
.symbol {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.symbol-circle .symbol-label {
    border-radius: 50%;
}

.symbol-label {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Button Styling */
.btn-light-primary {
    background-color: var(--kt-primary-light);
    border-color: var(--kt-primary-light);
    color: var(--kt-primary);
}

.btn-light-primary:hover,
.btn-active-light-primary:hover {
    background-color: var(--kt-primary);
    border-color: var(--kt-primary);
    color: #fff;
}

.btn-light {
    border-color: var(--kt-gray-300);
    background-color: #fff;
    color: var(--kt-gray-700);
}

.btn-light:hover {
    border-color: var(--kt-primary);
    background-color: var(--kt-primary-light);
    color: var(--kt-primary);
}

/* Table Enhancements */
#kt_table_users th {
    background-color: var(--kt-gray-100);
    position: sticky;
    top: 0;
    z-index: 10;
}

#kt_table_users tbody tr:last-child {
    border-bottom: 1px solid var(--kt-gray-200);
}

/* Separator */
.separator {
    display: block;
    height: 0;
    overflow: visible;
    border-top: 1px dashed var(--kt-gray-300);
}

.separator-dashed {
    border-top-style: dashed;
}

/* Border Colors */
.border-gray-300 {
    border-color: var(--kt-gray-300) !important;
}

.border-dashed {
    border-style: dashed !important;
}

/* Text Colors */
.text-gray-800 {
    color: var(--kt-gray-800) !important;
}

.text-gray-600 {
    color: var(--kt-gray-600) !important;
}

.text-muted {
    color: var(--kt-gray-500) !important;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .order-details-content {
        padding: 1rem !important;
    }
    
    .table-responsive {
        margin: 0 -0.75rem;
        padding: 0 0.75rem;
    }
    
    .btn span:not(.svg-icon) {
        display: none;
    }
    
    .btn {
        padding: 0.375rem 0.75rem;
    }
}

/* Hover Effects */
.text-hover-primary:hover {
    color: var(--kt-primary) !important;
}

/* Print Styles */
@media print {
    .order-details-container,
    .toggle-items,
    .btn,
    .badge-light-primary {
        display: none !important;
    }
    
    .card-dashed {
        border: 1px solid #ddd !important;
    }
}

/* Animation Classes */
.rotate-180 {
    transition: transform 0.3s ease;
}

.collapsed .rotate-180 {
    transform: rotate(0deg);
}

:not(.collapsed) .rotate-180 {
    transform: rotate(180deg);
}

/* Custom Scrollbar for Table */
.table-responsive::-webkit-scrollbar {
    height: 6px;
}

.table-responsive::-webkit-scrollbar-track {
    background: var(--kt-gray-100);
    border-radius: 3px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: var(--kt-gray-300);
    border-radius: 3px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: var(--kt-gray-400);
}
</style>

<script>
function printOrder(orderId) {
    console.log('Printing order:', orderId); // Debug log
    
    const printElement = document.getElementById('printableOrder' + orderId);
    
    if (!printElement) {
        console.error('Print element not found for order ID:', orderId);
        alert('Print content not found for this order.');
        return;
    }
    
    const printContent = printElement.innerHTML;
    const printWindow = window.open('', '_blank', 'width=900,height=700,scrollbars=yes');
    
    if (!printWindow) {
        alert('Please allow popups to print this order.');
        return;
    }
    
    // Create a clean print document with proper styling
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Order Invoice - ${orderId}</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: 'Inter', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                    line-height: 1.5;
                    color: #181c32;
                    background: white;
                    padding: 20px;
                    font-size: 14px;
                }
                
                .invoice-container {
                    max-width: 800px;
                    margin: 0 auto;
                    background: white;
                }
                
                .invoice-header {
                    text-align: center;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 3px solid #009ef7;
                }
                
                .invoice-header h1 {
                    color: #009ef7;
                    font-size: 28px;
                    margin-bottom: 5px;
                }
                
                .invoice-header h3 {
                    color: #3f4254;
                    font-size: 18px;
                    font-weight: 600;
                }
                
                .invoice-info {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 30px;
                    flex-wrap: wrap;
                    gap: 20px;
                }
                
                .info-section {
                    flex: 1;
                    min-width: 250px;
                }
                
                .info-label {
                    color: #5e6278;
                    font-weight: 600;
                    margin-bottom: 4px;
                    font-size: 13px;
                }
                
                .info-value {
                    color: #181c32;
                    font-weight: bold;
                    margin-bottom: 12px;
                }
                
                .payment-info {
                    background-color: #f8f9fa;
                    border: 1px solid #e4e6ef;
                    border-radius: 8px;
                    padding: 20px;
                    margin-bottom: 30px;
                }
                
                .payment-title {
                    color: #009ef7;
                    margin-bottom: 15px;
                    padding-bottom: 10px;
                    border-bottom: 1px dashed #e4e6ef;
                    font-size: 18px;
                }
                
                .payment-details {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 30px;
                }
                
                .payment-detail {
                    flex: 1;
                    min-width: 250px;
                }
                
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 30px;
                }
                
                th {
                    background-color: #f8f9fa;
                    border: 1px solid #e4e6ef;
                    padding: 12px;
                    text-align: left;
                    color: #5e6278;
                    font-weight: 600;
                    font-size: 12px;
                    text-transform: uppercase;
                }
                
                td {
                    border: 1px solid #e4e6ef;
                    padding: 12px;
                    color: #181c32;
                }
                
                .text-center {
                    text-align: center;
                }
                
                .text-right {
                    text-align: right;
                }
                
                .text-danger {
                    color: #f1416c !important;
                }
                
                .text-success {
                    color: #50cd89 !important;
                }
                
                .text-primary {
                    color: #009ef7 !important;
                }
                
                .summary {
                    text-align: right;
                    margin-bottom: 40px;
                }
                
                .summary-table {
                    display: inline-block;
                    width: 300px;
                    text-align: left;
                }
                
                .summary-title {
                    color: #009ef7;
                    margin-bottom: 15px;
                    text-align: left;
                    font-size: 18px;
                }
                
                .summary-row {
                    padding: 8px 0;
                }
                
                .summary-label {
                    color: #5e6278;
                }
                
                .summary-value {
                    text-align: right;
                    font-weight: bold;
                }
                
                .total-row {
                    border-top: 2px dashed #e4e6ef;
                    padding: 12px 0;
                    font-weight: bold;
                }
                
                .total-amount {
                    font-size: 20px;
                }
                
                .invoice-footer {
                    margin-top: 40px;
                    padding-top: 20px;
                    border-top: 2px solid #e4e6ef;
                    text-align: center;
                    color: #7e8299;
                    font-size: 13px;
                }
                
                @media print {
                    body {
                        padding: 15px;
                        font-size: 12px;
                    }
                    
                    .invoice-header h1 {
                        font-size: 24px;
                    }
                    
                    th, td {
                        padding: 8px;
                    }
                    
                    .no-print {
                        display: none !important;
                    }
                    
                    .print-btn {
                        display: none !important;
                    }
                }
                
                @page {
                    size: A4;
                    margin: 15mm;
                }
            </style>
        </head>
        <body>
            <div class="invoice-container">
                ${printContent}
                
                <div class="no-print" style="text-align: center; margin-top: 30px;">
                    <button class="print-btn" onclick="window.print()" style="
                        background: #009ef7;
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 6px;
                        font-weight: bold;
                        cursor: pointer;
                        margin-right: 10px;
                    ">Print Invoice</button>
                    <button class="print-btn" onclick="window.close()" style="
                        background: #f1416c;
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 6px;
                        font-weight: bold;
                        cursor: pointer;
                    ">Close Window</button>
                </div>
            </div>
            
            <script>
                // Auto-print after a short delay
                window.onload = function() {
                    // Focus the window
                    window.focus();
                    
                    // Auto-print option (uncomment if you want auto-print)
                    // setTimeout(() => {
                    //     window.print();
                    // }, 500);
                };
                
                // Handle beforeprint event to add page breaks if needed
                window.onbeforeprint = function() {
                    // Add any pre-print adjustments here
                };
                
                // Handle afterprint event
                window.onafterprint = function() {
                    // Optional: Close window after printing
                    // setTimeout(() => {
                    //     window.close();
                    // }, 1000);
                };
            <\/script>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    
    // Wait for content to load then focus
    printWindow.onload = function() {
        printWindow.focus();
    };
}

// Initialize tooltips and toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Handle toggle animation
    const toggleButtons = document.querySelectorAll('.toggle-items');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const target = document.querySelector(this.getAttribute('data-bs-target'));
            if (target) {
                this.classList.toggle('collapsed');
            }
        });
    });
});
</script>