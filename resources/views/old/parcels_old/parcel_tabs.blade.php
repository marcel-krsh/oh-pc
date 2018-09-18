<script>
// Update Tab Title - note the refresh button in tab text script.
$('#detail-tab-1-text').html(' : {{$parcel->parcel_id}} <span uk-tooltip="pos:top-right;title:Refresh this parcel"><span id="detail-1-refresh-button" onClick="loadDetailTab(\'/parcel/\',{{$parcel->id}},1,1,0);"><span uk-icon="refresh" class="uk-link-muted tab-refresh"></span></span></span>');

$('#detail-tab-1-icon').attr('uk-icon','home');
// display the tab
$('#detail-tab-1').show();
</script>
<?php /*<div class="uk-align-right" style="margin:0px;">
				<div class="styled-select-tabbed">
					<select class="uk-button uk-button-default uk-button-small uk-form-select" id="change-status" name="change-status">
						<?php // Land Bank Status Change // ?>
						<option value="">Change Status To:</option>
						<option value="1">In Process</option>
						<option value="2">Ready for Signator</option>
						<option value="3">Ready for Submission</option>
						<?php // HFA Status Change // ?>
						<option value="">Change Status To:</option>
						<option value="4">Compliance Review</option>
						<option value="5">Ready for Signators</option>
						
						
						
					</select>
				</div>
			</div>*/?>
<nav class="uk-navbar">
	<ul class="uk-navbar-nav  uk-list" uk-switcher >
			<!-- dev note: the subtab-1 id is only here to trigger it being loaded on the initial load of the detail content. -->
			<li class="uk-visible@m" id="parcel-subtab-1" onClick="loadParcelSubTab('detail',{{$parcel->id}});" class="uk-active">
				<a><span>Parcel Detail</span></a>
			</li>
			<!-- <li class="uk-visible@m" onClick="loadParcelSubTab('compliance',{{$parcel->id}});"><a><span>Compliance</span></a></li> -->
			<!-- <li class="uk-visible@m" onClick="loadParcelSubTab('program_signatures',{{$parcel->id}});"><a><span>Program Signatures</span></a></li> -->
			<li class="uk-visible@m" id="parcel-subtab-2" onClick="loadParcelSubTab('documents',{{$parcel->id}});"><a><span>Supporting Documents</span></a></li>
			<li class="uk-visible@m" id="parcel-subtab-3" onClick="loadParcelSubTab('communications',{{$parcel->id}});"><a><span>Communications</span></a></li>
			<li class="uk-visible@m" id="parcel-subtab-4" onClick="loadParcelSubTab('notes',{{$parcel->id}});"><a><span>Notes</span></a></li>
			<!-- <li class="uk-visible@m" onClick="loadParcelSubTab('hfa_signatures',{{$parcel->id}});"><a><span>HFA Signatures</span></a></li> -->
			<li class="uk-visible@m" onClick="loadParcelSubTab('history',{{$parcel->id}});"><a><span>Parcel History</span></a></li>
			<div class="uk-visible@s uk-hidden@m uk-navbar-item"><a uk-toggle="{target:'#parcel-offcanvas'}" class="uk-link-muted"><span uk-icon="bars"></span><span id="parcel-option-text"> Parcel Detail</span></a></div>
			
	</ul>
	<?php /*
		<div class="uk-navbar-flip">
			<ul class="uk-navbar-nav " >
				<li class="uk-visible@s"><a uk-toggle="{target:'#main-offcanvas'}" class="uk-link-muted"><i class="uk-icon-arrow-circle-o-left"></i></a></li>
			</ul>
		</div>
	*/ ?>
