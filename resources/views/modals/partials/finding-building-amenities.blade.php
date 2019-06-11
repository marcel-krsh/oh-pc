<script>
	$('#amenity-list').scroll(function(){
	    scrollPosAmenity = $('#amenity-list').scrollTop();
	    console.log(scrollPosAmenity);
	});
</script>
<div id="amenity-list" class="uk-width-1-1 uk-panel">
	<h3 class="uk-text-uppercase uk-text-emphasis uk-margin-top">Select Amenity</h3>
	<div class="uk-width-1-1 ">
		<ul class="uk-list uk-margin-left uk-margin-top">
			@php
			$currentBuildingId = 0;
  		$currentAmenityId = 0;
  		$amenityIncrement = 1;
			@endphp
			@foreach($amenities as $amenity)
			@if($loop->first)
			<li class="b-{{$amenity->building_id}} amenity-list-item finding-modal-list-items @if($audit->hasAmenityInspectionAssigned($amenity->building_id)) uid-{{Auth::user()->id}} @endif"><strong>Building BIN: {{$amenity->building_key}}</strong></li>
			@endif
			@php
			if($currentAmenityId != $amenity->amenity_id) {
				$currentAmenityId = $amenity->amenity_id;
				if($amenity->building_has_multiple()){
					$amenityIncrement = 1;
				} else {
					$amenityIncrement = '';
				}
			} else {
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
				<a onClick="selectAmenity('{{$amenity->amenity_id}}','amenity-inspection-{{$amenity->id}}','{{$amenity->id}}','@if($amenity->auditor_id) {{$amenity->user->initials()}} @else NA @endIf :{{$amenity->amenity->amenity_description}} {{$amenityIncrement}}', {{$amenityIncrement}})" style="color : @if(is_null($amenity->completed_date_time)) #50b8ec @else #000 @endIf ">{{$amenity->amenity->amenity_description}} {{$amenityIncrement}}
				</a>
			</li>
			@endforeach
			
				
			
		</ul>

	</div>
	<div class="uk-width-1-1">
		<a class="uk-button" onClick="addAmenity('{{$amenity->building_id}}', 'building',2)">
			<i class="a-circle-plus" uk-tooltip title="ADD A BUILDING AMENITY"></i> ADD AMENITY 
		</a>
	</div>
</div>