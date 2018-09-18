
<nav class="uk-navbar">
	<ul class="uk-navbar-nav   uk-visible@m" uk-switcher >
		<li class="uk-visible@m" id="svm-subtab-1" onClick="loadSiteVisitManagerSubTab('site_visit_manager','visit_list');"  style="margin-left:10px;"  ><a><span>Site Visits</span></a></li>
		
		
		<?php /* <li  class="uk-visible@m" id="svm-subtab-4" onClick="loadSiteVisitManagerSubTab('site_visit_manager','manage_list');"><a><span>Manage Visit Lists</span></a></li> */ ?>

		<li  class="uk-visible@m" id="svm-subtab-3" onClick="loadSiteVisitManagerSubTab('site_visit_manager','manage_devices');"><a><span>Manage Devices</span></a></li>
	</ul>
	
</nav>
<div id="SiteManager-offcanvas" uk-offcanvas="overlay:true;  flip:true">
    <div class="uk-offcanvas-bar">
    	<ul class="uk-nav uk-nav-default" uk-nav >
			<li onClick="loadSiteVisitManagerSubTab('site_visit_manager','visit_list');$('#site-manager-option-text').html(' Vists List');UIkit.offcanvas.hide();"><a><span>Site Visits</span></a></li>
			<?php /* <li onClick="loadSiteVisitManagerSubTab('site_visit_manager','manage_list');$('#site-manager-option-text').html(' Manage List');UIkit.offcanvas.hide();" ><a><span>Manage Visit List</span></a></li> */ ?>
			<li onClick="loadSiteVisitManagerSubTab('site_visit_manager','manage_devices');$('#site-manager-option-text').html(' Manage Devices');UIkit.offcanvas.hide();"><a><span>Manage Devices</span></a></li>
		
		</ul>
    </div>
</div>


	<div id="svm-subtabs-content">
		
	</div>
	
	

	<script>
	if( !$.trim( $('#svm-subtabs-content').html() ).length ){
		loadSiteVisitManagerSubTab('site_visit_manager','visit_list');
	}
	//function for loading this detail page's content
	function loadSiteVisitManagerSubTab(typeId,contentId) {
		///check that content doesn't need to be saved
		
		var continueToLoad = 1;
		if (window.saved != 1) {
			continueToLoad = 0;
			UIkit.modal.confirm("You have unsaved changes, are you sure you want to continue loading this tab without saving?").then(function() {
				continueToLoad = 1;
				console.log('User confirmed to continue without saving.');
				window.saved=1;
				loadSiteVisitManagerSubTab(typeId,contentId);
			}); 
		} else {
			if(continueToLoad == 1) {
				console.log('Loading SVM Subtab Content');
				//unload the content of the detail tab
				$('#svm-subtabs-content').html('');
				//take back to top
				$('#smoothscrollLink').trigger("click");	
				//load the selected detail tab content
				$('#svm-subtabs-content').load('/'+typeId+'/'+contentId, function(response, status, xhr) {
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
	</script>
	@if(session('hideHowTo')<1)
		@include('partials.helpers.landbank.reimbursement_steps')
	@else 
		<?php  session(['hideHowTo'=>0]); ?>
	@endIf
		

