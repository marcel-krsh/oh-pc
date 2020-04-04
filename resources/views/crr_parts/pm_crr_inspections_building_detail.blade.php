@forEach($inspections as $i)

	<?php
	$currentBuilding = $i->building_id;
	$findingCount = 'findings' . $currentBuilding;
	if($canViewSiteInspections){
		$thisBuildingValues = collect($inspections)->where('building_id', $i->building_id)->sortByDesc('is_site_visit');

	}else{
		$thisBuildingValues = collect([]);
	}
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
	if($canViewFindings){
		$findingCount = collect($findings);

	}else{
		$findingCount = collect([]);
	}
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
	if($canViewFindings){
		$thisBuildingSiteFindings = count($findingCount->where('finding_type.type', '!=', 'file'));
		$thisBuildingResolvedSiteFindings = count($findingCount->where('finding_type.type', '!=', 'file')->where('auditor_approved_resolution', 1));
		$thisBuildingUnresolvedSiteFindings = $thisBuildingSiteFindings - $thisBuildingResolvedSiteFindings;
		$thisBuildingFileFindings = count($findingCount->where('finding_type.type', '==', 'file'));
		$thisBuildingResolvedFileFindings = count($findingCount->where('finding_type.type', '==', 'file')->where('auditor_approved_resolution', 1));
		$thisBuildingUnresolvedFileFindings = $thisBuildingFileFindings - $thisBuildingResolvedFileFindings;

	} else {
		$thisBuildingSiteFindings = 0;
		$thisBuildingResolvedSiteFindings = 0;
		$thisBuildingUnresolvedSiteFindings = 0;
		$thisBuildingFileFindings = 0;
		$thisBuildingResolvedFileFindings = 0;
		$thisBuildingUnresolvedFileFindings = 0;
	}


	if ($thisBuildingSiteFindings || $thisBuildingFileFindings) {
		$hasFindings = 1;
	}

	?>
	<div  class="inspection-data-row">
		<div  class="unit-name"  >
			@if($print !== 1 && !$dpView)
			<a href="#findings-list" class="uk-link-mute">
				@elseif($dpView)
				<span  >
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
			@if($canViewFindings)
			<br /> <span class="attention" style="color:red; display: inline-block;margin-top: 5px;"><i class="a-multiply"></i> UNCORRECTED </span>
			@else
			 <span class="" style="display: inline-block;margin-top: 5px;"><i class="a-circle"></i> INSPECTION PENDING </span>
			@endIf
			@if($dpView)
			<br/> <small><i class="a-circle-checked"></i> INSPECTION COMPLETE</small>
			@endIf
			@elseif($dpView && count($thisBuildingUnfinishedInspections) && ($canViewFindings) || count($selected_audit->audit->findings) && $canViewFindings))
			<br /> <small><i class="a-circle"></i> INSPECTION IN PROGRESS</small>
			@elseif($dpView && !count($thisBuildingUnfinishedInspections) && !$hasFindings && $canViewFindings)
			<br /><small><i class="a-circle-checked"></i> INSPECTION COMPLETE </small>
			@elseif($dpView && !count($thisBuildingUnfinishedInspections) && $hasFindings && $canViewFindings)
			<br /><small ><i class="a-circle"></i> INSPECTION INCOMPLETE </small>
			@else
			<br /><small ><i class="a-circle"></i> INSPECTION PENDING</small>
			@endIf

			@if($dpView)
			@if($building_auditors && count($building_auditors) > 0)
			<br /><small>AUDITORS ASSIGNED:</small>
			@foreach($building_auditors as $auditor)
			<div class="amenity-auditor uk-margin-remove auditor-badge-on-details">
				<div id="building-{{ $i->building_id }}-avatar-{{ $loop->iteration }}" uk-tooltip="pos:top-left;title:{{ strtoupper($auditor->full_name()) }};" title="" aria-expanded="false" class="auditor-badge auditor-badge-{{ $auditor->badge_color }} no-float">
					{{ $auditor->initials() }}
				</div>
			</div>
			@endforeach
			@else
			<div class="uk-inline uk-padding-remove" style="margin-top:6px; margin: 3px 3px 3px 3px; font-size: 20px">
				<i class="a-avatar" uk-tooltip title="UNASSIGNED">
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
			@if($canViewSiteInspections)
			<i class="a-mobile uk-text-large uk-margin-small-right" ></i> @if($thisBuildingSiteFindings > 0 && $canViewFindings) <span class="uk-badge finding-number on-phone @if($thisBuildingUnresolvedSiteFindings > 0) attention @endIf" uk-tooltip title="{{ $thisBuildingSiteFindings }} @if($thisBuildingSiteFindings > 1) FINDINGS @else FINDING @endIf @if($thisBuildingUnresolvedSiteFindings > 0) WITH {{ $thisBuildingUnresolvedSiteFindings }} PENDING RESOLUTION @else FULLY RESOLVED @endIf">{{ $thisBuildingSiteFindings }}</span> @elseif($canViewFindings)<i class="a-circle-checked on-phone no-findings"></i> @else <span class="uk-badge finding-number on-phone" >NA</span> @endif
			@endIf
			@if($canViewFileInspections)
			<i class="a-folder uk-text-large @if($auditor_access)@if(!$print)use-hand-cursor @endif @endif" ></i> @if($thisBuildingFileFindings > 0 && $canViewFindings) <span class="uk-badge finding-number on-folder @if($thisBuildingUnresolvedFileFindings > 0) attention @endIf" uk-tooltip title="{{ $thisBuildingFileFindings }} @if($thisBuildingFileFindings > 1) FINDINGS @else FINDING @endIf @if($thisBuildingUnresolvedFileFindings > 0) WITH {{ $thisBuildingUnresolvedFileFindings }} PENDING RESOLUTION @else FULLY RESOLVED @endIf">{{ $thisBuildingFileFindings }}</span> @elseif($canViewFindings)<i class="a-circle-checked on-folder no-findings"></i> @else <span class="uk-badge finding-number on-folder">NA</span>@endIf
			@endIf

		</div>
		<hr class="dashed-hr uk-margin-small-bottom">
	</div>

	@endForEach