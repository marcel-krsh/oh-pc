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
				<h2 class="uk-width-1-1@s uk-width-1-5@m">{{ count($allBuildingInspections) }} @if(count($inspections) > 1 || count($inspections) < 1) Buildings @else Building @endIf @if($dpView) Selected: @else Audited: @endIf </h2> 
				<div class="uk-width-1-2" style="padding-left: 10px;"> 
			    	{{ $inspections->links() }}
			    </div>
			    <div uk-grid class="uk-grid" style="width: 350px;">
			    	<div class="filter-box uk-width-1-1 uk-first-column">
						<!-- <input id="filter-by-address" class="filter-box filter-search-address-input" type="text" placeholder="PRIMARY ADDRESS" value=""> -->
						<!-- <select onchange="getSingleBuilding(this);" class="uk-select filter-box filter-search-address-input" multiple="multiple" id="building_dropdown"> -->
						<select onchange="getSingleBuilding(this);" class="custom-select">
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
			    	
				</div>
			 	<div class="uk-width-1-1@s uk-width-1-5@m" style="padding-left: 10px;">
					<label class="switch">
					  	<input type="checkbox" onchange="getUnCorrectedBuilding(this);" id="uncorrected_checkbox" {{ (session()->has('is_uncorrected') && session()->get('is_uncorrected') == 'true') ? 'checked' : ''  }} >
					  	<span class="slider round"></span> 
					</label> <span class="attention" style="display: inline-block;position: absolute;margin-left: 5px;"> PENDING RESOLUTIONS </span>
				</div>
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

				.select_box_area {
				  position: relative;
				  display: inline-block;
				}
				.select_box_area p {
				  /*margin-bottom: 0px;*/
				  min-width: 300px;
				  max-width: 300px;
				 /* background: #31599c;
				  padding: 10px 15px;
				  border: 1px solid rgba(255, 255, 255, 0.5);
				  line-height: 24px;
				  padding-right: 30px;
				  cursor: pointer;*/
				  box-sizing: border-box;
				    border: none;
				    background-color: aliceblue;
				    font-size: 12px;
				    color: black;
				    padding-left: 10px;
				    border-radius: 0;
				    height: 30px;
				}
				.select_box_area p em {
				  position: absolute;
				  right: 15px;
				  top: 6px;
				  font-size: 20px;
				  transition: all 0.3s linear;
				  color: #000;
				}
				.select_box_area p em.angle-up {
				  transform: rotate(180deg);
				}
				.select_box_area p .option {
				  position: relative;
				  display: inline-block;
				  padding-right: 15px;
				}
				.select_box_area p .option::after {
				  content: ",";
				  position: absolute;
				  right: 5px;
				  top: 0;
				}
				.select_box_area p .option:last-of-type {
				  padding-right: 0px;
				}
				.select_box_area p .option:last-of-type::after {
				  display: none;
				}

				.filter_list_ul {
				  padding: 0px;
				  background: aliceblue;
				  border: 1px solid #999999;
				  border-top: none;
				  display: none;
				  max-height: 300px;
				  overflow-y: scroll;
				  position: relative;
				  z-index: 999999
				}
				.filter_list_ul li {
				  list-style: none;
				}
				.filter_list_ul li label {
				  display: block;
				  width: 100%;
				  padding: 10px;
				  margin: 0px;
				  font-size: 14px;
				  cursor: pointer;
				}
				.filter_list_ul li input[type="checkbox"] {
				  margin-right: 5px;
				}
				.filter_list_ul li + li {
				  border-top: 1px solid #999999;
				}

				.custom-select {
				  display: none;
				}
			</style>
		 	<div class="show_buildings" style="width: 500px;">
		 		<?php
		 			if(session()->has('name')){
		 				$BuildingsId = session()->get('name');
    				    foreach ($BuildingsId as $key => $value) {
    				    ?>
    				    <div id="audit-filter-step" class="uk-badge uk-text-right@s badge-filter">
							<!-- <a onclick="filterAudits('step-schedule-audit', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>STEP "Schedule audit"</span></a> -->
							<span><?php echo $value; ?></span>
						</div>
    				    <?php
    					}
    				}
    			?>
		    	<!-- <div id="audit-filter-step" class="uk-badge uk-text-right@s badge-filter">
					<a onclick="filterAudits('step-schedule-audit', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>STEP "Schedule audit"</span></a>
					<span>STEP "Schedule audit"</span>
				</div> -->
		    </div>
		    <small><i class="a-mobile"></i> : PHYSICAL INSPECTION </small>

			<hr class="dashed-hr uk-margin-bottom">

			<div class="uk-column-1-3 uk-column-divider" id="building-detail">
				@include('crr_parts.crr_inspections_building_detail')
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