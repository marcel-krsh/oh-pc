<?php
$inspections = $bladeData;
$projectDetails = null;
$findings = null;
$dpView = null;
if (array_key_exists(4, $pieceData)) {
	$projectDetails = $pieceData[4];
}
if (array_key_exists(5, $pieceData)) {
	$findings = $pieceData[5];
}
if (isset($pdtDetails)) {
	$projectDetails = $pdtDetails;
	$findings = $pdtFindings;
}
if (isset($detailsPage)) {
	$dpView = 1;
}
?>
@if(null == $projectDetails)
@if(session('projectDetailsOutput') == 0)
<div id="project-details-stats" class="uk-width-1-1 uk-grid-margin uk-first-column" style="margin-top:20px;">
	<div uk-grid="" class="uk-grid">
		<div class="uk-width-1-1">
			<h2>Project Details: </h2>
		</div>
		<div class="uk-width-2-3 uk-first-column">
			<ul class="leaders" style="margin-right:30px;">
				<li><span>Total Buildings</span> <span>{{ $projectDetails->total_building }}</span></li>
				<li><span>Total Units</span> <span>{{ $projectDetails->total_units }}</span></li>
				<li><span class="indented">• Market Rate Units</span> <span>{{ $projectDetails->market_rate }}</span></li>
				<li><span class="indented">• Program Units</span> <span>{{ $projectDetails->subsidized }}</span></li>
				<?php $pdPrograms = json_decode($projectDetails->programs);?>
				<li><span>Programs</span> <span>{{ count($pdPrograms) }}</span></li>
				<?php $pdpLoop = 1;?>
				@forEach($pdPrograms as $pdp)
				<li><span class="indented">• [{{ $pdpLoop }}] {{ $pdp->name }}</span> <span>{{ $pdp->units }}</span></li>
				<?php $programReference[$pdp->program_id] = $pdpLoop;
$pdpLoop++;?>
				@endForEach
			</ul>
		</div>
		<div class="uk-width-1-3">
			<h5 class="uk-margin-remove"><strong>OWNER: </strong></h5>
			<div class="address" style="margin-bottom:20px;">
				<i class="a-bank" style="font-weight: bolder;"></i> @if($projectDetails->owner_name != '') {{ $projectDetails->owner_name }} @else NA @endIf<br>
				<i class="a-avatar"></i> POC: @if($projectDetails->owner_poc != ''){{ $projectDetails->owner_poc }}@else NA @endIf<br>
				<i class="a-phone-5"></i>  @if($projectDetails->owner_phone != ''){{ $projectDetails->owner_phone }}@else NA @endIf<br>
				<i class="a-fax-2"></i>  @if($projectDetails->owner_fax != ''){{ $projectDetails->owner_fax }}@else NA @endIf<br>
				<i class="a-mail-send"></i> @if($projectDetails->owner_email != '')<a class="uk-link-mute" href="mailto:{{ $projectDetails->owner_email }}">{{ $projectDetails->owner_email }}</a>@else NA @endIf<br>
			</div>
			<h5 class="uk-margin-remove"><strong>Managed By: </strong></h5>
			<div class="address">
				<i class="a-bank" style="font-weight: bolder;"></i> @if($projectDetails->manager_name != '') {{ $projectDetails->manager_name }} @else NA @endIf<br>
				<i class="a-avatar"></i> POC: @if($projectDetails->manager_poc != ''){{ $projectDetails->manager_poc }}@else NA @endIf<br>
				<i class="a-phone-5"></i>  @if($projectDetails->manager_phone != ''){{ $projectDetails->manager_phone }}@else NA @endIf<br>
				<i class="a-fax-2"></i>  @if($projectDetails->manager_fax != ''){{ $projectDetails->manager_fax }}@else NA @endIf<br>
				<i class="a-mail-send"></i> @if($projectDetails->manager_email != '')<a class="uk-link-mute" href="mailto:{{ $projectDetails->manager_email }}">{{ $projectDetails->manager_email }}</a>@else NA @endIf<br>
			</div>
		</div>
	</div>
</div>
<hr class="dashed-hr uk-margin-bottom">
<?php session(['projectDetailsOutput' => 1])?>
@endIf
@endIf

