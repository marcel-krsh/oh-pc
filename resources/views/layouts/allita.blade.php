<?php setlocale(LC_MONETARY, 'en_US'); 
if(!isset($filter['lbFilters'])){
	$filter['lbFilters'] = DB::table('property_status_options')->where('for','landbank')->where('active','1')->orderBy('order','asc')->get();
    $filter['hfaFilters'] = DB::table('property_status_options')->where('for','hfa')->where('active','1')->orderBy('order','asc')->get();
}
/// protect against inactive users.
$allowPageLoad = false;

if(Auth::check()){
	if(Auth::user()->active == 1){
		$allowPageLoad = true;
	}
}else{
	/// user is not logged in -- the auth middleware will protect against that access.
	$allowPageLoad = true;
}
	if($allowPageLoad){
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" id="parentHTML" class="no-js">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Allita Program Compliance</title>

<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
<link rel="manifest" href="/manifest.json">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">

<meta name="theme-color" content="#ffffff">
<link rel="stylesheet" href="/css/allita-font.css">
<link rel="stylesheet" href="/css/uikit.min.css">
<link rel="stylesheet" href="/css/allita-admin-419171046.css">
<link rel="stylesheet" href="/css/system-419171130.css">
@if (Auth::guest())
@else
<link rel="stylesheet" href="/css/cdfs-tab.css">
<link rel="stylesheet" href="/css/communications-tab.css">
<link rel="stylesheet" href="/css/documents-tab.css">
<link rel="stylesheet" href="/css/funding-tab.css">
<link rel="stylesheet" href="/css/history-tab.css">
<link rel="stylesheet" href="/css/notes-tab.css">
<link rel="stylesheet" href="/css/outcomes-tab.css">
<link rel="stylesheet" href="/css/processing-tab.css">
<link rel="stylesheet" href="/css/handsontable.full.min.css">
<link rel="stylesheet" href="/css/components/slideshow.css">
<link rel="stylesheet" href="/css/auto-complete.css">
@endif
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@if(session('disablePacer')==1)
<style type="text/css">
	body:not(.pace-done) > :not(.pace),body:not(.pace-done):before,body:not(.pace-done):after {
  		opacity:1 !important;
	}
</style>
<?php session(['disablePacer'=>0]); ?>
@endif
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    window.Laravel = <?php echo json_encode([
        'csrfToken' => csrf_token(),
    ]); ?>
</script>
<script src="/js/jquery.js"></script>
<script src="/js/uikit.js"></script>
<script src="/js/uikit-icons.min.js"></script>
<script src="/js/handsontable.full.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/multiple-select/1.2.0/multiple-select.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/multiple-select/1.2.0/multiple-select.min.js"></script>

<script>
	$('select').multipleSelect();
</script>

@if (Auth::guest())
@else
<script src="/js/taffy.js"></script>
<script> window.continueToLoad = 1; window.saved = 1;
</script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.5/Chart.bundle.js"></script>
@endif
@yield('head')
</head>
<body >
<a name="top"></a>
<!-- MAIN VIEW -->
<div class="uk-container uk-align-center">
<div uk-grid class="uk-grid-collapse">
<div id="main-window" class=" " uk-scrollspy="cls:uk-animation-fade; delay: 900">
		
		<ul id="top-tabs" uk-switcher="swiping:false;" class="uk-tab uk-visible@m"  style="background: white; ">
    			@if (Auth::guest())
    			<li id="list-tab" class="list-tab" style="display: none" uk-scrollspy="cls:uk-animation-slide-bottom; delay: 1000"><a href=""> <span class="list-tab-text"> Welcome Back!</span></a></li>
    			
				@else
				<li id="list-tab" class="list-tab" style="display: none" uk-scrollspy="cls:uk-animation-slide-bottom; delay: 1000"><a href=""><i class="a-grid"></i> <span class="list-tab-text"> Dashboard</span></a></li>
    			
				<li id="detail-tab-1" class="detail-tab-1" style="display: none" uk-scrollspy="cls:uk-animation-slide-bottom; delay: 1300"><a href=""><span class="detail-tab-1-text"></span></a></li>
				<li id="detail-tab-2" class="detail-tab-2" style="display: none" uk-scrollspy="cls:uk-animation-slide-bottom; delay: 1600"><a href=""><i class="a-location-pin"></i> <span class="detail-tab-2-text"></span></a></li><!-- 
				<li id="detail-tab-3" class="detail-tab-3" style="display: none" ><a href=""><span class="detail-tab-3-text"></span></a></li>
				<li id="detail-tab-4" class="detail-tab-4" style="display: none" ><a href=""><span class="detail-tab-4-text"></span></a></li>
				<li id="detail-tab-5" class="detail-tab-5" style="display: none" ><a href=""><span class="detail-tab-5-text"></span></a></li> -->
				@endif
		</ul>
		
		<ul id="tabs" class="uk-switcher"> 
			<li>
				<div id="list-tab-content-b" >
					@yield('content')
					@yield('listTabContent')
				</div>
				<div id="loading" style="display:none;"><p class="uk-text-large uk-text-center"><i class="a-refresh-2 uk-icon-spin"></i></p></div>
			</li>
			@if (Auth::guest())
			@else
			<li>
				<div id="detail-tab-1-content">
					@yield('detail-tab-1-content')
				</div>
			</li>
			<li>
				<div id="detail-tab-2-content">
				</div>
			</li>
			<!-- <li>
				<div id="detail-tab-3-content">
					@yield('detail-tab-3-content')
					Detail tab 3
				</div>
			</li>
			<li>
				<div id="detail-tab-4-content">
					@yield('detail-tab-4-content')
					Detail tab 4
				</div>
			</li>
			<li>
				<div id="detail-tab-5-content">
					@yield('detail-tab-5-content')
					Detail tab 5
				</div>
			</li> -->
			@endif
		</ul>
		
		
<div id="filters" class="" uk-offcanvas="overlay:true;bgClose: true;">
	<div class="uk-offcanvas-bar">
			@if (Auth::guest())
			<p class="uk-margin-left"><a href="{{url('/login')}}" class="uk-dark uk-link-muted uk-light">Please Login</a></p>
			@else
		<?php // Land bank filters ?>
		<div class="" style="margin-bottom: 16px; display: inline-block;">
			<div id="user-logged-in ">
				<div id="logged-in-user-badge" class="user-badge-{{ Auth::user()->badge_color }} uk-margin-left" style="font-weight: 200">{{ userInitials(Auth::user()->name) }}</div>
				<div id="logged-in-user-info" class="" style="font-size: 1rem; line-height: 1.2rem; font-weight: 300"><span style="font-size: .8rem;">Why hello there,</span><br/>{{ Auth::user()->name }}</div>
				
				<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
			</div>
		</div>
		<div style="clear:both"></div>
		
		<ul class="uk-nav uk-nav-default uk-nav-parent-icon" uk-nav>
			<li><a href="#" onClick="logout()" class="uk-light"><i class="a-locked-2 uk-icon-justify"></i> Logout</a>
			</li>
			@if(Auth::user()->entity_type == 'landbank')
			<li><a href="#" onclick="showReimbursementHowTo()"><i class="a-arrow-right-2_1"></i> 8 Steps to Reimbursement</a></li>
			@endIf
			@if(Auth::user()->entity_type == 'hfa')
			<li><a href="#" onclick="showReimbursementHowTo()"><i class="a-arrow-right-2_1"></i> 8 Steps to Reimbursement for Land Banks</a></li>
			@endIf
			<?php /*
			<li><a class="uk-dark uk-link-muted uk-light" href="#" onclick="loadListTab('/lists/activity_log')"><i class="uk-icon-list-ul uk-icon-justify"></i> Activity Log</a></li>
			*/?>
			<li class="uk-parent uk-nav-parent-icon"><a href="#"><i class="a-grid uk-icon-justify"></i> Dashboard</a>
			<ul class="uk-nav uk-nav-sub">
					<li><a href="/dashboard?tab=10">Comms</a></li>
					<li><a href="/dashboard?tab=1">Stats</a></li>
					<li><a href="/dashboard?tab=4">Reqs</a></li>
                    <li><a href="/dashboard?tab=3">POs</a></li>
                    <li><a href="/dashboard?tab=2">Invoices</a></li>
                   	<li><a href="/dashboard?tab=8">Disposition Inv</a></li>
                   	<li><a href="/dashboard?tab=11">Recapture Inv</a></li>
                    <li><a href="/dashboard?tab=5">Parcels</a></li>
                    <li><a href="/dashboard?tab=6">Accounting</a></li>
                    
			</ul>
			</li>
			
			<li class="uk-parent uk-nav-parent-icon">
				<a href="#"><i class="a-tools-3 uk-icon-justify"></i> Tools</a>
				<ul class="uk-nav uk-nav-sub">
                    <li><a href='/import_parcels' uk-tooltip="Going here will reset all open tabs.">Import Landbank Parcels</a></li>

                    @if(Auth::user()->entity_type == "hfa")
                    <li><a href='/import_hhf_retention_parcels' uk-tooltip="Going here will reset all open tabs.">Import HHF Retention Parcels</a></li>
                    <li><a href="/dashboard?tab=9">Admin Tools</a></li>
                    <li><a href="/reports/export_parcels">Parcels Export Reports</a></li>
                    <li><a href="/reports/export_vendor_stats">Vendor Stats Export Reports</a></li>
                    @endIf
					<li><a href="/validate_parcels">Validate Imported Parcels</a></li>
					@if(Auth::user()->canManageUsers())
                    <li><a href="/dashboard?tab=7">User Management</a></li>

                    @endif
                </ul>
             </li>
             <?php /*
             <li class="uk-parent uk-nav-parent-icon">
				<a href="#"><i class="a-parameters-2 uk-icon-justify"></i> Parcel Filters</a>
					<ul class="uk-nav uk-nav-sub">
						@foreach($filter['lbFilters'] as $item)
							<li id="filter{{$item->id}}" class="" ><a href="/dashboard?tab=5&parcelsListFilter={{$item->id}}" onClick="loadListTab('/dashboard?tab=5&parcelsListFilter={{$item->id}}');"><i class="a-list"></i> {{$item->option_name}}</a></li>
						@endforeach
					</ul>
                 </li>
	        	<?php // HFA filters ?>
	        	@if(Auth::user()->entity_type == 'bob')
				<li class="uk-parent uk-nav-parent-icon">
					<a href="#"><i class="a-parameters-2 uk-icon-justify"></i> HFA Parcel Filters</a>
					<ul class="uk-nav uk-nav-sub">
						@foreach($filter['hfaFilters'] as $item)
							<li id="filter{{$item->id}}" class="" ><a href="/dashboard?tab=5&hfaParcelsListFilter={{$item->id}}" onClick="loadListTab('/dashboard?tab=5&parcelsListFilter={{$item->id}}');"><i class="a-list" class=""></i> {{$item->option_name}}</a></li>
						@endforeach
	                </ul>
	            </li>
	            @endIf
	            */ ?>
	    	</ul>
         @endif
	</div>
</div>


<script src="/js/app.js"></script>
@if (Auth::guest())
@else
<script type="text/javascript">
	$('.list-tab').slideToggle();
	//$('#detail-tab-1').slideToggle();
	//$('#detail-tab-2').slideToggle();
	//$('#detail-tab-3').slideToggle();
	//$('#detail-tab-4').slideToggle();
	//$('#detail-tab-5').slideToggle();

</script>
<script type="text/javascript" src="/js/systems/system.js"></script>
<script type="text/javascript" src="/js/systems/cdfs-tab.js"></script>
<script type="text/javascript" src="/js/systems/communications-tab.js"></script>
<script type="text/javascript" src="/js/systems/documents-tab.js"></script>
<script type="text/javascript" src="/js/systems/funding-tab.js"></script>
<script type="text/javascript" src="/js/systems/history-tab.js"></script>
<script type="text/javascript" src="/js/systems/notes-tab.js"></script>
<script type="text/javascript" src="/js/systems/outcomes-tab.js"></script>
<script type="text/javascript" src="/js/systems/processing-tab.js"></script>

<script src="/js/auto-complete.js"></script>
<script>
    var quicklookupbox = new autoComplete({
        selector: '#quick-lookup-box',
        minChars: 3,
        cache: 0,
        delay: 480,
        source: function(term, suggest){
        	$.get( "/parcels/parcel-autocomplete", {
				'search' : term,
				'_token' : '{{ csrf_token() }}'
			},
			function(data) {
				var output = eval(data);
				 //console.log(output.length);
				 //console.log(output[0][0]+' '+output[0][1]+' '+output[0][3]+' '+output[0][4]);
				console.log('Line 404: Searched for "'+term+'"');
				term = term.toLowerCase();
	            var suggestions = [];
	            for (i=0;i<output.length;i++)
		            if (~(output[i][0]+' '+output[i][1]+' '+output[i][3]+' '+output[i][4]).toLowerCase().indexOf(term)) {
		            	suggestions.push(output[i]);
		            	console.log('Line 410: Suggestion '+(i+1)+' of '+output.length+' pushed: '+output[i][0]+' | '+output[i][1]+' | '+output[i][3]+' | '+output[i][4]);
		            } else {
		            	console.log('Line 412: Skipped '+(i+1));
		            }
		        //console.log(suggestions);
		        suggest(suggestions);
				
			},
			'json' );
        },
        renderItem: function (item, search){
		    search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
		    var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");

		    var output = '<div class="autocomplete-suggestion" data-item-id="'+item[4]+'" data-val="'+search+'">';
		    output = output + 'Parcel ID: '+item[3]+'<br />';
		    output = output + item[0]+'<br />';
		    output = output + item[1]+', '+item[2]+' '+item[3]+'<br />';
		<?php if(Auth::user()->entity_type == "hfa"){ ?>
			output = output + 'LB: '+ item[5] +'<br />HFA: '+ item[6]+'<br />';
		<?php } else { ?>
			output = output + item[5]+'<br />';
		<?php } ?>
			output = output + '<span class="hideImport'+item[7]+'">';
			output = output + 'Import #'+item[7]+' on '+item[11]+'<br />By '+item[8]+'</span>';
		    output = output + '</div>';
		    
		    return output;
		},
	    onSelect: function(e, term, item){
	    	loadDetailTab('/parcel/',item.getAttribute('data-item-id'),'1',0,0);
	    	$('#quick-lookup-box').val('');
	    }
    });

</script>

@endif

	<a id="smoothscrollLink" href="#top" uk-scroll="{offset: 90}"></a>
	<div id="dynamic-modal" uk-modal>
		<div id="modal-size" class="uk-modal-dialog uk-modal-body uk-modal-content"> 
			<a class="uk-modal-close-default" uk-close></a>
			<div id="dynamic-modal-content"></div>
		</div>
	</div>
</div>

</div>
<div class="uk-width-1-1 uk-margin-large-bottom"><p class="uk-text-center uk-dark uk-text-small uk-light">Powered by <a href="http://allita.org" target="_blank" class="uk-link-muted uk-dark uk-light">Allita</a> for <a href="http://ohiohome.org" class="uk-link-muted uk-dark uk-light" target="_blank">Ohio Housing Finance Agency</a> &copy; 2016 — @php echo date('Y',time()); @endphp All Rights Reserved.</p></div>
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
@if (Auth::check())
<script>
	@if(Auth::user()->canViewSiteVisits())
	function loadSiteVisitManager(typeId,contentId) {
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
				
				//unload the content of the detail tab
				$('#detail-tab-2-content').html('');
				//take back to top
				$('#smoothscrollLink').trigger("click");	
				//load the selected detail tab content
				$('#detail-tab-2-content').load('/'+typeId+'/'+contentId, function(response, status, xhr) {
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
					//console.log('Should close the menu.');
			}
		}	
	}
	// load the initial view
	loadSiteVisitManager('site_visit_manager','index');
	@endif
    setInterval(function() {
    	// console.log("checking for new messages...");
	  // ajax newest message and display notification
	  $.get( "/communications/new-messages", function( data ) {
	  		if(data){
	  			console.log("message found");
	  			var messages = '';
	  			var summary = '';
	  			for (var i = 0; i < data.length; i++) {
	  				summary = data[i]['communication']['message'];
	  				summary = (summary.length > 200) ? summary.substr(0, 199) + '&hellip;' : summary;
	  				if(data[i]['communication']['parcel']){
						messages = messages+'<a href="/view_message/'+data[i]['communication_id']+'" onclick="UIkit.notification.close()">"'+summary+'" from '+data[i]['communication']['owner']['name']+' for parcel '+data[i]['communication']['parcel']['parcel_id']+'</a>';
	  				}else{
	  					messages = messages+'<a href="/view_message/'+data[i]['communication_id']+'" onclick="UIkit.notification.close()">"'+summary+'" from '+data[i]['communication']['owner']['name']+'</a>';
	  				}
	  				
	  				if (!i < data.length -1) {
	  					messages = messages+'<hr />';	
	  				}
	  			}

	 			// reload the unseen communications
	 			reloadUnseenMessages();

	  			UIkit.notification('<i uk-icon="envelope" class=""></i> You have '+data.length+' messages:<br /><br />'+messages, {pos:'top-right', timeout:0, status:'success'});

	  		}
        });
	}, 1000 * 10);

	
</script>
@endif

@if(session('open_parcel') > 0) 
<script> 
$(document).ready(function(){
	var open_parcel_id = '';
    $.get( "/session/open_parcel", function( data ) {
            open_parcel_id = data;
            console.log('Loading Parcel Id: '+open_parcel_id);

	        if(open_parcel_id != ''){
	        	loadDetailTab('/parcel/',open_parcel_id,'1',0,0);
	        }
        });
});
</script>
@endif

@if(session('open_vendor') > 0) 
<script> 
$(document).ready(function(){
	var open_vendor_id = '';
    $.get( "/session/open_vendor", function( data ) {
            open_vendor_id = data;
            console.log('Loading Vendor Id: '+open_vendor_id);

	        if(open_vendor_id != ''){
	        	loadDetailTab('/vendor/',open_vendor_id,'1',0,0);
	        }
        });
});
</script>
@endif

<script>
	$(".uk-modal").on("hide", function() {
	    $("html").removeClass("uk-modal-page");
	});
</script>
@if(Auth::check())
@if(Auth::user()->entity_type == 'hfa')
	<script type="text/javascript">
		var $zoho=$zoho || {};$zoho.salesiq = $zoho.salesiq || 
		{widgetcode:"676622b8482bc91a831d0cd4ca9043e6c19fad1e199256fac50d2b5354d1e743a84f59a27361c238a1b1d868cfdeb375", values:{},ready:function(){}};
		var d=document;s=d.createElement("script");s.type="text/javascript";s.id="zsiqscript";s.defer=true;
		s.src="https://salesiq.zoho.com/widget";t=d.getElementsByTagName("script")[0];t.parentNode.insertBefore(s,t);d.write("<div id='zsiqwidget'></div>");
	</script>
@endIf
@endIf
<script>
	var _uh = _uh || [];
	_uh.push(['AllitaHost', 'https://pcinspectdev.ohiohome.org']);
	_uh.push(['Logo', 'https://static.wixstatic.com/media/64bb8d_0ca6465192ae42b89d419bbadaa42a05~mv2.png/v1/fill/w_171,h_169,al_c,usm_0.66_1.00_0.01/64bb8d_0ca6465192ae42b89d419bbadaa42a05~mv2.png']);
	_uh.push(['Css', '/poc_files/universal-header/universal-header.css']);
	_uh.push(['LeftItems', '<button>HELP!</button>']);
	_uh.push(['RightItems', '<button>🔔</button>']);	
	(function() {
	    var uh = document.createElement('script'); uh.type = 'text/javascript'; uh.async = true;
	    //uh.src = 'https://devco.ohiohome.org/AuthorityOnlineALT/Unified/UnifiedHeader.aspx';
	    uh.src = "{{config('app.url')}}/poc/universal-header/hosted.js";
	    var s = document.getElementsByTagName('script')[0]; 
	    $( document ).ready(function() {
	    	s.parentNode.insertBefore(uh, s);
	    });
	})();
</script>
@if(session('disablePacer') != 1)
<script>
	window.paceOptions = { ajax: { trackMethods: ['GET', 'POST', 'DELETE', 'PUT', 'PATCH'], ignoreURLs: ['{{config('app.url')}}/poc/universal-header/'] } }
</script>
<script src="/js/pace.min.js">{{session('disablePacer')}}</script>
@endif
</body>
</html>
<?php } else { /// show for inactive users ?>
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
<link rel="stylesheet" href="/css/allita-admin-419171046.css">
<link rel="stylesheet" href="/css/system-419171130.css">

<script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
@if(session('disablePacer') != 1)
<script data-pace-options='{ "restartOnRequestAfter": false }' src="/js/pace.js">{{session('disablePacer')}}</script>

@endif
<script src="/js/jquery.js"></script>
<script src="/js/uikit.min.js"></script>
<!-- <script src="/js/components/autocomplete.js"></script>
<script src="/js/core/modal.js"></script>
<script src="/js/components/lightbox.js"></script>
<script src="/js/components/sticky.js"></script>
<script src="/js/components/notify.js"></script>
<script src="/js/components/tooltip.js"></script>
<script src="/js/components/datepicker.js"></script>
<script src="/js/components/slideshow.js"></script>
<script src="/js/components/slideshow-fx.js"></script>
<script src="/js/components/upload.js"></script>
<script src="/js/components/lightbox.js"></script>
<script src="/js/components/form-select.js"></script>
<script src="/js/components/slider.js"></script>
<script src="/js/components/slideset.js"></script>
<script src="/js/components/accordion.js"></script>
<script src="/js/components/notify.js"></script>
<script src="/js/components/search.js"></script>
<script src="/js/components/timepicker.js"></script>
<script src="/js/components/nestable.js"></script>
<script src="/js/components/sortable.js"></script>
<script src="/js/components/grid.min.js"></script>
<script src="/js/handsontable.full.min.js"></script> -->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/multiple-select/1.2.0/multiple-select.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/multiple-select/1.2.0/multiple-select.min.js"></script>

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

}  ?>