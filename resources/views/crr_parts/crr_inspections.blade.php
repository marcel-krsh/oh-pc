<?php $inspections = $bladeData ?>
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
    				left:-5px;
    				top:5px;
    				font-size: .5rem;
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
		<small><i class="a-mobile"></i> : @if($siteInspections > 1 || $siteInspections < 1) {{$siteInspections}} SITE INSPECTIONS @else {{$siteInspections}} SITE INSPECTION @endIf @if($homeSiteInspections > 0) {{$homeSiteInspections}} @endIf &nbsp;|   &nbsp;<i class="a-folder"></i> :   &nbsp; @if($fileInspections > 1 || $fileInspections < 1) {{$fileInspections}} FILE INSPECTIONS @else {{$fileInspections}} FILE INSPECTION @endIf &nbsp;| @if($homeSiteInspections > 0) &nbsp;| HOME SITE {{$homeSiteInspections}} @endIf  @if($homeFileInspections > 0) &nbsp;| HOME FILE {{$homeFileInspections}} @endIf 

			

		</small>
		<hr class="dashed-hr uk-margin-bottom">
		
		<div class="uk-column-1-3 uk-column-divider">
			@forEach($inspections as $i)
			<?php $noShow = 0 ; ?>
			@if($currentUnit != $i->unit_id)
			<div>
				<?php
				$currentUnit = $i->unit_id;
				$thisUnitValues = collect($inspections)->where('unit_id',$i->unit_id)->sortByDesc('is_site_visit');
				?>
				@if(!in_array($i->unit_id, $nameOutput))
				<div style="float: left;">
					{{ $i->building->building_name }} : {{ $i->unit_name }}<?php $nameOutput[] =$i->unit_id; ?> :
				</div>

				@endIf
				<div style="float: right;">
					@forEach($thisUnitValues as $g)
					<?php //dd($thisUnitValues, $g); ?>
					@if($g->is_site_visit == 1)
						@if(!in_array($g->unit_id, $siteVisited))
						{-- a-mobile-checked for no findings -- a-mobile-not for findings  add a a-circle-cross or a-circle-checked onto regular icons to show good or no? --}
						<i class="a-mobile uk-text-large uk-margin-small-right @can('access_auditor') @if(!$print)use-hand-cursor @endif @endcan" @can('access_auditor') @if(!$print) onclick="openFindings(this, {{ $report->audit->id }}, null, {{ $g->unit_id }}, null, null,'0');" @endif @endcan></i> <?php $siteVisited[] =$g->unit_id;  ?>
						@else
						<?php $noShow = 1; ?>
						@endIf

					@elseIf(!in_array($g->unit_id, $fileVisited))
						@if(!in_array($g->unit_id, $siteVisited))
						<i class="a-mobile uk-text-large uk-margin-small-right @can('access_auditor')@if(!$print)use-hand-cursor @endif @endcan" style="color:rgba(0,0,0,0);" @can('access_auditor')@if(!$print) onclick="openFindings(this, {{ $report->audit->id }}, null, {{ $g->unit_id }}, null, null,'0');" @endif  @endcan></i>
						@endIf
						<i class="a-folder uk-text-large @can('access_auditor')@if(!$print)use-hand-cursor @endif @endcan" @can('access_auditor')@if(!$print) onclick="openFindings(this, {{ $report->audit->id }}, null, {{ $g->unit_id }}, 'file',null,'0');" @endif @endcan></i> <i class="a-circle-checked onfolder"></i><?php $fileVisited[]=$g->unit_id; ?>

					@else
					<?php $noShow = 1; ?>
					@endIf

					@endForEach
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