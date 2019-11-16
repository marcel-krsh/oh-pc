<?php setlocale(LC_MONETARY, 'en_US'); ?>
<style>
 th.left-border, td.left-border{border-left:1px solid #dddddd;}
 th.right-border,td.right-border{border-right:1px solid #dddddd;}
 th.no-bottom-border,td.no-bottom-border{border-bottom:none;}
</style>
<div id="dynamic-modal-content">
	<script>
	resizeModal(95);
	</script>
	<h2>{{$vendor->vendor_name}}</h2>
    <div class="uk-grid">
    	<div class="uk-width-1-1">
    		<table class="uk-table uk-table-hover uk-table-condensed uk-table-striped uk-width-1-1">
				<thead>
					<tr>
						<th colspan="2" class="uk-width-1-4 no-bottom-border" style="vertical-align:top;">
							<div class="uk-panel uk-panel-box uk-margin-large-right">
								<h3 class="uk-panel-title">Contact Information</h3>

								<div class="uk-align-left" >
									@if($vendor->vendor_street_address) {{$vendor->vendor_street_address}}<br> @endif
									@if($vendor->vendor_street_address2) {{$vendor->vendor_street_address2}}<br /> @endif
									{{$vendor->vendor_city}} @if($vendor->state->state_acronym) {{$vendor->state->state_acronym}} @endif @if($vendor->vendor_zip), {{$vendor->vendor_zip}} @endif
									<hr class="dashed-hr">
									@if($vendor->vendor_email)
									<p>
									<a href="mailto:{{$vendor->vendor_email}}">{{$vendor->vendor_email}}</a><br />
									@endif
									{{$vendor->vendor_phone}}</p> 
									<hr class="dashed-hr">
									<p>Include Legacy Vendor Stats <input type="checkbox" name="include_legacy_vendor" value="1" id="include_legacy_vendor" @if($include_legacy_vendor) checked @endif></p>
								</div>
							</div>

						</th>
						@if ($in_parcel == 1)
						<th colspan="2" class="uk-width-1-4">
							<canvas id="vendorsParcelChart_cost" width="70%" height="70%" style="display: block; max-width: 270px; max-height: 270px; margin-top:auto;margin-bottom: auto;margin-left:auto;margin-right: auto;"></canvas>
						</th>
						@endif
						@if ($in_program == 1)
						<th colspan="2" class="uk-width-1-4">
							<canvas id="vendorsProgramsChart_cost" width="70%" height="70%" style="display: block; max-width: 270px; max-height: 270px; margin-top:auto;margin-bottom: auto;margin-left:auto;margin-right: auto;"></canvas>
						</th>
						@endif
						<th colspan="2" class="uk-width-1-4">
							<canvas id="vendorsOverallChart_cost" width="70%" height="70%" style="display: block; max-width: 270px; max-height: 270px; margin-top:auto;margin-bottom: auto;margin-left:auto;margin-right: auto;"></canvas>
						</th>
					</tr>
					<tr>
						<th>
							
						</th>
						<th>
							<div class=" uk-text-right" style="font-weight:normal;">Based on cost amounts</div>
						</th>
						@if ($in_parcel)
						<th colspan="2" class="uk-text-right left-border">
							<small>This Parcel</small>
						</th>
						@endif
						@if ($in_program)
						<th colspan="2" class="uk-text-right left-border">
							<small>This Program</small>
						</th>
						@endif
						<th colspan="2" class="uk-text-right left-border right-border">
							<small>Overall</small>
						</th>
					</tr>
				</thead>
				<tbody>
					@foreach($expense_categories as $expense_category)
					<tr>
						<td style="background: {{$expense_category->color_hex}}" uk-tooltip=""></td>
						<td class="left-border">{{$expense_category->expense_category_name}}</td>
						@if ($in_parcel)
						<td class="uk-text-right left-border">${{number_format($data['parcel']['cost'][$expense_category->id]['total'], 2, '.', ',')}}</td>
						<td class="uk-text-right left-border">{{number_format($data['parcel']['cost'][$expense_category->id]['percentage'], 2, '.', ',')}}%</td>
						@endif
						@if ($in_program)
						<td class="uk-text-right left-border">${{number_format($data['program']['cost'][$expense_category->id]['total'], 2, '.', ',')}}</td>
						<td class="uk-text-right left-border">{{number_format($data['program']['cost'][$expense_category->id]['percentage'], 2, '.', ',')}}%</td>
						@endif
						<td class="uk-text-right left-border">${{number_format($data['overview']['cost'][$expense_category->id]['total'], 2, '.', ',')}}</td>
						<td class="uk-text-right left-border right-border">{{number_format($data['overview']['cost'][$expense_category->id]['percentage'], 2, '.', ',')}}%</td>
					</tr>
					@endforeach
				</tbody>
			</table>
    	</div>
    </div>


    <div class="uk-grid">
    	<div class="uk-width-1-1">
    		<table class="uk-table uk-table-hover uk-table-condensed uk-table-striped uk-width-1-1">
				<thead>
					<tr>
						<th colspan="2" class="uk-width-1-4 no-bottom-border" style="vertical-align:top;">
							
						</th>
						@if ($in_parcel == 1)
						<th colspan="2" class="uk-width-1-4">
							<canvas id="vendorsParcelChart_request" width="70%" height="70%" style="display: block; max-width: 270px; max-height: 270px; margin-top:auto;margin-bottom: auto;margin-left:auto;margin-right: auto;"></canvas>
						</th>
						@endif
						@if ($in_program == 1)
						<th colspan="2" class="uk-width-1-4">
							<canvas id="vendorsProgramsChart_request" width="70%" height="70%" style="display: block; max-width: 270px; max-height: 270px; margin-top:auto;margin-bottom: auto;margin-left:auto;margin-right: auto;"></canvas>
						</th>
						@endif
						<th colspan="2" class="uk-width-1-4">
							<canvas id="vendorsOverallChart_request" width="70%" height="70%" style="display: block; max-width: 270px; max-height: 270px; margin-top:auto;margin-bottom: auto;margin-left:auto;margin-right: auto;"></canvas>
						</th>
					</tr>
					<tr>
						<th>
							
						</th>
						<th>
							<div class=" uk-text-right" style="font-weight:normal;">Based on request amounts</div>
						</th>
						@if ($in_parcel)
						<th colspan="2" class="uk-text-right left-border">
							<small>This Parcel</small>
						</th>
						@endif
						@if ($in_program)
						<th colspan="2" class="uk-text-right left-border">
							<small>This Program</small>
						</th>
						@endif
						<th colspan="2" class="uk-text-right left-border right-border">
							<small>Overall</small>
						</th>
					</tr>
				</thead>
				<tbody>
					@foreach($expense_categories as $expense_category)
					<tr>
						<td style="background: {{$expense_category->color_hex}}" uk-tooltip=""></td>
						<td class="left-border">{{$expense_category->expense_category_name}}</td>
						@if ($in_parcel)
						<td class="uk-text-right left-border">${{number_format($data['parcel']['request'][$expense_category->id]['total'], 2, '.', ',')}}</td>
						<td class="uk-text-right left-border">{{number_format($data['parcel']['request'][$expense_category->id]['percentage'], 2, '.', ',')}}%</td>
						@endif
						@if ($in_program)
						<td class="uk-text-right left-border">${{number_format($data['program']['request'][$expense_category->id]['total'], 2, '.', ',')}}</td>
						<td class="uk-text-right left-border">{{number_format($data['program']['request'][$expense_category->id]['percentage'], 2, '.', ',')}}%</td>
						@endif
						<td class="uk-text-right left-border">${{number_format($data['overview']['request'][$expense_category->id]['total'], 2, '.', ',')}}</td>
						<td class="uk-text-right left-border right-border">{{number_format($data['overview']['request'][$expense_category->id]['percentage'], 2, '.', ',')}}%</td>
					</tr>
					@endforeach
				</tbody>
			</table>
    	</div>
    </div>

    <div class="uk-grid">
    	<div class="uk-width-1-1">
    		<table class="uk-table uk-table-hover uk-table-condensed uk-table-striped uk-width-1-1">
				<thead>
					<tr>
						<th colspan="2" class="uk-width-1-4 no-bottom-border" style="vertical-align:top;">

						</th>
						@if ($in_parcel == 1)
						<th colspan="2" class="uk-width-1-4">
							<canvas id="vendorsParcelChart_po" width="70%" height="70%" style="display: block; max-width: 270px; max-height: 270px; margin-top:auto;margin-bottom: auto;margin-left:auto;margin-right: auto;"></canvas>
						</th>
						@endif
						@if ($in_program == 1)
						<th colspan="2" class="uk-width-1-4">
							<canvas id="vendorsProgramsChart_po" width="70%" height="70%" style="display: block; max-width: 270px; max-height: 270px; margin-top:auto;margin-bottom: auto;margin-left:auto;margin-right: auto;"></canvas>
						</th>
						@endif
						<th colspan="2" class="uk-width-1-4">
							<canvas id="vendorsOverallChart_po" width="70%" height="70%" style="display: block; max-width: 270px; max-height: 270px; margin-top:auto;margin-bottom: auto;margin-left:auto;margin-right: auto;"></canvas>
						</th>
					</tr>
					<tr>
						<th>
							
						</th>
						<th>
							<div class=" uk-text-right" style="font-weight:normal;">Based on PO amounts</div>
						</th>
						@if ($in_parcel)
						<th colspan="2" class="uk-text-right left-border">
							<small>This Parcel</small>
						</th>
						@endif
						@if ($in_program)
						<th colspan="2" class="uk-text-right left-border">
							<small>This Program</small>
						</th>
						@endif
						<th colspan="2" class="uk-text-right left-border right-border">
							<small>Overall</small>
						</th>
					</tr>
				</thead>
				<tbody>
					@foreach($expense_categories as $expense_category)
					<tr>
						<td style="background: {{$expense_category->color_hex}}" uk-tooltip=""></td>
						<td class="left-border">{{$expense_category->expense_category_name}}</td>
						@if ($in_parcel)
						<td class="uk-text-right left-border">${{number_format($data['parcel']['po'][$expense_category->id]['total'], 2, '.', ',')}}</td>
						<td class="uk-text-right left-border">{{number_format($data['parcel']['po'][$expense_category->id]['percentage'], 2, '.', ',')}}%</td>
						@endif
						@if ($in_program)
						<td class="uk-text-right left-border">${{number_format($data['program']['po'][$expense_category->id]['total'], 2, '.', ',')}}</td>
						<td class="uk-text-right left-border">{{number_format($data['program']['po'][$expense_category->id]['percentage'], 2, '.', ',')}}%</td>
						@endif
						<td class="uk-text-right left-border">${{number_format($data['overview']['po'][$expense_category->id]['total'], 2, '.', ',')}}</td>
						<td class="uk-text-right left-border right-border">{{number_format($data['overview']['po'][$expense_category->id]['percentage'], 2, '.', ',')}}%</td>
					</tr>
					@endforeach
				</tbody>
			</table>
    	</div>
    </div>

    <div class="uk-grid">
    	<div class="uk-width-1-1">
    		<table class="uk-table uk-table-hover uk-table-condensed uk-table-striped uk-width-1-1">
				<thead>
					<tr>
						<th colspan="2" class="uk-width-1-4 no-bottom-border" style="vertical-align:top;">
						</th>
						@if ($in_parcel == 1)
						<th colspan="2" class="uk-width-1-4">
							<canvas id="vendorsParcelChart_invoice" width="70%" height="70%" style="display: block; max-width: 270px; max-height: 270px; margin-top:auto;margin-bottom: auto;margin-left:auto;margin-right: auto;"></canvas>
						</th>
						@endif
						@if ($in_program == 1)
						<th colspan="2" class="uk-width-1-4">
							<canvas id="vendorsProgramsChart_invoice" width="70%" height="70%" style="display: block; max-width: 270px; max-height: 270px; margin-top:auto;margin-bottom: auto;margin-left:auto;margin-right: auto;"></canvas>
						</th>
						@endif
						<th colspan="2" class="uk-width-1-4">
							<canvas id="vendorsOverallChart_invoice" width="70%" height="70%" style="display: block; max-width: 270px; max-height: 270px; margin-top:auto;margin-bottom: auto;margin-left:auto;margin-right: auto;"></canvas>
						</th>
					</tr>
					<tr>
						<th>
							
						</th>
						<th>
							<div class=" uk-text-right" style="font-weight:normal;">Based on invoice amounts</div>
						</th>
						@if ($in_parcel)
						<th colspan="2" class="uk-text-right left-border">
							<small>This Parcel</small>
						</th>
						@endif
						@if ($in_program)
						<th colspan="2" class="uk-text-right left-border">
							<small>This Program</small>
						</th>
						@endif
						<th colspan="2" class="uk-text-right left-border right-border">
							<small>Overall</small>
						</th>
					</tr>
				</thead>
				<tbody>
					@foreach($expense_categories as $expense_category)
					<tr>
						<td style="background: {{$expense_category->color_hex}}" uk-tooltip=""></td>
						<td class="left-border">{{$expense_category->expense_category_name}}</td>
						@if ($in_parcel)
						<td class="uk-text-right left-border">${{number_format($data['parcel']['invoice'][$expense_category->id]['total'], 2, '.', ',')}}</td>
						<td class="uk-text-right left-border">{{number_format($data['parcel']['invoice'][$expense_category->id]['percentage'], 2, '.', ',')}}%</td>
						@endif
						@if ($in_program)
						<td class="uk-text-right left-border">${{number_format($data['program']['invoice'][$expense_category->id]['total'], 2, '.', ',')}}</td>
						<td class="uk-text-right left-border">{{number_format($data['program']['invoice'][$expense_category->id]['percentage'], 2, '.', ',')}}%</td>
						@endif
						<td class="uk-text-right left-border">${{number_format($data['overview']['invoice'][$expense_category->id]['total'], 2, '.', ',')}}</td>
						<td class="uk-text-right left-border right-border">{{number_format($data['overview']['invoice'][$expense_category->id]['percentage'], 2, '.', ',')}}%</td>
					</tr>
					@endforeach
				</tbody>
			</table>
    	</div>
    </div>

    <div class="uk-grid">
    	<div class="uk-width-1-3@m">
	    </div>
	    <div class="uk-width-1-3@m">
	    </div>
	    <div class="uk-width-1-3@m">
	    	<button class="uk-button uk-button-success uk-width-1-1@m uk-button-large uk-modal-close-default" uk-close>OK</button>
	    </div>
    </div>
</div>	
<script type="text/javascript">

	$('#include_legacy_vendor').change(function(){
        if(this.checked){
        	$.get( "/session/include_legacy_vendors", function( data ) {
                dynamicModalLoad('expense-categories-vendor-details/{{$vendor->id}}/@php if($parcel) echo $parcel->id; @endphp/@php if($program) echo $program->id; @endphp/')
            });
        }else{
        	$.get( "/session/exclude_legacy_vendors", function( data ) {
                dynamicModalLoad('expense-categories-vendor-details/{{$vendor->id}}/@php if($parcel) echo $parcel->id; @endphp/@php if($program) echo $program->id; @endphp/')
            });
        }
    });

	var chartsOptions = {
		segmentShowStroke : false,
		legendPosition : 'bottom',
		segmentStrokeColor : "#fff",
		segmentStrokeWidth : 0,
		cutoutPercentage : 67,
		easing: "easeOutBounce",
		duration: 100,
		tooltips: {
			enabled: true,
			mode: 'single',
			callbacks: {
				label: function(tooltipItem, data) {
					var label = data.labels[tooltipItem.index];
					var datasetLabel = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
					return label + ': $' + addCommas(datasetLabel) ;
				}
			}
		}
	}
	function addCommas(nStr)
	{
		nStr += '';
		x = nStr.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;
	}
@if($parcel)
	var data_parcel_cost = {
		datasets: [{
			data: [
			@foreach($expense_categories as $expense_category)
				{{$data['parcel']['cost'][$expense_category->id]['total']}},
				@if($loop->last)
			        {{$data['parcel']['cost'][$expense_category->id]['grand_total_all_categories']}}
			    @endif
			@endforeach
		    ],
			backgroundColor: [
				{!!$cat_colors!!}
			],
			label: 'My dataset' // for legend
		}],
		labels: [
			{!!$cat_names!!}
			@if($include_legacy_vendor)
			"All Others"
			@else
			"All Others Excluding Legacy"
			@endif
		]
	};
@endif
@if($program)
	var data_program_cost = {
		datasets: [{
			data: [
			@foreach($expense_categories as $expense_category)
				{{$data['program']['cost'][$expense_category->id]['total']}},
				@if($loop->last)
			        {{$data['program']['cost'][$expense_category->id]['grand_total_all_categories']}}
			    @endif
			@endforeach
				
            ],
			backgroundColor: [
			 	{!!$cat_colors!!}
			],
			label: 'My dataset' // for legend
		}],
		labels: [
			{!!$cat_names!!}
			@if($include_legacy_vendor)
			"All Others"
			@else
			"All Others Excluding Legacy"
			@endif	
		]
	};
@endif
	var data_overall_cost = {
		datasets: [{
			data: [
			@foreach($expense_categories as $expense_category)
				{{$data['overview']['cost'][$expense_category->id]['total']}},
				@if($loop->last)
			        {{$data['overview']['cost'][$expense_category->id]['grand_total_all_categories']}}
			    @endif
			@endforeach
            ],
			backgroundColor: [
				{!!$cat_colors!!}
			],
			label: 'My dataset' // for legend
		}],
		labels: [
			{!!$cat_names!!}	
			@if($include_legacy_vendor)
			"All Others"
			@else
			"All Others Excluding Legacy"
			@endif
		]
	};

@if($parcel)
	var data_parcel_request = {
		datasets: [{
			data: [
			@foreach($expense_categories as $expense_category)
				{{$data['parcel']['request'][$expense_category->id]['total']}},
				@if($loop->last)
			        {{$data['parcel']['request'][$expense_category->id]['grand_total_all_categories']}}
			    @endif
			@endforeach
		    ],
			backgroundColor: [
				{!!$cat_colors!!}
			],
			label: 'My dataset' // for legend
		}],
		labels: [
			{!!$cat_names!!}
			@if($include_legacy_vendor)
			"All Others"
			@else
			"All Others Excluding Legacy"
			@endif
		]
	};
@endif
@if($program)
	var data_program_request = {
		datasets: [{
			data: [
			@foreach($expense_categories as $expense_category)
				{{$data['program']['request'][$expense_category->id]['total']}},
				@if($loop->last)
			        {{$data['program']['request'][$expense_category->id]['grand_total_all_categories']}}
			    @endif
			@endforeach
				
            ],
			backgroundColor: [
			 	{!!$cat_colors!!}
			],
			label: 'My dataset' // for legend
		}],
		labels: [
			{!!$cat_names!!}	
			@if($include_legacy_vendor)
			"All Others"
			@else
			"All Others Excluding Legacy"
			@endif
		]
	};
@endif
	var data_overall_request = {
		datasets: [{
			data: [
			@foreach($expense_categories as $expense_category)
				{{$data['overview']['request'][$expense_category->id]['total']}},
				@if($loop->last)
			        {{$data['overview']['request'][$expense_category->id]['grand_total_all_categories']}}
			    @endif
			@endforeach
				
            ],
			backgroundColor: [
				{!!$cat_colors!!}
			],
			label: 'My dataset' // for legend
		}],
		labels: [
			{!!$cat_names!!}	
			@if($include_legacy_vendor)
			"All Others"
			@else
			"All Others Excluding Legacy"
			@endif
		]
	};

@if($parcel)
	var data_parcel_po = {
		datasets: [{
			data: [
			@foreach($expense_categories as $expense_category)
				{{$data['parcel']['po'][$expense_category->id]['total']}},
				@if($loop->last)
			        {{$data['parcel']['po'][$expense_category->id]['grand_total_all_categories']}}
			    @endif
			@endforeach
		    ],
			backgroundColor: [
				{!!$cat_colors!!}
			],
			label: 'My dataset' // for legend
		}],
		labels: [
			{!!$cat_names!!}
			@if($include_legacy_vendor)
			"All Others"
			@else
			"All Others Excluding Legacy"
			@endif
		]
	};
@endif
@if($program)
	var data_program_po = {
		datasets: [{
			data: [
			@foreach($expense_categories as $expense_category)
				{{$data['program']['po'][$expense_category->id]['total']}},
				@if($loop->last)
			        {{$data['program']['po'][$expense_category->id]['grand_total_all_categories']}}
			    @endif
			@endforeach
				
            ],
			backgroundColor: [
			 	{!!$cat_colors!!}
			],
			label: 'My dataset' // for legend
		}],
		labels: [
			{!!$cat_names!!}
			@if($include_legacy_vendor)
			"All Others"
			@else
			"All Others Excluding Legacy"
			@endif
		]
	};
@endif
	var data_overall_po = {
		datasets: [{
			data: [
			@foreach($expense_categories as $expense_category)
				{{$data['overview']['po'][$expense_category->id]['total']}},
				@if($loop->last)
			        {{$data['overview']['po'][$expense_category->id]['grand_total_all_categories']}}
			    @endif
			@endforeach
				
            ],
			backgroundColor: [
				{!!$cat_colors!!}
			],
			label: 'My dataset' // for legend
		}],
		labels: [
			{!!$cat_names!!}
			@if($include_legacy_vendor)
			"All Others"
			@else
			"All Others Excluding Legacy"
			@endif	
		]
	};

@if($parcel)
	var data_parcel_invoice = {
		datasets: [{
			data: [
			@foreach($expense_categories as $expense_category)
				{{$data['parcel']['invoice'][$expense_category->id]['total']}},
				@if($loop->last)
			        {{$data['parcel']['invoice'][$expense_category->id]['grand_total_all_categories']}}
			    @endif
			@endforeach
		    ],
			backgroundColor: [
				{!!$cat_colors!!}
			],
			label: 'My dataset' // for legend
		}],
		labels: [
			{!!$cat_names!!}
			@if($include_legacy_vendor)
			"All Others"
			@else
			"All Others Excluding Legacy"
			@endif
		]
	};
@endif
@if($program)
	var data_program_invoice = {
		datasets: [{
			data: [
			@foreach($expense_categories as $expense_category)
				{{$data['program']['invoice'][$expense_category->id]['total']}},
				@if($loop->last)
			        {{$data['program']['invoice'][$expense_category->id]['grand_total_all_categories']}}
			    @endif
			@endforeach
				
            ],
			backgroundColor: [
			 	{!!$cat_colors!!}
			],
			label: 'My dataset' // for legend
		}],
		labels: [
			{!!$cat_names!!}	
			@if($include_legacy_vendor)
			"All Others"
			@else
			"All Others Excluding Legacy"
			@endif
		]
	};
@endif
	var data_overall_invoice = {
		datasets: [{
			data: [
			@foreach($expense_categories as $expense_category)
				{{$data['overview']['invoice'][$expense_category->id]['total']}},
				@if($loop->last)
			        {{$data['overview']['invoice'][$expense_category->id]['grand_total_all_categories']}}
			    @endif
			@endforeach
				
            ],
			backgroundColor: [
				{!!$cat_colors!!}
			],
			label: 'My dataset' // for legend
		}],
		labels: [
			{!!$cat_names!!}	
			@if($include_legacy_vendor)
			"All Others"
			@else
			"All Others Excluding Legacy"
			@endif
		]
	};
	@if ($in_parcel)
	var ctx_parcel_cost = document.getElementById("vendorsParcelChart_cost").getContext("2d");
	var ctx_parcel_request = document.getElementById("vendorsParcelChart_request").getContext("2d");
	var ctx_parcel_po = document.getElementById("vendorsParcelChart_po").getContext("2d");
	var ctx_parcel_invoice = document.getElementById("vendorsParcelChart_invoice").getContext("2d");
	@endif

	@if ($in_program)
	var ctx_programs_cost = document.getElementById("vendorsProgramsChart_cost").getContext("2d");
	var ctx_programs_request = document.getElementById("vendorsProgramsChart_request").getContext("2d");
	var ctx_programs_po = document.getElementById("vendorsProgramsChart_po").getContext("2d");
	var ctx_programs_invoice = document.getElementById("vendorsProgramsChart_invoice").getContext("2d");
	@endif

	var ctx_overall_cost = document.getElementById("vendorsOverallChart_cost").getContext("2d");
	var ctx_overall_request = document.getElementById("vendorsOverallChart_request").getContext("2d");
	var ctx_overall_po = document.getElementById("vendorsOverallChart_po").getContext("2d");
	var ctx_overall_invoice = document.getElementById("vendorsOverallChart_invoice").getContext("2d");

	@if ($in_parcel)
	var parcelChart_cost = new Chart(ctx_parcel_cost, {type: 'doughnut', data: data_parcel_cost, options: chartsOptions});
	var parcelChart_request = new Chart(ctx_parcel_request, {type: 'doughnut', data: data_parcel_request, options: chartsOptions});
	var parcelChart_po = new Chart(ctx_parcel_po, {type: 'doughnut', data: data_parcel_po, options: chartsOptions});
	var parcelChart_invoice = new Chart(ctx_parcel_invoice, {type: 'doughnut', data: data_parcel_invoice, options: chartsOptions});
	@endif
	@if ($in_program)
	var programsChart_cost = new Chart(ctx_programs_cost, {type: 'doughnut', data: data_program_cost, options: chartsOptions});
	var programsChart_request = new Chart(ctx_programs_request, {type: 'doughnut', data: data_program_request, options: chartsOptions});
	var programsChart_po = new Chart(ctx_programs_po, {type: 'doughnut', data: data_program_po, options: chartsOptions});
	var programsChart_invoice = new Chart(ctx_programs_invoice, {type: 'doughnut', data: data_program_invoice, options: chartsOptions});
	@endif
	var overallChart_cost = new Chart(ctx_overall_cost, {type: 'doughnut', data: data_overall_cost, options: chartsOptions});
	var overallChart_request = new Chart(ctx_overall_request, {type: 'doughnut', data: data_overall_request, options: chartsOptions});
	var overallChart_po = new Chart(ctx_overall_po, {type: 'doughnut', data: data_overall_po, options: chartsOptions});
	var overallChart_invoice = new Chart(ctx_overall_invoice, {type: 'doughnut', data: data_overall_invoice, options: chartsOptions});

</script>