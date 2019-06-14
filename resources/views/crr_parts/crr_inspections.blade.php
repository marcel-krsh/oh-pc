<?php

	$inspections = $bladeData;
	$projectDetails = null;
	$findings = null;
	if(array_key_exists(4, $pieceData)){
	  $projectDetails = $pieceData[4];
	}
	if(array_key_exists(5, $pieceData)){
	  $findings = $pieceData[5];
	}



?>
@if(null !== $projectDetails)
	@if(session('projectDetailsOutput') == 0)
		<div id="project-details-stats" class="uk-width-1-1 uk-grid-margin uk-first-column" style="margin-top:20px;">
			<div uk-grid="" class="uk-grid">
				<div class="uk-width-1-1">
					<h2>Project Details: </h2>
				</div>
				<div class="uk-width-2-3 uk-first-column">
					<ul class="leaders" style="margin-right:30px;">
						<li><span>Total Buildings</span> <span>{{$projectDetails->total_building}}</span></li>
						<li><span>Total Units</span> <span>{{$projectDetails->total_units}}</span></li>
						<li><span class="indented">• Market Rate Units</span> <span>{{$projectDetails->market_rate}}</span></li>
						<li><span class="indented">• Program Units</span> <span>{{$projectDetails->subsidized}}</span></li>
						<?php $pdPrograms = json_decode($projectDetails->programs); ?>
						<li><span>Programs</span> <span>{{count($pdPrograms)}}</span></li>
											<?php $pdpLoop = 1; ?>
											@forEach($pdPrograms as $pdp)
												<li><span class="indented">• [{{$pdpLoop}}] {{$pdp->name}}</span> <span>{{$pdp->units}}</span></li>
												<?php $programReference[$pdp->program_id] = $pdpLoop; $pdpLoop ++; ?>
											@endForEach
					</ul>
				</div>
				<div class="uk-width-1-3">
					<h5 class="uk-margin-remove"><strong>OWNER: </strong></h5>
					<div class="address" style="margin-bottom:20px;">
						<i class="a-building" style="font-weight: bolder;"></i> @if($projectDetails->owner_name != '') {{$projectDetails->owner_name}} @else NA @endIf<br>
						<i class="a-avatar"></i> POC: @if($projectDetails->owner_poc != ''){{$projectDetails->owner_poc}}@else NA @endIf<br>
						<i class="a-phone-5"></i>  @if($projectDetails->owner_phone != ''){{$projectDetails->owner_phone}}@else NA @endIf<br>
						<i class="a-fax-2"></i>  @if($projectDetails->owner_fax != ''){{$projectDetails->owner_fax}}@else NA @endIf<br>
						<i class="a-mail-send"></i> @if($projectDetails->owner_email != '')<a class="uk-link-mute" href="mailto:{{$projectDetails->owner_email}}">{{$projectDetails->owner_email}}</a>@else NA @endIf<br>
										</div>
					<h5 class="uk-margin-remove"><strong>Managed By: </strong></h5>
					<div class="address">
						<i class="a-building" style="font-weight: bolder;"></i> @if($projectDetails->manager_name != '') {{$projectDetails->manager_name}} @else NA @endIf<br>
						<i class="a-avatar"></i> POC: @if($projectDetails->manager_poc != ''){{$projectDetails->manager_poc}}@else NA @endIf<br>
						<i class="a-phone-5"></i>  @if($projectDetails->manager_phone != ''){{$projectDetails->manager_phone}}@else NA @endIf<br>
						<i class="a-fax-2"></i>  @if($projectDetails->manager_fax != ''){{$projectDetails->manager_fax}}@else NA @endIf<br>
						<i class="a-mail-send"></i> @if($projectDetails->manager_email != '')<a class="uk-link-mute" href="mailto:{{$projectDetails->manager_email}}">{{$projectDetails->manager_email}}</a>@else NA @endIf<br>
										</div>
				</div>
			</div>
		</div>
		<hr class="dashed-hr uk-margin-bottom">
		<?php session(['projectDetailsOutput'=>1]) ?>
	@endIf
@endIf

