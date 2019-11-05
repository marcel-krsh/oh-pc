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

  <link rel="stylesheet" href="/css/signature-pad.css{{ asset_version() }}">

  <!--[if IE]>
    <link rel="stylesheet" type="text/css" href="css/ie9.css{{ asset_version() }}">
  <![endif]-->

<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
<link rel="manifest" href="/manifest.json">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">

<meta name="theme-color" content="#ffffff">
<link rel="stylesheet" href="/css/allita-font.css{{ asset_version() }}">
<link rel="stylesheet" href="/css/uikit.min.css{{ asset_version() }}">
<link rel="stylesheet" href="/css/allita-admin-419171046.css{{ asset_version() }}">
<link rel="stylesheet" href="/css/system-419171130.css{{ asset_version() }}">
<link rel="stylesheet" href="/css/auto-complete.css{{ asset_version() }}">

<script src="/js/auto-complete.js{{ asset_version() }}"></script>
@if (Auth::guest())
@else
@endif
<script>
        window.Laravel = <?php echo json_encode([
    'csrfToken' => csrf_token(),
  ]); ?>
    </script>

<script data-pace-options='{ "restartOnRequestAfter": false }' src="/js/pace.js{{ asset_version() }}"></script>
<script src="/js/jquery.js{{ asset_version() }}"></script>
<script src="/js/uikit.js{{ asset_version() }}"></script>
<script src="/js/uikit-icons.min.js{{ asset_version() }}"></script>
<!-- <script src="/js/components/autocomplete.js{{ asset_version() }}"></script>
<script src="/js/core/modal.js{{ asset_version() }}"></script>
<script src="/js/components/lightbox.js{{ asset_version() }}"></script>
<script src="/js/components/sticky.js{{ asset_version() }}"></script>
<script src="/js/components/notify.js{{ asset_version() }}"></script>
<script src="/js/components/tooltip.js{{ asset_version() }}"></script> -->
<!-- <script src="/js/components/datepicker.js{{ asset_version() }}"></script> -->
<!-- <script src="/js/components/slideshow.js{{ asset_version() }}"></script>
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
<script src="/js/components/grid.min.js{{ asset_version() }}"></script> -->
@if (Auth::guest())
@else
<!-- <script src="/js/taffy.js{{ asset_version() }}"></script>
<script> window.continueToLoad = 1; window.saved = 1;
</script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.5/Chart.bundle.js{{ asset_version() }}"></script> -->
@endif
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css{{ asset_version() }}">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@yield('head')
<style>
		#releaseformpanel .uk-panel-box-white {background-color:#ffffff;}
		#releaseformpanel .uk-panel-box .uk-panel-badge {top:-10px;}
		#releaseformpanel .green {color:#82a53d;}
		#releaseformpanel .blue {color:#005186;}

		#releaseformpanel table tfoot tr td {border: none;}
		#releaseformpanel textarea {width:100%;}
		#releaseformpanel .property-summary {margin-top:0;}


		.guidesteps .uk-panel {
			font-size: small;
			opacity: 0.6;
		}
		.guidesteps .uk-panel.active {
			opacity: 1;

		}
		.guidesteps .uk-panel .doubleheight {
			min-height: 40px;
		}
		.guidesteps .uk-panel .uk-panel-title {
			font-size: small;
			background-color: #a4bed3;
			color: #fff;
			padding-top: 4px;
    		padding-bottom: 4px;
		}
		.guidesteps .uk-panel.active .uk-panel-title {
			background-color: #074f8c;
			color: #fff;
		}
		.uk-input{width:inherit;}
		#modal-amenity-form .uk-input {width: 100%}
		</style>
</head>
<body class="simpler">

<a name="top"></a>
<!-- MAIN VIEW -->
<div class="uk-container uk-align-center">
<div class="uk-grid uk-grid-collapse">
<div id="main-window" class="uk-width-1-1 " style="padding-top: 15px;">


		<div id="main-offcanvas" uk-offcanvas="overlay:true; flip:true">
		    <div class="uk-offcanvas-bar">
		    	<ul class="uk-nav uk-nav-default" uk-nav >
					@if (Auth::guest())
    			<li class="list-tab" style="display: none"><a><span class="list-tab-text"> Welcome Back!</span></a></li>

				@else
				<li class="list-tab" onClick="$('#list-tab').trigger('click');$('#main-option-text').html($('.list-tab-text').html());$('#main-option-icon').attr('uk-icon','');
