<?php $inspections = $bladeData ?>

@if(!is_null($inspections))
<div uk-grid class="uk-margin-bottom">
	<div class="uk-width-1-1 crr-blocks" style="page-break-inside: avoid;">
		<h2>Units Inspected: </h2><small><i class="a-folder"></i> : FILE INSPECTION &nbsp;|  &nbsp;<i class="a-mobile"></i> : SITE INSPECTION </small><hr class="dashed-hr uk-margin-bottom">
		<?php //dd($i); 
			$siteVisited = array();
			$fileVisited = array();
			$nameOutput = array();
			$loops = 0;
			if(is_array($inspections) && count($inspections)> 0){
				$currentUnit = $inspections[0]->unit_id;
			}
			$inspections = collect($inspections);
			//$inspections =$inspections->sortByDesc('unit_id');
		?>
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
								{{$i->building->building_name}} : {{$i->unit_name}}<?php $nameOutput[] =$i->unit_id; ?> : 
							</div>

					@endIf
							<div style="float: right;">
					@forEach($thisUnitValues as $g)
						<?php //dd($thisUnitValues, $g); ?>
						@if($g->is_site_visit == 1) 
							@if(!in_array($g->unit_id, $siteVisited))
								 <i class="a-mobile"></i> <?php $siteVisited[] =$g->unit_id;  ?> 
							@else 
								<?php $noShow = 1; ?> 
							@endIf 
							
						@elseIf(!in_array($g->unit_id, $fileVisited))
							@if(!in_array($g->unit_id, $siteVisited))
								 <i class="a-mobile" style="color:rgba(0,0,0,0);"></i> 
							@endIf
							 <i class="a-folder"></i> <?php $fileVisited[]=$g->unit_id; ?> 
							 
					@else 
						<?php $noShow = 1; ?> 
					@endIf 

					@endForEach
						</div>
						<hr class="dashed-hr uk-margin-small-bottom">
					</div>
					
				@endIf
			
			@endForEach
			


		</li></ul>
		<?php //dd($siteVisited,$fileVisited); ?>
		</p>
		</div>
	</div>
</div>
<hr class="dashed-hr uk-margin-large-bottom">
@else
<hr class="dashed-hr">
<h3>NO INSPECTIONS COMPLETED YET</h3>
<hr class="dashed-hr uk-margin-large-bottom">
@endIf