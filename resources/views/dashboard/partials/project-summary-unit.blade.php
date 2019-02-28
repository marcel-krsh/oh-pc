@foreach($unitprograms as $unit_id => $unitprogram)
@php
$extra_programs = array();
$assigned_temp_programs = ($unitprogram->pluck('program'));
if(!empty($assigned_temp_programs)) {
	$assigned_temp_program_keys = $assigned_temp_programs->pluck('program_key');
	$substitute_programs = collect($actual_programs)->whereNotIn('program_key', $assigned_temp_program_keys);
} else {
	$substitute_programs = $actual_programs;
}
$unit = $unitprogram->first();
$unit_selected = collect($unitprogram->pluck('unitInspected')->flatten())
																			->where('audit_id', $unit->audit_id);
@endphp
<div class="modal-project-summary-unit summary-unit-{{ $unit->unit_id }} {{ empty($unit_selected) ? 'no-selection' : 'has-selected' }}">
	<div class="modal-project-summary-unit-status">
		<i class="a-circle" uk-tooltip="title:SELECT ALL ELIGIBLE PROGRAMS FOR BOTH INSPECTIONS;" onclick="projectSummarySelection(this, {{ $unit->unit_id }});">
		</i>
	</div>
	<div class="modal-project-summary-unit-info">
		<div class="modal-project-summary-unit-info-icon">
			<i class="a-marker-basic uk-text-muted uk-link use-hand-cursor" uk-tooltip="title:View On Map;" title="" aria-expanded="false" onclick="window.open('https://maps.google.com/maps?q={{ $unit->unit->building->address->line_1 }}+{{ $unit->unit->building->address->city }}+{{ $unit->unit->building->address->state }}+{{ $unit->unit->building->address->zip }}');"></i>
		</div>
		<div class="modal-project-summary-unit-info-main">
			<h4 class="uk-margin-bottom-remove">{!! $unit->unit->building->address->formatted_address($unit->unit->unit_name) !!}<br />
				{{ $unit->unit->most_recent_event()->type->event_type_description }}: {{ formatDate($unit->unit->most_recent_event()->event_date) }}
			</h4>
		</div>
	</div>
</div>
@foreach($unitprogram as $each_program)
@php
$program_selected = $unit_selected->where('program_key', $each_program->program_key);
$unit_program_selected = $program_selected->where('is_site_visit', 1)->where('is_file_visit', 1);
$site_program_selected = $program_selected->where('is_site_visit', 1);
$file_program_selected = $program_selected->where('is_file_visit', 1);
@endphp
<div class="modal-project-summary-unit-programs uk-margin-remove uk-width-1-1  summary-unit-programs-{{ $each_program->unit_id }} {{ empty($unit_selected) ? 'no-selection' : 'has-selected' }}">
	<div class="modal-project-summary-unit-program uk-visible-toggle">
		<div class="uk-invisible-hover modal-project-summary-unit-program-quick-toggle {{ (!empty($unit_program_selected)) ? 'inspectable-selected' : '' }}" data-unitid="{{ $each_program->unit_id }}">
			@if(!empty($unit_program_selected))
			<i class="a-circle-checked" onclick="projectSummarySelection(this, {{ $each_program->unit_id }}, {{ $each_program->program_id }});"></i>
			@else
			<i class="a-circle" onclick="projectSummarySelection(this, {{ $each_program->unit_id }}, {{ $each_program->program_id }});"></i>
			@endif
		</div>
		<div class="modal-project-summary-unit-program-info">
			<div class="modal-project-summary-unit-program-icon {{ !empty($site_program_selected) ? 'inspectable-selected': '' }}" data-unitid="{{ $each_program->unit_id }}" onclick="projectSummarySelection(this, {{ $each_program->unit_id }}, {{ $each_program->program_id }}, 'physical');">
				<i class="a-mobile"></i>
				<div class="modal-project-summary-unit-program-icon-status">
					@if(!empty($site_program_selected))
					<i class="a-circle-checked"></i>
					@else
					<i class="a-circle"></i>
					@endif
				</div>
			</div>
			<div class="modal-project-summary-unit-program-icon {{ !empty($file_program_selected) ? 'inspectable-selected': '' }}"  data-unitid="{{ $each_program->unit_id }}" onclick="projectSummarySelection(this, {{ $each_program->unit_id }}, {{ $each_program->program_id }}, 'file');">
				<i class="a-folder"></i>
				<div class="modal-project-summary-unit-program-icon-status">
					@if(!empty($file_program_selected))
					<i class="a-circle-checked"></i>
					@else
					<i class="a-circle"></i>
					@endif
				</div>
			</div>
			{{ $each_program->program->program_name }} ({{ implode(', ', $each_program->program->relatedGroups->pluck('group_name')->toArray()) }})
		</div>
	</div>
</div>

@if($loop->last)
@foreach($substitute_programs as $ex_pr)
<div class="modal-project-summary-unit-programs uk-margin-remove uk-width-1-1  summary-unit-programs-{{ $unit->unit_id }} {{ empty($unit_selected) ? 'no-selection' : 'has-selected' }}">
	<div class="modal-project-summary-unit-program uk-visible-toggle">
		<div class="uk-invisible-hover modal-project-summary-unit-program-quick-toggle" data-unitid="{{ $unit->unit_id }}">
			<i class="a-circle" onclick="projectSummarySelection(this, {{ $unit->unit_id }}, {{ $unit->program_id }});"></i>
		</div>
		<div class="modal-project-summary-unit-program-info">
			<div class="modal-project-summary-unit-program-icon" data-unitid="{{ $unit->unit_id }}" onclick="projectSummarySelection(this, {{ $unit->unit_id }}, {{ $unit->program_id }}, 'physical');">
				<i class="a-mobile"></i>
				<div class="modal-project-summary-unit-program-icon-status">
					<i class="a-circle"></i>
				</div>
			</div>
			<div class="modal-project-summary-unit-program-icon"  data-unitid="{{ $unit->unit_id }}" onclick="projectSummarySelection(this, {{ $unit->unit_id }}, {{ $unit->program_id }}, 'file');">
				<i class="a-folder"></i>
				<div class="modal-project-summary-unit-program-icon-status">
					<i class="a-circle"></i>
				</div>
			</div>
			Substitute for: {{ $ex_pr['program_name'] }} ({{ implode(', ', collect($ex_pr['related_groups'])->pluck('group_name')->toArray()) }})
		</div>
	</div>
</div>
@endforeach
@endif
@endforeach



@endforeach
