
		<div uk-grid>
			<div class="uk-width-1-1 uk-padding-remove uk-hidden" style="min-height:200px; margin-top:30px;min-height:300px;">
				<canvas id="chartjs-modal-summary" class="chartjs" style="display: none;"></canvas>
			</div>
			<div class="uk-width-1-1 uk-padding-remove uk-text-center" style="display:block; max-height:740px; overflow: auto; width: 100%; margin-top:30px;">
				<h3>PROGRAMS<br /><small>Project #: {{$project->project_number}} | Audit #: {{$audit->id}}</small></h3>
				<table class="uk-table uk-table-small noline small-padding" >
					<tbody>
						<tr>
							<td></td>
							<td class="uk-text-center"><i class="a-mobile-home iheader"></i></td>
							<td class="uk-text-center" style="padding-bottom:15px;"><i class="a-folder iheader"></i></td>
						</tr>
            @if(array_key_exists('programs',$data))
						@foreach($data['programs'] as $prog)

						<tr style="border-top: 1px solid" @if($prog['building_name'])id="program-selection-{{ $prog['id'] }}-{{ $prog['building_key'] }}" @else id="program-selection-{{ $prog['id'] }}" @endIf>
							<td style="padding-top:10px;">
								<div uk-leader><strong>{{ $prog['name'] }} @if($prog['building_name']) | <a onClick="filterBuilding('building-{{$prog['building_key']}}')">{{$prog['building_name']}}</a> @endif

                </strong></div>
							</td>
							<td class="uk-text-center border-right"></td>
							<td class="uk-text-center"></td>
						</tr>
						<tr @if($prog['building_name'])class="program-selection-{{ $prog['id'] }}-{{ $prog['building_key'] }}" @else class="program-selection-{{ $prog['id'] }}" @endIf>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-required"></i> Required Units</div>
							</td>
							<td class="uk-text-center border-right">{{$prog['required_units']}}</td>
							<td class="uk-text-center">{{$prog['required_units_file']}}</td>
						</tr>
						<tr @if($prog['building_name'])class="program-selection-{{ $prog['id'] }}-{{ $prog['building_key'] }}" @else class="program-selection-{{ $prog['id'] }}" @endIf>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-needed"></i> Needed Units</div>
							</td>
							<td class="uk-text-center border-right">{{$prog['needed_units']}}</td>
							<td class="uk-text-center">{{$prog['needed_units_file']}}</td>
						</tr>
						<tr @if($prog['building_name'])class="program-selection-{{ $prog['id'] }}-{{ $prog['building_key'] }}" @else class="program-selection-{{ $prog['id'] }}" @endIf>
							<td>
								<div class="indented" uk-leader><i class="fas fa-square chart-color-selected"></i> Selected Units</div>
							</td>
							<td class="uk-text-center border-right">{{$prog['selected_units']}}</td>
							<td class="uk-text-center" style="padding-bottom:20px;">{{$prog['selected_units_file']}}</td>
						</tr>
						@endforeach
            @else
            <tr><td colspan="3">It appears your compliance run did not fully run. Please rerun to avoid potential issues.</td></tr>
            @endIf
					</tbody>
				</table>
			</div>
		</div>

	<script>
			var data = JSON.parse('{!! json_encode($data) !!}');
      var chartColors = {
        estimated: '#0099d5',
        needed: '#d31373'
      };
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