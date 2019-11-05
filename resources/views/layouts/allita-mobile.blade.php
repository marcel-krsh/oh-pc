<?php session(['disablePacer' => 1]);?>
<?php setlocale(LC_MONETARY, 'en_US');
/// protect against inactive users.
$allowPageLoad = false;

if (Auth::check()) {
  if (Auth::user()->active == 1) {
    $allowPageLoad = true;
  }
} else {
  /// user is not logged in -- the auth middleware will protect against that access.
  $allowPageLoad = true;
}
if ($allowPageLoad) {
  ?>
	<!DOCTYPE html>
	<html lang="en" dir="ltr" id="parentHTML" class="no-js">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<title>
			@can('access_auditor')
			Allita Program Compliance
			@else
			Dev|Co Inspection
			@endCan
		</title>

		<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
		<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
		<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
		<link rel="manifest" href="/manifest.json">
		<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">

		<meta name="theme-color" content="#ffffff">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css{{ asset_version() }}" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

		@if (Auth::guest())
		@else
		<link rel="stylesheet" href="/css/documents-tab.css{{ asset_version() }}">
	<!-- <link rel="stylesheet" href="/css/cdfs-tab.css{{ asset_version() }}">

	<link rel="stylesheet" href="/css/documents-tab.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/funding-tab.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/history-tab.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/notes-tab.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/outcomes-tab.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/processing-tab.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/handsontable.full.min.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/components/slideshow.css{{ asset_version() }}"> -->
	<link rel="stylesheet" href="/css/communications-tab.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/auto-complete.css{{ asset_version() }}">

	@endif

	<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css{{ asset_version() }}"> -->
	@if(session('disablePacer')==1)
	<style type="text/css">
		body:not(.pace-done) > :not(.pace),body:not(.pace-done):before,body:not(.pace-done):after {
			opacity:1 !important;
		}
		html{
			background-color: black !important;
			font-size:20px;
		}
		/*Universal header styles*/

		body,h1,h2,h3,h4,h5,p,ul,li,a {
			color:white !important;
		}
		p,div{
			font-size: 20px !important;
		}
		h3,h4,h5 {
			font-size: 23px !important;
		}
		h2 {
			font-size: 25px !important;
		}
		h1 {
			font-size:27px !important;
		}
		.small-h3{
			font-size:14px !important;
		}
		.uk-modal{
			background-color: darkgray;
			width: 99%;
			border: 1px solid white;
		}
		#modal-size,#modal-size-2,#modal-size-3{
			width:92% !important;
			padding:20px !important;
			background-color: black;
			
			border: 1px solid white;
			margin-left: calc(.7% )!important;
			height: 98%!important;
    		max-height: 98%!important;
		}
		.uk-modal-header{
			    margin-bottom: 15px;
			    margin: -20px -20px 15px -20px;
			    padding: 20px;
			    border-bottom: 1px solid #dddddd;
			    border-radius: 4px 4px 0 0;
			    background: #000000 !important;
		}
		.uk-modal-title{
			    font-size: 15px !important;
   				line-height: 36px !important;
		}
		.uk-modal-title h4{
			line-height: 0px !important;
		    margin-top: 0px !important;
		    margin-left: 35px !important;
		    position: relative;
		    top: -19px;
		}
		.uk-close{
			color: lightgray !important;
		}
		.uk-button {
			background: white;
    		color: black !important;
		}
		.uk-button-success{
			background-color: #73FF6B !important;
		}
		#apcsv-logo {
			position: relative; top: -9px; padding-left: 10px; display: inline-block;
		}
		@media only screen and (max-width: 1310px) {
			#apcsv-logo {
				display: none;
			}
		}

		#apcsv-list-left {
			float: left;
			display: inline-block;
			margin-top: 3px;
		}
		#apcsv-avatar {
			float: right;
			margin-right: 25px;
			border-radius: 15px;
			width: 20px;
			height: 20px;
			padding: 5px;
			font-size: 14px;
			background: #123458;
			text-align: center;
			-webkit-box-shadow: 0px 4px 5px 0px rgba(0,0,0,0.22);
			-moz-box-shadow: 0px 4px 5px 0px rgba(0,0,0,0.22);
			box-shadow: 0px 4px 5px 0px rgba(0,0,0,0.22);
			color: white;
		}
		#apcsv-menu-icon {
			float: right;
			position: relative;
			top: 3px;
			text-decoration: none;
			padding: 1px 5px;
			margin-right: 15px;
			border: 1px solid #123458;
		}
		#apcsv-list-right {
			float: right;
			display: inline-block;
			margin-top: 3px;
		}
		.pcsv-toggle {
			color: #123458 ;
			text-decoration: none;
		}
		#apcsv-menu-items.hidden {
			visibility: hidden;
			opacity: 0;
		}
		#apcsv-menu-items {
			display: block;
			position: absolute;
			top: 35px;
			right: -55px;
			width: 200px;
			padding: 15px;
			background: #fbfbfb;
			border-radius: 3px;
			box-shadow: 0px 8px 10px 0px rgba(0,0,0,0.22);
			transition: opacity 600ms, visibility 600ms;
		}
		.apcsv-menu-item:first-of-type {
			border-radius: 6px 6px 0 0;
		}
		.apcsv-menu-item {
			display: block;
			border-bottom: 1px solid lightgray;
			padding-top: 15px;
			padding-bottom: 15px;
			transition: background-color 400ms;
		}
		.apcsv-menu-item:last-of-type {
			border-bottom: none;
			border-radius: 0 0 6px 6px;
		}
		.apcsv-menu-item a {
			color: #123458;
			text-decoration: none;
			display: block;
			text-align: center;
			font-weight: 100;
		}
		#main-tabs {
			padding-top:0px !important;
		}

		#phone {
			height: 100%;
			width: 100%;
			position: absolute;
			top:0;
			left:0;
			background: #000;
		}
		h3 .a-info-circle, h3 .a-circle-up{
			position: relative;
			top:1px;
			margin-right: 4px;
		}
		a.uk-button i.a-phone-talk {
			position: relative;
		    font-size: 19px;
		    top: 3px;
			
		}
		a.uk-button i.a-comment {
			position: relative;
		    font-size: 19px;
		    top: 3px;
			
		}
		a.uk-button i.a-envelope-4 {
			position: relative;
		    font-size: 20px;
		    top: 3px;
			
		}
		.dimmer{
			color: #d4d2d2;
		    font-size: 10px;
		    font-weight: bold;
		    vertical-align: bottom;
			transform: rotate(-90deg);


			  /* Legacy vendor prefixes that you probably don't need... */

			  /* Safari */
				-webkit-transform: rotate(-90deg);

			  /* Firefox */
			  -moz-transform: rotate(-90deg);

			  /* IE */
			  -ms-transform: rotate(-90deg);

			  /* Opera */
			  -o-transform: rotate(-90deg);
			  display: inline-table;
		}
		#apcsv-avatar {
		    float: left;
		    margin-right: 25px;
		    border-radius: 20px;
		    width: 30px;
		    height: 30px;
		    padding: 5px;
		    font-size: 20px;
		    background: #d4e9ff;
		    text-align: center;
		    -webkit-box-shadow: 0px 4px 5px 0px rgba(0,0,0,0.22);
		    -moz-box-shadow: 0px 4px 5px 0px rgba(0,0,0,0.22);
		    box-shadow: 0px 4px 5px 0px rgba(255, 255, 255, 0.22);
		    color: black;
		    line-height: 28px;
		}
		.audit-info-mobile li {
			        margin-bottom: 13px;
				    border-bottom: 1px dotted #737373;
				    padding-bottom: 18px;
		}
		.findings-details-mobile i.a-booboo, .findings-details-mobile i.a-skull {
			font-size: 21px;
		    position: relative;
		    top: 3px;
		    margin-right: 3px;
		}
		.finding-breakdown{
			width: 84px;
			font-size: 15px;
			display: inline-table;;
		}
		.finding-breakdown-stat{
			width: 80px;
			display: inline-table;;
		}
		.close-long-box {
			    background-color: #9a9a9a;
			    color: black;
			    width: 96%;
			    text-align: center;
			    padding: 5px;
			    font-size: 14px;
			    font-weight: bold;
			    margin-top: 13px;
		}

	</style>
	<?php /* session(['disablePacer'=>0]); */?>
	@endif
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css{{ asset_version() }}">
	<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
	<script>
		function openUserPreferences(){
			dynamicModalLoad('auditors/{{Auth::user()->id}}/preferences',0,0,1);
		}
		window.Laravel = <?php echo json_encode([
    'csrfToken' => csrf_token(),
  ]); ?>
		</script>

		<script src="/js/jquery.js{{ asset_version() }}"></script>
		<script src="/js/uikit.js{{ asset_version() }}"></script>
		<script src="/js/uikit-icons.min.js{{ asset_version() }}"></script>
		<script src="/js/handsontable.full.min.js{{ asset_version() }}"></script>

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/multiple-select/1.2.0/multiple-select.min.css{{ asset_version() }}" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/multiple-select/1.2.0/multiple-select.min.js{{ asset_version() }}"></script>

		
		<script src="{{ mix('js/app.js') }}"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
		{{-- <script src="/HemantNegi-jquery.sumoselect-a1d8d68/jquery.sumoselect.min.js"><script> --}}

		<script>
		// $('select').multipleSelect();
	</script>

	@if (Auth::guest())
	@else
	<script src="/js/taffy.js{{ asset_version() }}"></script>
	<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js{{ asset_version() }}"></script> -->
	<script type="text/javascript" src="/js/Chart.bundle.js{{ asset_version() }}"></script>
	@endif

	<link rel="stylesheet" href="/css/allita-font.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/uikit.min.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/allita-admin-419171046.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/system-419171130.css{{ asset_version() }}">

	@yield('head')

	<style>
		[v-cloak] {
			display: none;
		}
		.uk-notification {z-index: 1060;}
	</style>

	

