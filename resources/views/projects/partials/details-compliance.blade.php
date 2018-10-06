<template class="uk-hidden" id="project-details-info-compliance-program-template">
	<li>
		<div class="project-details-info-compliance-program uk-panel" uk-grid>
			<div class="uk-width-1-4">
				<canvas id="chartjs-tplProgramId" class="chartjs" style="display: block;"></canvas>
			</div>
			<div class="uk-width-3-4">
				<table class="uk-table uk-table-small noline small-padding">
					<tbody>
						<tr>
							<td><strong>tplProgramName INSPECTION</strong></td>
							<td class="uk-text-center"><i class="a-mobile-home iheader"></i></td>
							<td class="uk-text-center"><i class="a-folder iheader"></i></td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-required"></i> Required Units</div>
							</td>
							<td class="uk-text-center border-right">500</td>
							<td class="uk-text-center">500</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-selected"></i> Selected Units</div>
							</td>
							<td class="uk-text-center border-right">375</td>
							<td class="uk-text-center">375</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-needed"></i> Needed Units</div>
							</td>
							<td class="uk-text-center border-right">125</td>
							<td class="uk-text-center">125</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-inspected"></i> Inspected Units</div>
							</td>
							<td class="uk-text-center border-right">125</td>
							<td class="uk-text-center">250</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-to-be-inspected"></i> To Be Inspected Units</div>
							</td>
							<td class="uk-text-center border-right">250</td>
							<td class="uk-text-center">125</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</li>
</template>

