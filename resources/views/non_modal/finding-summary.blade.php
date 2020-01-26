@php
$findingHeader = "";
@endphp

@if(!is_null($f->building_id))

@if ($findingHeader !== $f->building->building_name)


@php $findingHeader = $f->building->building_name; $columnCount = 1; $findingsRun = 1; @endphp
<div class="uk-width-1-1 uk-margin-bottom @if($f->unit_id > 0) unit-{{$f->unit_id}}-finding building-{{$f->unit->building_id}}-finding @endif @if($f->building_id > 0) building-{{$f->building_id}}-finding @endif @if(null == $f->unit_id && null == $f->building_id) site-amenity-finding-{{$f->id}} @endif @if(isset($site_finding) && $site_finding == 1) site-{{ $f->amenity->amenity_type_key }}-finding @endif finding-group uk-contrast" style="background: #4e4e4e; padding-top: 11px; padding-bottom: 11px; margin-bottom: 0px !important;">
	<h3 class="uk-margin-remove">BUILDING FINDINGS FOR BIN: {{$f->building->building_name}}</h3>@else <small>BUILDING FINDINGS FOR BIN: {{$f->building->building_name}}</small>
	@if(!is_null($f->building->address))
	<small style="text-transform: uppercase;">{{$f->building->address->line_1}} {{$f->building->address->line_2}} |
		{{$f->building->address->city}}, {{$f->building->address->state}} {{$f->building->address->zip}}
	</small>
	@endif

</div>
@endif


@elseIf(!is_null($f->unit_id))
@if ($findingHeader !== $f->unit->unit_name)
@php $findingHeader = $f->unit->unit_name; $columnCount = 1; $findingsRun = 1; @endphp
<div class="uk-width-1-1 uk-margin-bottom @if($f->unit_id > 0) unit-{{$f->unit_id}}-finding building-{{$f->unit->building_id}}-finding @endIf @if($f->building_id > 0) building-{{$f->building_id}}-finding @endIf @if(null == $f->unit_id && null == $f->building_id) site-amenity-finding-{{$f->id}} @endIf @if(isset($site_finding) && $site_finding == 1) site-{{ $f->amenity->amenity_type_key }}-finding @endif finding-group uk-contrast"  style="background: #4e4e4e; padding-top: 11px; padding-bottom: 11px; margin-bottom: 0px !important;">
	<h3 class="uk-margin-remove">UNIT FINDINGS FOR UNIT: {{$f->unit->unit_name}}</h3>
	@if(!is_null($f->unit->building_id))IN BIN: {{$f->unit->building->building_name}} <br />
	@if($f->unit->building && !is_null($f->unit->building->address))
	<small style="text-transform: uppercase;">{{$f->unit->building->address->line_1}} {{$f->unit->building->address->line_2}} |
		{{$f->unit->building->address->city}}, {{$f->unit->building->address->state}} {{$f->unit->building->address->zip}}
	</small>
	@endif

</div>
@endif

@else
@if ($findingHeader !== $f->project->project_name)
@php $findingHeader = $f->project->project_name; $columnCount = 1; $findingsRun = 1; @endphp
<div class="uk-width-1-1 uk-margin-bottom @if($f->unit_id > 0) unit-{{$f->unit_id}}-finding building-{{$f->unit->building_id}}-finding @endIf @if($f->building_id > 0) building-{{$f->building_id}}-finding @endIf @if(null == $f->unit_id && null == $f->building_id) site-amenity-finding-{{$f->id}} @endIf @if(isset($site_finding) && $site_finding == 1) site-{{ $f->amenity->amenity_type_key }}-finding @endif finding-group uk-contrast"  style="background: #4e4e4e; padding-top: 11px; padding-bottom: 11px; margin-bottom: 0px !important;">
	<h3 class="uk-margin-remove">SITE FINDINGS FOR: {{$f->project->project_name}}</h3>
	@if($f->project->address)
	<small style="text-transform: uppercase;"> {{$f->project->address->line_1}} {{$f->project->address->line_2}} |
		{{$f->project->address->city}}, {{$f->project->address->state}} {{$f->project->address->zip}}
	</small>
	@endif
	@if($print)<hr class="dashed-hr uk-margin-bottom"> @endif
</div>
@endif

@endif