@if(!is_null($inspections))
@if(isset($inspections_type) && $inspections_type == 'unit')
<?php
$totalUnits = count(collect($inspections)->groupBy('unit_id'));
?>
<div uk-grid class="uk-margin-bottom">

	<div class="uk-width-1-1 crr-blocks" style="page-break-inside: avoid;">
		<h2 id="units-summary-header">@if($totalUnits >1 || $totalUnits < 1) {{ $totalUnits }} Units @else 1 Unit @endIf @if($dpView) Selected: @else Audited: @endIf </h2> @if($auditor_access) <small> <span class="use-hand-cursor" onclick="dynamicModalLoad('projects/{{ $report->project->id }}/programs/0/summary/@if(isset($audit)){{ $audit->audit_id }}@else{{ $audit_id }} @endIf',0,0,3);"><i class="a-arrow-diagonal-both use-hand-cursor" uk-tooltip="pos:top-left;title:CLICK TO SWAP UNITS;"  title="" aria-expanded="false"></i> SWAP UNITS </span>  &nbsp;|  &nbsp;</small>
		@endif
		<style>
			#modal-size {
				height: 815px;
			}
			.uk-column-divider {
				column-rule: 1px solid #939598;
			}
			.on-folder {
				position: relative;
				left: -4px;
				top: -6.5px;
				font-size: 0.95rem;
				font-weight: bolder;
			}
			.on-phone {
				position: relative;
				left: -10px;
				top: -6px;
				font-size: 0.95rem;
				font-weight: bolder;
			}
			.no-findings {
				color:#45925e;
			}
			.has-findings {
				color:#d21b7c;
			}
			.has-home {
				font-weight: bolder;
			}
			.finding-number {
				font-size: 9px;
				background: #666;
				padding: 0px 4px 0px;
				border: 0px;
				min-width: 13px;
				max-height: 13px;
				line-height: 1.5;
			}
			.home-folder {

				font-weight: bolder;
			}
			.home-folder-small {
				position: relative;
				left: -12px;
				top: 0px;
				font-size: 0.48rem;
				font-weight: bolder;
			}
			.inspection-icons {
				float: right;
			}
			.inspection-data-row {
				min-height: 40px;
				clear: both;
				display: inline-block;
				width: 100%;
			}
			.unit-name {
				float: left;
				@if($dpView)
				max-width: 360px;
				@else
				max-width: 200px;
				@endIf
				margin-bottom: 8px;
			}
			.amenity-auditor .auditor-badge {
				height: 20px;
				width: 20px;
				font-size: 10px;
				text-align: center;
				border-radius: 50%;
				border: 1px solid #50b8ec;
				background-color: #ffffff;
				color: #50b8ec;
				font-weight: 400;
				line-height: 21px;
				margin: 3px 3px 3px 3px;
			}
			.auditor-badge-on-details {
				display: inline-table;
				margin-right: 5px;
			}
		</style>


		<?php
$siteVisited = [];
$fileVisited = [];
$nameOutput = [];
$loops = 0;

$currentUnit = 0;

$inspections = collect($inspections);
$inspections = $inspections->sortBy('unit_name');
//dd($inspections);
$fileInspections = count(collect($inspections)->where('is_site_visit', 0)->groupBy('unit_id'));
$siteInspections = count(collect($inspections)->where('is_site_visit', 1)->groupBy('unit_id'));
$homeSiteInspections = count(collect($inspections)->where('group', 'HOME')->where('is_site_visit', 1)->groupBy('unit_id'));
$homeFileInspections = count(collect($inspections)->whereIn('group', 'HOME')->where('is_site_visit', 0)->groupBy('unit_id'));

$ohtfSiteInspections = count(collect($inspections)->where('group', 'OHTF')->where('is_site_visit', 1)->groupBy('unit_id'));
$ohtfFileInspections = count(collect($inspections)->whereIn('group', 'OHTF')->where('is_site_visit', 0)->groupBy('unit_id'));

