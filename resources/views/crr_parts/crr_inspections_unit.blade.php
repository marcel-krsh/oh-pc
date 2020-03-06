
<?php
if (isset($detailsPage)) {
	$dpView = 1;
}
?>
@if(!is_null($inspections))

<?php
	$totalUnits = number_format($inspections->total(), 0);
?>
<div uk-grid class="uk-margin-bottom unit">
	<div class="uk-width-1-2 " style="padding-top:4px;"> 
    	{{ $inspections->links() }}
    </div>
	<div class="uk-width-1-1 crr-blocks" id="unit" style="page-break-inside: avoid;">
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

		// $inspections = collect($inspections);
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
							// $thisUnitValues = collect($inspections)->where('unit_id', $i->unit_id)->sortByDesc('is_site_visit');
							$thisUnitValues = $inspections->where('unit_id', $i->unit_id)->sortByDesc('is_site_visit');
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
	
@else
	<hr class="dashed-hr">
	<h3>NO {{ strtoupper($inspections_type) }} INSPECTIONS COMPLETED YET</h3>
	<hr class="dashed-hr uk-margin-large-bottom">
@endIf