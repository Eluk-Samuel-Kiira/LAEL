
<style>
    /* Ensure menu title and menu link have larger fonts */
    .menu-item .menu-link .menu-title {
        font-size: 1.1rem; /* Increased font size for the menu title */
        font-weight: bold; /* Bold the menu title */
        font-family: 'Verdana', sans-serif; /* Set a distinct font for menu title */
    }

    /* Increase font size of the menu link text */
    .menu-item .menu-link {
        font-size: 2rem; /* Increase the size of the link text */
        font-family: 'Arial', sans-serif; /* Font style for the link */
    }

    /* Increase the size of the icon */
    .menu-item .menu-icon i {
        font-size: 2rem; /* Increase the icon size */
        color: #007bff; /* Optional: Change the icon color */
    }

    /* Increase the arrow size */
    .menu-item .menu-arrow {
        font-size: 1.5rem; /* Increase the size of the menu arrow */
    }

</style>
<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
    <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
        <div id="kt_app_sidebar_menu_scroll" class="scroll-y my-5 mx-3" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px">
            <div class="menu menu-column menu-rounded menu-sub-indention px-3" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">

                <div data-kt-menu-trigger="click" class="menu-item here menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="bi bi-building-dash fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </span>
                        <span class="menu-title" style="font-family:  {{ getMailOptions('font_family') }}; font-size: {{ getMailOptions('font_size') }}rem">{{__('Dashboards')}}</span>
                        <span class="menu-arrow"></span>
                    </span>

                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link active" data-link href="javascript:void(0);" onclick="navigateToAppPages('{{ route('dashboard') }}')">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">{{ __('Dashboard') }}</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link" data-link href="javascript:void(0);" onclick="navigateToAppPages('{{ route('overview') }}')">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">{{ __('Overview') }}</span>
                            </a>
                        </div>


                    </div>
                </div>

                <div data-kt-menu-trigger="click" class="menu-item here {{ is_tab_show([]) }} menu-accordion">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="bi bi-gear-wide-connected fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </span>
                        <span class="menu-title" style="font-family:  {{ getMailOptions('font_family') }}; font-size: {{ getMailOptions('font_size') }}rem">{{__('App Settings')}}</span>
                        <span class="menu-arrow"></span>
                    </span>

                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <a class="menu-link active" data-link href="javascript:void(0);" onclick="navigateToAppPages('{{ route('settings.index') }}')">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">{{ __('General Settings') }}</span>
                            </a>
                        </div>


                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
