<style type="text/css">
	.on-phone-pd {
		position: relative;
		left: -4px;
		top: -17px;
		font-size: 0.95rem;
		font-weight: bolder;
	}

	.finding-number-pd {
		font-size: 9px;
		background: #666;
		padding: 0px 4px 0px;
		border: 0px;
		min-width: 13px;
		max-height: 13px;
		line-height: 1.5;
	}
	.manager-fail {
		color:red;
	}
	#detail-tab-4-content.p, #detail-tab-4-content.div, #detail-tab-4-content {
		font-size: 13pt;
		line-height: 26px;
	}
	#detail-tab-4-content.h1 {
		font-size: 24pt;
		line-height: 32px;
	}
	#detail-tab-4-content.h2 {
		font-size: 20pt;
		line-height: 28px;
	}
	#detail-tab-4-content.h3 {
		font-size: 16pt
		line-height: 24px;
	}
	#detail-tab-4-content.h4,#detail-tab-4-content.h5 {
		font-size: 14pt;
		line-height: 22px;
	}
</style>

<?php
	$fileCount = count($selected_audit->audit->files);
	$nltCount = count($selected_audit->audit->nlts);
	$ltCount = count($selected_audit->audit->lts);
	$car = collect($selected_audit->audit->reports)->where('from_template_id','1')->first();
	$ehs = collect($selected_audit->audit->reports)->where('from_template_id','2')->first();
	//dd($selected_audit->audit->reports,collect($selected_audit->audit->reports)->where('from_template_id','1')->first());
