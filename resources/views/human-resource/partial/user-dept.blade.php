
<div class="modal fade delete-user-modal" id="editUserDeptModal{{$employee->id}}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-750px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">{{ __('auth.user_department') }} {{ $employee->first_name }}</h2>
                <button type="button" class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"></i>
                </button>
            </div>
            <div class="modal-body scroll-y mx-lg-5 my-7">
                <form id="edit_user_form{{ $employee->id }}" 
                    class="form" 
                    action="{{ route('employees.updateDepartments', $employee->id) }}" 
                    method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="user_id" value="{{ $employee->id }}">

                    <div class="d-flex flex-column mb-8 fv-row col-md-12">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">{{ __('auth._department') }}</span>
                        </label>

                        <div class="row">
                            @foreach ($departments as $department)
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input 
                                            type="checkbox" 
                                            class="form-check-input" 
                                            name="departments[]" 
                                            value="{{ $department->id }}"
                                            id="dept-{{ $employee->id }}-{{ $department->id }}"
                                            {{ optional($employee->departments)->contains($department->id) ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="dept-{{ $employee->id }}-{{ $department->id }}">
                                            {{ ucwords(str_replace('_', ' ', $department->name)) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div id="departments{{ $employee->id }}"></div>
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





