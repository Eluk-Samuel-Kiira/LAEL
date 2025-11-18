
<script>
    function formatCurrency(value) {
        return Number(value).toLocaleString('en-US', { 
            minimumFractionDigits: 2, 
            maximumFractionDigits: 2 
        });
    }


    let cart = [];

    function addToCart(variant) {
        if (variant.quantity_available <= 0) {
            toastr['error']('{{ __("pagination.out_of_stock") }}');
            return;
        }

        const idx = cart.findIndex(i => i.id === variant.id);
        if (idx > -1) {
            if (cart[idx].quantity < variant.quantity_available) {
                cart[idx].quantity += 1;
                updateCartItem(idx);
                toastr['success']('{{ __("pagination.item_added") }}');
            } else {
                toastr['warning']('{{ __("pagination.max_quantity_reached") }}');
            }
        } else {
            const cartItem = {
                id: variant.id,
                name: variant.name,
                price: parseFloat(variant.price),
                image: variant.image,
                quantity: 1,
                quantity_available: variant.quantity_available,
                taxes: Array.isArray(variant.taxes) ? variant.taxes : [],
                promotions: Array.isArray(variant.promotions) ? variant.promotions : []

            };
            cart.push(cartItem);
            renderCartItem(cartItem);
            toastr['success']('{{ __("pagination.item_added") }}');
        }

        updateCartTotal();
        calculateCartSummary();
    }

    function renderCartItem(item) {
        const cartTable = document.querySelector('#kt_pos_form tbody');
        const newRow = document.createElement('tr');
        newRow.setAttribute('data-kt-pos-element', '{{ __("pagination._item") }}');
        newRow.setAttribute('data-kt-pos-item-price', formatCurrency(item.price));
        newRow.setAttribute('data-item-id', item.id);

        const lineSubtotal = (item.price * item.quantity);
        newRow.innerHTML = `
            <td class="pe-0">
                <div class="d-flex align-items-center">
                    <img src="${item.image}" class="w-50px h-50px rounded-3 me-3" alt="${item.name}" />
                    <span class="fw-bold text-gray-800 cursor-pointer text-hover-primary fs-6 me-1">${item.name}</span>
                </div>
            </td>
            <td class="pe-0">
                <div class="position-relative d-flex align-items-center" data-kt-dialer="true" data-kt-dialer-min="1" data-kt-dialer-max="${item.quantity_available}" data-kt-dialer-step="1" data-kt-dialer-decimals="0">
                    <button type="button" class="btn btn-icon btn-sm btn-light btn-icon-gray-500" onclick="decreaseQuantity(${item.id})">
                        <i class="ki-duotone ki-minus fs-3x"></i>
                    </button>
                    <input type="text" class="form-control border-0 text-center px-0 fs-3 fw-bold text-gray-800 w-30px quantity-input"
                        placeholder="Amount" name="quantity_${item.id}"
                        value="${item.quantity}" onchange="updateQuantity(${item.id}, this.value)" />
                    <button type="button" class="btn btn-icon btn-sm btn-light btn-icon-gray-500" onclick="increaseQuantity(${item.id})">
                        <i class="ki-duotone ki-plus fs-3x"></i>
                    </button>
                </div>
            </td>
            <td class="text-end">
                <div class="d-flex align-items-center justify-content-end">
                    <div class="d-flex flex-column text-end">
                        <span class="fw-bold text-primary fs-2 item-total">${formatCurrency(lineSubtotal)}</span>
                        <small class="text-muted item-tax-line" style="display:none;"></small>
                        <small class="text-success item-discount-line" style="display:none;"></small>
                    </div>
                    <button type="button" class="btn btn-icon btn-sm btn-danger ms-2" onclick="removeFromCart(${item.id})">
                        <i class="bi bi-trash fs-2"></i>
                    </button>
                </div>
            </td>
        `;
        cartTable.appendChild(newRow);
        updateItemExtraLines(item.id);
    }

    function updateItemExtraLines(itemId) {
        const item = cart.find(i => i.id === itemId);
        const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
        if (!item || !row) return;

        const tax = computeItemTax(item);
        const discount = computeItemDiscount(item);

        const taxEl = row.querySelector('.item-tax-line');
        const discEl = row.querySelector('.item-discount-line');

        if (tax > 0) {
            taxEl.style.display = '';
            taxEl.textContent = `+ {{ __("pagination._tax") }} ${formatCurrency(tax)}`;
        } else {
            taxEl.style.display = 'none';
            taxEl.textContent = '';
        }

        if (discount > 0) {
            discEl.style.display = '';
            discEl.textContent = `- {{ __("pagination._disc") }} ${formatCurrency(discount)}`;
        } else {
            discEl.style.display = 'none';
            discEl.textContent = '';
        }

        // refresh subtotal display (base only)
        const itemTotalEl = row.querySelector('.item-total');
        const lineSubtotal = item.price * item.quantity;
        itemTotalEl.textContent = `${formatCurrency(lineSubtotal)}`;

        calculateCartSummary();
    }

    function computeItemTax(item) {
        const base = item.price * item.quantity;
        let taxTotal = 0;
        if (!item.taxes || !item.taxes.length) return 0;

        item.taxes.forEach(t => {
            const rate = parseFloat(t.rate || 0);
            if (t.type === 'percentage') {
                taxTotal += base * rate;
            } else {
                taxTotal += rate * item.quantity;
            }
        });
        return taxTotal;
    }

    function computeItemDiscount(item) {
        let discountTotal = 0;
        const subtotal = item.price * item.quantity;

        (item.promotions || []).forEach(promo => {
            if (promo.type === 'percentage') {
                discountTotal += subtotal * (promo.value / 100);
            } else if (promo.type === 'fixed_amount') {
                discountTotal += promo.value * item.quantity;
            } else if (promo.type === 'buy_x_get_y') {
                // Implement your custom logic if needed
                discountTotal += 0; 
            }
        });

        // console.log(discountTotal);

        return discountTotal;
    }

    function updateCartItem(itemIndex) {
        const item = cart[itemIndex];
        const row = document.querySelector(`tr[data-item-id="${item.id}"]`);
        if (!row) return;

        const quantityInput = row.querySelector('.quantity-input');
        quantityInput.value = item.quantity;

        updateItemExtraLines(item.id);
    }

    function increaseQuantity(itemId) {
        const idx = cart.findIndex(i => i.id === itemId);
        if (idx > -1) {
            if (cart[idx].quantity < cart[idx].quantity_available) {
                cart[idx].quantity += 1;
                updateCartItem(idx);
                updateCartTotal();
            } else {
                toastr['warning']('{{ __("pagination.max_quantity_reached") }}');
            }
        }
    }

    function decreaseQuantity(itemId) {
        const idx = cart.findIndex(i => i.id === itemId);
        if (idx > -1 && cart[idx].quantity > 1) {
            cart[idx].quantity -= 1;
            updateCartItem(idx);
            updateCartTotal();
        }
    }

    function updateQuantity(itemId, newQuantity) {
        const qty = parseInt(newQuantity);
        const idx = cart.findIndex(i => i.id === itemId);
        if (idx > -1 && !isNaN(qty)) {
            if (qty > 0 && qty <= cart[idx].quantity_available) {
                cart[idx].quantity = qty;
                updateCartItem(idx);
                updateCartTotal();
            } else if (qty > cart[idx].quantity_available) {
                toastr['warning']('{{ __("pagination.max_quantity_reached") }}');
                cart[idx].quantity = cart[idx].quantity_available;
                updateCartItem(idx);
                updateCartTotal();
            }
        }
    }

    function removeFromCart(itemId) {
        const idx = cart.findIndex(i => i.id === itemId);
        if (idx > -1) {
            cart.splice(idx, 1);
            const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
            if (row) row.remove();
            updateCartTotal();
            toastr['success']('{{ __("pagination.item_removed") }}');
        }
    }

    function clearCart() {
        cart = [];
        const cartTable = document.querySelector('#kt_pos_form tbody');
        cartTable.innerHTML = '';
        updateCartTotal();
        toastr['success']('{{ __("pagination.cart_cleared") }}');
    }

    // === Totals ===
    function calculateCartSummary() {
        const subtotal = cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
        const tax = cart.reduce((sum, i) => sum + computeItemTax(i), 0);
        const discount = cart.reduce((sum, i) => sum + computeItemDiscount(i), 0);
        const grandTotal = subtotal - discount + tax;

        document.querySelector('[data-kt-pos-element="total"]').textContent = `${formatCurrency(subtotal)}`;
        document.querySelector('[data-kt-pos-element="discount"]').textContent = `-${formatCurrency(discount)}`;
        document.querySelector('[data-kt-pos-element="tax"]').textContent = `${formatCurrency(tax)}`;
        document.querySelector('[data-kt-pos-element="grant-total"]').textContent = `${formatCurrency(grandTotal)}`;
    }

    function updateCartTotal() {
        calculateCartSummary();
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateCartTotal();
        const clearBtn = document.querySelector('[onclick="clearCart()"]');
        if (clearBtn) {
            clearBtn.addEventListener('click', function (e) {
                e.preventDefault();
                clearCart();
            });
        }
    });
