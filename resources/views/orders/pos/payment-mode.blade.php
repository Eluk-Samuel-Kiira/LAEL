<!-- Cash Payment Modal -->
<div class="modal fade" id="cashModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fw-bold text-gray-800">{{__('pagination.cash_payment')}}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-8">
                    <h4 class="fw-bold text-gray-600 mb-3">{{__('pagination.order_summary')}}</h4>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{__('pagination.total_amount')}}:</span>
                        <span id="cashTotalAmount" 
                              style="font-size: 1.8rem; font-weight: 700; color: #000;">
                              $0.00
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{__('pagination.amount_tendered')}}:</span>
                        <span id="cashAmountTendered" 
                              style="font-size: 1.8rem; font-weight: 700; color: #0d6efd;">
                              $0.00
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{__('pagination.change_due')}}:</span>
                        <span id="cashChangeDue" 
                              style="font-size: 2rem; font-weight: 800; color: #198754;">
                              $0.00
                        </span>
                    </div>
                </div>
                
                <div class="mb-8">
                    <label class="form-label fw-bold">{{__('pagination.enter_amount_tendered')}}</label>
                    <input type="number" class="form-control form-control-lg" id="cashAmountInput" 
                           placeholder="0.00" step="0.01" min="0" oninput="calculateChange()">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{__('pagination.cancel')}}</button>
                <button 
                    id="cashCheckout" 
                    type="button" 
                    class="btn btn-primary"
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