$nhtfSiteInspections = count(collect($inspections)->where('group', 'NHTF')->where('is_site_visit', 1)->groupBy('unit_id'));
$nhtfFileInspections = count(collect($inspections)->whereIn('group', 'NHTF')->where('is_site_visit', 0)->groupBy('unit_id'));
?>

		<small>
			@if($homeSiteInspections > 0) <i class="a-mobile"></i> :@if($homeSiteInspections > 1 || $homeSiteInspections < 1) {{ $homeSiteInspections }} HOME PHYSICAL INSPECTIONS @else 1 HOME PHYSICAL INSPECTION @endIf  @endIf @if($homeFileInspections > 0) &nbsp;|&nbsp; <i class="a-folder"></i> <i class="a-home-2 home-folder-small"></i>: @if($homeFileInspections > 1 || $homeFileInspections < 1) {{ $homeFileInspections }} HOME FILE INSPECTIONS @else 1 HOME FILE INSPECTION @endIf &nbsp;| &nbsp; @endIf

			@if($ohtfSiteInspections > 0) <i class="a-mobile"></i> :@if($ohtfSiteInspections > 1 || $ohtfSiteInspections < 1) {{ $ohtfSiteInspections }} OHTF PHYSICAL INSPECTIONS @else 1 OHTF PHYSICAL INSPECTION @endIf  @endIf @if($ohtfFileInspections > 0) &nbsp;|&nbsp; <i class="a-folder"></i> <i class="a-home-2 home-folder-small"></i>: @if($ohtfFileInspections > 1 || $ohtfFileInspections < 1) {{ $ohtfFileInspections }} OHTF FILE INSPECTIONS @else 1 OHTF FILE INSPECTION @endIf &nbsp;| &nbsp; @endIf

			@if($nhtfSiteInspections > 0) ;<i class="a-mobile"></i> :@if($nhtfSiteInspections > 1 || $nhtfSiteInspections < 1) {{ $nhtfSiteInspections }} NHTF PHYSICAL INSPECTIONS @else 1 NHTF PHYSICAL INSPECTION @endIf  @endIf @if($nhtfFileInspections > 0) &nbsp;|&nbsp; <i class="a-folder"></i> <i class="a-home-2 home-folder-small"></i>: @if($nhtfFileInspections > 1 || $nhtfFileInspections < 1) {{ $nhtfFileInspections }} NHTF FILE INSPECTIONS @else 1 NHTF FILE INSPECTION @endIf &nbsp;| &nbsp @endIf
			<i class="a-mobile"></i> : @if($siteInspections > 1 || $siteInspections < 1) {{ $siteInspections }} PHYSICAL INSPECTIONS @else {{ $siteInspections }} PHYSICAL INSPECTION @endIf &nbsp;|   &nbsp;<i class="a-folder"></i> :&nbsp; @if($fileInspections > 1 || $fileInspections < 1) {{ $fileInspections }} FILE INSPECTIONS @else {{ $fileInspections }} FILE INSPECTION @endIf




		</small>
		<hr class="dashed-hr uk-margin-bottom">

		<div id="unit-column-set" class="uk-column-1-3 uk-column-divider">
			@forEach($inspections as $i)
			<?php $noShow = 0;?>
			@if($currentUnit != $i->unit_id)
			<div id="unit-inspection-{{ $i->unit_id }}"  class="inspection-data-row">

				@if(!in_array($i->unit_id, $nameOutput))
				<?php
$currentUnit = $i->unit_id;
$thisUnitValues = collect($inspections)->where('unit_id', $i->unit_id)->sortByDesc('is_site_visit');
$thisUnitFileFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', 'file'));
//$thisUnitUnresolvedFileFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', 'file')->where('finding_type.auditor_last_approved_at', '=', null));
//$thisUnitSiteFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', '!=', 'file'));
//$thisUnitUnresolvedSiteFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', '!=', 'file')->where('finding_type.auditor_last_approved_at', '=', null));
$thisUnitResolvedFileFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', 'file')->where('auditor_approved_resolution', 1));
$thisUnitUnresolvedFileFindings = $thisUnitFileFindings - $thisUnitResolvedFileFindings;
$thisUnitSiteFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', '!=', 'file'));
//$thisUnitUnresolvedSiteFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', '!=', 'file')->where('finding_type.auditor_last_approved_at', '=', null));
$thisUnitResolvedSiteFindings = count(collect($findings)->where('unit_id', $i->unit_id)->where('finding_type.type', '!=', 'file')->where('auditor_approved_resolution', 1));
$thisUnitUnresolvedSiteFindings = $thisUnitSiteFindings - $thisUnitResolvedSiteFindings;
$isHome = count(collect($inspections)->where('unit_id', $i->unit_id)->where('is_file_audit', 1)->where('group', 'HOME'));
$isOhtf = count(collect($inspections)->where('unit_id', $i->unit_id)->where('is_file_audit', 1)->where('group', 'OHTF'));
$isNhtf = count(collect($inspections)->where('unit_id', $i->unit_id)->where('is_file_audit', 1)->where('group', 'NHTF'));

//if ($thisBuildingSiteFindings || $thisBuildingFileFindings) {
//	$hasFindings = 1;
//}

