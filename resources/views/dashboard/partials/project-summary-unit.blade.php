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
	$unit_selected = collect($unitprogram->pluck('unitInspected')->flatten())->where('audit_id', $unit->audit_id)->whereIn('program_key', $unitprogram->pluck('program_key'));
	$selected_units_count = $unit_selected->count();
	//dd($unit_selected);
	@endphp
	<div class="unit-group modal-project-summary-unit summary-unit-{{ $unit->unit_id }} {{ $selected_units_count > 0 ? 'has-selected' : 'no-selection' }} building-{{$unit->unit->building_key}} uk-width-1-1">
		<div class="modal-project-summary-unit-status">
			<i class="a-circle" uk-tooltip="title:SELECT ALL ELIGIBLE PROGRAMS FOR BOTH INSPECTIONS;" style="display:none" onclick="projectSummarySelection(this, {{ $unit->unit_id }});">
			</i>
		</div>
		<div class="modal-project-summary-unit-info">
			<div class="modal-project-summary-unit-info-icon">
				<i class="a-marker-basic uk-text-muted uk-link use-hand-cursor" uk-tooltip="title:View On Map;" title="" aria-expanded="false" onclick="window.open('https://maps.google.com/maps?q={{ $unit->unit->building->address->line_1 }}+{{ $unit->unit->building->address->city }}+{{ $unit->unit->building->address->state }}+{{ $unit->unit->building->address->zip }}');"></i>
			</div>
			<div class="modal-project-summary-unit-info-main">
				<h4 class="uk-margin-bottom-remove">{{$unit->unit->building->building_name}}<br />{!! $unit->unit->building->address->formatted_address($unit->unit->unit_name) !!}<br />
					@if($unit->unit->most_recent_event())
					{{ $unit->unit->most_recent_event()->type->event_type_description }}: {{ formatDate($unit->unit->most_recent_event()->event_date) }}
					@endIf
				</h4>
			</div>
		</div>
	</div>
	@foreach($unitprogram as $each_program)

		@php
		//dd($each_program->project_program->multiple_building_election_key);
		$program_selected = $unit_selected->where('program_key', $each_program->program_key);
		$all_groups_count = $each_program->program->relatedGroups->count();
		$htc_groups = $each_program->program->relatedGroups->where('id', $htc_group_id);
		$htc_groups_count = $htc_groups->count();
		$other_groups = $each_program->program->relatedGroups->whereNotIn('id', [$htc_group_id]);
		//this is complicated
		//	Determine the htc groups that separate them
		//	HTC GROUP
		//		If selected send this program id and group id
		//	OTHER GROUPS
		//		If selected send this program id and comma seperated group ids
		if($htc_groups_count > 0 && ($all_groups_count > $htc_groups_count)) {
			$split = true;
			// $htc_program_selected = $program_selected->where('group_id', $htc_group_id);
			// //$htc_unit_program_selected_count = $htc_program_selected->where('is_site_visit', 1)->where('is_file_audit', 1)->count();
			// $htc_site_program_selected_count = $htc_program_selected->where('is_site_visit', 1)->count();
			// $htc_file_program_selected_count = $htc_program_selected->where('is_file_audit', 1)->count();
			// $htc_group_ids = json_encode([$htc_group_id]);
			// if($htc_site_program_selected_count > 0 && $htc_file_program_selected_count > 0) {
			// 	$htc_unit_program_selected_count = 1;
			// } else {
			// 	$htc_unit_program_selected_count = 0;
			// }
			// $program_selected = $program_selected->where('group_id','!=', $htc_group_id);
			// //$unit_program_selected_count = $program_selected->where('is_site_visit', 1)->where('is_file_audit', 1)->count();
			// $site_program_selected_count = $program_selected->where('is_site_visit', 1)->count();
			// $file_program_selected_count = $program_selected->where('is_file_audit', 1)->count();
			// $group_ids = $other_groups->pluck('id')->toJson();
		} else {
			$split = false;
		}
			//$unit_program_selected_count = $program_selected->where('is_site_visit', 1)->where('is_file_audit', 1)->count();
			$site_program_selected_count = $program_selected->where('is_site_visit', 1)->count();
			$file_program_selected_count = $program_selected->where('is_file_audit', 1)->count();
			$group_ids = $each_program->program->relatedGroups->pluck('id')->toJson();
		
		if($site_program_selected_count > 0 && $file_program_selected_count > 0) {
			$unit_program_selected_count = 1;
		} else {
			$unit_program_selected_count = 0;
		}
		// if($each_program->program_key == 30004)
		// dd($htc_program_selected);
		@endphp
		
		
		
		<div class="unit-group modal-project-summary-unit-programs uk-margin-remove uk-width-1-1  summary-unit-programs-{{ $each_program->unit_id }} {{ $selected_units_count > 0 ? 'has-selected' : 'no-selection' }} building-{{$unit->unit->building_key}}">
			<div class="modal-project-summary-unit-program uk-visible-toggle">
				<div class="uk-invisible-hover modal-project-summary-unit-program-quick-toggle {{ $unit_program_selected_count > 0 ? 'inspectable-selected' : '' }}" data-unitid="{{ $each_program->unit_id }}">
					@if($unit_program_selected_count > 0)
					<i class="a-circle-checked" onclick="projectSummarySelection(this, {{ $each_program->unit_id }}, {{ $each_program->program_key }}, {{ $group_ids }},'both',@if($unit->unit->building_key && $htc_groups->count() > 0 && $each_program->project_program && $each_program->project_program->multiple_building_election_key != 2) {{$unit->unit->building_key}} @else 'none' @endif);"></i>
					@else

					<i class="a-circle" onclick="projectSummarySelection(this, {{ $each_program->unit_id }}, {{ $each_program->program_key }}, {{ $group_ids }},'both',@if($unit->unit->building_key && $htc_groups->count() > 0 && $each_program->project_program && $each_program->project_program->multiple_building_election_key != 2) {{$unit->unit->building_key}} @else 'none' @endif);"></i>
					@endif
				</div>
				<div class="modal-project-summary-unit-program-info">
					<div class="modal-project-summary-unit-program-icon {{ $site_program_selected_count > 0 ? 'inspectable-selected': '' }}" data-unitid="{{ $each_program->unit_id }}" onclick="projectSummarySelection(this, {{ $each_program->unit_id }}, {{ $each_program->program_key }}, {{ $group_ids }}, 'physical',@if($unit->unit->building_key && $htc_groups->count() > 0 && $each_program->project_program && $each_program->project_program->multiple_building_election_key != 2) {{$unit->unit->building_key}} @else 'none' @endif);">
						<i class="a-mobile"></i>
						<div class="modal-project-summary-unit-program-icon-status">
							@if($site_program_selected_count > 0)
							<i class="a-circle-checked"></i>
							@else
							<i class="a-circle"></i>
							@endif
						</div>
					</div>
					<div class="modal-project-summary-unit-program-icon {{ $file_program_selected_count > 0 ? 'inspectable-selected': '' }}"  data-unitid="{{ $each_program->unit_id }}" onclick="projectSummarySelection(this, {{ $each_program->unit_id }}, {{ $each_program->program_key }}, {{ $group_ids }}, 'file',@if($unit->unit->building_key && $htc_groups->count() > 0 && $each_program->project_program && $each_program->project_program->multiple_building_election_key != 2) {{$unit->unit->building_key}} @else 'none' @endif);">
						<i class="a-folder"></i>
						<div class="modal-project-summary-unit-program-icon-status">
							@if( $file_program_selected_count > 0 )
							<i class="a-circle-checked"></i>
							@else
							<i class="a-circle"></i>
							@endif
						</div>
					</div>
					@if($each_program->is_substitute) Substitute for: @endIf {{ $each_program->program->program_name }}
					@if($split)
						({{ implode(', ', $other_groups->pluck('group_name')->toArray()) }})
					@else
						({{ implode(', ', $each_program->program->relatedGroups->pluck('group_name')->toArray()) }})
					@endif
				</div>
			</div>
		</div>

		@if($loop->last)
		@foreach($substitute_programs as $ex_pr)
		@php
			$other_group_ids = array_diff($ex_pr['group_ids'], [$htc_group_id]);
			if(empty($other_group_ids)) {
				continue;
			}
			$sub_group_ids = [];
			$pr_group_coll = $ex_pr['related_groups'];
			$sub_groups = array_column($pr_group_coll, 'id');
			$sub_group_ids = json_encode($sub_groups);
		@endphp
			<div class="unit-group modal-project-summary-unit-programs uk-margin-remove uk-width-1-1  summary-unit-programs-{{ $unit->unit_id }} {{ $selected_units_count > 0 ? 'has-selected' : 'no-selection' }} building-{{$unit->unit->building_key}}">
				<div class="modal-project-summary-unit-program uk-visible-toggle">
					<div class="uk-invisible-hover modal-project-summary-unit-program-quick-toggle" data-unitid="{{ $unit->unit_id }}">
						<i class="a-circle" onclick="projectSummarySelection(this, {{ $unit->unit_id }}, {{ $ex_pr['program_key'] }}, {{ $sub_group_ids }});"></i>
					</div>
					<div class="modal-project-summary-unit-program-info">
						<div class="modal-project-summary-unit-program-icon" data-unitid="{{ $unit->unit_id }}" onclick="projectSummarySelection(this, {{ $unit->unit_id }}, {{ $ex_pr['program_key'] }}, {{ $sub_group_ids }}, 'physical');">
							<i class="a-mobile"></i>
							<div class="modal-project-summary-unit-program-icon-status">
								<i class="a-circle"></i>
							</div>
						</div>
						<div class="modal-project-summary-unit-program-icon"  data-unitid="{{ $unit->unit_id }}" onclick="projectSummarySelection(this, {{ $unit->unit_id }}, {{ $ex_pr['program_key'] }}, {{ $sub_group_ids }}, 'file');">
							<i class="a-folder"></i>
							<div class="modal-project-summary-unit-program-icon-status">
								<i class="a-circle"></i>
							</div>
						</div>
						Substitute for: {{ $ex_pr['program_name'] }} (
							@foreach($ex_pr['related_groups'] as $ex_group)
								@if($ex_group['id'] != 7)
								{{ $ex_group['group_name'] }} {{ !($loop->last) ? ',' : '' }}
								@endif
							@endforeach
							)
					</div>
				</div>
			</div>
		@endforeach
		@endif
	@endforeach



@endforeach
