{{--
 AUDIT: XXXXX

BIN: XX-XXXXXX : UNIT: XXXXXXXXX
Addresss Dr, City, ZIP345
FN: XXXX : OH.NLT.196 Level 2: Description only - no comment
FN: XXXX : OH.NLT.187 Level 1:
Description only - no comment
what I can't illustrate here is the icon to precede each finding with the open circle, circle-x, or circle check on its resolution

then you can pull up the full details - including the resolve button if it is not resolved

so they can mark it resolved

to keep things consistent- it would be good to make the finding count flash (attention) if it is attached to an unresolved finding.

 --}}



@php
$findingHeader = "";
$f = [];
@endphp

@if(count($site_findings))
	@php
	$f = $site_findings->first();
	@endphp
	{{--  AUDIT: XXXXX --}} {{-- BIN: XX-XXXXXX  --}}
	<div class="uk-width-1-1">
		<span class="uk-text-uppercase"> AUDIT: {{ $f->audit_id }}, </span> <strong><span class="uk-text-uppercase"> SITE: {{ $f->project->project_name }}</span></strong>
	</div>
	{{-- Addresss Dr, City, ZIP345 --}}
	@if($f->project->address)
		<div class="uk-width-1-1">
			<small style="text-transform: uppercase;">{{ $f->project->address->line_1 }} {{ $f->project->address->line_2 }} |
			{{ $f->project->address->city }}, {{ $f->project->address->state }} {{ $f->project->address->zip }}
			</small>
		</div>
	@endif
	{{-- FN: XXXX : OH.NLT.196 Level 2: Description only - no comment
	FN: XXXX : OH.NLT.187 Level 1:Description only - no comment --}}
	<small>
	@foreach($site_findings as $fin)
	F|N #{{ $f->id }}:
	<span>OH.{{ strtoupper($f->finding_type->type) }}.{{ $f->finding_type_id }} @if($f->level) LEVEL {{ $f->level }} @endif:{{ $f->finding_type->name }}
	</span>
	@endforeach
	</small>
@endif



@if(count($building_findings))
<hr class="dashed-hr uk-margin-bottom">
	{{-- Check if there are any unit specific findings for this building --}}
	@php
	$f = $building_findings->first();
	$b_unit_findings = $unit_findings->where('unit.building_id', $f->building_id);
	if(count($b_unit_findings)) {
		$unit_findings = $unit_findings->where('unit.building_id', '<>', $f->building_id);
	}
	@endphp
	{{--  AUDIT: XXXXX --}} {{-- BIN: XX-XXXXXX  --}}
	<div class="uk-width-1-1">
		<span class="uk-text-uppercase"> AUDIT: {{ $f->audit_id }}, </span> <strong><span class="uk-text-uppercase"> BIN: {{ $f->building->building_name }}</span></strong>
	</div>
	{{-- Addresss Dr, City, ZIP345 --}}
	@if(!is_null($f->building->address))
		<div class="uk-width-1-1">
			<small style="text-transform: uppercase;">{{ $f->building->address->line_1 }} {{ $f->building->address->line_2 }} |
				{{ $f->building->address->city }}, {{ $f->building->address->state }} {{ $f->building->address->zip }}
			</small>
		</div>
	@endif
	{{-- FN: XXXX : OH.NLT.196 Level 2: Description only - no comment
	FN: XXXX : OH.NLT.187 Level 1:Description only - no comment --}}
	<small>
	@foreach($building_findings as $fin)
	F|N #{{ $f->id }}:
	<span>OH.{{ strtoupper($f->finding_type->type) }}.{{ $f->finding_type_id }} @if($f->level) LEVEL {{ $f->level }} @endif:{{ $f->finding_type->name }}
	</span>
	@endforeach
	</small>

	@if(count($b_unit_findings)) {
		@php
			$f = $b_unit_findings->first();
		@endphp
		{{--  AUDIT: XXXXX --}} {{-- UNIT: XX-XXXXXX  --}}
		<div class="uk-width-1-1">
			<strong><span class="uk-text-uppercase"> AUDIT: {{ $f->audit_id }}, </span> <span class="uk-text-uppercase"> UNIT: {{ $f->unit->unit_name }}</span></strong>
		</div>
		{{-- FN: XXXX : OH.NLT.196 Level 2: Description only - no comment
		FN: XXXX : OH.NLT.187 Level 1:Description only - no comment --}}
		<small>
		@foreach($b_unit_findings as $fin)
		F|N #{{ $fin->id }}:
		<span>OH.{{ strtoupper($fin->finding_type->type) }}.{{ $fin->finding_type_id }} @if($fin->level) LEVEL {{ $fin->level }} @endif:{{ $fin->finding_type->name }}
		</span>
		@endforeach
		</small>
	@endif
@endif



@if(count($unit_findings))
<hr class="dashed-hr uk-margin-bottom">
	@php
		$f = $unit_findings->first();
	@endphp
		{{--  AUDIT: XXXXX --}} {{-- UNIT: XX-XXXXXX  --}}
		<div class="uk-width-1-1">
			<span class="uk-text-uppercase"> AUDIT: {{ $f->audit_id }}, </span> <strong><span class="uk-text-uppercase"> UNIT: {{ $f->unit->unit_name }}</span></strong>
		</div>
		{{-- Addresss Dr, City, ZIP345 --}}
		@if(!is_null($f->unit->building->address))
			<div class="uk-width-1-1">
				<small style="text-transform: uppercase;">{{ $f->unit->building->address->line_1 }} {{ $f->unit->building->address->line_2 }} |
					{{ $f->unit->building->address->city }}, {{ $f->unit->building->address->state }} {{ $f->unit->building->address->zip }}
				</small>
			</div>
		@endif
		{{-- FN: XXXX : OH.NLT.196 Level 2: Description only - no comment
		FN: XXXX : OH.NLT.187 Level 1:Description only - no comment --}}
		<small>
		@foreach($unit_findings as $fin)
		<div>
		F|N #{{ $fin->id }}:
		<span>OH.{{ strtoupper($fin->finding_type->type) }}.{{ $fin->finding_type_id }} @if($fin->level) LEVEL {{ $fin->level }} @endif:{{ $fin->finding_type->name }}
		</span>
		</div>
		@endforeach
		</small>
@endif