</script>



<script>
    function processPayment() {
        const submitButton = document.getElementById('processBill');

        // Check if cart is empty
        if (cart.length === 0) {
            toastr['warning']('{{ __("pagination.cart_empty") }}');
            console.log('Cart is empty - cannot process payment');
            return;
        }

        // --- Check Customer Selection ---
        const existingCustomer = document.getElementById('existingCustomer'); // dropdown
        const newCustomer = document.getElementById('newCustomer'); // input
        const existingOption = document.getElementById('existingOption'); // radio
        const newOption = document.getElementById('newOption'); // radio

        let customerData = null;

        if (existingOption.checked) {
            // Existing customer chosen
            if (existingCustomer.value === "") {
                toastr['warning']('{{ __("pagination.please_select_existing_customer") }}');
                return;
            }
            customerData = {
                type: "existing",
                id: existingCustomer.value
            };
        } else if (newOption.checked) {
            // New customer chosen
            if (newCustomer.value.trim() === "") {
                toastr['warning']('{{ __("pagination.please_enter_customer") }}');
                return;
            }
            customerData = {
                type: "new",
                name: newCustomer.value.trim()
            };
        }

        // Get selected payment method
        const selectedMethod = document.querySelector('input[name="method"]:checked');
        const paymentMethod = selectedMethod ? selectedMethod.value : '1';
        // console.log(paymentMethod);
        if (paymentMethod == 3 || paymentMethod == 2) {
            toastr['warning']('{{ __("pagination.Payment_method_coming_soon") }}');
            return;
        }

        const cartData = {
            items: cart.map(item => {
                const itemSubtotal = item.price * item.quantity;

                // Taxes per item
                const itemTaxes = (item.taxes || []).map(tax => {
                    let taxAmount = 0;
                    const rate = parseFloat(tax.rate || 0);

                    if (tax.type === 'percentage') {
                        taxAmount = itemSubtotal * rate;
                    } else {
                        taxAmount = rate * item.quantity;
                    }

                    return {
                        id: tax.id,
                        name: tax.name,
                        type: tax.type,
                        rate: tax.rate,
                        amount: taxAmount
                    };
                });

                const itemTaxTotal = itemTaxes.reduce((sum, t) => sum + t.amount, 0);

                // Discounts & promotions
                let discountTotal = 0;
                const appliedPromotions = [];

                (item.promotions || []).forEach(promo => {
                    let promoDiscount = 0;
                    if (promo.type === 'percentage') {
                        promoDiscount = itemSubtotal * (promo.value / 100);
                    } else if (promo.type === 'fixed_amount') {
                        // fixed per item = (price - value) * qty  (if value is a price override)
                        promoDiscount = (item.price - promo.value) * item.quantity;
                    } else if (promo.type === 'buy_x_get_y') {
                        // Example: buy 2 get 1 free
                        promoDiscount = 0;
                    }

                    if (promoDiscount > 0) {
                        discountTotal += promoDiscount;
                        appliedPromotions.push({
                            id: promo.id,
                            name: promo.name,
                            type: promo.type,
                            value: promo.value,
                            discount: promoDiscount
                        });
                    }
                });

                return {
                    variant_id: item.id,
                    quantity: item.quantity,
                    price: item.price,
                    name: item.name,
                    subtotal: itemSubtotal,
                    taxes: itemTaxes,
                    tax_total: itemTaxTotal,
                    discount: discountTotal,
                    promotions: appliedPromotions, // 👈 attach applied promotions here
                    total: itemSubtotal - discountTotal + itemTaxTotal
                };
            }),

            customer: customerData,
            payment_method: paymentMethod,

            subtotal: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0),
            discount: cart.reduce((sum, item) => sum + computeItemDiscount(item), 0),
            tax: cart.reduce((sum, item) => sum + computeItemTax(item), 0),
            total: 0
        };

        cartData.total = cartData.subtotal - cartData.discount + cartData.tax;
        // console.log(cartData);


        
        // Start loading
        LiveBlade.toggleButtonLoading(submitButton, true);

        
        const formData = new FormData();
        formData.append('cart_data', JSON.stringify(cartData));
        
        fetch('{{ route("orders.process-payment") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            // Check if response is OK
            if (!response.ok) {
                toastr['danger']('{{ __("pagination.network_error") }}');
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Stop loading
            LiveBlade.toggleButtonLoading(submitButton, false);
            
            if (data.success) {
                toastr['success'](data.message);

                const cartDataRaw = formData.get("cart_data");  // JSON string
                const cartData = JSON.parse(cartDataRaw);       // Convert to object
                cartData.order_number = data.order_number;     
                cartData.customerName = data.customerName;    
                cartData.order_id = data.order_id; 
                
                // Open modal based on payment method
                openPaymentModal(paymentMethod, cartData);
                
            } else {
                // Show error toast from backend response
                const message = data.message || '{{ __("pagination.order_failed") }}';
                toastr['success'](message);
            }
        })
        .catch(error => {
            // Stop loading
            LiveBlade.toggleButtonLoading(submitButton, false);
            console.error('Payment error:', error);
            toastr['error']('{{ __("pagination.payment_error") }}');
        });

        
    }