</head>
<body >
	<a name="top"></a>
	<!-- MOBILE -->
	<div id="phone" class="uk-visible-touch ">
		<div id="phone-app" class="uk-container uk-padding-small uk-align-center" >
			<div class="uk-padding-small" style="background-color:#3c3c3c; margin-bottom: 20px; z-index: 980;" uk-sticky="width-element: #phone; show-on-up: true">
				<a class="uk-contrast" uk-toggle="target: #offcanvas-phone"><h3 style="margin-bottom: 0px"><i class="a-menu uk-text-muted uk-contrast" style="color:white !important; font-weight: bolder; margin-right:5px; font-size: 20px;position: relative; top: 2px;"></i> @yield('header')</h3></a>
			</div>
			<div class="uk-container">

				<div id="mobile-content" class="" uk-grid style="height:100%; padding:3px !important" >
					@yield('content')
				</div>
				<script type="text/javascript">
					isMobile = function(){
						var isMobile = window.matchMedia("only screen and (max-width: 640px)");
						return isMobile.matches ? true : false
					}
					if(isMobile){
						
					}
				</script>
			</div>
		</div>
	</div>

	<div id="offcanvas-phone" uk-offcanvas="overlay: true">
		<div class="uk-offcanvas-bar" style="background-color: #0d0d23">

			<button class="uk-offcanvas-close" type="button" uk-close></button>


			<div id="apcsv-avatar" class="" title="{{Auth::user()->full_name()}} - User ID:{{Auth::user()->id}} @if(Auth::user()->root_access()) Root Access @elseIf(Auth::user()->admin_access()) Admin Access @elseIf(Auth::user()->auditor_access()) Auditor Access @elseIf(Auth::user()->pm_access()) Property Manager @endIf" onclick="openUserPreferences();" style="cursor: pointer; margin-top:15px">
								{{Auth::user()->initials()}}
							</div>
							@yield('side_bar_links')

			

		</div>
	</div>

	<a id="smoothscrollLink" href="#top" uk-scroll="{offset: 90}"></a>
	<div id="dynamic-modal" uk-modal uk-overflow-auto>
		<div id="modal-size" class="uk-modal-dialog uk-modal-body uk-modal-content">
			<a class="uk-modal-close-default" uk-close></a>
			<div id="dynamic-modal-content" style="height: 100%; overflow-y: scroll;"></div>
		</div>
	</div>
	<div id="dynamic-modal-2" uk-modal uk-overflow-auto>
		<div id="modal-size-2" class="uk-modal-dialog uk-modal-body uk-modal-content">
			<a class="uk-modal-close-default" uk-close></a>
			<div id="dynamic-modal-content-2" style="height: 100%; overflow-y: scroll;"></div>
		</div>
	</div>
	<div id="dynamic-modal-3" uk-modal uk-overflow-auto>
		<div id="modal-size-3" class="uk-modal-dialog uk-modal-body uk-modal-content">
			<a class="uk-modal-close-default" uk-close></a>
			<div id="dynamic-modal-content-3" style="height: 100%; overflow-y: scroll;"></div>
		</div>
	</div>




	<script>
		flatpickr.defaultConfig.animate = window.navigator.userAgent.indexOf('MSIE') === -1;
		flatpickr(".flatpickr");

		var configs = {
			dateformat: {
				dateFormat: "m/d/Y",
			}
		}



		$(".uk-modal").on("hide", function() {
			$("html").removeClass("uk-modal-page");
		});



	</script>


		<!-- <script src="/js/app.js{{ asset_version() }}"></script> -->
		@if (Auth::guest())
		@else
		<script type="text/javascript">
		// $('.list-tab').slideToggle();
		//$('#detail-tab-1').slideToggle();
		//$('#detail-tab-2').slideToggle();
		//$('#detail-tab-3').slideToggle();
		//$('#detail-tab-4').slideToggle();
		//$('#detail-tab-5').slideToggle();

	</script>
	<script src="/js/auto-complete.js{{ asset_version() }}"></script>
	<script type="text/javascript" src="/js/systems/system.js{{ asset_version() }}"></script>
	<script type="text/javascript" src="/js/systems/audits.js{{ asset_version() }}"></script>
	<script type="text/javascript" src="/js/systems/findings.js{{ asset_version() }}"></script>
	<script type="text/javascript" src="/js/systems/communications.js{{ asset_version() }}"></script>


	<script type="text/javascript" src="/js/systems/documents-tab.js{{ asset_version() }}"></script>
	<!-- <script type="text/javascript" src="/js/systems/cdfs-tab.js{{ asset_version() }}"></script>
	<script type="text/javascript" src="/js/systems/communications-tab.js{{ asset_version() }}"></script>
	<script type="text/javascript" src="/js/systems/documents-tab.js{{ asset_version() }}"></script>
	<script type="text/javascript" src="/js/systems/funding-tab.js{{ asset_version() }}"></script>
	<script type="text/javascript" src="/js/systems/history-tab.js{{ asset_version() }}"></script>
	<script type="text/javascript" src="/js/systems/notes-tab.js{{ asset_version() }}"></script>
	<script type="text/javascript" src="/js/systems/outcomes-tab.js{{ asset_version() }}"></script> -->
	<script type="text/javascript" src="/js/systems/processing-tab.js{{ asset_version() }}"></script>
	<script>
		// single script tag
		

	@endif

	@if(session()->has('notification_modal_source'))
	notificationModelSource = "{{ (session()->pull('notification_modal_source', null)) }}";
	setTimeout(function(){
		dynamicModalLoad(notificationModelSource);
	},400);
	@endif

	



		

	</script>

	@if(session('disablePacer') != 1)
	<script>
		window.paceOptions = { ajax: { trackMethods: ['GET', 'POST', 'DELETE', 'PUT', 'PATCH'], ignoreURLs: ['https://pcinspectdev.ohiohome.org'] } }

		// universal header:

		function ToggleMenu() {
			var menu = document.getElementById("apcsv-menu-items");
			if ( menu.classList.contains('hidden') ) {
				menu.classList.remove('hidden');
			} else {
				menu.classList.add('hidden');
			}
		}
	</script>
	<script src="/js/pace.min.js{{ asset_version() }}">{{session('disablePacer')}}</script>
	@endif