<script>
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
</script>
<div class="project-details-info-compliance uk-overflow-auto ok-actionable" style="" uk-grid>
	
	<div class="uk-width-1-1">		
		<div class=" uk-margin-left uk-margin-right ">
			<hr>
			<span style="font-style: italic;">{{$data['summary']['required_unit_selected']}} ALL REQUIRED UNIT COUNTS FOR EACH PROGRAM HAVE BEEN SELECTED. {{$data['summary']['inspectable_areas_assignment_needed']}} INSPECTABLE AREAS NEED ASSIGNMENT. {{$data['summary']['required_units_selection']}} REQUIRED UNITS NEED TO BE SELECTED. {{$data['summary']['file_audits_needed']}} FILE AUDITS NEED TO BE COMPLETED. {{$data['summary']['physical_audits_needed']}} PHYSICAL AUDITS NEED TO BE COMPLETED. {{$data['summary']['schedule_conflicts']}} SCHEDULE CONFLICTS NEED TO BE RESOLVED.</span>
		</div>
		<div class="project-details-info-compliance-summary uk-margin-top uk-margin-left uk-margin-right uk-grid-match" uk-grid>
			<div class="uk-width-1-5">
					<canvas id="chartjs-summary" class="chartjs" style="display: block;"></canvas>
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
								<div uk-leader><strong>Compliance Requirements (with overlap)</strong></div>
							</td>
							<td class="uk-text-center border-right">2,000</td>
							<td class="uk-text-center">2,000</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-required"></i> Required Units</div>
							</td>
							<td class="uk-text-center border-right">500</td>
							<td class="uk-text-center">500</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-selected"></i> Selected Units</div>
							</td>
							<td class="uk-text-center border-right">375</td>
							<td class="uk-text-center">375</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-needed"></i> Needed Units</div>
							</td>
							<td class="uk-text-center border-right">125</td>
							<td class="uk-text-center">125</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-inspected"></i> Inspected Units</div>
							</td>
							<td class="uk-text-center border-right">125</td>
							<td class="uk-text-center">250</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-to-be-inspected"></i> To Be Inspected Units</div>
							</td>
							<td class="uk-text-center border-right">250</td>
							<td class="uk-text-center">125</td>
						</tr>
					</tbody>
				</table>
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
								<div uk-leader><strong>Inspections Required to Meet Compliance</strong></div>
							</td>
							<td class="uk-text-center border-right">188</td>
							<td class="uk-text-center">188</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-selected"></i> Actual Sample Size</div>
							</td>
							<td class="uk-text-center border-right">188</td>
							<td class="uk-text-center">188</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-inspected"></i> Actual Inspections Completed</div>
							</td>
							<td class="uk-text-center border-right">47</td>
							<td class="uk-text-center">94</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-to-be-inspected"></i> Actual Remaining Inspections</div>
							</td>
							<td class="uk-text-center border-right">141</td>
							<td class="uk-text-center">94</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="project-details-info-compliance-programs uk-position-relative uk-visible-toggle uk-margin-top"  uk-slider="finite: true">
    		<ul class="uk-slider-items uk-child-width-1-2 uk-margin-top">
        		@foreach($data['programs'] as $program)
		        <li>
					<div class="project-details-info-compliance-program uk-panel uk-grid-match" style="height:180px" uk-grid>
						<div class="uk-width-1-3">
							<canvas id="chartjs-{{$program['id']}}" class="chartjs" style="height:100%"></canvas>
						</div>
						<div class="uk-width-2-3">
							<table class="uk-table uk-table-small noline small-padding">
								<tbody>
									<tr>
										<td><strong>{{$program['name']}} INSPECTION</strong></td>
										<td class="uk-text-center"><i class="a-mobile-home iheader"></i></td>
										<td class="uk-text-center"><i class="a-folder iheader"></i></td>
									</tr>
									<tr>
										<td>
											<div class="indented" uk-leader><i class="fas fa-square chart-color-required"></i> Required Units</div>
										</td>
										<td class="uk-text-center border-right">500</td>
										<td class="uk-text-center">500</td>
									</tr>
									<tr>
										<td>
											<div class="indented" uk-leader><i class="fas fa-square chart-color-selected"></i> Selected Units</div>
										</td>
										<td class="uk-text-center border-right">375</td>
										<td class="uk-text-center">375</td>
									</tr>
									<tr>
										<td>
											<div class="indented" uk-leader><i class="fas fa-square chart-color-needed"></i> Needed Units</div>
										</td>
										<td class="uk-text-center border-right">125</td>
										<td class="uk-text-center">125</td>
									</tr>
									<tr>
										<td>
											<div class="indented" uk-leader><i class="fas fa-square chart-color-inspected"></i> Inspected Units</div>
										</td>
										<td class="uk-text-center border-right">125</td>
										<td class="uk-text-center">250</td>
									</tr>
									<tr>
										<td>
											<div class="indented" uk-leader><i class="fas fa-square chart-color-to-be-inspected"></i> To Be Inspected Units</div>
										</td>
										<td class="uk-text-center border-right">250</td>
										<td class="uk-text-center">125</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<script>
						new Chart(document.getElementById("chartjs-{{$program['id']}}"),{
							"type":"doughnut",
							"options": summaryOptions,
							"data":{
								"labels": ["Red","Blue","Yellow"],
								"datasets":[
									{
										"label":"Program 1",
										"data":[50,40],
										"backgroundColor":[
											chartColors.inspected,
											chartColors.tobeinspected
										],
										"borderWidth": 1
									},
									{
										"label":"Program 2",
										"data":[20,40],
										"backgroundColor":[
											chartColors.selected,
											chartColors.needed
										],
										"borderWidth": 1
									},
									{
										"label":"Program 3",
										"data":[90],
										"backgroundColor":[
											chartColors.required
										],
										"borderWidth": 1
									}
								]
							}
						});
					</script>
				</li>
		        @endforeach
    		</ul>
		    <a class="uk-position-center-left uk-position-small uk-hidden-hover" style="width:20px" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
		    <a class="uk-position-center-right uk-position-small uk-hidden-hover" style="width:20px" href="#" uk-slidenav-next uk-slider-item="next"></a>
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

	new Chart(document.getElementById("chartjs-summary"),{
		"type":"doughnut",
		"options": summaryOptions,
		
		"data":{
			"labels": ["Red","Blue","Yellow"],
			"datasets":[
				{
					"label":"Program 1",
					"data":[30,70],
					"backgroundColor":[
						chartColors.inspected,
						chartColors.tobeinspected
					],
					"borderWidth": 1
				},
				{
					"label":"Program 3",
					"data":[30,50],
					"backgroundColor":[
						chartColors.selected,
						chartColors.needed,
					],
					"borderWidth": 1
				},
				{
					"label":"Program 2",
					"data":[100],
					"backgroundColor":[
						chartColors.required
					],
					"borderWidth": 1
				}
			]
		}
	});
</script>

	
	