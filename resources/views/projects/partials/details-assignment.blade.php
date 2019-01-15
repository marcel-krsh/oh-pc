<div class="project-details-info-assignment uk-overflow-auto ok-actionable" uk-grid>
	<div class="uk-width-1-1">	
		<div class=" uk-margin-left uk-margin-right ">
			<hr>
		</div>
		<div class="project-details-info-assignment-summary uk-margin-left uk-margin-right uk-margin-large-top" uk-grid>
			<div class="uk-width-1-6">
				<canvas id="chartjs-assignment" class="chartjs" style="display: block;"></canvas>
			</div>
			<div class="uk-width-1-2">
				@if($data['summary']['estimated'] != ':' || !$data['summary']['estimated'])
				<h3 class="estHour">
					It will take an <span class="underlined italic">ESTIMATED</span> <i class="a-pencil-2 use-hand-cursor" onclick=" $('.estHour').toggle();" uk-tooltip="title:EDIT ESTIMATED HOURS;"></i><span id="estimated_hours_field">{{$data['summary']['estimated']}}</span> to complete this audit.
					@if($data['summary']['needed'])<br />
					<span id="estimated_hours_needed">{{$data['summary']['needed']}}</span> Need Assigned @endif
				</h3>
				@else
				<h3 class="estHour">
					Enter an estimated number of hours for this audit.
				</h3>
				@endif
				<h3 class="estHour estHourForm" @if($data['summary']['estimated'] != ':' || !$data['summary']['estimated']) style="display:none" @else style="margin-top: 0;" @endif>
					<form id="estimated_hours_form" method="post" class="uk-width-1-1 uk-margin-bottom">
						<div class="uk-grid-small" uk-grid>
							<div class="uk-width-1-4">
								<label class="uk-text-small">Hours</label>
	  							<input class="uk-input" type="number" name="estimated_hours" id="estimated_hours" value="{{$data['summary']['estimated_hours']}}" min="1" max="999"/>
	  						</div>
	  						<div class="uk-width-1-4">
								<label class="uk-text-small">Minutes</label>
								<select id="estimated_minutes" name="estimated_minutes" class="uk-select">
									<option value="00" @if($data['summary']['estimated_minutes'] == '00') selected @endif>00</option>
									<option value="15" @if($data['summary']['estimated_minutes'] == '15') selected @endif>15</option>
									<option value="30" @if($data['summary']['estimated_minutes'] == '30') selected @endif>30</option>
									<option value="45" @if($data['summary']['estimated_minutes'] == '45') selected @endif>45</option>
								</select>
							</div>
	  						<div class="uk-width-1-4">
								<button class="uk-button uk-button-primary" style=" width: 100%;margin-top: 26px;" onclick="saveEstimatedHours(event);">SAVE</button>	
							</div>
							@if(!$data['summary']['estimated'])
	  						<div class="uk-width-1-4">
								<button class="uk-button uk-button-default" style=" width: 100%;margin-top: 26px;" type="cancel" onclick=" $('.estHour').toggle();return false;">CANCEL</button>	
							</div>
							@endif
					</form>
				</h3>
			</div>
			<div class="uk-width-1-3">
				<ul class="uk-list">
					<li>{{$data['summary']['required_unit_selected']}} ALL REQUIRED UNIT COUNTS FOR EACH PROGRAM HAVE BEEN SELECTED.</li>
					<li>{{$data['summary']['inspectable_areas_assignment_needed']}} INSPECTABLE AREAS NEED ASSIGNMENT.</li>
					<li>{{$data['summary']['required_units_selection']}} REQUIRED UNITS NEED TO BE SELECTED.</li>
					<li>{{$data['summary']['file_audits_needed']}} FILE AUDITS NEED TO BE COMPLETED.</li>
					<li>{{$data['summary']['physical_audits_needed']}} PHYSICAL AUDITS NEED TO BE COMPLETED.</li>
					<li>{{$data['summary']['schedule_conflicts']}} SCHEDULE CONFLICTS NEED TO BE RESOLVED.</li>
				</ul>	
			</div>

			<div id="project-details-assignment-buttons" class="uk-width-1-1 uk-margin-large-top project-details-buttons ">
				<div class="project-details-button-container flatpickr" id="addadaybutton">
					<input type="text" id="addaday" name="addaday" class="flatpickr-input"  data-input style="display:none">
					<button class="uk-button uk-link addadaybutton" type="button" data-toggle><i class="far fa-calendar-plus"></i> ADD A DAY</button>
				</div>
				
			</div>
		</div>

		<div class="project-details-info-assignment-schedule uk-position-relative uk-visible-toggle uk-margin-right uk-margin-left">
			<div class="uk-overflow-auto">
				@foreach($project->selected_audit()->days as $day)

				<div class="divTable divTableFixed">
					<div class="divTableBody">
						
						<div class="divTableRow divTableHeader">
							<div class="divTableCell">
								<h3 style="margin-top:5px;text-align: left;"> {{formatDate($day->date, 'l F d, Y')}} <small><i class="a-trash-4 use-hand-cursor" onclick="deleteDay({{$day->id}});"></i></small></h3>
							</div>
							<div class="divTableCell">
								<span uk-tooltip="title:VIEW STATS & DETAILED SCHEDULE FOR {{strtoupper($project->selected_audit()->lead_auditor->full_name())}};" title="" aria-expanded="false" class="user-badge user-badge-{{$project->selected_audit()->lead_auditor->badge_color}} no-float uk-link" >{{$project->selected_audit()->lead_auditor->initials()}}</span>
							</div>
							@foreach($project->selected_audit()->auditors as $auditor)
							@if($auditor->user_id != $project->selected_audit()->lead_auditor->id)
							<div class="divTableCell">
								<span uk-tooltip="title:VIEW STATS & DETAILED SCHEDULE FOR {{strtoupper($auditor->user->full_name())}};" title="" aria-expanded="false" class="user-badge user-badge-{{$auditor->user->badge_color}} no-float uk-link" >{{$auditor->user->initials()}}</span>
							</div>
							@endif
							@endforeach

							<div class="divTableCell">
								<i class="a-circle-plus" onclick="addAssignmentAuditor({{$data['project']['id']}});" uk-tooltip="title:CLICK TO ADD AUDITORS;"></i>
							</div>
							<div class="divTableCell">&nbsp;</div>
						</div>

						@foreach($data['audits'] as $audit)
						<div class="divTableRow @if(Auth::user()->id == $audit['lead']) isLead @endif">
							<div class="divTableCell">
								<div uk-grid>
									<div class="uk-width-1-3">
										<strong>{{$audit['ref']}}</strong><br />
										<strong>{{$audit['date']}}</strong>
									</div>
									<div class="uk-width-2-3">
										<i class="a-marker-basic uk-text-muted uk-link" uk-tooltip="title:View On Map;" title="" aria-expanded="false"></i> <strong>{{$audit['name']}}</strong>
									</div>
								</div>
							</div>
							@foreach($audit['schedules'] as $schedule)
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

				@endforeach
			</div>
		</div>


		<div class="project-details-info-assignment-schedule uk-position-relative uk-visible-toggle uk-margin-right uk-margin-left" hidden>
			<div class="uk-overflow-auto">
				<div class="divTable divTableFixed">
					<div class="divTableBody">
						<div class="divTableRow divTableHeader">
							<div class="divTableCell">&nbsp;</div>
							
							<div class="divTableCell">
								<i class="a-circle-plus" onclick="addAssignmentAuditor({{$data['project']['id']}});" uk-tooltip="title:CLICK TO ADD AUDITORS;"></i>
							</div>
							<div class="divTableCell">&nbsp;</div>
						</div>
						@foreach($data['audits'] as $audit)
						<div class="divTableRow @if(Auth::user()->id == $audit['lead']) isLead @endif">
							<div class="divTableCell">
								<strong>{{$audit['id']}}</strong><br />
								<strong>{{$audit['date']}}</strong>
							
								<i class="a-marker-basic uk-text-muted uk-link" uk-tooltip="title:View On Map;" title="" aria-expanded="false"></i> <strong>{{$audit['name']}}</strong><br />
							</div>
							@foreach($audit['schedules'] as $schedule)
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
			
		</div>
	</div>
