<!-- Cash Payment Modal -->
<div class="modal fade" id="cashModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content" style="font-size: 1.4rem;">
            <div class="modal-header">
                <h2 class="modal-title fw-bold text-gray-800" style="font-size: 2rem;">
                    {{__('pagination.cash_payment')}}
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        style="transform: scale(1.5);"></button>
            </div>
            <div class="modal-body">
                <div class="mb-8">
                    <h4 class="fw-bold text-gray-600 mb-4" style="font-size: 1.6rem;">
                        {{__('pagination.order_summary')}}
                    </h4>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span style="font-size: 1.5rem;">{{__('pagination.total_amount')}}:</span>
                        <span id="cashTotalAmount" 
                              style="font-size: 2.2rem; font-weight: 700; color: #000;">
                              $0.00
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span style="font-size: 1.5rem;">{{__('pagination.amount_tendered')}}:</span>
                        <span id="cashAmountTendered" 
                              style="font-size: 2.2rem; font-weight: 700; color: #0d6efd;">
                              $0.00
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <span style="font-size: 1.5rem;">{{__('pagination.change_due')}}:</span>
                        <span id="cashChangeDue" 
                              style="font-size: 2.5rem; font-weight: 800; color: #198754;">
                              $0.00
                        </span>
                    </div>
                </div>
                
                <div class="mb-8">
                    <label class="form-label fw-bold" style="font-size: 1.5rem;">
                        {{ __('pagination.enter_amount_tendered') }}
                    </label>
                    <input type="number" 
                        class="form-control text-center" 
                        id="cashAmountInput"
                        placeholder="0.00" 
                        step="0.01" 
                        min="0" 
                        oninput="calculateChange(this)"
                        style="font-size: 2.5rem; height: 90px; line-height: 90px;">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" 
                        class="btn btn-light btn-lg px-5 py-3" 
                        data-bs-dismiss="modal" 
                        style="font-size: 1.4rem;">
                    {{__('pagination.cancel')}}
                </button>
                <button 
                    id="cashCheckout" 
                    type="button" 
                    class="btn btn-primary btn-lg px-5 py-3"
                    style="font-size: 1.4rem;"
                    onclick="completeCashPayment()">
                    
                    <span class="indicator-label">{{__('pagination.complete_payment')}}</span>
                    <span class="indicator-progress">{{__('pagination.complete_checkout')}}
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>



<!-- Mobile Money Modal -->
<div class="modal fade" id="mobileMoneyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fw-bold text-gray-800">{{__('pagination.mobile_money_payment')}}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-6">
                    <h4 class="fw-bold text-gray-600 mb-3">{{__('pagination.cartData')}}: 
                        <span class="text-primary" id="mobileTotalAmount">$0.00</span>
                    </h4>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">{{__('pagination.phone_number')}}</label>
                    <input type="tel" class="form-control" id="mobilePhoneNumber" 
                           placeholder="+256 XXX XXX XXX">
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">{{__('pagination.transaction_id')}}</label>
                    <input type="text" class="form-control" id="mobileTransactionId" 
                           placeholder="Enter transaction ID">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('pagination.cancel')}}</button>
                <button type="button" class="btn btn-primary" onclick="completeMobilePayment()">
                    {{__('pagination.confirm_payment')}}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Card Payment Modal -->
<div class="modal fade" id="cardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fw-bold text-gray-800">{{__('pagination.card_payment')}}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-6">
                    <h4 class="fw-bold text-gray-600 mb-3">{{__('pagination.cartData')}}: 
                        <span class="text-primary" id="cardTotalAmount">$0.00</span>
                    </h4>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">{{__('pagination.last_4_digits')}}</label>
                    <input type="text" class="form-control" id="cardLastDigits" 
                           placeholder="1234" maxlength="4">
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">{{__('pagination.auth_code')}}</label>
                    <input type="text" class="form-control" id="cardAuthCode" 
                           placeholder="Enter authorization code">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('pagination.cancel')}}</button>
                <button type="button" class="btn btn-primary" onclick="completeCardPayment()">
                    {{__('pagination.confirm_payment')}}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fw-bold text-gray-800">{{__('pagination.payment_receipt')}}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="receiptContent">
                <!-- Receipt content will be generated here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('pagination.close')}}</button>
                <button type="button" class="btn btn-primary" onclick="printReceipt()">
                    <i class="ki-duotone ki-printer fs-2 me-2"></i>
                    {{__('pagination.print_receipt')}}
                </button>
            </div>
        </div>
    </div>
</div>
