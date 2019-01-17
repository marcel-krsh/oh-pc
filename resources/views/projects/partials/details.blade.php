<div id="project-details-main" class="uk-overflow-auto" uk-grid>
	<div class="uk-width-1-1 uk-padding-remove">
		<div id="project-details-main-row" class="{{$selected_audit->status}}">
			<div class="uk-grid-match" uk-grid>
				<div class="uk-width-1-4 uk-padding-remove">
					<div uk-grid>
						<div class="uk-width-1-5 uk-padding-remove" style="margin-top: 10px;">
							<a href="#modal-select-audit" uk-toggle><i class="a-square-right-2"></i></a>
							<div id="modal-select-audit" uk-modal>
							    <div class="uk-modal-dialog uk-modal-body">
							        <h2 class="uk-modal-title">Select another audit</h2>
							        <select name="audit-selection" id="audit-selection">
							        	@foreach($audits as $audit)
							        	<option value="{{$audit->id}}" @if($audit->id == $selected_audit->id) selected @endif>Audit {{$audit->id}} @if($audit->completed_date) | Completed on {{formatDate($audit->completed_date)}}@endif</option>
							        	@endforeach
							        </select>
							        <p class="uk-text-right">
							            <button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
							            <button class="uk-button uk-button-primary" onclick="changeAudit();" type="button">Select</button>
							        </p>
							    </div>
							</div>
						</div>
						<div class="uk-width-1-5 uk-padding-remove" style="margin-top: 7px;">
							<span id="audit-avatar-badge-1" uk-tooltip="pos:top-left;title:{{$selected_audit->lead_json->name}};" title="" aria-expanded="false" class="user-badge user-badge-{{$selected_audit->lead_json->color}} no-float uk-link">
								{{$selected_audit->lead_json->initials}}
							</span>
						</div>
						<div class="uk-width-3-5" style="padding-right:0">
							<h3 id="audit-project-name-1" class="uk-margin-bottom-remove">{{$project->project_number}}</h3>
			            	<small class="uk-text-muted" style="font-size: 0.7em;">AUDIT {{$selected_audit->id}}</small>
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
						            			<i class="{{$selected_audit->inspection_icon}} {{$selected_audit->inspection_status}}" uk-tooltip="title:{{$selected_audit->inspection_status_text}};"></i>
						            		</div>
						            		<div class="uk-width-2-3 uk-padding-remove">
							            		<h3 class="uk-link uk-margin-remove" uk-tooltip="title:Click to reschedule audits;">12/21</h3>
							            		<div class="dateyear">2018</div>
						            		</div>
						            	</div> 
						            	<div class="uk-width-1-6 iconpadding uk-text-right" uk-tooltip="title:{{$selected_audit->auditor_items()}} INSPECTABLE ITEMS;">{{$selected_audit->auditor_items()}}@if($selected_audit->lead == Auth::user()->id)*@endif /</div> 
						            	<div class="uk-width-1-6 iconpadding uk-text-left">{{$selected_audit->total_items}}</div> 
						            	<div class="uk-width-1-6 iconpadding uk-text-left">
						            		<i class="{{$selected_audit->audit_compliance_icon}} {{$selected_audit->audit_compliance_status}}"  uk-tooltip="title:{{$selected_audit->audit_compliance_status_text}};"></i>
						            	</div>
						            </div>
								</div>
								<div class="uk-width-1-3 uk-padding-remove">
									<div class="divider"></div>
									<div class="uk-text-center hasdivider uk-margin-small-top" uk-grid>
						            	<div class="uk-width-1-3">
						            		<i class="a-bell-2" uk-tooltip="title:No followups;"></i>
						            	</div> 
						            	<div class="uk-width-2-3">
						            		<i class="a-calendar-pencil" uk-tooltip="title:New followup;"></i>
						            	</div> 
						            </div>
								</div>
							</div>
						</div>
						<div class="uk-width-1-2 uk-padding-remove">
							<div uk-grid>
								<div class="uk-width-2-5 uk-padding-remove">
									<div class="divider"></div>
									<div class="uk-text-center hasdivider uk-margin-small-top" uk-grid>
						            	<div uk-tooltip="title:ADD FINDING" class="uk-width-1-3 use-hand-cursor ok-actionable uk-first-column" title="" aria-expanded="false"><i class="a-folder"></i></div>
						            	<div uk-tooltip="title:ADD FINDING" class="uk-width-1-3 use-hand-cursor action-required" title="" aria-expanded="false"><i class="a-booboo"></i></div>
						            	<div uk-tooltip="title:ADD FINDING" class="uk-width-1-3 use-hand-cursor in-progress" title="" aria-expanded="false"><i class="a-skull"></i></div>
						            </div>
								</div>
								<div class="uk-width-2-5 uk-padding-remove">
									<div class="divider"></div>
									<div class="uk-text-center hasdivider uk-margin-small-top" uk-grid>
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
						            </div>
								</div>
								<div class="uk-width-1-5 iconpadding">
									<i class="a-calendar-7"></i>
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
				<h3>{{$project->project_name}}<br /><small>Project {{$project->project_number}} @if($project->currentAudit())| Current Audit {{$project->currentAudit()->id}}@endif</small></h3>
			</div>
			<div class="uk-width-1-3">
				Last Audit Completed: {{$project->lastAuditCompleted()}}<br />
				Next Audit Due By: {{$project->nextDueDate()}}<br />
				Current Project Score : N/A
			</div>
		</div>
	</div>
	<div id="project-details-stats" class="uk-width-1-1" style="margin-top:20px;">
		<div uk-grid>
			<div class="uk-width-2-3">
				<ul class="leaders" style="margin-right:30px;">
					<li><span>Total Buildings</span> <span>{{$details->total_building}}</span></li>
					<li><span class="indented">Total Building Common Areas</span> <span>{{$details->total_building_common_areas}}</span></li>
					<li><span class="indented">Total Building Systems</span> <span>{{$details->total_building_systems}}</span></li>
					<li><span class="indented">Total Building Exteriors</span> <span>{{$details->total_building_exteriors}}</span></li>
					<li><span>Total Project Common Areas</span> <span></span></li>
					<li><span>Total Units</span> <span>{{$details->total_units}}</span></li>
					<li><span class="indented">• Market Rate Units</span> <span>{{$details->market_rate}}</span></li>
					<li><span class="indented">• Program Units</span> <span>{{$details->subsidized}}</span></li>
					<li><span>Total Programs</span> <span></span></li>
					@foreach(json_decode($details->programs, true) as $program)
					<li><span class="indented">• {{$program['name']}}</span> <span>{{$program['units']}}</span></li>
					@endforeach
				</ul>
			</div>
			<div class="uk-width-1-3">
				<h5 class="uk-margin-remove"><strong>OWNER: {{$details->owner_name}}</strong></h5>
				<div class="address" style="margin-bottom:20px;">
					<i class="a-avatar"></i> {{$details->owner_poc}}<br />
					<i class="a-phone-5"></i> {{$details->owner_phone}} @if($details->owner_fax != '')<i class="a-fax-2" style="margin-left:10px"></i> {{$details->owner_fax}} @endif<br />
					<i class="a-mail-send"></i> {{$details->owner_email}}<br />
					@if($details->owner_address)<i class="a-mailbox"></i> {{$details->owner_address}} @endif
				</div>
				<h5 class="uk-margin-remove"><strong>Managed By: {{$details->manager_name}}</strong></h5>
				<div class="address">
					<i class="a-avatar"></i> {{$details->manager_poc}}<br />
					<i class="a-phone-5"></i> {{$details->manager_phone}} @if($details->manager_fax != '')<i class="a-fax-2" style="margin-left:10px"></i> {{$details->manager_fax}} @endif<br />
					<i class="a-mail-send"></i> {{$details->manager_email}}<br />
					@if($details->manager_address)<i class="a-mailbox"></i> {{$details->manager_address}} @endif
				</div>
			</div>
		</div>
	</div>
