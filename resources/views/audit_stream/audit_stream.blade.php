<div class="modal-findings-right" uk-filter="target: .js-findings">
	
		<div class="modal-findings-right-top">
		    <div class="uk-width-1-1 filter-button-set-right js-findings-buttons" uk-grid>
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
	                <button id="finding-modal-audit-stream-refresh" class="uk-button uk-button-default button-filter"  onclick="refreshFindingStream('{{$type}}',{{$auditid}},{{$buildingid}},{{$unitid}},{{$amenityid}});">REFRESH</button>
	                
		        </div>
		    </div>
		</div>
		   
	    <div class="modal-findings-right-bottom-container">
			<div class="modal-findings-right-bottom">
				<div class="inspec-tools-tab-findings-container uk-panel uk-panel-scrollable uk-padding-remove js-findings">
					@if(count($findings))
					@foreach($findings as $finding)

					<div id="inspec-tools-tab-finding-{{$finding->id}}" class="inspec-tools-tab-finding" data-ordering-finding="{{$finding->id}}" data-finding-id="{{$finding->id}}" data-audit-filter="@if($finding->is_current_audit()) this-audit @endif all" data-finding-filter="@if(Auth::user()->id == $finding->user_id) my-finding @endif all" @if(!$finding->is_current_audit() || Auth::user()->id != $finding->user_id) style="display:none" @endif uk-grid>
			        	<div id="inspec-tools-tab-finding-sticky-{{$finding->id}}" class="inspec-tools-tab-finding-sticky uk-width-1-1 uk-padding-remove findingstatus" style="display:none">
			        		<div class="uk-grid-match" uk-grid>
								<div class="uk-width-1-4 uk-padding-remove-top uk-padding-remove-left">
									<div>
										<i class="uk-inline {{$finding->icon()}}"></i> <i class="uk-inline a-menu" onclick="expandFindingItems(this);"></i>
									</div>
								</div>
								<div class="uk-width-3-4 uk-padding-remove-top uk-padding-remove-right">
									<div>
										{{formatDate($finding->date_of_finding)}}: FN#{{$finding->id}}
										<div class="uk-float-right"><i class="a-circle-plus use-hand-cursor"></i></div>
									</div>
								</div>
			        		</div>
			        	</div>

						<div class="inspec-tools-tab-finding-info uk-width-1-1  uk-active findingstatus" style="padding-top: 15px;">
		    				<div class="uk-grid-match" uk-grid>
				    			<div class="uk-width-1-4 uk-padding-remove-top uk-padding-remove-left">
				    				<div class="uk-display-block">
					    				<i class="{{$finding->icon()}}"></i><br>
					    				<span class="auditinfo">AUDIT {{$finding->audit_id}}</span>
					    			</div>
					    			<div id="inspec-tools-finding-resolve-{{$finding->id}}" class="uk-display-block" style="margin: 15px 0;">
					    			@can('access_auditor')
					    				@if($finding->auditor_approved_resolution != 1)
					    				<button class="uk-button inspec-tools-findings-resolve uk-link" onclick="resolveFinding({{$finding->id}})"><span class="a-circle">
									    	 &nbsp; </span>RESOLVE</button>
									   	@else
									   	<button class="uk-button inspec-tools-findings-resolve uk-link" uk-tooltip="pos:top-left;title:RESOLVED ON {{strtoupper(formatDate($finding->auditor_last_approved_resolution_at))}};" onclick="resolveFinding({{$finding->id}})"><span class="a-circle-checked">
									    	 &nbsp; </span>RESOLVED</button>
									   	
									    @endif
									@else
										@if($finding->auditor_approved_resolution == 1)
					    				<button class="uk-button inspec-tools-findings-resolve uk-link" uk-tooltip="pos:top-left;title:RESOLVED ON {{strtoupper(formatDate($finding->auditor_last_approved_resolution_at))}};"><span class="a-circle-checked">
									    	 &nbsp; </span>RESOLVED</button>
									    @endif
									@endcan
									</div>
									<div class="inspec-tools-tab-finding-stats" style="margin: 0 0 15px 0;">
										<i class="a-bell"></i> <span id="inspec-tools-tab-finding-stat-reminders">{{count($finding->followups)}}</span><br />
										<i class="a-comment"></i> {{count($finding->comments)}}<br />
										<i class="a-file"></i> {{count($finding->documents)}}<br />
										<i class="a-picture"></i> {{count($finding->photos)}}<br />
										@if(count($finding->followups) || count($finding->comments) || count($finding->documents) || count($finding->photos))
										<i class="a-menu" onclick="expandFindingItems(this);"></i>
										@endif
									</div>
				    			</div>
				    			<div class="uk-width-3-4 uk-padding-remove-right uk-padding-remove">
				    				<div class="uk-width-1-1 uk-display-block uk-padding-remove inspec-tools-tab-finding-description">
					    				<p>{{formatDate($finding->date_of_finding)}}: FN#{{$finding->id}}<br />
					    					By {{$finding->auditor->full_name()}}<br>
					    					{!!$finding->amenity_inspection->address()!!}
					    				</p>
					    				<p>{{$finding->amenity_inspection->building_unit_amenity_names()}}<br />{{$finding->finding_type->name}}</p>
					    				<div class="inspec-tools-tab-finding-actions">
										    <button class="uk-button uk-link"><i class="a-pencil-2"></i> EDIT</button>
					    					<button class="uk-button uk-link"><i class="a-trash-3"></i> DELETE</button>
					    				</div>
					    				<div class="inspec-tools-tab-finding-top-actions">
					    					<i class="a-circle-plus use-hand-cursor"></i>
										    <div uk-drop="mode: click" style="min-width: 315px;">
										        <div class="uk-card uk-card-body uk-card-default uk-card-small">
										    	 	<div class="uk-drop-grid uk-child-width-1-4" uk-grid>
										    	 		<div class="icon-circle use-hand-cursor" onclick="addChildItem({{$finding->id}}, 'followup')"><i class="a-bell-plus"></i></div>
										    	 		<div class="icon-circle use-hand-cursor"  onclick="addChildItem({{$finding->id}}, 'comment')"><i class="a-comment-plus"></i></div>
										    	 		<div class="icon-circle use-hand-cursor"  onclick="addChildItem({{$finding->id}}, 'document')"><i class="a-file-plus"></i></div>
										    	 		<div class="icon-circle use-hand-cursor"  onclick="addChildItem({{$finding->id}}, 'photo')"><i class="a-picture"></i></div>
										    	 	</div>
										        </div>
										    </div>
					    				</div>
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
		$('.findinggroup.uk-active').trigger('click');
		$('.auditgroup.uk-active').trigger('click');
	});

	@can('access_auditor')
	function resolveFinding(findingid){
		$.post('/findings/'+findingid+'/resolve', {
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			if(data != 0){
				$('#inspec-tools-finding-resolve-'+findingid).html('<button class="uk-button inspec-tools-findings-resolve uk-link" uk-tooltip="pos:top-left;title:RESOLVED ON '+data.toUpperCase()+';" onclick="resolveFinding('+findingid+')"><span class="a-circle-checked">&nbsp; </span>RESOLVED</button>');
			}else{
				$('#inspec-tools-finding-resolve-'+findingid).html('<button class="uk-button inspec-tools-findings-resolve uk-link" onclick="resolveFinding('+findingid+')"><span class="a-circle">&nbsp; </span>RESOLVE</button>');
			}
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
			    positionItem = $(currentItem).offset().top - $(currentItem).offsetParent().offset().top;
				// console.log("currentItemId "+currentItemId+" | positionItem "+positionItem+" | tmpPositionItem "+tmpPositionItem);
		        if(positionItem < 40){
		        	if(positionItem >= tmpPositionItem) {
		        		tmpPositionItem = positionItem;
		        		itemId = currentItemId;
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
			
			if($(".inspec-tools-tab-findings-container").find('#inspec-tools-tab-finding-item-'+itemId).attr('expanded')){
		    	
		        $(".inspec-tools-tab-findings-container").find('#inspec-tools-tab-finding-reply-sticky-'+itemId).show();
		        $(".inspec-tools-tab-findings-container").find('#inspec-tools-tab-finding-reply-sticky-'+itemId).css("margin-top", $(".inspec-tools-tab-findings-container").scrollTop());
			}else{
				// hide that sticky
				//console.log('hiding #inspec-tools-tab-finding-sticky-'+findingId+'');
				$(".inspec-tools-tab-findings-container").find('#inspec-tools-tab-finding-reply-sticky-'+itemId).hide();
			}
	    } else {
	    	// hide the sticky for all findings
	    	// console.log('scrolltop <= 40');
		    $('div[id^="inspec-tools-tab-finding-sticky-"]').css("margin-top", 0);
	    	$('div[id^="inspec-tools-tab-finding-sticky-"]').hide();
	    }

	});  
</script>