@if(!isset($loadingAjax))
<script>
	$('#type-list').scroll(function(){
		scrollPosType = $('#type-list').scrollTop();
		console.log(scrollPosType);
	});
</script>
<div id="type-list" class="uk-width-1-1 uk-panel">
	@endIf
	<h3 class="uk-text-uppercase uk-text-emphasis uk-margin-top">Select Location</h3>
	<div class="uk-column-1-3@m uk-column-1-2@s ">
		<ul class="uk-list uk-list-divider uk-margin-left">
			{{-- Site Here --}}
			@php
			$site_status = $site->where('completed_date_time', null)->count();
					// $building_auditors = $type->auditors($audit->audit_id);
			$mine = [];
			$site_auditors = $site->where('building_id', null)->where('unit_id', null)->where('auditor_id', '<>', null);
			if(count($site_auditors)) {
				$site_auditors = $site_auditors->pluck('user')->unique();
				$mine = $site_auditors->where('id', Auth::user()->id);
			}
			@endphp
			<li class="uk-column-span uk-margin-top uk-margin-bottom use-hand-cursor {{ (count($mine)) ? '' : 'not-mine-items' }}" style="color : @if($site_status == 0 && count($site) > 0) #000 @else #50b8ec @endIf " >
				<div class="uk-inline uk-padding-remove" style="margin-top:2px; flex:140px;">
					<i @if($site_status != 0) onclick="markSiteComplete({{ $audit->audit_id }}, 0, 0, 0,'markcomplete', 1)" @endif class="{{ ($site_status == 0 && count($site) > 0)  ? 'a-circle-checked': 'a-circle completion-icon use-hand-cursor' }} " style="font-size: 26px;">
					</i>
				</div>

				@if($site_auditors && count($site_auditors) > 0)
				@foreach($site_auditors as $auditor)
				<div class="amenity-auditor uk-margin-remove">
					<div id="site-{{ $audit->audit_id }}-avatar-{{ $loop->iteration }}" uk-tooltip="pos:top-left;title:{{ $auditor->full_name() }};" title="" aria-expanded="false" class="auditor-badge auditor-badge-{{ $auditor->badge_color }} use-hand-cursor no-float" onclick="swapFindingsAuditor({{ $auditor->id }}, {{ $audit->audit_id }}, 0, 0, 'site-auditors-{{ $audit->audit_id }}')">
						{{ $auditor->initials() }}
					</div>
				</div>
				@endforeach
				@else
				<div class="uk-inline uk-padding-remove" style="margin-top:6px; margin: 3px 3px 3px 3px; font-size: 20px">
					<i class="a-avatar-plus_1" uk-tooltip title="NEEDS ASSIGNED" onclick="assignFindingAuditor({{ $audit->audit_id }}, 0, 0, 0, 'site-auditor-0', 0, 0, 0, 2);">
					</i>
				</div>
				@endif

				<div class="uk-inline uk-padding-remove" style="flex:140px;">
					<a onclick="filterSiteAmenities({{ $audit->project_ref }}, 'Site: {{ $audit->project->address ? $audit->project->address->basic_address() : 'NA' }}')" style="color : @if($site_status == 0 && count($site) > 0) #000 @else #50b8ec @endIf ">  Site: <strong style="color : @if($site_status == 0 && count($site) > 0) #000 @else #50b8ec @endIf ">{{ $audit->project->address ?  $audit->project->address->basic_address() : '' }}</strong>
					</a>
				</div>
			</li>
			{{-- <hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top"> --}}
			{{-- Buildings here --}}
			@foreach($buildings as $type)
			@if(!is_null($type->building_id))

			@php
					// $building_auditors = $type->auditors($audit->audit_id);
        		// return $type;
      $building_amenities = $amenities->where('building_id', '=', $type->building_id)->count();
			$building_auditors = $amenities->where('building_id', '=', $type->building_id)->where('auditor_id', '<>', null);
			$buildingUnits = $units->where('building_id', $type->building_id);
						// return $buildingUnits->pluck('unit_id');
			$mine = [];
			if($buildingUnits) {
				$bu_all_units = $amenities->whereIn('unit_id', $buildingUnits->pluck('unit_id'))->where('auditor_id', '<>', null);
				$building_amenities = $building_amenities + $amenities->whereIn('unit_id', $buildingUnits->pluck('unit_id'))->count();
				if($bu_all_units) {
					$all_auditors = $bu_all_units->merge($building_auditors);
					$bu_all_units_users = $all_auditors->pluck('user')->unique();
					$mine = $bu_all_units_users->where('id', Auth::user()->id);
				}
			}
			if(count($building_auditors)) {
				$b_units = $building_auditors->pluck('building')->first();
				$unit_ids = $b_units->units->pluck('id');
				$unit_auditors = $amenities->whereIn('unit_id', $unit_ids)->where('auditor_id', '<>', null);
				$combined_auditors = $building_auditors->merge($unit_auditors);
				$building_auditors = $combined_auditors->pluck('user')->unique();
							// $mine = $bu_unit_auditors->where('id', Auth::user()->id);
			}
			$buildingUnits = $buildingUnits->sortBy('unit_name')
			@endphp
			<li class="uk-column-span uk-margin-top uk-margin-bottom use-hand-cursor {{ (count($mine)) ? '' : 'not-mine-items' }}" style="color : @if($type->complete == 1 && $building_amenities > 0) #000 @else #50b8ec @endIf ">
				<div class="uk-inline uk-padding-remove" style="margin-top:2px; flex:140px;">
					<i @if($type->complete == 0 || is_null($type->complete)) onclick="markBuildingCompleteModal({{ $audit->audit_id }}, {{ $type->building_id }}, 0, 0,'markcomplete', 0)" @endif class="{{ ($type->complete && $building_amenities > 0)  ? 'a-circle-checked': 'a-circle completion-icon use-hand-cursor' }} " style="font-size: 26px;">
					</i>
				</div>

				{{-- @if($type->order_building->auditors() && count($type->order_building->auditors()) > 0) --}}
				@if($building_auditors && count($building_auditors) > 0)
				@foreach($building_auditors as $auditor)
				<div class="amenity-auditor uk-margin-remove">
					<div id="building-{{ $type->building_id }}-avatar-{{ $loop->iteration }}" uk-tooltip="pos:top-left;title:{{ $auditor->full_name() }};" title="" aria-expanded="false" class="auditor-badge auditor-badge-{{ $auditor->badge_color }} use-hand-cursor no-float" onclick="swapFindingsAuditor({{ $auditor->id }}, {{ $audit->audit_id }}, {{ $type->building_id }}, 0, 'building-auditors-{{ $type->building_id }}')">
						{{ $auditor->initials() }}
					</div>
				</div>
				@endforeach
				@else
				<div class="uk-inline uk-padding-remove" style="margin-top:6px; margin: 3px 3px 3px 3px; font-size: 20px">
					<i class="a-avatar-plus_1" uk-tooltip title="NEEDS ASSIGNED" onclick="assignFindingAuditor({{ $audit->audit_id }}, {{ $type->building_id }}, 0, 0, 'building-auditor-0', 0, 0, 0, 2);">
					</i>
				</div>
				@endif
				{{-- <input type="hidden" name="building-complete-baseLink-{{ $type->building_id }}" id="building-complete-baseLink-{{ $type->building_id }}" value="'amenities/0/audit/{{ $audit->audit_id }}/building/{{ $type->building_id }}/unit/0/1/complete'"> --}}
				<div class="uk-inline uk-padding-remove" style="flex:140px;">
					<a onclick="filterBuildingAmenities({{ $type->building_id }},'Building BIN: {{ $type->building->building_name }}, ADDRESS: @if($type->building->address){{ $type->building->address->line_1 }} @else NO ADDRESS SET IN DEVCO @endIf')" style="color : @if($type->complete && $building_amenities > 0) #000 @else #50b8ec @endIf ">  BIN: <strong style="color : @if($type->complete == 1 && $building_amenities > 0) #000 @else #50b8ec @endIf ">{{ $type->building_name }}</strong> @ <strong style="color : @if($type->complete == 1 && $building_amenities > 0) #000 @else #50b8ec @endIf ">{{ $type->address }}</strong>
					</a>
				</div>
			</li>
			{{--
			Units here
			Making units complete (Check during amenity add, duplicate, delete and mark complete/incomplete)
			Add: saveAmenity
			Duplicate: saveAmenity
			Delete: saveDeleteAmenity
			Complete: markCompleted
			Add modal trigger
			--}}
			@if($buildingUnits)
			<ul class="uk-margin-left">
				@forEach($buildingUnits as $bu)
				@php
				$unit_am_status = $amenities->where('unit_id', $bu->unit_id)->where('completed_date_time', null)->count();
				$unit_amenities = $amenities->where('unit_id', $bu->unit_id)->count();
				// if(count($building_auditors)) {
				// 	$bu_unit_auditors = $unit_auditors->where('unit_id', $bu->unit_id);
				// 	if(count($bu_unit_auditors))
				// 	$bu_unit_auditors = $bu_unit_auditors->pluck('user')->unique();
				// } else {
				// 	$bu_unit_auditors = [];
				// }
				$bu_unit_auditors = $amenities->where('unit_id', $bu->unit_id)->where('auditor_id', '<>', null);
				if(count($bu_unit_auditors)) {
					$bu_unit_auditors = $bu_unit_auditors->pluck('user')->unique();
					$mine = $bu_unit_auditors->where('id', Auth::user()->id);
				} else {
					$bu_unit_auditors = [];
					$mine = [];
				}

				// dd($unit_auditors);
				@endphp
				<li class="uk-margin-left use-hand-cursor uk-column-span uk-margin {{ (count($mine)) ? '' : 'not-mine-items' }}"  style="color : @if( ($bu->complete == 1 || ($unit_am_status == 0)) && $unit_amenities > 0) #000 @else #50b8ec @endIf ">
					<div class="uk-inline uk-padding-remove" style="margin-top:2px; flex:140px;">
						<i @if($bu->complete == 0 || is_null($bu->complete)) onclick="markUnitComplete({{ $audit->audit_id }}, 0, {{ $bu->unit_id }}, 0,'markcomplete', 0)" @endif class="{{ (($bu->complete) || ($unit_am_status == 0)) && $unit_amenities > 0  ? 'a-circle-checked': 'a-circle completion-icon use-hand-cursor' }} " style="font-size: 26px;">
						</i>
					</div>

				{{-- 	@if($bu->auditors($audit->audit_id) && count($bu->auditors($audit->audit_id)) > 0)
					@foreach($bu->auditors($audit->audit_id) as $auditor) --}}
					@if($bu_unit_auditors && count($bu_unit_auditors) > 0)
					@foreach($bu_unit_auditors as $auditor)
					<div class="amenity-auditor uk-margin-remove">
						<div id="unit-{{ $bu->unit_id }}-avatar-{{ $loop->iteration }}" uk-tooltip="pos:top-left;title:{{ $auditor->full_name() }};" title="" aria-expanded="false" class="auditor-badge auditor-badge-{{ $auditor->badge_color }} use-hand-cursor no-float" onclick="swapFindingsAuditor({{ $auditor->id }}, {{ $audit->audit_id }}, {{ $bu->building_id }}, {{ $bu->unit_id }}, 'unit-auditors-{{ $bu->unit_id }}')">
							{{ $auditor->initials() }}
						</div>
					</div>
					@endforeach
					@else
					<div class="uk-inline uk-padding-remove" style="margin-top:6px; margin: 3px 3px 3px 3px; font-size: 20px">
						<i class="a-avatar-plus_1" uk-tooltip title="NEEDS ASSIGNED" onclick="assignFindingAuditor({{ $audit->audit_id }}, {{ $bu->building_id }}, {{ $bu->unit_id }}, 0, 'building-auditor-0', 0, 0, 0, 2);">
						</i>
					</div>
					@endif


					<div class="uk-inline uk-padding-remove" style="flex:140px;">
						<a onclick="filterUnitAmenities({{ $bu->unit_id }} ,'Unit {{ $bu->unit_name }} in BIN:{{ $bu->building->building_name }} ')" style="color : @if( ($bu->complete || ($unit_am_status == 0)) && $unit_amenities > 0) #000 @else #50b8ec @endIf "> Unit <strong>{{ $bu->unit_name }}</strong>
						</a>
					</div>
				</li>
				{{-- &nbsp;&nbsp;&nbsp;@if($bu->complete == 0 || is_null($bu->complete)) <i class="a-circle" style="color: #50b8ec" ></i> @else <i class="a-circle-checked"></i> @endIf<i class="a-buildings-2"></i> Unit {{ $bu->unit_name }}</li> --}}
				@endforeach
			</ul>
			{{-- <hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top"> --}}
			@else
			{{-- <hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top"> --}}
			@endif
			@endif
			@endforeach
		</ul>
	</div>
	@if(!isset($loadingAjax))
</div>
<!-- This is the modal -->
@endIf

<script>

	function swapFindingsAuditor(auditor_id, audit_id, building_id, unit_id, element, amenity_id=0, inmodallevel=4){
		dynamicModalLoad('amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/swap/'+auditor_id+'/'+element+'/'+inmodallevel, 0,0,0,2);
	}

	function assignFindingAuditor(audit_id, building_id, unit_id=0, amenity_id=0, element, fullscreen=null,warnAboutSave=null,fixedHeight=0,inmodallevel=0){
		dynamicModalLoad('amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/assign/'+element+'/4', fullscreen,warnAboutSave,fixedHeight,inmodallevel);
	}

	function markBuildingCompleteModal(audit_id, building_id, unit_id, amenity_id, element, toplevel = 0){
		loadTypeView = '';
		dynamicModalLoad('property-amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/'+toplevel+'/complete', 0,0,0,2);
	}

	function postUnitComplete(audit_id, building_id, unit_id, amenity_id, element, toplevel, hide_confirm_modal) {
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
					}else if(amenity_id == 0){
						UIkit.notification('<span uk-icon="icon: check"></span> Marked Completed', {pos:'top-right', timeout:1000, status:'success'});
					}else{
						UIkit.notification('<span uk-icon="icon: check"></span> Marked Completed', {pos:'top-right', timeout:1000, status:'success'});
					}
				} else{
					if(toplevel == 1){
						UIkit.notification('<span uk-icon="icon: check"></span> Marked Not Completed', {pos:'top-right', timeout:1000, status:'success'});
					}else if(amenity_id == 0){
						UIkit.notification('<span uk-icon="icon: check"></span> Marked Not Completed', {pos:'top-right', timeout:1000, status:'success'});
					}else{
						UIkit.notification('<span uk-icon="icon: check"></span> Marked Not Completed', {pos:'top-right', timeout:1000, status:'success'});
					}
				}
			}
			loadTypeView = '';
			loadTypes();
		});
	}

	function markUnitComplete(audit_id, building_id, unit_id, amenity_id, element, toplevel = 0) {
		if(window.hide_confirm_modal_flag) {
			postUnitComplete(audit_id, building_id, unit_id, amenity_id, element, toplevel, window.hide_confirm_modal_flag)
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
				postUnitComplete(audit_id, building_id, unit_id, amenity_id, element, toplevel, hide_confirm_modal)
			}, function () {
				console.log('Rejected.')
			});
		}
	}

	function postSiteComplete(audit_id, building_id, unit_id, amenity_id, element, toplevel, hide_confirm_modal) {
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
					}else if(amenity_id == 0){
						UIkit.notification('<span uk-icon="icon: check"></span> Marked Completed', {pos:'top-right', timeout:1000, status:'success'});
					}else{
						UIkit.notification('<span uk-icon="icon: check"></span> Marked Completed', {pos:'top-right', timeout:1000, status:'success'});
					}
				} else{
					if(toplevel == 1){
						UIkit.notification('<span uk-icon="icon: check"></span> Marked Not Completed', {pos:'top-right', timeout:1000, status:'success'});
					}else if(amenity_id == 0){
						UIkit.notification('<span uk-icon="icon: check"></span> Marked Not Completed', {pos:'top-right', timeout:1000, status:'success'});
					}else{
						UIkit.notification('<span uk-icon="icon: check"></span> Marked Not Completed', {pos:'top-right', timeout:1000, status:'success'});
					}
				}
			}
			loadTypeView = '';
			loadTypes();
		});
	}

	function markSiteComplete(audit_id, building_id, unit_id, amenity_id, element, toplevel = 0) {
		if(window.hide_confirm_modal_flag) {
			postSiteComplete(audit_id, building_id, unit_id, amenity_id, element, toplevel, window.hide_confirm_modal_flag)
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
				postSiteComplete(audit_id, building_id, unit_id, amenity_id, element, toplevel, hide_confirm_modal)
		}, function () {
			console.log('Rejected.')
		});
	}
	}

</script>