@if(!is_null($inspections))
@if(isset($inspections_type) && $inspections_type == 'unit')
<?php
	$totalUnits = count(collect($inspections)->groupBy('unit_id'));
?>
<div uk-grid class="uk-margin-bottom">
	<div class="uk-width-1-1 crr-blocks" style="page-break-inside: avoid;">
		<h2>@if($totalUnits >1 || $totalUnits < 1) {{$totalUnits}} Units @else 1 Unit @endIf Inspected: </h2> @can('access_auditor') <small> <span class="use-hand-cursor" onclick="dynamicModalLoad('projects/{{$report->project->id}}/programs/0/summary',0,0,3);"><i class="a-arrow-diagonal-both use-hand-cursor" uk-tooltip="pos:top-left;title:CLICK TO SWAP UNITS;"  title="" aria-expanded="false"></i> SWAP UNITS </span>  &nbsp;|  &nbsp;</small>
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
					    top: -8px;
					    font-size: 0.88rem;
					    font-weight: bolder;
    			}
    			.on-phone {
    				position: relative;
				    left: -10px;
				    top: -8px;
				    font-size: 0.88rem;
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
			</style>
		@endCan


		<?php

			$siteVisited = array();
			$fileVisited = array();
			$nameOutput = array();
			$loops = 0;
			if(is_array($inspections) && count($inspections)> 0){
				$currentUnit = 0;
			}
			$inspections = collect($inspections);
			$inspections =$inspections->sortBy('unit_name');
			//dd($inspections);


			$fileInspections = count(collect($inspections)->where('is_site_visit',0)->groupBy('unit_id'));
			$siteInspections = count(collect($inspections)->where('is_site_visit',1)->groupBy('unit_id'));
			$homeSiteInspections = count(collect($inspections)->whereIn('group','HOME')->where('is_site_visit',1)->groupBy('unit_id'));
			$homeFileInspections = count(collect($inspections)->whereIn('group','HOME')->where('is_site_visit',0)->groupBy('unit_id'));


		?>

		<small><i class="a-mobile"></i> : @if($siteInspections > 1 || $siteInspections < 1) {{$siteInspections}} SITE INSPECTIONS @else {{$siteInspections}} SITE INSPECTION @endIf @if($homeSiteInspections > 0) {{$homeSiteInspections}} @endIf &nbsp;|   &nbsp;<i class="a-folder"></i> :   &nbsp; @if($fileInspections > 1 || $fileInspections < 1) {{$fileInspections}} FILE INSPECTIONS @else {{$fileInspections}} FILE INSPECTION @endIf @if($homeSiteInspections > 0) &nbsp;| &nbsp; @if($homeSiteInspections > 1 || $homeSiteInspections < 1) {{$homeSiteInspections}} HOME SITE INSPECTIONS @else 1 HOME SITE INSPECTION @endIf  @endIf @if($homeFileInspections > 0) &nbsp;|&nbsp; @if($homeFileInspections > 1 || $homeFileInspections < 1) {{$homeFileInspections}} HOME FILE INSPECTIONS @else 1 HOME FILE INSPECTION @endIf @endIf


		</small>
		<hr class="dashed-hr uk-margin-bottom">

		<div class="uk-column-1-3 uk-column-divider">
			@forEach($inspections as $i)
			<?php $noShow = 0 ; ?>
			@if($currentUnit != $i->unit_id)
			<div>

				@if(!in_array($i->unit_id, $nameOutput))
				<?php
				$currentUnit = $i->unit_id;
				$thisUnitValues = collect($inspections)->where('unit_id',$i->unit_id)->sortByDesc('is_site_visit');
				$thisUnitFileFindings = count(collect($findings)->where('unit_id',$i->unit_id)->where('finding_type.type','file'));
				$thisUnitSiteFindings = count(collect($findings)->where('unit_id',$i->unit_id)->where('finding_type.type','!=','file'));
				?>
				<div  style="float: left;"  >

					@if($print !== 1)<a href="#findings-list" class="uk-link-mute" onClick="showOnlyFindingsFor('unit-{{$i->unit_id}}-finding');">

					@endIf {{ $i->building->building_name }} : {{ $i->unit_name }}<?php $nameOutput[] =$i->unit_id; ?> :
				@if($print !== 1)</a>@endIf
				</div>

				@endIf
				<div style="float: right;">
					@forEach($thisUnitValues as $g)
					<?php //dd($thisUnitValues, $g); ?>
					@if($g->is_site_visit == 1)
						@if(!in_array($g->unit_id, $siteVisited))


						<i class="a-mobile uk-text-large uk-margin-small-right @can('access_auditor') @if(!$print)use-hand-cursor @endif @endcan" @can('access_auditor') @if(!$print) onclick="openFindings(this, {{ $report->audit->id }}, null, {{ $g->unit_id }}, null, null,'0');" @endif @endcan></i> @if($thisUnitSiteFindings > 0) <span class="uk-badge finding-number on-phone">{{$thisUnitSiteFindings}}</span> @else<i class="a-circle-checked on-phone no-findings"></i>@endIf <?php $siteVisited[] =$g->unit_id;  ?>
						@else
						<?php $noShow = 1; ?>
						@endIf

					@elseIf(!in_array($g->unit_id, $fileVisited))
						@if(!in_array($g->unit_id, $siteVisited))
						<i class="a-mobile uk-text-large uk-margin-small-right @can('access_auditor')@if(!$print)use-hand-cursor @endif @endcan" style="color:rgba(0,0,0,0);" @can('access_auditor')@if(!$print) onclick="openFindings(this, {{ $report->audit->id }}, null, {{ $g->unit_id }}, null, null,'0');" @endif  @endcan></i>
						@endIf
						<i class="a-folder uk-text-large @can('access_auditor')@if(!$print)use-hand-cursor @endif @endcan" @can('access_auditor')@if(!$print) onclick="openFindings(this, {{ $report->audit->id }}, null, {{ $g->unit_id }}, 'file',null,'0');" @endif @endcan></i> @if($thisUnitFileFindings > 0) <span class="uk-badge finding-number on-folder">{{$thisUnitFileFindings}}</span> @else<i class="a-circle-checked on-folder no-findings"></i>@endIf<?php $fileVisited[]=$g->unit_id; ?>

					@else
					<?php $noShow = 1; ?>
					@endIf
					
					@endForEach
					@if(!in_array($g->unit_id, $fileVisited))
						<span style="opacity: 0"><i class="a-folder uk-text-large"></i> <i class="a-circle-checked on-folder no-findings"></i></span>
						
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
		<h2>Site Amenities Inspected: </h2><small><i class="a-mobile"></i> : SITE INSPECTION </small>
		<hr class="dashed-hr uk-margin-bottom">
		<?php
		$inspections = collect($inspections);
		?>
		<div class="uk-column-1-3 uk-column-divider">
			@forEach($inspections as $i)
			<div>
				<div style="float: left;">
					<strong><i class="{{ $i->amenity->icon }}"></i></strong> {{ $i->amenity->amenity_description }}
				</div>
				<div style="float: right;">
					<i class="a-mobile uk-text-large uk-margin-small-right @can('access_auditor')@if(!$print)use-hand-cursor @endif @endcan" @can('access_auditor')@if(!$print) onclick="openFindings(this, {{ $report->audit->id }}, null, null, null, {{ $i->amenity_id }},'0');" @endif  @endcan></i>
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
		<h2>Buildings Inspected: </h2><small><i class="a-mobile"></i> : SITE INSPECTION </small>
		<hr class="dashed-hr uk-margin-bottom">
		<?php //dd($i);
		$inspections = collect($inspections);
		?>
		<div class="uk-column-1-3 uk-column-divider">
			@forEach($inspections as $i)
			<div>
				<div style="float: left;">
					{{ $i->building_name }}
				</div>
				<div style="float: right;">
					<i class="a-mobile uk-text-large uk-margin-small-right @can('access_auditor')@if(!$print)use-hand-cursor @endif @endcan"  @can('access_auditor')@if(!$print) onclick="openFindings(this, {{ $report->audit->id }}, {{ $i->building_id }}, null, null, null,'0');" @endif  @endcan></i>
				</div>
			</div>
				<hr class="dashed-hr uk-margin-small-bottom">
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