?>
<div id="project-details-main" class="uk-overflow-auto" uk-grid>
	<div class="uk-width-1-1 uk-padding-remove">
		<div id="project-details-main-row" class="{{$selected_audit->status}}">
			<div class="uk-grid-match" uk-grid>
				<div class="uk-width-1-4 uk-padding-remove">
					<div uk-grid>
						<div class="uk-width-1-5 uk-padding-remove" style="margin-top: 10px;" onclick="UIkit.modal('#modal-select-audit').show();">
							<i class="a-square-right-2"></i>
						</div>
						<div class="uk-width-1-5 uk-padding-remove" style="margin-top: 7px;">
							<span id="audit-avatar-badge-1" uk-tooltip="pos:top-left;title:{{$selected_audit->lead_json->name}};" title="" aria-expanded="false" class="user-badge user-badge-{{$selected_audit->lead_json->color}} no-float uk-link">
								{{$selected_audit->lead_json->initials}}
							</span>
						</div>
						<div class="uk-width-3-5" style="padding-right:0">
							<h3 id="audit-project-name-1" class="uk-margin-bottom-remove">{{$project->project_number}}</h3>
			            	<small class="uk-text-muted" style="font-size: 0.7em;">AUDIT {{$selected_audit->audit_id}}</small>
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
						            			@if(!is_null($selected_audit->inspection_schedule_date))
							            		<h3 class="uk-link uk-margin-remove" uk-tooltip="title:AUDIT'S STARTING DATE;">{{date('m/d',strtotime($selected_audit->inspection_schedule_date))}}</h3>
							            		<div class="dateyear">{{date('Y',strtotime($selected_audit->inspection_schedule_date))}}</div>
							            		@else
							            		<i class="a-calendar-pencil" uk-tooltip="title:SCHEDULE USING ASSIGNMENT BELOW;"></i>
							            		@endif

						            		</div>
						            	</div>
						            	<div class="uk-width-1-6 iconpadding uk-text-right" uk-tooltip="title:I AM INSPECTING {{$selected_audit->auditor_items()}} ITEMS OF THE {{$selected_audit->total_items}} TOTAL;">{{$selected_audit->auditor_items()}}@if($selected_audit->lead == Auth::user()->id)*@endif /</div>
						            	<div class="uk-width-1-6 iconpadding uk-text-left">{{$selected_audit->total_items}}</div>
						            	<div class="uk-width-1-6 iconpadding uk-text-left">
						            		<i class="{{$selected_audit->audit_compliance_icon}} {{$selected_audit->audit_compliance_status}}"  uk-tooltip="title:{{$selected_audit->audit_compliance_status_text}};"></i> @if(!count($selected_audit->audit->findings))<i class="use-hand-cursor a-rotate-left" uk-tooltip title="CLICK TO RERUN AUDIT SELECTION"></i>@endIf
						            	</div>
						            </div>
								</div>
								<div class="uk-width-1-3 uk-padding-remove">
									<div class="divider"></div>
									<div class="uk-text-center hasdivider uk-margin-small-top" uk-grid>

						            	<div class="uk-width-1-2">
						            		@if(($car))
						            		<?php
						            			switch ($car->crr_approval_type_id) {
						            				case '1':
						            					$carIcon = "a-file-pencil-2"; // draft
						            					break;
						            				case '2':
						            					$carIcon = "a-file-clock"; // pending manager review
						            					break;
						            				case '3':
						            					$carIcon = "a-file-fail manager-fail attention"; // declined by manager
						            					break;
						            				case '4':
						            					$carIcon = "a-file-repeat"; // approved with changes
						            					break;
						            				case '5':
						            					$carIcon = "a-file-certified"; // approved
						            					break;
						            				case '6':
						            					$carIcon = "a-file-mail"; // Unopened by PM
						            					break;
						            				case '7':
						            					$carIcon = "a-file-person"; // Viewed by a PM
						            					break;
						            				case '9':
						            					$carIcon = "a-file-approve"; // All items resolved
						            					break;
						            				default:
						            					$carIcon = "a-file-fail";
						            					break;
						            			}
						            		?>
						            		<a class="uk-link-mute" href="/report/{{$car->id}}" target="report-{{$car->id}}" uk-tooltip="title:VIEW CAR {{$car->id}} : {{strtoupper($car->status_name())}}"><i class="{{$carIcon}}" ></i></a> &nbsp; <i class="a-square-right-2 use-hand-cursor" id="car-dropdown-{{$selected_audit->audit_id}}" uk-tooltip title="ACTION"></i>
												<div  uk-dropdown="mode:click">
												    <ul class="uk-nav uk-dropdown-nav">
												        <li @if($car->crr_approval_type_id == 1) class="uk-active" @endIf><a onclick="reportActionPDT({{$car->id}},1,{{$selected_audit->project_id}});">DRAFT</a></li>
				                                        @if($car->requires_approval)
				                                        <li @if($car->crr_approval_type_id == 2) class="uk-active" @endIf ><a onclick="reportActionPDT({{$car->id}},2,{{$selected_audit->project_id}});">SEND TO MANAGER REVIEW</a></li>
				                                        @endIf
				                                        @can('access_manager')
				                                        <li @if($car->crr_approval_type_id == 3) class="uk-active" @endIf ><a onclick="reportActionPDT({{$car->id}},3,{{$selected_audit->project_id}});">DECLINE</a></li>
				                                        <li @if($car->crr_approval_type_id == 4) class="uk-active" @endIf ><a onclick="reportActionPDT({{$car->id}},4,{{$selected_audit->project_id}});">APPROVE WITH CHANGES</a></li>
				                                        <li @if($car->crr_approval_type_id == 5) class="uk-active" @endIf ><a onclick="reportActionPDT({{$car->id}},5,{{$selected_audit->project_id}});">APPROVE</a></li>
				                                        @endCan
				                                        @if(($car->requires_approval == 1 && $car->crr_approval_type_id > 3) || $car->requires_approval == 0 || Auth::user()->can('access_manager'))
				                                        <li @if($car->crr_approval_type_id == 6) class="uk-active" @endIf ><a onclick="reportActionPDT({{$car->id}},6,{{$selected_audit->project_id}});">SEND TO PROPERTY CONTACT</a></li>
				                                        <li @if($car->crr_approval_type_id == 7) class="uk-active" @endIf ><a onclick="reportActionPDT({{$car->id}},7,{{$selected_audit->project_id}});">PROPERTY VIEWED IN PERSON</a></li>
				                                        <li @if($car->crr_approval_type_id == 9) class="uk-active" @endIf ><a onclick="reportActionPDT({{$car->id}},9,{{$selected_audit->project_id}});">ALL ITEMS RESOLVED</a></li>
				                                        @endIf

												    </ul>
												</div><br /><small>CAR #{{$car->id}}</small>

						            		@else
						            		<i  @if($selected_audit->step_id > 59 && $selected_audit->step_id < 67) class="a-file-plus" uk-tooltip="title:GENERATE THIS AUDIT'S CAR" onclick="submitNewReportPD({{$selected_audit->id}},1)" @else class="a-file-fail" uk-tooltip="title:SORRRY, THE AUDIT'S STATUS DOES NOT ALLOW A CAR TO BE GENERATED." @endIf></i><br /><small>CAR</small>
							            	@endIf
							            </div><div class="uk-width-1-2">
							            	@if(($ehs))
							            	<?php
						            			switch ($ehs->crr_approval_type_id) {
						            				case '1':
						            					$ehsIcon = "a-file-pencil-2"; // draft
						            					break;
						            				case '2':
						            					$ehsIcon = "a-file-clock"; // pending manager review
						            					break;
						            				case '3':
						            					$ehsIcon = "a-file-fail manager-fail attention"; // declined by manager
						            					break;
						            				case '4':
						            					$ehsIcon = "a-file-repeat"; // approved with changes
						            					break;
						            				case '5':
						            					$ehsIcon = "a-file-certified"; // approved
						            					break;
						            				case '6':
						            					$ehsIcon = "a-file-mail"; // Unopened by PM
						            					break;
						            				case '7':
						            					$ehsIcon = "a-file-pen"; // Viewed by a PM
						            					break;
						            				case '9':
						            					$ehsIcon = "a-file-approve"; // All items resolved
						            					break;
						            				default:
						            					$ehsIcon = "a-file-fail";
						            					break;
						            			}
						            		?>
						            		<a class="uk-link-mute" href="/report/{{$ehs->id}}" target="report-{{$ehs->id}}" uk-tooltip="title:{{$ehs->status_name()}}"><i class="{{$ehsIcon}}" ></i><br /><small>EHS</small></a>
						            		@else
						            		@if($selected_audit->step_id > 59 && $selected_audit->step_id < 67) <span class="use-hand-cursor" uk-tooltip="title:GENERATE THIS AUDIT'S EHS" onclick="submitNewReportPD({{$selected_audit->audit_id}},2)"><i  class="a-file-plus"></i><br /><small>EHS</small></span>  @else <i class="a-file-fail" uk-tooltip="title:SORRRY, THE AUDIT'S STATUS DOES NOT ALLOW A EHS TO BE GENERATED." ></i><br /><small>EHS</small>@endIf
							            	@endIf
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
						            	<div uk-tooltip="title: CLICK TO ADD A FILE FINDING" class="uk-width-1-3 use-hand-cursor  uk-first-column" title="" aria-expanded="false" onclick="openFindings(this, {{$selected_audit->audit_id}}, {{$selected_audit->id}}, null, 'file', null,'0');"><i class="a-folder"></i><span class="uk-badge finding-number-pd on-phone-pd" uk-tooltip title="{{$fileCount}} @if($fileCount < 1 || $fileCount > 1) FINDINGS @else FINDING @endIf" aria-expanded="false">{{$fileCount}}</span></div>
						            	<div uk-tooltip="title: CLICK TO ADD A NLT FINDING" class="uk-width-1-3 use-hand-cursor " title="" aria-expanded="false" onclick="openFindings(this, {{$selected_audit->audit_id}}, {{$selected_audit->id}}, null, 'nlt', null,'0');"><i class="a-booboo"></i><span class="uk-badge finding-number-pd on-phone-pd" uk-tooltip title="{{$nltCount}} @if($nltCount < 1 || $nltCount > 1) FINDINGS @else FINDING @endIf" aria-expanded="false">{{$nltCount}}</span></div>
						            	<div uk-tooltip="title: CLICK TO ADD A LT FINDING" class="uk-width-1-3 use-hand-cursor" title="" aria-expanded="false" onclick="openFindings(this, {{$selected_audit->audit_id}}, {{$selected_audit->id}}, null, 'lt', null,'0');"><i class="a-skull"></i><span class="uk-badge finding-number-pd on-phone-pd" uk-tooltip title="{{$ltCount}} @if($ltCount < 1 || $ltCount > 1) FINDINGS @else FINDING @endIf" aria-expanded="false">{{$ltCount}}</span></div>
						            </div>

								</div>
								<div class="uk-width-2-5 uk-padding-remove">
									<div class="divider"></div>
									<div class="uk-text-center hasdivider uk-margin-small-top" uk-grid>
										@if(env('APP_ENV') == 'local')
						            	<div class="uk-width-1-4">
						            		<i class="{{$selected_audit->auditor_status_icon}}" uk-tooltip="title:{{$selected_audit->auditor_status_text}}"></i>
						            	</div>
						            	<div class="uk-width-1-4">
						            		<i class="{{$selected_audit->message_status_icon}}" uk-tooltip="title:{{$selected_audit->message_status_text}};"></i>
						            	</div>
						            	<div class="uk-width-1-4">
						            		<i class="{{$selected_audit->document_status_icon}}" uk-tooltip="title:{{$selected_audit->document_status_text}}"></i>
						            	</div>
						            	<div class="uk-width-1-4">
						            		<!-- <i class="a-person-clock" uk-tooltip="title:VIEW HISTORY;"></i> -->
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
				<h3>{{$project->project_name}}<br /><small>Project {{$project->project_number}} @if($project->currentAudit())| Current Audit {{$project->currentAudit()->audit_id}}@endif</small></h3>
			</div>
			<div class="uk-width-1-3">
				<div class="audit-info" style="width: 80%;float: left;">
				Last Audit Completed: {{$project->lastAuditCompleted()}}<br />
				Next Audit Due By: {{$project->nextDueDate()}}<br />
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
	<div id="project-details-selections" class="uk-width-1-1">
		<?php
			$siteInspections = $selected_audit->audit->project_amenity_inspections;
			$buildingInspections = $selected_audit->audit->building_inspections;
			$unitInspections = $selected_audit->audit->unit_inspections;
			$pdtDetails = $details;
			$pdtFindings = $selected_audit->audit->findings;
			$pieceData = [];
			$print = null;
			$report = $selected_audit;
			?>
		@if(count($siteInspections))
			<?php
			  $bladeData = $siteInspections;
			  ?>
				@include('crr_parts.crr_inspections', [$inspections_type = 'site',$detailsPage = 1])
		@endif
		@if(count($buildingInspections))
			<?php
			  $bladeData = $buildingInspections;
			  ?>
				@include('crr_parts.crr_inspections', [$inspections_type = 'building',$detailsPage = 1])
		@endif
		@if(count($unitInspections))
			<?php
				//dd($unitInspections);
			  $bladeData = $unitInspections;
			  ?>
				@include('crr_parts.crr_inspections', [$inspections_type = 'unit',$detailsPage = 1])
		@endif
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

