<div class="" uk-filter="target: .js-findings">
	<div uk-grid>
		{{-- <div class="uk-width-1-1 filter-button-set-right js-findings-buttons" uk-grid>
			<div class="uk-width-1-5 uk-active findinggroup" uk-filter-control="filter: [data-finding-filter*='my-finding']; group: findingfilter; " onclick="clickingOnFindingFilter(this);">
				<button class="uk-button uk-button-default button-filter button-filter-border-left" >My finding</button>
				<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="">
					<a class="sort-asc"></a>
				</span>
			</div>
			<div class="uk-width-1-5 findinggroup" uk-filter-control="filter: [data-finding-filter*='all']; group: findingfilter;" onclick="clickingOnFindingFilter(this);">
				<button class="uk-button uk-button-default button-filter" style="padding-left: 5px; padding-right: 5px;">All findings</button>
				<span style="display:none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="">
					<a class="sort-asc"></a>
				</span>
			</div>
			<div class="uk-width-1-5 uk-active auditgroup" uk-filter-control="filter: [data-audit-filter*='this-audit']; group: auditfilter;" onclick="clickingOnFindingFilter(this);">
				<button class="uk-button uk-button-default button-filter">this audit</button>
				<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="">
					<a class="sort-asc"></a>
				</span>
			</div>
			<div class="uk-width-1-5 auditgroup" uk-filter-control="filter: [data-audit-filter*='all']; group: auditfilter; " onclick="clickingOnFindingFilter(this);">
				<button class="uk-button uk-button-default button-filter">all audits</button>
				<span style="display:none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="">
					<a class="sort-asc"></a>
				</span>
			</div>
			<div class="uk-width-1-5 auditgroup">
				<button id="finding-modal-audit-stream-refresh" class="uk-button uk-button-default button-filter"  onclick="refreshFindingStream('{{ $type }}',{{ $auditid }},{{ $buildingid }},{{ $unitid }},{{ $amenityid }});">REFRESH</button>
			</div>
		</div> --}}

		<div class="uk-width-1-1 filter-button-set-right js-findings-buttons uk-grid uk-first-column" uk-grid="">
			<div class="uk-width-1-6 uk-active findinggroup uk-first-column" uk-filter-control="filter: [data-finding-filter*='my-finding']; group: findingfilter; " id="findings-mine" onclick="clickingOnFindingFilter(this, 0, 'mine');">
				<button id="findings-mine-button" class="uk-button uk-button-default button-filter button-filter-border-left" uk-tooltip title="ONLY DISPLAY YOUR FINDINGS">MINE</button>
				<span class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="" aria-expanded="false">
					<a class="sort-desc"></a>
				</span>
			</div>
			<div class="uk-width-1-6 findinggroup" uk-filter-control="filter: [data-finding-filter*='all']; group: findingfilter;" id="findings-everyone" onclick="clickingOnFindingFilter(this, 0, 'everyone');">
				<button id="findings-everyone-button" class="uk-button uk-button-default button-filter" style="padding-left: 5px; padding-right: 5px;" uk-tooltip title="DISPLAY EVERYONE'S FINDINGS">Everyone</button>
				<span style="display:none" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="" aria-expanded="false">
					<a class="sort-desc"></a>
				</span>
			</div>
			<div class="uk-width-1-6 uk-active auditgroup" uk-filter-control="filter: [data-audit-filter*='this-audit']; group: auditfilter;" id="findings-current" onclick="clickingOnFindingFilter(this, 0, 'current');">
				<button id="findings-current-button" class="uk-button uk-button-default button-filter" uk-tooltip title="ONLY DISPLAY CURRENT AUDIT'S FINDINGS">CURRENT</button>
				<span class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="" aria-expanded="false">
					<a class="sort-desc"></a>
				</span>
			</div>
			<div class="uk-width-1-6 auditgroup" uk-filter-control="filter: [data-audit-filter*='all']; group: auditfilter; " id="findings-all" onclick="clickingOnFindingFilter(this, 0, 'all');">
				<button id="findings-all-button" class="uk-button uk-button-default button-filter" uk-tooltip title="DISPLAY EVERY AUDIT'S FINDINGS">ALL</button>
				<span style="display:none" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="" aria-expanded="false">
					<a class="sort-desc"></a>
				</span>
			</div>
			<div class="uk-width-1-6 auditgroup" id="findings-location">
				<button id="finding-modal-audit-stream-location" class="uk-button uk-button-default button-filter" onclick="refreshLocationFindingStream('location',{{ $auditid }},{{ $buildingid }},{{ $unitid }},{{ $amenityid }});" uk-tooltip title="ONLY DISPLAY FINDINGS FOR THE SELECTED LOCATION">LOCATION</button>
				<button class="uk-hidden" id="finding-modal-audit-stream-location-sticky" onclick="refreshLocationFindingStreamSticky('location',{{ $auditid }},{{ $buildingid }},{{ $unitid }},{{ $amenityid }});"></button>
				<span style="display: none" id="location-findings-filter" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
					<a class="sort-desc"></a>
				</span>
			</div>
			<div class="uk-width-1-6 auditgroup">
				<button id="finding-modal-audit-stream-refresh" class="uk-button uk-button-default button-filter" onclick="refreshFindingStream('{{ $type }}',{{ $auditid }},{{ $buildingid }},{{ $unitid }},{{ $amenityid }});" uk-tooltip title="REFRESH THE LIST OF FINDINGS">REFRESH</button>
			</div>
		</div>

		<div class="uk-width-1-1 mmodal-findings-right-bottom">
			<div class="inspec-tools-tab-findings-container uk-panel uk-panel-scrollable uk-padding-remove js-findings" style="    height: 400px;">
				@if(count($findings))
				@foreach($findings as $finding)
				<div id="inspec-tools-tab-finding-{{ $finding->id }}" class="inspec-tools-tab-finding @if($finding->cancelled_at) cancelled @endif" @if($finding->cancelled_at) data-ordering-finding="x{{ $finding->id }}" @else data-ordering-finding="{{ $finding->id }}" @endif data-finding-id="{{ $finding->id }}" data-audit-filter="@if($finding->is_current_audit()) this-audit @endif all" data-finding-filter="@if(Auth::user()->id == $finding->user_id) my-finding @endif all" @if(!$finding->is_current_audit() || Auth::user()->id != $finding->user_id) style="display:none" @endif uk-grid>
					<div id="inspec-tools-tab-finding-sticky-{{ $finding->id }}" class="inspec-tools-tab-finding-sticky uk-width-1-1 uk-padding-remove findingstatus" style="display:none">
						<div class="uk-grid-match" uk-grid>
							<div class="uk-width-1-4 uk-padding-remove-top uk-padding-remove-left">
								<div>
									<i class="uk-inline {{ $finding->icon() }}"></i> <i class="uk-inline a-menu" onclick="expandFindingItems(this);"></i>
								</div>
							</div>
							<div class="uk-width-3-4 uk-padding-remove-top uk-padding-remove-right">
								<div>
									{{ formatDate($finding->date_of_finding) }}: FN#{{ $finding->id }}
								</div>
							</div>
						</div>
					</div>
					<div class="inspec-tools-tab-finding-info uk-width-1-1  uk-active findingstatus" style="padding-top: 15px;">
						<div class="uk-grid-match" uk-grid>
							<div class="uk-width-1-4 uk-padding-remove-top uk-padding-remove-left">
								<div class="uk-display-block">
									<i class="{{ $finding->icon() }}"></i><br>
									<span class="auditinfo">AUDIT {{ $finding->audit_id }}</span>
								</div>
								<div id="as-inspec-tools-finding-resolve-{{ $finding->id }}" class="uk-display-block" style="margin: 15px 0;">
									@can('access_auditor')
									@if(!$finding->cancelled_at)
									@if($finding->auditor_approved_resolution != 1)
									<button class="uk-button inspec-tools-findings-resolve uk-link" onclick="resolveFindingAS({{ $finding->id }})"><span class="a-circle"></span> RESOLVE</button>
									@else
									<button class="uk-button inspec-tools-findings-resolve uk-link" uk-tooltip="pos:top-left;title:RESOLVED ON {{ strtoupper(formatDate($finding->auditor_last_approved_resolution_at)) }};" onclick="resolveFindingAS({{ $finding->id }})"><span class="a-circle-checked"></span> RESOLVED</button>
									@endif
									@endif
									@else
									@if($finding->auditor_approved_resolution == 1)
									<button class="uk-button inspec-tools-findings-resolve uk-link" uk-tooltip="pos:top-left;title:RESOLVED ON {{ strtoupper(formatDate($finding->auditor_last_approved_resolution_at)) }};"><span class="a-circle-checked"></span> RESOLVED</button>
									@endif
									@endcan
								</div>
								<div class="inspec-tools-tab-finding-stats" style="margin: 0 0 15px 0;">
									<i class="a-bell"></i> <span id="inspec-tools-tab-finding-stat-reminders">{{ count($finding->followups) }}</span><br />
									<i class="a-comment"></i> {{ count($finding->comments) }}<br />

									<i class="a-file"></i> {{ count($finding->documents) }}<br />
									<i class="a-picture"></i> {{ count($finding->photos) }}<br />
									@if(env('APP_ENV') == 'local')
									@endIf
									@if(count($finding->followups) || count($finding->comments) || count($finding->documents) || count($finding->photos))
									<i class="a-menu" onclick="expandFindingItems(this);"></i>
									@endif
								</div>
							</div>
							<div class="uk-width-3-4 uk-padding-remove-right uk-padding-remove">
								@if(!$finding->cancelled_at)
								<div class="inspec-tools-tab-finding-top-actions" style="z-index:10">
									<i class="a-circle-plus use-hand-cursor"></i>
									<div uk-drop="mode: click" style="min-width: 315px; background-color: #ffffff;  ">
										<div class="uk-card uk-card-body uk-card-default uk-card-small">
											<div class="uk-drop-grid uk-child-width-1-4" uk-grid>
												<div class="icon-circle use-hand-cursor" onclick="addChildItem({{ $finding->id }}, 'followup')"><i class="a-bell-plus"></i></div>
												<div class="icon-circle use-hand-cursor"  onclick="addChildItem({{ $finding->id }}, 'comment')"><i class="a-comment-plus"></i></div>

												<div class="icon-circle use-hand-cursor"  onclick="addChildItem({{ $finding->id }}, 'document')"><i class="a-file-plus"></i></div>

												<div class="icon-circle use-hand-cursor"  onclick="addChildItem({{ $finding->id }}, 'photo')"><i class="a-picture"></i></div>
												@if(env('APP_ENV') == 'local')@endIf
											</div>
										</div>
									</div>
								</div>
								@else
								<div class="inspec-tools-tab-finding-top-actions">
									CANCELLED
								</div>
								@endif
								<div class="uk-width-1-1 uk-display-block uk-padding-remove inspec-tools-tab-finding-description"  style="z-index:auto">
									<p><small>{{ formatDate($finding->date_of_finding, "F j, Y") }}: FN#{{ $finding->id }}</small><br />
										<small>By {{ $finding->auditor->full_name() }}</small><br>
										@if($finding->amenity_inspection)<strong>{{ $finding->amenity_inspection->building_unit_name()}}</strong>@endif<br />
										@if($finding->amenity_inspection)
										{!! $finding->amenity_inspection->address() !!}
										@endIf
									</p>
									<p>@if($finding->amenity_inspection)
										@php
										$check_existing = $amenities->where('id', $finding->amenity_inspection_id)->first();
										$existing_records = null;
										$index = 0;
										if($check_existing && !is_null($check_existing->unit_id)) {
											$existing_records = $amenities->where('unit_id', $check_existing->unit_id)->where('amenity_id', $check_existing->amenity_id)->pluck('id')->toArray();
										} elseif($check_existing && !is_null($check_existing->building_id)) {
											$existing_records = $amenities->where('building_id', $check_existing->building_id)->where('amenity_id', $check_existing->amenity_id)->pluck('id')->toArray();
										} elseif($check_existing ) {
											$existing_records = $amenities->where('amenity_id', $check_existing->amenity_id)->pluck('id')->toArray();
										} else {
											$existing_records = [];
										}
										if(!is_null($existing_records)) {
											$index = array_search($finding->amenity_inspection_id, $existing_records);
											if($index == 0 && count($existing_records) <= 1) {
												$index = '';
											} else {
												$index = $index + 1;
											}
										}
										if($index == 0) {
											$index = '';
										}
										@endphp
										{{ $finding->amenity_inspection->building_unit_amenity_names() }} {{ $index }}<br />
										@endIf
										@if($finding->finding_type)
										{{ $finding->finding_type->name }}
										@endIf
									</p>
									<p>
										{{$finding->level_description()}}
									</p>
									@can('access_auditor')
									<div class="inspec-tools-tab-finding-actions">
										@if(!$finding->cancelled_at)
										<button class="uk-button uk-link" onclick="dynamicModalLoad('edit/finding/{{ $finding->id }}',0,0,0,2)"><i class="a-pencil-2"></i> EDIT</button>
										@endif
										@if($finding->cancelled_at)
										<button class="uk-button uk-link" onclick="restoreFindingAS({{ $finding->id }})"><i class="a-trash-3"></i> RESTORE</button>
										@else
										<button class="uk-button uk-link" onclick="cancelFindingAS({{ $finding->id }})"><i class="a-trash-3"></i> CANCEL</button>
										@endif
									</div>
									@endcan
								</div>
							</div>
						</div>
					</div>
				</div>
				@endforeach
				@else
				<div id="inspec-tools-tab-finding-" class="inspec-tools-tab-finding"   uk-grid>
					<h3 class="uk-margin-top">Sorry, no information is available for this project</h3>
				</div>
				@endif
			</div>
		</div>
	</div>
