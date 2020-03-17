
<?php
if (isset($detailsPage)) {
	$dpView = 1;
}
?>
@if(!is_null($inspections))

<?php
	// $totalUnits = number_format($inspections->total(), 0);
	$totalUnits = count(collect($allUnitInspections)->groupBy('unit_id'));
?>
<div uk-grid class="uk-margin-bottom pm-unit">
	
	<div class="uk-width-1-1 crr-blocks" style="page-break-inside: avoid;">
		<div id="containerIntro" style="display: flex;">
			<h2 id="units-summary-header">@if($totalUnits >1 || $totalUnits < 1) {{ $totalUnits }} Units @else 1 Unit @endIf @if($dpView) Selected: @else Audited: @endIf </h2> 
			<div class="uk-width-1-2" style="padding-left: 10px;"> 
		    	{{ $inspections->links() }}
		    </div>
	    </div>
		@if($auditor_access) <small> <span class="use-hand-cursor" onclick="dynamicModalLoad('projects/{{ $report->project->id }}/programs/0/summary/@if(isset($audit)){{ $audit->audit_id }}@else{{ $audit_id }} @endIf',0,0,3);"><i class="a-arrow-diagonal-both use-hand-cursor" uk-tooltip="pos:top-left;title:CLICK TO SWAP UNITS;"  title="" aria-expanded="false"></i> SWAP UNITS </span>  &nbsp;|  &nbsp;</small>
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
		// $siteVisited = [];
		// $fileVisited = [];
		// $nameOutput = [];
		// $loops = 0;

		// $currentUnit = 0;

		// $inspections = collect($inspections);
		$inspections = $inspections->sortBy('unit_name');
		$allUnitInspections = $allUnitInspections->sortBy('unit_name');
		//dd($inspections);
		$fileInspections = count(collect($allUnitInspections)->where('is_site_visit', 0)->groupBy('unit_id'));
		$siteInspections = count(collect($allUnitInspections)->where('is_site_visit', 1)->groupBy('unit_id'));
		$homeSiteInspections = count(collect($allUnitInspections)->where('group', 'HOME')->where('is_site_visit', 1)->groupBy('unit_id'));
		$homeFileInspections = count(collect($allUnitInspections)->whereIn('group', 'HOME')->where('is_site_visit', 0)->groupBy('unit_id'));

		$ohtfSiteInspections = count(collect($allUnitInspections)->where('group', 'OHTF')->where('is_site_visit', 1)->groupBy('unit_id'));
		$ohtfFileInspections = count(collect($allUnitInspections)->whereIn('group', 'OHTF')->where('is_site_visit', 0)->groupBy('unit_id'));

		$nhtfSiteInspections = count(collect($allUnitInspections)->where('group', 'NHTF')->where('is_site_visit', 1)->groupBy('unit_id'));
		$nhtfFileInspections = count(collect($allUnitInspections)->whereIn('group', 'NHTF')->where('is_site_visit', 0)->groupBy('unit_id'));
		?>

		<small>
			@if($homeSiteInspections > 0) <i class="a-mobile"></i> :@if($homeSiteInspections > 1 || $homeSiteInspections < 1) {{ $homeSiteInspections }} HOME PHYSICAL INSPECTIONS @else 1 HOME PHYSICAL INSPECTION @endIf  @endIf @if($homeFileInspections > 0) &nbsp;|&nbsp; <i class="a-folder"></i> <i class="a-home-2 home-folder-small"></i>: @if($homeFileInspections > 1 || $homeFileInspections < 1) {{ $homeFileInspections }} HOME FILE INSPECTIONS @else 1 HOME FILE INSPECTION @endIf &nbsp;| &nbsp; @endIf

			@if($ohtfSiteInspections > 0) <i class="a-mobile"></i> :@if($ohtfSiteInspections > 1 || $ohtfSiteInspections < 1) {{ $ohtfSiteInspections }} OHTF PHYSICAL INSPECTIONS @else 1 OHTF PHYSICAL INSPECTION @endIf  @endIf @if($ohtfFileInspections > 0) &nbsp;|&nbsp; <i class="a-folder"></i> <i class="a-home-2 home-folder-small"></i>: @if($ohtfFileInspections > 1 || $ohtfFileInspections < 1) {{ $ohtfFileInspections }} OHTF FILE INSPECTIONS @else 1 OHTF FILE INSPECTION @endIf &nbsp;| &nbsp; @endIf

			@if($nhtfSiteInspections > 0) ;<i class="a-mobile"></i> :@if($nhtfSiteInspections > 1 || $nhtfSiteInspections < 1) {{ $nhtfSiteInspections }} NHTF PHYSICAL INSPECTIONS @else 1 NHTF PHYSICAL INSPECTION @endIf  @endIf @if($nhtfFileInspections > 0) &nbsp;|&nbsp; <i class="a-folder"></i> <i class="a-home-2 home-folder-small"></i>: @if($nhtfFileInspections > 1 || $nhtfFileInspections < 1) {{ $nhtfFileInspections }} NHTF FILE INSPECTIONS @else 1 NHTF FILE INSPECTION @endIf &nbsp;| &nbsp @endIf
			<i class="a-mobile"></i> : @if($siteInspections > 1 || $siteInspections < 1) {{ $siteInspections }} PHYSICAL INSPECTIONS @else {{ $siteInspections }} PHYSICAL INSPECTION @endIf &nbsp;|   &nbsp;<i class="a-folder"></i> :&nbsp; @if($fileInspections > 1 || $fileInspections < 1) {{ $fileInspections }} FILE INSPECTIONS @else {{ $fileInspections }} FILE INSPECTION @endIf
		</small>
		<hr class="dashed-hr uk-margin-bottom">

		<div id="unit-column-set" class="uk-column-1-3 uk-column-divider">
			@include('crr_parts.pm_crr_inspections_unit_detail')
		</div>
	</div>
</div>
<hr class="dashed-hr uk-margin-large-bottom">
	
@else
	<hr class="dashed-hr">
	<h3>NO {{ strtoupper($inspections_type) }} INSPECTIONS COMPLETED YET</h3>
	<hr class="dashed-hr uk-margin-large-bottom">
@endIf