<x-guest-layout>
    @section('title', __('Login Page'))
    @section('content')

    <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form">
        @csrf
        <div class="text-center mb-11">
            <h1 class="text-gray-900 fw-bolder mb-3">{{__('Sign In')}}</h1>
            <div class="text-gray-500 fw-semibold fs-6">{{__('Enter Your Email And Password')}}</div>
        </div>
        <div class="fv-row mb-8">
            <input type="text" placeholder="Email" name="email" autocomplete="off" class="form-control bg-transparent" />
            <div id="email"></div>
        </div>
        <div class="fv-row mb-3">
            <input type="password" placeholder="Password" name="password" autocomplete="off" class="form-control bg-transparent" />
            <div id="password"></div>
        </div>
        <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
            <div></div>
            <a href="javascript:void(0)" class="link-primary" onclick="navigateToGuestPage('{{route('password.request')}}')">
                {{__('Forgot Password ?')}}
            </a>
        </div>

        <div class="d-grid mb-10">
            <button id="loginSubmitButton" onclick="loginAction()" type="button" class="btn btn-primary">
                <span class="indicator-label">{{__('Sign In')}}</span>
                <span class="indicator-progress" style="display: none;">{{__('Please wait... ')}}
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
        </div>
    </form>

    @endsection

</x-guest-layout>