</div>
<script>
	$( document ).ready(function() {
		// $('.findinggroup.uk-active').trigger('click');
		// $('.auditgroup.uk-active').trigger('click');
			// window.findingModalRightMine = true;
			// window.findingModalRightCurrent = true;
			// window.findingModalRightEveryone = false;
			// window.findingModalRightAll = false;
			//everyone
			//all
			//location
			//refresh
			// debugger;
			// mineElement = document.getElementById('findings-mine');
			// currentElement = document.getElementById('findings-current');
			// everyoneElement = document.getElementById('findings-everyone');
			// allElement = document.getElementById('findings-all');
			if(!window.findingModalRightMine && !window.findingModalRightCurrent && !window.findingModalRightAll && !window.findingModalRightEveryone) {
				window.findingModalRightMine = true;
				window.findingModalRightCurrent = true;
				window.findingModalRightEveryone = false;
				window.findingModalRightAll = false;
				$( "#findings-everyone-mine" ).trigger( "click" );
				$( "#findings-everyone-current" ).trigger( "click" );
			}
			if(window.findingModalRightEveryone) {
				$( "#findings-everyone-button" ).trigger( "click" );
				// clickingOnFindingFilter(everyoneElement, 0, 'everyone');
			}
			if(window.findingModalRightAll) {
				$( "#findings-all-button" ).trigger( "click" );
			}
			if(window.findingModalRightLocation) {
				$('#findings-location').removeClass('uk-active').addClass('uk-active');
				$('#location-findings-filter').show();
			} else {
				$('#findings-location').removeClass('uk-active');
				$('#location-findings-filter').hide();
			}
		});

	@can('access_auditor')
	function resolveFindingAS(findingid){
		$.post('/findings/'+findingid+'/resolve', {
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data != 0){
				$('#as-inspec-tools-finding-resolve-'+findingid).html('<button class="uk-button inspec-tools-findings-resolve uk-link" uk-tooltip="pos:top-left;title:RESOLVED ON '+data.toUpperCase()+';" onclick="resolveFindingAS('+findingid+')"><span class="a-circle-checked">&nbsp; </span>RESOLVED</button>');
			}else{
				$('#as-inspec-tools-finding-resolve-'+findingid).html('<button class="uk-button inspec-tools-findings-resolve uk-link" onclick="resolveFindingAS('+findingid+')"><span class="a-circle">&nbsp; </span>RESOLVE</button>');
			}
		});
	}

	function cancelFindingAS(findingid){

		UIkit.modal.confirm('<div class="uk-grid"><div class="uk-width-1-1"><h2>Cancel Finding #'+findingid+'</h2></div><div class="uk-width-1-1"><hr class="dashed-hr uk-margin-bottom"><h3>Are you sure you want to cancel this finding? All its comments/photos/documents/followups will remain and the cancelled finding will be displayed at the bottom of the list.</h3></div>', {stack:1}).then(function() {

			$.post('/findings/'+findingid+'/cancel', {
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data==0){
					UIkit.modal.alert(data,{stack: true});
				} else {
					UIkit.notification('<span uk-icon="icon: check"></span> Finding Canceled', {pos:'top-right', timeout:1000, status:'success'});
					$('#finding-modal-audit-stream-refresh').trigger('click');
				}
			} );


		}, function () {
			console.log('Rejected.')
		});
	}

	function restoreFindingAS(findingid){

		UIkit.modal.confirm('<div class="uk-grid"><div class="uk-width-1-1"><h2>Restore Finding #'+findingid+'</h2></div><div class="uk-width-1-1"><hr class="dashed-hr uk-margin-bottom"><h3>Are you sure you want to restore this finding?</h3></div>', {stack:1}).then(function() {

			$.post('/findings/'+findingid+'/restore', {
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				if(data==0){
					UIkit.modal.alert(data,{stack: true});
				} else {
					UIkit.notification('<span uk-icon="icon: check"></span> Finding Restored', {pos:'top-right', timeout:1000, status:'success'});
					$('#finding-modal-audit-stream-refresh').trigger('click');
				}
			} );


		}, function () {
			console.log('Rejected.')
		});
	}
	@endcan

	$(".inspec-tools-tab-findings-container").on( 'scroll', function(){

		var offset = $(".inspec-tools-tab-findings-container").scrollTop();

		var currentFinding ="";
		var currentFindingId ="";
		var position = 0;
		var findingId= "";
		var currentItem ="";
		var currentItemId ="";
		var positionItem = 0;
		var itemId= "";
		var tmpPosition = -offset;
		var tmpPositionItem = - offset - 48;

		if ($(".inspec-tools-tab-findings-container").scrollTop() > 40) {
    	// console.log('scrolltop > 40');

    	$.each($(".inspec-tools-tab-finding-sticky"), function(index, item) {

    		currentFinding = $(item).closest("[data-finding-id], .inspec-tools-tab-finding");
    		currentFindingId = currentFinding.data('finding-id');
    		position = $(currentFinding).offset().top - $(currentFinding).offsetParent().offset().top;

    		if(position < 0){
    			if(position >= tmpPosition) {
    				tmpPosition = position;
    				findingId = currentFindingId;
    			}
    		}
    	});

    	$.each($(".inspec-tools-tab-finding-reply-sticky"), function(index, item) {

    		currentItem = $(item).closest("[data-parent-id], .inspec-tools-tab-finding-item");
    		currentItemId = currentItem.data('parent-id');
    		currentFindingId = currentItem.data('finding-id');
		    //console.log("currentItemId "+currentItemId, $('#inspec-tools-tab-finding-'+currentFindingId).offset().top);
		    //console.log("currentItem offset "+$(currentItem).offset().top+" offsetparent "+$(currentItem).offsetParent().offset().top);
		    positionItem = $(currentItem).offset().top - $(currentItem).offsetParent().offset().top;
			//console.log("currentItemId "+currentItemId+" | positionItem "+positionItem+" | tmpPositionItem "+tmpPositionItem);
			if(positionItem < 40){
				if(positionItem >= tmpPositionItem) {
					tmpPositionItem = positionItem;
					itemId = currentItemId;
	        		//console.log("SETTING itemid "+itemId+" tmpPositionItem "+tmpPositionItem);
	        	}
	        }
	      });

	    // console.log("Finding id: "+findingId+" | Item id: "+itemId);

    	//console.log("finding: "+findingId);
    	$('div[id^="inspec-tools-tab-finding-sticky-"]').not( 'div[id="inspec-tools-tab-finding-sticky-'+findingId+'"]' ).hide();
    	$('div[id^="inspec-tools-tab-finding-reply-sticky-"]').not( 'div[id="inspec-tools-tab-finding-reply-sticky-'+itemId+'"]' ).hide();

    	if($('#inspec-tools-tab-finding-'+findingId).attr('expanded')){
	    	//console.log('#inspec-tools-tab-finding-'+findingId+' expanded');

	    	$('#inspec-tools-tab-finding-sticky-'+findingId).show();
	    	$('#inspec-tools-tab-finding-sticky-'+findingId).css("margin-top", $(".inspec-tools-tab-findings-container").scrollTop());
	    }else{
			// hide that sticky
			//console.log('hiding #inspec-tools-tab-finding-sticky-'+findingId+'');
			$('#inspec-tools-tab-finding-sticky-'+findingId).hide();
		}

			// if($(".inspec-tools-tab-findings-container").find('#inspec-tools-tab-finding-item-'+itemId).attr('expanded')){
		 //        $(".inspec-tools-tab-findings-container").find('#inspec-tools-tab-finding-reply-sticky-'+itemId).show();
		 //        $(".inspec-tools-tab-findings-container").find('#inspec-tools-tab-finding-reply-sticky-'+itemId).css("margin-top", $(".inspec-tools-tab-findings-container").scrollTop());
			// }else{
			// 	// hide that sticky
			// 	//console.log('hiding #inspec-tools-tab-finding-sticky-'+findingId+'');
			// 	$(".inspec-tools-tab-findings-container").find('#inspec-tools-tab-finding-reply-sticky-'+itemId).hide();
			// }
		} else {
	    	// hide the sticky for all findings
	    	// console.log('scrolltop <= 40');
	    	$('div[id^="inspec-tools-tab-finding-sticky-"]').css("margin-top", 0);
	    	$('div[id^="inspec-tools-tab-finding-sticky-"]').hide();
	    }

	  });
</script>