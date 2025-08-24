
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
                                            data-product="{{ $product->id }}">
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


    <!--begin::Sidebar-->
    <div class="flex-row-auto w-xl-450px">
        <!--begin::Pos order-->
        <div class="card card-flush bg-body" id="kt_pos_form">
            <!--begin::Header-->
            <div class="card-header pt-5">
                <h3 class="card-title fw-bold text-gray-800 fs-2qx">Current Order</h3>
                <!--begin::Toolbar-->
                <div class="card-toolbar">
                    <a href="#" class="btn btn-light-primary fs-4 fw-bold py-4">Clear All</a>
                </div>
                <!--end::Toolbar-->
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body pt-0">
                <!--begin::Table container-->
                <div class="table-responsive mb-8">
                    <!--begin::Table-->
                    <table class="table align-middle gs-0 gy-4 my-0">
                        <!--begin::Table head-->
                        <thead>
                            <tr>
                                <th class="min-w-175px"></th>
                                <th class="w-125px"></th>
                                <th class="w-60px"></th>
                            </tr>
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody>
                            <tr data-kt-pos-element="item" data-kt-pos-item-price="33">
                                <td class="pe-0">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/media/stock/food/img-2.jpg" class="w-50px h-50px rounded-3 me-3" alt="" />
                                        <span class="fw-bold text-gray-800 cursor-pointer text-hover-primary fs-6 me-1">T-Bone Stake</span>
                                    </div>
                                </td>
                                <td class="pe-0">
                                    <!--begin::Dialer-->
                                    <div class="position-relative d-flex align-items-center" data-kt-dialer="true" data-kt-dialer-min="1" data-kt-dialer-max="10" data-kt-dialer-step="1" data-kt-dialer-decimals="0">
                                        <!--begin::Decrease control-->
                                        <button type="button" class="btn btn-icon btn-sm btn-light btn-icon-gray-500" data-kt-dialer-control="decrease">
                                            <i class="ki-duotone ki-minus fs-3x"></i>
                                        </button>
                                        <!--end::Decrease control-->
                                        <!--begin::Input control-->
                                        <input type="text" class="form-control border-0 text-center px-0 fs-3 fw-bold text-gray-800 w-30px" data-kt-dialer-control="input" placeholder="Amount" name="manageBudget" readonly="readonly" value="2" />
                                        <!--end::Input control-->
                                        <!--begin::Increase control-->
                                        <button type="button" class="btn btn-icon btn-sm btn-light btn-icon-gray-500" data-kt-dialer-control="increase">
                                            <i class="ki-duotone ki-plus fs-3x"></i>
                                        </button>
                                        <!--end::Increase control-->
                                    </div>
                                    <!--end::Dialer-->
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-primary fs-2" data-kt-pos-element="item-total">$66.00</span>
                                </td>
                            </tr>
                            <tr data-kt-pos-element="item" data-kt-pos-item-price="7.5">
                                <td class="pe-0">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/media/stock/food/img-9.jpg" class="w-50px h-50px rounded-3 me-3" alt="" />
                                        <span class="fw-bold text-gray-800 cursor-pointer text-hover-primary fs-6 me-1">Soup of the Day</span>
                                    </div>
                                </td>
                                <td class="pe-0">
                                    <!--begin::Dialer-->
                                    <div class="position-relative d-flex align-items-center" data-kt-dialer="true" data-kt-dialer-min="1" data-kt-dialer-max="10" data-kt-dialer-step="1" data-kt-dialer-decimals="0">
                                        <!--begin::Decrease control-->
                                        <button type="button" class="btn btn-icon btn-sm btn-light btn-icon-gray-500" data-kt-dialer-control="decrease">
                                            <i class="ki-duotone ki-minus fs-3x"></i>
                                        </button>
                                        <!--end::Decrease control-->
                                        <!--begin::Input control-->
                                        <input type="text" class="form-control border-0 text-center px-0 fs-3 fw-bold text-gray-800 w-30px" data-kt-dialer-control="input" placeholder="Amount" name="manageBudget" readonly="readonly" value="1" />
                                        <!--end::Input control-->
                                        <!--begin::Increase control-->
                                        <button type="button" class="btn btn-icon btn-sm btn-light btn-icon-gray-500" data-kt-dialer-control="increase">
                                            <i class="ki-duotone ki-plus fs-3x"></i>
                                        </button>
                                        <!--end::Increase control-->
                                    </div>
                                    <!--end::Dialer-->
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-primary fs-2" data-kt-pos-element="item-total">$7.50</span>
                                </td>
                            </tr>
                            <tr data-kt-pos-element="item" data-kt-pos-item-price="13.5">
                                <td class="pe-0">
                                    <div class="d-flex align-items-center">
                                        <img src="assets/media/stock/food/img-3.jpg" class="w-50px h-50px rounded-3 me-3" alt="" />
                                        <span class="fw-bold text-gray-800 cursor-pointer text-hover-primary fs-6 me-1">Pancakes</span>
                                    </div>
                                </td>
                                <td class="pe-0">
                                    <!--begin::Dialer-->
                                    <div class="position-relative d-flex align-items-center" data-kt-dialer="true" data-kt-dialer-min="1" data-kt-dialer-max="10" data-kt-dialer-step="1" data-kt-dialer-decimals="0">
                                        <!--begin::Decrease control-->
                                        <button type="button" class="btn btn-icon btn-sm btn-light btn-icon-gray-500" data-kt-dialer-control="decrease">
                                            <i class="ki-duotone ki-minus fs-3x"></i>
                                        </button>
                                        <!--end::Decrease control-->
                                        <!--begin::Input control-->
                                        <input type="text" class="form-control border-0 text-center px-0 fs-3 fw-bold text-gray-800 w-30px" data-kt-dialer-control="input" placeholder="Amount" name="manageBudget" readonly="readonly" value="2" />
                                        <!--end::Input control-->
                                        <!--begin::Increase control-->
                                        <button type="button" class="btn btn-icon btn-sm btn-light btn-icon-gray-500" data-kt-dialer-control="increase">
                                            <i class="ki-duotone ki-plus fs-3x"></i>
                                        </button>
                                        <!--end::Increase control-->
                                    </div>
                                    <!--end::Dialer-->
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-primary fs-2" data-kt-pos-element="item-total">$27.00</span>
                                </td>
                            </tr>
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Table container-->
                <!--begin::Summary-->
                <div class="d-flex flex-stack bg-success rounded-3 p-6 mb-11">
                    <!--begin::Content-->
                    <div class="fs-6 fw-bold text-white">
                        <span class="d-block lh-1 mb-2">Subtotal</span>
                        <span class="d-block mb-2">Discounts</span>
                        <span class="d-block mb-9">Tax(12%)</span>
                        <span class="d-block fs-2qx lh-1">Total</span>
                    </div>
                    <!--end::Content-->
                    <!--begin::Content-->
                    <div class="fs-6 fw-bold text-white text-end">
                        <span class="d-block lh-1 mb-2" data-kt-pos-element="total">$100.50</span>
                        <span class="d-block mb-2" data-kt-pos-element="discount">-$8.00</span>
                        <span class="d-block mb-9" data-kt-pos-element="tax">$11.20</span>
                        <span class="d-block fs-2qx lh-1" data-kt-pos-element="grant-total">$93.46</span>
                    </div>
                    <!--end::Content-->
                </div>
                <!--end::Summary-->
                <!--begin::Payment Method-->
                <div class="m-0">
                    <!--begin::Title-->
                    <h1 class="fw-bold text-gray-800 mb-5">Payment Method</h1>
                    <!--end::Title-->
                    <!--begin::Radio group-->
                    <div class="d-flex flex-equal gap-5 gap-xxl-9 px-0 mb-12" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                        <!--begin::Radio-->
                        <label class="btn bg-light btn-color-gray-600 btn-active-text-gray-800 border border-3 border-gray-100 border-active-primary btn-active-light-primary w-100 px-4" data-kt-button="true">
                            <!--begin::Input-->
                            <input class="btn-check" type="radio" name="method" value="0" />
                            <!--end::Input-->
                            <!--begin::Icon-->
                            <i class="ki-duotone ki-dollar fs-2hx mb-2 pe-0">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <!--end::Icon-->
                            <!--begin::Title-->
                            <span class="fs-7 fw-bold d-block">Cash</span>
                            <!--end::Title-->
                        </label>
                        <!--end::Radio-->
                        <!--begin::Radio-->
                        <label class="btn bg-light btn-color-gray-600 btn-active-text-gray-800 border border-3 border-gray-100 border-active-primary btn-active-light-primary w-100 px-4 active" data-kt-button="true">
                            <!--begin::Input-->
                            <input class="btn-check" type="radio" name="method" value="1" />
                            <!--end::Input-->
                            <!--begin::Icon-->
                            <i class="ki-duotone ki-credit-cart fs-2hx mb-2 pe-0">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <!--end::Icon-->
                            <!--begin::Title-->
                            <span class="fs-7 fw-bold d-block">Card</span>
                            <!--end::Title-->
                        </label>
                        <!--end::Radio-->
                        <!--begin::Radio-->
                        <label class="btn bg-light btn-color-gray-600 btn-active-text-gray-800 border border-3 border-gray-100 border-active-primary btn-active-light-primary w-100 px-4" data-kt-button="true">
                            <!--begin::Input-->
                            <input class="btn-check" type="radio" name="method" value="2" />
                            <!--end::Input-->
                            <!--begin::Icon-->
                            <i class="ki-duotone ki-paypal fs-2hx mb-2 pe-0">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <!--end::Icon-->
                            <!--begin::Title-->
                            <span class="fs-7 fw-bold d-block">E-Wallet</span>
                            <!--end::Title-->
                        </label>
                        <!--end::Radio-->
                    </div>
                    <!--end::Radio group-->
                    <!--begin::Actions-->
                    <button class="btn btn-primary fs-1 w-100 py-4">Print Bills</button>
                    <!--end::Actions-->
                </div>
                <!--end::Payment Method-->
            </div>
            <!--end: Card Body-->
        </div>
        <!--end::Pos order-->
    </div>
    <!--end::Sidebar-->
</div>