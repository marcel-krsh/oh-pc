<style type="text/css">
	.on-phone-pd {
		position: relative;
		left: -4px;
		top: -28px;
		font-size: 0.95rem;
		font-weight: bolder;
	}

	.finding-number-pd {
		    font-size: 15px;
		    background: #666;
		    padding: 0px 4px 0px;
		    border: 0px;
		    min-width: 24px;
		    max-height: 24px;
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
	#project-details-main-row .pd-findings-column i  {
		font-size: 36px;
    	line-height: 37px;
	}

</style>

<?php

	if($selected_audit->update_cached_audit()){
		$selected_audit->refresh();
	}

	$fileCount = count($selected_audit->audit->files);
	$correctedFileCount = count($selected_audit->audit->files->where('auditor_last_approved_resolution_at', '<>',null));
	$nltCount = count($selected_audit->audit->nlts);
	$correctedNltCount = count($selected_audit->audit->nlts->where('auditor_last_approved_resolution_at', '<>',null));
	
	$ltCount = count($selected_audit->audit->lts);
	$correctedLtCount = count($selected_audit->audit->lts->where('auditor_last_approved_resolution_at', '<>',null));
	
	$car = collect($selected_audit->audit->reports)->where('from_template_id','1')->first();
	$ehs = collect($selected_audit->audit->reports)->where('from_template_id','2')->first();
	$_8823 = collect($selected_audit->audit->reports)->where('from_template_id','5')->first();
	//dd($selected_audit->audit->reports,collect($selected_audit->audit->reports)->where('from_template_id','1')->first());

