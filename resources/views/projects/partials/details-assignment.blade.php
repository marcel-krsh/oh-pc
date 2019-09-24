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
					It will take an <span class=" italic">ESTIMATED </span> @if(Auth::user()->id == $audit->lead_auditor->id || Auth::user()->can('access_manager')) <i class="a-pencil-2 use-hand-cursor"  onclick=" $('.estHour').toggle();" uk-tooltip="title:EDIT ESTIMATED HOURS;"></i>@endIf <span id="estimated_hours_field">{{$data['summary']['estimated']}}</span> to complete this audit.
					@if($data['summary']['needed'])<br />
					<span id="estimated_hours_needed">{{$data['summary']['needed']}}</span> Need Assigned @endif
				</h3>
				@elseif(($audit->lead_auditor && Auth::user()->id == $audit->lead_auditor->id) || Auth::user()->can('access_manager'))
				<h3 class="estHour">
					Enter an estimated number of hours for this audit.
				</h3>
				@else
				<h3 class="estHour">
					@if($audit->lead_auditor )
					Sorry, no assignments have been made available yet. {{$audit->lead_auditor->full_name()}} needs to enter the estimated time for this audit, and then assign auditors to each day of the inspection.
					@else
					Sorry, no assignments have been made and no lead auditor has been named.
					@endif
				</h3>

				@endif

				@if(($audit->lead_auditor && Auth::user()->id == $audit->lead_auditor->id) || Auth::user()->can('access_manager'))
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
				@endIf
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

			@if(($audit->lead_auditor && Auth::user()->id == $audit->lead_auditor->id) || Auth::user()->can('access_manager'))
				<div id="project-details-assignment-buttons" class="uk-width-1-1 uk-margin-large-top project-details-buttons ">
					<div class="project-details-button-container" id="addadaybutton">
						<!-- <input type="text" id="addaday" name="addaday" class="flatpickr-input"  data-input style="display:none">
						<button class="uk-button uk-link addadaybutton" type="button" data-toggle><i class="far fa-calendar-plus"></i> ADD A DAY</button> -->
						<span class="uk-form-icon" uk-icon="icon: calendar" style="margin-left: 10px;" ></span>
						<input type="text" id="addaday" name="addaday" value="" class="uk-button uk-link addadaybutton flatpickr flatpickr-input active" placeholder="ADD A DAY" />
					</div>

				</div>
				@endIf
		</div>

		<div class="project-details-info-assignment-schedule uk-position-relative uk-visible-toggle uk-margin-right uk-margin-left">
			<div class="uk-overflow-auto">
				@foreach($audit->days as $day)

				<div class="divTable divTableFixed">
					<div class="divTableBody">

						<div class="divTableRow divTableHeader">
							<div class="divTableCell">

							</div>
							<div class="divTableCell">
								<span uk-tooltip="title:VIEW STATS & DETAILED SCHEDULE FOR {{strtoupper($audit->lead_auditor->full_name())}} {{$audit->lead}};" title="" aria-expanded="false" class="user-badge user-badge-{{$audit->lead_auditor->badge_color}} no-float uk-link" >{{$audit->lead_auditor->initials()}}</span>
							</div>
							@foreach($audit->auditors as $auditor)
							@if($auditor->user_id != $audit->lead_auditor->id && Auth::user()->can('access_manager'))
							<div class="divTableCell">
								<span @if(Auth::user()->id == $audit->lead_auditor->id || Auth::user()->can('access_manager'))
								 uk-tooltip="title:VIEW STATS & DETAILED SCHEDULE FOR {{strtoupper($auditor->user->full_name())}} {{$auditor->user_id}};" title="" onclick="removeAuditorFromAudit({{$auditor->user_id}});" aria-expanded="false" class="user-badge user-badge-{{$auditor->user->badge_color}} no-float uk-link" @else uk-tooltip title="{{strtoupper($auditor->user->full_name())}}" class="user-badge user-badge-{{$auditor->user->badge_color}} no-float uk-link" @endIf >{{$auditor->user->initials()}}</span>
							</div>
							@endif
							@endforeach

							<div class="divTableCell">
								@if(Auth::user()->id == $audit->lead_auditor->id || Auth::user()->can('access_manager'))
								<i class="a-circle-plus use-hand-cursor" style="font-size:34px;" onclick="addAssignmentAuditor({{$day->id}});" uk-tooltip="title:CLICK TO ADD AUDITORS;"></i>
								@endIf
							</div>
							<div class="divTableCell">&nbsp;</div>
						</div>

						<div class="divTableRow isLead">
							<div class="divTableCell">
								<h3 style="margin-top:5px;text-align: left;"> {{formatDate($day->date, 'l F d, Y')}} <small>@if(Auth::user()->id == $audit->lead_auditor->id || Auth::user()->can('access_manager')) <i class="a-trash-4 use-hand-cursor" onclick="deleteDay({{$day->id}});"></i>@endIf</small></h3>
							</div>
							@foreach($auditors_key as $auditor_id)
							<div class="divTableCell isLead" style="padding-top:14px">
								@if($auditor_id == $audit->lead) <i class="a-star-3 corner"></i> @endif
								<i class="a-circle" style="font-size:34px;"></i>
							</div>
							@endforeach
							<div class="divTableCell">&nbsp;</div>
							<div class="divTableCell">&nbsp;</div>
						</div>

						<div class="divTableRow isLead ">
							<div class="divTableCell" style="padding:0 10px;">
								<div class="grid-schedule">
									<div class="grid-schedule-sidebar">
										<div>06:00 AM</div><div></div><div></div><div></div>
										<div></div><div></div><div></div><div></div>
										<div>08:00 AM</div><div></div><div></div><div></div>
										<div></div><div></div><div></div><div></div>
										<div>10:00 AM</div><div></div><div></div><div></div>
										<div></div><div></div><div></div><div></div>
										<div>12:00 PM</div><div></div><div></div><div></div>
										<div></div><div></div><div></div><div></div>
										<div>02:00 PM</div><div></div><div></div><div></div>
										<div></div><div></div><div></div><div></div>
										<div>04:00 PM</div><div></div><div></div><div></div>
										<div></div><div></div><div></div><div></div>
										<div>06:00 PM</div><div></div><div></div><div></div>
										<div></div><div></div><div></div><div></div>
										<div>08:00 PM</div><div></div><div></div><div></div>
									</div>
								</div>
							</div>
							@foreach($daily_schedules[$day->id] as $daily_schedule)
							<div class="divTableCell isLead grid-schedule">

								<div class="auditor-calendar-content grid-schedule-content">
									<div class="day">
										@if($daily_schedule['content']['no_availability'])
										<div class="event" data-start="1" data-span="60">
											<i class="a-circle-cross" uk-tooltip="title:No availability"></i>
										</div>
										@else
										@if($daily_schedule['content']['before_time_start'] != 1 && $daily_schedule['content']['before_time_span'] != 0)
										<div class="event beforetime" data-start="{{$daily_schedule['content']['before_time_start']}}" data-span="{{$daily_schedule['content']['before_time_span']}}"></div>
										@endif
										@foreach($daily_schedule['content']['events'] as $event)
										<div class="event {{$event['status']}} {{$event['class']}}" data-start="{{$event['start']}}" data-span="{{$event['span']}}" uk-tooltip="title:{{$event['tooltip']}}" @if(Auth::user()->id == $audit->lead_auditor->id || Auth::user()->can('access_manager')) @if($event['modal_type'] != '' && $event['modal_type'] != "removeschedule") uk-toggle="target: #eventmodal-{{$event['id']}}" @endif @if($event['modal_type'] == "removeschedule") onclick="removeSchedule('{{$event['id']}}');" @endif @endif>
											@if($event['icon'] != '')
												@if($event['span'] < 3)
												<i class="{{$event['icon']}}" style="font-size:10px;"></i>
												@else
												<i class="{{$event['icon']}}"></i>
												@endif
											@endif
											@if((Auth::user()->id == $audit->lead || Auth::user()->can('access_manager')) && $event['icon'] != '' && $event['modal_type'] != '')
												@if($event['modal_type'] == 'addschedule')
													@if(Auth::user()->id == $audit->lead_auditor->id || Auth::user()->can('access_manager'))
													<div id="eventmodal-{{$event['id']}}" uk-modal>
													    <div class="uk-modal-dialog uk-modal-body">
													        <a class="uk-modal-close-default" uk-close></a>
													        <h2 class="uk-modal-title">Schedule</h2>
													        <div class="">
														    	<ul class="uk-list no-hover uk-form-horizontal">
										                        	<li onclick="">
																        <label class="uk-form-label" style="margin-top: 10px;"><div style="display:inline-block;width:30px;float:left;"><i class="a-marker-directions"></i></div> TRAVEL ADJUSTMENT:</label>
																        <div class="uk-form-controls">
																            <select class="uk-select travel-select" id="travel-{{$event['id']}}" data-eventid="{{$event['id']}}" data-start="{{$event['start']}}" data-span="{{$event['span']}}">
																            @php
																            for ($i = 0; $i < $event['span']; $i++){
																            	$hours = sprintf("%02d", intval($i * 15 / 60));
																				$minutes = sprintf("%02d",$i * 15 - ($hours * 60));
																				if($event['travel_time'] == $i){
																					echo "<option value='".$i."' selected>".$hours.":".$minutes."</option>";
																				}else{
																					echo "<option value='".$i."'>".$hours.":".$minutes."</option>";
																				}

																            }
																            @endphp
																            </select>
																        </div>
										                        	</li>
										                        	<li onclick="">
																        <label class="uk-form-label" style="margin-top: 10px;">
																        	<div style="display:inline-block;width:30px;float:left;"><i class="a-clock-3"></i></div> ARRIVAL TIME:</label>
																        <div class="uk-form-controls">
																            <select class="uk-select start-select" id="start-{{$event['id']}}" data-eventid="{{$event['id']}}" data-start="{{$event['start']}}" data-span="{{$event['span']}}">
																            @php
																            for ($i = $event['start']; $i <= $event['start']; $i++){
																            	$hours = sprintf("%02d", intval(($i-1) * 15 / 60) + 6);
																				$minutes = sprintf("%02d",($i-1) * 15 - (($hours - 6) * 60));
																				if((int)$hours > 12){
																					$ushours = $hours - 12;
																					echo "<option value='".$i."'>".$ushours.":".$minutes." PM</option>";
																				}else{
																					echo "<option value='".$i."'>".$hours.":".$minutes." AM</option>";
																				}
																            }
																            @endphp
																            </select>
																        </div>
										                        	</li>
										                        	<li onclick="">
																        <label class="uk-form-label" style="margin-top: 10px;"><div style="display:inline-block;width:30px;float:left;"><i class="a-clock-arrow-right"></i></div> DURATION:</label>
																        <div class="uk-form-controls">
																            <select class="uk-select duration-select" id="duration-{{$event['id']}}" data-eventid="{{$event['id']}}" data-start="{{$event['start']}}" data-span="{{$event['span']}}">
																            @php
																            for ($i = $event['span']; $i > 0; $i--){
																            	$hours = sprintf("%02d", intval($i * 15 / 60));
																				$minutes = sprintf("%02d",$i * 15 - ($hours * 60));
																                echo "<option value='".$i."'>".$hours.":".$minutes."</option>";
																            }
																            @endphp
																            </select>
																        </div>
										                        	</li>
										                        	<li onclick="">
																        <div class="uk-form-controls">
																        	<button class="uk-button uk-button-primary" onclick="scheduleTime('{{$event['id']}}', '{{$day->id}}', '{{$event['auditor_id']}}');$(this).attr('disabled', true);">Schedule</button>
																        </div>
																    </li>
											                    </ul>
															</div>
													    </div>
													</div>
													@endif
												@endif
											@endif
										</div>
										@endforeach
										<div class="event aftertime" data-start="{{$daily_schedule['content']['after_time_start']}}" data-span="{{$daily_schedule['content']['after_time_span']}}"></div>
										@endif
									</div>
								</div>

							</div>
							@endforeach
							<div class="divTableCell">&nbsp;</div>
							<div class="divTableCell">&nbsp;</div>
						</div>

					</div>
				</div>

				@endforeach
			</div>
			<hr />
			@if($audit->lead_auditor)
			<span class="italic">NOTE: Only lead auditors and managers and above can change a shedule. If you have any questions about your schedule please contact {{$audit->lead_auditor->full_name()}}.</span>
			@else
			<span class="italic">NOTE: Only lead auditors can change a shedule.</span>
			@endif
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
		margin-top: 5px;
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
		padding-top: 0px;
		padding-bottom: 0px;
		height: auto;
		width:90px;
		padding-left: 0px;
		padding-right: 0px;
		vertical-align: top;
	}
	.divTableCell:nth-child(n+2) i {
		font-size: 20px;
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

	.divTableCell .event.schedule {
	    background-color: #a7a9ac;
	    color: #eee;
	    border-top: 2px solid #fff;
	}

	.divTableCell .event.schedule.thisaudit {
	    background-color: #00aeef;
	    color: #fff;
	}
	.divTableCell .event.travel.thisaudit {
	    background-color: #75c1de;
	    color: #fff;
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
			"labels": {!!$chart_data['labels']!!},
			"datasets":[
				{
					"label":"Program 1",
					"data":{{$chart_data['data']}},
					"backgroundColor":{{$chart_data['backgroundColor']}},
					"borderWidth": 1
				}
			]
		}
	});