</body>
</html>

<?php } else {
  /// show for inactive users ?>

<!DOCTYPE html>
<html lang="en" dir="ltr" id="parentHTML" class="no-js">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Inactive User</title>

	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="/manifest.json">
	<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">

	<meta name="theme-color" content="#ffffff">
	<link rel="stylesheet" href="/css/allita-admin-419171046.css{{ asset_version() }}">
	<link rel="stylesheet" href="/css/system-419171130.css{{ asset_version() }}">

	<script>
		window.Laravel = <?php echo json_encode([
    'csrfToken' => csrf_token(),
  ]); ?>
		</script>
		@if(session('disablePacer') != 1)
		<script data-pace-options='{ "restartOnRequestAfter": false }' src="/js/pace.js{{ asset_version() }}">{{session('disablePacer')}}</script>
		@endif
		<script src="/js/jquery.js{{ asset_version() }}"></script>
		<script src="/js/uikit.min.js{{ asset_version() }}"></script>
	<!-- <script src="/js/components/autocomplete.js{{ asset_version() }}"></script>
	<script src="/js/core/modal.js{{ asset_version() }}"></script>
	<script src="/js/components/lightbox.js{{ asset_version() }}"></script>
	<script src="/js/components/sticky.js{{ asset_version() }}"></script>
	<script src="/js/components/notify.js{{ asset_version() }}"></script>
	<script src="/js/components/tooltip.js{{ asset_version() }}"></script>
	<script src="/js/components/datepicker.js{{ asset_version() }}"></script>
	<script src="/js/components/slideshow.js{{ asset_version() }}"></script>
	<script src="/js/components/slideshow-fx.js{{ asset_version() }}"></script>
	<script src="/js/components/upload.js{{ asset_version() }}"></script>
	<script src="/js/components/lightbox.js{{ asset_version() }}"></script>
	<script src="/js/components/form-select.js{{ asset_version() }}"></script>
	<script src="/js/components/slider.js{{ asset_version() }}"></script>
	<script src="/js/components/slideset.js{{ asset_version() }}"></script>
	<script src="/js/components/accordion.js{{ asset_version() }}"></script>
	<script src="/js/components/notify.js{{ asset_version() }}"></script>
	<script src="/js/components/search.js{{ asset_version() }}"></script>
	<script src="/js/components/timepicker.js{{ asset_version() }}"></script>
	<script src="/js/components/nestable.js{{ asset_version() }}"></script>
	<script src="/js/components/sortable.js{{ asset_version() }}"></script>
	<script src="/js/components/grid.min.js{{ asset_version() }}"></script>
	<script src="/js/handsontable.full.min.js{{ asset_version() }}"></script> -->

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/multiple-select/1.2.0/multiple-select.min.css{{ asset_version() }}" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/multiple-select/1.2.0/multiple-select.min.js{{ asset_version() }}"></script>

	<script>
		$('select').multipleSelect();
	</script>
</head>
<body class="uk-height-1-1">
	<script type="text/javascript">
		UIkit.modal.alert('Sorry your user has been deactivated. You can contact your administrator to reactivate your user and then we can work together again.');
	</script>
	<div class="uk-vertical-align uk-text-center uk-height-1-1">
		<div class="uk-vertical-align-middle uk-margin-top" style="width: 250px;">



			<form class="uk-panel uk-panel-box">
				<div class="uk-form-row">
					<h2 align="center">Inactive User</h2>
					<p align="center">{{Auth::user()->name}}</p>
				</div>

				<div class="uk-form-row">
					<a class="uk-width-1-1 uk-button uk-button-primary uk-button-large" href="/login">Return to Login</a>
				</div>

			</form>

		</div>
	</div>

</body>
</html>
<?php
Auth::logout();
}?>