?>
<div id="project-details-main" class="uk-overflow-auto" uk-grid>
	<div class="uk-width-1-1 uk-padding-remove">
		<div id="project-details-main-row" class="{{$selected_audit->status}}">
			<div class="uk-grid-match" uk-grid>
				<div class="uk-width-1-6 uk-padding-remove">
					<div uk-grid>
						<div class="uk-width-1-5 uk-padding-remove" style="margin-top: 10px;" onclick="UIkit.modal('#modal-select-audit').show();">
							<i class="a-square-right-2"></i>
						</div>
						<div class="uk-width-1-5 uk-padding-remove" style="margin-top: 7px;">
							<span id="audit-avatar-badge-1" uk-tooltip="pos:top-left;title:{{$selected_audit->lead_json->name}};" title="" aria-expanded="false" class="user-badge user-badge-{{$selected_audit->lead_json->color}} uk-link" style="height: 48px;
    width: 48px;
    line-height: 48px; font-size: 27px; margin-top: .2em">
								{{$selected_audit->lead_json->initials}}
							</span>
						</div>
						<div class="uk-width-3-5" style="padding-left:1.2em">
							<h3 id="audit-project-name-1" class="uk-margin-bottom-remove uk-text-align-center" style="font-size: 1.5em; padding-top: .5em;">{{$project->project_number}}</h3>
			            	<small class="uk-text-muted" style="font-size: 1em;">AUDIT {{$selected_audit->audit_id}}</small>
						</div>
					</div>
				</div>
				<div class="uk-width-5-6 uk-padding-remove">
					<div uk-grid>
						<div class="uk-width-1-2 uk-padding-remove">
							<div uk-grid>
								<div class="uk-width-2-5 uk-padding-remove">
						            <div class="divider"></div>
									<div class="uk-text-center hasdivider" uk-grid>
						            	<div class="uk-width-1-2 uk-padding-remove" uk-grid>
						    
						            		<div class="uk-width-1-1 uk-padding-remove" uk-tooltip @if(!is_null($selected_audit->inspection_schedule_date) && count($selected_audit->days) > 0) title="AUDIT WILL TAKE {{count($selected_audit->days)}} @if( count($selected_audit->days) < 1 || count($selected_audit->days) > 1) DAYS @else DAY @endIf" @elseIf(!is_null($selected_audit->inspection_schedule_date) && count($selected_audit->days) == 0) title="NO DATES ARE SCHEDULED PLEASE USE ADD DAYS IN THE SCHEDULING TAB BELOW" @endIf>
						            			@if(!is_null($selected_audit->inspection_schedule_date))
							            		<div class="dateyear" style="margin-top:5px; text-transform: uppercase;">{{date('l',strtotime($selected_audit->inspection_schedule_date))}}</div>
							            		<h3 class="uk-link" style="margin: 9px 10px 6px 0px !important;">{{date('M jS',strtotime($selected_audit->inspection_schedule_date))}}</h3>
							            		<div class="dateyear">{{date('Y',strtotime($selected_audit->inspection_schedule_date))}}</div>
							            		@else
							            		<i class="a-calendar-pencil" uk-tooltip="title:SCHEDULE USING SCHEDULING BELOW;"></i>
							            		@endif

						            		</div>
						            	</div>
						            	<div class="uk-width-1-2 uk-padding-remove">
							            	<div class="uk-width-1-1" uk-tooltip title="INSPECTING {{count($selected_audit->audit->building_inspections)}} @if(count($selected_audit->audit->building_inspections) > 1 || count($selected_audit->audit->building_inspections) < 1) BUILDINGS @else BUILDING @endIf" style="margin-top: 8px;"><i class="a-buildings" style="font-size: 25px;"></i> : {{count($selected_audit->audit->building_inspections)}}</div>
							            	<hr class="uk-width-1-1" style="margin-bottom: 8px; margin-top: 0px" >
							            	<div class="uk-width-1-1" uk-tooltip title="INSPECTING {{count($selected_audit->audit->unique_unit_inspections)}} @if(count($selected_audit->audit->unique_unit_inspections) > 1 || count($selected_audit->audit->unique_unit_inspections) < 1) UNITS @else UNIT @endIf"><i class="a-buildings-2" style="font-size: 25px;"></i> : {{count($selected_audit->audit->unique_unit_inspections)}}</div>
							            	
							            </div>
						            	
						            </div>
								</div>
								<div class="uk-width-3-5 uk-padding-remove">
									<div class="divider"></div>
									<div class="uk-text-center hasdivider uk-margin-small-top" uk-grid>

						            	<div class="uk-width-1-3">
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
						            		<a class="uk-link-mute" href="/report/{{$car->id}}" target="report-{{$car->id}}" uk-tooltip="title:VIEW CAR {{$car->id}} : {{strtoupper($car->status_name())}}"><i class="{{$carIcon}}" style="font-size: 30px;"></i></a> &nbsp; <i class="a-square-right-2 use-hand-cursor" id="car-dropdown-{{$selected_audit->audit_id}}" uk-tooltip title="ACTION"></i>
												<div  uk-dropdown="mode:click">
												    <ul class="uk-nav uk-dropdown-nav " style="text-align: left !important;">
												        <li @if($car->crr_approval_type_id == 1) class="uk-active" @endIf><a onclick="reportActionPDT({{$car->id}},1,{{$selected_audit->project_id}});"><i class="a-file-pencil-2"></i> DRAFT</a></li>
				                                        @if($car->requires_approval)
				                                        <li @if($car->crr_approval_type_id == 2) class="uk-active" @endIf ><a onclick="reportActionPDT({{$car->id}},2,{{$selected_audit->project_id}});"><i class="a-file-clock"></i> SEND TO HFA MANAGER REVIEW</a></li>
				                                        @endIf
				                                        @can('access_manager')
				                                        <li @if($car->crr_approval_type_id == 3) class="uk-active" @endIf ><a onclick="reportActionPDT({{$car->id}},3,{{$selected_audit->project_id}});"><i class="a-file-fail"></i> DECLINE</a></li>
				                                        <li @if($car->crr_approval_type_id == 4) class="uk-active" @endIf ><a onclick="reportActionPDT({{$car->id}},4,{{$selected_audit->project_id}});"><i class="a-file-repeat"></i> APPROVE WITH CHANGES</a></li>
				                                        <li @if($car->crr_approval_type_id == 5) class="uk-active" @endIf ><a onclick="reportActionPDT({{$car->id}},5,{{$selected_audit->project_id}});"><i class="a-file-certified"></i> APPROVE</a></li>
				                                        @endCan
				                                        @if(($car->requires_approval == 1 && $car->crr_approval_type_id > 3) || $car->requires_approval == 0 || Auth::user()->can('access_manager'))
				                                        <li @if($car->crr_approval_type_id == 6) class="uk-active" @endIf ><a onclick="reportActionPDT({{$car->id}},6,{{$selected_audit->project_id}});"><i class="a-file-mail"></i> SEND TO PROPERTY CONTACT</a></li>
				                                        <li @if($car->crr_approval_type_id == 7) class="uk-active" @endIf ><a onclick="reportActionPDT({{$car->id}},7,{{$selected_audit->project_id}});"><i class="a-file-person"></i> PROPERTY VIEWED IN PERSON</a></li>
				                                        <li @if($car->crr_approval_type_id == 9) class="uk-active" @endIf ><a onclick="reportActionPDT({{$car->id}},9,{{$selected_audit->project_id}});"><i class="a-file-approve"></i> ALL ITEMS RESOLVED</a></li>
				                                        @endIf

												    </ul>
												</div><br /><small>CAR #{{$car->id}}</small>

						            		@else
						            		<i  @if($selected_audit->step_id > 59 && $selected_audit->step_id < 67) class="a-file-plus" uk-tooltip="title:GENERATE THIS AUDIT'S CAR" onclick="submitNewReportPD({{$selected_audit->audit_id}},1)" @else class="a-file-fail" uk-tooltip="title:SORRRY, THE AUDIT'S STATUS DOES NOT ALLOW A CAR TO BE GENERATED." @endIf></i><br /><small>CAR</small>
							            	@endIf
							            </div><div class="uk-width-1-3">
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
						            		<a class="uk-link-mute" href="/report/{{$ehs->id}}" target="report-{{$ehs->id}}" uk-tooltip="title:{{$ehs->status_name()}}"><i class="{{$ehsIcon}}" style="font-size: 30px;" ></i></a>

						            			&nbsp; <i class="a-square-right-2 use-hand-cursor" id="ehs-dropdown-{{$selected_audit->audit_id}}" uk-tooltip title="ACTION"></i>
												<div  uk-dropdown="mode:click">
												    <ul class="uk-nav uk-dropdown-nav " style="text-align: left !important;">
												        <li @if($ehs->crr_approval_type_id == 1) class="uk-active" @endIf><a onclick="reportActionPDT({{$ehs->id}},1,{{$selected_audit->project_id}});"><i class="a-file-pencil-2"></i> DRAFT</a></li>
				                                        @if($ehs->requires_approval)
				                                        <li @if($ehs->crr_approval_type_id == 2) class="uk-active" @endIf ><a onclick="reportActionPDT({{$ehs->id}},2,{{$selected_audit->project_id}});"><i class="a-file-clock"></i> SEND TO HFA MANAGER REVIEW</a></li>
				                                        
				                                        @can('access_manager')
				                                        <li @if($ehs->crr_approval_type_id == 3) class="uk-active" @endIf ><a onclick="reportActionPDT({{$ehs->id}},3,{{$selected_audit->project_id}});"><i class="a-file-fail"></i> DECLINE</a></li>
				                                        <li @if($ehs->crr_approval_type_id == 4) class="uk-active" @endIf ><a onclick="reportActionPDT({{$ehs->id}},4,{{$selected_audit->project_id}});"><i class="a-file-repeat"></i> APPROVE WITH CHANGES</a></li>
				                                        <li @if($ehs->crr_approval_type_id == 5) class="uk-active" @endIf ><a onclick="reportActionPDT({{$ehs->id}},5,{{$selected_audit->project_id}});"><i class="a-file-certified"></i> APPROVE</a></li>
				                                        @endCan
				                                        @endIf
				                                        @if(($ehs->requires_approval == 1 && $ehs->crr_approval_type_id > 3) || $ehs->requires_approval == 0 || Auth::user()->can('access_manager'))
				                                        <li @if($ehs->crr_approval_type_id == 6) class="uk-active" @endIf ><a onclick="reportActionPDT({{$ehs->id}},6,{{$selected_audit->project_id}});"><i class="a-file-mail"></i> SEND TO PROPERTY CONTACT</a></li>
				                                        <li @if($ehs->crr_approval_type_id == 7) class="uk-active" @endIf ><a onclick="reportActionPDT({{$ehs->id}},7,{{$selected_audit->project_id}});"><i class="a-file-person"></i> PROPERTY VIEWED IN PERSON</a></li>
				                                        <li @if($ehs->crr_approval_type_id == 9) class="uk-active" @endIf ><a onclick="reportActionPDT({{$ehs->id}},9,{{$selected_audit->project_id}});"><i class="a-file-approve"></i> ALL ITEMS RESOLVED</a></li>
				                                        @endIf

												    </ul>
												</div>
						            			<br /><small>EHS #{{$ehs->id}}</small></a>
						            		@else
						            		@if($selected_audit->step_id > 59 && $selected_audit->step_id < 67) <span class="use-hand-cursor" uk-tooltip="title:GENERATE THIS AUDIT'S EHS" onclick="submitNewReportPD({{$selected_audit->audit_id}},2)"><i  class="a-file-plus"></i><br /><small>EHS</small></span>  @else <i class="a-file-fail" uk-tooltip="title:SORRRY, THE AUDIT'S STATUS DOES NOT ALLOW A EHS TO BE GENERATED." ></i><br /><small>EHS</small>@endIf
							            	@endIf
						            	</div>
						            	<div class="uk-width-1-3">
							            	@if(($_8823))
							            	<?php
						            			switch ($_8823->crr_approval_type_id) {
						            				case '1':
						            					$_8823Icon = "a-file-pencil-2"; // draft
						            					break;
						            				case '2':
						            					$_8823Icon = "a-file-clock"; // pending manager review
						            					break;
						            				case '3':
						            					$_8823Icon = "a-file-fail manager-fail attention"; // declined by manager
						            					break;
						            				case '4':
						            					$_8823Icon = "a-file-repeat"; // approved with changes
						            					break;
						            				case '5':
						            					$_8823Icon = "a-file-certified"; // approved
						            					break;
						            				case '6':
						            					$_8823Icon = "a-file-mail"; // Unopened by PM
						            					break;
						            				case '7':
						            					$_8823Icon = "a-file-pen"; // Viewed by a PM
						            					break;
						            				case '9':
						            					$_8823Icon = "a-file-approve"; // All items resolved
						            					break;
						            				default:
						            					$_8823Icon = "a-file-fail";
						            					break;
						            			}
						            		?>
						            		<a class="uk-link-mute" @can('access_manager') href="/report/{{$_8823->id}}" target="report-{{$_8823->id}}" uk-tooltip="title:{{$_8823->status_name()}}" @else onClick="alert('Sorry, this feature is still under development. It will be availble in a future release.');" @endCan ><i class="{{$_8823Icon}}" style="font-size: 30px;"></i></a>

						            			&nbsp; <i class="a-square-right-2 use-hand-cursor" @can('access_manager') id="8823-dropdown-{{$selected_audit->audit_id}}" uk-tooltip title="ACTION" @else onClick="alert('Sorry, this feature is still under development. It will be availble in a future release.');" @endCan></i>
												<div  uk-dropdown="mode:click">
												    @can('access_manager')
												    <ul class="uk-nav uk-dropdown-nav " style="text-align: left !important;">
												        <li @if($_8823->crr_approval_type_id == 1) class="uk-active" @endIf><a onclick="reportActionPDT({{$_8823->id}},1,{{$selected_audit->project_id}});"><i class="a-file-pencil-2"></i> DRAFT</a></li>
				                                        @if($_8823->requires_approval)
				                                        <li @if($_8823->crr_approval_type_id == 2) class="uk-active" @endIf ><a onclick="reportActionPDT({{$_8823->id}},2,{{$selected_audit->project_id}});"><i class="a-file-clock"></i> SEND TO HFA MANAGER REVIEW</a></li>
				                                        
				                                        @can('access_manager')
				                                        <li @if($_8823->crr_approval_type_id == 3) class="uk-active" @endIf ><a onclick="reportActionPDT({{$_8823->id}},3,{{$selected_audit->project_id}});"><i class="a-file-fail"></i> DECLINE</a></li>
				                                        <li @if($_8823->crr_approval_type_id == 4) class="uk-active" @endIf ><a onclick="reportActionPDT({{$_8823->id}},4,{{$selected_audit->project_id}});"><i class="a-file-repeat"></i> APPROVE WITH CHANGES</a></li>
				                                        <li @if($_8823->crr_approval_type_id == 5) class="uk-active" @endIf ><a onclick="reportActionPDT({{$_8823->id}},5,{{$selected_audit->project_id}});"><i class="a-file-certified"></i> APPROVE</a></li>
				                                        @endCan
				                                        @endIf
				                                        @if(($_8823->requires_approval == 1 && $_8823->crr_approval_type_id > 3) || $_8823->requires_approval == 0 || Auth::user()->can('access_manager'))
				                                        <li @if($_8823->crr_approval_type_id == 6) class="uk-active" @endIf ><a onclick="reportActionPDT({{$_8823->id}},6,{{$selected_audit->project_id}});"><i class="a-file-mail"></i> SEND TO PROPERTY CONTACT</a></li>
				                                        <li @if($_8823->crr_approval_type_id == 7) class="uk-active" @endIf ><a onclick="reportActionPDT({{$_8823->id}},7,{{$selected_audit->project_id}});"><i class="a-file-person"></i> PROPERTY VIEWED IN PERSON</a></li>
				                                        <li @if($_8823->crr_approval_type_id == 9) class="uk-active" @endIf ><a onclick="reportActionPDT({{$_8823->id}},9,{{$selected_audit->project_id}});"><i class="a-file-approve"></i> ALL ITEMS RESOLVED</a></li>
				                                        @endIf

												    </ul>
												    @endCan
												</div>
						            			<br /><small>8823 #{{$_8823->id}}</small></a>
						            		@else
						            		@if($selected_audit->step_id > 59 && $selected_audit->step_id < 67) <span class="use-hand-cursor" uk-tooltip="title:GENERATE THIS AUDIT'S 8823" onclick="submitNewReportPD({{$selected_audit->audit_id}},5)"><i  class="a-file-plus"></i><br /><small>8823</small></span>  @else <i class="a-file-fail" uk-tooltip="title:SORRRY, THE AUDIT'S STATUS DOES NOT ALLOW A 8823 TO BE GENERATED." ></i><br /><small>EHS</small>@endIf
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
									<div class="uk-text-center hasdivider uk-margin-top" uk-grid>
						            	<div uk-tooltip="title: CLICK TO ADD A FILE FINDING" class="uk-width-1-3 use-hand-cursor  uk-first-column pd-findings-column" title="" aria-expanded="false" onclick="openFindings(this, {{$selected_audit->audit_id}}, {{$selected_audit->id}}, null, 'file', null,'0');"><i class="a-folder"></i><span class="uk-badge finding-number-pd on-phone-pd @if($correctedFileCount < $fileCount) attention @endIf" uk-tooltip title="{{$correctedFileCount}} / {{$fileCount}} @if($fileCount < 1 || $fileCount > 1) FINDINGS @else FINDING @endIf CORRECTED" aria-expanded="false">{{$fileCount}}</span></div>
						            	<div uk-tooltip="title: CLICK TO ADD A NLT FINDING" class="uk-width-1-3 use-hand-cursor pd-findings-column" title="" aria-expanded="false" onclick="openFindings(this, {{$selected_audit->audit_id}}, {{$selected_audit->id}}, null, 'nlt', null,'0');"><i class="a-booboo"></i><span class="uk-badge finding-number-pd on-phone-pd @if($correctedNltCount < $nltCount) attention @endIf" uk-tooltip title="{{$correctedNltCount}} / {{$nltCount}} @if($nltCount < 1 || $nltCount > 1) FINDINGS @else FINDING @endIf CORRECTED" aria-expanded="false">{{$nltCount}}</span></div>
						            	<div uk-tooltip="title: CLICK TO ADD A LT FINDING" class="uk-width-1-3 use-hand-cursor pd-findings-column" title="" aria-expanded="false" onclick="openFindings(this, {{$selected_audit->audit_id}}, {{$selected_audit->id}}, null, 'lt', null,'0');"><i class="a-skull"></i><span class="uk-badge finding-number-pd on-phone-pd @if($correctedLtCount < $ltCount) attention @endIf" uk-tooltip title="{{$correctedLtCount}} / {{$ltCount}} @if($ltCount < 1 || $ltCount > 1) FINDINGS @else FINDING @endIf CORRECTED" aria-expanded="false">{{$ltCount}}</span></div>
						            </div>

								</div>
								<div class="uk-width-2-5 uk-padding-remove">
									<div class="divider"></div>
									<div class="uk-text-center hasdivider uk-margin-small-top" uk-grid>
										
										<div class="uk-width-1-4 pd-findings-column">
						            		<i class="{{$selected_audit->audit_compliance_icon}} {{$selected_audit->audit_compliance_status}}"  uk-tooltip="title:{{$selected_audit->audit_compliance_status_text}};"></i> @if(!count($selected_audit->audit->findings))<i class="use-hand-cursor a-rotate-left" uk-tooltip title="CLICK TO RERUN AUDIT SELECTION"></i>@endIf
						            	</div>
						            	<div class="uk-width-1-4 pd-findings-column">
						            		<i class="{{$selected_audit->auditor_status_icon}}" uk-tooltip="title:{{$selected_audit->auditor_status_text}}"></i>
						            	</div>
						            	<div class="uk-width-1-4 pd-findings-column">
						            		<i class="{{$selected_audit->message_status_icon}}" uk-tooltip="title:{{$selected_audit->message_status_text}};"></i>
						            	</div>
						            	<div class="uk-width-1-4 pd-findings-column">
						            		<i class="{{$selected_audit->document_status_icon}}" uk-tooltip="title:{{$selected_audit->document_status_text}}"></i>
						            	</div>
						            	
						            	
						            </div>
								</div>
								<div class="uk-width-1-5 iconpadding  pd-findings-column uk-text-center" onclick="dynamicModalLoad('audits/{{$selected_audit->audit_id}}/updateStep?details_refresh=1',0,0,0);">
									
									<i class="{{$selected_audit->step_status_icon}} use-hand-cursor" uk-tooltip title="{{$selected_audit->step_status_text}}" ></i><br />
									<small>UPDATE</small>
									
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
				<h3>{{$project->project_name}}<br /><small>Project {{$project->project_number}} @if($selected_audit->audit_id)| Current Audit {{ $selected_audit->audit_id }}@endif</small></h3>
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
	
