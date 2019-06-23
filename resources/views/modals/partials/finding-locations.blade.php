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
			<li class="uk-column-span uk-margin-top uk-margin-bottom use-hand-cursor" onclick="filterSiteAmenities({{ $audit->project_ref }}, 'Site: {{$audit->project->address->basic_address()}}')" style="color : @if(count($site) == 0) #000 @else #50b8ec @endIf " >@if(count($site) == 0) <i class="a-circle-checked"></i> @else <i class="a-circle"></i>@endIf Site: {{ $audit->project->address->basic_address() }}
			</li>
			{{-- <hr class="dashed-hr uk-column-span uk-margin-bottom uk-margin-top"> --}}
			{{-- Buildings here --}}
			@foreach($buildings as $type)
			@if(!is_null($type->building_id))
			<li class="uk-column-span uk-margin-top uk-margin-bottom use-hand-cursor" style="color : @if($type->complete == 1) #000 @else #50b8ec @endIf ">
				<div class="uk-inline uk-padding-remove" style="margin-top:2px; flex:140px;">
					<i @if($type->complete == 0 || is_null($type->complete)) onclick="markBuildingCompleteModal({{ $audit->audit_id }}, {{ $type->building_id }}, 0, 0,'markcomplete', 1)" @endif class="{{ ($type->complete)  ? 'a-circle-checked': 'a-circle completion-icon use-hand-cursor'}} " style="font-size: 26px;">
					</i>
				</div>
				<input type="hidden" name="building-complete-baseLink-{{ $type->building_id }}" id="building-complete-baseLink-{{ $type->building_id }}" value="'amenities/0/audit/{{ $audit->audit_id }}/building/{{ $type->building_id }}/unit/0/1/complete'">
				<div class="uk-inline uk-padding-remove" style="flex:140px;">
					<a onclick="filterBuildingAmenities({{ $type->building_id }},'Building BIN: {{ $type->building_key }}, NAME: {{ $type->building_name }}, ADDRESS: @if($type->building->address){{ $type->building->address->line_1 }} @else NO ADDRESS SET IN DEVCO @endIf')" style="color : @if($type->complete) #000 @else #50b8ec @endIf "> <strong style="color : @if($type->complete == 1) #000 @else #50b8ec @endIf "> Building BIN:{{ $type->building_key }} NAME: {{ $type->building_name }} ADDRESS: {{ $type->address }}</strong>
					</a>
				</div>
			</li>
			@php
			$buildingUnits = $units->where('building_id', $type->building_id);
			@endphp
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
					@endphp
					<li class="uk-margin-left use-hand-cursor uk-column-span uk-margin"  style="color : @if($bu->complete == 1 || ($unit_am_status == 0)) #000 @else #50b8ec @endIf ">
						<div class="uk-inline uk-padding-remove" style="margin-top:2px; flex:140px;">
							<i @if($bu->complete == 0 || is_null($bu->complete)) onclick="markUnitComplete({{ $audit->audit_id }}, 0, {{ $bu->unit_id }}, 0,'markcomplete', 1)" @endif class="{{ ($bu->complete) || ($unit_am_status == 0)  ? 'a-circle-checked': 'a-circle completion-icon use-hand-cursor'}} " style="font-size: 26px;">
							</i>
						</div>
						<div class="uk-inline uk-padding-remove" style="flex:140px;">
							<a onclick="filterUnitAmenities({{ $bu->unit_id }} ,'Unit {{ $bu->unit_name }} in BIN:{{ $bu->building_key }} ')" style="color : @if($bu->complete || ($unit_am_status == 0)) #000 @else #50b8ec @endIf "> Unit {{ $bu->unit_name }}
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

		function markBuildingCompleteModal(audit_id, building_id, unit_id, amenity_id, element, toplevel = 0){
			loadTypeView = '';
				dynamicModalLoad('property-amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/'+toplevel+'/complete', 0,0,0,2);
		}

		function markUnitComplete(audit_id, building_id, unit_id, amenity_id, element, toplevel = 0) {
			@if(!session()->has('hide_confirm_modal'))
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
				var hide_confirm_modal = $("#hide_confirm_modal").is(':checked');
				@endif
				$.post('amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/'+toplevel+'/complete', {
					@if(!session()->has('hide_confirm_modal'))
					'hide_confirm_modal': hide_confirm_modal,
					@endif
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
				@if(!session()->has('hide_confirm_modal'))
			}, function () {
				console.log('Rejected.')
			});
			@endif
		}

	</script>