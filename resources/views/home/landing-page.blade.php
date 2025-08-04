
@extends('home.layout')
@section('content')
	<!--begin::Header Section-->
	<div class="mb-0" id="home">
		<div class="bgi-no-repeat bgi-size-contain bgi-position-x-center bgi-position-y-bottom landing-dark-bg" style="background-image: url(assets/media/svg/illustrations/landing.svg)">
			<div class="landing-header" data-kt-sticky="true" data-kt-sticky-name="landing-header" data-kt-sticky-offset="{default: '200px', lg: '300px'}">
				<div class="container">
					<div class="d-flex align-items-center justify-content-between">
						<div class="d-flex align-items-center flex-equal">
							<button class="btn btn-icon btn-active-color-primary me-3 d-flex d-lg-none" id="kt_landing_menu_toggle">
								<i class="ki-duotone ki-abstract-14 fs-2hx">
									<span class="path1"></span>
									<span class="path2"></span>
								</i>
							</button>
							<a href="landing.html">
								<img alt="Logo" src="{{ asset('assets/media/logos/landing.svg') }}" class="logo-default h-25px h-lg-30px" />
								<img alt="Logo" src="{{ asset('assets/media/logos/landing-dark.svg') }}" class="logo-sticky h-20px h-lg-25px" />
							</a>
						</div>
						<div class="d-lg-block" id="kt_header_nav_wrapper">
							<div class="d-lg-block p-5 p-lg-0" data-kt-drawer="true" data-kt-drawer-name="landing-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="200px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_landing_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav_wrapper'}">
								<div class="menu menu-column flex-nowrap menu-rounded menu-lg-row menu-title-gray-600 menu-state-title-primary nav nav-flush fs-5 fw-semibold" id="kt_landing_menu">
									<div class="menu-item">
										<a class="menu-link nav-link active py-3 px-4 px-xxl-6" href="#kt_body" data-kt-scroll-toggle="true" data-kt-drawer-dismiss="true">Home</a>
									</div>
									<div class="menu-item">
										<a class="menu-link nav-link py-3 px-4 px-xxl-6" href="#how-it-works" data-kt-scroll-toggle="true" data-kt-drawer-dismiss="true">How it Works</a>
									</div>
									<div class="menu-item">
										<a class="menu-link nav-link py-3 px-4 px-xxl-6" href="#achievements" data-kt-scroll-toggle="true" data-kt-drawer-dismiss="true">Achievements</a>
									</div>
									<div class="menu-item">
										<a class="menu-link nav-link py-3 px-4 px-xxl-6" href="#team" data-kt-scroll-toggle="true" data-kt-drawer-dismiss="true">Team</a>
									</div>
									<div class="menu-item">
										<a class="menu-link nav-link py-3 px-4 px-xxl-6" href="#portfolio" data-kt-scroll-toggle="true" data-kt-drawer-dismiss="true">Portfolio</a>
									</div>
									<div class="menu-item">
										<a class="menu-link nav-link py-3 px-4 px-xxl-6" href="#pricing" data-kt-scroll-toggle="true" data-kt-drawer-dismiss="true">Pricing</a>
									</div>
								</div>
							</div>
						</div>
						
						<div class="flex-equal text-end ms-1">
							@auth
								<button data-link onclick="reloadTo('/dashboard')" class="btn btn-primary">{{__('Dashboard')}}</button>
							@else
								<button data-link onclick="navigateToGuestPage('/login')" class="btn btn-success">Sign In</button>
							@endauth
						</div>

					</div>
				</div>
			</div>
			<!--end::Header-->
			
			<!--begin::Landing hero-->
			<div class="d-flex flex-column flex-center w-100 min-h-350px min-h-lg-500px px-9">
				<div class="text-center mb-5 mb-lg-10 py-10 py-lg-20">
					<h1 class="text-white lh-base fw-bold fs-2x fs-lg-3x mb-15">Build An Outstanding Solutions 
					<br />with 
					<span style="background: linear-gradient(to right, #12CE5D 0%, #FFD80C 100%);-webkit-background-clip: text;-webkit-text-fill-color: transparent;">
						<span id="kt_landing_hero_text">The Best Theme Ever</span>
					</span></h1>
					<a href="index.html" class="btn btn-primary">Try Metronic</a>
				</div>
				<div class="d-flex flex-center flex-wrap position-relative px-5">
					<div class="d-flex flex-center m-3 m-md-6" data-bs-toggle="tooltip" title="Fujifilm">
						<img src="assets/media/svg/brand-logos/fujifilm.svg" class="mh-30px mh-lg-40px" alt="" />
					</div>
					<div class="d-flex flex-center m-3 m-md-6" data-bs-toggle="tooltip" title="Vodafone">
						<img src="assets/media/svg/brand-logos/vodafone.svg" class="mh-30px mh-lg-40px" alt="" />
					</div>
					<div class="d-flex flex-center m-3 m-md-6" data-bs-toggle="tooltip" title="KPMG International">
						<img src="assets/media/svg/brand-logos/kpmg.svg" class="mh-30px mh-lg-40px" alt="" />
					</div>
					<div class="d-flex flex-center m-3 m-md-6" data-bs-toggle="tooltip" title="Nasa">
						<img src="assets/media/svg/brand-logos/nasa.svg" class="mh-30px mh-lg-40px" alt="" />
					</div>
					<div class="d-flex flex-center m-3 m-md-6" data-bs-toggle="tooltip" title="Aspnetzero">
						<img src="assets/media/svg/brand-logos/aspnetzero.svg" class="mh-30px mh-lg-40px" alt="" />
					</div>
					<div class="d-flex flex-center m-3 m-md-6" data-bs-toggle="tooltip" title="AON - Empower Results">
						<img src="assets/media/svg/brand-logos/aon.svg" class="mh-30px mh-lg-40px" alt="" />
					</div>
					<div class="d-flex flex-center m-3 m-md-6" data-bs-toggle="tooltip" title="Hewlett-Packard">
						<img src="assets/media/svg/brand-logos/hp-3.svg" class="mh-30px mh-lg-40px" alt="" />
					</div>
					<div class="d-flex flex-center m-3 m-md-6" data-bs-toggle="tooltip" title="Truman">
						<img src="assets/media/svg/brand-logos/truman.svg" class="mh-30px mh-lg-40px" alt="" />
					</div>
				</div>
			</div>
		</div>
		<div class="landing-curve landing-dark-color mb-10 mb-lg-20">
			<svg viewBox="15 12 1470 48" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M0 11C3.93573 11.3356 7.85984 11.6689 11.7725 12H1488.16C1492.1 11.6689 1496.04 11.3356 1500 11V12H1488.16C913.668 60.3476 586.282 60.6117 11.7725 12H0V11Z" fill="currentColor"></path>
			</svg>
		</div>
	</div>

	<div class="mb-n10 mb-lg-n20 z-index-2">
		<!--begin::Container-->
		<div class="container">
			<!--begin::Heading-->
			<div class="text-center mb-17">
				<!--begin::Title-->
				<h3 class="fs-2hx text-gray-900 mb-5" id="how-it-works" data-kt-scroll-offset="{default: 100, lg: 150}">How it Works</h3>
				<!--end::Title-->
				<!--begin::Text-->
				<div class="fs-5 text-muted fw-bold">Save thousands to millions of bucks by using single tool 
				<br />for different amazing and great useful admin</div>
				<!--end::Text-->
			</div>
			<!--end::Heading-->
			<!--begin::Row-->
			<div class="row w-100 gy-10 mb-md-20">
				<!--begin::Col-->
				<div class="col-md-4 px-5">
					<!--begin::Story-->
					<div class="text-center mb-10 mb-md-0">
						<!--begin::Illustration-->
						<img src="assets/media/illustrations/sketchy-1/2.png" class="mh-125px mb-9" alt="" />
						<!--end::Illustration-->
						<!--begin::Heading-->
						<div class="d-flex flex-center mb-5">
							<!--begin::Badge-->
							<span class="badge badge-circle badge-light-success fw-bold p-5 me-3 fs-3">1</span>
							<!--end::Badge-->
							<!--begin::Title-->
							<div class="fs-5 fs-lg-3 fw-bold text-gray-900">Jane Miller</div>
							<!--end::Title-->
						</div>
						<!--end::Heading-->
						<!--begin::Description-->
						<div class="fw-semibold fs-6 fs-lg-4 text-muted">Save thousands to millions of bucks 
						<br />by using single tool for different 
						<br />amazing and great</div>
						<!--end::Description-->
					</div>
					<!--end::Story-->
				</div>
				<!--end::Col-->
				<!--begin::Col-->
				<div class="col-md-4 px-5">
					<!--begin::Story-->
					<div class="text-center mb-10 mb-md-0">
						<!--begin::Illustration-->
						<img src="assets/media/illustrations/sketchy-1/8.png" class="mh-125px mb-9" alt="" />
						<!--end::Illustration-->
						<!--begin::Heading-->
						<div class="d-flex flex-center mb-5">
							<!--begin::Badge-->
							<span class="badge badge-circle badge-light-success fw-bold p-5 me-3 fs-3">2</span>
							<!--end::Badge-->
							<!--begin::Title-->
							<div class="fs-5 fs-lg-3 fw-bold text-gray-900">Setup Your App</div>
							<!--end::Title-->
						</div>
						<!--end::Heading-->
						<!--begin::Description-->
						<div class="fw-semibold fs-6 fs-lg-4 text-muted">Save thousands to millions of bucks 
						<br />by using single tool for different 
						<br />amazing and great</div>
						<!--end::Description-->
					</div>
					<!--end::Story-->
				</div>
				<!--end::Col-->
				<!--begin::Col-->
				<div class="col-md-4 px-5">
					<!--begin::Story-->
					<div class="text-center mb-10 mb-md-0">
						<!--begin::Illustration-->
						<img src="assets/media/illustrations/sketchy-1/12.png" class="mh-125px mb-9" alt="" />
						<!--end::Illustration-->
						<!--begin::Heading-->
						<div class="d-flex flex-center mb-5">
							<!--begin::Badge-->
							<span class="badge badge-circle badge-light-success fw-bold p-5 me-3 fs-3">3</span>
							<!--end::Badge-->
							<!--begin::Title-->
							<div class="fs-5 fs-lg-3 fw-bold text-gray-900">Enjoy Nautica App</div>
							<!--end::Title-->
						</div>
						<!--end::Heading-->
						<!--begin::Description-->
						<div class="fw-semibold fs-6 fs-lg-4 text-muted">Save thousands to millions of bucks 
						<br />by using single tool for different 
						<br />amazing and great</div>
						<!--end::Description-->
					</div>
					<!--end::Story-->
				</div>
				<!--end::Col-->
			</div>
			<!--end::Row-->
		</div>
		<!--end::Container-->
	</div>
	<!--end::How It Works Section-->

	<!--begin::Pricing Section-->
	<div class="mt-sm-n20">
		<div class="landing-curve landing-dark-color">
			<svg viewBox="15 -1 1470 48" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M1 48C4.93573 47.6644 8.85984 47.3311 12.7725 47H1489.16C1493.1 47.3311 1497.04 47.6644 1501 48V47H1489.16C914.668 -1.34764 587.282 -1.61174 12.7725 47H1V48Z" fill="currentColor"></path>
			</svg>
		</div>
		<div class="py-20 landing-dark-bg">
			<div class="container">
				<div class="d-flex flex-column container pt-lg-20">
					<div class="mb-13 text-center">
						<h1 class="fs-2hx fw-bold text-white mb-5" id="pricing" data-kt-scroll-offset="{default: 100, lg: 150}">Clear Pricing Makes it Easy</h1>
						<div class="text-gray-600 fw-semibold fs-5">Save thousands to millions of bucks by using single tool for different 
						<br />amazing and outstanding cool and great useful admin</div>
					</div>
					
					<div class="text-center" id="kt_pricing">
						<!--begin::Nav group-->
						<div class="nav-group landing-dark-bg d-inline-flex mb-15" data-kt-buttons="true" style="border: 1px dashed #2B4666;">
							<a href="#" class="btn btn-color-gray-600 btn-active btn-active-success px-6 py-3 me-2 active" data-kt-plan="month">Monthly</a>
							<a href="#" class="btn btn-color-gray-600 btn-active btn-active-success px-6 py-3" data-kt-plan="annual">Annual</a>
						</div>
						<div class="row g-10">
							<div class="col-xl-4">
								<div class="d-flex h-100 align-items-center">
									<div class="w-100 d-flex flex-column flex-center rounded-3 bg-body py-15 px-10">
										<div class="mb-7 text-center">
											<h1 class="text-gray-900 mb-5 fw-boldest">Startup</h1>
											<div class="text-gray-500 fw-semibold mb-5">Best Settings for Startups</div>
											<div class="text-center">
												<span class="mb-2 text-primary">$</span>
												<span class="fs-3x fw-bold text-primary" data-kt-plan-price-month="99" data-kt-plan-price-annual="999">99</span>
												<span class="fs-7 fw-semibold opacity-50" data-kt-plan-price-month="/ Mon" data-kt-plan-price-annual="/ Ann">/ Mon</span>
											</div>
										</div>
										<div class="w-100 mb-10">
											<!--begin::Item-->
											<div class="d-flex flex-stack mb-5">
												<span class="fw-semibold fs-6 text-gray-800 text-start pe-3">Up to 10 Active Users</span>
												<i class="ki-duotone ki-check-circle fs-1 text-success">
													<span class="path1"></span>
													<span class="path2"></span>
												</i>
											</div>
											<div class="d-flex flex-stack mb-5">
												<span class="fw-semibold fs-6 text-gray-800 text-start pe-3">Up to 30 Project Integrations</span>
												<i class="ki-duotone ki-check-circle fs-1 text-success">
													<span class="path1"></span>
													<span class="path2"></span>
												</i>
											</div>
											<div class="d-flex flex-stack mb-5">
												<span class="fw-semibold fs-6 text-gray-800">Keen Analytics Platform</span>
												<i class="ki-duotone ki-cross-circle fs-1">
													<span class="path1"></span>
													<span class="path2"></span>
												</i>
											</div>
											<div class="d-flex flex-stack mb-5">
												<span class="fw-semibold fs-6 text-gray-800">Targets Timelines & Files</span>
												<i class="ki-duotone ki-cross-circle fs-1">
													<span class="path1"></span>
													<span class="path2"></span>
												</i>
											</div>
											<div class="d-flex flex-stack">
												<span class="fw-semibold fs-6 text-gray-800">Unlimited Projects</span>
												<i class="ki-duotone ki-cross-circle fs-1">
													<span class="path1"></span>
													<span class="path2"></span>
												</i>
											</div>
										</div>
										<a href="#" class="btn btn-primary">Select</a>
									</div>
								</div>
							</div>
							<div class="col-xl-4">
								<div class="d-flex h-100 align-items-center">
									<div class="w-100 d-flex flex-column flex-center rounded-3 bg-primary py-20 px-10">
										<div class="mb-7 text-center">
											<h1 class="text-white mb-5 fw-boldest">Business</h1>
											<div class="text-white opacity-75 fw-semibold mb-5">Best Settings for Business</div>
											<div class="text-center">
												<span class="mb-2 text-white">$</span>
												<span class="fs-3x fw-bold text-white" data-kt-plan-price-month="199" data-kt-plan-price-annual="1999">199</span>
												<span class="fs-7 fw-semibold text-white opacity-75" data-kt-plan-price-month="/ Mon" data-kt-plan-price-annual="/ Ann">/ Mon</span>
											</div>
										</div>
										<div class="w-100 mb-10">
											<div class="d-flex flex-stack mb-5">
												<span class="fw-semibold fs-6 text-white opacity-75 text-start pe-3">Up to 10 Active Users</span>
												<i class="ki-duotone ki-check-circle fs-1 text-white">
													<span class="path1"></span>
													<span class="path2"></span>
												</i>
											</div>
											<div class="d-flex flex-stack mb-5">
												<span class="fw-semibold fs-6 text-white opacity-75 text-start pe-3">Up to 30 Project Integrations</span>
												<i class="ki-duotone ki-check-circle fs-1 text-white">
													<span class="path1"></span>
													<span class="path2"></span>
												</i>
											</div>
											<div class="d-flex flex-stack mb-5">
												<span class="fw-semibold fs-6 text-white opacity-75 text-start pe-3">Keen Analytics Platform</span>
												<i class="ki-duotone ki-check-circle fs-1 text-white">
													<span class="path1"></span>
													<span class="path2"></span>
												</i>
											</div>
											<div class="d-flex flex-stack mb-5">
												<span class="fw-semibold fs-6 text-white opacity-75 text-start pe-3">Targets Timelines & Files</span>
												<i class="ki-duotone ki-check-circle fs-1 text-white">
													<span class="path1"></span>
													<span class="path2"></span>
												</i>
											</div>
											<div class="d-flex flex-stack">
												<span class="fw-semibold fs-6 text-white opacity-75">Unlimited Projects</span>
												<i class="ki-duotone ki-cross-circle fs-1 text-white">
													<span class="path1"></span>
													<span class="path2"></span>
												</i>
											</div>
										</div>
										<a href="#" class="btn btn-color-primary btn-active-light-primary btn-light">Select</a>
									</div>
								</div>
							</div>
							<div class="col-xl-4">
								<div class="d-flex h-100 align-items-center">
									<div class="w-100 d-flex flex-column flex-center rounded-3 bg-body py-15 px-10">
										<div class="mb-7 text-center">
											<h1 class="text-gray-900 mb-5 fw-boldest">Enterprise</h1>
											<div class="text-gray-500 fw-semibold mb-5">Best Settings for Enterprise</div>
											<div class="text-center">
												<span class="mb-2 text-primary">$</span>
												<span class="fs-3x fw-bold text-primary" data-kt-plan-price-month="999" data-kt-plan-price-annual="9999">999</span>
												<span class="fs-7 fw-semibold opacity-50" data-kt-plan-price-month="/ Mon" data-kt-plan-price-annual="/ Ann">/ Mon</span>
											</div>
										</div>
										<div class="w-100 mb-10">
											<div class="d-flex flex-stack mb-5">
												<span class="fw-semibold fs-6 text-gray-800 text-start pe-3">Up to 10 Active Users</span>
												<i class="ki-duotone ki-check-circle fs-1 text-success">
													<span class="path1"></span>
													<span class="path2"></span>
												</i>
											</div>
											<div class="d-flex flex-stack mb-5">
												<span class="fw-semibold fs-6 text-gray-800 text-start pe-3">Up to 30 Project Integrations</span>
												<i class="ki-duotone ki-check-circle fs-1 text-success">
													<span class="path1"></span>
													<span class="path2"></span>
												</i>
											</div>
											<div class="d-flex flex-stack mb-5">
												<span class="fw-semibold fs-6 text-gray-800 text-start pe-3">Keen Analytics Platform</span>
												<i class="ki-duotone ki-check-circle fs-1 text-success">
													<span class="path1"></span>
													<span class="path2"></span>
												</i>
											</div>
											<div class="d-flex flex-stack mb-5">
												<span class="fw-semibold fs-6 text-gray-800 text-start pe-3">Targets Timelines & Files</span>
												<i class="ki-duotone ki-check-circle fs-1 text-success">
													<span class="path1"></span>
													<span class="path2"></span>
												</i>
											</div>
											<div class="d-flex flex-stack">
												<span class="fw-semibold fs-6 text-gray-800 text-start pe-3">Unlimited Projects</span>
												<i class="ki-duotone ki-check-circle fs-1 text-success">
													<span class="path1"></span>
													<span class="path2"></span>
												</i>
											</div>
										</div>
										<a href="#" class="btn btn-primary">Select</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="landing-curve landing-dark-color">
			<svg viewBox="15 12 1470 48" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M0 11C3.93573 11.3356 7.85984 11.6689 11.7725 12H1488.16C1492.1 11.6689 1496.04 11.3356 1500 11V12H1488.16C913.668 60.3476 586.282 60.6117 11.7725 12H0V11Z" fill="currentColor"></path>
			</svg>
		</div>
	</div>
	<!--end::Pricing Section-->

	<!--begin::Footer Section-->
	<div class="mb-0">
		<div class="landing-curve landing-dark-color">
			<svg viewBox="15 -1 1470 48" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M1 48C4.93573 47.6644 8.85984 47.3311 12.7725 47H1489.16C1493.1 47.3311 1497.04 47.6644 1501 48V47H1489.16C914.668 -1.34764 587.282 -1.61174 12.7725 47H1V48Z" fill="currentColor"></path>
			</svg>
		</div>
		<div class="landing-dark-bg pt-20">
			<div class="container">
				<div class="row py-10 py-lg-20">
					<div class="col-lg-6 pe-lg-16 mb-10 mb-lg-0">
						<div class="rounded landing-dark-border p-9 mb-10">
							<h2 class="text-white">Would you need a Custom License?</h2>
							<span class="fw-normal fs-4 text-gray-700">Email us to 
							<a href="https://keenthemes.com/support" class="text-white opacity-50 text-hover-primary">support@keenthemes.com</a></span>
						</div>
						<div class="rounded landing-dark-border p-9">
							<!--begin::Title-->
							<h2 class="text-white">How About a Custom Project?</h2>
							<span class="fw-normal fs-4 text-gray-700">Use Our Custom Development Service. 
							<a href="pages/user-profile/overview.html" class="text-white opacity-50 text-hover-primary">Click to Get a Quote</a></span>
						</div>
					</div>
					<div class="col-lg-6 ps-lg-16">
						<div class="d-flex justify-content-center">
							<div class="d-flex fw-semibold flex-column me-20">
								<h4 class="fw-bold text-gray-500 mb-6">More for Metronic</h4>
								<a href="https://keenthemes.com/faqs" class="text-white opacity-50 text-hover-primary fs-5 mb-6">FAQ</a>
								<a href="https://preview.keenthemes.com/html/metronic/docs" class="text-white opacity-50 text-hover-primary fs-5 mb-6">Documentaions</a>
								<a href="https://www.youtube.com/c/KeenThemesTuts/videos" class="text-white opacity-50 text-hover-primary fs-5 mb-6">Video Tuts</a>
								<a href="https://preview.keenthemes.com/html/metronic/docs/getting-started/changelog" class="text-white opacity-50 text-hover-primary fs-5 mb-6">Changelog</a>
								<a href="https://devs.keenthemes.com/" class="text-white opacity-50 text-hover-primary fs-5 mb-6">Support Forum</a>
								<a href="https://keenthemes.com/blog" class="text-white opacity-50 text-hover-primary fs-5">Blog</a>
							</div>
							<div class="d-flex fw-semibold flex-column ms-lg-20">
								<h4 class="fw-bold text-gray-500 mb-6">Stay Connected</h4>
								<a href="https://www.facebook.com/keenthemes" class="mb-6">
									<img src="assets/media/svg/brand-logos/facebook-4.svg" class="h-20px me-2" alt="" />
									<span class="text-white opacity-50 text-hover-primary fs-5 mb-6">Facebook</span>
								</a>
								<a href="https://github.com/KeenthemesHub" class="mb-6">
									<img src="assets/media/svg/brand-logos/github.svg" class="h-20px me-2" alt="" />
									<span class="text-white opacity-50 text-hover-primary fs-5 mb-6">Github</span>
								</a>
								<a href="https://twitter.com/keenthemes" class="mb-6">
									<img src="assets/media/svg/brand-logos/twitter.svg" class="h-20px me-2" alt="" />
									<span class="text-white opacity-50 text-hover-primary fs-5 mb-6">Twitter</span>
								</a>
								<a href="https://dribbble.com/keenthemes" class="mb-6">
									<img src="assets/media/svg/brand-logos/dribbble-icon-1.svg" class="h-20px me-2" alt="" />
									<span class="text-white opacity-50 text-hover-primary fs-5 mb-6">Dribbble</span>
								</a>
								<a href="https://www.instagram.com/keenthemes" class="mb-6">
									<img src="assets/media/svg/brand-logos/instagram-2-1.svg" class="h-20px me-2" alt="" />
									<span class="text-white opacity-50 text-hover-primary fs-5 mb-6">Instagram</span>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="landing-dark-separator"></div>
			<div class="container">
				<div class="d-flex flex-column flex-md-row flex-stack py-7 py-lg-10">
					<div class="d-flex align-items-center order-2 order-md-1">
						<a href="landing.html">
							<img alt="Logo" src="assets/media/logos/landing.svg" class="h-15px h-md-20px" />
						</a>
						<span class="mx-5 fs-6 fw-semibold text-gray-600 pt-1" href="https://keenthemes.com">&copy; 2024 Keenthemes Inc.</span>
					</div>
					<ul class="menu menu-gray-600 menu-hover-primary fw-semibold fs-6 fs-md-5 order-1 mb-5 mb-md-0">
						<li class="menu-item">
							<a href="https://keenthemes.com" target="_blank" class="menu-link px-2">About</a>
						</li>
						<li class="menu-item mx-5">
							<a href="https://devs.keenthemes.com" target="_blank" class="menu-link px-2">Support</a>
						</li>
						<li class="menu-item">
							<a href="" target="_blank" class="menu-link px-2">Purchase</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<!--end::Footer Section-->

	<!--begin::Scrolltop-->
	<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
		<i class="ki-duotone ki-arrow-up">
			<span class="path1"></span>
			<span class="path2"></span>
		</i>
	</div>
	<!--end::Scrolltop-->
@endsection

        