</div>


<div id="project-details-buttons" class="project-details-buttons" uk-grid>
	<div class="uk-width-1-1">
		
				<div uk-grid>
					<div class="uk-width-1-4">
						<button id="project-details-button-3" class="uk-button uk-link  active" onclick="projectDetailsInfo({{$project->id}}, 'selections', {{$selected_audit->audit_id}},this);" type="button">
						<i class="a-mobile"></i> <i class="a-folder"></i>
						
					 SELECTIONS</button>
					</div>
					<div class="uk-width-1-4">
						<button id="project-details-button-1" class="uk-button uk-link " onclick="projectDetailsInfo({{$project->id}}, 'compliance', {{$selected_audit->audit_id}}, this);" type="button"><i class="a-circle-checked"></i> COMPLIANCE</button>
					</div>
					<div class="uk-width-1-4">
						<button id="project-details-button-2" class="uk-button uk-link" onclick="projectDetailsInfo({{$project->id}}, 'assignment', {{$selected_audit->audit_id}}, this);" type="button"><i class="a-calendar-person"></i> SCHEDULING</button>
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
		$('#project-details-button-3').trigger("click");
	}
	loadProjectDetailsBuildings( {{$project->id}}, {{$project->id}} ) ;
	UIkit.dropdown('#car-dropdown-{{$selected_audit->audit_id}}', 'mode:click');
});
</script>
