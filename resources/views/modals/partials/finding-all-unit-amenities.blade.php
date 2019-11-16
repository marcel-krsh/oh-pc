<script>
	$('#amenity-list').scroll(function(){
		scrollPosAmenity = $('#amenity-list').scrollTop();
		console.log(scrollPosAmenity);
	});
</script>
<div id="amenity-list" class="uk-width-1-1 uk-panel">
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
<script>
	$( document ).ready(function() {
		toggleMineSticky();
	});
</script>