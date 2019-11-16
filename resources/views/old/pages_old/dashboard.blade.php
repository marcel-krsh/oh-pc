@extends('layouts.allita')

@section('content')
<script>
// Update Tab Title - note the refresh button in tab text script.
$('#list-tab-text').html(' Dashboard');
@if(Auth::user()->canViewSiteVisits())
$('.detail-tab-2-text').html(' Site Visit Manager');
$('#detail-tab-2').delay(2).fadeIn();
@endIf
$('#list-tab-icon').attr('uk-icon','dashboard');
// 
$('#main-option-icon').attr('uk-icon','bars');
</script>

<nav class="uk-navbar-container" uk-navbar>
	<div class="uk-navbar-left">
		<ul class="uk-navbar-nav uk-visible@m" uk-switcher uk-scrollspy="target:li;cls:uk-animation-fade; delay: 300" >
			<li  class="uk-visible@m" id="dash-subtab-10" onClick="loadDashBoardSubTab('dashboard','communications');" style="margin-left: 10px;" ><a><span>Comms</span></a></li>
			<li class="uk-visible@m" id="dash-subtab-1" onClick="loadDashBoardSubTab('dashboard','stats');"  ><a><span>Stats</span></a></li>		
			<li  class="uk-visible@m" id="dash-subtab-4" onClick="loadDashBoardSubTab('dashboard','request_list');"  ><a><span>Reqs</span></a></li><li  class="uk-visible@m" id="dash-subtab-3" onClick="loadDashBoardSubTab('dashboard','po_list');"><a><span>POs</span></a></li>
			<li  class="uk-visible@m" id="dash-subtab-2" onClick="loadDashBoardSubTab('dashboard','invoice_list');"  ><a><span>Invoices</span></a></li>
			<li  class="uk-visible@m" id="dash-subtab-8" onClick="loadDashBoardSubTab('dashboard','disposition_invoice_list');" ><a><span>Disposition Inv</span></a></li>
			<li  class="uk-visible@m" id="dash-subtab-11" onClick="loadDashBoardSubTab('dashboard','recapture_invoice_list');" ><a><span>Recapture Inv</span></a></li>
			<li  class="uk-visible@m" id="dash-subtab-5" onClick="loadDashBoardSubTab('dashboard','parcel_list');"  ><a><span>Parcels</span></a></li>
			<li  class="uk-visible@m" id="dash-subtab-6" onClick="loadDashBoardSubTab('dashboard','accounting');"  ><a><span>Accounting</span></a></li>
			@if(Auth::user()->canManageUsers())<li  class="uk-visible@m" id="dash-subtab-7" onClick="loadDashBoardSubTab('dashboard','user_list');"  ><a><span>Users</span></a></li>@endIf
			@if(Auth::user()->entity_type == 'hfa')<li  class="uk-visible@m" id="dash-subtab-9" onClick="loadDashBoardSubTab('dashboard','admin_tools');"  ><a><span>Tools</span></a></li>@endif
			
			<?php /*
			<li  class="uk-hidden@s" id="dash-subtab-9" onClick="loadDashBoardSubTab('dashboard','activity_logs');"><a><span>Activity Logs</span></a></li>
			
			<li class="uk-visible@s uk-hidden@m uk-navbar-item"><a uk-toggle="{target:'#dashboard-offcanvas'}" class="uk-link-muted"><span uk-icon="bars"></span><span id="dashboard-option-text"> Program Stats</span></a></li>*/?>
		</ul>
		<?php /*
			<div class="uk-navbar-flip">
				<ul class="uk-navbar-nav " >
					<li class="uk-visible@s"><a uk-toggle="{target:'#main-offcanvas'}" class="uk-link-muted"><i class="uk-icon-arrow-circle-o-left"></i></a></li>
				</ul>
			</div>
		*/?>
	</div>
