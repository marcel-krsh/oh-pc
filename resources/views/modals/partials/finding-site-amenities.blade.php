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
			<li class="s-{{ $audit->project_ref }} amenity-list-item finding-modal-list-items @if($audit->hasAmenityInspectionAssigned()) uid-{{ Auth::user()->id }} @endif"><strong>Site : {{ $audit->address }}</strong>
			</li>
			@php
			$projectAmenities = $amenities->filter(function ($project){
				if(!is_null($project->project_id)){
					return true;
				} else {
					return false;
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
			<li id="amenity-inspection-{{ $amenity->id }}" class="building s-{{ $audit->project_ref }} aa-{{ $amenity->amenity_id }} amenity-inspection-{{ $amenity->id }} amenity-list-item finding-modal-list-items uid-{{ $amenity->auditor_id }} {{ $current_user && ($amenity->auditor_id == $current_user->id) ? '' : 'not-mine-items' }}" style="color : @if(is_null($amenity->completed_date_time)) #50b8ec @else #000 @endIf ">
				<div class="uk-inline uk-padding-remove" style="margin-top:6px; flex:140px;">
					<i onclick="markSiteAmenityComplete({{ $audit->audit_id }}, 0, 0, {{ $amenity->id }},'markcomplete', 1)" class="{{ is_null($amenity->completed_date_time) ? 'a-circle completion-icon use-hand-cursor' : 'a-circle-checked ok-actionable completion-icon use-hand-cursor'}} " style="font-size: 26px;">
					</i>
				</div>
				{{-- @if(is_null($amenity->completed_date_time)) <i class="a-circle"></i> @else <i class="a-circle-checked"></i> @endIf --}}
				@if($amenity->auditor_id)
				<div class="amenity-auditor uk-margin-remove" id="site-auditor-{{ $amenity->id }}-avatar-1">
					<div  uk-tooltip="pos:top-left;title:{{ $amenity->user->full_name() }};" class="auditor-badge auditor-badge-blue use-hand-cursor no-float" onclick="assignSiteAuditor({{ $audit->audit_id }}, 0, 0, {{ $amenity->id }}, 'site-auditor-{{ $amenity->id }}-avatar-1', 0, 0, 0, 2);">
						{{ $amenity->user->initials() }}
					</div>
				</div>
				@else
				<div id="site-auditor-{{ $amenity->id }}-avatar-1" class="uk-inline uk-padding-remove" style="margin-top:6px; margin: 3px 3px 3px 3px; font-size: 20px">
					<i onclick="assignSiteAuditor({{ $audit->audit_id }}, 0, 0, {{ $amenity->id }}, 'site-auditor-{{ $amenity->id }}-avatar-1', 0, 0, 0, 2);" class="a-avatar-plus_1" uk-tooltip title="NEEDS ASSIGNED"></i>
				</div>
				@endif

				<div class="uk-inline uk-padding-remove" style="margin-top:2px; flex:140px;">
					<a onClick="selectAmenity('{{ $amenity->amenity_id }}','amenity-inspection-{{ $amenity->id }}','{{ $amenity->id }}','@if($amenity->auditor_id) {{ $amenity->user->initials() }} @else NA @endIf : {{ $amenity->amenity->amenity_description }} @if($amenityIncrement[$amenity->amenity_id] != 0){{ $amenityIncrement[$amenity->amenity_id] }} @endif',{{ $amenityIncrement[$amenity->amenity_id] }})" style="color : @if(is_null($amenity->completed_date_time)) #50b8ec @else #000 @endIf "> {{ $amenity->amenity->amenity_description }} @if($amenityIncrement[$amenity->amenity_id] != 0){{ $amenityIncrement[$amenity->amenity_id] }} @endif
					</a>
				</div>

				<span class="uk-inline uk-padding-remove uk-float-right inspection-area" style="max-height: 30px">
					<span class="findings-icon toplevel uk-inline" onclick="copySiteAmenity('', {{ $audit->audit_id }}, 0, 0, {{ $amenity->id }}, 0, 1);" style="margin:-10px 3px 2px 3px">
						<i class="a-file-copy-2" style="font-size: 18px"></i>
						<div class="findings-icon-status plus">
							<span class="uk-badge">+</span>
						</div>
					</span>
					<span class="findings-icon toplevel uk-inline uk-margin-right" onclick="deleteSiteAmenity('site', {{ $audit->audit_id }}, 0, 0, {{ $amenity->id  }}, 0, 1);" style="margin:-10px 3px 2px 3px">
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
		<a class="uk-button" onClick="addAmenity('{{ $audit->audit_id }}', 'project', 2, 1)">
			<i class="a-circle-plus" uk-tooltip title="ADD A BUILDING AMENITY"></i> ADD AMENITY
		</a>
	</div>
</div>

<script>

	function postCopySiteAmenity(element, audit_id, building_id, unit_id, amenity_id, toplevel, fromfinding, hide_confirm_modal) {
		var newAmenities = [];
		$.post('/modals/amenities/save', {
			'project_id' : 0,
			'audit_id' : audit_id,
			'building_id' : building_id,
			'unit_id' : unit_id,
			'new_amenities' : newAmenities,
			'amenity_id' : amenity_id,
			'hide_confirm_modal': hide_confirm_modal,
			'toplevel': toplevel,
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			filterSiteAmenities(audit_id);
		});
	}


	function copySiteAmenity(element, audit_id, building_id, unit_id, amenity_id, toplevel=0, fromfinding=0){
		if(window.hide_confirm_modal_flag) {
			postCopySiteAmenity(element, audit_id, building_id, unit_id, amenity_id, toplevel, fromfinding, window.hide_confirm_modal_flag);
		} else {
			var modal_confirm_input = '<br><div><label><input class="uk-checkbox" id="hide_confirm_modal" type="checkbox" name="hide_confirm_modal"> DO NOT SHOW AGAIN FOR THIS SESSION</label></div>';
			UIkit.modal.confirm('<div uk-modal-dialog class="uk-grid"><div class="uk-width-1-1"><h2>MAKE A DUPLICATE?</h2></div><div class="uk-width-1-1"><hr class="dashed-hr uk-margin-bottom"><h3>Are you sure you want to make a duplicate?</h3>'+modal_confirm_input+'</div>', {stack: true}).then(function() {
				if(window.hide_confirm_modal_flag || $("#hide_confirm_modal").is(':checked')) {
					var hide_confirm_modal = 1;
					window.hide_confirm_modal_flag = 1;
				} else {
					var hide_confirm_modal = 0;
				}
				postCopySiteAmenity(element, audit_id, building_id, unit_id, amenity_id, toplevel, fromfinding, hide_confirm_modal);
			});
		}
	}

	function assignSiteAuditor(audit_id, building_id, unit_id=0, amenity_id=0, element, fullscreen=null,warnAboutSave=null,fixedHeight=0,inmodallevel = 0){
		if(inmodallevel)
			dynamicModalLoad('amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/assign/'+element+'/2', fullscreen,warnAboutSave,fixedHeight,inmodallevel);
		else
			dynamicModalLoad('amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/assign/'+element, fullscreen,warnAboutSave,fixedHeight,inmodallevel);
	}

	function deleteSiteAmenity(element, audit_id, building_id, unit_id, amenity_id, has_findings = 0, toplevel=0){
		if(has_findings){
			UIkit.modal.alert('<p class="uk-modal-body">This amenity has some findings and cannot be deleted.</p>', {modal: false}).then(function () {  });
		}else{
			dynamicModalLoad('findings-amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/delete/'+element, 0,0,0,2);
		}
	}

	function postSiteAmenityComplete(audit_id, building_id, unit_id, amenity_id, element, toplevel, hide_confirm_modal) {
		$.post('amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/'+toplevel+'/complete', {
			'hide_confirm_modal': hide_confirm_modal,
			'_token' : '{{ csrf_token() }}'
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
			filterSiteAmenities(audit_id);
		});
	}

	function markSiteAmenityComplete(audit_id, building_id, unit_id, amenity_id, element, toplevel = 0) {
		if(window.hide_confirm_modal_flag) {
			postSiteAmenityComplete(audit_id, building_id, unit_id, amenity_id, element, toplevel, window.hide_confirm_modal_flag)
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
				postSiteAmenityComplete(audit_id, building_id, unit_id, amenity_id, element, toplevel, hide_confirm_modal)
			}, function () {
				console.log('Rejected.')
			});
		}
	}
</script>