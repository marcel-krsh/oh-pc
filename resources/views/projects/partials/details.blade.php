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

$fileCount = $selected_audit->file_findings_count;
$correctedFileCount = $fileCount - $selected_audit->unresolved_file_findings_count;
$nltCount = $selected_audit->nlt_findings_count;
$correctedNltCount = $nltCount - $selected_audit->unresolved_nlt_findings_count;

$ltCount = $selected_audit->lt_findings_count;
$correctedLtCount = $fileCount - $selected_audit->unresolved_lt_findings_count;
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

						<div uk-dropdown="mode: click" class="uk-dropdown filter-dropdown uk-dropdown-bottom-right" uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="text-align: left; left: 0px; top: 0px;">
							<form id="auditor_selection">
								<fieldset class="uk-fieldset">
									<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">
										@foreach($auditors as $auditor)
										<label for="user-id-{{ $auditor->id }}">
											<input value="{{ $auditor->id }}" class="uk-radio" id="user-id-{{ $auditor->id }}" name="select-auditor" type="radio" {{ $selected_audit->lead == $auditor->id ? 'checked=checked' : '' }}>
											{{ $auditor->full_name() }}
										</label>
										@endforeach
									</div>
									<div class="uk-margin-remove uk-grid" uk-grid="">
										<div class="uk-width-1-2 uk-first-column">
											<button onclick="changeAuditor(event);" class="uk-button uk-button-primary uk-width-1-1">SAVE</button>
										</div>
										<div class="uk-width-1-2">
											<button onclick="$('#audit-avatar-badge-1').trigger( 'click' );return false;" class="uk-button uk-button-secondary uk-width-1-1"><i class="a-circle-cross"></i> CANCEL</button>
										</div>
									</div>
								</fieldset>
							</form>
						</div>

					</div>
					<div class="uk-width-3-5" style="padding-left:1.2em">
						<h3 id="audit-project-name-1" class="uk-margin-bottom-remove uk-text-align-center" style="font-size: 1.5em; padding-top: .5em;">{{$selected_audit->project_ref}}</h3>
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
											<i class="a-calendar-pencil use-hand-cursor" onclick="openSchedule()" uk-tooltip="title:SCHEDULE USING SCHEDULING BELOW;"></i>
											{{-- project-details-button-2 scrollTo --}}
											@endif

										</div>
									</div>
									<div class="uk-width-1-2 uk-padding-remove">
										<div class="uk-width-1-1" uk-tooltip title="INSPECTING {{$selected_audit->total_buildings}} @if($selected_audit->total_buildings > 1 || $selected_audit->total_buildings) < 1) BUILDINGS @else BUILDING @endIf" style="margin-top: 8px;"><i class="a-buildings" style="font-size: 25px;"></i> : {{$selected_audit->total_buildings}}</div>
										<hr class="uk-width-1-1" style="margin-bottom: 8px; margin-top: 0px" >
										<div class="uk-width-1-1" uk-tooltip title="INSPECTING {{$selected_audit->total_units}} @if($selected_audit->total_units > 1 || $selected_audit->total_units < 1) UNITS @else UNIT @endIf"><i class="a-buildings-2" style="font-size: 25px;"></i> : {{$selected_audit->total_units}}</div>

									</div>

								</div>
							</div>
							<div class="uk-width-3-5 uk-padding-remove">
								<div class="divider"></div>
								<div class="uk-text-center hasdivider uk-margin-small-top" uk-grid>

									<div class="uk-width-1-3">
										
										<?php
												$carIcon = $selected_audit->car_icon;
												$ehsIcon = $selected_audit->ehs_icon;
												$_8823Icon = $selected_audit->_8823_icon;
												$carId = $selected_audit->car_id;
												$ehsId = $selected_audit->ehs_id;
												$_8823Id = $selected_audit->_8823_id;
												$carStatus = $selected_audit->car_status_text;
												$ehsStatus = $selected_audit->ehs_status_text;
												$_8823Status = $selected_audit->_8823_status_text;
												$carApprovalTypeId = $selected_audit->car_approval_type_id;
												$ehsApprovalTypeId = $selected_audit->ehs_approval_type_id;
												$_8823ApprovalTypeId = $selected_audit->_8823_approval_type_id;
											  ?>@if(($carIcon))
											  <a class="uk-link-mute" href="/report/{{$carId}}" target="report-{{$carId}}" uk-tooltip="title:VIEW CAR {{$carId}} : {{strtoupper($carStatus)}}"><i class="{{$carIcon}}" style="font-size: 30px;"></i></a> &nbsp; <i class="a-square-right-2 use-hand-cursor" id="car-dropdown-{{$selected_audit->audit_id}}" uk-tooltip title="ACTION"></i>
											  <div  uk-dropdown="mode:click">
											  	<ul class="uk-nav uk-dropdown-nav " style="text-align: left !important;">
											  		<li @if($carApprovalTypeId == 1) class="uk-active" @endIf><a onclick="reportActionPDT({{$carId}},1,{{$selected_audit->project_id}});"><i class="a-file-pencil-2"></i> DRAFT</a></li>
											  		
											  		<li @if($carApprovalTypeId == 2) class="uk-active" @endIf ><a onclick="reportActionPDT({{$carId}},2,{{$selected_audit->project_id}});"><i class="a-file-clock"></i> SEND TO HFA MANAGER REVIEW</a></li>
											  		
											  		@if($manager_access)
											  		<li @if($carApprovalTypeId == 3) class="uk-active" @endIf ><a onclick="reportActionPDT({{$carId}},3,{{$selected_audit->project_id}});"><i class="a-file-fail"></i> DECLINE</a></li>
											  		<li @if($carApprovalTypeId == 4) class="uk-active" @endIf ><a onclick="reportActionPDT({{$carId}},4,{{$selected_audit->project_id}});"><i class="a-file-repeat"></i> APPROVE WITH CHANGES</a></li>
											  		<li @if($carApprovalTypeId == 5) class="uk-active" @endIf ><a onclick="reportActionPDT({{$carId}},5,{{$selected_audit->project_id}});"><i class="a-file-certified"></i> APPROVE</a></li>
											  		@endIf
											  		@if(($carApprovalTypeId > 3) || Auth::user()->can('access_manager'))
											  		<li @if($carApprovalTypeId == 6) class="uk-active" @endIf ><a onclick="reportActionPDT({{$carId}},6,{{$selected_audit->project_id}});"><i class="a-file-mail"></i> SEND TO PROPERTY CONTACT</a></li>
											  		<li @if($carApprovalTypeId == 7) class="uk-active" @endIf ><a onclick="reportActionPDT({{$carId}},7,{{$selected_audit->project_id}});"><i class="a-file-person"></i> PROPERTY VIEWED IN PERSON</a></li>
											  		<li @if($carApprovalTypeId == 9) class="uk-active" @endIf ><a onclick="reportActionPDT({{$carId}},9,{{$selected_audit->project_id}});"><i class="a-file-approve"></i> ALL ITEMS RESOLVED</a></li>
											  		@endIf

											  	</ul>
											  </div><br /><small>CAR #{{$carId}}</small>

											  @else
											  <i  @if($selected_audit->step_id > 59 && $selected_audit->step_id < 67) class="a-file-plus" uk-tooltip="title:GENERATE THIS AUDIT'S CAR" onclick="submitNewReportPD({{$selected_audit->audit_id}},1)" @else class="a-file-fail" uk-tooltip="title:SORRY, THE AUDIT'S STATUS DOES NOT ALLOW A CAR TO BE GENERATED." @endIf></i><br /><small>CAR</small>
											  @endIf
											</div><div class="uk-width-1-3">
												@if(($ehsIcon))
												
											  <a class="uk-link-mute" href="/report/{{$ehsId}}" target="report-{{$ehsId}}" uk-tooltip="title:{{$ehsStatus}}"><i class="{{$ehsIcon}}" style="font-size: 30px;" ></i></a>

											  &nbsp; <i class="a-square-right-2 use-hand-cursor" id="ehs-dropdown-{{$selected_audit->audit_id}}" uk-tooltip title="ACTION"></i>
											  <div  uk-dropdown="mode:click">
											  	<ul class="uk-nav uk-dropdown-nav " style="text-align: left !important;">
											  		<li @if($ehsApprovalTypeId == 1) class="uk-active" @endIf><a onclick="reportActionPDT({{$ehsId}},1,{{$selected_audit->project_id}});"><i class="a-file-pencil-2"></i> DRAFT</a></li>
											  		
											  		<li @if($ehsApprovalTypeId == 2) class="uk-active" @endIf ><a onclick="reportActionPDT({{$ehsId}},2,{{$selected_audit->project_id}});"><i class="a-file-clock"></i> SEND TO HFA MANAGER REVIEW</a></li>

											  		@if($manager_access)
											  		<li @if($ehsApprovalTypeId == 3) class="uk-active" @endIf ><a onclick="reportActionPDT({{$ehsId}},3,{{$selected_audit->project_id}});"><i class="a-file-fail"></i> DECLINE</a></li>
											  		<li @if($ehsApprovalTypeId == 4) class="uk-active" @endIf ><a onclick="reportActionPDT({{$ehsId}},4,{{$selected_audit->project_id}});"><i class="a-file-repeat"></i> APPROVE WITH CHANGES</a></li>
											  		<li @if($ehsApprovalTypeId == 5) class="uk-active" @endIf ><a onclick="reportActionPDT({{$ehsId}},5,{{$selected_audit->project_id}});"><i class="a-file-certified"></i> APPROVE</a></li>
											  		@endIf
											  		
											  		@if(($ehsApprovalTypeId > 3) || Auth::user()->can('access_manager'))
											  		<li @if($ehsApprovalTypeId == 6) class="uk-active" @endIf ><a onclick="reportActionPDT({{$ehsId}},6,{{$selected_audit->project_id}});"><i class="a-file-mail"></i> SEND TO PROPERTY CONTACT</a></li>
											  		<li @if($ehsApprovalTypeId == 7) class="uk-active" @endIf ><a onclick="reportActionPDT({{$ehsId}},7,{{$selected_audit->project_id}});"><i class="a-file-person"></i> PROPERTY VIEWED IN PERSON</a></li>
											  		<li @if($ehsApprovalTypeId == 9) class="uk-active" @endIf ><a onclick="reportActionPDT({{$ehsId}},9,{{$selected_audit->project_id}});"><i class="a-file-approve"></i> ALL ITEMS RESOLVED</a></li>
											  		@endIf

											  	</ul>
											  </div>
											  <br /><small>EHS #{{$ehsId}}</small></a>
											  @else
											  @if($selected_audit->step_id > 59 && $selected_audit->step_id < 67) <span class="use-hand-cursor" uk-tooltip="title:GENERATE THIS AUDIT'S EHS" onclick="submitNewReportPD({{$selected_audit->audit_id}},2)"><i  class="a-file-plus"></i><br /><small>EHS</small></span>  @else <i class="a-file-fail" uk-tooltip="title:SORRY, THE AUDIT'S STATUS DOES NOT ALLOW A EHS TO BE GENERATED." ></i><br /><small>EHS</small>@endIf
											  @endIf
											</div>
											<div class="uk-width-1-3">
												@if(env('APP_ENV') != 'production')
												@if(($_8823Icon))
												
											  <a class="uk-link-mute" @if($manager_access) href="/report/{{$_8823Id}}" target="report-{{$_8823Id}}" uk-tooltip="title:{{$_8823Status}}" @else onClick="alert('Sorry, this feature is still under development. It will be availble in a future release.');" @endIf ><i class="{{$_8823Icon}}" style="font-size: 30px;"></i></a>

											  &nbsp; <i class="a-square-right-2 use-hand-cursor" @if($manager_access) id="8823-dropdown-{{$selected_audit->audit_id}}" uk-tooltip title="ACTION" @else onClick="alert('Sorry, this feature is still under development. It will be availble in a future release.');" @endIf></i>
											  <div  uk-dropdown="mode:click">
											  	@if($manager_access)
											  	<ul class="uk-nav uk-dropdown-nav " style="text-align: left !important;">
											  		<li @if($_8823ApprovalTypeId == 1) class="uk-active" @endIf><a onclick="reportActionPDT({{$_8823Id}},1,{{$selected_audit->project_id}});"><i class="a-file-pencil-2"></i> DRAFT</a></li>
											  		
											  		<li @if($_8823ApprovalTypeId == 2) class="uk-active" @endIf ><a onclick="reportActionPDT({{$_8823Id}},2,{{$selected_audit->project_id}});"><i class="a-file-clock"></i> SEND TO HFA MANAGER REVIEW</a></li>

											  		@if($manager_access)
											  		<li @if($_8823ApprovalTypeId == 3) class="uk-active" @endIf ><a onclick="reportActionPDT({{$_8823Id}},3,{{$selected_audit->project_id}});"><i class="a-file-fail"></i> DECLINE</a></li>
											  		<li @if($_8823ApprovalTypeId == 4) class="uk-active" @endIf ><a onclick="reportActionPDT({{$_8823Id}},4,{{$selected_audit->project_id}});"><i class="a-file-repeat"></i> APPROVE WITH CHANGES</a></li>
											  		<li @if($_8823ApprovalTypeId == 5) class="uk-active" @endIf ><a onclick="reportActionPDT({{$_8823Id}},5,{{$selected_audit->project_id}});"><i class="a-file-certified"></i> APPROVE</a></li>
											  		@endIf
											  		
											  		@if(($_8823ApprovalTypeId > 3) ||  Auth::user()->can('access_manager'))
											  		<li @if($_8823ApprovalTypeId == 6) class="uk-active" @endIf ><a onclick="reportActionPDT({{$_8823Id}},6,{{$selected_audit->project_id}});"><i class="a-file-mail"></i> SEND TO PROPERTY CONTACT</a></li>
											  		<li @if($_8823ApprovalTypeId == 7) class="uk-active" @endIf ><a onclick="reportActionPDT({{$_8823Id}},7,{{$selected_audit->project_id}});"><i class="a-file-person"></i> PROPERTY VIEWED IN PERSON</a></li>
											  		<li @if($_8823ApprovalTypeId == 9) class="uk-active" @endIf ><a onclick="reportActionPDT({{$_8823Id}},9,{{$selected_audit->project_id}});"><i class="a-file-approve"></i> ALL ITEMS RESOLVED</a></li>
											  		@endIf

											  	</ul>
											  	@endIf
											  </div>
											  <br /><small>8823 #{{$_8823Id}}</small></a>
											  @else
											  @if($selected_audit->step_id > 59 && $selected_audit->step_id < 67) <span class="use-hand-cursor" uk-tooltip="title:GENERATE THIS AUDIT'S 8823" onclick="submitNewReportPD({{$selected_audit->audit_id}},5)"><i  class="a-file-plus"></i><br /><small>8823</small></span>  @else <i class="a-file-fail" uk-tooltip="title:SORRY, THE AUDIT'S STATUS DOES NOT ALLOW A 8823 TO BE GENERATED." ></i><br /><small>EHS</small>@endIf
											  @endIf
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
											<div uk-tooltip="title: CLICK TO ADD A FILE FINDING" class="uk-width-1-3 use-hand-cursor  uk-first-column pd-findings-column" title="" aria-expanded="false" onclick="openFindings(this, {{$selected_audit->audit_id}}, {{$selected_audit->id}}, null, 'file', null,'0');" ><i class="a-folder"></i><span class="uk-badge finding-number-pd on-phone-pd @if($correctedFileCount < $fileCount) attention @endIf" uk-tooltip title="{{$correctedFileCount}} / {{$fileCount}} @if($fileCount < 1 || $fileCount > 1) FINDINGS @else FINDING @endIf CORRECTED" aria-expanded="false">{{$fileCount}}</span></div>

											<div  uk-tooltip="title: CLICK TO ADD A NLT FINDING" class="uk-width-1-3 use-hand-cursor pd-findings-column" title="" aria-expanded="false" onclick="openFindings(this, {{$selected_audit->audit_id}}, {{$selected_audit->id}}, null, 'nlt', null,'0');" ><i class="a-booboo"></i><span class="uk-badge finding-number-pd on-phone-pd @if($correctedNltCount < $nltCount) attention @endIf" uk-tooltip title="{{$correctedNltCount}} / {{$nltCount}} @if($nltCount < 1 || $nltCount > 1) FINDINGS @else FINDING @endIf CORRECTED" aria-expanded="false">{{$nltCount}}</span></div>

											<div  uk-tooltip="title: CLICK TO ADD A LT FINDING" class="uk-width-1-3 use-hand-cursor pd-findings-column" title="" aria-expanded="false" onclick="openFindings(this, {{$selected_audit->audit_id}}, {{$selected_audit->id}}, null, 'lt', null,'0');"><i class="a-skull"></i><span class="uk-badge finding-number-pd on-phone-pd @if($correctedLtCount < $ltCount) attention @endIf" uk-tooltip title="{{$correctedLtCount}} / {{$ltCount}} @if($ltCount < 1 || $ltCount > 1) FINDINGS @else FINDING @endIf CORRECTED" aria-expanded="false">{{$ltCount}}</span></div>
										</div>


									</div>
									<div class="uk-width-2-5 uk-padding-remove">
										<div class="divider"></div>

										<div class="uk-text-center hasdivider uk-margin-small-top" uk-grid>

											<div class="uk-width-1-4 pd-findings-column">
												<i onclick="openCompliance()" class="{{$selected_audit->audit_compliance_icon}} {{$selected_audit->audit_compliance_status}} use-hand-cursor"  uk-tooltip="title:{{$selected_audit->audit_compliance_status_text}};"></i> @if(!$selected_audit->file_findings_count || !$selected_audit->lt_findings_count || !$selected_audit->nlt_findings_count))<i class="use-hand-cursor a-rotate-left" uk-tooltip title="CLICK TO RERUN AUDIT SELECTION"></i>@endIf
											</div>
											<div class="uk-width-1-4 pd-findings-column">
												<i class="{{$selected_audit->auditor_status_icon}}" uk-tooltip="title:{{$selected_audit->auditor_status_text}}"></i>
											</div>
											<div class="uk-width-1-4 pd-findings-column">
												<i onclick="openCommunication()" class="{{$selected_audit->message_status_icon}} use-hand-cursor" uk-tooltip="title:{{$selected_audit->message_status_text}};"></i>
											</div>
											<div class="uk-width-1-4 pd-findings-column">
												<i onclick="openDocuments()" class="{{$selected_audit->document_status_icon}} use-hand-cursor" uk-tooltip="title:{{$selected_audit->document_status_text}}"></i>
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
					<h3>{{$selected_audit->title}}<br /><small>Project {{$selected_audit->project_ref}} @if($selected_audit->audit_id)| Current Audit {{ $selected_audit->audit_id }}@endif</small></h3>
				</div>
				<div class="uk-width-1-3">
					<div class="audit-info" style="width: 80%;float: left;">
					</div>
					<div class="audit-refresh">
						<a onclick="refresh_details({{ $selected_audit->project_id }}, {{ $selected_audit->audit_id }});" style="padding: 10px;border-radius: 5px;width: 25px;float: left;height: 25px;" href="javascript:void(0);" class="btn btn-refresh"><i class="a-rotate-right-2 iheader" style="font-size: 25px;color: #000;"></i></a>
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
					<button id="project-details-button-3" class="uk-button uk-link  active" onclick="projectDetailsInfo({{$selected_audit->project_id}}, 'selections', {{$selected_audit->audit_id}},this);" type="button">
						<i class="a-mobile"></i> <i class="a-folder"></i>

					SELECTIONS</button>
				</div>
				<div class="uk-width-1-4">
					<button id="project-details-button-1" class="uk-button uk-link " onclick="projectDetailsInfo({{$selected_audit->project_id}}, 'compliance', {{$selected_audit->audit_id}}, this);" type="button"><i class="a-circle-checked"></i> COMPLIANCE</button>
				</div>
				<div class="uk-width-1-4">
					<button id="project-details-button-2" class="uk-button uk-link" onclick="projectDetailsInfo({{$selected_audit->project_id}}, 'assignment', {{$selected_audit->audit_id}}, this);" type="button"><i class="a-calendar-person"></i> SCHEDULING</button>
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
    	$.post("/session/project.{{$selected_audit->project_id}}.selectedaudit/"+nextAudit, {
    		'_token' : '{{ csrf_token() }}'
    	}, function(data) {
    		loadTab('{{ route('project.details', $selected_audit->project_id) }}', '1', 0, 0, 'project-',1);

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
    			loadTab('{{ route('project.details', $selected_audit->project_id) }}', '1', 0, 0, 'project-',1);

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


		$(document ).ready(function() {
			if($('#project-details-info-container').html() == ''){
				$('#project-details-button-3').trigger("click");
			}
			if(window.subtab == 'communications') {
				openCommunication();
				window.subtab = '';
			} else if(window.subtab == 'documents') {
				openDocuments();
				window.subtab = '';
			}
			loadProjectDetailsBuildings( {{$selected_audit->project_id}}, {{$selected_audit->project_id}} ) ;
			UIkit.dropdown('#car-dropdown-{{$selected_audit->audit_id}}', 'mode:click');

		});


		function openSchedule() {
			$('#project-details-button-2').trigger('click');
			$('html, body').animate({
				scrollTop: 400
			}, 1000);
		}

		function openCommunication() {
			$('#project-detail-tab-2').trigger('click');
		}

		function openDocuments() {
			$('#project-detail-tab-3').trigger('click');
		}

		function openCompliance() {
			$('#project-details-button-1').trigger('click');
			$('html, body').animate({
				scrollTop: 400
			}, 1000);
		}

		function changeAuditor(e)
		{
			e.preventDefault();
			selected_auditor = $('input[name=select-auditor]:checked').val();
			console.log(selected_auditor);

			jQuery.ajax({
				url: "{{ url("audit/swap-auditor/" . $selected_audit->id) }}",
				method: 'post',
				data: {
					selected_auditor: selected_auditor,
					audit_id: "{{ $selected_audit->audit_id }}",
					'_token' : '{{ csrf_token() }}'
				},
				success: function(data){
					if(data == 1) {
						console.log(data);
						$('#detail-tab-4').trigger( 'click' );
						{{-- loadTab('{{ route('dashboard.audits') }}','1','','','',1); --}}
					} else {
						UIkit.notification({
							message: data,
							status: 'danger',
							pos: 'top-right',
							timeout: 5000
						});
					}
				}
			});

		}
	</script>
