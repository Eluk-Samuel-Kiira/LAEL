
<div class="modal fade delete-user-modal" id="updateProductLoc{{$product->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">{{ __('pagination._dept_loc') }} {{ $product->name }}</h2>
                <button type="button" class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"></i>
                </button>
            </div>
            <div class="modal-body scroll-y mx-lg-5 my-7">
                <form id="edit_user_form{{ $product->id }}" 
                    class="form" 
                    action="{{ route('location.dept', $product) }}" 
                    method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div class="mb-3">
                        <label for="departments">{{ __('auth._department') }}</label>
                        <div class="row">
                            @foreach($departments as $department)
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input 
                                            type="checkbox" 
                                            class="form-check-input"
                                            name="departments[]" 
                                            value="{{ $department->id }}"
                                            {{ in_array($department->id, old('departments', $product->departments->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}
                                        >
                                        {{ $department->name }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="locations">{{ __('pagination._location') }}</label>
                        <div class="row">
                            @foreach($locations as $location)
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input 
                                            type="checkbox"
                                            class="form-check-input" 
                                            name="locations[]" 
                                            value="{{ $location->id }}"
                                            {{ in_array($location->id, old('locations', $product->locations->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}
                                        >
                                        {{ $location->name }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="text-center pt-15">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('auth._discard') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">{{ __('auth._update') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>