$('#main-option-icon').attr('uk-icon','bars');UIkit.offcanvas.hide();" style="display: none"><a><span class="list-tab-text"><span class="a-home-2"></span>  My Dashboard</span></a></li>

				<li class="detail-tab-1" onClick="$('#detail-tab-1').trigger('click');$('#main-option-text').html($('.detail-tab-1-text').html());UIkit.offcanvas.hide();" style="display: none"><a><span class="detail-tab-1-text"></span></a></li>
				<li class="detail-tab-2" onClick="$('#detail-tab-2').trigger('click');$('#main-option-text').html($('.detail-tab-2-text').html());UIkit.offcanvas.hide();" style="display: none"><a><span class="detail-tab-2-text"></span></a></li>
				<li class="detail-tab-3" onClick="$('#detail-tab-3').trigger('click');$('#main-option-text').html($('.detail-tab-3-text').html());UIkit.offcanvas.hide();" style="display: none"><a><span class="detail-tab-3-text"></span></a></li>
				<li class="detail-tab-4" onClick="$('#detail-tab-4').trigger('click');$('#main-option-text').html($('.detail-tab-4-text').html());UIkit.offcanvas.hide();" style="display: none"><a><span class="detail-tab-4-text"></span></a></li>
				<li class="detail-tab-5" onClick="$('#detail-tab-5').trigger('click');$('#main-option-text').html($('.detail-tab-5-text').html());UIkit.offcanvas.hide();" style="display: none"><a><span class="detail-tab-5-text"></span></a></li>
				@endif
				</ul>
		    </div>
		</div>

					@yield('content')



