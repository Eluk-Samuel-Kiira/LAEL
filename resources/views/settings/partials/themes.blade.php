<section>
    <form id="updateAppThemes" class="form">
        @csrf
        @method('patch')
        <div class="card-body border-top p-9">
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6">{{__('auth.menu_color')}}</label>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12 fv-row">
                            <input type="color" name="menu_nav_color" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" value="{{ $app_info->menu_nav_color }}" />
                            <div id="menu_nav_color"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6">{{__('auth.font_family')}}</label>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12 fv-row">
                            <input type="text" name="font_family" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" value="{{ $app_info->font_family }}" />
                            <div id="font_family"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-6">
                <label class="col-lg-4 col-form-label fw-semibold fs-6">{{__('auth.font_size')}}</label>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12 fv-row">
                            <input type="number" name="font_size" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" value="{{ $app_info->font_size }}" />
                            <div id="font_size"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end py-6 px-9">
            <button id="submitupdateTheme" type="submit" class="btn btn-primary">
                <span class="indicator-label">{{__('auth.submit') }}</span>
                <span class="indicator-progress" style="display: none;">
                    {{__('auth.please_wait') }}
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
        </div>
    </form>
</section>