<script>
    // Function to open appropriate modal
    function openPaymentModal(paymentMethod, cartData) {
        // Set order data globally for access in modal functions
        window.currentOrder = cartData;
        // console.log(cartData);
        
        switch(paymentMethod) {
            case '1': // Cash
                openCashModal(cartData);
                break;
            case '2': // Mobile Money
                openMobileMoneyModal(cartData);
                break;
            case '3': // Card
                openCardModal(cartData);
                break;
            default:
                openReceiptModal(cartData);
        }
    }

    // Cash Modal Functions
    function openCashModal(cartData) {
        document.getElementById('cashTotalAmount').textContent = `$${cartData.total}`;
        document.getElementById('cashAmountInput').value = '';
        document.getElementById('cashAmountTendered').textContent = '$0.00';
        document.getElementById('cashChangeDue').textContent = '$0.00';
        
        const cashModal = new bootstrap.Modal(document.getElementById('cashModal'));
        cashModal.show();
    }

    function calculateChange() {
        const total = window.currentOrder.total;
        const tendered = parseFloat(document.getElementById('cashAmountInput').value) || 0;
        const change = tendered - total;
        
        document.getElementById('cashAmountTendered').textContent = `$${tendered}`;
        document.getElementById('cashChangeDue').textContent = `$${Math.max(0, change)}`;
    }

    function completeCashPayment() {
        const submitButton = document.getElementById('cashCheckout');
        const tendered = parseFloat(document.getElementById('cashAmountInput').value) || 0;
        
        if (tendered < window.currentOrder.total) {
            toastr['error']('{{ __("pagination.insufficient_amount") }}');
            return;
        }
        
        // Spinning
        LiveBlade.toggleButtonLoading(submitButton, true);
        // Process cash payment
        processFinalPayment('cash', {
            amount_tendered: tendered,
            change_due: tendered - window.currentOrder.total
        });

        LiveBlade.toggleButtonLoading(submitButton, false);
    }

    // Mobile Money Modal Functions
    function openMobileMoneyModal(cartData) {
        document.getElementById('mobileTotalAmount').textContent = `$${cartData.total}`;
        document.getElementById('mobilePhoneNumber').value = '';
        document.getElementById('mobileTransactionId').value = '';
        
        const mobileModal = new bootstrap.Modal(document.getElementById('mobileMoneyModal'));
        mobileModal.show();
    }

    function completeMobilePayment() {
        const phoneNumber = document.getElementById('mobilePhoneNumber').value;
        const transactionId = document.getElementById('mobileTransactionId').value;
        
        if (!phoneNumber || !transactionId) {
            showToast('error', '{{__("pagination.fill_all_fields")}}');
            return;
        }
        
        processFinalPayment('mobile_money', {
            phone_number: phoneNumber,
            transaction_id: transactionId
        });
    }

    // Card Modal Functions
    function openCardModal(cartData) {
        document.getElementById('cardTotalAmount').textContent = `$${cartData.total}`;
        document.getElementById('cardLastDigits').value = '';
        document.getElementById('cardAuthCode').value = '';
        
        const cardModal = new bootstrap.Modal(document.getElementById('cardModal'));
        cardModal.show();
    }

    function completeCardPayment() {
        const lastDigits = document.getElementById('cardLastDigits').value;
        const authCode = document.getElementById('cardAuthCode').value;
        
        if (!lastDigits || !authCode) {
            toastr['error']('{{ __("pagination.fill_all_fields") }}');
            return;
        }
        
        processFinalPayment('card', {
            last_digits: lastDigits,
            auth_code: authCode
        });
    }

    // Final Payment Processing
    function processFinalPayment(method, paymentDetails) {
        // Close current modal
        bootstrap.Modal.getInstance(document.getElementById('cashModal'))?.hide();
        bootstrap.Modal.getInstance(document.getElementById('mobileMoneyModal'))?.hide();
        bootstrap.Modal.getInstance(document.getElementById('cardModal'))?.hide();

        const order = window.currentOrder;
        order.payment_method = method;
        order.payment_details = {
            amount_tendered: paymentDetails.amount_tendered,
            change_due: paymentDetails.change_due
        };

        fetch("/orders/checkout", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(order)
        })
        .then(res => res.json().then(data => ({ status: res.status, ok: res.ok, body: data })))
        .then(response => {
            if (!response.ok) {
                // Laravel usually returns { message: "...", errors: {...} }
                throw new Error(response.body.message || '{{ __("pagination.order_saved_error") }}');
            }
            toastr['success']('{{ __("pagination.order_saved") }}');
            console.log("Order saved:", response.body);

            setTimeout(() => {
                generateReceipt(order.payment_method, order);
                openReceiptModal(window.currentOrder);

                toastr['success']('{{ __("pagination.payment_completed") }}');
            }, 2000);
        })
        .catch(err => {
            console.error("❌ Error saving order:", err);
            toastr['error'](err.message || '{{ __("pagination.order_saved_error") }}');
        });

        
    }

    // --- Generate Receipt ---
    function generateReceipt(paymentMethod, paymentDetails) {
        const receiptContent = document.getElementById('receiptContent');
        const order = window.currentOrder;

        // Determine customer display: use customerName if available
        let customerInfo = '{{ __("pagination.customer") }}: N/A';
        if (order.customerName) {
            customerInfo = `{{ __("pagination.customer") }}: ${order.customerName}`;
        } else if (order.customer) {
            customerInfo = `{{ __("pagination.customer") }}: ${order.customer.name}`;
        }

        // Generate items table
        const itemsHtml = `
            <table style="width:100%; border-collapse: collapse; margin-bottom: 10px;">
                <thead>
                    <tr>
                        <th style="border-bottom: 1px solid #ddd; text-align:left;">Item</th>
                        <th style="border-bottom: 1px solid #ddd; text-align:center;">Qty</th>
                        <th style="border-bottom: 1px solid #ddd; text-align:right;">Price</th>
                        <th style="border-bottom: 1px solid #ddd; text-align:right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    ${order.items.map(item => `
                        <tr>
                            <td>${item.name}</td>
                            <td style="text-align:center;">${item.quantity}</td>
                            <td style="text-align:right;">$${item.price}</td>
                            <td style="text-align:right;">$${(item.price * item.quantity).toFixed(2)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;

        receiptContent.innerHTML = `
            <div class="text-center mb-6">
                <h3 class="fw-bold text-gray-800">{{ getMailOptions('app_name') }}</h3>
                <p class="text-gray-600">Order #: ${order.order_number || 'N/A'}</p>
                <p class="text-gray-600">Date: ${new Date().toLocaleString()}</p>
                <p class="text-gray-600">${customerInfo}</p>
            </div>

            <div class="border-bottom mb-4 pb-4">
                <h4 class="fw-bold text-gray-800 mb-3">{{__('pagination.order_summary')}}</h4>
                ${itemsHtml}
                <div class="d-flex justify-content-between mb-2">
                    <span>{{__('pagination.subtotal')}}:</span>
                    <span>$${order.subtotal}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>{{__('pagination.tax')}}:</span>
                    <span>$${order.tax_total ?? order.tax}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>{{__('pagination.total')}}:</span>
                    <span class="fw-bold">$${order.total}</span>
                </div>
            </div>

            <div class="border-bottom mb-4 pb-4">
                <h4 class="fw-bold text-gray-800 mb-3">{{__('pagination.payment_details')}}</h4>
                <div class="d-flex justify-content-between mb-2">
                    <span>{{__('pagination.method')}}:</span>
                    <span>${paymentMethod.replace('_', ' ').toUpperCase()}</span>
                </div>
                ${paymentMethod === 'cash' ? `
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{__('pagination.amount_tendered')}}:</span>
                        <span>$${paymentDetails.payment_details.amount_tendered}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{__('pagination.change_due')}}:</span>
                        <span>$${paymentDetails.payment_details.change_due}</span>
                    </div>
                ` : ''}
            </div>

            <div class="text-center mt-6">
                <p class="text-gray-600">{{__('pagination.thank_you_message')}}</p>
            </div>
        `;
    }


    // --- Open Receipt Modal ---
    function openReceiptModal() {
        const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
        receiptModal.show();
    }

    // --- Print Receipt ---
    function printReceipt() {
        const receiptContent = document.getElementById('receiptContent').innerHTML;
        const printWindow = window.open('', '_blank');

        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Receipt - ${window.currentOrder.order_number || 'N/A'}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .text-center { text-align: center; }
                    .fw-bold { font-weight: bold; }
                    .border-bottom { border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 10px; }
                    .d-flex { display: flex; justify-content: space-between; }
                    .mb-2 { margin-bottom: 8px; }
                    .mb-4 { margin-bottom: 16px; }
                    .pb-4 { padding-bottom: 16px; }
                </style>
            </head>
            <body>
                ${receiptContent}
            </body>
            </html>
        `);

        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();

        // Close the modal after printing
        bootstrap.Modal.getInstance(document.getElementById('receiptModal'))?.hide();

    }

</script>