<style>
	.finding-modal-list-items {
		padding-top: 4px;
		padding-bottom: 10px;
		list-style-type: circle;
	}
	.calendar-selection-input {
		border: none;
		margin-left: 21px;
		font-size: 14px;
		margin-top: 3px;
		background: transparent;
	}
	.amenity-list-item {
		list-style: none;
	}
	.amenity-auditor {
		display: inline-block;
	}
	.amenity-auditor .auditor-badge {
		height: 20px;
		width: 20px;
		font-size: 10px;
		text-align: center;
		border-radius: 50%;
		border: 1px solid #50b8ec;
		background-color: #ffffff;
		color: #50b8ec;
		font-weight: 400;
		line-height: 21px;
		margin: 3px 3px 3px 3px;
	}
</style>

<script>
	window.findingModalSelectedType = '{{$type}}';
	<?php $passedAmenity = $amenity;
$passedBuilding = $building;
$passedUnit = $unit;
if ($amenity && $passedAmenity->building_id) {
    $buildingName = $passedAmenity->building_inspection()->building_name;
}
?>
</script>

<div id="modal-findings" class="uk-margin-top" style="height: 90%" >
	<div id="modal-findings-items-container">
		@include('audit_stream.audit_stream')
	</div>

	<div class="modal-findings-left" uk-filter="target: .js-filter-findings">
		<div class="modal-findings-left-bottom-container">
			<div class="modal-findings-left-bottom">
				<div id="modal-findings-filters" class="uk-margin uk-child-width-auto" uk-grid>
					<div class="uk-width-1-1 uk-padding-remove uk-inline">
						<button id="amenity-selection" class="uk-button button-finding-filter uk-width-1-1" type="button" onclick="amenityList()"><i id="amenity-selection-icon" class="a-arrow-small-up"></i> <span id="select-amenity-text">Select Amenity</span></button>
						<div id="amenity-list" class="uk-width-1-1 uk-panel-scrollable" style="display: none" uk-grid>
							<div class="uk-width-1-1 ">
								<ul class="uk-list uk-list-divider uk-margin-left uk-margin-top">
									<li class="s-{{$audit->project_ref}} amenity-list-item finding-modal-list-items @if($audit->hasAmenityInspectionAssigned()) uid-{{Auth::user()->id}} @endif"><strong>Site : {{$audit->address}}</strong>
									</li>
					        		@php // get the project level amenities
					        		$projectAmenities = $amenities->filter(function ($project){
					        			if(!is_null($project->project_id)){
																    return true; // not complete
																  } else {
																	return false; // complete
																}
															});
					        		$projectAmenities = $projectAmenities->sortBy('amenity_id')->sortBy('id');
					        		$currentAmenityId = 0;
					        		$amenityIncrement = array();
					        		@endphp
					        		@foreach($projectAmenities as $amenity)
					        		@php
					        		if(!array_key_exists($amenity->amenity_id, $amenityIncrement)){
					        				//if($currentAmenityId != $amenity->amenity_id) {
				        						// new amenity
					        			$currentAmenityId = $amenity->amenity_id;
					        			if($amenity->project_has_multiple()){
					        				$amenityIncrement[$currentAmenityId] = 1;
					        			} else {
					        				$amenityIncrement[$currentAmenityId] = 0;
					        			}
					        		} else {
				        						// same amenity - increment it.
					        			$amenityIncrement[$amenity->amenity_id]++;
					        		}
					        		@endphp
					        		<li id="amenity-inspection-{{$amenity->id}}" class=" s-{{$audit->project_ref}} aa-{{$amenity->amenity_id}} amenity-inspection-{{$amenity->id}} amenity-list-item finding-modal-list-items uid-{{$amenity->auditor_id}}" style="color : @if(is_null($amenity->completed_date_time)) #50b8ec @else #000 @endIf ">
					        			<a onClick="selectAmenity('{{$amenity->amenity_id}}','amenity-inspection-{{$amenity->id}}','{{$amenity->id}}','@if($amenity->auditor_id) {{$amenity->user->initials()}} @else NA @endIf : {{$amenity->amenity->amenity_description}} @if($amenityIncrement[$amenity->amenity_id] != 0){{$amenityIncrement[$amenity->amenity_id]}} @endif',{{$amenityIncrement[$amenity->amenity_id]}})" style="color : @if(is_null($amenity->completed_date_time)) #50b8ec @else #000 @endIf ">@if(is_null($amenity->completed_date_time)) <i class="a-circle"></i> @else <i class="a-circle-checked"></i> @endIf @if($amenity->auditor_id)
					        				<div class="amenity-auditor uk-margin-remove">
					        					<div class="amenity-auditor uk-margin-remove">
					        						<div uk-tooltip="pos:top-left;title:{{$amenity->user->full_name()}};" class="auditor-badge auditor-badge-blue use-hand-cursor no-float">
					        							{{$amenity->user->initials()}}
					        						</div>
					        					</div>
					        				</div>
					        				@else
					        				<i class="a-avatar-plus_1" uk-tooltip title="NEEDS ASSIGNED"></i>  @endif {{$amenity->amenity->amenity_description}} @if($amenityIncrement[$amenity->amenity_id] != 0){{$amenityIncrement[$amenity->amenity_id]}} @endif
					        			</a>
					        		</li>
					        		@endforeach
					        		@php // get the building level amenities
					        		$buildingAmenities = $amenities->filter(function ($building){
					        			if(!is_null($building->building_id)){
																    return true; // not complete
																  } else {
																	return false; // complete
																}
															});
					        		$buildingAmenities = $buildingAmenities->sortBy('building_id')->sortBy('amenity_id')->sortBy('id');
					        		$currentBuildingId = 0;
					        		$currentAmenityId = 0;
					        		$amenityIncrement = 1;
					        		@endphp
					        		@foreach($buildingAmenities as $amenity)
					        		@if($currentBuildingId != $amenity->building_id)
					        		<li class="b-{{$amenity->building_id}} amenity-list-item finding-modal-list-items @if($audit->hasAmenityInspectionAssigned($amenity->building_id)) uid-{{Auth::user()->id}} @endif"><strong>Building BIN: {{$amenity->building_key}}</strong></li>
					        		@php $currentBuildingId = $amenity->building_id; @endphp
					        		@endif
					        		@php
					        		if($currentAmenityId != $amenity->amenity_id) {
					        		// new amenity
					        			$currentAmenityId = $amenity->amenity_id;
					        			if($amenity->building_has_multiple()){
					        				$amenityIncrement = 1;
					        			} else {
					        				$amenityIncrement = '';
					        			}
					        		} else {
					      				// same amenity - increment it.
					        			$amenityIncrement++;
					        		}
					        		@endphp
					        		<li id="amenity-inspection-{{$amenity->id}}" class="b-{{$amenity->building_id}} aa-{{$amenity->amenity_id}} amenity-inspection-{{$amenity->id}} amenity-list-item finding-modal-list-items  uid-{{$amenity->auditor_id}}" style="color : @if(is_null($amenity->completed_date_time)) #50b8ec @else #000 @endIf ">@if(is_null($amenity->completed_date_time)) <i class="a-circle"></i> @else <i class="a-circle-checked"></i> @endIf @if($amenity->auditor_id)
					        			<div class="amenity-auditor uk-margin-remove">
					        				<div class="amenity-auditor uk-margin-remove">
					        					<div uk-tooltip="pos:top-left;title:{{$amenity->user->full_name()}};" class="auditor-badge auditor-badge-blue use-hand-cursor no-float">
					        						{{$amenity->user->initials()}}
					        					</div>
					        				</div>
					        			</div>
					        			@else
					        			<i class="a-avatar-plus_1" uk-tooltip title="NEEDS ASSIGNED"></i>  @endif
					        			<a onClick="selectAmenity('{{$amenity->amenity_id}}','amenity-inspection-{{$amenity->id}}','{{$amenity->id}}','@if($amenity->auditor_id) {{$amenity->user->initials()}} @else NA @endIf :{{$amenity->amenity->amenity_description}} {{$amenityIncrement}} ', {{$amenityIncrement}})" style="color : @if(is_null($amenity->completed_date_time)) #50b8ec @else #000 @endIf ">{{$amenity->amenity->amenity_description}} {{$amenityIncrement}}
					        			</a>
					        		</li>
					        		@endforeach
					        		@php // get the unit level amenities
					        		$unitAmenities = $amenities->filter(function ($unit){
					        			if(!is_null($unit->unit_id)){
																    return true; // not complete
																  } else {
																	return false; // complete
																}
															});
					        		$unitAmenities = $unitAmenities->sortBy('unit_id')->sortBy('amenity_id')->sortBy('id');
					        		$currentUnitId = 0;
					        		$currentAmenityId = 0;
					        		$amenityIncrement = 1;
					        		@endphp
					        		@foreach($unitAmenities as $amenity)
					        		@if($currentUnitId != $amenity->unit_id)
					        		<li class="u-{{$amenity->unit_id}} amenity-inspection-{{$amenity->id}} amenity-list-item finding-modal-list-items @if($audit->hasAmenityInspectionAssigned(null, $amenity->unit_id)) uid-{{Auth::user()->id}} @endif">
					        			@if($amenity->cached_unit())
					        			<strong>Unit : {{$amenity->cached_unit()->unit_name}} in BIN: {{$amenity->cached_unit()->building_key}} ADDRESS: {{$amenity->cached_unit()->address}}</strong>
					        		@endif</li>
					        		@php $currentUnitId = $amenity->unit_id; @endphp
					        		@endif
					        		@php
					        		if($currentAmenityId != $amenity->amenity_id) {
					        		// new amenity
					        			$currentAmenityId = $amenity->amenity_id;
					        			if($amenity->unit_has_multiple()){
					        				$amenityIncrement = 1;
					        			} else {
					        				$amenityIncrement = '';
					        			}
					        		} else {
					      				// same amenity - increment it.
					        			$amenityIncrement++;
					        		}
					        		@endphp
					        		<li id="amenity-inspection-{{$amenity->id}}" class="u-{{$amenity->unit_id}} amenity-list-item finding-modal-list-items aa-{{$amenity->amenity_id}} uid-{{$amenity->auditor_id}}" style="color : @if(is_null($amenity->completed_date_time)) #50b8ec @else #000 @endIf ">
					        			<a onClick="selectAmenity('{{$amenity->amenity_id}}','amenity-inspection-{{$amenity->id}}','{{$amenity->id}}','@if($amenity->auditor_id) {{$amenity->user->initials()}} @else NA @endIf :{{$amenity->amenity->amenity_description}} {{$amenityIncrement}}', {{$amenityIncrement}})"  style="color : @if(is_null($amenity->completed_date_time)) #50b8ec @else #000 @endIf ">
					        				@if(is_null($amenity->completed_date_time)) <i class="a-circle"></i> @else <i class="a-circle-checked"></i> @endIf @if($amenity->auditor_id)
					        				<div class="amenity-auditor uk-margin-remove">
					        					<div class="amenity-auditor uk-margin-remove">
					        						<div uk-tooltip="pos:top-left;title:{{$amenity->user->full_name()}};" class="auditor-badge auditor-badge-blue use-hand-cursor no-float">
					        							{{$amenity->user->initials()}}
					        						</div>
					        					</div>
					        				</div>
					        				@else
					        				<i class="a-avatar-plus_1" uk-tooltip title="NEEDS ASSIGNED"></i>  @endif {{$amenity->amenity->amenity_description}} {{$amenityIncrement}}
					        			</a>
					        		</li>
					        		@endforeach
					        	</ul>
					        </div>
					      </div>
					    </div>

					    <div class="uk-width-1-1 uk-padding-remove uk-margin-small uk-inline">
					    	<button id="type-selection"  class="uk-button button-finding-filter uk-width-1-1" type="button" onclick="typeList()"><i id="type-selection-icon" class="a-arrow-small-up"></i> <span id="select-type-text">Select Location</span></button>
					    	<div id="type-list" class="uk-width-1-1 uk-panel-scrollable" style="display: none">
					    		<div class="uk-column-1-3@m uk-column-1-2@s ">
					    			<ul >
					    				<!-- Site is always the first one -->
					    				@php
					    				$siteCompleteTest = $amenities->filter(function ($site) {
					    					if(!is_null($site->project_id) && is_null($site->completed_date_time)){
					    						return true;
					    				// not complete
					    					} else {
					    						return false;
					    			// complete
					    					}
					    				});
					    	// if there are any returned - they are not all complete.
					    				if(count($siteCompleteTest)>0) {
					    					$siteComplete = 0;
					    	// not complete
					    				} else {
					    					$siteComplete = 1;
					    // complete
					    				}
					    				@endphp
					    				<li class="uk-column-span uk-margin-top uk-margin-bottom use-hand-cursor" onclick="filterAmenities('s-{{$audit->project_ref}}','Site: {{$audit->project->address->basic_address()}}')" style="color : @if($siteComplete == 1) #000 @else #50b8ec @endIf " >@if($siteComplete == 1) <i class="a-circle-checked"></i> @else <i class="a-circle"></i>@endIf Site: {{$audit->project->address->basic_address()}}
					    				</li>
					    				<hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">

					    				@foreach($buildings as $type)
					    				@if(!is_null($type->building_id))
					    				<li class="uk-column-span uk-margin-top uk-margin-bottom use-hand-cursor" onclick="filterAmenities('b-{{$type->building_id}}','Building BIN: {{$type->building_key}}, NAME: {{$type->building_name}}, ADDRESS: {{$type->address}}')">@if($type->complete == 0 || is_null($type->complete)) <i class="a-circle" style="color: #50b8ec" ></i> @else <i class="a-circle-checked"></i> @endIf <strong style="color : @if($type->complete == 1) #000 @else #50b8ec @endIf "> Building BIN:{{$type->building_key}} NAME: {{$type->building_name}} ADDRESS: {{$type->address}}</strong>
					    				</li>
					    				@php
					    				$buildingUnits = $units->filter(function ($unit) use ($type){
					    					if($unit->building_id == $type->building_id){
					    						return true;
					    					} else {
					    						return false;
					    					}
					    				});
					    				@endphp
					    				@if($buildingUnits)
					    				<ul class="uk-margin-left">
					    					@forEach($buildingUnits as $bu)
					    					<li class="use-hand-cursor uk-column-span uk-margin" onclick="filterAmenities('u-{{$bu->unit_id}}','Unit {{$bu->unit_name}} in BIN:{{$bu->building_key}} ')" style="color : @if($bu->complete == 1) #000 @else #50b8ec @endIf ">&nbsp;&nbsp;&nbsp;@if($bu->complete == 0 || is_null($bu->complete)) <i class="a-circle" style="color: #50b8ec" ></i> @else <i class="a-circle-checked"></i> @endIf<i class="a-buildings-2"></i> Unit {{$bu->unit_name}}</li>
					    					@endforeach
					    				</ul>
					    				<hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
					    				@else
					    				<hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top">
					    				@endIf
					    				@endif
					    				@endforeach
					    			</ul>
					    		</div>
					    	</div>
					    </div>

					    <div class="uk-width-1-1 uk-padding-remove uk-margin-small uk-inline">
					    	<div class="uk-width-1-1 uk-padding-remove uk-first-column">
					    		<div class="uk-inline uk-button button-finding-filter ">
					    			<span class="uk-form-icon" ><i class="a-calendar-pencil" style="color:#000"></i></span>
					    			<input type="text" id="finding-date" name="date" class=" flatpickr flatpickr-input calendar-selection-input uk-width-1-1"  readonly="readonly">
					    		</div>

					    		<script type="text/javascript">
					    			flatpickr.defaultConfig.animate = window.navigator.userAgent.indexOf('MSIE') === -1;
					    			flatpickr("#finding-date", {
					    				altInput: true,
					    				altFormat: "F j, Y",
					    				dateFormat: "Y-m-d",
					    				defaultDate: "{{date('Y-m-d',time())}}",
					    			});
					    		</script>

					    	</div>
					    </div>
					  </div>
					  <div class="uk-margin-remove" uk-grid>
					  	<div class="uk-width-1-1 uk-padding-remove">
					  		<button class="uk-button uk-button-primary button-finding-filter uk-width-1-1 @if(!$checkDoneAddingFindings) uk-modal-close @endif" <?php /* @if($checkDoneAddingFindings) onclick="completionCheck();return false;" @endif */?> onClick="dynamicModalClose()">DONE ADDING FINDINGS</button>
					  	</div>
					  </div>
					</div>
				</div>

				<!-- FINDING TYPE LISTS -->

				<div class="modal-findings-left-main-container" style="display:none">
					<div class="modal-findings-left-main">
						<div id="modal-findings-list" class="uk-margin uk-child-width-auto uk-grid">

						</div>
					</div>
				</div>

				<!-- END FINDING TYPE LISTS -->


				<!-- 		TOP LEFT BAR FILTERS -->



				<div class="modal-findings-left-top" uk-grid>
					<div class="uk-width-1-1 filter-button-set">
						<div uk-grid>
							<div class="uk-inline uk-width-1-2">
								<div uk-grid>
									<div class="uk-width-1-4">
										<button id="mine-filter-button" uk-tooltip="title:SHOW MY AMENITIES AND LOCATIONS;" class="uk-button uk-button-default button-filter" style="border-left: 1px solid;border-right: 0px;" onclick=" console.log(window.findingModalSelectedMine);
										if(window.findingModalSelectedMine == 'true'){
											window.findingModalSelectedMine='false';
											$('.amenity-list-item.finding-modal-list-items:not(.uid-{{Auth::user()->id}}').addClass('notmine');
											$('#mine-filter-button').addClass('uk-active');
										}else{
											window.findingModalSelectedMine='true';
											$('.amenity-list-item.finding-modal-list-items:not(.uid-{{Auth::user()->id}}').removeClass('notmine');
											$('#mine-filter-button').removeClass('uk-active');
										}">MINE</button>
									</div>
									<div class="uk-width-3-4">
										<input type='text' name="finding-description" id="finding-description" class="uk-input button-filter" placeholder="ENTER FINDING DESCRIPTION" type="text">
									</div>
								</div>
							</div>
							<div class="uk-inline uk-width-1-2">
								<div uk-grid>
									<div class="uk-width-1-4">
										<button id="all-filter-button" data-uk-tooltip="{pos:'bottom'}" class="uk-button uk-button-default button-filter uk-active"  title="Show All Findings (Unfiltered)" onclick="window.findingModalSelectedType='all'; if(window.findingModalSelectedAmenity != ''){ filterFindingTypes(); } $('#all-findings-filter').fadeOut(); $('#lt-findings-filter').fadeOut();$('#nlt-findings-filter').fadeOut();$('#file-findings-filter').fadeOut();$('#all-findings-filter').fadeIn(); $('#lt-filter-button').removeClass('uk-active'); $('#nlt-filter-button').removeClass('uk-active'); $('#file-filter-button').removeClass('uk-active'); $('#all-filter-button').removeClass('uk-active'); $('#all-filter-button').addClass('uk-active');"><i class="uk-icon-asterisk"></i></button>
										<span id="all-findings-filter"  class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false" @if($type != 'all') style="display: none;" @endIf>
											<a  class="sort-desc"></a>
										</span>
									</div>
									<div class="uk-width-1-4">
										<button id="file-filter-button" data-uk-tooltip="{pos:'bottom'}" class="uk-button uk-button-default button-filter" title="Show File Findings Only" onclick="window.findingModalSelectedType='file'; if(window.findingModalSelectedAmenity != ''){ filterFindingTypes(); } setTimeout(function(){$('#all-findings-filter').fadeOut(); $('#lt-findings-filter').fadeOut();$('#nlt-findings-filter').fadeOut();$('#file-findings-filter').fadeOut();$('#file-findings-filter').fadeIn(); $('#lt-filter-button').removeClass('uk-active'); $('#nlt-filter-button').removeClass('uk-active'); $('#file-filter-button').removeClass('uk-active'); $('#all-filter-button').removeClass('uk-active'); $('#file-filter-button').addClass('uk-active');},.85);"><i class="a-folder"></i></button>
										<span id="file-findings-filter" @if($type != 'file') style="display: none;" @endIf  class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
											<a  class="sort-desc"></a>
										</span>
									</div>
									<div class="uk-width-1-4">
										<button id="nlt-filter-button" data-uk-tooltip="{pos:'bottom'}" class="uk-button uk-button-default button-filter" title="Show Non-life Threatning Findings Only" onclick="window.findingModalSelectedType='nlt'; if(window.findingModalSelectedAmenity != ''){ filterFindingTypes(); } setTimeout(function(){$('#all-findings-filter').fadeOut();  $('#lt-findings-filter').fadeOut();$('#nlt-findings-filter').fadeOut();$('#file-findings-filter').fadeOut();$('#nlt-findings-filter').fadeIn(); $('#lt-filter-button').removeClass('uk-active'); $('#nlt-filter-button').removeClass('uk-active'); $('#file-filter-button').removeClass('uk-active'); $('#all-filter-button').removeClass('uk-active'); $('#nlt-filter-button').addClass('uk-active');},.85);"><i class="a-booboo"></i></button>
										<span id="nlt-findings-filter" @if($type != 'nlt') style="display: none;" @endIf class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
											<a  class="sort-desc"></a>
										</span>
									</div>
									<div class="uk-width-1-4">
										<button id="lt-filter-button" data-uk-tooltip="{pos:'bottom'}" class="uk-button uk-button-default button-filter" title="Show Life Threatning Findings Only" onclick="window.findingModalSelectedType='lt'; if(window.findingModalSelectedAmenity != ''){ filterFindingTypes(); } setTimeout(function(){$('#all-findings-filter').fadeOut(); $('#lt-findings-filter').fadeOut();$('#nlt-findings-filter').fadeOut();$('#file-findings-filter').fadeOut();$('#lt-findings-filter').fadeIn(); $('#lt-filter-button').removeClass('uk-active'); $('#nlt-filter-button').removeClass('uk-active'); $('#file-filter-button').removeClass('uk-active'); $('#all-filter-button').removeClass('uk-active'); $('#lt-filter-button').addClass('uk-active');},.85);"><i class="a-skull"></i></button>
										<span id="lt-findings-filter" @if($type != 'lt') style="display: none;" @endIf  class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
											<a  class="sort-desc"></a>
										</span>
									</div>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
		</script>



		@include('templates.modal-findings-items')


		<div id="modal-findings-completion-check" uk-modal>
			<div class="uk-modal-dialog uk-modal-body uk-modal-content" uk-overflow-auto>
				<a class="uk-modal-close-default" uk-close></a>
				<div uk-grid>
					<div class="uk-width-1-2  uk-margin-medium-top">
						<p>Have you finished inspecting all items for that building/unit/common area?</p>
						<div class="uk-padding-remove" uk-grid>
							<div class="uk-width-1-1 uk-padding-remove uk-margin-medium-top">
								<button class="uk-button uk-button-primary uk-margin-left uk-margin-right uk-padding-remove uk-margin-remove uk-width-1-1">Yes, Mark as Complete and Submit to Lead.</button>
							</div>
							<div class="uk-width-1-1 uk-padding-remove uk-margin-medium-top">
								<button class="uk-button uk-button-primary uk-padding-remove uk-margin-remove uk-width-1-1">Just the Items I have Findings For.</button>
							</div>
							<div class="uk-width-1-1 uk-padding-remove uk-margin-medium-top">
								<button class="uk-button uk-button-default uk-padding-remove uk-margin-remove uk-width-1-1 uk-modal-close">No, I am still working on it.</button>
							</div>
						</div>
					</div>
					<div class="uk-width-1-2  uk-margin-medium-top">
						<div>bulleted list of items that have not had any findings here<br />
							<ul class="uk-list">
								<li>item</li>
								<li>item</li>
							</ul>
						</div>
					</div>
				</div>
			</div>

			<style>
				.notmine{display:none;}
			</style>
			<script>
					window.findingModalSelectedAmenity = ''; //we do this to ensure we don't filter to a previous selection that is not relevant.
					window.findingModalSelectedAmenityInspection = '';
					function clickDefault(){

						@if(!is_null($passedAmenity))

						// set filter text for amenity
						window.findingModalSelectedAmenity = 'a-{{$passedAmenity->amenity_id}}';
						window.findingModalSelectedAmenityInspection = 'amenity-inspection-{{$passedAmenity->id}}';
						window.selectedAmenityInspection = '{{$passedAmenity->id}}';
						<?php
if ($passedAmenity->project_id) {
    // is a project type
    $locationType = 's-' . $passedAmenity->project_ref;
    $locationText = "Site picked";

} elseif ($passedAmenity->building_id) {
    // is a building
    $locationType = 'b-' . $passedAmenity->building_id;
    $locationText = "Building BIN: " . $passedAmenity->building_id . ", NAME: " . addslashes($buildingName);
    if ($passedAmenity->building->address) {
        $locationText .= ", ADDRESS: " . addslashes($passedAmenity->building->address->line_1);
    } else {
        $locationText .= ", NO ADDRESSS SET IN DEVCO.";
    }
    echo "console.log('Passed amenity is a building type');";
} else {
    // is a unit
    $locationType = 'u-' . $passedAmenity->unit_id;
    $locationText = "Unit Name: " . $passedAmenity->cached_unit()->unit_name . ", in BIN: " . $passedAmenity->building_key;
    if ($passedAmenity->unit->building->address) {
        $locationText .= " at ADDRESS: " . $passedAmenity->unit->building->address->line_1;
    } else {
        $locationText .= ", NO ADDRESSS SET IN DEVCO.";
    }
}
?>
						// set filter text for drop lists
						filterAmenities('{{$locationType}}','{!!$locationText!!}',0,0,$('#amenity-inspection-{{$passedAmenity->id}}').text());

						window.findingModalSelectedLocationType = '{{$locationType}}';

						//filterFindingTypes();
						console.log('Filtering to amenity id:a-{{$passedAmenity->amenity_id}} ({{$passedAmenity->amenity->amenity_description}}) for amenity inspection {{$passedAmenity->id}} with a location type target of '+window.findingModalSelectedLocationType+' further filtered to show '+window.findingModalSelectedType+' findings.');


						@elseif(!is_null($passedUnit))
						@if($toplevel != 1)
						console.log('Filtering to unit id:u-{{$passedUnit->unit_id}}');
		        		// set filter test for type
		        		<?php
$locationType = 'u-' . $passedUnit->unit_id;
?>
						// set filter text for type
						window.findingModalSelectedLocationType = '{{$locationType}}';
						filterAmenities('u-{{$passedUnit->unit_id}}', 'Unit NAME: {{$passedUnit->unit_name}} in Building BIN:{{$passedUnit->building_key}} ADDRESS: @if($passedUnit->building->address) {{$passedUnit->building->address->line_1}} @else NO ADDRESS SET IN DEVCO @endIf',0);
		        		// filter to type and allita type (nlt, lt, file)

		        		@else
		        		console.log('Filtering building-level amenities {{$passedBuilding->building_id}}}');
		        		filterAmenities('b-{{$passedBuilding->building_id}}', 'Building BIN:{{$passedBuilding->building_key}} NAME: {{$passedBuilding->building_name}}',0,1);
		        		@endif

		        		@elseif(!is_null($passedBuilding))
		        		@if($toplevel != 1)
		        		console.log('Filtering to building id:b-{{$passedBuilding->building_id}}');
		        		<?php
$locationType = 'b-' . $passedBuilding->building_id;
?>
								// set filter text for type
								window.findingModalSelectedLocationType = '{{$locationType}}';


		        		// set filter test for type
		        		@if($passedBuilding->building)
		        		filterAmenities('b-{{$passedBuilding->building_id}}', 'Building BIN:{{$passedBuilding->building_key}} NAME: {{$passedBuilding->building_name}}, ADDRESS: @if($passedBuilding->building->address){{$passedBuilding->building->address->line_1}} @else NO ADDRESS SET IN DEVCO @endIf',0,1);
		        		@else
		        		filterAmenities('b-{{$passedBuilding->building_id}}', 'Building BIN:{{$passedBuilding->building_key}} NAME: {{$passedBuilding->building_name}}',0,1);
		        		@endif
		        		//filterAmenities('b-16713','Building BIN: 93670, NAME: OH-11-00214, ADDRESS: ')

		        		// filter to type and allita type (nlt, lt, file)
		        		@else
		        		console.log('Filtering project-level amenities {{$audit->project_ref}}');
		        		filterAmenities('s-{{$audit->project_ref}}', 'Site: {{$audit->project->address->basic_address()}}',0,1);
		        		@endif
		        		@else
		        		//console.log('filtering by project-level');
		        		// setTimeout(function() {
		        		// 	typeList();
		        		// }, .7);

		        		@endif

		        		window.findingModalSelectedAmenityDate = $('#finding-date').val();

		        		$('#'+window.findingModalSelectedType+'-filter-button').trigger('click');

		        		console.log("select mine: "+window.findingModalSelectedMine);
		        		if(window.findingModalSelectedMine == 'true'){
		        			window.findingModalSelectedMine = 'false';
		        			$('#mine-filter-button').addClass('uk-active');

								// only already visible elements?

								$('.amenity-list-item.finding-modal-list-items').not('.uid-{{Auth::user()->id}}').addClass('notmine');
							}else{
								window.findingModalSelectedMine = 'true';
								$('.amenity-list-item.finding-modal-list-items').not('.uid-{{Auth::user()->id}}').removeClass('notmine');
								$('#mine-filter-button').removeClass('uk-active');
							}

						}




        // filter findings based on class
        $('#finding-description').on('keyup', function () {
        	if($('#finding-description').val().length > 2 && window.findingModalSelectedAmenity != ''){
        		filterFindingTypes();
        	}else if($('#finding-description').val().length == 0 && window.findingModalSelectedAmenity != ''){
        		filterFindingTypes();
        	}
        });

        function amenityList(){
        	// make sure type is up
        	if($('#type-selection-icon').hasClass('a-arrow-small-down')){
        		$('#type-selection-icon').removeClass('a-arrow-small-up');
        		$('#type-selection-icon').addClass('a-arrow-small-down');
        		$('#type-selection-icon').slideToggle();
        	}
        	if($('#amenity-selection-icon').hasClass('a-arrow-small-up')){
        		$('#amenity-selection-icon').removeClass('a-arrow-small-up');
        		$('#amenity-selection-icon').addClass('a-arrow-small-down');
        		$('#select-amenity-text').text('Select Amenity');
        		$('.modal-findings-left-main-container').slideUp();
        	} else {
        		$('#amenity-selection-icon').addClass('a-arrow-small-up');
        		$('#amenity-selection-icon').removeClass('a-arrow-small-down');
        	}
        	$('#amenity-list').slideToggle();
        }
        function typeList(){
			// make sure amenities is up
			if($('#amenity-selection-icon').hasClass('a-arrow-small-down')){
				$('#amenity-selection-icon').addClass('a-arrow-small-up');
				$('#amenity-selection-icon').removeClass('a-arrow-small-down');
				$('#amenity-list').slideToggle();
			}

			if($('#type-selection-icon').hasClass('a-arrow-small-up')){
				$('#type-selection-icon').removeClass('a-arrow-small-up');
				$('#type-selection-icon').addClass('a-arrow-small-down');
			} else {
				$('#type-selection-icon').addClass('a-arrow-small-up');
				$('#type-selection-icon').removeClass('a-arrow-small-down');
			}
			$('#type-list').load('https://www.google.com');
			$('#type-list').slideToggle();
		}
		function selectAmenity(amenity_id,amenity_inspection_class,amenity_inspection_id,display='selected',amenity_increment=''){
			$('.modal-findings-left-main-container').slideDown();
			amenityList();
			// filter the findings to the selection
			$('#select-amenity-text').text(display);

			console.log('Selected '+amenity_id);
			window.findingModalSelectedAmenity = amenity_id;
			window.findingModalSelectedAmenityIncrement = amenity_increment;
			window.findingModalSelectedAmenityInspection = amenity_inspection_class;
			window.selectedAmenityInspection = amenity_inspection_id;
			filterFindingTypes();
		}
		function filterAmenities(type_id,display='selected',closeType=1,openAmenity=1,selectAmenityText="Select Amenity"){

			if(type_id == 'all'){
				console.log('Filtering amenities list to all.');
				$('.amenity-list-item').show();
				if(closeType == 1){
					console.log('Closing the location type list.');
					typeList();
				}
				if(openAmenity == 1){
					console.log('Opening the full amenity list.')
					amenityList();
				}
			} else {
				$('.amenity-list-item').hide();
				$('.'+type_id).show();
				if(closeType == 1){
					console.log('Closing the location type list.');
					typeList();
				}
				if(openAmenity == 1){
					console.log('Opening the amenity type list filtered to '+type_id+' items.');
					amenityList();
				}
			}
			$('#select-type-text').text(display);
			$('.modal-findings-left-main-container').slideUp();
			$('#select-amenity-text').text(selectAmenityText);

		}

		function filterFindingTypes(){
			debugger;
			var searchString = $('#finding-description').val();
				// load into the div
				console.log('loading /modals/findings_list/'+window.findingModalSelectedType+'/'+window.selectedAmenityInspection+'?search='+searchString);
				$('#modal-findings-list').html('');
				var tempdiv = '<div>';
				tempdiv = tempdiv + '<div style="height:200px; width:610px; text-align:center;"><div uk-spinner style="margin-left:auto;margin-right:auto;margin-top:30%"></div></div>';
				tempdiv = tempdiv + '</div>';
				$('#modal-findings-list').html(tempdiv);

				$('#modal-findings-list').load('/modals/findings_list/'+window.findingModalSelectedType+'/'+window.selectedAmenityInspection+'?search='+searchString, function(response, status, xhr) {
					if (status == "error") {
						if(xhr.status == "401") {
							var msg = "<h2>SERVER ERROR 401 :(</h2><p>Looks like your login session has expired. Please refresh your browser window to login again.</p>";
						} else if( xhr.status == "500"){
							var msg = "<h2>SERVER ERROR 500 :(</h2><p>I ran into trouble processing your request - the server says it had an error.</p><p>It looks like everything else is working though. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.</p>";
						} else {
							var msg = "<h2>"+xhr.status + " " + xhr.statusText +"</h2><p>Sorry, but there was an error and it isn't one I was expecting. Please contact support and let them know how you came to this page and what you clicked on to trigger this message.";
						}

						UIkit.modal.alert(msg);
					} else {
						$('.modal-findings-left-main-container').fadeIn();
					}
				});

			}
		// A $( document ).ready() block.
		$( document ).ready(function() {
			console.log( "Modal Loaded!" );
			clickDefault();
		});


	</script>