<?php
$lead = $audit->lead_json;

// if($audit->update_cached_audit()){
//   //refreshed - update values.
//   $audit->refresh();
// }
?>

<td id="'audit-c-1-'{{ $audit->audit_id }}" class="uk-text-center audit-td-lead use-hand-cursor" >
	<span id="audit-avatar-badge-{{ $audit->audit_id }}" onClick="openAssignment({{ $audit->project_key }},{{ $audit->audit_id }})" uk-tooltip title="{{ $lead->name }}" aria-expanded="false" class="user-badge-{{ $lead->color }} user-badge-v2 uk-align-center no-float uk-link" style="margin-right: auto !important; margin-left: auto; margin-bottom: 0px; margin-top: 0px; float: none;">
		<span >{{ $lead->initials }}</span>
	</span>
	<span id="'audit-rid-'{{ $audit->audit_id }}" class="uk-align-center" style="margin-right: auto; margin-left: auto; margin-bottom: 0px; margin-top: 0px">
		<small>@if(isset($loop)) {{ $loop->iteration }} @else  <i class="a-star" uk-tooltip title="THIS RECORD WAS UPDATED SINCE YOU LAST REFRESHED YOUR SCREEN" ></i>@endIf </small>
	</span>
</td>
<td id="audit-c-2-{{ $audit->audit_id }}" class="audit-td-project">
	<div class="uk-vertical-align-middle uk-display-inline-block uk-margin-small-top">
		@php /* <span id="audit-i-project-detail-{{ $audit->audit_id }}" onClick="openProjectDetails({{ $audit->audit_id }},{{ $audit->total_buildings }})" uk-tooltip="title: VIEW BUILDINGS AND COMMON AREAS; pos: top-left;" class="uk-link" style="margin-right: 10px; margin-left: 7px; margin-top: 0px !important;"><i class="a-menu uk-text-muted"></i></span> */ @endPhp
	</div>
	<div class="uk-vertical-align-middle uk-display-inline-block use-hand-cursor"  onClick="openProjectDetails({{ $audit->audit_id }},{{ $audit->total_buildings }})" >
		<h3 id="audit-project-name-{{ $audit->audit_id }}" class="uk-margin-bottom-remove uk-link filter-search-project" uk-tooltip title="VIEW BUILDINGS AND COMMON AREAS"><span >{{ $audit->project_ref }}</span></h3>
		<small id="audit-project-aid-{{ $audit->audit_id }}" class="uk-text-muted faded filter-search-project" uk-tooltip title="VIEW BUILDINGS AND COMMON AREAS;">AUDIT <span >{{ $audit->audit_id }}</span></small>
	</div>
</td>
<td class="audit-td-name">
	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
		<i class="a-info-circle uk-text-muted uk-link" onClick="openContactInfo({{ $audit->project_id }})" uk-tooltip title="VIEW CONTACT DETAILS;"></i>
	</div>
	<div class="uk-vertical-align-top uk-display-inline-block @if(isset($audits) && count($audits) < 50)fadetext @endIf use-hand-cursor"  onClick="openProject({{$audit->project_key}},{{$audit->audit_id}});" uk-tooltip title="VIEW AUDIT DETAILS">
		<h3 class="uk-margin-bottom-remove filter-search-pm" >{{$audit->title}}</h3>
		<small class="uk-text-muted faded filter-search-pm" >{{$audit->pm}}</small>
	</div>
</td>
<td class="hasdivider audit-td-address uk-text-truncate">
	<div class="divider"></div>
	@if(null != $audit->address && null != $audit->city && null != $audit->state && null != $audit->zip)
	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
		<a href="https://maps.google.com/maps?q={{ $audit->address }}+{{ $audit->address2 }}" class="uk-link-mute" target="_blank"><i class="a-marker-basic uk-text-muted uk-link" uk-tooltip title="VIEW ON MAP;"></i></a>
	</div>
	<div class="uk-vertical-align-top uk-display-inline-block fullwidthleftpad @if(isset($audits) && count($audits) < 50)fadetext @endIf" >
		<a href="https://maps.google.com/maps?q={{$audit->address}}+{{$audit->address2}}" class="uk-link-mute" target="_blank"><h3 class="uk-margin-bottom-remove filter-search-address">{{$audit->address}}</h3>
			<small class="uk-text-muted faded filter-search-address">@if($audit->city){{$audit->city}}, @endIf {{$audit->state}} {{$audit->zip}} </small>
		</a>
	</div>
	@else
	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
		<i class="a-marker-fail uk-text-muted uk-link" uk-tooltip title="NO ADDRESS"></i>
	</div>
	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
		<span class="uk-text-muted" >NO ADDRESS</span>
	</div>
	@endIf