</script>

<script>
	@if(($audit->lead_auditor && Auth::user()->id == $audit->lead_auditor->id) || Auth::user()->can('access_manager'))
	function scheduleTime(eventid, dayid, auditorid){
		var travel = parseInt($('#travel-'+eventid).val(), 10);
		var start = parseInt($('#start-'+eventid).val(), 10);
		var duration = parseInt($('#duration-'+eventid).val(), 10);
		// console.log(eventid +" - "+ auditorid+"-"+travel+"-"+start+"-"+duration);

		$.post("audit/{{$data['project']['audit_id']}}/scheduling/days/"+dayid+"/auditors/"+auditorid, {
                'travel' : travel,
                'start' : start,
                'duration' : duration,
                '_token' : '{{ csrf_token() }}'
                }, function(data) {
                    if(data!=1){
                        UIkit.modal.alert(data,{stack: true});
                    } else {
                    	UIkit.modal('#eventmodal-'+eventid).hide();
                        UIkit.notification('<span uk-icon="icon: check"></span> Auditor Scheduled', {pos:'top-right', timeout:1000, status:'success'});
                        $('#project-details-button-2').trigger( 'click' );
                    }
        });
	}

	function pad(num, size) {
	    var s = num+"";
	    while (s.length < size) s = "0" + s;
	    return s;
	}

	$( document ).ready(function() {
		$( ".travel-select" ).change(function() {
			var eventid = $(this).data('eventid');
			var eventstart = $(this).data('start');
			var eventspan = $(this).data('span');

			var travel = parseInt($(this).val(), 10);
			var start = parseInt($('#start-'+eventid).val(), 10);
			var duration = parseInt($('#duration-'+eventid).val(), 10);
			console.log(eventid+"-"+eventstart+"-"+eventspan+"-"+travel+"-"+start+"-"+duration);

			var hours = '';
			var minutes = '';
			var time = '';
			var start_slot = '';

			// check that time is later than end of travel time
			if(start < eventstart + travel){
				start = eventstart + travel;
			}

			// how many slots are available with that time?
			var slots = eventspan - travel;

			// reload start
			$('#start-'+eventid).empty();

			for (i = 0; i < slots ; i++){
				start_slot = eventstart + travel + i;
            	hours = Math.floor((start_slot-1) * 15 / 60) + 6;
            	hours = pad(hours, 2);
				minutes = (start_slot-1) * 15 - ((hours - 6) * 60);
            	minutes = pad(minutes, 2);

            	if(hours > 12){
            		hours = hours - 12;
            		time = hours+':'+minutes+' PM';
            	} else {
            		time = hours+':'+minutes+' AM';
            	}

				if(start_slot == start){
					$('#start-'+eventid).append($('<option selected value="'+start_slot+'">'+time+'</option>'));
				}else{
					$('#start-'+eventid).append($('<option/>', { value: start_slot, text: time }));
				}

            }

            slots = eventstart + eventspan - start;

			// reload duration
			$('#duration-'+eventid).empty();

			for (i = slots; i > 0; i--){

				hours = Math.floor(i * 15 / 60);
            	hours = pad(hours, 2);
				minutes = i * 15 - (hours * 60);
            	minutes = pad(minutes, 2);
				time = hours+':'+minutes;
                if(i == duration){
					$('#duration-'+eventid).append($('<option selected value="'+i+'">'+time+'</option>'));
				}else{
					$('#duration-'+eventid).append($('<option/>', { value: i, text: time }));
				}
  			 }

		});

		$( ".start-select" ).change(function() {
			var eventid = $(this).data('eventid');
			var eventstart = $(this).data('start');
			var eventspan = $(this).data('span');

			var start = parseInt($(this).val(), 10);
			var duration = parseInt($('#duration-'+eventid).val(), 10);
			var travel = parseInt($('#travel-'+eventid).val(), 10);

			var hours = '';
			var minutes = '';
			var time = '';
			var start_slot = '';

			var slots = eventstart + eventspan - start;

			// reload duration
			$('#duration-'+eventid).empty();
			for (i = slots; i > 0; i--){
				hours = Math.floor(i * 15 / 60);
            	hours = pad(hours, 2);
				minutes = i * 15 - (hours * 60);
            	minutes = pad(minutes, 2);
				time = hours+':'+minutes;
                if(i == duration){
					$('#duration-'+eventid).append($('<option selected value="'+i+'">'+time+'</option>'));
				}else{
					$('#duration-'+eventid).append($('<option/>', { value: i, text: time }));
				}
  			 }
		});

		$( ".duration-select" ).change(function() {
			var eventid = $(this).data('eventid');
			var eventstart = $(this).data('start');
			var eventspan = $(this).data('span');

			var duration = parseInt($(this).val(), 10);
			var start = parseInt($('#start-'+eventid).val(), 10);
			var travel = parseInt($('#travel-'+eventid).val(), 10);

			var hours = '';
			var minutes = '';
			var time = '';
			var start_slot = '';

			// check that time is later than end of travel time
			if(start < eventstart + travel){
				start = eventstart + travel;
			}

			// check that time is compatible with new duration
			if(start > eventstart + eventspan - duration){
				start = eventstart + eventspan - duration;
			}

			// how many slots are available with that duration?
			var slots = eventspan - travel - duration + 1;

			// reload start
			$('#start-'+eventid).empty();
			for (i = 0; i < slots; i++){
				start_slot =eventstart + travel + i;
            	hours = Math.floor((start_slot-1) * 15 / 60) + 6;
            	hours = pad(hours, 2);
				minutes = (start_slot-1) * 15 - ((hours - 6) * 60);
            	minutes = pad(minutes, 2);

            	if(hours > 12){
            		hours = hours - 12;
            		time = hours+':'+minutes+' PM';
            	} else {
            		time = hours+':'+minutes+' AM';
            	}

				if(start_slot == start){
					$('#start-'+eventid).append($('<option selected value="'+start_slot+'">'+time+'</option>'));
				}else{
					$('#start-'+eventid).append($('<option/>', { value: start_slot, text: time }));
				}

            }
		});

		// make sure the numbers make sense with initial travel time
		$('.travel-select').trigger('change');
	});

	function addAssignmentAuditor(dayid, auditorid=''){
		dynamicModalLoad('audit/{{$data['project']['audit_id']}}/scheduling/days/'+dayid+'/auditors/'+auditorid,1,0,1);
	}

	function removeSchedule(eventid){
		UIkit.modal.confirm("Are you sure you want delete this scheduled time?").then(function(){
            $.post("scheduling/event/"+eventid+"/delete", {
	                '_token' : '{{ csrf_token() }}'
	                }, function(data) {
	                    if(data!=1){
	                        UIkit.modal.alert(data,{stack: true});
	                    } else {
	                    	UIkit.notification('<span uk-icon="icon: check"></span> Scheduled Time Removed', {pos:'top-right', timeout:1000, status:'success'});
	                        $('#project-details-button-2').trigger( 'click' );
	                    }
	        });
        }, function () {
		    return false;
		});
	}

	function removeAuditorFromAudit(auditorid){
		UIkit.modal.confirm("Are you sure you want remove this auditor from this audit? It will also remove all the scheduled times associated with that auditor.").then(function(){
            $.post("auditors/"+auditorid+"/removefromaudit/{{$data['project']['audit_id']}}", {
                    '_token' : '{{ csrf_token() }}'
                    }, function(data) {
                        if(data!=1){
                            UIkit.modal.alert(data,{stack: true});
                        } else {
                            UIkit.notification('<span uk-icon="icon: check"></span> Auditor Removed', {pos:'top-right', timeout:1000, status:'success'});
                            $('#project-details-button-2').trigger( 'click' );
                        }
            });
        });
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

		// flatpickr("#addaday", {
		//     mode: "range",
		//     minDate: "today",
		//     altFormat: "F j, Y",
		//     dateFormat: "F j, Y",
		//     wrap: true,
	 //     positionElement: $('.addadaybutton')[0],
	 //    onChange: function(selectedDates, dateStr, instance) {

	 //        $.post("/audit/{{$data['project']['audit_id']}}/scheduling/addaday", {
	 //            'date' : dateStr,
	 //            '_token' : '{{ csrf_token() }}'
	 //        }, function(data) {
		//         $('#project-details-button-2').trigger( 'click' );
		// 	});
	 //    }
		// });
		flatpickr("#addaday", {
		    mode: "range",
		    minDate: "today",
		    altFormat: "F j, Y",
		    dateFormat: "F j, Y",
		    onChange: function(selectedDates, dateStr, instance) {

	        $.post("/audit/{{$data['project']['audit_id']}}/scheduling/addaday", {
	            'date' : dateStr,
	            '_token' : '{{ csrf_token() }}'
	        }, function(data) {
		        $('#project-details-button-2').trigger( 'click' );
			});
	    }
		});

	@endIf

</script>