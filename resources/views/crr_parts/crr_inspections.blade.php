<?php $inspections = $bladeData ?>
@if(!is_null($inspections))
<div uk-grid>
	<div class="uk-width-1-1 crr-blocks" style="page-break-inside: avoid;">
		<h2>Units Inspected: </h2><small><i class="a-folder"></i> : FILE INSPECTION &nbsp;|  &nbsp;<i class="a-mobile"></i> : SITE INSPECTION </small><hr class="dashed-hr uk-margin-bottom">
		<?php //dd($i); 
			$siteVisited = array();
			$fileVisited = array();
			$nameOutput = array();
			if(is_array($inspections) && count($inspections)> 0){
				$currentUnit = $inspections[0]->unit_id;
			}
		?>
		<div class="uk-column-1-3 uk-column-divider"><p>@forEach($inspections as $i) <?php $noShow = 0 ; ?>@if($currentUnit != $i->unit_id)
		   <br />
		   <?php $currentUnit = $i->unit_id; ?>
		@endIf
		@if(!in_array($i->unit_id, $nameOutput))
			{{$i->building->building_name}} : {{$i->unit_name}}<?php $nameOutput[] =$i->unit_id; ?> : 

		@endIf	
		@if($i->is_site_visit) 
			@if(!in_array($i->unit_id, $siteVisited))
				 <i class="a-mobile"></i> <?php $siteVisited[] =$i->unit_id; ?> 
			@else 
				<?php $noShow = 1; ?> 
			@endIf 
		@elseIf(!in_array($i->unit_id, $fileVisited))
			@if(!in_array($i->unit_id, $siteVisited))
				 <i class="a-mobile" style="color:rgba(0,0,0,0);"></i> 
			@endIf
			 <i class="a-folder"></i> <?php $fileVisited[]=$i->unit_id; ?> 
		@else 
			<?php $noShow = 1; ?> 
		@endIf 



		
		@endForEach
		</li></ul>
		<?php //dd($siteVisited,$fileVisited); ?>
		</p>
		</div>
	</div>
</div>
@else
<hr class="dashed-hr">
<h3>NO INSPECTIONS COMPLETED YET</h3>
@endIf