<div id="filters" uk-offcanvas="overlay:true">
	<div class="uk-offcanvas-bar">
			@if (Auth::guest())
			<p class="uk-margin-left"><a href="{{url('/login')}}" class="uk-dark uk-link-muted uk-light">Please Login</a></p>
			@else
		<?php // Land bank filters ?>
		<div class="uk-visible@s" style="margin-top:12px">
			<div id="user-logged-in ">
				<div id="logged-in-user-badge" class="user-badge-green uk-margin-left" style="font-weight: 200">{{ userInitials(Auth::user()->name) }}</div>
				<div id="logged-in-user-info" class="uk-visible@s" style="font-size: 1rem; line-height: 1.2rem; font-weight: 300"><span style="font-size: .8rem;">Why hello there,</span><br/>{{ Auth::user()->name }}</div>

				<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
			</div>
		</div>
		<hr class="" style="margin-top:30px;clear:both;">


		<ul class="uk-nav uk-nav-default uk-nav-parent-icon" uk-nav="">
			<li><a href="#" onClick="logout()" class="uk-dark uk-link-muted uk-light"><span class="a-locked-2 uk-icon-justify"></span> Logout</a>
			</li>
			<li><a class="uk-dark uk-link-muted uk-light" href="#" onclick="loadListTab('/lists/activity_log')"><span class="a-list uk-icon-justify"></span> View Activity Log</a></li>

			<li class="uk-parent">
				<a href="#"><span class="a-tools-3 uk-icon-justify"></span> Tools</a>
				<ul class="uk-nav uk-nav-sub">
                    <li><a href='/import_parcels',1)">Import Parcels</a></li>
                    <li class="uk-nav-divider"></li>
                    <li><a onclick="loadListTab('/lists/land_banks')">Blight Programs</a></li>
                    <li class="uk-nav-divider"></li>

                    <li><a onclick="loadListTab('/lists/reimbursement_requests')">Reimbursement Requests</a></li>
                    <li class="uk-nav-divider"></li>
                    <li><a onclick="loadListTab('/lists/purchase_orders')">Purchase Orders</a></li>
                    <li class="uk-nav-divider"></li>
                    <li><a onclick="loadListTab('/lists/reimbursement_invoices')">Reimbursement Invoices</a></li>
                    <li class="uk-nav-divider"></li>
                    <li><a onclick="loadListTab('/lists/dispostion_invoices')">Disposition Invoices</a></li>

                    <li class="uk-nav-divider"></li>
                    <li><a onclick="loadListTab('/lists/accounting')">Full Accounting</a></li>
                    <li class="uk-nav-divider"></li>
                    <li><a onclick="loadListTab('lists/users')">User Management</a></li>

                </ul>
             </li>
             <li class="uk-parent">
				<a href="#"><span class="a-parameters-2 uk-icon-justify"></span> Parcel Filters</a>
					<ul class="uk-nav uk-nav-sub">
                        <li id="filter6" class="" ><a href="#" onClick="loadListTab('/lists/parcels/in_process');"><span class="a-list"></span> In Process: <span id="parcels-in-process" class="uk-text-right">XXX</span></a></li>

                        <li id="filter1" class="" ><a href="#" onClick="loadListTab('/lists/parcels/ready_for_signator');"><span class="a-list"></span> Ready for Signature: <span id="parcels-ready-for-signator" class="uk-text-right">XXX</span></a></li>

                        <li id="filter1" class="" ><a href="#" onClick="loadListTab('/lists/parcels/ready_for_submission');"><span class="a-list"></span> Ready for Submission: <span id="parcels-ready-for-submission" class="uk-text-right">XXX</span></a></li>

                        <li id="filter2" class="" ><a href="#" onClick="loadListTab('/lists/parcels/requested_reimbursement');"><span class="a-list"></span> Requested Reimbursement: <span id="parcels-requested-reimbursement" class="uk-text-right">XXX</span></a></li>

                        <li id="filter2" class="" ><a href="#" onClick="loadListTab('/lists/parcels/reimbursement_denied');"><span class="a-list"></span> Corrections Requested: <span id="parcels-reimbursement-denied" class="uk-text-right">XXX</span></a></li>

                        <li id="filter2" class="" ><a href="#" onClick="loadListTab('/lists/parcels/reimbursement_approved');"><span class="a-list"></span> Reimbursement Approved: <span id="parcels-reimbursement-approved" class="uk-text-right">XXX</span></a></li>

                        <li id="filter3" class="" ><a href="#" onClick="loadListTab('/lists/parcels/invoiced');"><span class="a-list"></span> Invoiced: <span id="parcels-invoiced" class="uk-text-right">XXX</span></a></li>

                        <li id="filter4" class="" ><a href="#" onClick="loadListTab('/lists/parcels/paid');"><span class="a-list"></span> Paid: <span id="parcels-paid" class="uk-text-right">XXX</span></a></li>

                        <li id="filter5" class="" ><a href="#" onClick="loadListTab('/lists/parcels/disposition_requested');"><span class="a-list"></span> Disposition Requested: <span id="parcels-disposition-requested" class="uk-text-right">XXX</span></a></li>

                        <li id="filter6" class="" ><a href="#" onClick="loadListTab('/lists/parcels/dipostion_approved');"><span class="a-list"></span> Dispostion Approved: <span id="parcels-disposition-approved" class="uk-text-right">XXX</span></a></li>

                        <li id="filter6" class="" ><a href="#" onClick="loadListTab('/lists/parcels/disposition_released');"><span class="a-list"></span> Dispostion Released: <span id="parcels-diposition-released" class="uk-text-right">XXX</span></a></li>
					</ul>
                 </li>
	        	<?php // HFA filters ?>
				<li class="uk-parent">
					<a href="#"><span class="a-parameters-2 uk-icon-justify"></span> HFA Parcel Filters</a>
					<ul class="uk-nav uk-nav-sub">
	                                        <li id="filter6" class="" ><a href="#" onClick="loadListTab('/lists/parcels/in_process');"><span class="a-list"></span> Compliance Review: <span id="parcels-compliance" class="uk-text-right">XXX</span></a></li>

	                                        <li id="filter1" class="" ><a href="#" onClick="loadListTab('/lists/parcels/ready_for_signator');"><span class="a-list"></span> Ready for Singators: <span id="parcels-ready-for-signators" class="uk-text-right">XXX</span></a></li>


	                                        <li id="filter2" class="" ><a href="#" onClick="loadListTab('/lists/parcels/reimbursement_denied');"><span class="a-list"></span> Reimbursement Denied: <span id="parcels-reimbursement-denied" class="uk-text-right">XXX</span></a></li>

	                                        <li id="filter2" class="" ><a href="#" onClick="loadListTab('/lists/parcels/reimbursement_approved');"><span class="a-list"></span> Reimbursement Approved: <span id="parcels-reimbursement-approved" class="uk-text-right">XXX</span></a></li>

	                                        <li id="filter3" class="" ><a href="#" onClick="loadListTab('/lists/parcels/invoice_received');"><span class="a-list"></span> Invoice Received: <span id="parcels-invoice-received" class="uk-text-right">XXX</span></a></li>

	                                        <li id="filter4" class="" ><a href="#" onClick="loadListTab('/lists/parcels/paid');"><span class="a-list"></span> Paid: <span id="parcels-paid" class="uk-text-right">XXX</span></a></li>

	                                        <li id="filter5" class="" ><a href="#" onClick="loadListTab('/lists/parcels/disposition_requested');"><span class="a-list"></span> Disposition Requested: <span id="parcels-disposition-requested" class="uk-text-right">XXX</span></a></li>

	                                        <li id="filter6" class="" ><a href="#" onClick="loadListTab('/lists/parcels/dipostion_approved');"><span class="a-list"></span> Dispostion Approved: <span id="parcels-disposition-approved" class="uk-text-right">XXX</span></a></li>

	                                        <li id="filter6" class="" ><a href="#" onClick="loadListTab('/lists/parcels/dipostion_invoiced');"><span class="a-list"></span> Dispostion Invoiced: <span id="parcels-disposition-invoiced" class="uk-text-right">XXX</span></a></li>

	                                        <li id="filter6" class="" ><a href="#" onClick="loadListTab('/lists/parcels/disposition_released');"><span class="a-list"></span> Dispostion Released: <span id="parcels-diposition-released" class="uk-text-right">XXX</span></a></li>


	                </ul>
	            </li>
	    	</ul>
         @endif
	</div>
