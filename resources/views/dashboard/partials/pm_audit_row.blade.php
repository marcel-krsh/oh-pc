<?php
$lead = $audit->lead_json;

// if($audit->update_cached_audit()){
//   //refreshed - update values.
//   $audit->refresh();
// }
?>

<td id="'audit-c-1-'{{ $audit->audit_id }}" class="uk-text-center audit-td-lead use-hand-cursor" >
	<span id="audit-avatar-badge-{{ $audit->audit_id }}"  uk-tooltip=" delay: 1000" title="YOUR LEAD AUDITOR IS {{ strtoupper($lead->name) }}" aria-expanded="false" class="user-badge-{{ $lead->color }} user-badge-v2 uk-align-center no-float uk-link" style="margin-right: auto !important; margin-left: auto; margin-bottom: 0px; margin-top: 0px; float: none;">
		<span >{{ $lead->initials }}</span>
	</span>
	<span id="'audit-rid-'{{ $audit->audit_id }}" class="uk-align-center" style="margin-right: auto; margin-left: auto; margin-bottom: 0px; margin-top: 0px">
		<small>@if(isset($loop)) {{ $loop->iteration }} @else  <i class="a-star" uk-tooltip=" delay: 1000" title="THIS RECORD WAS UPDATED SINCE YOU LAST REFRESHED YOUR SCREEN" ></i>@endIf </small>
	</span>
</td>
<td id="audit-c-2-{{ $audit->audit_id }}" class="audit-td-project" align="center">
	<?php /*<div class="uk-vertical-align-middle uk-display-inline-block uk-margin-small-top">
		 <span id="audit-i-project-detail-{{ $audit->audit_id }}" onClick="openProjectDetails({{ $audit->audit_id }},{{ $audit->total_buildings }})" uk-tooltip="title: VIEW BUILDINGS AND COMMON AREAS; pos: top-left;" class="uk-link" style="margin-right: 10px; margin-left: 7px; margin-top: 0px !important;"><i class="a-menu uk-text-muted"></i></span> 
	</div>*/ ?>
	<div class="uk-vertical-align-middle uk-display-inline-block use-hand-cursor"  onClick="pmLoadTab('/pm-projects/view/{{$audit->project_key}}/{{$audit->audit_id}}', '4', 1, 1, '', 1, {{$audit->audit_id}});window.selectedProjectKey={{$audit->project_key}};window.selectedAuditId={{$audit->audit_id}}" uk-tooltip=" delay: 1000" title="VIEW AUDIT DETAILS">
		<h3 id="audit-project-name-{{ $audit->audit_id }}" class="uk-margin-bottom-remove uk-link filter-search-project" ><span >{{ $audit->project_ref }}</span></h3>
		<small id="audit-project-aid-{{ $audit->audit_id }}" class="uk-text-muted faded filter-search-project" >AUDIT <span >{{ $audit->audit_id }}</span></small>
	</div>
</td>
<td class="audit-td-name ">
	
	<div class="uk-vertical-align-top use-hand-cursor uk-display-inline-block @if(isset($audits) && count($audits) < 50)fadetext @endIf use-hand-cursor uk-padding-left"  onClick="pmLoadTab('/pm-projects/view/{{$audit->project_key}}/{{$audit->audit_id}}', '4', 1, 1, '', 1, {{$audit->audit_id}});window.selectedProjectKey={{$audit->project_key}};window.selectedAuditId={{$audit->audit_id}}" uk-tooltip=" delay: 1000" title="VIEW AUDIT DETAILS">
		<h3 class="uk-margin-bottom-remove filter-search-pm" >{{$audit->title}}</h3>
		<small class="uk-text-muted faded filter-search-pm" >{{$audit->pm}}</small>
	</div>