</nav>
<div id="parcel-offcanvas" uk-offcanvas="overlay:true;  flip:true">
    <div class="uk-offcanvas-bar">
    	<ul class="uk-nav uk-nav-default" uk-nav >
			<!-- dev note: the subtab-1 id is only here to trigger it being loaded on the initial load of the detail content. -->
			<li id="parcel-subtab-1" onClick="loadParcelSubTab('detail',{{$parcel->id}});$('#parcel-option-text').html(' Parcel Detail');UIkit.offcanvas.hide();" >
				<a><span>Parcel Detail</span></a>
			</li>
			<li onClick="loadParcelSubTab('program_signatures',{{$parcel->id}});$('#parcel-option-text').html(' Program Signatures');UIkit.offcanvas.hide();"><a><span>Program Signatures</span></a></li>
			<li onClick="loadParcelSubTab('documents',{{$parcel->id}});$('#parcel-option-text').html(' Supporting Documents');UIkit.offcanvas.hide();"><a><span>Supporting Documents</span></a></li>
			<li onClick="loadParcelSubTab('communications',{{$parcel->id}});$('#parcel-option-text').html(' Communications');UIkit.offcanvas.hide();"><a><span>Communications</span></a></li>
			<li onClick="loadParcelSubTab('notes',{{$parcel->id}});$('#parcel-option-text').html(' Parcel Detail');UIkit.offcanvas.hide();"><a><span>Notes</span></a></li>
			<li onClick="loadParcelSubTab('hfa_signatures',{{$parcel->id}});$('#parcel-option-text').html(' HFA Signatures');UIkit.offcanvas.hide();"><a><span>HFA Signatures</span></a></li>
			<li onClick="loadParcelSubTab('history',{{$parcel->id}});$('#parcel-option-text').html(' Parcel History');UIkit.offcanvas.hide();"><a><span>Parcel History</span></a></li>
			
			
		
		</ul>
    </div>
</div>



	<div id="parcel-detail-subtabs-content">
		
	</div>
	
	
	</div>
	<script>
	//function for loading this detail page's content
	function loadParcelSubTab(typeId,contentId) {
		//check that content doesn't need to be saved
		
		var continueToLoad = 1;
		if (window.saved != 1) {
			continueToLoad = 0;
			UIkit.modal.confirm("You have unsaved changes, are you sure you want to continue loading this tab without saving?").then(function() {
				continueToLoad = 1;
				console.log('User confirmed to continue without saving.');
				window.saved=1;
				loadParcelSubTab(typeId,contentId);
			}); 
		} 
		else {
				if(continueToLoad == 1) {
					console.log('Loading Parcel Subtab Content');
					//unload the content of the detail tab
					$('#detail-subtabs-content').html('');
					//take back to top
					$('#smoothscrollLink').trigger("click");	
					//load the selected detail tab content
					$('#parcel-detail-subtabs-content').load('/'+typeId+'/parcel/{{$parcel->id}}');
					console.log('Loaded document via ajax: /'+typeId+'/parcel/{{$parcel->id}}');
					$('.uk-tab-responsive').attr("aria-expaned", "false");
					$('.uk-tab-responsive').removeClass("uk-open");
					console.log('Should close the menu.');
				} 
		}	
	}
	@if (session()->has('parcel_subtab')  && session('parcel_subtab') != '')
	$(document).ready(function(){
		var parcel_subtab_id = '';
        $.get( "/session/parcel_subtab", function( data ) {
                parcel_subtab_id = data;

		        if(parcel_subtab_id != ''){
		        	loadParcelSubTab(parcel_subtab_id,{{$parcel->id}});
		        }
            });
        window.scrollTo(0, 0);
        console.log('Clicked smoothscrollLink');
	});
 	@elseif(Request::query('subTab'))
 	$('#parcel-subtab-{{ Request::query('subTab') }}').trigger("click");
 	window.scrollTo(0, 0);
 	console.log('Clicked smoothscrollLink');
 	@else
	$('#parcel-subtab-1').trigger("click");
	window.scrollTo(0, 0);
	console.log('Clicked smoothscrollLink');
	@endif
	</script>	
</div>