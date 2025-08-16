<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<title>@yield('title')</title>
		<meta charset="utf-8" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="The most advanced POS System in the world" />
        <meta name="keywords" content="Top 10 most used POS System in the world now" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta property="og:locale" content="en_US" />
        <meta property="og:type" content="LAEL POS SYSTEM" />
        <meta property="og:title" content="The most advanced POS System in the world" />
        <meta property="og:url" content="https://lael.stardena.com" />
        <meta property="og:site_name" content="Lael by Stardena" />
        <link rel="canonical" href="https://lael.stardena.com" />
        <link rel="shortcut icon" href="{{ getFaviconImage() }}" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
        <link href="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />

				
		<style>
			.custom-select-container {
				position: relative;
			}
			.custom-select-search {
				width: 100%;
				padding: 8px 12px;
				border: 1px solid #ddd;
				border-radius: 4px;
			}
			.custom-select-dropdown {
				position: absolute;
				top: 100%;
				left: 0;
				right: 0;
				z-index: 1000;
				background: white;
				border: 1px solid #ddd;
				border-radius: 4px;
				box-shadow: 0 2px 4px rgba(0,0,0,0.1);
				max-height: 300px;
				overflow-y: auto;
			}
			.custom-select-option {
				padding: 8px 12px;
				cursor: pointer;
			}
			.custom-select-option:hover {
				background-color: #f5f5f5;
			}
			.custom-select-option.selected {
				background-color: #e9ecef;
			}
		</style>

        <script>
            if (window.top != window.self) { window.top.location.replace(window.self.location.href); }
        </script>
        {{-- laravel bladeLive.js library --}}
		@include('layouts.liveblade-imports') 
    </head>
	@php
		use Illuminate\Support\Str;
	@endphp
	<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default">
		
        <div id="loader"></div>		
		<script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>


		<script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
        <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
            <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
				@include('layouts.header')
				<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                    <div id="kt_app_sidebar" class="app-sidebar flex-column"
                        style="background-color: {{ getMailOptions('menu_nav_color') }};"
                        data-kt-drawer="true"
                        data-kt-drawer-name="app-sidebar"
                        data-kt-drawer-activate="{default: true, lg: false}"
                        data-kt-drawer-overlay="true"
                        data-kt-drawer-width="225px"
                        data-kt-drawer-direction="start"
                        data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
						
                        <!--begin::Logo-->
						<div class="app-sidebar-logo px-6" id="kt_app_sidebar_logo">
						
							<!--begin::Logo image-->
							<a href="{{ route('dashboard') }}">
								<img 
									alt="Logo" 
									src="{{ getLogoImage() }}" 
									class="h-6 w-auto object-contain app-sidebar-logo-default" 
									style="max-height: 44px;" 
								/>

								<img alt="Logo" src="assets/media/logos/default-small.svg" class="h-20px app-sidebar-logo-minimize" />
							</a>
							{{-- 	
							<h1 class="text-white font-bold text-lg app-sidebar-logo-default" style="font-size: 50px">
								{{ appDefaultName() }}
							</h1>
							--}}
							<!--end::Logo image-->

							<!--begin::Sidebar toggle-->
							<!--begin::Minimized sidebar setup:
                                if (isset($_COOKIE["sidebar_minimize_state"]) && $_COOKIE["sidebar_minimize_state"] === "on") { 
                                    1. "src/js/layout/sidebar.js" adds "sidebar_minimize_state" cookie value to save the sidebar minimize state.
                                    2. Set data-kt-app-sidebar-minimize="on" attribute for body tag.
                                    3. Set data-kt-toggle-state="active" attribute to the toggle element with "kt_app_sidebar_toggle" id.
                                    4. Add "active" class to to sidebar toggle element with "kt_app_sidebar_toggle" id.
                                }
                            -->
							<div id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary h-30px w-30px position-absolute top-50 start-100 translate-middle rotate" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
								<i class="ki-duotone ki-black-left-line fs-3 rotate-180">
									<span class="path1"></span>
									<span class="path2"></span>
								</i>
							</div>
							<!--end::Sidebar toggle-->
						</div>
						<!--end::Logo-->
                        @include('layouts.navigation')
                    </div>
					<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
						<div class="d-flex flex-column flex-column-fluid">
							@yield('content')
						</div>
					</div>
				</div>
			</div>
		</div>

				
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const originalSelect = document.getElementById('originalVariantSelect');
				const searchInput = document.querySelector('.custom-select-search');
				const dropdown = document.querySelector('.custom-select-dropdown');
				const optionsContainer = document.querySelector('.custom-select-options');
				
				// Create a copy of original options
				const options = Array.from(originalSelect.options).map(option => ({
					value: option.value,
					text: option.text
				}));
				
				// Initialize dropdown
				function renderOptions(filter = '') {
					optionsContainer.innerHTML = '';
					
					const filteredOptions = options.filter(option => 
						option.text.toLowerCase().includes(filter.toLowerCase())
					);
					
					if (filteredOptions.length === 0) {
						const noResults = document.createElement('div');
						noResults.className = 'custom-select-option';
						noResults.textContent = 'No variants found';
						optionsContainer.appendChild(noResults);
						return;
					}
					
					filteredOptions.forEach(option => {
						const optionElement = document.createElement('div');
						optionElement.className = 'custom-select-option';
						optionElement.textContent = option.text;
						optionElement.dataset.value = option.value;
						
						optionElement.addEventListener('click', function() {
							originalSelect.value = option.value;
							searchInput.value = option.text;
							dropdown.style.display = 'none';
						});
						
						optionsContainer.appendChild(optionElement);
					});
				}
				
				// Event listeners
				searchInput.addEventListener('focus', function() {
					renderOptions();
					dropdown.style.display = 'block';
				});
				
				searchInput.addEventListener('input', function() {
					renderOptions(this.value);
				});
				
				document.addEventListener('click', function(e) {
					if (!e.target.closest('.custom-select-container')) {
						dropdown.style.display = 'none';
					}
				});
				
				// Initialize with first render
				renderOptions();
			});
		</script>
				
        
		<script>var hostUrl = "{{ asset('assets/') }}";</script>
		<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
		<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
		<script src="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>

		<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/map.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/worldLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/continentsLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/usaLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZonesLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZoneAreasLow.js"></script>

		<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
        <script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
		<script src="{{ asset('assets/js/custom/widgets.js') }}"></script>
		<script src="{{ asset('assets/js/custom/apps/chat/chat.js') }}"></script>
		<script src="{{ asset('assets/js/custom/utilities/modals/upgrade-plan.js') }}"></script>
		<script src="{{ asset('assets/js/custom/utilities/modals/create-app.js') }}"></script>
		<script src="{{ asset('assets/js/custom/utilities/modals/new-target.js') }}"></script>
		<script src="{{ asset('assets/js/custom/utilities/modals/users-search.js') }}"></script>

		<script src="{{ asset('assets/js/custom/apps/user-management/users/list/table.js') }}"></script>
		<script src="{{ asset('assets/js/custom/apps/user-management/users/list/export-users.js') }}"></script>
		<script src="{{ asset('assets/js/custom/apps/user-management/users/list/add.js') }}"></script>

        // <!--CKEditor Build Bundles:: Only include the relevant bundles accordingly-->
        <script src="{{ asset('assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js') }}"></script>
        
	</body>
</html>

