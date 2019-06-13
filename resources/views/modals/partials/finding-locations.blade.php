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
			@foreach($buildings as $type)
			@if(!is_null($type->building_id))
			<li class="uk-column-span uk-margin-top uk-margin-bottom use-hand-cursor" onclick="filterBuildingAmenities({{ $type->building_id }},'Building BIN: {{ $type->building_key }}, NAME: {{ $type->building_name }}, ADDRESS: @if($type->building->address){{ $type->building->address->line_1 }} @else NO ADDRESS SET IN DEVCO @endIf')">@if($type->complete == 0 || is_null($type->complete)) <i class="a-circle" style="color: #50b8ec" ></i> @else <i class="a-circle-checked"></i> @endIf <strong style="color : @if($type->complete == 1) #000 @else #50b8ec @endIf "> Building BIN:{{ $type->building_key }} NAME: {{ $type->building_name }} ADDRESS: {{ $type->address }}</strong>
			</li>
			@php
			$buildingUnits = $units->where('building_id', $type->building_id);
			@endphp
			@if($buildingUnits)
			<ul class="uk-margin-left">
				@forEach($buildingUnits as $bu)
				<li class="use-hand-cursor uk-column-span uk-margin" onclick="filterUnitAmenities({{ $bu->unit_id }} ,'Unit {{ $bu->unit_name }} in BIN:{{ $bu->building_key }} ')" style="color : @if($bu->complete == 1) #000 @else #50b8ec @endIf ">&nbsp;&nbsp;&nbsp;@if($bu->complete == 0 || is_null($bu->complete)) <i class="a-circle" style="color: #50b8ec" ></i> @else <i class="a-circle-checked"></i> @endIf<i class="a-buildings-2"></i> Unit {{ $bu->unit_name }}</li>
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
@endIf