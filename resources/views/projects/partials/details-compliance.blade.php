@include('projects.templates.details-compliance')
<style type="text/css">

	.editing {
		border:1px dotted;
	}
</style>
<div class="project-details-info-compliance uk-overflow-auto ok-actionable" style="" uk-grid>

	<div class="uk-width-1-1">
		<div class=" uk-margin-left uk-margin-right ">
			<hr>

		</div>
		<div class="project-details-info-compliance-summary uk-margin-top uk-margin-left uk-margin-right uk-grid-match" uk-grid>
			<div class="uk-width-1-5">
				<div>
					<canvas id="chartjs-summary" class="chartjs" style="display: block;"></canvas>
				</div>
				<a onclick="dynamicModalLoad('projects/{{$data["project"]["id"]}}/programs/0/summary/{{$data["auditID"]}}',0,0,1);" class="uk-button uk-button-small uk-button-success " style="margin-top: 18px; height: 25px; padding-top:2px;"><i class="a-arrow-diagonal-both use-hand-cursor" ></i> SWAP UNITS</a>
			</div>
			<div class="uk-width-2-5">
				<table class="uk-table uk-table-small noline small-padding">
					<tbody>
						<tr>
							<td></td>
							<td class="uk-text-center"><i class="a-mobile-home iheader"></i></td>
							<td class="uk-text-center"><i class="a-folder iheader"></i></td>
						</tr>
						<tr>
							<td>
								<div uk-leader><strong>Compliance Requirements (without overlap)</strong></div>
							</td>
							<td class="uk-text-center border-right"></td>
							<td class="uk-text-center"></td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-required"></i> Required Units</div>
							</td>
							<td class="uk-text-center ru border-right">{{$data['summary']['required_units']}}
									{{-- <div>
										<input ondblclick="enableEditField('#summary_required_units');" name="summary_required_units" id="summary_required_units" type="text" value="{{$data['summary']['required_units']}}" class="change-watch" onblur="disableEditField('#summary_required_units');" style="text-align: center;background: transparent;border: none;color: #333;width: 50px;" readonly="">
									</div> --}}
								</td>
								<td class="uk-text-center">
									{{$data['summary']['required_units_file']}}
								{{-- <div>
									<input ondblclick="enableEditField('#summary_required_units_file');" name="summary_required_units_file" id="summary_required_units_file" type="text" value="{{$data['summary']['required_units_file']}}" class="change-watch" onblur="disableEditField('#summary_required_units_file');" style="text-align: center;background: transparent;border: none;color: #333;width: 50px;" readonly="">
								</div> --}}
							</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-selected"></i> Selected Units</div>
							</td>
							<td class="uk-text-center border-right">{{$data['summary']['selected_units']}}</td>
							<td class="uk-text-center">{{$data['summary']['selected_units_file']}}</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-needed"></i> Needed Units</div>
							</td>
							<td class="uk-text-center border-right">{{$data['summary']['needed_units']}}</td>
							<td class="uk-text-center">{{$data['summary']['needed_units_file']}}</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-inspected"></i> Inspected Units</div>
							</td>
							<td class="uk-text-center border-right">{{$data['summary']['inspected_units']}}</td>
							<td class="uk-text-center">{{$data['summary']['inspected_units_file']}}</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-to-be-inspected"></i> To Be Inspected Units</div>
							</td>
							<td class="uk-text-center border-right">{{$data['summary']['to_be_inspected_units']}}</td>
							<td class="uk-text-center">{{$data['summary']['to_be_inspected_units_file']}}</td>
						</tr>
					</tbody>
				</table>
			</div>
			{{-- <div class="uk-width-2-5">
				<table class="uk-table uk-table-small noline small-padding">
					<tbody>
						<tr>
							<td></td>
							<td class="uk-text-center"><i class="a-mobile-home iheader"></i></td>
							<td class="uk-text-center"><i class="a-folder iheader"></i></td>
						</tr>
						<tr>
							<td>
								<div uk-leader><strong>Inspections Required to Meet Compliance</strong></div>
							</td>
							<td class="uk-text-center border-right"></td>
							<td class="uk-text-center"></td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-selected"></i> Optimized Sample Size</div>
							</td>
							<td class="uk-text-center border-right">{{$data['summary']['optimized_sample_size']}}</td>
							<td class="uk-text-center">{{$data['summary']['optimized_sample_size_file']}}</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-inspected"></i> Optimized Inspections Completed</div>
							</td>
							<td class="uk-text-center border-right">{{$data['summary']['optimized_completed_inspections']}}</td>
							<td class="uk-text-center">{{$data['summary']['optimized_completed_inspections_file']}}</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-to-be-inspected"></i> Optimized Remaining Inspections</div>
							</td>
							<td class="uk-text-center border-right">{{$data['summary']['optimized_remaining_inspections']}}</td>
							<td class="uk-text-center">{{$data['summary']['optimized_remaining_inspections_file']}}</td>
						</tr>
					</tbody>
				</table>
			</div> --}}
		</div>

		<div class="project-details-info-compliance-programs uk-position-relative uk-visible-toggle uk-margin-top"  >
			<ul class="uk-list uk-margin-top">
				@if(array_key_exists('programs',$data))
				@foreach($data['programs'] as $key => $program)
				{{-- {{ dd($data['programs']) }} --}}
				{{-- @if($program['required_units'] != 0) --}}
				<li>
					<div class="project-details-info-compliance-program uk-panel uk-grid-match" style="height:180px" uk-grid>
						<div class="uk-width-1-5">
							<div class="uk-padding-remove" style="min-height:165px;">
								<canvas id="chartjs-{{$program['id']}}{{$program['building_key']}}" class="chartjs" ></canvas>
							</div>
							<a onclick="dynamicModalLoad('projects/{{$data["project"]["id"]}}/programs/0/summary/{{$data["auditID"]}}',0,0,1);" class="uk-button uk-link proj_details proj_details_1" style="margin-top: 18px;"><i class="a-arrow-diagonal-both use-hand-cursor" ></i> SWAP UNITS</a>
						</div>
						<div class="uk-width-2-5">
							<?php
							$program_id=$program['id'];
							$auditID=$data['auditID'];
							?>
							<table class="uk-table uk-table-small noline small-padding">
								<tbody>
									<tr>
										<td><strong>{{$program['name']}} INSPECTION @if($program['building_name']!='') | BUILDING {{$program['building_name']}}@endif</strong></td>
										<td class="uk-text-center" style="min-width: 30px;"><i class="a-mobile-home iheader"></i></td>
										<td class="uk-text-center" style="min-width: 30px;"><i class="a-folder iheader"></i></td>
									</tr>
									<tr>
										<td>
											<div class="indented" uk-leader><i class="fas fa-square chart-color-required"></i> Required Units</div>
										</td>
										<td class="uk-text-center border-right">
											{{-- {{$program['required_units']}} --}}
											<div ondblclick="enableEditField('#required_units_{{$program_id}}_{{ $key }}','{{$program['name']}}','required_units','{{$program_id}}',{{$auditID}});">
												<input  name="required_units[{{$program_id}}]" id="required_units_{{$program_id}}_{{ $key }}" type="text" value="{{$program['required_units']}}" class="change-watch" onblur="disableEditField('#required_units_{{$program_id}}_{{ $key }}','{{$program['name']}}','required_units','{{$program_id}}',{{$auditID}},'{{ $program['building_key'] }}');" style="text-align: center;background: transparent;border: none;color: #333;width: 50px; font-size: 14px" disabled='true'>
											</div>
										</td>
										<td class="uk-text-center">
											{{-- {{$program['required_units_file']}} --}}
											<div ondblclick="enableEditField('#required_units_file_{{$program_id}}_{{ $key }}','{{$program['name']}}','required_units_file','{{$program_id}},{{$auditID}}');">
												<input  name="required_units_file[{{$program_id}}]" id="required_units_file_{{$program_id}}_{{ $key }}" type="text" value="{{$program['required_units_file']}}" class="change-watch" onblur="disableEditField('#required_units_file_{{$program_id}}_{{ $key }}','{{$program['name']}}','required_units_file','{{$program_id}}',{{$auditID}},'{{ $program['building_key'] }}');" style="text-align: center;background: transparent;border: none;color: #333;width: 50px; font-size: 14px;" disabled="true">
											</div>
										</td>
									</tr>
									<tr>
										<td>
											<div class="indented" uk-leader><i class="fas fa-square chart-color-selected"></i> Selected Units</div>
										</td>
										<td class="uk-text-center border-right">{{$program['selected_units']}}</td>
										<td class="uk-text-center">{{$program['selected_units_file']}}</td>
									</tr>
									<tr>
										<td>
											<div class="indented" uk-leader><i class="fas fa-square chart-color-needed"></i> Needed Units</div>
										</td>
										<td class="uk-text-center border-right">{{$program['needed_units']}}</td>
										<td class="uk-text-center">{{$program['needed_units_file']}}</td>
									</tr>
									<tr>
										<td>
											<div class="indented" uk-leader><i class="fas fa-square chart-color-inspected"></i> Inspected Units</div>
										</td>
										<td class="uk-text-center border-right">{{$program['inspected_units']}}</td>
										<td class="uk-text-center">{{$program['inspected_units_file']}}</td>
									</tr>
									<tr>
										<td>
											<div class="indented" uk-leader><i class="fas fa-square chart-color-to-be-inspected"></i> To Be Inspected Units</div>
										</td>
										<td class="uk-text-center border-right">{{$program['to_be_inspected_units']}}</td>
										<td class="uk-text-center">{{$program['to_be_inspected_units_file']}}</td>
									</tr>
								</tbody>
							</table>
							<button class="uk-button uk-button-default expandcomment" onclick="$(this).find( 'span' ).toggleClass('a-arrow-small-down a-arrow-small-up'); $('#comment-{{$program['id']}}{{$program['building_key']}}').toggleClass('uk-hidden');"><span class="a-arrow-small-down uk-text-small uk-vertical-align-middle"></span> VIEW COMPLIANCE SELECTION PROCESS</button>
						</div>
						<div class="uk-width-2-5">
						</div>
					</div>
					<div id="comment-{{$program['id']}}{{$program['building_key']}}" class="uk-column-1-2 uk-padding uk-hidden">

						<p><strong>{{$program['name']}} COMPLIANCE SELECTION NOTES:</strong></p>

						@foreach($program['comments'] as $comment)
						<p>{{$comment}}</p>
						@endforeach
					</div>
					<script>
						new Chart(document.getElementById("chartjs-{{$program['id']}}{{$program['building_key']}}"),{
							"type":"doughnut",
							"options": summaryOptions,
							"data":{
								"labels": ["Required","Selected","Needed","Inspected", "To Be Inspected"],
								"datasets":[
								{
									"label":"Inspected",
									"data":[0,0,0,{{$program['inspected_units_file'] + $program['inspected_units']}},{{$program['to_be_inspected_units_file'] + $program['to_be_inspected_units']}}],
									"backgroundColor":[
									chartColors.required,
									chartColors.selected,
									chartColors.needed,
									chartColors.inspected,
									chartColors.tobeinspected
									],
									"borderWidth": 1
								},
								{
									"label":"Selected/Needed",
									"data":[0,{{$program['selected_units_file'] + $program['selected_units']}},{{$program['needed_units_file'] + $program['needed_units']}},0,0],
									"backgroundColor":[
									chartColors.required,
									chartColors.selected,
									chartColors.needed,
									chartColors.inspected,
									chartColors.tobeinspected
									],
									"borderWidth": 1
								},
								{
									"label":"Required",
									"data":[{{$program['required_units_file'] + $program['required_units']}},0,0,0,0],
									"backgroundColor":[
									chartColors.required,
									chartColors.selected,
									chartColors.needed,
									chartColors.inspected,
									chartColors.tobeinspected
									],
									"borderWidth": 1
								}
								]
							}
						});

						{{--document.getElementById("chartjs-{{$program['id']}}").onclick = function(e) {--}}
						{{--var slice = mainSummaryChart.getElementAtEvent(e);--}}
						{{--if (!slice.length) return; // return if not clicked on slice--}}
						{{--var label = slice[0]._model.label;--}}
						{{--var color = slice[0]._view.backgroundColor;--}}
						{{--console.log(slice[0]._view.backgroundColor);--}}
						{{--var programName = label;--}}

						{{--switch (color) {--}}
						{{--case '#191818':--}}
						{{--// alert(label + ' / required');--}}
						{{--dynamicModalLoad('projects/{{$data["project"]["id"]}}/programs/{{$program["id"]}}/summary',0,0,1);--}}
						{{--break;--}}
						{{--case '#0099d5':--}}
						{{--dynamicModalLoad('projects/{{$data["project"]["id"]}}/programs/{{$program["id"]}}/summary',0,0,1);--}}
						{{--break;--}}
						{{--case '#d31373':--}}
						{{--dynamicModalLoad('projects/{{$data["project"]["id"]}}/programs/{{$program["id"]}}/summary',0,0,1);--}}
						{{--break;--}}
						{{--case '#21a26e':--}}
						{{--dynamicModalLoad('projects/{{$data["project"]["id"]}}/programs/{{$program["id"]}}/summary',0,0,1);--}}
						{{--break;--}}
						{{--case '#e0e0df':--}}
						{{--dynamicModalLoad('projects/{{$data["project"]["id"]}}/programs/{{$program["id"]}}/summary',0,0,1);--}}
						{{--break;--}}
						{{--}--}}
						{{--}--}}
					</script>
				</li>
				{{-- @endif --}}
				@endforeach
				@else
				<li>It appears your compliance run did not fully complete its run. Please rerun to avoid potential issues.</li>
				@endIf
			</ul>
		</div>

	</div>

</div>



<script>

	// function loadProjectDetailsInfoCompliancePrograms(data) {
	// 	var template = $('#project-details-info-compliance-program-template').html();

	// 	var programs = '';
	// 	var newprogram = '';
	// 	data.forEach(function(program) {
	// 		newprogram = inspectionAreaTemplate;
	// 		newprogram = newprogram.replace(/tplProgramId/g, program.id);
	// 		newprogram = newprogram.replace(/tplProgramName/g, program.name);

	// 		programs = programs + newprogram;
	// 	});
	// 	$('.project-details-info-compliance-programs-items').html(programs);
	// 	$('.project-details-info-compliance-programs').fadeIn( "slow", function() {
	// 	    // Animation complete
	// 	  });

	// }
	//
	var chartColors = {
		required: '#191818',
		selected: '#0099d5',
		needed: '#d31373',
		inspected: '#21a26e',
		tobeinspected: '#e0e0df'
	};
	Chart.defaults.global.legend.display = false;
	Chart.defaults.global.tooltips.enabled = true;

    // THIS SCRIPT MUST BE UPDATED WITH NEW VALUES AFTER A NEW FUNDING SUBMISSION HAS BEEN MADE  - to make this simple - this tab is reloaded on form submission of new payment/ payment edits //
    var summaryOptions = {
        //Boolean - Whether we should show a stroke on each segment
        segmentShowStroke : false,
        legendPosition : 'bottom',

        "cutoutPercentage":40,
        "legend" : {
        	"display" : false
        },
        "responsive" : true,
        "maintainAspectRatio" : false,

        //String - The colour of each segment stroke
        segmentStrokeColor : "#fff",

        //Number - The width of each segment stroke
        segmentStrokeWidth : 0,

        //The percentage of the chart that we cut out of the middle.
        // cutoutPercentage : 67,

        easing: "linear",

        duration: 100000,

        tooltips: {
        	enabled: true,
        	mode: 'single',
        	callbacks: {
        		label: function(tooltipItem, data) {
        			var label = data.labels[tooltipItem.index];
        			var datasetLabel = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
        			return label + ': ' + addCommas(datasetLabel) + ' units' ;
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

      var mainSummaryChart = new Chart(document.getElementById("chartjs-summary"),{
      	"type":"doughnut",
      	"options": summaryOptions,

      	"data":{
      		"labels": ["Required","Selected","Needed","Inspected", "To Be Inspected"],
      		"datasets":[
      		{
      			"label":"Program 1",
      			"data":[0,0,0,{{$data['summary']['inspected_units_file'] + $data['summary']['inspected_units']}},{{$data['summary']['to_be_inspected_units_file'] + $data['summary']['to_be_inspected_units']}}],
      			"backgroundColor":[
      			chartColors.required,
      			chartColors.selected,
      			chartColors.needed,
      			chartColors.inspected,
      			chartColors.tobeinspected
      			],
      			"borderWidth": 1
      		},
      		{
      			"label":"Program 3",
      			"data":[0,{{$data['summary']['selected_units_file'] + $data['summary']['selected_units']}},{{$data['summary']['needed_units_file'] + $data['summary']['needed_units']}},0,0],
      			"backgroundColor":[
      			chartColors.required,
      			chartColors.selected,
      			chartColors.needed,
      			chartColors.inspected,
      			chartColors.tobeinspected
      			],
      			"borderWidth": 1
      		},
      		{
      			"label":"Program 2",
      			"data":[{{$data['summary']['required_units_file'] + $data['summary']['required_units']}},0,0,0,0],
      			"backgroundColor":[
      			chartColors.required,
      			chartColors.selected,
      			chartColors.needed,
      			chartColors.inspected,
      			chartColors.tobeinspected
      			],
      			"borderWidth": 1
      		}
      		]
      	}
      });

      document.getElementById("chartjs-summary").onclick = function(e) {
      	var slice = mainSummaryChart.getElementAtEvent(e);
	   if (!slice.length) return; // return if not clicked on slice
	   var label = slice[0]._model.label;
	   var color = slice[0]._view.backgroundColor;
	   console.log(slice[0]._view.backgroundColor);
	   var programName = label;
	   // switch (label) {
	   //    // add case for each label/slice
	   //    case 'Program 1':
	   //       alert('clicked on Prorgam 1');
	   //       break;
	   //    case 'Program 2':
	   //       alert('clicked on program 2');
	   //       break;
	   //    case 'Program 3':
	   //       alert('clicked on program 3');
	   //       break;
	   // }
	   switch (color) {
	   	case 'rgb(24, 22, 22)':
	         // alert(label + ' / required');
	         dynamicModalLoad('projects/{{$data["project"]["id"]}}/programs/0/summary',0,0,1);
	         break;
	         case 'rgb(0, 139, 194)':
	         dynamicModalLoad('projects/{{$data["project"]["id"]}}/programs/0/summary',0,0,1);
	         break;
	         case 'rgb(209, 0, 105)':
	         dynamicModalLoad('projects/{{$data["project"]["id"]}}/programs/0/summary',0,0,1);
	         break;
	         case 'rgb(1, 173, 104)':
	         dynamicModalLoad('projects/{{$data["project"]["id"]}}/programs/0/summary',0,0,1);
	         break;
	         case 'rgb(203, 203, 200)':
	         dynamicModalLoad('projects/{{$data["project"]["id"]}}/programs/0/summary',0,0,1);
	         break;
	       }
	     }

	     function enableEditField(fieldId,fieldName,fieldUnitType,Id){
	     	console.log(fieldId+" "+fieldName+" "+fieldUnitType+" "+Id);
	     	$(fieldId).prop('disabled', false);
	     	$(fieldId).addClass('editing');
	     	$(fieldId).focus();
	     	console.log('Enabled field '+fieldId);
	     }
	     function disableEditField(fieldId,fieldName,fieldUnitType,Id,auditID,buildingKey = '') {
	     	console.log($(fieldId).val());
	     	$(fieldId).prop('disabled', true);
	     	$(fieldId).removeClass('editing');

	     	$.post('{{ URL::route("ajax.audit.required.units") }}', {
	     		'id' : auditID,
	     		'name' : fieldName,
	     		'req_type' : fieldUnitType,
	     		'req_val' : $(fieldId).val(),
	     		'building_key' : buildingKey,
	     		'_token' : '{{ csrf_token() }}'
	     	}, function(data) {
	     		$("#project-details-button-1").trigger("click");
		// if(data){
		// UIkit.modal.alert("");
		// }
	});


	     	console.log('Readonly field '+fieldId);
	     }
	   </script>