</td>
<td class="hasdivider audit-td-scheduled">
	<div class="divider"></div>
	<div class="uk-display-inline-block uk-text-center fullwidth" uk-grid>
		<div class="uk-width-1-2" uk-grid>
			@if($audit->inspection_schedule_date !== null)
			<div class="uk-padding-remove uk-align-center" @if($audit->inspection_schedule_date) onClick="" @endIf>
				<h3 class="uk-link" uk-tooltip title="{{ $audit->inspection_schedule_text }}">{{ date('m/d',strtotime($audit->inspection_schedule_date)) }}</h3>
				<div class="dateyear" style="width:50px;">{{ date('Y',strtotime($audit->inspection_schedule_date)) }}</div>
			</div>
			@else
			<div class="uk-width-1-2" onClick="openAssignment({{ $audit->project_key }},{{ $audit->audit_id }})">
				<i class="a-calendar-7 action-needed use-hand-cursor" uk-tooltip title="CLICK TO SCHEDULE AUDIT"></i>
			</div>
			@endIf
		</div>

		<div class="uk-width-1-4 uk-text-center uk-padding-remove" style="margin-top: -4px;" uk-tooltip title="INSPECTING {{ $audit->total_buildings }} @if($audit->total_buildings > 1 || $audit->total_buildings < 1) BUILDINGS @else BUILDING @endIf">{{ $audit->total_buildings }}</div>
		<div class="uk-width-1-4 uk-text-center uk-padding-remove" style="margin-top: -4px;" uk-tooltip title="INSPECTING {{ $audit->total_units }} @if($audit->total_units > 1 || $audit->total_units < 1) UNITS @else UNIT @endIf">{{ $audit->total_units }}</div>
	</div>
</td>
<td class="hasdivider audit-td-due">
	<div class="divider"></div>
	<div class="uk-text-center fullwidth  uk-remove-margin" uk-grid>
		<div class="uk-width-1-3 uk-remove-margin uk-padding-remove audit-list-report-holder" style="overflow: hidden;">
			<div class="audit-list-report-icons">
				@if($audit->car_id)
				<a href="/report/{{ $audit->car_id }}" target="car{{ $audit->car_id }}"><i class="{{ $audit->car_icon }} {{ $audit->car_status }}" uk-tooltip title="{{ $audit->car_status_text }}"></i></a><br /><small>CAR</small>
				@else
				<i  @if($audit->step_id > 59 && $audit->step_id < 67) class="a-file-plus use-hand-cursor" uk-tooltip title="GENERATE THIS AUDIT'S CAR" onclick="submitNewReportAL({{ $audit->audit_id }},1,this)" @else class="a-file-fail gray-text" uk-tooltip title="SORRY, THE AUDIT'S STATUS DOES NOT ALLOW A CAR TO BE GENERATED." @endIf></i><br /><small class="gray-text">CAR</small>
				@endIf
			</div>
		</div>
		<div class="uk-width-1-3  uk-remove-margin uk-padding-remove audit-list-report-holder" style="overflow: hidden;">
			<div class="audit-list-report-icons">
				@if($audit->ehs_id)
				<a href="/report/{{ $audit->ehs_id }}" target="ehs{{ $audit->ehs_id }}"><i class="{{ $audit->ehs_icon }} {{ $audit->ehs_status }}" uk-tooltip title="{{ $audit->ehs_status_text }}"></i></a><br /><small>EHS</small>
				@else
				<i  @if($audit->step_id > 59 && $audit->step_id < 67) class="a-file-plus use-hand-cursor" uk-tooltip title="GENERATE THIS AUDIT'S EHS" onclick="submitNewReportAL({{ $audit->audit_id }},2,this)" @else class="a-file-fail gray-text" uk-tooltip title="SORRY, THE AUDIT'S STATUS DOES NOT ALLOW A EHS TO BE GENERATED." @endIf></i><br /><small class="gray-text">EHS</small>
				@endIf
			</div>
		</div>
		@if(env('APP_ENV') != 'production')
		<div class="uk-width-1-3  uk-remove-margin uk-padding-remove audit-list-report-holder" style="overflow: hidden;">
			<div class="audit-list-report-icons">
				@if($audit->_8823_id)
				<a href="/report/{{ $audit->_8823_id }}" target="_8823{{ $audit->_8823_id }}"><i class="{{ $audit->_8823_icon }} {{ $audit->_8823_status }}" uk-tooltip title="{{ $audit->_8823_status_text }}"></i></a><br /><small>8823</small>
				@else
				<i  @if($auditor_access)@if($audit->step_id > 59 && $audit->step_id < 67) class="a-file-plus use-hand-cursor" uk-tooltip title="GENERATE THIS AUDIT'S 8823" onclick="submitNewReportAL({{ $audit->audit_id }},5,this)" @else class="a-file-fail gray-text" uk-tooltip title="SORRY, THE AUDIT'S STATUS DOES NOT ALLOW A 8823 TO BE GENERATED." @endIf @else class="a-file-fail use-hand-cursor"uk-tooltip title="SORRY, THE 8823 GENERATOR IS NOT AVAILABLE YET."@endif></i><br /><small class="gray-text">8823</small>
				@endIf
			</div>
		</div>
		@endif
	</div>