</div>
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
		/*padding: 13px 0;*/
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
	.divTableRow .divTableCell h3 i {
		font-size: 14px;
		margin-top: -5px;
		float: right;
	}
	.divTableRow:first-child .divTableCell {
	  border-top: 0;
	  border-right: 0;
	}
	.divTableRow .divTableCell:first-child {
	  border-left: 0;
	  width: 300px;
	  vertical-align: top;
	  padding-top: 13px;
	}
	.divTableRow .divTableCell:nth-child(1) {
	  border-left: 0;
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
	.divTableCell:nth-child(n+2) {
		text-align: center;
		padding-top: 14px;
		padding-bottom: 14px;
		height: auto;
		width:50px;
	}
	.divTableCell:nth-child(n+2) i {
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
                    return label + ': ' + addCommas(datasetLabel) ;
                }
            }
        }


    }
    function addCommas(nStr)
    {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? ':' + x[1] : '';
        if(x2.length == 2) x2 = x2+'0';
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
			"labels": ["Needed","Day 1","Day 2","Day 3","Day 5","Day 6"],
			"datasets":[
				{
					"label":"Program 1",
					//"data":{{$data['summary']['chart_data']}},
					"data":[2,3,4,5,6],
					"backgroundColor":[
						chartColors.needed,
						chartColors.estimated,
						chartColors.estimated,
						chartColors.estimated,
						chartColors.estimated,
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

	function deleteDay(id){

		UIkit.modal.confirm("Are you sure you want to delete this date? It will also delete all scheduled time for that day.").then(function(){
            $.post("/audit/{{$data['project']['audit_id']}}/scheduling/days/"+id+"/delete", {
                    '_token' : '{{ csrf_token() }}'
                    }, function(data) {
                        if(data.data!=1){ 
                            UIkit.modal.alert(data.data,{stack: true});
                        } else {
                            UIkit.notification('<span uk-icon="icon: check"></span> Day Deleted', {pos:'top-right', timeout:1000, status:'success'});   
                            $('#project-details-button-2').trigger( 'click' );       
                        }
            });
        });
	}

	function saveEstimatedHours(e){
		e.preventDefault();
		var form = $('#estimated_hours_form');

		$.post("/audit/{{$data['project']['audit_id']}}/estimated/save", {
            'inputs' : form.serialize(),
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            if(data.status!=1){ 
                UIkit.modal.alert(data.message,{stack: true});
            } else {
                UIkit.notification('<span uk-icon="icon: check"></span> Estimated Hours Saved', {pos:'top-right', timeout:1000, status:'success'});
                $('#estimated_hours_field').html(data.hours);
                $('#estimated_hours_needed').html(data.needed);
                $('.estHour').toggle();

                $('#project-details-button-2').trigger( 'click' );
            }
        } );
	}

	$( document ).ready(function() {
	});

	flatpickr.defaultConfig.animate = window.navigator.userAgent.indexOf('MSIE') === -1;

	flatpickr("#addadaybutton", {
	    minDate: "today",
	    altFormat: "F j, Y",
	    dateFormat: "F j, Y",
	     wrap: true,
	     positionElement: $('.addadaybutton')[0],
	    onChange: function(selectedDates, dateStr, instance) {
	        console.log("yo "+selectedDates+" ---- "+dateStr);

	        $.post("/audit/{{$data['project']['audit_id']}}/scheduling/addaday", {
	            'date' : dateStr,
	            '_token' : '{{ csrf_token() }}'
	        }, function(data) {
		        $('#project-details-button-2').trigger( 'click' );
			});
	    }
	});

</script>