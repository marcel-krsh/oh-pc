<div id="project-details-main" class="uk-overflow-auto" uk-grid>
	<div class="uk-width-1-1 uk-padding-remove">
		<div id="project-details-main-row" class="{{ $selected_audit->status }}">
			<div class="uk-grid-match" uk-grid>
				<div class="uk-width-1-4 uk-padding-remove">
					<div uk-grid>
						<div class="uk-width-1-5 uk-padding-remove" style="margin-top: 10px;" onclick="UIkit.modal('#modal-select-audit').show();">
							<i class="a-square-right-2"></i>
						</div>
						<div class="uk-width-1-5 uk-padding-remove" style="margin-top: 7px;">
							<span id="audit-avatar-badge-1" uk-tooltip="pos:top-left;title:{{ $selected_audit->lead_json->name }};" title="" aria-expanded="false" class="user-badge user-badge-{{ $selected_audit->lead_json->color }} no-float uk-link">
								{{ $selected_audit->lead_json->initials }}
							</span>
						</div>
						<div class="uk-width-3-5" style="padding-right:0">
							<h3 id="audit-project-name-1" class="uk-margin-bottom-remove">{{ $project->project_number }}</h3>
			            	<small class="uk-text-muted" style="font-size: 0.7em;">AUDIT {{ $selected_audit->audit_id }}</small>
						</div>
					</div>
				</div>
				<div class="uk-width-3-4 uk-padding-remove">
					<div uk-grid>
						<div class="uk-width-1-2 uk-padding-remove">
							<div uk-grid>
								<div class="uk-width-2-3 uk-padding-remove">
						            <div class="divider"></div>
									<div class="uk-text-center hasdivider" uk-grid>
						            	<div class="uk-width-1-2 uk-padding-remove" uk-grid>
						            		<div class="uk-width-1-3 iconpadding">
						            			<i class="{{ $selected_audit->inspection_icon }} {{ $selected_audit->inspection_status }}" uk-tooltip="title:{{ $selected_audit->inspection_status_text }};"></i>
						            		</div>
						            		<div class="uk-width-2-3 uk-padding-remove">
						            			@if(!is_null($selected_audit->inspection_schedule_date))
							            		<h3 class="uk-link uk-margin-remove" uk-tooltip="title:{{ $selected_audit->inspection_schedule_text }};">{{ date('m/d',strtotime($selected_audit->inspection_schedule_date)) }}</h3>
							            		<div class="dateyear">{{ date('Y',strtotime($selected_audit->inspection_schedule_date)) }}</div>
							            		@else
							            		<i class="a-calendar-pencil" uk-tooltip="title:Schedule Using Assignment Below;"></i>
							            		@endif

						            		</div>
						            	</div>
						            	<div class="uk-width-1-6 iconpadding uk-text-right" uk-tooltip="title:{{ $selected_audit->auditor_items() }} INSPECTABLE ITEMS;">{{ $selected_audit->auditor_items() }}@if($selected_audit->lead == Auth::user()->id)*@endif /</div>
						            	<div class="uk-width-1-6 iconpadding uk-text-left">{{ $selected_audit->total_items }}</div>
						            	<div class="uk-width-1-6 iconpadding uk-text-left">
						            		<i class="{{ $selected_audit->audit_compliance_icon }} {{ $selected_audit->audit_compliance_status }}"  uk-tooltip="title:{{ $selected_audit->audit_compliance_status_text }};"></i>
						            	</div>
						            </div>
								</div>
								<div class="uk-width-1-3 uk-padding-remove">
									<div class="divider"></div>
									<div class="uk-text-center hasdivider uk-margin-small-top" uk-grid>
						            	<div class="uk-width-1-3">
						            		<i class="a-bell-2" uk-tooltip="title:{{ $selected_audit->followup_status_text }};"></i>
						            	</div>
						            	<div class="uk-width-2-3">
						            		@if(is_null($selected_audit->followup_date))
						            		<i class="a-calendar-pencil" uk-tooltip="title:New followup;"></i>
						            		@else
						            		<h3 class="uk-link uk-margin-remove" uk-tooltip="title:{{ $selected_audit->inspection_schedule_text }};">{{ date('m/d',strtotime($selected_audit->followup_date)) }}</h3>
							            		<div class="dateyear">{{ date('Y',strtotime($selected_audit->followup_date)) }}</div>
							            	@endif
						            	</div>
						            </div>
								</div>
							</div>
						</div>
						<div class="uk-width-1-2 uk-padding-remove">
							<div uk-grid>
								<div class="uk-width-2-5 uk-padding-remove">
									@if(env('APP_ENV') == 'local')
									<div class="divider"></div>
									<div class="uk-text-center hasdivider uk-margin-small-top" uk-grid>
						            	<div uk-tooltip="title:ADD FINDING" class="uk-width-1-3 use-hand-cursor ok-actionable uk-first-column" title="" aria-expanded="false"><i class="a-folder"></i></div>
						            	<div uk-tooltip="title:ADD FINDING" class="uk-width-1-3 use-hand-cursor action-required" title="" aria-expanded="false"><i class="a-booboo"></i></div>
						            	<div uk-tooltip="title:ADD FINDING" class="uk-width-1-3 use-hand-cursor in-progress" title="" aria-expanded="false"><i class="a-skull"></i></div>
						            </div>
						            @endIf
								</div>
								<div class="uk-width-2-5 uk-padding-remove">
									<div class="divider"></div>
									<div class="uk-text-center hasdivider uk-margin-small-top" uk-grid>
										@if(env('APP_ENV') == 'local')
						            	<div class="uk-width-1-4">
						            		<i class="a-avatar action-needed" uk-tooltip="title:Auditors / schedule conflicts / unasigned items;"></i>
						            	</div>
						            	<div class="uk-width-1-4">
						            		<i class="a-envelope-4 action-required" uk-tooltip="title:;"></i>
						            	</div>
						            	<div class="uk-width-1-4">
						            		<i class="a-files ok-actionable" uk-tooltip="title:Document status;"></i>
						            	</div>
						            	<div class="uk-width-1-4">
						            		<i class="a-person-clock" uk-tooltip="title:NO/VIEW HISTORY;"></i>
						            	</div>
						            	@endIF
						            </div>
								</div>
								<div class="uk-width-1-5 iconpadding">
									@if(env('APP_ENV') == 'local')
									<i class="a-calendar-7"></i>
									@endIf
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="project-details" uk-grid>
	<div id="project-details-general" class="uk-width-1-1">
		<div uk-grid>
			<div class="uk-width-2-3">
				<h3>{{ $project->project_name }}<br /><small>Project {{ $project->project_number }} @if($selected_audit)| Current Audit {{ $selected_audit->audit_id }}@endif</small></h3>
			</div>
			<div class="uk-width-1-3">
				<div class="audit-info" style="width: 80%;float: left;">
				Last Audit Completed: {{ $project->lastAuditCompleted() }}<br />
				Next Audit Due By: {{ $project->nextDueDate() }}<br />
				Current Project Score : N/A
				</div>
				<div class="audit-refresh">
					<a onclick="refresh_details({{ $project->id }}, {{ $selected_audit->audit_id }});" style="padding: 10px;border-radius: 5px;width: 25px;float: left;height: 25px;" href="javascript:void(0);" class="btn btn-refresh"><i class="a-rotate-right-2 iheader" style="font-size: 25px;color: #000;"></i></a>
				</div>
			</div>
		</div>
	</div>
	<div id="project-details-stats" class="uk-width-1-1" style="margin-top:20px;">
		@include('projects.partials.details-project-details', ['details', $details])
	</div>