</td>
<td class="hasdivider audit-td-address uk-text-truncate" uk-tooltip=" delay: 1000" title="VIEW ON MAP">
	<div class="divider"></div>
	@if(null != $audit->address && null != $audit->city && null != $audit->state && null != $audit->zip)
	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
		<a href="https://maps.google.com/maps?q={{ $audit->address }}+{{ $audit->address2 }}" class="uk-link-mute" target="_blank"><i class="a-marker-basic uk-text-muted uk-link" ></i></a>
	</div>
	<div class="uk-vertical-align-top uk-display-inline-block fullwidthleftpad @if(isset($audits) && count($audits) < 50)fadetext @endIf" >
		<a href="https://maps.google.com/maps?q={{$audit->address}}+{{$audit->address2}}" class="uk-link-mute" target="_blank"><h3 class="uk-margin-bottom-remove filter-search-address">{{$audit->address}}</h3>
			<small class="uk-text-muted faded filter-search-address">@if($audit->city){{$audit->city}}, @endIf {{$audit->state}} {{$audit->zip}} </small>
		</a>
	</div>
	@else
	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
		<i class="a-marker-fail uk-text-muted uk-link" uk-tooltip=" delay: 1000" title="NO ADDRESS"></i>
	</div>
	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
		<span class="uk-text-muted" >NO ADDRESS</span>
	</div>
	@endIf
</td>
<td class="hasdivider audit-td-scheduled">
	<div class="divider"></div>
	<div class="uk-display-inline-block uk-text-center fullwidth" uk-grid>
		<div class="uk-width-1-2 uk-link" uk-grid @if($audit->inspection_schedule_date !== null) onClick="openAssignment({{ $audit->project_key }},{{ $audit->audit_id }})" @endIf>
			@if($audit->inspection_schedule_date !== null)
			<div class="uk-padding-remove uk-align-center" >
				<h3 class="" uk-tooltip=" delay: 1000" title="VIEW AUDIT SCHEDULE">{{ date('m/d',strtotime($audit->inspection_schedule_date)) }}</h3>
				<div class="dateyear" style="width:50px;">{{ date('Y',strtotime($audit->inspection_schedule_date)) }}</div>
			</div>
			@else
			<div class="uk-width-1-2" >
				<i class="a-calendar-7" uk-tooltip=" delay: 1000" title="AUDIT START DATE NOT SET"></i>
			</div>
			@endIf
		</div>

		<div class="uk-width-1-4 uk-text-center uk-padding-remove" style="margin-top: -4px;" uk-tooltip=" delay: 1000" title="INSPECTING {{ $audit->total_buildings }} @if($audit->total_buildings > 1 || $audit->total_buildings < 1) BUILDINGS @else BUILDING @endIf">{{ $audit->total_buildings }}</div>
		<div class="uk-width-1-4 uk-text-center uk-padding-remove" style="margin-top: -4px;" uk-tooltip=" delay: 1000" title="INSPECTING {{ $audit->total_units }} @if($audit->total_units > 1 || $audit->total_units < 1) UNITS @else UNIT @endIf">{{ $audit->total_units }}</div>
	</div>
</td>
<td class="hasdivider audit-td-due">
	<div class="divider"></div>
	<div class="uk-text-center fullwidth  uk-remove-margin" uk-grid>
		<div class="uk-width-1-3 uk-remove-margin uk-padding-remove audit-list-report-holder" style="overflow: hidden;">
			<div class="audit-list-report-icons">
				@if($audit->car_id)
				<a href="/report/{{ $audit->car_id }}" target="car{{ $audit->car_id }}"><i class="{{ $audit->car_icon }} {{ $audit->car_status }}" uk-tooltip=" delay: 1000" title="{{ $audit->car_status_text }}"></i></a><br /><small>CAR</small>
				@else
				<i  class="a-file-fail gray-text" uk-tooltip=" delay: 1000" title="CAR NOT AVAILABLE."></i><br /><small class="gray-text">CAR</small>
				@endIf
			</div>
		</div>
		<div class="uk-width-1-3  uk-remove-margin uk-padding-remove audit-list-report-holder" style="overflow: hidden;">
			<div class="audit-list-report-icons">
				@if($audit->ehs_id)
				<a href="/report/{{ $audit->ehs_id }}" target="ehs{{ $audit->ehs_id }}"><i class="{{ $audit->ehs_icon }} {{ $audit->ehs_status }}" uk-tooltip=" delay: 1000" title="{{ $audit->ehs_status_text }}"></i></a><br /><small>EHS</small>
				@else
				<i   class="a-file-fail gray-text" uk-tooltip=" delay: 1000" title="EHS NOT AVAILALBE"></i><br /><small class="gray-text">EHS</small>
				@endIf
			</div>
		</div>
		@if(env('APP_ENV') != 'production')
		<div class="uk-width-1-3  uk-remove-margin uk-padding-remove audit-list-report-holder" style="overflow: hidden;">
			<div class="audit-list-report-icons">
				@if($audit->_8823_id)
				<a href="/report/{{ $audit->_8823_id }}" target="_8823{{ $audit->_8823_id }}"><i class="{{ $audit->_8823_icon }} {{ $audit->_8823_status }}" uk-tooltip=" delay: 1000" title="{{ $audit->_8823_status_text }}"></i></a><br /><small>8823</small>
				@else
				<i  class="a-file-fail gray-text" uk-tooltip=" delay: 1000" title="8823 NOT AVAILALBE"></i><br /><small class="gray-text">8823</small>
				@endIf
			</div>
		</div>
		@endif
	</div>
