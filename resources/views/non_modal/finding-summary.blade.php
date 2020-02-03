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
$line = 0;
@endphp

{{-- Site --}}
@if(count($site_findings))
	@php
	$f = $site_findings->first();
	$line = 1;
	@endphp
	<div class="uk-width-1-1 small-line-height">
		<span class="uk-text-uppercase"> AUDIT: {{ $f->audit_id }}, </span> <strong><span class="uk-text-uppercase"> SITE: {{ $f->project->project_name }}</span></strong>
	</div>
	@if($f->project->address)
		<div class="uk-width-1-1 small-line-height">
			<small style="text-transform: uppercase;">{{ $f->project->address->line_1 }} {{ $f->project->address->line_2 }} |
			{{ $f->project->address->city }}, {{ $f->project->address->state }} {{ $f->project->address->zip }}
			</small>
		</div>
	@endif
	<small>
	@foreach($site_findings as $fin)
	<div class="small-line-height uk-margin-small-top uk-margin-small-left">
		<strong>
		@if($fin->finding_type->type == 'nlt')
			<i class="a-booboo"></i>
		@endif
		@if($fin->finding_type->type == 'lt')
			<i class="a-skull"></i>
		@endif
		@if($fin->finding_type->type == 'file')
		<i class="a-folder"></i>
		@endif
		<span class="use-hand-cursor" onclick="openFindingDetails({{ $fin->id }})">F|N #{{ $fin->id }}:</span>
		</strong>
		<span> {{ $fin->amenity->amenity_description }}, OH.{{ strtoupper($fin->finding_type->type) }}.{{ $fin->finding_type_id }} @if($fin->level) LEVEL {{ $fin->level }} @endif:{{ $fin->finding_type->name }}
		</span>
	</div>
	@endforeach
	</small>
@endif


{{-- Buildings --}}
@if(count($building_findings))
	@if($line)
	<hr class="dashed-hr-small-margin uk-margin-small-bottom">
	@endif
	{{-- Check if there are any unit specific findings for this building --}}
	@php
	$line = 1;
	$f = $building_findings->first();
	$b_unit_findings = $unit_findings->where('unit.building_id', $f->building_id);
	if(count($b_unit_findings)) {
		$unit_findings = $unit_findings->where('unit.building_id', '<>', $f->building_id);
	}
	@endphp
	<div class="uk-width-1-1 small-line-height">
		<span class="uk-text-uppercase"> AUDIT: {{ $f->audit_id }}, </span> <strong><span class="uk-text-uppercase"> BIN: {{ $f->building->building_name }}</span></strong>
	</div>
	@if(!is_null($f->building->address))
		<div class="uk-width-1-1 small-line-height">
			<small style="text-transform: uppercase;">{{ $f->building->address->line_1 }} {{ $f->building->address->line_2 }} |
				{{ $f->building->address->city }}, {{ $f->building->address->state }} {{ $f->building->address->zip }}
			</small>
		</div>
	@endif
	<small>
	@foreach($building_findings as $fin)
	<div class="small-line-height uk-margin-small-top uk-margin-small-left">
		<strong>
		@if($fin->finding_type->type == 'nlt')
			<i class="a-booboo"></i>
		@endif
		@if($fin->finding_type->type == 'lt')
			<i class="a-skull"></i>
		@endif
		@if($fin->finding_type->type == 'file')
		<i class="a-folder"></i>
		@endif
		<span class="use-hand-cursor" onclick="openFindingDetails({{ $fin->id }})">F|N #{{ $fin->id }}:</span>
		</strong>
		<span> {{ $fin->amenity->amenity_description }}, OH.{{ strtoupper($fin->finding_type->type) }}.{{ $fin->finding_type_id }} @if($fin->level) LEVEL {{ $fin->level }} @endif:{{ $fin->finding_type->name }}
		</span>
	</div>
	@endforeach
	</small>

	@if(count($b_unit_findings)) {
		@php
			$f = $b_unit_findings->first();
		@endphp
		<div class="uk-width-1-1 small-line-height">
			<strong><span class="uk-text-uppercase"> AUDIT: {{ $f->audit_id }}, </span> <span class="uk-text-uppercase"> UNIT: {{ $f->unit->unit_name }}</span></strong>
		</div>
		<small>
		@foreach($b_unit_findings as $fin)
		<div class="small-line-height uk-margin-small-top uk-margin-small-left">
			<strong>
			@if($fin->finding_type->type == 'nlt')
				<i class="a-booboo"></i>
			@endif
			@if($fin->finding_type->type == 'lt')
				<i class="a-skull"></i>
			@endif
			@if($fin->finding_type->type == 'file')
			<i class="a-folder"></i>
			@endif
			<span class="use-hand-cursor" onclick="openFindingDetails({{ $fin->id }})">F|N #{{ $fin->id }}:</span>
			</strong>
			<span> {{ $fin->amenity->amenity_description }}, OH.{{ strtoupper($fin->finding_type->type) }}.{{ $fin->finding_type_id }} @if($fin->level) LEVEL {{ $fin->level }} @endif:{{ $fin->finding_type->name }}
			</span>
		</div>
		@endforeach
		</small>
	@endif
@endif

{{-- Unit --}}
@if(count($unit_findings))
@if($line)
<hr class="dashed-hr-small-margin uk-margin-small-bottom">
@endif
	@php
		$f = $unit_findings->first();
	@endphp
		<div class="uk-width-1-1 small-line-height">
			<span class="uk-text-uppercase"> AUDIT: {{ $f->audit_id }}, </span> <strong><span class="uk-text-uppercase"> UNIT: {{ $f->unit->unit_name }}</span></strong>
		</div>
		@if(!is_null($f->unit->building->address))
			<div class="uk-width-1-1 small-line-height">
				<small style="text-transform: uppercase;">{{ $f->unit->building->address->line_1 }} {{ $f->unit->building->address->line_2 }} |
					{{ $f->unit->building->address->city }}, {{ $f->unit->building->address->state }} {{ $f->unit->building->address->zip }}
				</small>
			</div>
		@endif
		<small>
		@foreach($unit_findings as $fin)
		<div class="small-line-height uk-margin-small-top uk-margin-small-left">
			<strong>
			@if($fin->finding_type->type == 'nlt')
				<i class="a-booboo"></i>
			@endif
			@if($fin->finding_type->type == 'lt')
				<i class="a-skull"></i>
			@endif
			@if($fin->finding_type->type == 'file')
			<i class="a-folder"></i>
			@endif
			<span class="use-hand-cursor" onclick="openFindingDetails({{ $fin->id }})">F|N #{{ $fin->id }}:</span>
			</strong>
			<span> {{ $fin->amenity->amenity_description }}, OH.{{ strtoupper($fin->finding_type->type) }}.{{ $fin->finding_type_id }} @if($fin->level) LEVEL {{ $fin->level }} @endif:{{ $fin->finding_type->name }}
			</span>
		</div>
		@endforeach
		</small>
@endif