?>
				<div  class="unit-name"  >
					@if($print !== 1 && !$dpView)<a href="#findings-list" class="uk-link-mute" onClick="showOnlyFindingsFor('unit-{{ $i->unit_id }}-finding');"> @elseIf($dpView && $i->unit->household)<span onclick="dynamicModalLoad('household/{{ $i->unit_id }}')" class="use-hand-cursor" uk-tooltip title="VIEW HOUSEHOLD DETAILS"><i class="a-avatar-info"></i>
						@endIf {{ $i->building->building_name }} : {{ $i->unit_name }}<?php $nameOutput[] = $i->unit_id;?> :
					@if($print !== 1 && !$dpView)</a> @elseIf($dpView && $i->unit->household)</span>@endIf
					@if($dpView)
					@if($i->unit->bedroomCount())
					<br />Bedrooms: <strong>{{ $i->unit->bedroomCount() }}</strong>
					@endIf
					@if(!is_null($i->unit->building->address))
					<br /><small style="text-transform: uppercase;">{{ $i->unit->building->address->line_1 }} {{ $i->unit->building->address->line_2 }} <br />
						{{ $i->unit->building->address->city }}, {{ $i->unit->building->address->state }} {{ $i->unit->building->address->zip }}</small>
						@endIf


						@endIf
					</div>

					@endIf
					<div class="inspection-icons">
						@forEach($thisUnitValues as $g)
						<?php //dd($thisUnitValues, $g); ?>
						@if($g->is_site_visit == 1)
						@if(!in_array($g->unit_id, $siteVisited))


						<i class="a-mobile uk-text-large uk-margin-small-right @if($auditor_access) @if(!$print)use-hand-cursor @endif @endif" @if($auditor_access) @if(!$print) onclick="openFindings(this, {{ $report->audit->id }}, null, {{ $g->unit_id }}, null, null,'0');" @endif @endif></i> @if($thisUnitSiteFindings > 0) <span class="uk-badge finding-number on-phone @if($thisUnitUnresolvedSiteFindings > 0) attention @endIf" uk-tooltip title="{{ $thisUnitSiteFindings }} FINDINGS @if($thisUnitUnresolvedSiteFindings > 0) WITH {{ $thisUnitUnresolvedSiteFindings }} PENDING RESOLUTION @ELSE FULLY RESOLVED @endIf ">{{ $thisUnitSiteFindings }}</span> @else<i class="a-circle-checked on-phone no-findings"></i>@endIf <?php $siteVisited[] = $g->unit_id;?>
						@else
						<?php $noShow = 1;?>
						@endIf

						@elseIf(!in_array($g->unit_id, $fileVisited))
						@if(!in_array($g->unit_id, $siteVisited))
						<span style="color:#cecece"><i class="a-mobile uk-text-large uk-margin-small-right "  ></i> @if($thisUnitSiteFindings > 0) <span class="uk-badge finding-number on-phone" uk-tooltip title="{{ $thisUnitSiteFindings }}">{{ $thisUnitSiteFindings }}</span> @else<i class="a-circle-minus on-phone"></i> @endIf</span>
						@endIf
						<i class="a-folder uk-text-large @if($auditor_access)@if(!$print)use-hand-cursor @endif @endif" @if($auditor_access)@if(!$print) onclick="openFindings(this, {{ $report->audit->id }}, null, {{ $g->unit_id }}, 'file',null,'0');" @endif @endif></i> @if($thisUnitFileFindings > 0) <span class="uk-badge finding-number on-folder @if($thisUnitUnresolvedFileFindings > 0) attention @endIf" uk-tooltip title="{{ $thisUnitFileFindings }} FINDINGS @if($thisUnitUnresolvedFileFindings > 0) WITH {{ $thisUnitUnresolvedFileFindings }} PENDING RESOLUTION @ELSE FULLY RESOLVED @endIf ">{{ $thisUnitFileFindings }}</span> @else<i class="a-circle-checked on-folder no-findings"></i>@endIf
						@if($isHome || $isOhtf || $isNhtf) <i class="a-home-2 home-folder"></i> @endIf
						<?php $fileVisited[] = $g->unit_id;?>

						@else
						<?php $noShow = 1;?>
						@endIf

						@endForEach
						@if(!in_array($g->unit_id, $fileVisited))
						<span style="color:#cecece"><i class="a-folder uk-text-large"></i> <i class="a-circle-minus on-folder"></i></span>

						@endIf

					</div>
					<hr class="dashed-hr uk-margin-small-bottom">
				</div>
				@endIf
				@endForEach
			</div>
		</div>
	</div>
	<hr class="dashed-hr uk-margin-large-bottom">
	@endif

	@if(isset($inspections_type) && $inspections_type == 'site')
	<div uk-grid class="uk-margin-bottom">
		<div class="uk-width-1-1 crr-blocks" style="page-break-inside: avoid;">
			<?php
