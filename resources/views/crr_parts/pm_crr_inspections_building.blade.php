<?php
if (isset($detailsPage)) {
	$dpView = 1;
	// print_r(session()->get('type_id'));
	// print_r(session()->get('is_uncorrected'));
}
?>
@if(!is_null($inspections))
	<div uk-grid class="uk-margin-bottom pm-building">

		
		<div class="uk-width-1-1 crr-blocks" style="page-break-inside: avoid;">
			<div id="containerIntro" style="display: flex;">
				<h2 class="uk-width-1-1@s uk-width-1-5@m">{{ count($allBuildingInspections) }} @if(count($inspections) > 1 || count($inspections) < 1) Buildings @else Building @endIf @if($dpView) Selected: @else Audited: @endIf </h2> 
				<div class="uk-width-1-2" style="padding-left: 10px;"> 
			    	{{ $inspections->links() }}
			    </div>
			    <div class="uk-width-1-1@s uk-width-1-5@m">
			    	<select onchange="pmGetSingleBuilding(this);" class="uk-select filter-drops uk-width-1-1" multiple="multiple" id="building_dropdown">
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
					  	<input type="checkbox" onchange="pmGetUnCorrectedBuilding(this);" id="uncorrected_checkbox" {{ (session()->has('is_uncorrected') && session()->get('is_uncorrected') == 'true') ? 'checked' : ''  }} >
					  	<span class="slider round"></span> 
					</label> <span class="attention" style="display: inline-block;margin-top: 5px;position: absolute;margin-left: 5px;"> PENDING RESOLUTIONS </span>
				</div>
				<input type="hidden" name="_token" value="{{ csrf_token() }}" id="token">
			</div>
			<style>
				/*for toggler*/
				#containerIntro .switch {
				  position: relative;
				  display: inline-block;
				  width: 52px;
				  height: 26px;
				}

				#containerIntro .switch input { 
				  opacity: 0;
				  width: 0;
				  height: 0;
				}

				#containerIntro .slider {
				  position: absolute;
				  cursor: pointer;
				  top: 0;
				  left: 0;
				  right: 0;
				  bottom: 0;
				  background-color: #ccc;
				  -webkit-transition: .4s;
				  transition: .4s;
				}

				#containerIntro .slider:before {
				  position: absolute;
				  content: "";
				  height: 18px;
				  width: 18px;
				  left: 4px;
				  bottom: 4px;
				  background-color: white;
				  -webkit-transition: .4s;
				  transition: .4s;
				}

				#containerIntro input:checked + .slider {
				  background-color: #005186;
				}

				#containerIntro input:focus + .slider {
				  box-shadow: 0 0 1px #005186;
				}

				#containerIntro input:checked + .slider:before {
				  -webkit-transform: translateX(26px);
				  -ms-transform: translateX(26px);
				  transform: translateX(26px);
				}

				/* Rounded sliders */
				#containerIntro .slider.round {
				  border-radius: 34px;
				}

				#containerIntro .slider.round:before {
				  border-radius: 50%;
				}

				/* select2 style for filter dropdown*/
				.select2-selection {
					box-sizing: border-box !important;
				    border: none !important;
				    background-color: aliceblue !important;
				    font-size: 12px !important;
				    color: black !important;
				    padding-left: 10px !important;
				    border-radius: 0 !important !important;
				    height: 30px !important;
				}
			</style>
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