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
                    <th class="min-w-125px">{{__('auth._creater')}}</th>
                    <th class="text-end min-w-100px">{{__('auth._actions')}}</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold">
                @if (!empty($orders) && $orders->count() > 0)
                    @foreach ($orders as $order)
                        <tr data-role="{{ strtolower($order->id) }}">
                            <td>
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="1" />
                                </div>
                            </td>
                            <td>
                                <a href="javascript:void(0);" 
                                class="toggle-items fw-bold text-primary" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#orderItems{{ $order->id }}">
                                {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="fw-bold text-warning ms-3">{{ $order->customer_name ?? __('passwords.none') }}</td>
                            <td><div class="badge badge-light fw-bold">{{ ucwords($order->type) }}</div></td>
                            <td><div class="badge badge-light fw-bold">{{ ucwords($order->status) }}</div></td>
                            <td class="fw-bold text-primary ms-3">{{ ucwords($order->source) }}</td>
                            <td class="fw-bold text-primary ms-3">{{ displayFormatedCurrency($order->total) }} {{ currencySymbol() }}</td>
                            <td><div class="badge badge-light fw-bold">{{ $order->orderCreater->name ?? __('passwords.none') }}</div></td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if ($order->status == 'confirmed' && $order->source == 'pos')
                                        <button 
                                            class="btn btn-sm btn-light btn-active-color-primary d-flex align-items-center px-3 py-2" 
                                            data-bs-toggle="modal"
                                            data-bs-target="#completePayment{{$order->id}}">
                                            <i class="bi bi-cash me-1 fs-5"></i> <span>{{__('pagination._complete')}}</span>
                                        </button>
                                    @endif
                                    <button 
                                        class="btn btn-sm btn-light btn-active-color-primary d-flex align-items-center px-3 py-2"
                                        onclick="printOrder({{ $order->id }})">
                                        <i class="bi bi-printer me-1 fs-5"></i> 
                                        <span>{{ __('passwords._print') }}</span>
                                    </button>

                                    
                                <!-- Delete User Modal -->
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
                                                <!-- Discard Button -->
                                                <button type="button" id="closeDeleteModal{{$order->id}}" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('auth._discard') }}</button>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Expanded row -->
                        <tr class="collapse bg-light" id="orderItems{{ $order->id }}">
                            <td colspan="9">
                                <div class="p-4 border rounded shadow-sm">

                                    <!-- Items table -->
                                    <div class="table-responsive mb-3">
                                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                                            <thead>
                                                <tr class="fw-bold text-muted">
                                                    <th>{{ __('passwords.item_name') }}</th>
                                                    <th>{{ __('passwords.sku') }}</th>
                                                    <th>{{ __('passwords.quantity') }}</th>
                                                    <th>{{ __('passwords.unit_price') }}</th>
                                                    <th>{{ __('passwords.discount') }}</th>
                                                    <th>{{ __('passwords.tax') }}</th>
                                                    <th>{{ __('passwords.total') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order->orderItems as $item)
                                                    <tr>
                                                        <td>{{ $item->item_name }}</td>
                                                        <td>{{ $item->sku }}</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>{{ displayFormatedCurrency($item->unit_price) }} {{ currencySymbol() }}</td>
                                                        <td>{{ displayFormatedCurrency($item->discount) }} {{ currencySymbol() }}</td>
                                                        <td>{{ displayFormatedCurrency($item->tax_amount) }} {{ currencySymbol() }}</td>
                                                        <td>{{ displayFormatedCurrency($item->total_price) }} {{ currencySymbol() }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Financial summary -->
                                    <div class="mt-4">
                                        <h6 class="fw-bold">{{ __('passwords.summary') }}</h6>
                                        <table class="table table-sm table-borderless w-auto">
                                            <tbody>
                                                <tr>
                                                    <td>{{ __('passwords.subtotal') }}:</td>
                                                    <td class="text-end">{{ displayFormatedCurrency($order->subtotal) }} {{ currencySymbol() }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('passwords.discount') }}:</td>
                                                    <td class="text-end text-danger">-{{ displayFormatedCurrency($order->discount_total) }} {{ currencySymbol() }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('passwords.tax') }}:</td>
                                                    <td class="text-end">{{ displayFormatedCurrency($order->tax_total) }} {{ currencySymbol() }}</td>
                                                </tr>
                                                <tr class="fw-bold border-top">
                                                    <td>{{ __('passwords.total') }}:</td>
                                                    <td class="text-end text-primary">{{ displayFormatedCurrency($order->total) }} {{ currencySymbol() }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('passwords.paid') }}:</td>
                                                    <td class="text-end text-success">{{ displayFormatedCurrency($order->paid_amount) }} {{ currencySymbol() }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('passwords.balance_due') }}:</td>
                                                    <td class="text-end text-danger">{{ displayFormatedCurrency($order->balance_due) }} {{ currencySymbol() }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

{{-- Information to print --}}
@foreach ($orders as $order)
    <div id="printableOrder{{ $order->id }}" style="display:none;">
        <div style="width: 800px; margin: auto; font-family: Arial, sans-serif;">
            <h2 style="text-align:center;">{{ __('passwords.order_invoice') }}</h2>
            <p><strong>{{ __('passwords.order_number') }}:</strong> {{ $order->order_number }}</p>
            <p>
                <strong>{{ __('passwords._customer') }}:</strong> {{ $order->customer_name ?? __('passwords.none') }} <br>
                <strong>{{ __('passwords.location') }}:</strong> {{ $order->location->name ?? __('passwords.none') }} <br>
                <strong>{{ __('passwords.department') }}:</strong> {{ $order->department->name ?? __('passwords.none') }}
            </p>

            <table width="100%" border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr>
                        <th>{{ __('passwords.item_name') }}</th>
                        <th>{{ __('passwords.sku') }}</th>
                        <th>{{ __('passwords.quantity') }}</th>
                        <th>{{ __('passwords.unit_price') }}</th>
                        <th>{{ __('passwords.discount') }}</th>
                        <th>{{ __('passwords.tax') }}</th>
                        <th>{{ __('passwords.total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderItems as $item)
                        <tr>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->sku }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ displayFormatedCurrency($item->unit_price) }} {{ currencySymbol() }}</td>
                            <td>{{ displayFormatedCurrency($item->discount) }} {{ currencySymbol() }}</td>
                            <td>{{ displayFormatedCurrency($item->tax_amount) }} {{ currencySymbol() }}</td>
                            <td>{{ displayFormatedCurrency($item->total_price) }} {{ currencySymbol() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top: 20px; width: 50%; float: right;">
                <table width="100%">
                    <tbody>
                        <tr>
                            <td>{{ __('passwords.subtotal') }}:</td>
                            <td style="text-align:right;">{{ displayFormatedCurrency($order->subtotal) }} {{ currencySymbol() }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('passwords.discount') }}:</td>
                            <td style="text-align:right;">-{{ displayFormatedCurrency($order->discount_total) }} {{ currencySymbol() }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('passwords.tax') }}:</td>
                            <td style="text-align:right;">{{ displayFormatedCurrency($order->tax_total) }} {{ currencySymbol() }}</td>
                        </tr>
                        <tr class="fw-bold">
                            <td>{{ __('passwords.total') }}:</td>
                            <td style="text-align:right;">{{ displayFormatedCurrency($order->total) }} {{ currencySymbol() }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('passwords.paid') }}:</td>
                            <td style="text-align:right;">{{ displayFormatedCurrency($order->paid_amount) }} {{ currencySymbol() }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('passwords.balance_due') }}:</td>
                            <td style="text-align:right;">{{ displayFormatedCurrency($order->balance_due) }} {{ currencySymbol() }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="clear: both;"></div>
        </div>
    </div>
@endforeach