$inspections = collect($inspections);
?>
			<h2>{{ count($inspections) }} @if(count($inspections) > 1 || count($inspections) < 1) Site Amenities @else Site Amenity @endIf @if($dpView) Selected: @else Audited: @endIf </h2><small><i class="a-mobile"></i> : PHYSICAL INSPECTION </small>
			<hr class="dashed-hr uk-margin-bottom">

			<div class="uk-column-1-3 uk-column-divider">
				@forEach($inspections as $i)
				<?php
$currentSite = $i->amenity_id;
$thisAmenityValues = collect($inspections)->where('amenity_id', $i->amenity_id);
$thisAmenityFindings = count(collect($findings)->where('amenity_id', $i->amenity_id));
$thisAmenityUnresolvedFindings = count(collect($findings)->where('amenity_id', $i->amenity_id)->where('finding_type.auditor_last_approved_at', '=', null));
?>
				<div class="inspection-data-row">
					<div  class="unit-name" >
						@if($print !== 1)<a href="#findings-list" class="uk-link-mute" onClick="showOnlyFindingsFor('site-{{ $i->amenity->amenity_type_key }}-finding');">
							@endIf <strong><i class="{{ $i->amenity->icon }}"></i></strong> {{ $i->amenity->amenity_description }}
						@if($print !== 1)</a>@endIf
					</div>
				{{-- <div style="float: right;">
					<i class="a-mobile uk-text-large uk-margin-small-right @can('access_auditor')@if(!$print)use-hand-cursor @endif @endcan" @can('access_auditor')@if(!$print) onclick="openFindings(this, {{ $report->audit->id }}, null, null, null, {{ $i->amenity_id }},'0');" @endif  @endcan></i>
				</div> --}}
				<div style="float: right;">
					<i class="a-mobile uk-text-large uk-margin-small-right @if($auditor_access)@if(!$print)use-hand-cursor @endif @endif" @if($auditor_access)@if(!$print) onclick="openFindings(this, {{ $report->audit->id }}, null, null, null, {{ $i->amenity_id }},'0');" @endif  @endif></i> @if($thisAmenityFindings > 0) <span class="uk-badge finding-number on-phone @if($thisAmenityUnresolvedFindings > 0) attention @endIf" uk-tooltip title="{{ $thisAmenityFindings }} @if($thisAmenityFindings > 1) FINDINGS @else FINDING @endIf @if($thisAmenityUnresolvedFindings > 0) WITH {{ $thisAmenityUnresolvedFindings }} PENDING RESOLUTION @else FULLY RESOLVED @endIf">{{ $thisAmenityFindings }}</span> @else<i class="a-circle-checked on-phone no-findings"></i>@endif


				</div>
				<hr class="dashed-hr uk-margin-small-bottom">
			</div>
			@endForEach
		</div>
	</div>
</div>
<hr class="dashed-hr uk-margin-large-bottom">

@endif

@if(isset($inspections_type) && $inspections_type == 'building')
<div uk-grid class="uk-margin-bottom">
	<div class="uk-width-1-1 crr-blocks" style="page-break-inside: avoid;">
		<?php //dd($i);

$inspections = collect($inspections);

?>
		<h2>{{ count($inspections) }} @if(count($inspections) > 1 || count($inspections) < 1) Buildings @else Building @endIf @if($dpView) Selected: @else Audited: @endIf </h2><small><i class="a-mobile"></i> : PHYSICAL INSPECTION </small>
		<hr class="dashed-hr uk-margin-bottom">

		<div class="uk-column-1-3 uk-column-divider">
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
$thisBuildingSiteFindings = count($findingCount->where('finding_type.type', '!=', 'file'));
$thisBuildingResolvedSiteFindings = count($findingCount->where('finding_type.type', '!=', 'file')->where('auditor_approved_resolution', 1));
$thisBuildingUnresolvedSiteFindings = $thisBuildingSiteFindings - $thisBuildingResolvedSiteFindings;
$thisBuildingFileFindings = count($findingCount->where('finding_type.type', '==', 'file'));
$thisBuildingResolvedFileFindings = count($findingCount->where('finding_type.type', '==', 'file')->where('auditor_approved_resolution', 1));
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
		</div>
	</div>
</div>
<hr class="dashed-hr uk-margin-large-bottom">

@endif
@else
<hr class="dashed-hr">
<h3>NO {{ strtoupper($inspections_type) }} INSPECTIONS COMPLETED YET</h3>
<hr class="dashed-hr uk-margin-large-bottom">
@endIf