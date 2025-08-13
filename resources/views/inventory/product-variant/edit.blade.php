 
<div class="modal fade" id="editProduct{{ $product->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-850px">
        <div class="modal-content">
            <div class="modal-header" id="kt_modal_add_currency">
                <h2 class="fw-bold">{{__('auth._edit')}} {{__('pagination._variations_of')}} - {{ $product_variants->name }}</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body px-5 my-7">
                <form id="kt_modal_product_form{{ $product->id }}" class="form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" value="{{ $product_variants->id }}" class="form-control form-control-solid" name="product_id" />
                    <div class="text-center pt-10">
                        <div class="row g-9 mb-8">
                            <div class="d-flex flex-column mb-8 fv-row col-md-6">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">{{__('pagination._product')}} </span>
                                </label>
                                <input type="text" value="{{ $product->name }}" class="form-control form-control-solid" name="name" />
                                <div id="name{{ $product->id }}"></div>
                            </div>
                            <div class="d-flex flex-column mb-8 fv-row col-md-6">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">{{__('pagination._sku')}} </span>
                                </label>
                                <input type="text" value="{{ $product->sku }}" class="form-control form-control-solid" name="sku" />
                                <div id="sku{{ $product->id }}"></div>
                            </div>
                        </div>

                        <div class="row g-9 mb-8">
                            <div class="d-flex flex-column mb-8 fv-row col-md-12">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">{{__('pagination._barcode')}} </span>
                                </label>
                                <input type="text" value="{{ $product->barcode }}" class="form-control form-control-solid" name="barcode" />
                                <div id="barcode{{ $product->id }}"></div>
                            </div>
                        </div>
                        
                        <div class="row g-9 mb-8">
                            <div class="d-flex flex-column mb-8 fv-row col-md-6">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">{{__('pagination._price')}} </span>
                                </label>
                                <input type="text" value="{{ $product->price }}" class="form-control form-control-solid" name="price" />
                                <div id="price{{ $product->id }}"></div>
                            </div>
                            <div class="d-flex flex-column mb-8 fv-row col-md-6">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">{{__('pagination.cost_price')}} </span>
                                </label>
                                <input type="text" value="{{ $product->cost_price }}" class="form-control form-control-solid" name="cost_price" />
                                <div id="cost_price{{ $product->id }}"></div>
                            </div>
                        </div>

                        <div class="row g-9 mb-8">
                            <div class="d-flex flex-column mb-8 fv-row col-md-6">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">{{__('pagination._weight')}} </span>
                                </label>
                                <input type="text" value="{{ $product->weight }}" class="form-control form-control-solid" name="weight" />
                                <div id="weight{{ $product->id }}"></div>
                            </div>
                            <div class="d-flex flex-column mb-8 fv-row col-md-6">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">{{__('pagination.weight_unit')}} </span>
                                </label>
                                <select name="weight_unit" class="form-select" data-control="select2" data-close-on-select="false" data-placeholder="{{__('auth._select')}}" data-allow-clear="true">
                                    <option></option>
                                    @foreach ($uoms as $umo)
                                        <option value="{{ $umo->id }}" {{ $product->weight_unit == $umo->id ? 'selected' : '' }}>{{ $umo->name }}</option>
                                    @endforeach
                                </select>
                                <div id="weight_unit{{ $product->id }}"></div>
                            </div>
                        </div>
                        
                        
                        

                        <button type="button" id="closeModalEditButton{{$product->id}}" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('auth._discard') }}</button>
                        <button onclick="editInstanceLoop({{$product->id }})" id="editUOMButton{{ $product->id }}" type="button" class="btn btn-primary" id>
                            <span class="indicator-label">{{__('auth._update')}}</span>
                            <span class="indicator-progress">{{__('auth.please_wait') }}
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>  

<script>
    
    function editInstanceLoop(uniqueId) {
        const submitButton = document.getElementById('editUOMButton' + uniqueId);
        LiveBlade.toggleButtonLoading(submitButton, true);

        // Select the form and create FormData from it
        var form = document.getElementById('kt_modal_product_form' + uniqueId);
        var formData = new FormData(form);

        var data = Object.fromEntries(formData.entries());
        // console.log(data);

        // Set up the URL dynamically
        var updateUrl = '{{ route('variants.update', ['variant' => ':id']) }}'.replace(':id', uniqueId);

        // console.log(updateUrl);
        
        // Submit form data asynchronously
        LiveBlade.editLoopForms(data, updateUrl)
            .then(noErrorStatus => {
                if (noErrorStatus) {
                    var closeButton = document.getElementById('closeModalEditButton' + uniqueId);
                    if (closeButton) {
                        closeButton.click();
                    }
                }
                })
                .catch(error => {
                    console.error('An unexpected error occurred:', error);
                    // Display user-friendly error feedback here, if needed
                })
                .finally(() => {
                    // End loading state using reusable function
                    LiveBlade.toggleButtonLoading(submitButton, false);
                });


    }
</script>