</div>


<div id="project-details-buttons" class="project-details-buttons" uk-grid>
	<div class="uk-width-1-1">
		<div uk-grid>
			<div class="uk-width-1-2 uk-padding-remove">
				<div uk-grid>
					<div class="uk-width-1-2">
						<button id="project-details-button-1" class="uk-button uk-link ok-actionable active" onclick="projectDetailsInfo({{ $project->id }}, 'compliance', this);" type="button"><i class="a-circle-checked"></i> COMPLIANCE</button>
					</div>
					<div class="uk-width-1-2">
						<button id="project-details-button-2" class="uk-button uk-link critical" onclick="projectDetailsInfo({{ $project->id }}, 'assignment', this);" type="button"><i class="a-avatar-fail"></i> ASSIGNMENT</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="project-details-info-container"></div>

<div id="modal-select-audit" uk-modal>
    <div class="uk-modal-dialog uk-modal-body">
        <h2 class="uk-modal-title">Select another audit</h2>
        <select name="audit-selection" id="audit-selection" class="uk-select">
        	@foreach($audits as $audit)
        	<option value="{{ $audit->audit_id }}" @if($audit->audit_id == $selected_audit->audit_id) selected @endif>Audit {{ $audit->audit_id }} @if($audit->completed_date) | Completed on {{ formatDate($audit->completed_date) }}@endif</option>
        	@endforeach
        </select>
        <p class="uk-text-right">
            <button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
            <button class="uk-button uk-button-primary" onclick="changeAudit();" type="button">Select</button>
        </p>
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

    function changeAudit(){
    	console.log("changing audit");

    	var nextAudit = $('#audit-selection').val();

    	var tempdiv = '<div style="height:500px;text-align:center;"><div uk-spinner style="margin: 10% auto;"></div></div>';
		$('#project-detail-tab-1-content').html(tempdiv);

    	UIkit.modal('#modal-select-audit').hide();
    	$('#modal-select-audit').remove();
    	dynamicModalClose();

    	$.post("/session/project.{{ $project->id }}.selectedaudit/"+nextAudit, {
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            loadTab('{{ route('project.details', $project->id) }}', '1', 0, 0, 'project-',1);

        } );


    }

    function refresh_details(id, auditId) {
        UIkit.modal.confirm("Are you sure want to refresh the details?").then(function() {
            $.post('{{ URL::route("project.refreshdetails") }}', {
                'id' : id,
                'audit_id': auditId,
                '_token' : '{{ csrf_token() }}'
            }, function(data) {
                console.log(data.success);
				$("#project-details-stats").html(data.html);
            });
        }, function () {
            console.log('Rejected.');
        });
    }
</script>

<script>



$( document ).ready(function() {
	if($('#project-details-info-container').html() == ''){
		$('#project-details-button-1').trigger("click");
	}
	loadProjectDetailsBuildings( {{ $project->id }}, {{ $project->id }} ) ;
});
</script>
