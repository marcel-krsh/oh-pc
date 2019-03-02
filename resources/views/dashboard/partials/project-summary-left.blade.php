
		<div uk-grid>
			<div class="uk-width-1-1 uk-padding-remove " style="min-height:200px; margin-top:30px;">
				<canvas id="chartjs-modal-summary" class="chartjs" style="display: block;"></canvas>
			</div>
			<div class="uk-width-1-1 uk-padding-remove uk-text-center" style="display:block; max-height:300px; overflow: auto; width: 100%">
				<h3>PROGRAMS<br /><small>Project #: {{$project->project_number}} | Audit #: {{$audit->id}}</small></h3>
				<table class="uk-table uk-table-small noline small-padding" >
					<tbody>
						<tr>
							<td></td>
							<td class="uk-text-center"><i class="a-mobile-home iheader"></i></td>
							<td class="uk-text-center"><i class="a-folder iheader"></i></td>
						</tr>
						@foreach($data['programs'] as $prog)
						<tr>
							<td>
								<div uk-leader><strong>{{ $prog['name'] }}</strong></div>
							</td>
							<td class="uk-text-center border-right"></td>
							<td class="uk-text-center"></td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-required"></i> Required Units</div>
							</td>
							<td class="uk-text-center border-right">{{$prog['required_units']}}</td>
							<td class="uk-text-center">{{$prog['required_units_file']}}</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-needed"></i> Needed Units</div>
							</td>
							<td class="uk-text-center border-right">{{$prog['needed_units']}}</td>
							<td class="uk-text-center">{{$prog['needed_units_file']}}</td>
						</tr>
						<tr>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-selected"></i> Selected Units</div>
							</td>
							<td class="uk-text-center border-right">{{$prog['selected_units']}}</td>
							<td class="uk-text-center">{{$prog['selected_units_file']}}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>

	<script>
			var data = JSON.parse('{!! json_encode($data) !!}');

			var summaryCompositeOptions = {
        //Boolean - Whether we should show a stroke on each segment
        segmentShowStroke : false,
        legendPosition : 'bottom',
        "cutoutPercentage":20,
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

      var modalSummaryChart = new Chart(document.getElementById("chartjs-modal-summary"),{
      	"type":"doughnut",
      	"options": summaryCompositeOptions,

      	"data":{
      		"labels": ["Selected","Needed","Inspected", "To Be Inspected"],
      		"datasets":[
      		@foreach($datasets as $dataset)
      		{
      			"label":"{{$dataset['program_name']}}",
      			"data":[{{$dataset['selected']}},{{$dataset['needed']}}, 0,0],
      			"backgroundColor":[
      			chartColors.selected,
      			chartColors.needed,
      			chartColors.inspected,
      			chartColors.tobeinspected
      			],
      			"labels" : [
      			"{{$dataset['program_name']}}" + ' - Selected',
      			"{{$dataset['program_name']}}" + ' - Needed',
      			"{{$dataset['program_name']}}" + ' - Inspected',
      			"{{$dataset['program_name']}}" + ' - To Be Inspected'
      			],
      			"borderWidth": 1
      		},
      		@endforeach
      		]
      	},
      	options: {
      		responsive: true,
      		legend: {
      			display: false,
      		},
      		tooltips: {
      			callbacks: {
      				label: function(tooltipItem, data) {
      					var dataset = data.datasets[tooltipItem.datasetIndex];
      					var index = tooltipItem.index;
      					return dataset.labels[index] + ': ' + dataset.data[index];
      				}
      			}
      		}
      	}
      });
	</script>