</td>
<td class="hasdivider">
	<div class="divider"></div>
	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top " uk-grid>
		<div class="uk-width-1-3 use-hand-cursor {{ $audit->file_audit_status }}" uk-tooltip title="{{ $audit->file_audit_status_text }}" onClick="openFindings(this, {{ $audit->audit_id }}, null, null, 'file')">
			<i class="{{ $audit->file_audit_icon }}"></i>
		</div>
		<div class="uk-width-1-3 use-hand-cursor {{ $audit->nlt_audit_status }}" uk-tooltip title="{{ $audit->nlt_audit_status_text }}" onClick="openFindings(this, {{ $audit->audit_id }}, null, null, 'nlt')">
			<i class="{{ $audit->nlt_audit_icon }}"></i>
		</div>
		<div class="uk-width-1-3 use-hand-cursor {{ $audit->lt_audit_status }}" uk-tooltip title="{{ $audit->lt_audit_status_text }}" onClick="openFindings(this, {{ $audit->audit_id }}, null, null, 'lt')">
			<i class="{{ $audit->lt_audit_icon }}"></i>
		</div>
	</div>
</td>
<td class="hasdivider">
	<div class="divider"></div>
	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top " uk-grid>
		<div class="uk-width-1-4 ">
			@if($audit->audit && !$audit->audit->findings->count())
			<i class="a-rotate-left {{ $audit->audit_compliance_status }} use-hand-cursor" onClick="rerunCompliance({{ $audit->audit_id }})" uk-tooltip title="{{ $audit->audit_compliance_status_text }} : YOU CAN CLICK TO RERUN COMPLIANCE SELECTION"></i>
			@else
			<i class="{{ $audit->audit_compliance_icon }} {{ $audit->audit_compliance_status }}" uk-tooltip title="{{ $audit->audit_compliance_status_text }}" ></i>
			@endif
		</div>
		<div class="uk-width-1-4">
			<i  onClick="openAssignment({{ $audit->project_key }},{{ $audit->audit_id }})" class="{{ $audit->auditor_status_icon }} use-hand-cursor {{ $audit->auditor_status }}" uk-tooltip title="{{ $audit->auditor_status_text }}"></i>
		</div>
		<div class="uk-width-1-4">
			<i onClick="openProjectSubtab({{ $audit->project_key }},{{ $audit->audit_id }}, 'communications')" class="{{ $audit->message_status_icon }} use-hand-cursor {{ $audit->message_status }}" uk-tooltip title="{{ $audit->message_status_text }}"></i>
		</div>
		<div class="uk-width-1-4">
			<i onClick="openProjectSubtab({{ $audit->project_key }},{{ $audit->audit_id }}, 'documents')" class="{{ $audit->document_status_icon }} use-hand-cursor {{ $audit->document_status }}" uk-tooltip title="{{ $audit->document_status_text }}"></i>
		</div>

	</div>
</td>
<td>
	<div class="uk-margin-top" uk-grid>
		<div class="uk-width-1-1  uk-padding-remove-top uk-text-center">
			<i class="{{ $audit->step_status_icon }} use-hand-cursor {{ $audit->step_status_text }}" uk-tooltip title="{{ $audit->step_status_text }}" onClick="updateStep({{ $audit->audit_id }})"></i>
		</div>
		<script>
			if(window.onPageAudits !== undefined){
				window.onPageAudits[{{ $audit->audit_id }}] = ['{{ $audit->audit_id }}','{{ $audit->updated_at }}'];

			}
		</script>
	</div>
</td>
