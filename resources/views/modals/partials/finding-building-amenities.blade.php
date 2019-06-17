<script>
	$('#amenity-list').scroll(function(){
		scrollPosAmenity = $('#amenity-list').scrollTop();
		console.log(scrollPosAmenity);
	});
</script>
<div id="amenity-list" class="uk-width-1-1 uk-panel">
	<h3 class="uk-text-uppercase uk-text-emphasis uk-margin-top">Select Amenity</h3>
	<div class="uk-width-1-1 ">
		<ul class="uk-list uk-margin-left uk-margin-top" id="auditstable">
			@php
			$currentBuildingId = 0;
			$currentAmenityIds = [];
			$amenityIncrement = 1;
			@endphp
			@foreach($amenities as $amenity)
			@if($loop->first)
			<li class="b-{{ $amenity->building_id }} amenity-list-item finding-modal-list-items @if($audit->hasAmenityInspectionAssigned($amenity->building_id)) uid-{{ Auth::user()->id }} @endif"><strong>Building BIN: {{ $amenity->building_key }}</strong></li>
			@endif
			@php
				array_push($currentAmenityIds, $amenity->amenity_id);
				$amenityIncrements = array_count_values($currentAmenityIds);
				$amenityIncrement = $amenityIncrements[$amenity->amenity_id] - 1;
				if($amenityIncrement == 0)
					$amenityIncrement = '';
			@endphp
			<li id="amenity-inspection-{{ $amenity->id }}" class="b-{{ $amenity->building_id }} aa-{{ $amenity->amenity_id }} amenity-inspection-{{ $amenity->id }} amenity-list-item finding-modal-list-items  uid-{{ $amenity->auditor_id }} building" style="color : @if(is_null($amenity->completed_date_time)) #50b8ec @else #000 @endIf ">@if(is_null($amenity->completed_date_time)) <i class="a-circle"></i> @else <i class="a-circle-checked"></i> @endIf @if($amenity->auditor_id)
				<div class="amenity-auditor uk-margin-remove">
					<div class="amenity-auditor uk-margin-remove">
						<div uk-tooltip="pos:top-left;title:{{ $amenity->user->full_name() }};" class="auditor-badge auditor-badge-blue use-hand-cursor no-float" onclick="assignBuildingAuditor({{ $audit->audit_id }}, {{ $amenity->building_id }}, 0, {{ $amenity->id }}, 'building-auditor-{{ $amenity->user->id }}', 0, 0, 0, 2);">
							{{ $amenity->user->initials() }}
						</div>
					</div>
				</div>
				@else
				<i class="a-avatar-plus_1" uk-tooltip title="NEEDS ASSIGNED" onclick="assignBuildingAuditor({{ $audit->audit_id }}, {{ $amenity->building_id }}, 0, {{ $amenity->id }}, 'building-auditor', 0, 0, 0, 2);"></i>  @endif
				<a onClick="selectAmenity('{{ $amenity->amenity_id }}','amenity-inspection-{{ $amenity->id }}','{{ $amenity->id }}','@if($amenity->auditor_id) {{ $amenity->user->initials() }} @else NA @endIf :{{ $amenity->amenity->amenity_description }} {{ $amenityIncrement }}', {{ $amenityIncrement }})" style="color : @if(is_null($amenity->completed_date_time)) #50b8ec @else #000 @endIf ">{{ $amenity->amenity->amenity_description }} {{ $amenityIncrement }}
				</a>

				<span class="uk-inline uk-padding-remove uk-float-right inspection-area" style="max-height: 30px">

					<span class="findings-icon toplevel uk-inline" onclick="copyBuildingAmenity('', {{ $audit->audit_id }}, {{ $amenity->building_id }}, 0, {{ $amenity->id }}, 0, 1);" style="margin:-10px 3px 2px 3px">
						<i class="a-file-copy-2" style="font-size: 18px"></i>
						<div class="findings-icon-status plus">
							<span class="uk-badge">+</span>
						</div>
					</span>
					<span class="findings-icon toplevel uk-inline uk-margin-right" onclick="deleteBuildingAmenity('', {{ $audit->audit_id }}, {{ $amenity->building_id }}, 0, {{ $amenity->id  }}, 0, 1);" style="margin:-10px 3px 2px 3px">
						<i class="a-trash-4" style="font-size: 18px"></i>
						<div class="findings-icon-status plus">
							<span class="uk-badge">-</span>
						</div>
					</span>
				</span>
			</li>
			@endforeach
		</ul>

	</div>
	<div class="uk-width-1-1">
		<a class="uk-button" onClick="addAmenity('{{ $building_id }}', 'building',2)">
			<i class="a-circle-plus" uk-tooltip title="ADD A BUILDING AMENITY"></i> ADD AMENITY
		</a>
	</div>
</div>
<script>

	function copyBuildingAmenity(element, audit_id, building_id, unit_id, amenity_id, toplevel=0, fromfinding=0){
		UIkit.modal.confirm('<div uk-modal-dialog class="uk-grid"><div class="uk-width-1-1"><h2>MAKE A DUPLICATE?</h2></div><div class="uk-width-1-1"><hr class="dashed-hr uk-margin-bottom"><h3>Are you sure you want to make a duplicate?</h3></div>', {stack: true}).then(function() {
			var newAmenities = [];
			$.post('/modals/amenities/save', {
				'project_id' : 0,
				'audit_id' : audit_id,
				'building_id' : building_id,
				'unit_id' : unit_id,
				'new_amenities' : newAmenities,
				'amenity_id' : amenity_id,
				'toplevel': toplevel,
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				filterBuildingAmenities(building_id);
			});

		});
	}

	function assignBuildingAuditor(audit_id, building_id, unit_id=0, amenity_id=0, element, fullscreen=null,warnAboutSave=null,fixedHeight=0,inmodallevel=0){
    	if(inmodallevel)
    	dynamicModalLoad('amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/assign/'+element+'/1', fullscreen,warnAboutSave,fixedHeight,inmodallevel);
    	else
    	dynamicModalLoad('amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/assign/'+element, fullscreen,warnAboutSave,fixedHeight,inmodallevel);
    }

	function deleteBuildingAmenity(element, audit_id, building_id, unit_id, amenity_id, has_findings = 0, toplevel=0){
		if(has_findings){
			UIkit.modal.alert('<p class="uk-modal-body">This amenity has some findings and cannot be deleted.</p>', {modal: false}).then(function () {  });
		}else{
			dynamicModalLoad('findings-amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/delete/'+element, 0,0,0,2);
		}
	}
</script>