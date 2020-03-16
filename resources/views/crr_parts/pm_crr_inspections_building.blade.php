<?php
if (isset($detailsPage)) {
	$dpView = 1;
	// print_r(session()->get('type_id'));
	// print_r(session()->get('is_uncorrected'));
}
?>
@if(!is_null($inspections))
	<div uk-grid class="uk-margin-bottom building">

		
		<div class="uk-width-1-1 crr-blocks" style="page-break-inside: avoid;">
			<div id="containerIntro" style="display: flex;">
				<h2 class="uk-width-1-1@s uk-width-1-5@m">{{number_format($inspections->total(), 0)}} @if(count($inspections) > 1 || count($inspections) < 1) Buildings @else Building @endIf @if($dpView) Selected: @else Audited: @endIf </h2> 
				<div class="uk-width-1-2" style="padding-left: 10px;"> 
			    	{{ $inspections->links() }}
			    </div>
			    <div class="uk-width-1-1@s uk-width-1-5@m">
			    	<select onchange="getSingleBuilding(this);" class="uk-select filter-drops uk-width-1-1" multiple="multiple" id="building_dropdown">
			    		<!-- <option value="all" selected="">
							FILTER BY BUILDING
						</option> -->
			    		@forEach($allBuildingInspections as $i)
			    			<?php
			    				$selected = "";
			    				if(session()->has('type_id')){
			    				   $selected=in_array($i->building_id, session()->get('type_id'))? "selected" : ""; 	
			    				}
			    			?>
			    			<option <?php echo $selected;?> value="<?php echo $i->building_id; ?>"><?php echo $i->building_name; ?></option>
			    		@endforeach
			    	</select>
				</div>
			 	<div class="uk-width-1-1@s uk-width-1-5@m" style="padding-left: 10px;">
					<label class="switch">
					  	<input type="checkbox" onchange="getUnCorrectedBuilding(this);" id="uncorrected_checkbox" {{ (session()->has('is_uncorrected') && session()->get('is_uncorrected') == 'true') ? 'checked' : ''  }} >
					  	<span class="slider round"></span> 
					</label> <span class="attention" style="display: inline-block;margin-top: 5px;position: absolute;margin-left: 5px;"> PENDING RESOLUTIONS </span>
				</div>
				<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
			</div>
		    <small><i class="a-mobile"></i> : PHYSICAL INSPECTION </small>

			<hr class="dashed-hr uk-margin-bottom">

			<div class="uk-column-1-3 uk-column-divider" id="building-detail" >
				@include('crr_parts.pm_crr_inspections_building_detail')
			</div>
		</div>
	</div>
	<hr class="dashed-hr uk-margin-large-bottom">
			
@else
	<hr class="dashed-hr">
	<h3>NO {{ strtoupper($inspections_type) }} INSPECTIONS COMPLETED YET</h3>
	<hr class="dashed-hr uk-margin-large-bottom">
@endIf
		
<script>
	
</script>