</div>


<div id="project-details-buttons" class="project-details-buttons" uk-grid>
	<div class="uk-width-1-1">
		<div uk-grid>
			<div class="uk-width-1-2 uk-padding-remove">
				<div uk-grid>
					<div class="uk-width-1-2">
						<button id="project-details-button-1" class="uk-button uk-link ok-actionable active" onclick="projectDetailsInfo({{$project->id}}, 'compliance', this);" type="button"><i class="a-circle-checked"></i> COMPLIANCE</button>
					</div>
					<div class="uk-width-1-2">
						<button id="project-details-button-2" class="uk-button uk-link critical" onclick="projectDetailsInfo({{$project->id}}, 'assignment', this);" type="button"><i class="a-avatar-fail"></i> ASSIGNMENT</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="project-details-info-container"></div>

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

    	$.post("/session/project.{{$project->id}}.selectedaudit/"+nextAudit, {
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            loadTab('{{ route('project.details', $project->id) }}', '1', 0, 0, 'project-',1);
    		
        } );

    	
    }
</script>

<script>



$( document ).ready(function() {
	if($('#project-details-info-container').html() == ''){
		$('#project-details-button-1').trigger("click");
	}	
	loadProjectDetailsBuildings( {{$project->id}}, {{$project->id}} ) ;
});
</script>
	    