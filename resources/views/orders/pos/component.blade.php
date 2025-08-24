
<div class="d-flex flex-column flex-xl-row">
    <!--begin::Content-->
    <div class="d-flex flex-row-fluid me-xl-9 mb-10 mb-xl-0">
        <!--begin::Pos food-->
        <div class="card card-flush card-p-0 bg-transparent border-0">
            <!--begin::Body-->
            <div class="card-body">
                <!--begin::Nav-->
                <!-- Remove d-flex justify-content-between and add flex-nowrap -->

                <ul class="nav nav-pills nav-pills-custom gap-3 mb-6 d-flex flex-nowrap" 
                    id="productList"
                    style="overflow-x: auto; white-space: nowrap; list-style: none; padding: 0;">
                    @php $first = true; @endphp
                    @foreach ($products as $product)
                        @if ($product->variants->count() > 0)
                            @php
                                $words = explode(' ', $product->name);
                                $truncated = count($words) > 2 
                                    ? implode(' ', array_slice($words, 0, 2)) . '...' 
                                    : $product->name;
                                
                                // Get department IDs as comma-separated string
                                $departmentIds = $product->departments->pluck('id')->implode(',');
                            @endphp
                            <li class="nav-item mb-3 me-0 product-item" 
                                style="min-width: 180px;"
                                data-department="{{ $departmentIds }}"> <!-- Added data attribute -->
                                <a class="nav-link nav-link-border-solid btn btn-outline btn-flex btn-active-color-primary flex-column flex-stack pt-9 pb-7 page-bg {{ $first ? 'active' : '' }}" 
                                data-bs-toggle="pill" 
                                href="#kt_pos_{{ $product->id }}" 
                                style="width: 100%; height: 180px">
                                    <div class="nav-icon mb-3">
                                        <img src="{{ productImage($product->image_url) }}" class="w-50px" alt="" />
                                    </div>
                                    <div>
                                        <span class="text-gray-800 fw-bold fs-2 d-block" 
                                            data-bs-toggle="tooltip" 
                                            title="{{ $product->name }}">
                                            {{ $truncated }}
                                        </span>
                                        <span class="text-gray-500 fw-semibold fs-7">
                                            {{ $product->variants->count() }} {{ __('pagination._options') }}
                                        </span>
                                    </div>
                                </a>
                            </li>
                            @php $first = false; @endphp
                        @endif
                    @endforeach

                    @if ($products->filter(fn($p) => $p->variants->count() > 0)->isEmpty())
                        <div class="card-header pt-5">
                            <h3 class="card-title fw-bold text-gray-800 fs-2qx">{{ __('pagination.not_assigned') }}</h3>
                        </div>
                    @endif
                </ul>
                <!--end::Nav-->

                <!--begin::Tab Content-->
                <div class="tab-content" id="variantTabContent">
                    @php $firstTab = true; @endphp
                    @foreach ($products as $product)
                        @if ($product->variants->count() > 0)
                            <div class="tab-pane fade {{ $firstTab ? 'show active' : '' }}" id="kt_pos_{{ $product->id }}">
                                <div class="d-flex flex-wrap d-grid gap-5 gap-xxl-9 variant-container">
                                    @foreach ($product->variants as $variant)
                                        <div class="card card-flush flex-row-fluid p-6 pb-5 mw-100 variant-item" 
                                            data-name="{{ strtolower($variant->name ?? $product->name) }}"
                                            data-product="{{ $product->id }}"
                                            onclick="addToCart({{ json_encode([
                                                'id' => $variant->id,
                                                'name' => $variant->name ?? $product->name,
                                                'price' => $variant->price,
                                                'image' => productVariantImage($variant->image_url ?? $product->image_url),
                                                'quantity_available' => $variant->inventory->quantity_on_hand ?? 0
                                            ]) }})"
                                            style="cursor: pointer;">
                                            <div class="card-body text-center">
                                                <img src="{{ productVariantImage($variant->image_url ?? $product->image_url) }}" 
                                                    class="rounded-3 mb-4 w-150px h-150px w-xxl-200px h-xxl-200px" 
                                                    alt="{{ $variant->name ?? $product->name }}" />
                                                <div class="mb-2">
                                                    <div class="text-center">
                                                        <span class="fw-bold text-gray-800 cursor-pointer text-hover-primary fs-3 fs-xl-1"
                                                            data-bs-toggle="tooltip" 
                                                            title="{{ $variant->name ?? $product->name }}">
                                                            {{ \Illuminate\Support\Str::words($variant->name ?? $product->name, 2, '...') }}
                                                        </span>
                                                        <span class="text-gray-500 fw-semibold d-block fs-6 mt-n1">
                                                            {{ $variant->inventory->quantity_on_hand ?? 0 }} {{__('pagination._available')}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <span class="text-success text-end fw-bold fs-1">
                                                    {{ number_format($variant->price, 2) }}$
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @php $firstTab = false; @endphp
                        @endif
                    @endforeach
                </div>
                <!--end::Tab Content-->

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const departmentFilter = document.getElementById('departmentFilter');
                        const productItems = document.querySelectorAll('.product-item');
                        const productList = document.getElementById('productList');
                        
                        departmentFilter.addEventListener('change', function() {
                            const selectedDepartment = this.value;
                            let hasVisibleProducts = false;
                            
                            productItems.forEach(function(item) {
                                const itemDepartments = item.getAttribute('data-department').split(',');
                                
                                if (selectedDepartment === '' || itemDepartments.includes(selectedDepartment)) {
                                    item.style.display = ''; // Show the item
                                    hasVisibleProducts = true;
                                } else {
                                    item.style.display = 'none'; // Hide the item
                                }
                            });
                            
                            // Remove any existing "no products" message
                            const existingMessage = productList.querySelector('.no-products-message');
                            if (existingMessage) {
                                existingMessage.remove();
                            }
                            
                            // If no products match and a department is selected, show message
                            if (!hasVisibleProducts && selectedDepartment !== '') {
                                const messageDiv = document.createElement('div');
                                messageDiv.className = 'card-header pt-5 no-products-message';
                                messageDiv.innerHTML = '<h3 class="card-title fw-bold text-gray-800 fs-2qx">{{ __("pagination.no_products_in_department") }}</h3>';
                                productList.appendChild(messageDiv);
                            }
                        });
                    });
                </script>

                <script>
                    function filterVariants(searchTerm) {
                        const searchValue = searchTerm.toLowerCase().trim();
                        const allVariantItems = document.querySelectorAll('.variant-item');
                        const activeTab = document.querySelector('.tab-pane.active');
                        
                        if (searchValue === '') {
                            // Show all variants when search is empty
                            allVariantItems.forEach(item => {
                                item.style.display = '';
                            });
                            return;
                        }

                        // Filter variants based on search term
                        allVariantItems.forEach(item => {
                            const variantName = item.getAttribute('data-name');
                            const isInActiveTab = activeTab && activeTab.contains(item);
                            
                            if (variantName.includes(searchValue)) {
                                item.style.display = '';
                                // If not in active tab, ensure it's visible when user switches to that tab
                            } else {
                                item.style.display = 'none';
                            }
                        });

                        // Optional: Show message if no variants found in active tab
                        const visibleVariantsInActiveTab = activeTab ? 
                            activeTab.querySelectorAll('.variant-item[style=""]').length : 0;
                        
                        if (visibleVariantsInActiveTab === 0 && searchValue !== '') {
                            // You could add a "no results" message here if desired
                            console.log('No variants found matching search');
                        }
                    }

                    // Optional: Add event listener for tab changes to reapply search filter
                    document.addEventListener('DOMContentLoaded', function() {
                        const tabPanes = document.querySelectorAll('.tab-pane');
                        
                        tabPanes.forEach(tab => {
                            tab.addEventListener('shown.bs.tab', function () {
                                const searchInput = document.getElementById('variantSearchInput');
                                if (searchInput.value) {
                                    filterVariants(searchInput.value);
                                }
                            });
                        });
                    });
                </script>

            </div>
        </div>
    </div>
    <!--end::Content-->


    <!--begin::Body-->
    <div class="flex-row-auto w-xl-450px">
        <!--begin::Pos order-->
        <div class="card card-flush bg-body" id="kt_pos_form">
            <div class="card-header pt-5">
                <h3 class="card-title fw-bold text-gray-800 fs-2qx">{{__('pagination._current_order')}}</h3>
                <div class="card-toolbar">
                    <a href="#" class="btn btn-light-primary fs-4 fw-bold py-4" onclick="clearCart()">{{__('pagination.clear_all')}}</a>
                </div>
            </div>

            <!--begin::Body-->
            <div class="card-body pt-0">
                <div class="table-responsive mb-8">
                    <table class="table align-middle gs-0 gy-4 my-0">
                        <thead>
                            <tr>
                                <th class="min-w-175px">{{__('pagination._item')}}</th>
                                <th class="w-125px">{{__('pagination._quantity')}}</th>
                                <th class="w-60px">{{__('pagination._total')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Cart items will be dynamically inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!--begin::Summary-->
        <div class="d-flex flex-stack bg-success rounded-3 p-6 mb-11">
            <!--begin::Content-->
            <div class="fs-6 fw-bold text-white">
                <span class="d-block lh-1 mb-2">{{__('pagination.sub_total')}}</span>
                <span class="d-block mb-2">{{__('pagination._discount')}}</span>
                <span class="d-block mb-9">{{__('pagination._tax')}}</span>
                <span class="d-block fs-2qx lh-1">{{__('pagination.grant_total')}}</span>
            </div>
            <!--end::Content-->
            <!--begin::Content-->
            <div class="fs-6 fw-bold text-white text-end">
                <span class="d-block lh-1 mb-2" data-kt-pos-element="total">$0.00</span>
                <span class="d-block mb-2" data-kt-pos-element="discount">-$0.00</span>
                <span class="d-block mb-9" data-kt-pos-element="tax">$11.20</span>
                <span class="d-block fs-2qx lh-1" data-kt-pos-element="grant-total">$0.00</span>
            </div>
            <!--end::Content-->
        </div>
        
        <!--begin::Payment Method-->
        <div class="m-0">
            <h1 class="fw-bold text-gray-800 mb-5">{{__('pagination.payment_method')}}</h1>
            <!--begin::Radio group-->
            <div class="d-flex flex-equal gap-5 gap-xxl-9 px-0 mb-12" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                <!--begin::Radio-->
                <label class="btn bg-light btn-color-gray-600 btn-active-text-gray-800 border border-3 border-gray-100 border-active-primary btn-active-light-primary w-100 px-4" data-kt-button="true">
                    <input class="btn-check" type="radio" name="method" value="1" />
                    <i class="ki-duotone ki-dollar fs-2hx mb-2 pe-0">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <span class="fs-7 fw-bold d-block">{{__('pagination._cash')}}</span>
                </label>

                <!--begin::Radio-->
                <label class="btn bg-light btn-color-gray-600 btn-active-text-gray-800 border border-3 border-gray-100 border-active-primary btn-active-light-primary w-100 px-4" data-kt-button="true">
                    <input class="btn-check" type="radio" name="method" value="2" />
                    <i class="ki-duotone ki-finance-calculator fs-2hx mb-2 pe-0">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <span class="fs-7 fw-bold d-block">{{__('pagination.mobile_money')}}</span>
                </label>
                
                <!--begin::Radio-->
                <label class="btn bg-light btn-color-gray-600 btn-active-text-gray-800 border border-3 border-gray-100 border-active-primary btn-active-light-primary w-100 px-4 active" data-kt-button="true">
                    <input class="btn-check" type="radio" name="method" value="3" />
                    <i class="ki-duotone ki-credit-cart fs-2hx mb-2 pe-0">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <span class="fs-7 fw-bold d-block">{{__('pagination._card')}}</span>
                </label>
            </div>
            
            <button 
                id="processBill" 
                type="button" 
                class="btn btn-primary fs-1 w-100 py-4"
                onclick="processPayment()">
                
                <span class="indicator-label">{{__('pagination.print_bill')}}</span>
                <span class="indicator-progress">{{__('pagination.processing_payments')}}
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
        </div>
    </div>
    <!--end::Sidebar-->
</div>

<script>
    // Cart array to store items
    let cart = [];

    // Add item to cart with stock validation
    function addToCart(variant) {
        // Check if item is out of stock
        if (variant.quantity_available <= 0) {
            toastr['error']('{{ __("pagination.out_of_stock") }}');
            return;
        }
        
        // Check if item already exists in cart
        const existingItemIndex = cart.findIndex(item => item.id === variant.id);
        
        if (existingItemIndex > -1) {
            // Check if we can add more (don't exceed available stock)
            if (cart[existingItemIndex].quantity < variant.quantity_available) {
                cart[existingItemIndex].quantity += 1;
                updateCartItem(existingItemIndex);
                toastr['success']('{{ __("pagination.item_added") }}');
            } else {
                toastr['warning']('{{ __("pagination.max_quantity_reached") }}');
            }
        } else {
            // Add new item to cart
            const cartItem = {
                id: variant.id,
                name: variant.name,
                price: variant.price,
                image: variant.image,
                quantity: 1,
                quantity_available: variant.quantity_available
            };
            cart.push(cartItem);
            renderCartItem(cartItem);
            toastr['success']('{{ __("pagination.item_added") }}');
        }
        
        updateCartTotal();
        calculateCartSummary();
    }

    // Render new cart item
    function renderCartItem(item) {
        const cartTable = document.querySelector('#kt_pos_form tbody');
        const newRow = document.createElement('tr');
        newRow.setAttribute('data-kt-pos-element', 'item');
        newRow.setAttribute('data-kt-pos-item-price', item.price);
        newRow.setAttribute('data-item-id', item.id);
        newRow.innerHTML = `
            <td class="pe-0">
                <div class="d-flex align-items-center">
                    <img src="${item.image}" class="w-50px h-50px rounded-3 me-3" alt="${item.name}" />
                    <span class="fw-bold text-gray-800 cursor-pointer text-hover-primary fs-6 me-1">${item.name}</span>
                </div>
            </td>
            <td class="pe-0">
                <div class="position-relative d-flex align-items-center" data-kt-dialer="true" data-kt-dialer-min="1" data-kt-dialer-max="${item.quantity_available}" data-kt-dialer-step="1" data-kt-dialer-decimals="0">
                    <button type="button" class="btn btn-icon btn-sm btn-light btn-icon-gray-500"  onclick="decreaseQuantity(${item.id})">
                        <i class="ki-duotone ki-minus fs-3x"></i>
                    </button>
                    <input type="text" class="form-control border-0 text-center px-0 fs-3 fw-bold text-gray-800 w-30px quantity-input" 
                        data-kt-dialer-control="input" placeholder="Amount" name="quantity_${item.id}" 
                        value="${item.quantity}" onchange="updateQuantity(${item.id}, this.value)" />
                    <button type="button" class="btn btn-icon btn-sm btn-light btn-icon-gray-500"  onclick="increaseQuantity(${item.id})">
                        <i class="ki-duotone ki-plus fs-3x"></i>
                    </button>
                </div>
            </td>
            <td class="text-end">
                <div class="d-flex align-items-center justify-content-end">
                    <span class="fw-bold text-primary fs-2 item-total me-2">$${(item.price * item.quantity).toFixed(2)}</span>
                    <button type="button" class="btn btn-icon btn-sm btn-danger" onclick="removeFromCart(${item.id})">
                        <i class="bi bi-trash fs-2"></i>
                    </button>
                </div>
            </td>
        `;
        cartTable.appendChild(newRow);
    }

    // Update existing cart item
    function updateCartItem(itemIndex) {
        const item = cart[itemIndex];
        const row = document.querySelector(`tr[data-item-id="${item.id}"]`);
        if (row) {
            const quantityInput = row.querySelector('.quantity-input');
            const itemTotal = row.querySelector('.item-total');
            
            quantityInput.value = item.quantity;
            itemTotal.textContent = `$${(item.price * item.quantity).toFixed(2)}`;
        }
    }

    
    // Quantity functions with stock validation
    function increaseQuantity(itemId) {
        const itemIndex = cart.findIndex(item => item.id === itemId);
        if (itemIndex > -1) {
            if (cart[itemIndex].quantity < cart[itemIndex].quantity_available) {
                cart[itemIndex].quantity += 1;
                updateCartItem(itemIndex);
                updateCartTotal();
            } else {
                toastr['warning']('{{ __("pagination.max_quantity_reached") }}');
            }
        }
    }


    function decreaseQuantity(itemId) {
        const itemIndex = cart.findIndex(item => item.id === itemId);
        if (itemIndex > -1 && cart[itemIndex].quantity > 1) {
            cart[itemIndex].quantity -= 1;
            updateCartItem(itemIndex);
            updateCartTotal();
        }
    }

    function updateQuantity(itemId, newQuantity) {
        const quantity = parseInt(newQuantity);
        const itemIndex = cart.findIndex(item => item.id === itemId);
        
        if (itemIndex > -1 && !isNaN(quantity)) {
            if (quantity > 0 && quantity <= cart[itemIndex].quantity_available) {
                cart[itemIndex].quantity = quantity;
                updateCartItem(itemIndex);
                updateCartTotal();
            } else if (quantity > cart[itemIndex].quantity_available) {

                toastr['warning']('{{ __("pagination.max_quantity_reached") }}');
                // Reset to max available
                cart[itemIndex].quantity = cart[itemIndex].quantity_available;
                updateCartItem(itemIndex);
                updateCartTotal();
            }
        }
    }

    // Remove item from cart
    function removeFromCart(itemId) {
        const itemIndex = cart.findIndex(item => item.id === itemId);
        if (itemIndex > -1) {
            cart.splice(itemIndex, 1);
            const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
            if (row) row.remove();
            updateCartTotal();
            toastr['success']('{{ __("pagination.item_removed") }}');
        }
    }

    // Clear all items from cart
    function clearCart() {
        cart = [];
        const cartTable = document.querySelector('#kt_pos_form tbody');
        cartTable.innerHTML = '';
        updateCartTotal();
        toastr['success']('{{ __("pagination.cart_cleared") }}');
    }

    // Update cart total
    function updateCartTotal() {
        const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        document.querySelector('[data-kt-pos-element="total"]').textContent = `$${total.toFixed(2)}`;
        
        // Show/hide empty state
        const emptyState = document.querySelector('.cart-empty-state');
        const cartTable = document.querySelector('#kt_pos_form tbody');
        
        if (cart.length === 0) {
            if (!emptyState) {
                const emptyRow = document.createElement('tr');
                emptyRow.className = 'cart-empty-state';
                emptyRow.innerHTML = `
                    <td colspan="3" class="text-center py-10">
                        <div class="text-gray-600 fs-5">{{ __("pagination.cart_empty") }}</div>
                    </td>
                `;
                cartTable.appendChild(emptyRow);
            }
        } else {
            if (emptyState) emptyState.remove();
        }
    }

    // Initialize cart on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateCartTotal();
        
        // Add event listener to clear all button
        document.querySelector('[onclick="clearCart()"]').addEventListener('click', function(e) {
            e.preventDefault();
            clearCart();
        });
    });

    // Calculate cart summary (subtotal, discount, tax, grand total)
    function calculateCartSummary() {
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const discount = 0; // 0% discount for now
        const taxRate = 0; // 0% tax for now
        const tax = subtotal * (taxRate / 100);
        const grandTotal = subtotal - discount + tax;
        
        // Update summary display
        document.querySelector('[data-kt-pos-element="total"]').textContent = `$${subtotal.toFixed(2)}`;
        document.querySelector('[data-kt-pos-element="discount"]').textContent = `-$${discount.toFixed(2)}`;
        document.querySelector('[data-kt-pos-element="tax"]').textContent = `$${tax.toFixed(2)}`;
        document.querySelector('[data-kt-pos-element="grant-total"]').textContent = `$${grandTotal.toFixed(2)}`;
    }

    // Update the updateCartTotal function to also calculate summary
    function updateCartTotal() {
        calculateCartSummary();
    }
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

        // Start loading
        LiveBlade.toggleButtonLoading(submitButton, true);

        // Get selected payment method
        const selectedMethod = document.querySelector('input[name="method"]:checked');
        const paymentMethod = selectedMethod ? selectedMethod.value : '3'

        // Prepare cart data
        const cartData = {
            items: cart.map(item => ({
                variant_id: item.id,
                quantity: item.quantity,
                price: item.price,
                name: item.name
            })),
            payment_method: paymentMethod,
            subtotal: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0),
            discount: 0, 
            tax: 0, 
            total: cart.reduce((sum, item) => sum + (item.price * item.quantity), 0)
        };
        
        const formData = new FormData();
        formData.append('cart_data', JSON.stringify(cartData));
        console.log(formData);
        
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
                
                // Open modal based on payment method
                // openPaymentModal(paymentMethod, data.order_data || {});
                
                // Clear cart after successful payment
                clearCart();
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