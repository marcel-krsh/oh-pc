@forEach($inspections as $i)

	<?php
	$currentBuilding = $i->building_id;
	$findingCount = 'findings' . $currentBuilding;
	$thisBuildingValues = collect($inspections)->where('building_id', $i->building_id)->sortByDesc('is_site_visit');
	if ($dpView) {
		$thisBuildingUnfinishedInspections = collect($inspections)->where('building_id', $i->building_id)->where('complete', 0)->sortByDesc('is_site_visit');
		// $building_auditors = $type->auditors($audit->audit_id);
		$building_auditors = $selected_audit->audit->amenity_inspections->where('building_id', '=', $i->building_id)->where('auditor_id', '<>', null);
		//dd($inspections,$building_auditors);
		if (count($building_auditors)) {
			$b_units = $building_auditors->pluck('building')->first();
			$unit_ids = $b_units->units->pluck('id');
			$unit_auditors = $selected_audit->audit->amenity_inspections->whereIn('unit_id', $unit_ids)->where('auditor_id', '<>', null);
			$combined_auditors = $building_auditors->merge($unit_auditors);
			$building_auditors = $combined_auditors->pluck('user')->unique();
		}
	}
	$findingCount = collect($findings);
	$findingCount = $findingCount->filter(function ($item) use ($currentBuilding) {
		if ($item->building && $item->building->id == $currentBuilding) {
			return $item;
		}
		if ($item->unit && $item->unit->building_id == $currentBuilding) {
			return $item;
		}
	});
	$hasFindings = 0;
	// dd($currentBuilding,$findingCount);
	$thisBuildingSiteFindings = count($findingCount->where('finding_type.type', '!=', 'file')->where('cancelled_at',NULL));
	$thisBuildingResolvedSiteFindings = count($findingCount->where('finding_type.type', '!=', 'file')->where('auditor_approved_resolution', 1)->where('cancelled_at',NULL));
	$thisBuildingUnresolvedSiteFindings = $thisBuildingSiteFindings - $thisBuildingResolvedSiteFindings;
	$thisBuildingFileFindings = count($findingCount->where('finding_type.type', '==', 'file')->where('cancelled_at',NULL));
	$thisBuildingResolvedFileFindings = count($findingCount->where('finding_type.type', '==', 'file')->where('auditor_approved_resolution', 1)->where('cancelled_at',NULL));
	$thisBuildingUnresolvedFileFindings = $thisBuildingFileFindings - $thisBuildingResolvedFileFindings;

	if ($thisBuildingSiteFindings || $thisBuildingFileFindings) {
		$hasFindings = 1;
	}

	?>
	<div  class="inspection-data-row">
		<div  class="unit-name"  >
			@if($print !== 1 && !$dpView)
			<a href="#findings-list" class="uk-link-mute" onClick="showOnlyFindingsFor('building-{{ $i->building_id }}-finding');">
				@elseif($dpView)
				<span class="use-hand-cursor" onclick="openFindings(this, {{ $report->audit->id }}, {{ $i->building_id }}, null, 'file',null,'0');" >
					@endIf
					{{ $i->building_name }}
					@if($print !== 1 && !$dpView)
				</a>
				@elseif($dpView)
			</span>
			@endIf
			@if($hasFindings && property_exists($i, 'latest_resolution') && $i->latest_resolution)
			<br /> <i class="a-checkbox-checked" uk-tooltip title ="ALL ITEMS CORRECTED"></i> {{ date('m/d/Y',strtotime($i->latest_resolution)) }}
			@elseIf(($hasFindings && property_exists($i,'latest_resolution')) || ($dpView && !count($thisBuildingUnfinishedInspections) && $hasFindings && $i->latest_resolution == null))
			<br /> <span class="attention" style="color:red; display: inline-block;margin-top: 5px;"><i class="a-multiply"></i> UNCORRECTED </span>
			@if($dpView)
			<br/> <small onclick="openFindings(this, {{ $report->audit->id }}, {{ $i->building_id }}, null, 'file',null,'0');" class="use-hand-cursor"><i class="a-circle-checked"></i> INSPECTION COMPLETE</small>
			@endIf
			@elseif($dpView && count($thisBuildingUnfinishedInspections) && ($selected_audit->step_id > 59 || count($selected_audit->audit->findings)))
			<br /> <small onclick="openFindings(this, {{ $report->audit->id }}, {{ $i->building_id }}, null, 'file',null,'0');" class="use-hand-cursor"><i class="a-circle"></i> INSPECTION IN PROGRESS</small>
			@elseif($dpView && !count($thisBuildingUnfinishedInspections) && !$hasFindings)
			<br /><small onclick="openFindings(this, {{ $report->audit->id }}, {{ $i->building_id }}, null, 'file',null,'0');" class="use-hand-cursor"><i class="a-circle-checked"></i> INSPECTION COMPLETE </small>
			@elseif($dpView && !count($thisBuildingUnfinishedInspections) && $hasFindings)
			<br /><small onclick="openFindings(this, {{ $report->audit->id }}, {{ $i->building_id }}, null, 'file',null,'0');" class="use-hand-cursor"><i class="a-circle"></i> INSPECTION INCOMPLETE </small>
			@endIf

			@if($dpView)
			@if($building_auditors && count($building_auditors) > 0)
			<br /><small>AUDITORS ASSIGNED:</small>
			@foreach($building_auditors as $auditor)
			<div class="amenity-auditor uk-margin-remove auditor-badge-on-details">
				<div id="building-{{ $i->building_id }}-avatar-{{ $loop->iteration }}" uk-tooltip="pos:top-left;title:{{ strtoupper($auditor->full_name()) }};" title="" aria-expanded="false" class="auditor-badge auditor-badge-{{ $auditor->badge_color }} use-hand-cursor no-float" onclick="swapFindingsAuditor({{ $auditor->id }}, {{ $selected_audit->audit_id }}, {{ $i->building_id }}, 0, 'building-auditors-{{ $i->building_id }}',1)">
					{{ $auditor->initials() }}
				</div>
			</div>
			@endforeach
			@else
			<div class="uk-inline uk-padding-remove" style="margin-top:6px; margin: 3px 3px 3px 3px; font-size: 20px">
				<i class="a-avatar-plus_1" uk-tooltip title="NEEDS ASSIGNED" onclick="assignFindingAuditor({{ $selected_audit->audit_id }}, {{ $i->building_id }}, 0, 0, 'building-auditor-0', 0, 0, 0, 2,1);">
				</i>
			</div>
			@endif
			@endIf
		</div>
		{{--
		<div style="float: left;">
			{{ $i->building_name }}
		</div> --}}
		<div style="float: right;">
			<i class="a-mobile uk-text-large uk-margin-small-right @if($auditor_access)@if(!$print)use-hand-cursor @endif @endif"  @if($auditor_access)@if(!$print) onclick="openFindings(this, {{ $report->audit->id }}, {{ $i->building_id }}, null, null, null,'0');" @endif  @endif></i> @if($thisBuildingSiteFindings > 0) <span class="uk-badge finding-number on-phone @if($thisBuildingUnresolvedSiteFindings > 0) attention @endIf" uk-tooltip title="{{ $thisBuildingSiteFindings }} @if($thisBuildingSiteFindings > 1) FINDINGS @else FINDING @endIf @if($thisBuildingUnresolvedSiteFindings > 0) WITH {{ $thisBuildingUnresolvedSiteFindings }} PENDING RESOLUTION @else FULLY RESOLVED @endIf">{{ $thisBuildingSiteFindings }}</span> @else<i class="a-circle-checked on-phone no-findings"></i>@endif
			<i class="a-folder uk-text-large @if($auditor_access)@if(!$print)use-hand-cursor @endif @endif" @if($auditor_access)@if(!$print) onclick="openFindings(this, {{ $report->audit->id }}, {{ $i->building_id }}, null, 'file',null,'0');" @endif @endif></i> @if($thisBuildingFileFindings > 0) <span class="uk-badge finding-number on-folder @if($thisBuildingUnresolvedFileFindings > 0) attention @endIf" uk-tooltip title="{{ $thisBuildingFileFindings }} @if($thisBuildingFileFindings > 1) FINDINGS @else FINDING @endIf @if($thisBuildingUnresolvedFileFindings > 0) WITH {{ $thisBuildingUnresolvedFileFindings }} PENDING RESOLUTION @else FULLY RESOLVED @endIf">{{ $thisBuildingFileFindings }}</span> @else<i class="a-circle-checked on-folder no-findings"></i>@endIf

		</div>
		<hr class="dashed-hr uk-margin-small-bottom">
	</div>

	@endForEach