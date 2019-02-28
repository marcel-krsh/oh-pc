@php
$current_unitid = 0;
//$current_programkey = 0;
@endphp
@foreach($unitprograms as $unitprogram)

@if($current_unitid != $unitprogram->unit_id)
@php
$current_unitid = $unitprogram->unit_id;
//$current_programkey = $unitprogram->program_key;
$extra_programs = array();collect($unitprogram->program->toArray())->diffAssoc($actual_programs);
foreach($actual_programs as $actual_prog) {
	if($unitprogram->program->program_key != $actual_prog['program_key']) {
		$extra_programs[] = $actual_prog;
	}
}
@endphp
<div class="modal-project-summary-unit summary-unit-{{$unitprogram->unit_id}} @if($unitprogram->unitHasSelection()) has-selected @else no-selection @endif">
	<div class="modal-project-summary-unit-status">
		<i class="a-circle" uk-tooltip="title:SELECT ALL ELIGIBLE PROGRAMS FOR BOTH INSPECTIONS;" onclick="projectSummarySelection(this, {{$unitprogram->unit_id}});">
		</i>
	</div>
	<div class="modal-project-summary-unit-info">
		<div class="modal-project-summary-unit-info-icon">
			<i class="a-marker-basic uk-text-muted uk-link use-hand-cursor" uk-tooltip="title:View On Map;" title="" aria-expanded="false" onclick="window.open('https://maps.google.com/maps?q={{$unitprogram->unit->building->address->line_1}}+{{$unitprogram->unit->building->address->city}}+{{$unitprogram->unit->building->address->state}}+{{$unitprogram->unit->building->address->zip}}');"></i>
		</div>
		<div class="modal-project-summary-unit-info-main">
			<h4 class="uk-margin-bottom-remove">{!!$unitprogram->unit->building->address->formatted_address($unitprogram->unit->unit_name)!!}<br />
				{{$unitprogram->unit->most_recent_event()->type->event_type_description}}: {{formatDate($unitprogram->unit->most_recent_event()->event_date)}}
			</h4>
		</div>
	</div>
</div>
<div class="modal-project-summary-unit-programs uk-margin-remove uk-width-1-1  summary-unit-programs-{{$unitprogram->unit_id}} @if($unitprogram->unitHasSelection()) has-selected @else no-selection @endif" >
	<div class="modal-project-summary-unit-program uk-visible-toggle">
		<div class="uk-invisible-hover modal-project-summary-unit-program-quick-toggle @if($unitprogram->hasSiteInspection() && $unitprogram->hasFileInspection()) inspectable-selected @endif"  data-unitid="{{$unitprogram->unit_id}}">
			@if($unitprogram->hasSiteInspection() && $unitprogram->hasFileInspection())
			<i class="a-circle-checked" onclick="projectSummarySelection(this, {{$unitprogram->unit_id}}, {{$unitprogram->program_id}});"></i>
			@else
			<i class="a-circle" onclick="projectSummarySelection(this, {{$unitprogram->unit_id}}, {{$unitprogram->program_id}});"></i>
			@endif
		</div>
		<div class="modal-project-summary-unit-program-info">
			<div class="modal-project-summary-unit-program-icon @if($unitprogram->hasSiteInspection()) inspectable-selected @endif" data-unitid="{{$unitprogram->unit_id}}" onclick="projectSummarySelection(this, {{$unitprogram->unit_id}}, {{$unitprogram->program_id}}, 'physical');">
				<i class="a-mobile"></i>
				<div class="modal-project-summary-unit-program-icon-status">
					@if($unitprogram->hasSiteInspection())
					<i class="a-circle-checked"></i>
					@else
					<i class="a-circle"></i>
					@endif
				</div>
			</div>
			<div class="modal-project-summary-unit-program-icon @if($unitprogram->hasFileInspection()) inspectable-selected @endif"  data-unitid="{{$unitprogram->unit_id}}" onclick="projectSummarySelection(this, {{$unitprogram->unit_id}}, {{$unitprogram->program_id}}, 'file');">
				<i class="a-folder"></i>
				<div class="modal-project-summary-unit-program-icon-status">
					@if($unitprogram->hasFileInspection())
					<i class="a-circle-checked"></i>
					@else
					<i class="a-circle"></i>
					@endif
				</div>
			</div>
			{{ $unitprogram->program->program_name }} ({{ implode(', ', $unitprogram->program->relatedGroups->pluck('group_name')->toArray())}})
			@foreach($extra_programs as $ex_pr)
			<br> Substitute for: 	{{($ex_pr['program_name'])}} ({{ implode(', ', collect($ex_pr['related_groups'])->pluck('group_name')->toArray())}})
			@endforeach
		</div>
	</div>
</div>
@else
<div class="modal-project-summary-unit-programs summary-unit-programs-{{$unitprogram->unit_id}} uk-margin-remove uk-width-1-1 @if($unitprogram->unitHasSelection()) has-selected @else no-selection @endif">
	<div class="modal-project-summary-unit-program uk-visible-toggle">
		<div class="uk-invisible-hover modal-project-summary-unit-program-quick-toggle @if($unitprogram->hasSiteInspection() && $unitprogram->hasFileInspection()) inspectable-selected @endif"  data-unitid="{{$unitprogram->unit_id}}">
			@if($unitprogram->hasSiteInspection() && $unitprogram->hasFileInspection())
			<i class="a-circle-checked" onclick="projectSummarySelection(this, {{$unitprogram->unit_id}}, {{$unitprogram->program_id}});"></i>
			@else
			<i class="a-circle" onclick="projectSummarySelection(this, {{$unitprogram->unit_id}}, {{$unitprogram->program_id}});"></i>
			@endif
		</div>
		<div class="modal-project-summary-unit-program-info">
			<div class="modal-project-summary-unit-program-icon @if($unitprogram->hasSiteInspection()) inspectable-selected @endif"  data-unitid="{{$unitprogram->unit_id}}" onclick="projectSummarySelection(this, {{$unitprogram->unit_id}}, {{$unitprogram->program_id}}, 'physical');">
				<i class="a-mobile"></i>
				<div class="modal-project-summary-unit-program-icon-status">
					@if($unitprogram->hasSiteInspection())
					<i class="a-circle-checked"></i>
					@else
					<i class="a-circle"></i>
					@endif
				</div>
			</div>
			<div class="modal-project-summary-unit-program-icon @if($unitprogram->hasFileInspection()) inspectable-selected @endif" data-unitid="{{$unitprogram->unit_id}}" onclick="projectSummarySelection(this, {{$unitprogram->unit_id}}, {{$unitprogram->program_id}}, 'file');">
				<i class="a-folder"></i>
				<div class="modal-project-summary-unit-program-icon-status">
					@if($unitprogram->hasFileInspection())
					<i class="a-circle-checked"></i>
					@else
					<i class="a-circle"></i>
					@endif
				</div>
			</div>
			{{$unitprogram->program->program_name}}
		</div>
	</div>
</div>
@endif

@endforeach