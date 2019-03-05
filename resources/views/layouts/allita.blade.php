<?php session(['disablePacer'=>1]); ?>
<?php setlocale(LC_MONETARY, 'en_US'); 
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
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>
	@if(Auth::check() && Auth::user()->entity_type == 'hfa') 
	Allita Program Compliance
	@else
	Dev|Co Inspect
	@endif
	</title>

	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="/manifest.json">
	<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">

	<meta name="theme-color" content="#ffffff">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">

	@if (Auth::guest())
	@else

	<!-- <link rel="stylesheet" href="/css/cdfs-tab.css">
	
	<link rel="stylesheet" href="/css/documents-tab.css">
	<link rel="stylesheet" href="/css/funding-tab.css">
	<link rel="stylesheet" href="/css/history-tab.css">
	<link rel="stylesheet" href="/css/notes-tab.css">
	<link rel="stylesheet" href="/css/outcomes-tab.css">
	<link rel="stylesheet" href="/css/processing-tab.css">
	<link rel="stylesheet" href="/css/handsontable.full.min.css">
	<link rel="stylesheet" href="/css/components/slideshow.css"> -->
	<link rel="stylesheet" href="/css/communications-tab.css">
	<link rel="stylesheet" href="/css/auto-complete.css">

	@endif

	<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"> -->
	@if(session('disablePacer')==1)
	<style type="text/css">
		body:not(.pace-done) > :not(.pace),body:not(.pace-done):before,body:not(.pace-done):after {
	  		opacity:1 !important;
		}
		/*Universal header styles*/
		
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
		
	</style>
	<?php /* session(['disablePacer'=>0]); */ ?>
	@endif
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
	<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
	<script>
		function openUserPreferences(){
			dynamicModalLoad('auditors/{{Auth::user()->id}}/preferences',0,0,1);
		}
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
	
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.17/vue.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<!--     <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.1/socket.io.js"></script>  -->
	<script src="{{ mix('js/app.js') }}"></script>

	<script>
		// $('select').multipleSelect();
	</script>

	@if (Auth::guest())
	@else
	<script src="/js/taffy.js"></script>
	<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script> -->
	<script type="text/javascript" src="/js/Chart.bundle.js"></script>
	@endif

	<link rel="stylesheet" href="/css/allita-font.css">
	<link rel="stylesheet" href="/css/uikit.min.css">
	<link rel="stylesheet" href="/css/allita-admin-419171046.css">
	<link rel="stylesheet" href="/css/system-419171130.css">

	@yield('head')
	
    <style>
	  [v-cloak] {
	    display: none;
	  }
	  .uk-notification {z-index: 1060;}
	</style>

	<script>
		// initial values
	    var statsAuditsTotal = "{{$stats_audits_total}}";
	    var statsCommunicationTotal = "{{$stats_communication_total}}";
	    var statsReportsTotal = "{{$stats_reports_total}}";
	    var uid = "{{$current_user->id}}";
	    var sid = "{{$current_user->socket_id}}";
	</script>
	