</script>


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
        document.getElementById('cashTotalAmount').textContent = `${formatCurrency(cartData.total)}`;
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
        
        document.getElementById('cashAmountTendered').textContent = `${formatCurrency(tendered)}`;
        document.getElementById('cashChangeDue').textContent = `${formatCurrency(Math.max(0, change))}`;
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

    }

    // Mobile Money Modal Functions
    function openMobileMoneyModal(cartData) {
        document.getElementById('mobileTotalAmount').textContent = `${formatCurrency(cartData.total)}`;
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
        document.getElementById('cardTotalAmount').textContent = `${formatCurrency(cartData.total)}`;
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
        const submitButton = document.getElementById('cashCheckout');

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
                throw new Error(response.body.message || '{{ __("pagination.order_saved_error") }}');
            }
            toastr['success']('{{ __("pagination.order_saved") }}');
            // console.log("Order saved:", response.body);

            // Close current modal
            bootstrap.Modal.getInstance(document.getElementById('cashModal'))?.hide();
            // bootstrap.Modal.getInstance(document.getElementById('mobileMoneyModal'))?.hide();
            // bootstrap.Modal.getInstance(document.getElementById('cardModal'))?.hide();

            setTimeout(() => {
                generateReceipt(order.payment_method, order);
                openReceiptModal(window.currentOrder);

                toastr['success']('{{ __("pagination.payment_completed") }}');
            }, 2000);
            
            // Clear cart after successful payment
            clearCart();
            LiveBlade.toggleButtonLoading(submitButton, false);
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
        // console.log(order)

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
                        <th style="border-bottom: 1px solid #ddd; text-align:left;">{{ __("pagination._item") }}</th>
                        <th style="border-bottom: 1px solid #ddd; text-align:center;">{{ __("pagination._qty") }}</th>
                        <th style="border-bottom: 1px solid #ddd; text-align:right;">{{ __("pagination._price") }}</th>
                        <th style="border-bottom: 1px solid #ddd; text-align:right;">{{ __("pagination._discount") }}</th>
                        <th style="border-bottom: 1px solid #ddd; text-align:right;">{{ __("pagination._total") }}</th>
                    </tr>
                </thead>
                <tbody>
                    ${order.items.map(item => `
                        <tr>
                            <td>${item.name}</td>
                            <td style="text-align:center;">${item.quantity}</td>
                            <td style="text-align:right;">${formatCurrency(item.price)}</td>
                            <td style="text-align:right;">${formatCurrency(item.discount)}</td>
                            <td style="text-align:right;">${formatCurrency(item.price * item.quantity)}</td>
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
                    <span>${formatCurrency(order.subtotal)}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>{{__('pagination.tax')}}:</span>
                    <span>${formatCurrency(order.tax)}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>{{__('pagination._discount')}}:</span>
                    <span>${formatCurrency(order.discount)}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>{{__('pagination.total')}}:</span>
                    <span class="fw-bold">${formatCurrency(order.total)}</span>
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
                        <span>${formatCurrency(paymentDetails.payment_details.amount_tendered)}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{__('pagination.change_due')}}:</span>
                        <span>${formatCurrency(paymentDetails.payment_details.change_due)}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{__('pagination._currency')}}:</span>
                        <span>{{ getMailOptions('currency') }} - {{ currencySymbol() }}</span>
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
        window.location.href = "{{ url()->current() }}";
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


    // Prevent all closing methods
    document.addEventListener('DOMContentLoaded', function() {
        const receiptModal = document.getElementById('receiptModal');
        
        // Prevent backdrop click
        receiptModal.addEventListener('click', function(event) {
            if (event.target === receiptModal) {
                event.stopPropagation();
                event.preventDefault();
                return false;
            }
        });
        
        // Prevent ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && receiptModal.classList.contains('show')) {
                event.preventDefault();
                event.stopPropagation();
                return false;
            }
        });
        
        // Prevent Bootstrap hide events
        receiptModal.addEventListener('hide.bs.modal', function(event) {
            event.preventDefault();
            return false;
        });
        
        // Prevent any other hide attempts
        receiptModal.addEventListener('hidden.bs.modal', function(event) {
            event.preventDefault();
            return false;
        });
    });

</script>