<div id="modal-select-audit" uk-modal>
    <div class="uk-modal-dialog uk-modal-body">
        <h2 class="uk-modal-title">Select another audit</h2>
        <select name="audit-selection" id="audit-selection" class="uk-select">
        	@foreach($audits as $audit)
        	<option value="{{$audit->audit_id}}" @if($audit->audit_id == $selected_audit->audit_id) selected @endif>Audit {{$audit->audit_id}} @if($audit->completed_date) | Completed on {{formatDate($audit->completed_date)}}@endif</option>
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
    	$.post("/session/project.{{$project->id}}.selectedaudit/"+nextAudit, {
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

    function submitNewReportPD(audit_id,template_id) {
		console.log('Submitting Request for New Report.');
		$.post('/new-report', {
	            'template_id' : template_id,
	            'audit_id' : audit_id,
	            '_token' : '{{ csrf_token() }}'
	            }, function(data) {
	                if(data!=1){
	                    UIkit.notification({
					    message: data,
					    status: 'danger',
					    pos: 'top-right',
					    timeout: 5000
						});


	                } else {

	                    UIkit.notification({
					    message: 'Report Created',
					    status: 'success',
					    pos: 'top-right',
					    timeout: 1500
						});
						loadTab('{{ route('project.details', $project->id) }}', '1', 0, 0, 'project-',1);

	                }
		} );

		// by nature this note is it's history note - so no need to ask them for a comment.


	}

	function reportActionPDT(reportId,action,project_id = null){
            window.crrActionReportId = reportId;
            //Here goes the notification code
            if(action == 6) {
            	dynamicModalLoad('report-ready/' + reportId + '/' + project_id);
            } else if(action == 2) {
            	dynamicModalLoad('report-send-to-manager/' + reportId + '/' + project_id);
            } else if(action == 3) {
            	dynamicModalLoad('report-decline/' + reportId + '/' + project_id);
            } else if(action == 4) {
            	dynamicModalLoad('report-approve-with-changes/' + reportId + '/' + project_id);
            } else if(action == 5) {
            	dynamicModalLoad('report-approve/' + reportId + '/' + project_id);
            } else if(action == 9) {
                dynamicModalLoad('report-resolved/' + reportId + '/' + project_id);
            } else if(action != 8){
                $.get('/dashboard/reports', {
                                            'id' : reportId,
                                            'action' : action,
                                            'project_id' : project_id
                                             }, function(data2) {

                                         });
                $('#project-detail-tab-1').trigger('click');
                //loadTab('/dashboard/reports?id='+reportId+'&action='+action, '3','','','',1);
            }else if(action == 8){
                UIkit.modal.confirm('Refreshing the dynamic data will set the report back to Draft status - are you sure you want to do this?').then(function(){
                    $.get('/dashboard/reports', {
                                            'id' : window.crrActionReportId,
                                            'action' : 8
                                             }, function(data2) {

                                         });
                    $('#project-detail-tab-1').trigger('click');
                },function(){
                    //nope
                });
            }
        }

        $.fn.scrollView = function () {
		  return this.each(function () {
		    $('html, body').animate({
		      scrollTop: $(this).offset().top-80
		    }, 1000);
		  });
		}


$( document ).ready(function() {
	if($('#project-details-info-container').html() == ''){
		$('#project-details-button-1').trigger("click");
	}
	loadProjectDetailsBuildings( {{$project->id}}, {{$project->id}} ) ;
	UIkit.dropdown('#car-dropdown-{{$selected_audit->audit_id}}', 'mode:click');
});
</script>
