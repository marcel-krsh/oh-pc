<script>
	$('#amenity-list').scroll(function(){
		scrollPosAmenity = $('#amenity-list').scrollTop();
		scrollPosAmenityBottom = $('#amenity-list').scrollBottom();
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
				<li class="u-{{ $amenity->unit_id }} amenity-inspection-{{ $amenity->id }} amenity-list-item finding-modal-list-items @if($audit->hasAmenityInspectionAssigned(null, $amenity->unit_id)) uid-{{ Auth::user()->id }} @endif">
					@if($amenity->cached_unit())
					Unit : <strong>{{ $amenity->cached_unit()->unit_name }}</strong> in BIN: <strong>{{ $amenity->cached_unit()->building->building_name }}</strong> @ <strong>{{ $amenity->cached_unit()->address }}</strong>
					@endif
				</li>
				@endif
				@php
				array_push($currentAmenityIds, $amenity->amenity_id);
				$amenityIncrements = array_count_values($currentAmenityIds);
				$amenityIncrement = $amenityIncrements[$amenity->amenity_id];
				$exists_more = count($amenities->where('amenity_id', $amenity->amenity_id)) - 1;
				if($amenityIncrement == 1 && $exists_more){
					$amenityIncrement = 1;
				} elseif($amenityIncrement == 1) {
					$amenityIncrement = '';
				}
				@endphp
				{{-- @php
				if($currentAmenityId != $amenity->amenity_id) {
					$currentAmenityId = $amenity->amenity_id;
					if($amenity->unit_has_multiple()){
						$amenityIncrement = 1;
					} else {
						$amenityIncrement = '';
					}
				} else {
					$amenityIncrement++;
				}
				@endphp --}}
				<li id="amenity-inspection-{{ $amenity->id }}" class="u-{{ $amenity->unit_id }} amenity-list-item finding-modal-list-items aa-{{ $amenity->amenity_id }} uid-{{ $amenity->auditor_id }} building {{ $current_user && ($amenity->auditor_id == $current_user->id) ? '' : 'not-mine-items' }}" style="color : @if(is_null($amenity->completed_date_time)) #50b8ec @else #000 @endIf ">

					<div class="uk-inline uk-padding-remove" style="margin-top:9px; flex:140px;">
						<i id="unit-{{ $amenity->id }}-markcomplete" onclick="markUnitAmenityComplete({{ $audit->audit_id }}, 0, {{ $amenity->unit_id }}, {{ $amenity->id }},'unit-{{ $amenity->id }}-markcomplete')" class="{{ is_null($amenity->completed_date_time) ? 'a-circle completion-icon use-hand-cursor' : 'a-circle-checked ok-actionable completion-icon use-hand-cursor' }} " style="font-size: 26px;">
						</i>
					</div>

					@if($amenity->auditor_id)
					<div class="amenity-auditor uk-margin-remove" id="unit-auditor-{{ $amenity->id }}-avatar-1">
						<div uk-tooltip="pos:top-left;title:{{ $amenity->user->full_name() }};" class="auditor-badge auditor-badge-blue use-hand-cursor no-float" onclick="assignUnitAuditor({{ $audit->audit_id }}, {{ $amenity->cached_unit() ? $amenity->cached_unit()->building_id : '' }}, {{ $amenity->unit_id }}, {{ $amenity->id }}, 'unit-auditor-{{ $amenity->id }}-avatar-1', 0, 0, 0, 2);">
							{{ $amenity->user->initials() }}
						</div>
					</div>
					@else
					<div class="uk-inline uk-padding-remove" id="unit-auditor-{{ $amenity->id }}-avatar-1" style="margin-top:6px; margin: 3px 3px 3px 3px; font-size: 20px">
						<i class="a-avatar-plus_1" uk-tooltip title="NEEDS ASSIGNED" onclick="assignUnitAuditor({{ $audit->audit_id }}, {{ $amenity->cached_unit()->building_id }}, {{ $amenity->unit_id }}, {{ $amenity->id }}, 'unit-auditor-{{ $amenity->id }}-avatar-1', 0, 0, 0, 2);">
						</i>
					</div>
					@endif
					<div class="uk-inline uk-padding-remove" style="margin-top:2px; flex:140px;">
						<a onClick="selectAmenity('{{ $amenity->amenity_id }}','amenity-inspection-{{ $amenity->id }}','{{ $amenity->id }}','@if($amenity->auditor_id) {{ $amenity->user->initials() }} @else NA @endIf :{{ $amenity->amenity->amenity_description }} {{ $amenityIncrement }}', {{ $amenityIncrement }})"  style="color : @if(is_null($amenity->completed_date_time)) #50b8ec @else #000 @endIf ">
							{{ $amenity->amenity->amenity_description }} {{ $amenityIncrement }}
						</a>
					</div>

					<span class="uk-inline uk-padding-remove uk-float-right inspection-area" style="max-height: 30px">
						<span class="findings-icon toplevel uk-inline" onclick="copyUnitAmenity('', {{ $audit->audit_id }}, 0, {{ $amenity->unit_id }}, {{ $amenity->id }}, 0, 1);" style="margin:-10px 3px 2px 3px">
							<i class="a-file-copy-2" style="font-size: 18px"></i>
							<div class="findings-icon-status plus">
								<span class="uk-badge">+</span>
							</div>
						</span>
						<span class="findings-icon toplevel uk-inline uk-margin-right" onclick="deleteUnitAmenity('unit', {{ $audit->audit_id }}, 0, {{ $amenity->unit_id }}, {{ $amenity->id  }}, 0, 1);" style="margin:-10px 3px 2px 3px">
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
			<a class="uk-button" onClick="addAmenity('{{ $amenity->unit_id }}', 'unit', 2, 1, '{{ $audit->audit_id }}')">
				<i class="a-circle-plus" uk-tooltip title="ADD A BUILDING AMENITY"></i> ADD AMENITY
			</a>
		</div>
	</div>
	<script>

		function copyUnitAmenity(element, audit_id, building_id, unit_id, amenity_id, toplevel=0, fromfinding=0){
			loadTypeView = '';
			if(window.hide_confirm_modal_flag) {
				postCopyUnitAmenity(element, audit_id, building_id, unit_id, amenity_id, toplevel, fromfinding, window.hide_confirm_modal_flag);
			} else {
				var modal_confirm_input = '<br><div><label><input class="uk-checkbox" id="hide_confirm_modal" type="checkbox" name="hide_confirm_modal"> DO NOT SHOW AGAIN FOR THIS SESSION</label></div>';
				UIkit.modal.confirm('<div uk-modal-dialog class="uk-grid"><div class="uk-width-1-1"><h2>MAKE A DUPLICATE?</h2></div><div class="uk-width-1-1"><hr class="dashed-hr uk-margin-bottom"><h3>Are you sure you want to make a duplicate?</h3>'+modal_confirm_input+'</div>', {stack: true}).then(function() {
					if(window.hide_confirm_modal_flag || $("#hide_confirm_modal").is(':checked')) {
						var hide_confirm_modal = 1;
						window.hide_confirm_modal_flag = 1;
					} else {
						var hide_confirm_modal = 0;
					}
					postCopyUnitAmenity(element, audit_id, building_id, unit_id, amenity_id, toplevel, fromfinding, hide_confirm_modal);
				});
			}
		}

		function postCopyUnitAmenity(element, audit_id, building_id, unit_id, amenity_id, toplevel, fromfinding, hide_confirm_modal) {
			var newAmenities = [];
			$.post('/modals/amenities/save', {
				'project_id' : 0,
				'audit_id' : audit_id,
				'building_id' : building_id,
				'unit_id' : unit_id,
				'new_amenities' : newAmenities,
				'amenity_id' : amenity_id,
				'toplevel': toplevel,
				'hide_confirm_modal': hide_confirm_modal,
				'_token' : '{{ csrf_token() }}'
			}, function(data) {
				$('#amenity-list').scroll(function(){
					scrollPosAmenity = $('#amenity-list').scrollBottom();
					console.log(scrollPosAmenity);
				});
				filterUnitAmenities(unit_id, null, 1);
			});
		}

		function assignUnitAuditor(audit_id, building_id, unit_id=0, amenity_id=0, element, fullscreen=null,warnAboutSave=null,fixedHeight=0,inmodallevel=0){
			loadTypeView = '';
			if(inmodallevel)
				dynamicModalLoad('amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/assign/'+element+'/3', fullscreen,warnAboutSave,fixedHeight,inmodallevel);
			else
				dynamicModalLoad('amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/assign/'+element, fullscreen,warnAboutSave,fixedHeight,inmodallevel);
		}

		function deleteUnitAmenity(element, audit_id, building_id, unit_id, amenity_id, has_findings = 0, toplevel=0){
			loadTypeView = '';
			if(has_findings){
				UIkit.modal.alert('<p class="uk-modal-body">This amenity has some findings and cannot be deleted.</p>', {modal: false}).then(function () {  });
			}else{
				dynamicModalLoad('findings-amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/delete/'+element, 0,0,0,2);
			}
		}

		function postUnitAmenityComplete(audit_id, building_id, unit_id, amenity_id, element, toplevel, hide_confirm_modal) {
			$.post('amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/'+toplevel+'/complete', {
				'_token' : '{{ csrf_token() }}',
				'hide_confirm_modal': hide_confirm_modal
			}, function(data) {
				if(data==0){
					UIkit.modal.alert(data,{stack: true});
				} else {
					console.log(data.status);
					if(data.status == 'complete'){
						if(toplevel == 1){
							UIkit.notification('<span uk-icon="icon: check"></span> Marked Completed', {pos:'top-right', timeout:1000, status:'success'});
							$('#'+element).toggleClass('a-circle');
							$('#'+element).toggleClass('a-circle-checked');
						}else if(amenity_id == 0){
							UIkit.notification('<span uk-icon="icon: check"></span> Marked Completed', {pos:'top-right', timeout:1000, status:'success'});
							$('[id^=completed-'+audit_id+building_id+']').removeClass('a-circle');
							$('[id^=completed-'+audit_id+building_id+']').addClass('a-circle-checked');
						}else{
							UIkit.notification('<span uk-icon="icon: check"></span> Marked Completed', {pos:'top-right', timeout:1000, status:'success'});
							$('#'+element).toggleClass('a-circle');
							$('#'+element).toggleClass('a-circle-checked');
							// $('#'+element).removeAttr("style");

						}
					} else{
						if(toplevel == 1){
							UIkit.notification('<span uk-icon="icon: check"></span> Marked Not Completed', {pos:'top-right', timeout:1000, status:'success'});
							$('#'+element).toggleClass('a-circle');
							$('#'+element).toggleClass('a-circle-checked');
						}else if(amenity_id == 0){
							UIkit.notification('<span uk-icon="icon: check"></span> Marked Not Completed', {pos:'top-right', timeout:1000, status:'success'});
							$('[id^=completed-'+audit_id+building_id+']').removeClass('a-circle-checked');
							$('[id^=completed-'+audit_id+building_id+']').addClass('a-circle');
						}else{
							UIkit.notification('<span uk-icon="icon: check"></span> Marked Not Completed', {pos:'top-right', timeout:1000, status:'success'});
							$('#'+element).toggleClass('a-circle-checked');
							$('#'+element).toggleClass('a-circle');
						}
					}
				}
				//filterUnitAmenities(unit_id);
			});
		}

		function markUnitAmenityComplete(audit_id, building_id, unit_id, amenity_id, element, toplevel = 0) {
			loadTypeView = '';
			if(window.hide_confirm_modal_flag) {
				postUnitAmenityComplete(audit_id, building_id, unit_id, amenity_id, element, toplevel, window.hide_confirm_modal_flag)
			} else {
				if(element){
					if($('#'+element).hasClass('a-circle-checked')){
						var title = 'MARK THIS INCOMPLETE?';
						var message = 'Are you sure you want to mark this incomplete?';
					}else{
						var title = 'MARK THIS COMPLETE?';
						var message = 'Are you sure you want to mark this complete?';
					}
				}else{
					var title = 'MARK THIS COMPLETE?';
					var message = 'Are you sure you want to mark this complete?';
				}
				var modal_confirm_input = '<br><div><label><input class="uk-checkbox" id="hide_confirm_modal" type="checkbox" name="hide_confirm_modal"> DO NOT SHOW AGAIN FOR THIS SESSION</label></div>';
				UIkit.modal.confirm('<div class="uk-grid"><div class="uk-width-1-1"><h2>'+title+'</h2></div><div class="uk-width-1-1"><hr class="dashed-hr uk-margin-bottom"><h3>'+message+'</h3>'+modal_confirm_input+'</div>', {stack: true}).then(function() {
					if(window.hide_confirm_modal_flag || $("#hide_confirm_modal").is(':checked')) {
						var hide_confirm_modal = 1;
						window.hide_confirm_modal_flag = 1;
					} else {
						var hide_confirm_modal = 0;
					}
					postUnitAmenityComplete(audit_id, building_id, unit_id, amenity_id, element, toplevel, hide_confirm_modal)
				}, function () {
					console.log('Rejected.')
				});
			}

		}
	</script>