</td>
<td class="hasdivider">
	<div class="divider"></div>
	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top " uk-grid>
		<div class="uk-width-1-3 use-hand-cursor {{ $audit->file_audit_status }} uk-link" uk-tooltip=" delay: 1000" title="{{ $audit->file_audit_status_text }}" onClick="openFindings(this, {{ $audit->audit_id }}, null, null, 'file')">
			<i class="{{ $audit->file_audit_icon }} "></i><span class="uk-badge finding-number on-folder @if($audit->unresolved_file_findings_count > 0) attention @endIf" >{{$audit->file_findings_count}}</span>
		</div>
		<div class="uk-width-1-3 use-hand-cursor {{ $audit->nlt_audit_status }} uk-link" uk-tooltip=" delay: 1000" title="{{ $audit->nlt_audit_status_text }}" onClick="openFindings(this, {{ $audit->audit_id }}, null, null, 'nlt')">
			<i class="{{ $audit->nlt_audit_icon }}"></i><span class="uk-badge finding-number on-boo-boo @if($audit->unresolved_nlt_findings_count > 0) attention @endIf" >{{$audit->nlt_findings_count}}</span>
		</div>
		<div class="uk-width-1-3 use-hand-cursor {{ $audit->lt_audit_status }} uk-link" uk-tooltip=" delay: 1000" title="{{ $audit->lt_audit_status_text }}" onClick="openFindings(this, {{ $audit->audit_id }}, null, null, 'lt')">
			<i class="{{ $audit->lt_audit_icon }}"></i><span class="uk-badge finding-number on-death @if($audit->unresolved_lt_findings_count > 0) attention @endIf" >{{$audit->lt_findings_count}}</span>
		</div>
	</div>
</td>
<td class="hasdivider">
	<div class="divider"></div>
	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top " uk-grid>
		
		<div class="uk-width-1-2">

			<i onClick="openProjectSubtab({{ $audit->project_key }},{{ $audit->audit_id }}, 'communications')" class="{{ $audit->message_status_icon }} use-hand-cursor {{ $audit->message_status }}" uk-tooltip=" delay: 1000" title="{{ $audit->message_status_text }}"></i>
		</div>
		<div class="uk-width-1-2">
			<i onClick="openProjectSubtab({{ $audit->project_key }},{{ $audit->audit_id }}, 'documents')" class="{{ $audit->document_status_icon }} use-hand-cursor {{ $audit->document_status }}" uk-tooltip=" delay: 1000" title="{{ $audit->document_status_text }}"></i>
		</div>

	</div>
</td>
<td>
	<div class="uk-margin-top" uk-grid>
		<div class="uk-width-1-1  uk-padding-remove-top uk-text-center use-hand-cursor">
			<i class="a-file-up" uk-tooltip=" delay: 1000" title="UPLOAD DOCUMENTS"></i>
		</div>
		<script>
			if(window.onPageAudits !== undefined){
				window.onPageAudits[{{ $audit->audit_id }}] = ['{{ $audit->audit_id }}','{{ $audit->updated_at }}'];

			}
		</script>
	</div>
</td>
