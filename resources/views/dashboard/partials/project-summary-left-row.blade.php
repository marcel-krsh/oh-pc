@if(null !== $program)
<tr style="border-top: 1px solid" @if($program['building_name'])id="program-selection-{{ $program['id'] }}-{{ $program['building_key'] }}" @else id="program-selection-{{ $program['id'] }}" @endIf>
<td style="padding-top:10px;">
	<div uk-leader>
		<strong>{{ $program['name'] }} @if($program['building_name']) | <a onClick="filterBuilding('building-{{$program['building_key']}}')">{{$program['building_name']}}</a> @endif
		</strong>
	</div>
</td>
<td class="uk-text-center border-right"></td>
<td class="uk-text-center"></td>
<tr @if($program['building_name'])class="program-selection-{{ $program['id'] }}-{{ $program['building_key'] }}" @else class="program-selection-{{ $program['id'] }}" @endIf>
	<td>
		<div class="indented" uk-leader><i class="fas fa-square chart-color-required"></i> Required Units</div>
	</td>
	<td class="uk-text-center border-right">{{$program['required_units']}}</td>
	<td class="uk-text-center">{{$program['required_units_file']}}</td>
</tr>
<tr @if($program['building_name'])class="program-selection-{{ $program['id'] }}-{{ $program['building_key'] }}" @else class="program-selection-{{ $program['id'] }}" @endIf>
	<td>
		<div class="indented" uk-leader><i class="fas fa-square chart-color-needed"></i> Needed Units</div>
	</td>
	<td class="uk-text-center border-right">{{$program['needed_units']}}</td>
	<td class="uk-text-center">{{$program['needed_units_file']}}</td>
</tr>
<tr @if($program['building_name'])class="program-selection-{{ $program['id'] }}-{{ $program['building_key'] }}" @else class="program-selection-{{ $program['id'] }}" @endIf>
	<td @if($program['building_name'])class="program-selection-{{ $program['id'] }}-{{ $program['building_key'] }}" @else class="program-selection-{{ $program['id'] }}" @endIf>
		<div class="indented" uk-leader><i class="fas fa-square chart-color-selected"></i> Selected Units</div>
	</td>
	<td class="uk-text-center border-right">{{$program['selected_units']}}</td>
	<td class="uk-text-center" style="padding-bottom:20px;">{{$program['selected_units_file']}}</td>
</tr>
@endIf