</div>


<script src="/js/app.js{{ asset_version() }}"></script>
@if (Auth::guest())
@else

<script type="text/javascript" src="/js/systems/system.js{{ asset_version() }}"></script><!--
<script type="text/javascript" src="/js/systems/cdfs-tab.js{{ asset_version() }}"></script>
<script type="text/javascript" src="/js/systems/communications-tab.js{{ asset_version() }}"></script>
<script type="text/javascript" src="/js/systems/documents-tab.js{{ asset_version() }}"></script>
<script type="text/javascript" src="/js/systems/funding-tab.js{{ asset_version() }}"></script>
<script type="text/javascript" src="/js/systems/history-tab.js{{ asset_version() }}"></script>
<script type="text/javascript" src="/js/systems/notes-tab.js{{ asset_version() }}"></script>
<script type="text/javascript" src="/js/systems/outcomes-tab.js{{ asset_version() }}"></script>
<script type="text/javascript" src="/js/systems/processing-tab.js{{ asset_version() }}"></script> -->

@endif

<a id="smoothscrollLink" href="#top" uk-scroll="{offset: 90}"></a>
	<div id="dynamic-modal" uk-modal>
		<div  class="uk-modal-dialog uk-modal-body uk-modal-content">
			<a class="uk-modal-close-default" uk-close></a>
			<div id="dynamic-modal-content" class="uk-modal-body" uk-overflow-auto></div>
		</div>
		<script>
			 $(function() {
			    // Handler for .ready() called.
			    $('#dynamic-modal-content').css({"min-height":"500px","max-height":"800px"});
			  });
		</script>
	</div>

</div>
</div>

</div>
<div class="uk-width-1-1 uk-margin-large-bottom"><p class="uk-text-center uk-dark uk-text-small uk-light">Powered by <a href="http://allita.org" target="_blank" class="uk-link-muted uk-dark uk-light">Allita</a> for <a href="http://ohiohome.org" class="uk-link-muted uk-dark uk-light" target="_blank">Ohio Housing Finance Agency</a> &copy;2016 All Rights Reserved.</p></div>
</div>
<script>
	flatpickr.defaultConfig.animate = window.navigator.userAgent.indexOf('MSIE') === -1;
	flatpickr(".flatpickr");

	var configs = {
	    dateformat: {
	        dateFormat: "m/d/Y",
	    }
	}
</script>

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
<script src="/js/uikit.js{{ asset_version() }}"></script>
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