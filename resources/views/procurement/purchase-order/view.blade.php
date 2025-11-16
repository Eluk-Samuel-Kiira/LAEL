<div class="modal fade" id="viewPurchase{{ $order->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h2 class="fw-bold">{{ __('passwords.purchase_order_details') }} - {{ $order->po_number }}</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body px-5 py-7">
                <!-- Basic Information -->
                <div class="row mb-8">
                    <div class="col-md-4 text-start">
                        <label class="fw-semibold text-gray-600">{{ __('passwords.supplier') }}:</label>
                        <div class="fw-bold fs-6 text-gray-800">{{ $order->supplier->name ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 text-start">
                        <label class="fw-semibold text-gray-600">{{ __('passwords.expected_delivery') }}:</label>
                        <div class="fw-bold fs-6 text-gray-800">{{ optional($order->expected_delivery_date)->format('M d, Y') ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 text-start">
                        <label class="fw-semibold text-gray-600">{{ __('pagination._location') }}:</label>
                        <div class="fw-bold fs-6 text-gray-800">{{ $order->location->name ?? '—' }}</div>
                    </div>
                </div>

                <div class="row mb-8">
                    <div class="col-md-4 text-start">
                        <label class="fw-semibold text-gray-600">{{ __('auth._status') }}:</label>
                        <div class="fw-bold fs-6 {{ $order->status_badge ? 'text-'.$order->status_badge : 'text-gray-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                        </div>
                    </div>
                    <div class="col-md-4 text-start">
                        <label class="fw-semibold text-gray-600">{{ __('auth._creater') }}:</label>
                        <div class="fw-bold fs-6 text-gray-800">{{ $order->creator->name ?? '—' }}</div>
                        <small class="text-muted">{{ optional($order->created_at)->format('M d, Y H:i') }}</small>
                    </div>
                    <div class="col-md-4 text-start">
                        <label class="fw-semibold text-gray-600">{{ __('auth.updated_at') }}:</label>
                        <div class="fw-bold fs-6 text-gray-800">{{ optional($order->updated_at)->format('M d, Y H:i') }}</div>
                    </div>
                </div>

                <!-- Status Action History -->
                <div class="row mb-8">
                    @if($order->submitted_at)
                    <div class="col-md-6 text-start">
                        <label class="fw-semibold text-gray-600">{{ __('passwords.submitted_for_approval') }}:</label>
                        <div class="fw-bold fs-6 text-gray-800">{{ $order->submittedBy->name ?? '—' }}</div>
                        <small class="text-muted">{{ optional($order->submitted_at)->format('M d, Y H:i') }}</small>
                    </div>
                    @endif

                    @if($order->approved_at)
                    <div class="col-md-6 text-start">
                        <label class="fw-semibold text-gray-600">{{ __('passwords.approved_by') }}:</label>
                        <div class="fw-bold fs-6 text-gray-800">{{ $order->approvedBy->name ?? '—' }}</div>
                        <small class="text-muted">{{ optional($order->approved_at)->format('M d, Y H:i') }}</small>
                    </div>
                    @endif

                    @if($order->sent_at)
                    <div class="col-md-6 text-start">
                        <label class="fw-semibold text-gray-600">{{ __('passwords.sent_to_supplier_by') }}:</label>
                        <div class="fw-bold fs-6 text-gray-800">{{ $order->sentBy->name ?? '—' }}</div>
                        <small class="text-muted">{{ optional($order->sent_at)->format('M d, Y H:i') }}</small>
                    </div>
                    @endif

                    @if($order->received_at)
                    <div class="col-md-6 text-start">
                        <label class="fw-semibold text-gray-600">{{ __('passwords.received_by') }}:</label>
                        <div class="fw-bold fs-6 text-gray-800">{{ $order->receivedBy->name ?? '—' }}</div>
                        <small class="text-muted">{{ optional($order->received_at)->format('M d, Y H:i') }}</small>
                    </div>
                    @endif

                    @if($order->cancelled_at)
                    <div class="col-md-6 text-start">
                        <label class="fw-semibold text-gray-600">{{ __('passwords.cancelled_by') }}:</label>
                        <div class="fw-bold fs-6 text-gray-800">{{ $order->cancelledBy->name ?? '—' }}</div>
                        <small class="text-muted">{{ optional($order->cancelled_at)->format('M d, Y H:i') }}</small>
                    </div>
                    @endif
                </div>

                <hr class="my-4">

                @php
                    $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
                @endphp

                @if(!empty($items) && count($items) > 0)
                    <ul class="list-group list-group-flush mb-5">
                        @foreach($items as $item)
                            <li class="list-group-item px-4 py-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    
                                    <!-- Left side: Product details -->
                                    <div class="me-4">
                                        <h6 class="fw-bold text-gray-800 mb-1">{{ $item['product_name'] ?? '—' }}</h6>
                                        <div class="text-muted fs-7 mb-1">SKU: {{ $item['sku'] ?? 'N/A' }}</div>
                                        <div class="text-muted fs-7">
                                            Quantity: <span class="fw-semibold">{{ $item['quantity'] ?? 0 }}</span> | 
                                            Received: <span class="badge bg-success">{{ $item['received_quantity'] ?? 0 }}</span> | 
                                            Pending: <span class="badge bg-danger">{{ (($item['quantity'] ?? 0) - ($item['received_quantity'] ?? 0)) }}</span>
                                        </div>
                                    </div>

                                    <!-- Right side: Costs -->
                                    <div class="text-end">
                                        <div class="fs-7"><strong>Unit Cost:</strong> {{ number_format($item['unit_cost'] ?? 0, 2) }} {{ currencySymbol() }}</div>
                                        <div class="fs-7"><strong>Tax:</strong> {{ number_format($item['tax_amount'] ?? 0, 2) }} {{ currencySymbol() }}</div>
                                        <div class="fw-bold text-primary fs-6 mt-1"><strong>Total:</strong> {{ number_format($item['total_cost'] ?? 0, 2) }} {{ currencySymbol() }}</div>
                                    </div>

                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="alert alert-secondary text-center">{{ __('passwords.no_items_found') }}</div>
                @endif

                <!-- Order Summary -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold">{{ __('pagination.order_summary') }}</h6>
                        <table class="table table-sm table-borderless w-auto">
                            <tr>
                                <td>{{ __('pagination.subtotal') }}:</td>
                                <td class="text-end">{{ displayFormatedCurrency($order->subtotal) }} {{ currencySymbol() }}</td>
                            </tr>
                            <tr>
                                <td>{{ __('passwords.tax_total') }}:</td>
                                <td class="text-end">{{ displayFormatedCurrency($order->tax_total) }} {{ currencySymbol() }}</td>
                            </tr>
                            <tr class="fw-bold text-primary">
                                <td>{{ __('passwords.grand_total') }}:</td>
                                <td class="text-end">{{ displayFormatedCurrency($order->total) }} {{ currencySymbol() }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold">{{ __('passwords.status_history') }}</h6>
                        <table class="table table-sm table-borderless w-auto">
                            <tr>
                                <td>{{ __('auth.created_at') }}:</td>
                                <td class="text-end">{{ optional($order->created_at)->format('M d, Y H:i') }}</td>
                            </tr>
                            @if($order->submitted_at)
                            <tr>
                                <td>{{ __('passwords.submitted_at') }}:</td>
                                <td class="text-end">{{ optional($order->submitted_at)->format('M d, Y H:i') }}</td>
                            </tr>
                            @endif
                            @if($order->approved_at)
                            <tr>
                                <td>{{ __('passwords.approved_at') }}:</td>
                                <td class="text-end">{{ optional($order->approved_at)->format('M d, Y H:i') }}</td>
                            </tr>
                            @endif
                            @if($order->sent_at)
                            <tr>
                                <td>{{ __('passwords.sent_at') }}:</td>
                                <td class="text-end">{{ optional($order->sent_at)->format('M d, Y H:i') }}</td>
                            </tr>
                            @endif
                            @if($order->received_at)
                            <tr>
                                <td>{{ __('passwords.received_at') }}:</td>
                                <td class="text-end">{{ optional($order->received_at)->format('M d, Y H:i') }}</td>
                            </tr>
                            @endif
                            @if($order->cancelled_at)
                            <tr>
                                <td>{{ __('passwords.cancelled_at') }}:</td>
                                <td class="text-end">{{ optional($order->cancelled_at)->format('M d, Y H:i') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                @if($order->notes)
                    <div class="mt-4 mb-0">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-sticky text-muted me-2 mt-1"></i>
                            <div>
                                <small class="text-muted d-block">{{ __('passwords._notes') }}</small>
                                <p class="mb-0 text-dark">{{ $order->notes }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="text-end mt-6">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('auth._close') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>