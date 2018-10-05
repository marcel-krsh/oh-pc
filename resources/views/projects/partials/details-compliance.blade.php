<div class="project-details-info-compliance uk-overflow-auto ok-actionable" style="" uk-grid>
	
	<div class="uk-width-1-1">		
		<div class=" uk-margin-left uk-margin-right ">
			<hr>
			<span style="font-style: italic;">{{$data['summary']['required_unit_selected']}} ALL REQUIRED UNIT COUNTS FOR EACH PROGRAM HAVE BEEN SELECTED. {{$data['summary']['inspectable_areas_assignment_needed']}} INSPECTABLE AREAS NEED ASSIGNMENT. {{$data['summary']['required_units_selection']}} REQUIRED UNITS NEED TO BE SELECTED. {{$data['summary']['file_audits_needed']}} FILE AUDITS NEED TO BE COMPLETED. {{$data['summary']['physical_audits_needed']}} PHYSICAL AUDITS NEED TO BE COMPLETED. {{$data['summary']['schedule_conflicts']}} SCHEDULE CONFLICTS NEED TO BE RESOLVED.</span>
		</div>
		<div class="project-details-info-compliance-summary uk-margin-top uk-margin-left uk-margin-right uk-grid-match" uk-grid>
			<div class="uk-width-1-5">
					<canvas id="chartjs-4" class="chartjs" style="display: block;"></canvas>
			</div>
			<div class="uk-width-2-5">
				<table class="uk-table uk-table-small noline small-padding">
					<tbody>
						<tr>
							<td><strong>Compliance Requirements (with overlap)</strong></td>
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
			<div class="uk-width-2-5">
				<table class="uk-table uk-table-small noline small-padding">
					<tbody>
						<tr>
							<td><strong>Inspections Required to Meet Compliance</strong></td>
							<td class="uk-text-center"><i class="a-mobile-home iheader"></i></td>
							<td class="uk-text-center"><i class="a-folder iheader"></i></td>
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
	</div>

</div>

<script>
	var chartColors = {
		  required: '#191818',
		  selected: '#0099d5',
		  needed: '#d31373',
		  inspected: '#21a26e',
		  tobeinspected: '#e0e0df'
		};
	new Chart(document.getElementById("chartjs-4"),{
		"type":"doughnut",
		"options": {
			"cutoutPercentage":40,
			"legend" : {
				"display" : false
			},
			"responsive" : true,
			"maintainAspectRatio" : false
		},
		"data":{
			"labels": ["Red","Blue","Yellow"],
			"datasets":[
				{
					"label":"Program 1",
					"data":[10,50,20,15,5],
					"backgroundColor":[
						chartColors.required,
						chartColors.selected,
						chartColors.needed,
						chartColors.inspected,
						chartColors.tobeinspected
					],
					"borderWidth": 3
				},
				{
					"label":"Program 2",
					"data":[100],
					"backgroundColor":[
						chartColors.required,
						chartColors.selected,
						chartColors.needed,
						chartColors.inspected,
						chartColors.tobeinspected
					],
					"borderWidth": 3
				},
				{
					"label":"Program 3",
					"data":[30,50,20],
					"backgroundColor":[
						chartColors.required,
						chartColors.selected,
						chartColors.needed,
						chartColors.inspected,
						chartColors.tobeinspected
					],
					"borderWidth": 3
				}
			]
		}
	});
</script>

	
	