</head>
<body >
	<a name="top"></a>
	<div id="phone" class="uk-visible-touch uk-hidden@s">
		<div id="phone-app" class="uk-container uk-align-center" >
			<div uk-grid>
				<div class="uk-width-1-1 uk-padding-small" uk-sticky style="height: 10%; background: #333">
					<a ><h2><i class="a-menu uk-text-muted uk-contrast"></i> DEV|CO INSPECT</h2></a>
				</div>
				<div id="mobile-content" class="uk-scroll uk-width-1-1" style="height: 88%">
				</div>
				<script type="text/javascript">
					isMobile = function(){
    							var isMobile = window.matchMedia("only screen and (max-width: 640px)");
					    return isMobile.matches ? true : false
					}
					if(isMobile){
						//load mobile content
					}
				</script>
			</div>
		</div>
	</div>
	
	
	<!-- MAIN VIEW -->

	<div id="app" class="uk-container uk-align-center uk-visible@s" >
		<div uk-grid class="uk-grid-collapse">
			<div id="main-window" class=" uk-margin-large-bottom" uk-scrollspy="cls:uk-animation-fade; delay: 900">
			
				<div id="main-tabs" uk-sticky style="max-width: 1519px; ">
					<div uk-grid>
						<div class="uk-width-1-1">
							<img id="apcsv-logo" src="/images/devco_logo.png" alt="DEV|CO Inspection powered by Allita PC" >
						
					        @can('access_auditor')
					        <div class="menu-search uk-margin-large-left uk-padding-bottom" style="display: inline-block; position: relative;top:-5px;" class="uk-margin-large-left">
								<div class="uk-autocomplete quick-lookup-box uk-inline">
									<span class="uk-form-icon a-magnify-2"></span>
									<input class="uk-input" id="quick-lookup-box" type="text" placeholder="QUICK LOOK UP..." style="width: 250px;">
								</div>
							</div>
							@else
							<div style="width: 20px; display: inline-block;"></div>
							@endCan
					    
					    	<div id="top-tabs-container" style="display: inline-block; overflow: visible; padding-top:15px; min-height: 26px;">
						        @can('access_pm')
						        <ul id="top-tabs" uk-switcher="connect: .maintabs; swiping:false; animation: uk-animation-fade;" class="uk-tab uk-visible@m" style="background-color: transparent;">
					    			<li id="detail-tab-1" class="detail-tab-1" uk-scrollspy="cls:uk-animation-slide-bottom; delay: 1000" onClick="if($('#detail-tab-1').hasClass('uk-active')  || window.auditsLoaded != 1){loadTab('{{ route('dashboard.audits') }}','1','','','',1);}">
					    				<a href="" style="">
					    					<span class="list-tab-text">
					    						<span class="uk-badge" v-if="statsAuditsTotal" v-cloak>@{{statsAuditsTotal}}
					    						</span> 
					    						<i class="a-mobile-home"></i> AUDITS
					    					</span>
					    				</a>
					    			</li>
									<li id="detail-tab-2" class="detail-tab-2" uk-scrollspy="cls:uk-animation-slide-bottom; delay: 1000" onClick="if($('#detail-tab-2').hasClass('uk-active') || window.comunicationsLoaded != 1){loadTab('{{ route('communication.tab') }}', '2','','','',1);}">
										<a href=""> 
											<span class="list-tab-text">
												<span class="uk-badge" v-if="statsCommunicationTotal" v-cloak v-html="statsCommunicationTotal"></span> 
												 <i class="a-envelope-3"></i> COMMUNICATIONS
											</span>
										</a>
									</li>
									@if(env('APP_ENV') == 'local')
									<li id="detail-tab-3" class="detail-tab-3" uk-scrollspy="cls:uk-animation-slide-bottom; delay: 1000" onClick="if($('#detail-tab-3').hasClass('uk-active')  || window.reportsLoaded != 1){loadTab('{{ route('dashboard.reports') }}', '3','','','',1);}">
										<a href=""><span class="list-tab-text"><span class="uk-badge" v-if="statsReportsTotal" v-cloak>@{{statsReportsTotal}}</span></span> <i class="a-file-chart-3"></i> <span class="list-tab-text">  REPORTS</span></a>
									</li>
									@endif
									@can('access_admin')
									<li id="detail-tab-5" class="detail-tab-5" uk-scrollspy="cls:uk-animation-slide-bottom; delay: 1000" onClick="if($('#detail-tab-5').hasClass('uk-active')  || window.adminLoaded != 1){loadTab('{{ route('dashboard.admin') }}', '5','','','',1);}" >
										<a href=""><span class="list-tab-text">ADMIN</span></a>
									</li>
									@endcan
								</ul>
								@endcan
							</div>
				    	
				    		<div id="apcsv-avatar" class="" title="{{Auth::user()->full_name()}} - User ID:{{Auth::user()->id}} @if(Auth::user()->root_access()) Root Access @elseIf(Auth::user()->admin_access()) Admin Access @elseIf(Auth::user()->auditor_access()) Auditor Access @elseIf(Auth::user()->pm_access()) Property Manager @endIf" onclick="openUserPreferences();" style="cursor: pointer; margin-top:15px">
							{{Auth::user()->initials()}}
							</div>
							<div id="apcsv-menu-icon" class="hvr-grow uk-inline" style="margin-top:15px">
								<button id="apcsv-toggle" class="pcsv-toggle" style="background-color: transparent; border: none; cursor: pointer; 0" >APPS</button>    
								<div uk-dropdown="mode: click">
									<div class="apcsv-menu-item"> 
										<a href="https://devco.ohiohome.org/AuthorityOnline/" style="font-weight: 400">DEV|CO Compliance</a>
									</div>
									<div class="apcsv-menu-item">
										<a href="/" style="font-weight: 400">DEV|CO Inspection</a>
									</div>
									@if(Auth::user()->allowed_tablet && Auth::user()->auditor_access())
									<div class="apcsv-menu-item uk-hidden-notouch">
										<a href="allitapc://" style="font-weight: 400">Open Tablet App</a>
									</div>
									@endif
								</div>
							</div>
				    	</div>
					</div>
				</div>
				
				<ul id="tabs" class="maintabs uk-switcher" > 
					<li>
						<div id="detail-tab-1-content"></div>
					</li>
					<li>
						<div id="detail-tab-2-content"></div>
					</li>
					@if(env('APP_ENV') == 'local')
					<li>
						<div id="detail-tab-3-content"></div>
					</li>
					@endIf
					@if(Auth::user()->admin_access())
					<li>
						<div id="detail-tab-5-content" style="padding-top:20px;"></div>
					</li>
					@endif
				</ul>

				<a id="smoothscrollLink" href="#top" uk-scroll="{offset: 90}"></a>
				<div id="dynamic-modal" uk-modal>
					<div id="modal-size" class="uk-modal-dialog uk-modal-body uk-modal-content"> 
						<a class="uk-modal-close-default" uk-close></a>
						<div id="dynamic-modal-content"></div>
					</div>
				</div>
			</div>

		</div>
	</div>

	<div id="mainfooter"  class="uk-visible@s" uk-grid>
		<div class="uk-width-1-3">
			<p class="uk-dark uk-light" style="position: absolute; bottom: 20px;"><a href="http://allita.org" target="_blank" class="uk-link-muted uk-dark uk-light"><i class="a-mobile-home"></i>
			@if(Auth::check() && Auth::user()->auditor_access()) 
			Allita Program Compliance 
			@else
			Dev|Co Inspect
			@endif
			&copy; 2018<?php if(date('Y',time()) != '2018') echo " — ".date('Y',time()); ?>: @include('git-version::version-comment')</a> </p>
		</div>
		<div id="footer-content" class="uk-width-1-3">
			<div id="footer-actions-tpl"  class="uk-text-right"></div>
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

	
	<!-- <script src="/js/app.js"></script> -->
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
	<script src="/js/auto-complete.js"></script>
	<script type="text/javascript" src="/js/systems/system.js"></script>
	<script type="text/javascript" src="/js/systems/audits.js"></script>
	<script type="text/javascript" src="/js/systems/findings.js"></script>
	<script type="text/javascript" src="/js/systems/communications.js"></script>

	<!-- <script type="text/javascript" src="/js/systems/cdfs-tab.js"></script>
	<script type="text/javascript" src="/js/systems/communications-tab.js"></script>
	<script type="text/javascript" src="/js/systems/documents-tab.js"></script>
	<script type="text/javascript" src="/js/systems/funding-tab.js"></script>
	<script type="text/javascript" src="/js/systems/history-tab.js"></script>
	<script type="text/javascript" src="/js/systems/notes-tab.js"></script>
	<script type="text/javascript" src="/js/systems/outcomes-tab.js"></script> -->
	<script type="text/javascript" src="/js/systems/processing-tab.js"></script>
	<script>
	    var quicklookupbox = new autoComplete({
	    	selector: '#quick-lookup-box',
	        minChars: 3,
	        cache: 1,
	        delay: 150,
			offsetLeft: 0,
			offsetTop: 1,
			menuClass: '',

	        source: function(term, suggest){
	        	console.log('Looking up... '+term);
	        	$.get( "/autocomplete/all", {
					'search' : term,
					'_token' : '{{ csrf_token() }}'
				},
				function(data) {
					var output = eval(data);
					term = term.toLowerCase();
		            var suggestions = [];
		            for (i=0;i<output.length;i++)
		            	suggestions.push(output[i]);
			        suggest(suggestions);
				},
				'json' );
	        },
	        renderItem: function (item, search){
			    search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
			    var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");

			    var output = '<div class="autocomplete-suggestion" data-item-id="'+item[8]+'" data-val="'+search+'">';
			    output = output + item[4]+'<br />';
			    output = output + 'Project ID: '+item[7]+'<br />';
				output = output + 'PM: '+item[6]+'<br />';
			    output = output + item[0]+'<br />';
			    output = output + item[1]+', '+item[2]+' '+item[3]+'<br />';
			    output = output + '</div>';
			    
			    return output;
			},
		    onSelect: function(e, term, item){
		    	e.preventDefault();
		    	loadTab('/projects/'+item.getAttribute('data-item-id'), '4', 1, 1, '', 1);
		    	//loadDetailTab('/parcel/',item.getAttribute('data-item-id'),'1',0,0);
		    	$('#quick-lookup-box').val('');
		    }
	    });

	    $( document ).ready(function() {
	    	$('.uk-sticky-placeholder:last').remove();
	    	$("html, body").animate({ scrollTop: 0 }, "slow");
	    });

	</script>
	@endif

	@if($tab !== null)
	<script>
		if(!isMobile){
			setTimeout(function(){
				$('#{{$tab}}').trigger("click");
			},100);
		}
			window.currentSite='allita_pc';
	</script>
	@else
	<script >
		if(!isMobile){
			setTimeout(function(){
				$('#detail-tab-1').trigger("click");
			},100);
		}
		window.currentSite='allita_pc';
		
	</script>
	@endif

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
	<script src="/js/pace.min.js">{{session('disablePacer')}}</script>
	@endif

	<!-- <script type="text/javascript" src="https://devco.ohiohome.org/AuthorityOnlineALT/Unified/UnifiedHeader.aspx"></script> -->
	<script>		
		new Vue({
		  el: '#top-tabs',
		  data: {
		    statsAuditsTotal: statsAuditsTotal,
		    statsCommunicationTotal: statsCommunicationTotal,
		    statsReportsTotal: statsReportsTotal
		  	},

		    mounted: function() {
		    	console.log("Tabs Working");

		    	//Echo.join('communications.'+uid+'.'+sid);
		    	Echo.private('updates.{{Auth::user()->id}}')
				    .listen('UpdateEvent', (payload) => {
				    	@if(env('APP_DEBUG'))
					    	console.log('Update received with:');
					    	console.log(payload);
				    	@endIf

				    	if(payload.data.event == 'tab'){
					        console.log("Tab event received.");
					        this.statsCommunicationTotal = payload.data.communicationTotal;
					    }
			    });
		    	
		            // console.log("new total "+data.communicationTotal);
		            

		        // socket.on('communications.'+uid+'.'+sid+':NewRecipient', function(data){
		        //     // console.log("user " + data.userId + " is getting a message because a new message has been sent.");
		        //     // console.log("new total "+data.communicationTotal);
		        //     this.statsCommunicationTotal = data.communicationTotal;
		        
			
		    
		}
	});
	</script>
	<!-- <script>
    	function openWebsocket(url){
		    try {
		        socket = new WebSocket(url);
		        socket.onopen = function(){
		            console.log('Socket is now open.');
		        };
		        socket.onerror = function (error) {
		            console.error('There was an un-identified Web Socket error');
		        };
		        socket.onmessage = function (message) {
		            console.info("Message: %o", message.data);
		        };
		    } catch (e) {
		        console.error('Sorry, the web socket at "%s" is un-available', url);
		    }
		}

		openWebsocket("http://192.168.10.10:6001");
	</script> -->

       
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