</nav>
<div id="dashboard-offcanvas" uk-offcanvas="overlay:true;  flip:true">
    <div class="uk-offcanvas-bar">
    	<ul class="uk-nav uk-nav-default" uk-nav >
			<li onClick="loadDashBoardSubTab('dashboard','stats');$('#dashboard-option-text').html(' Program Stats');UIkit.offcanvas.hide();" ><a><span>Stats</span></a></li>
			<li onClick="loadDashBoardSubTab('dashboard','map');$('#dashboard-option-text').html(' Map');UIkit.offcanvas.hide();" ><a><span>Map</span></a></li>
			<li onClick="loadDashBoardSubTab('dashboard','request_list');$('#dashboard-option-text').html(' Reimbursement Requests');UIkit.offcanvas.hide();"><a><span>Reqs</span></a></li>
			<li onClick="loadDashBoardSubTab('dashboard','po_list');$('#dashboard-option-text').html(' Purchase Orders');UIkit.offcanvas.hide();"><a><span>POs</span></a></li>
			<li onClick="loadDashBoardSubTab('dashboard','invoice_list');$('#dashboard-option-text').html(' Invoices');UIkit.offcanvas.hide();"><a><span>Invoices</span></a></li>
			<li onClick="loadDashBoardSubTab('dashboard','disposition_invoice_list');$('#dashboard-option-text').html(' Disposition Invoice');UIkit.offcanvas.hide();"><a><span>Disposition Inv.</span></a></li>
			<li onClick="loadDashBoardSubTab('dashboard','recapture_invoice_list');$('#dashboard-option-text').html(' Recapture Invoices');UIkit.offcanvas.hide();"><a><span>Recapture Inv.</span></a></li>
			
			
			<li onClick="loadDashBoardSubTab('dashboard','parcel_list');$('#dashboard-option-text').html(' All Parcels');UIkit.offcanvas.hide();"><a><span>Parcels</span></a></li>
			<li onClick="loadDashBoardSubTab('dashboard','accounting');$('#dashboard-option-text').html(' Accounting');UIkit.offcanvas.hide();"><a><span>Accounting</span></a></li>
			@if(Auth::user()->canManageUsers())<li onClick="loadDashBoardSubTab('dashboard','user_list');$('#dashboard-option-text').html(' Users');UIkit.offcanvas.hide();"><a><span>Users</span></a></li>@endIf
			@if(Auth::user()->entity_type == 'hfa')
			<li onClick="loadDashBoardSubTab('dashboard','admin_tools');$('#dashboard-option-text').html(' Admin Tools');UIkit.offcanvas.hide();"><a><span>Tools</span></a></li>@endIf
			<?php /*<li onClick="loadDashBoardSubTab('dashboard','activity_logs');$('#dashboard-option-text').html(' Activity Logs');UIkit.offcanvas.hide();"><a><span>Activity</span></a></li>*/ ?>
			
		
		</ul>
    </div>
</div>


	<div id="dashboard-subtabs-content">
		
	</div>
	
	

	<script>
	//function for loading this detail page's content
	function loadDashBoardSubTab(typeId,contentId) {
		// hide and stop infinite scroll
		$('#loading').hide();
		window.getContentForListId = 0;
		window.gettingHtml = 1;

		//check that content doesn't need to be saved
		
		var continueToLoad = 1;
		if (window.saved != 1) {
			continueToLoad = 0;
			UIkit.modal.confirm("You have unsaved changes, are you sure you want to continue loading this tab without saving?").then(function() {
				continueToLoad = 1;
				console.log('User confirmed to continue without saving.');
				window.saved=1;
				loadDashBoardSubTab(typeId,contentId);
			}); 
		} else {
			if(continueToLoad == 1) {
				console.log('Loading Subtab Content');
				//unload the content of the detail tab
				$('#dashboard-subtabs-content').html('');
				//take back to top
				$('#smoothscrollLink').trigger("click");	
				//load the selected detail tab content
				$('#dashboard-subtabs-content').load('/'+typeId+'/'+contentId, function(response, status, xhr) {
				  if (status == "error") {
				  	if(xhr.status == "401") {
				  		var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
				  	} else if( xhr.status == "500"){
				  		var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble processing your request - the server says it had an error.</p><p>It looks like everything else is working though. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.</p>";
				  	} else {
				  		var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.";
				  	}
				    
				    UIkit.modal.alert(msg);
				  }
				});
				console.log('Loaded document via ajax: /'+typeId+'/'+contentId);
				$('.uk-tab-responsive').attr("aria-expaned", "false");
					$('.uk-tab-responsive').removeClass("uk-open");
					console.log('Should close the menu.');
			}
		}	
	}
	
	
	@if(session('open_parcel_id')>0)
	$( document ).ready(function() {
		$.when(
			loadDetailTab('/parcel/','{{session('open_parcel_id')}}','1',1,1)
		).then(
			setTimeout(function(){console.log('timeout'); 
			$('#detail-tab-1-content').fadeIn();
			$('.detail-tab-1').fadeIn();
			$('#detail-tab-1').trigger("click");
		}, 300)
		);	
    });
	@elseif(session('open_vendor_id')>0)
	$('#dash-subtab-9').trigger("click");
	$( document ).ready(function() {
		$.when(
			dynamicModalLoad('admin/vendor/create/'+{{session('open_vendor_id')}})
		).then(
			setTimeout(function(){
				$('#vendors-tab').trigger("click");
			}, 300)
		);	
    });
	@elseif($loadDetailTab == 2)
	$('#detail-tab-2').trigger("click");
	$('#detail-tab-2').addClass('uk-active');
	@else
	$('#{{$tab}}').trigger("click");
	$('#{{$tab}}').addClass('uk-active');
	@endif
	</script>
	@if(session('hideHowTo')<1)
		@include('partials.helpers.landbank.reimbursement_steps')
	@else 
		<?php  session(['hideHowTo'=>0]); ?>
	@endIf
		
@stop
