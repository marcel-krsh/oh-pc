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
			<li class="s-{{ $audit->project_ref }} amenity-list-item finding-modal-list-items @if($audit->hasAmenityInspectionAssigned()) uid-{{ Auth::user()->id }} @endif"><strong>Site : {{ $audit->address }}</strong>
			</li>
			@php
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
				$currentAmenityId = $amenity->amenity_id;
				if($amenity->project_has_multiple()){
					$amenityIncrement[$currentAmenityId] = 1;
				} else {
					$amenityIncrement[$currentAmenityId] = 0;
				}
			} else {
				$amenityIncrement[$amenity->amenity_id]++;
			}
			@endphp
			<li id="amenity-inspection-{{ $amenity->id }}" class=" s-{{ $audit->project_ref }} aa-{{ $amenity->amenity_id }} amenity-inspection-{{ $amenity->id }} amenity-list-item finding-modal-list-items uid-{{ $amenity->auditor_id }}" style="color : @if(is_null($amenity->completed_date_time)) #50b8ec @else #000 @endIf ">
				<a onClick="selectAmenity('{{ $amenity->amenity_id }}','amenity-inspection-{{ $amenity->id }}','{{ $amenity->id }}','@if($amenity->auditor_id) {{ $amenity->user->initials() }} @else NA @endIf : {{ $amenity->amenity->amenity_description }} @if($amenityIncrement[$amenity->amenity_id] != 0){{ $amenityIncrement[$amenity->amenity_id] }} @endif',{{ $amenityIncrement[$amenity->amenity_id] }})" style="color : @if(is_null($amenity->completed_date_time)) #50b8ec @else #000 @endIf ">@if(is_null($amenity->completed_date_time)) <i class="a-circle"></i> @else <i class="a-circle-checked"></i> @endIf @if($amenity->auditor_id)
					<div class="amenity-auditor uk-margin-remove">
						<div class="amenity-auditor uk-margin-remove">
							<div uk-tooltip="pos:top-left;title:{{ $amenity->user->full_name() }};" class="auditor-badge auditor-badge-blue use-hand-cursor no-float">
								{{ $amenity->user->initials() }}
							</div>
						</div>
					</div>
					@else
					<i class="a-avatar-plus_1" uk-tooltip title="NEEDS ASSIGNED"></i>  @endif {{ $amenity->amenity->amenity_description }} @if($amenityIncrement[$amenity->amenity_id] != 0){{ $amenityIncrement[$amenity->amenity_id] }} @endif
				</a>
			</li>
			@endforeach
		</ul>
	</div>
</div>