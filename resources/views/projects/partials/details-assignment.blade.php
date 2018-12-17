<div class="project-details-info-assignment uk-overflow-auto ok-actionable" uk-grid>
	<div class="uk-width-1-1">	
		<div class=" uk-margin-left uk-margin-right ">
			<hr>
			<span style="font-style: italic;">{{$data['summary']['required_unit_selected']}} ALL REQUIRED UNIT COUNTS FOR EACH PROGRAM HAVE BEEN SELECTED. {{$data['summary']['inspectable_areas_assignment_needed']}} INSPECTABLE AREAS NEED ASSIGNMENT. {{$data['summary']['required_units_selection']}} REQUIRED UNITS NEED TO BE SELECTED. {{$data['summary']['file_audits_needed']}} FILE AUDITS NEED TO BE COMPLETED. {{$data['summary']['physical_audits_needed']}} PHYSICAL AUDITS NEED TO BE COMPLETED. {{$data['summary']['schedule_conflicts']}} SCHEDULE CONFLICTS NEED TO BE RESOLVED.</span>
		</div>
		<div class="project-details-info-assignment-summary uk-margin-top uk-margin-left uk-margin-right" uk-grid>
			<div class="uk-width-1-5">
				<canvas id="chartjs-assignment" class="chartjs" style="display: block;"></canvas>
			</div>
			<div class="uk-width-4-5">
				<h3>
					It will take an <span class="underlined italic">ESTIMATED</span> <i class="a-pencil-2 use-hand-cursor" onclick="editEstimatedHours();" uk-tooltip="title:EDIT ESTIMATED HOURS;"></i>{{$data['summary']['estimated']}} to complete this audit.<br />
					{{$data['summary']['needed']}} Need Assigned
				</h3>
			</div>
			<div id="project-details-assignment-buttons" class="uk-width-1-1 project-details-buttons uk-margin-small">
				@foreach($data['days'] as $day)
				<div class="project-details-button-container">
					<button class="uk-button uk-link {{$day['status']}} active" onclick="assignmentDay({{$data['project']['id']}}, {{$day['id']}}, this);" type="button" ><i class="{{$day['icon']}}"></i> {{$day['date']}}</button>
				</div>
				@endforeach
				<div class="project-details-button-container">
					<button class="uk-button uk-link" onclick="assignmentDay('', this);" type="button"><i class="far fa-calendar-plus"></i> ADD A DAY</button>
				</div>
			</div>
		</div>
		<div class="project-details-info-assignment-schedule uk-position-relative uk-visible-toggle uk-margin-right uk-margin-left">
			<div class="uk-overflow-auto">
				<div class="divTable divTableFixed">
					<div class="divTableBody">
						<div class="divTableRow divTableHeader">
							<div class="divTableCell">&nbsp;</div>
							<div class="divTableCell">&nbsp;</div>
							@foreach($data['auditors'] as $auditor)
							<div class="divTableCell">
								<span uk-tooltip="title:VIEW AUDITOR STATS & DETAILED SCHEDULE;" title="" aria-expanded="false" class="user-badge user-badge-{{$auditor['color']}} no-float uk-link" >{{$auditor['initials']}}</span>
							</div>
							@endforeach
							<div class="divTableCell">
								<i class="a-circle-plus" onclick="addAssignmentAuditor({{$data['project']['id']}});" uk-tooltip="title:CLICK TO ADD AUDITORS;"></i>
							</div>
							<div class="divTableCell">&nbsp;</div>
						</div>
						@foreach($data['projects'] as $project)
						<div class="divTableRow @if(Auth::user()->id == $project['lead']) isLead @endif">
							<div class="divTableCell">
								<strong>{{$project['id']}}</strong><br />
								<strong>{{$project['date']}}</strong>
							</div>
							<div class="divTableCell">
								<i class="a-marker-basic uk-text-muted uk-link" uk-tooltip="title:View On Map;" title="" aria-expanded="false"></i> <strong>{{$project['name']}}</strong><br />
							</div>
							@foreach($project['schedules'] as $schedule)
							<div class="divTableCell {{$schedule['status']}} @if($schedule['is_lead']) isLead @endif">
								@if($schedule['is_lead']) <i class="a-star-3 corner"></i> @endif
								<i class="{{$schedule['icon']}}" uk-tooltip="title:{{$schedule['tooltip']}};"></i>
							</div>
							@endforeach
							<div class="divTableCell">&nbsp;</div>
							<div class="divTableCell">&nbsp;</div>
						</div>
						@endforeach
						
					</div>
				</div>
			</div>
			<hr />
			<span class="italic">NOTE: YOU CAN ONLY APPROVE SCHEDULE CONFLICTS FOR AUDITS THAT YOU ARE THE LEAD. IF YOU ARE NOT THE LEAD, YOU CAN REQUEST APPROVAL FOR THE CONFLICT BY THE LEAD OF THAT AUDIT.</span>

			<style>

				.divTable{
					display: table;
					width: 100%;
					margin-top: 30px;
				}
				.divTableFixed {
					table-layout: auto;
				}
				.divTableRow {
					display: table-row;
					opacity:0.5;
				}
				.divTableRow.isLead, .divTableHeader {
					opacity:1;
				}
				.divTableHeading {
					background-color: #EEE;
					display: table-header-group;
				}
				.divTableCell, .divTableHead {
					border-left: 2px solid #939598;
					border-top: 2px solid #939598;
					display: table-cell;
					padding: 3px 10px;
					width:50px;
				}
				.divTableHeader i {
				    font-size: 30px;
				    display: inline-block;
				    line-height: 31px;
				    vertical-align: middle;
				    color: #939598;
				}
				.divTableHeader .divTableCell {
					text-align:center;
					padding: 13px 0;
    				height: 32px;
				}
				.divTableHeader .divTableCell span.user-badge{
					width: 30px;
				    height: 30px;
				    color: rgba(255,255,255,0.8);
				    line-height: 31px;
				    font-size: 12px;
				    font-weight: normal;
				    display: inline-block;
    				float: none;
    				vertical-align: middle;
    				padding: 0;
    				margin: 0;
				}
				.divTableRow {
					
				}
				.divTableRow:first-child .divTableCell {
				  border-top: 0;
				  border-right: 0;
				}
				.divTableRow .divTableCell:first-child {
				  border-left: 0;
				  width: 80px;
				  vertical-align: top;
    			  padding-top: 13px;
				}
				.divTableRow .divTableCell:nth-child(2) {
				  border-left: 0;
				  width: 240px;
				  vertical-align: top;
    			  padding-top: 13px;
				}
				.divTableRow .divTableCell:last-child {
					width: auto;
				}
				.divTableRow .divTableCell:last-child {
				  border-right: 0;
				}
				.divTableHeading {
					background-color: #EEE;
					display: table-header-group;
					font-weight: bold;
				}
				.divTableFoot {
					background-color: #EEE;
					display: table-footer-group;
					font-weight: bold;
				}
				.divTableBody {
					display: table-row-group;
				}
				.divTableCell:nth-child(n+3) {
					text-align: center;
					padding-top: 14px;
    				padding-bottom: 14px;
    				height: auto;
				}
				.divTableCell:nth-child(n+3) i {
					font-size: 34px;
				}
				.isLead {
				    position: relative;
				}
				.isLead i.corner {
				    position: absolute;
				    top: 4px;
				    right: 4px;
				    font-size: 16px;
				}

				#project-details-info-container .divTable .no-action,
				#project-details-info-container .divTable .action-needed,
				#project-details-info-container .divTable .action-required, 
				#project-details-info-container .divTable .critical,
				#project-details-info-container .divTable .ok-actionable,
				#project-details-info-container .divTable .in-progress {
					border-top: 2px solid #939598; border-left: 2px solid #939598;
					border-bottom: none; border-right: 0px;
					-webkit-animation: none;
					opacity:1;
				}
				#project-details-info-container .divTable .no-action { color:#939598; }
				#project-details-info-container .divTable .action-needed { color:#76338b; }
				#project-details-info-container .divTable .action-required, 
				#project-details-info-container .divTable .critical { color:#da328a; background-color: rgba(218, 50, 138, 0.1); }
				#project-details-info-container .divTable .ok-actionable { color:#56b285; background-color:rgba(86, 178, 133, 0.2); }
				#project-details-info-container .divTable .in-progress { color:#49ade9; }

				#project-details-info-container .divTableRow:last-child .divTableCell {
				  
					border-bottom: 2px solid #939598;
				}
			</style>
			
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

    var mainAssignmentChart = new Chart(document.getElementById("chartjs-assignment"),{
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

<script>
	function addAssignmentAuditor(projectid){
		dynamicModalLoad('projects/'+projectid+'/assignments/addauditor',1,0,1);
	}
	
</script>