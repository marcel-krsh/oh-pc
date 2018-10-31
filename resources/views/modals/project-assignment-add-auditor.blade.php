<div id="modal-project-assignment-add-auditor" class="uk-padding-remove uk-margin-bottom uk-overflow-auto">
	<h2>Please Add Auditors for the Selected Date: <small class="use-hand-cursor" style="padding-left: 30px; font-size: 0.7em;"  uk-tooltip="title:CLICK TO CHANGE SELECTED DATE;" onclick=""><i class="a-calendar-pencil" style=" font-size: 18px; vertical-align: text-top;"></i> {{$data['summary']['date']}}</small></h2>

	<div id="project-assignment-add-auditor-table">
		<div id="project-assignment-add-auditor-table-header" uk-grid>
			<div class="uk-width-1-2 uk-padding-remove">
				<div uk-grid>
					<div class="uk-width-1-6 uk-padding-remove">
						<div uk-grid>
							<div class="uk-width-1-1 uk-padding-remove">
				            </div>
						</div>
					</div>
					<div class="uk-width-5-6 uk-padding-remove">
						<div uk-grid>
							<div class="uk-width-1-2 uk-padding-remove">
								<div uk-grid>
									<div class="uk-width-1-1 uk-padding-remove-left">
										STATS AUDITOR NAME
									</div>
									<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-left uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" uk-tooltip="title:SORT BY NAME;" aria-expanded="false">
										<a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'sort-by-name',1);"></a>
									</span> 
									<div class="uk-dropdown" aria-expanded="false"></div>
								</div>
							</div>
							<div class="uk-width-1-2 uk-padding-remove">
								<div uk-grid>
					            	<div class="uk-width-1-1">
										TIME AVAILABLE THIS DAY
									</div>
									<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column"  uk-tooltip="title:SORT BY AVAILABILITY;" aria-expanded="false">
										<a id="" class="sort-asc" onclick="loadListTab(1,null,null,'sort-by-availability',1);"></a>
									</span> 
									<div class="uk-dropdown" aria-expanded="false"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="uk-width-1-2 uk-padding-remove">
				<div uk-grid>
					<div class="uk-width-1-4 uk-padding-remove">
						<div uk-grid>
			            	<div class="uk-width-1-1 uk-text-center">
								OPEN
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column"  uk-tooltip="title:SORT BY OPEN TIME;" aria-expanded="false">
								<a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'sort-by-open',1);"></a>
							</span> 
							<div class="uk-dropdown" aria-expanded="false"></div>
						</div>
					</div>
					<div class="uk-width-1-4 uk-padding-remove">
						<div uk-grid>
			            	<div class="uk-width-1-1 uk-text-center">
								STARTING
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column"  uk-tooltip="title:SORT BY START TIME;" aria-expanded="false">
								<a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'sort-by-starting',1);"></a>
							</span> 
							<div class="uk-dropdown" aria-expanded="false"></div>
						</div>
					</div>
					<div class="uk-width-1-2 uk-padding-remove">
						<div uk-grid>
			            	<div class="uk-width-1-1 uk-text-center">
								DISTANCE TO PROJECT
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column"  uk-tooltip="title:SORT BY DISTANCE;" aria-expanded="false">
								<a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'sort-by-distance',1);"></a>
							</span> 
							<div class="uk-dropdown" aria-expanded="false"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="uk-overflow-auto">
			@foreach($data['auditors'] as $auditor)
			<div class="project-assignment-add-auditor-row @if($auditor['status'] == 'action-required') {{$auditor['status']}} @endif" uk-grid>
				<div class="uk-width-1-2 uk-padding-remove">
					<div uk-grid>
						<div class="uk-width-1-6 uk-padding-remove uk-text-center">
							<div uk-grid>
								<div class="uk-width-1-1 uk-padding-remove {{$auditor['status']}}">
									<i class="{{$auditor['icon']}} large use-hand-cursor" onclick=""  uk-tooltip="title:{{$auditor['icon_tooltip']}};"></i>
					            </div>
							</div>
						</div>
						<div class="uk-width-5-6 uk-padding-remove">
							<div uk-grid>
								<div class="uk-width-1-2 uk-padding-remove">
									<div class="leaders uk-width-1-1">
					    				<div>
					    					<span><i class="a-person-chart-bar large use-hand-cursor" style="padding-right: 8px;" onclick="" uk-tooltip="title:CLICK TO VIEW AUDITOR'S SCHEDULE & STATS;"></i> {{$auditor['name']}}</span>
					    				</div>
					    			</div>
								</div>
								<div class="uk-width-1-2">
									{{$auditor['availability']}}
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="uk-width-1-2 uk-padding-remove">
					<div uk-grid>
						<div class="uk-width-1-4 uk-text-center">
							<span uk-tooltip="title:{{$auditor['open_tooltip']}};">{{$auditor['open']}}</span>
						</div>
						<div class="uk-width-1-4 uk-text-center">
							<span uk-tooltip="title:{{$auditor['starting_tooltip']}};">{{$auditor['starting']}}</span>
						</div>
						<div class="uk-width-1-2 uk-text-center">
							{{$auditor['distance_time']}} | {{$auditor['distance']}} Miles | <i class="{{$auditor['distance_icon']}}" uk-tooltip="title:{{$auditor['distance_tooltip']}};"></i>
						</div>
					</div>
				</div>
			</div>
			@endforeach
		</div>
	</div>

	<div class="project-details-info-assignment-summary uk-margin-top uk-flex-middle" uk-grid>
		<div class="uk-width-1-6">
			<canvas id="chartjs-assignment-auditor" class="chartjs" style="display: block;"></canvas>
		</div>
		<div class="uk-width-1-2 uk-padding-remove">
			<h4>
				It will take an <span class="underlined italic">ESTIMATED</span> <i class="a-pencil-2 use-hand-cursor" onclick="editEstimatedHours();" uk-tooltip="title:EDIT ESTIMATED HOURS;"></i>{{$data['summary']['estimated']}} to complete this audit.<br />
				{{$data['summary']['needed']}} Need Assigned
			</h4>
		</div>
		<div class="uk-width-1-3 uk-padding-remove">
			<button class="uk-button uk-button-border uk-link" onclick="" type="button"><i class="far fa-calendar-check"></i> DONE SCHEDULING</button>
		</div>
		<div class="uk-width-2-3" uk-padding-remove>
			<div class="uk-card uk-card-info uk-card-body">
				<div class="uk-grid-small uk-flex-top" uk-grid>
		            <div class="uk-width-1-6">
		                <i class="a-info-circle"></i>
		            </div>
		            <div class="uk-width-5-6">
			            <p>Clicking the <i class="a-circle-plus"></i> icon will add the auditor to your audit and automatically assign either all their open hours, or the number of hours needed (whichever is less) to your audit.</p> 
			            <p>Pink/Grayed out lines indicate auditors who have assignments that same day, and will require approval by the lead of the other assignments if selected.</p> 
			            <p>"Time Available This Day" is the time period the auditor stated they are available to be scheduled for audits on the selected day.</p>
			            <p>"Open" is the number of hours that the auditor has left on the selected day that can be scheduled for this audit.</p> 
			            <p>"Starting" is the approximate time they would be available to start their travel to this audit.</p>
			            <p>"Distance to Project" shows the time, miles, and an icon designating if they will be traveling from their default starting point (<i class="a-home-marker"></i>) or from another audit(<i class="a-marker-basic"></i>).</p>
		            </div>
		        </div>
	        </div>
		</div>
	</div>
</div>
<script>
	var chartColors = {
		  estimated: '#0099d5',
		  needed: '#d31373'
		};
	Chart.defaults.global.legend.display = false;
    Chart.defaults.global.tooltips.enabled = true;

    // THIS SCRIPT MUST BE UPDATED WITH NEW VALUES AFTER A NEW FUNDING SUBMISSION HAS BEEN MADE  - to make this simple - this tab is reloaded on form submission of new payment/ payment edits //
    var assignmentOptions = {
        //Boolean - Whether we should show a stroke on each segment
        segmentShowStroke : false,
        legendPosition : 'bottom',

        rotation: (1.5 * Math.PI),

        "cutoutPercentage":70,
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
                    return label + ': ' + addCommas(datasetLabel) + ':00' ;
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

    var mainAssignmentChart = new Chart(document.getElementById("chartjs-assignment-auditor"),{
		"type":"doughnut",
		"options": assignmentOptions,
		
		"data":{
			"labels": ["Needed","Estimated"],
			"datasets":[
				{
					"label":"Program 1",
					"data":[27,107],
					"backgroundColor":[
						chartColors.needed,
						chartColors.estimated
					],
					"borderWidth": 1
				}
			]
		}
	});
</script>