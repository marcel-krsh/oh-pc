<?php $inspections = $bladeData ?>
@if(!is_null($inspections))
<div uk-grid>
	<div class="uk-width-1-1 crr-blocks" style="page-break-inside: avoid;">
		<h2>Units Inspected: </h2><small><i class="a-folder"></i> : FILE INSPECTION &nbsp;|  &nbsp;<i class="a-mobile"></i> : SITE INSPECTION </small><hr class="dashed-hr">
		<?php //dd($i); 
			$siteVisited = array();
			$fileVisited = array();
		?>
		<p>@forEach($inspections as $i)
		
		<?php $noShow = 0 ; ?>	
		@if($i->is_site_visit) @if(!in_array($i->unit_id, $siteVisited))<span style="display: inline-block;"> | <i class="a-mobile"></i> <?php $siteVisited[] =$i->unit_id; ?> @else <?php $noShow = 1; ?> @endIf @elseIf(!in_array($i->unit_id, $fileVisited))<span style="display: inline-block;"> | <i class="a-folder"></i> <?php $fileVisited[]=$i->unit_id; ?> @else <?php $noShow = 1; ?> @endIf @if($noShow !== 1){{$i->building->building_name}} : {{$i->unit_name}}</span>@endIf

		
		@endForEach
		|
		<?php //dd($siteVisited,$fileVisited); ?>
		</p>
	</div>
</div>
@else
<hr class="dashed-hr">
<h3>NO INSPECTIONS COMPLETED YET</h3>
@endIf