<template class="uk-hidden" id="inspection-left-template">
    <div class="inspection-menu">
    </div>
</template>

<template class="uk-hidden" id="inspection-menu-item-template">
    <button class="uk-button uk-link menuStatus" onclick="switchInspectionMenu('menuAction', 'menuLevel', 'menuTarget', 'menuAudit', 'menuBuilding', 'menuUnit');" style="menuStyle"><i class="menuIcon"></i> menuName</button>
</template>

<template class="uk-hidden" id="inspection-comment-reply-template">
	<div class="uk-width-1-1 uk-margin-remove inspec-tools-tab-finding-comment">
		<div uk-grid>
			<div class="uk-width-1-4">
				<i class="tplCommentTypeIcon"></i> tplCommentType
			</div>
			<div class="uk-width-3-4 borderedcomment">
				<p>tplCommentCreatedAt: By tplCommentUserName<br />
					<span class="finding-comment">tplCommentContent</span>
				</p>
				<button class="uk-button inspec-tools-tab-finding-reply uk-link">
					<i class="a-comment-pencil"></i> REPLY
			    </button>
			</div>
		</div>
	</div>
</template>

<template class="uk-hidden" id="inspection-comment-template">
	<li class="comment-type">
		<div class="inspec-tools-tab-finding tplCommentStatus" uk-grid>
			<div class="uk-width-1-1" style="padding-top: 15px;">
				<div uk-grid>
	    			<div class="uk-width-1-4">
	    				<i class="tplCommentTypeIcon"></i> tplCommentType<br />
	    				<span class="auditinfo">AUDIT tplCommentAuditId</span><br />
	    				tplCommentResolve
	    			</div>
	    			<div class="uk-width-3-4 bordered">
	    				<p>tplCommentCreatedAt: By tplCommentUserName<br />
	    				tplCommentContent</p>
	    			</div>
	    		</div>
	    	</div>

	    	tplCommentReplies

	    	<div class="uk-width-1-1 uk-margin-remove">
	    		<div uk-grid>
	    			tplCommentActions
	    		</div>
	    	</div>
		</div>
	</li>
</template>

<template class="uk-hidden" id="inspection-areas-template">
    <div class="inspection-areas uk-height-large uk-height-max-large uk-panel uk-panel-scrollable sortable" uk-sortable="handle: .uk-sortable-inspec-area;" data-context="areaContext">
    </div>
</template>

<template class="uk-hidden" id="inspection-area-template">
	    <div id="inspection-areaContext-area-r-areaRowId" class="inspection-area uk-flex uk-flex-row areaStatus" style="padding:6px 0 0 0;" data-context="areaContext" data-audit="areaDataAudit" data-building="areaDataBuilding" data-area="areaDataArea" data-amenity="areaDataAmenity">
	    	<div class="uk-inline uk-sortable-inspec-area" style="min-width: 16px; padding: 0 3px;">
				<div class="linespattern"></div>
				<span id="" class="uk-position-bottom-center colored"><small><span class="rowindex" style="display:none;">areaRowId</span></small></span>
			</div>
	    	<div class="uk-inline uk-padding-remove" style="margin-top:10px; ">
    			<div class="area-avatar">
					<div id="auditor-areaAuditorIdareaDataAuditareaDataBuildingareaDataAreaareaDataAmenity" uk-tooltip="pos:top-left;title:areaAuditorName;" title="" aria-expanded="false" class="user-badge auditor-badge-areaAuditorColor no-float use-hand-cursor" onclick="assignAuditor(areaDataAudit, areaDataBuilding, areaDataArea, areaDataAmenity, 'auditor-areaAuditorIdareaDataAuditareaDataBuildingareaDataAreaareaDataAmenity');">
						areaAuditorInitials
					</div>
				</div>
			</div>
    		<div class="uk-inline uk-padding-remove" style="margin-top:7px; flex:140px;">
    			<i id="completed-areaDataAuditareaDataBuildingareaDataAreaareaDataAmenity" class="areaCompletedIcon completion-icon use-hand-cursor" uk-tooltip="title:CLICK TO COMPLETE" onclick="markAmenityComplete(areaDataAudit, areaDataBuilding, areaDataArea, areaDataAmenity, 'completed-areaDataAuditareaDataBuildingareaDataAreaareaDataAmenity')"></i>
    			<div class="area-name">
					areaName
				</div>
			</div>
    		<div class="uk-inline uk-padding-remove">
    			<div class="findings-icon uk-inline areaNLTStatus" onclick="openFindings(this, areaDataAudit, areaDataBuilding, areaDataArea, 'nlt', areaDataAmenity);">
					<i class="a-booboo"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
					</div>
				</div>
				<div class="findings-icon uk-inline areaLTStatus" onclick="openFindings(this, areaDataAudit, areaDataBuilding, areaDataArea, 'lt', areaDataAmenity);">
					<i class="a-skull"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
					</div>
				</div>
				<div class="findings-icon uk-inline areaSDStatus" style="display:none">
					<i class="a-bell-2"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
					</div>
				</div>
			</div>
			<div class="uk-inline" style="padding: 0 15px; display:none">
				<div class="findings-icon uk-inline areaPicStatus">
					<i class="a-picture"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
					</div>
				</div>
				<div class="findings-icon uk-inline areaCommentStatus" style="display:none">
					<i class="a-comment-text"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
					</div>
				</div>
			</div>
			<div class="uk-inline uk-padding-remove">	
				<div class="findings-icon uk-inline areaCopyStatus" onclick="copyAmenity('inspection-areaContext-area-r-areaRowId', areaDataAudit, areaDataBuilding, areaDataArea, areaDataAmenity);">
					<i class="a-file-copy-2"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
					</div>
				</div>
				<div class="findings-icon uk-inline areaTrashStatus" onclick="deleteAmenity('inspection-areaContext-area-r-areaRowId', areaDataAudit, areaDataBuilding, areaDataArea, areaDataAmenity, areaDataHasFindings);">
					<i class="a-trash-4"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">-</span>
					</div>
				</div>
			</div> 
	    </div>
</template>

<template class="uk-hidden" id="inspection-tools-template">
    <div class="inspection-tools"  uk-grid>
    	<div class="inspection-tools-top uk-width-1-1">
    		<div uk-grid>
    			<div class="uk-width-1-3">
    				
    			</div>
    			<div class="uk-width-1-3 uk-text-right" hidden>
    				<i class="a-horizontal-expand"></i>
    			</div>
    		</div>
    	</div>
    	<div class="inspection-tools-tabs uk-width-1-1" uk-filter="target: .js-filter-comments">
    		<ul class="uk-subnav uk-subnav-pill" style="display:none">
			    <li class="uk-badge use-hand-cursor" uk-filter-control=".comment-type-finding">FINDINGS</li>
			    <li class="uk-badge use-hand-cursor" uk-filter-control=".comment-type-comment">COMMENTS</li>
			    <li class="uk-badge use-hand-cursor" uk-filter-control=".comment-type-photo">PHOTOS</li>
			    <li class="uk-badge use-hand-cursor" uk-filter-control=".comment-type-file">DOCUMENTS</li>
			    <li class="uk-badge use-hand-cursor" uk-filter-control=".comment-type-followup">FOLLOW UPS</li>
			</ul>

			<div style="color:#bbb; display:none">
	    		<i class="fas fa-filter"></i> FILTER FINDINGS
	    	</div>

	    	<img src="images/fpo_finding.png" style="width: 100%;">
	

			<ul class="uk-margin js-filter-comments inspec-tools-tab-findings-container uk-panel uk-panel-scrollable uk-height-large uk-height-max-large">
			</ul>
    	</div>
    </div>
</template>
<div id="audits" class="uk-no-margin-top" uk-grid>
	<div class="uk-margin-remove-top uk-width-1-1" uk-grid>
		<div id="auditsfilters" class="uk-width-1-1 uk-margin-top">
			<div class="uk-align-right uk-label  uk-margin-top uk-margin-right">{{count($audits)}}  Audits </div>
			@if(isset($auditFilterMineOnly) && $auditFilterMineOnly == 1)
			<div id="audit-filter-mine" class="uk-badge uk-text-right@s badge-filter">
				@can('access_auditor')
				<a onClick="filterAudits('audit-my-audits',0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>MY AUDITS ONLY</span></a>
				@else
				@endcan
			</div>
			@endif
			@if(isset($auditFilterProjectId) && $auditFilterProjectId != 0)
			<div id="audit-filter-project" class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('filter-search-project');" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>PROJECT/AUDIT ID "{{$auditFilterProjectId}}"</span></a>
			</div>
			@endif
			@if(isset($auditFilterProjectName) && $auditFilterProjectName != '')
			<div id="audit-filter-name" class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('filter-search-pm', '');" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>PROJECT/PM NAME "{{$auditFilterProjectName}}"</span></a>
			</div>
			@endif
			@if(isset($auditFilterAddress) && $auditFilterAddress != '')
			<div id="audit-filter-address" class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('filter-search-address', '');" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>ADDRESS "{{$auditFilterAddress}}"</span></a>
			</div>
			@endif

			@foreach($steps as $step)
				@if(session($step->session_name) == 1)
				<div id="audit-filter-step" class="uk-badge uk-text-right@s badge-filter">
					<a onClick="filterAudits('{{$step->session_name}}', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>STEP "{{$step->name}}"</span></a>
				</div>
				@endif
			@endforeach

			@if(isset($auditFilterComplianceRR) && $auditFilterComplianceRR != '0')
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('compliance-status-rr', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>UNITS REQUIRE REVIEW</span></a>
			</div>
			@endif

			@if(isset($auditFilterComplianceNC) && $auditFilterComplianceNC != '0')
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('compliance-status-nc', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>NOT COMPLIANT</span></a>
			</div>
			@endif

			@if(isset($auditFilterComplianceC) && $auditFilterComplianceC != '0')
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('compliance-status-c', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>COMPLIANT</span></a>
			</div>
			@endif

			@if(session('file-audit-status-h') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('file-audit-status-h', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS FILE AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(session('file-audit-status-r') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('file-audit-status-r', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS RESOLVED FILE AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(session('file-audit-status-ar') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('file-audit-status-ar', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS ACTION REQUIRED FILE AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(session('file-audit-status-c') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('file-audit-status-c', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS CRITICAL FILE AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(session('file-audit-status-nf') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('file-audit-status-nf', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>DOES NOT HAVE FILE AUDIT FINDINGS</span></a>
			</div>
			@endif


			@if(session('nlt-audit-status-h') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('nlt-audit-status-h', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS NLT AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(session('nlt-audit-status-r') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('nlt-audit-status-r', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS RESOLVED NLT AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(session('nlt-audit-status-ar') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('nlt-audit-status-ar', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS ACTION REQUIRED NLT AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(session('nlt-audit-status-c') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('nlt-audit-status-c', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS CRITICAL NLT AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(session('nlt-audit-status-nf') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('nlt-audit-status-nf', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>DOES NOT HAVE NLT AUDIT FINDINGS</span></a>
			</div>
			@endif


			@if(session('lt-audit-status-h') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('lt-audit-status-h', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS LT AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(session('lt-audit-status-r') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('lt-audit-status-r', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS RESOLVED LT AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(session('lt-audit-status-ar') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('lt-audit-status-ar', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS ACTION REQUIRED LT AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(session('lt-audit-status-c') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('lt-audit-status-c', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS CRITICAL LT AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(session('lt-audit-status-nf') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('lt-audit-status-nf', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>DOES NOT HAVE LT AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(isset($auditFilterInspection) && $auditFilterInspection != '')
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('total_inspection_amount', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>{{$auditFilterInspection}}</span></a>
			</div>
			@endif

			@if(session('schedule_assignment_unassigned') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('schedule_assignment_unassigned', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>AUDITS WITH UNASSIGNED AMENITIES</span></a>
			</div>
			@endif

			@if(session('schedule_assignment_not_enough') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('schedule_assignment_not_enough', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>AUDITS WITH NOT ENOUGH HOURS SCHEDULED</span></a>
			</div>
			@endif

			@if(session('schedule_assignment_too_many') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="filterAudits('schedule_assignment_too_many', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>AUDITS WITH TOO MANY HOURS SCHEDULED</span></a>
			</div>
			@endif
			

			<div id="audit-filter-date" class="uk-badge uk-text-right@s badge-filter" hidden>
				<a onClick="loadTab('{{ route('dashboard.audits', ['filter' => 'yes']) }}', '1');" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>FILTER HERE</span></a>
			</div>
			@if(session()->has('audit-message'))
			@if(session('audit-message') != '')
				<div class="uk-badge uk-text-right@s badge-filter">
					<a onClick="applyFilter('audit-message',null);" class="uk-dark uk-light">
						<i class="a-circle-cross"></i> 
						@switch(session('audit-message'))
						    @case(0)
						        <span>ALL MESSAGES</span>
						        @break
						    @case(1)
						        <span>UNREAD</span>
						        @break
						    @case(2)
						        <span>DOES NOT HAVE MESSAGES</span>
						        @break
						    @default
						        <span>ALL MESSAGES</span>
						@endswitch
					</a>
				</div>
			@endif
			@endif
			@if(session()->has('audit-mymessage'))
			@if(session('audit-mymessage') == 1)
				<div class="uk-badge uk-text-right@s badge-filter">
					<a onClick="applyFilter('audit-mymessage',null);" class="uk-dark uk-light">
						<i class="a-circle-cross"></i> 
						<span>MESSAGES FOR ME</span>
					</a>
				</div>
			@endif
			@endif

			@if(is_array(session('assignment-auditor')))
			@php
			$assigned_auditors = '';
			$assigned_auditors_hover_text = '';
			$counter = 0;
			foreach($auditors_array as $auditor){
				if(in_array($auditor['user_id'], session('assignment-auditor'))){
				 	$counter++;
				 	if($counter < 3){
				 		if($assigned_auditors == ''){
							$assigned_auditors = $auditor['name'];
						}else{
							$assigned_auditors = $assigned_auditors.", ".$auditor['name'];
						}
				 	}
				 	if($assigned_auditors_hover_text == ''){
						$assigned_auditors_hover_text = $auditor['name'];
					}else{
						$assigned_auditors_hover_text = $assigned_auditors_hover_text.", ".$auditor['name'];
					}
				}
			}
			if($counter > 2){
				$counter = $counter - 2;
				$assigned_auditors = $assigned_auditors." +".$counter." MORE";
			}
			@endphp
			<div id="audit-filter-address" class="uk-badge uk-text-right@s badge-filter" uk-tooltip="title:{{$assigned_auditors_hover_text}}">
				<a onClick="filterAudits('assignment-auditor', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>@if(count(session('assignment-auditor')) > 1)AUDITORS ASSIGNED: @else AUDITOR ASSIGNED:@endif {{$assigned_auditors}}</span></a>
			</div>
			@endif
		</div>
	</div>

	<div id="auditstable" class="uk-width-1-1 uk-overflow-auto" style="min-height: 700px;">
		<table class="uk-table uk-table-striped uk-table-hover uk-table-small uk-table-divider" style="min-width: 1320px;">
		    <thead>
		        <tr>
		            <th class="uk-table-shrink">
		            	<div uk-grid>
			            	<div class="filter-box filter-icons uk-text-center uk-width-1-1 uk-link">
			            		<i class="a-avatar-star" onclick="filterAudits('audit-my-audits')"></i>
			            	</div>
			            	<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top" title="SORT BY LEAD AUDITOR">
			            		@if($sort_by == 'audit-sort-lead')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-lead', @php echo 1-$sort_order; @endphp);"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-lead', 1);"></a>
			            		@endif
			            	</span>
			            </div>
		            </th>
		            <th class="uk-table-small" style="width:130px;">
		            	<div uk-grid>
		            		<div class="filter-box uk-width-1-1">
								<input id="filter-by-project" class="filter-box filter-search-project-input" type="text" placeholder="PROJECT & AUDIT" value="@if(session()->has('filter-search-project')){{session('filter-search-project')}}@endif">
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top" title="SORT BY PROJECT ID">
			            		@if($sort_by == 'audit-sort-project')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-project', @php echo 1-$sort_order; @endphp, 'filter-search-project');"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-project', 1, 'filter-search-project');"></a>
			            		@endif
							</span> 
							<div class="uk-dropdown" aria-expanded="false"></div>
						</div>
					</th>
		            <th>
		            	<div uk-grid>
			            	<div class="filter-box uk-width-1-1">
								<input id="filter-by-name" class="filter-box filter-search-pm-input" type="text" placeholder="PROJECT / PM NAME" value="@if(session()->has('filter-search-pm')){{session('filter-search-pm')}}@endif">
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-2 uk-padding-remove-top uk-margin-remove-top" title="SORT BY PROJECT NAME">
			            		@if($sort_by == 'audit-sort-project-name')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-project-name',  @php echo 1-$sort_order; @endphp, 'filter-search-pm-input');"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-project-name', 1, 'filter-search-pm-input');"></a>
			            		@endif
							</span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-2 uk-padding-remove-top uk-margin-remove-top" title="SORT BY PROPERTY MANAGER NAME">
			            		@if($sort_by == 'audit-sort-pm')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-pm',  @php echo 1-$sort_order; @endphp, 'filter-search-pm-input');"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-pm', 1, 'filter-search-pm-input');"></a>
			            		@endif
							</span> 
							<div class="uk-dropdown" aria-expanded="false"></div>
						</div>
					</th>
		            <th >
		            	<div uk-grid>
			            	<div class="filter-box uk-width-1-1">
								<input id="filter-by-address" class="filter-box filter-search-address-input" type="text" placeholder="PRIMARY ADDRESS" value="@if(session()->has('filter-search-address')){{session('filter-search-address')}}@endif">
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top" title="SORT BY STREET ADDRESS">
			            		@if($sort_by == 'audit-sort-address')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-address',  @php echo 1-$sort_order; @endphp, 'filter-search-address-input');"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-address', 1, 'filter-search-address-input');"></a>
			            		@endif
							</span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top" title="SORT BY CITY">
			            		@if($sort_by == 'audit-sort-city')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-city',  @php echo 1-$sort_order; @endphp, 'filter-search-address-input');"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-city', 1, 'filter-search-address-input');"></a>
			            		@endif
							</span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top" title="SORT BY ZIP">
			            		@if($sort_by == 'audit-sort-zip')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-zip',  @php echo 1-$sort_order; @endphp, 'filter-search-address-input');"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-zip', 1, 'filter-search-address-input');"></a>
			            		@endif
							</span> 
							<div class="uk-dropdown" aria-expanded="false"></div>
						</div>
		            </th>
		            <th @can('access_auditor') style="min-width:190px;" @else style="max-width:50px;" @endcan>
		            	<div uk-grid>
			            	<div class="filter-box filter-date-aging uk-vertical-align uk-width-1-1" uk-grid> 
								<!-- SPAN TAG TITLE NEEDS UPDATED TO REFLECT CURRENT DATE RANGE -->
								<span class="@can('access_auditor') uk-width-1-2 @else uk-width-1-1 @endcan uk-text-center uk-padding-remove-top uk-margin-remove-top">
									<i class="a-calendar-8 uk-vertical-align-middle"></i> <i class="uk-icon-asterisk  uk-vertical-align-middle uk-text-small tiny-middle-text"></i> <i class="a-calendar-8 uk-vertical-align-middle"></i>
								</span>
								@can('access_auditor')
								<span class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top uk-text-right uk-link">
									<i id="assignmentselectionbutton" class="a-avatar-home"></i>
									<div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="top: 26px; left: 0px; text-align:left;">
										<form id="audit_assignment_selection" method="post">
				            				<fieldset class="uk-fieldset">
				            					<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">
				            						<h5>AUDITS FOR:</h5>
												@foreach($auditors_array as $auditor)
													<input id="assignment-auditor-{{$auditor['user_id']}}" user-id="{{$auditor['user_id']}}" class="assignmentauditor" type="checkbox" @if(is_array(session('assignment-auditor'))) @if(in_array($auditor['user_id'], session('assignment-auditor')) == 1) checked @endif @endif/>
													<label for="assignment-auditor-{{$auditor['user_id']}}">{{$auditor['name']}}</label>
												@endforeach
										        </div>
										        <div class="uk-margin-remove" uk-grid>
				                            		<div class="uk-width-1-2">
				                            			<button onclick="updateAuditAssignment(event);" class="uk-button uk-button-primary uk-width-1-1"><i class="fas fa-filter"></i> APPLY FILTER</button>
				                            		</div>
				                            		<div class="uk-width-1-2">
				                            			<button onclick="$('#assignmentselectionbutton').trigger( 'click' );return false;" class="uk-button uk-button-secondary uk-width-1-1"><i class="a-circle-cross"></i> CANCEL</button>
				                            		</div>
				                            	</div>
				            				</fieldset>
				                        </form>
				            			
				                    </div> / <i id="totalinspectionbutton" class="a-home-2"></i>
				                    <div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="top: 26px; left: 0px; text-align:left;">
										<form id="total_inspection_filter" method="post">
				            				<fieldset class="uk-fieldset">
				            					<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">
													<input id="total_inspection_more" class="totalinspectionfilter" type="checkbox" @if(session('total_inspection_filter') != 1) checked @endif/>
													<label for="total_inspection_more">MORE THAN OR EQUAL TO</label>
													<input id="total_inspection_less" class="totalinspectionfilter" type="checkbox" @if(session('total_inspection_filter') == 1) checked @endif/>
													<label for="total_inspection_less">LESS THAN OR EQUAL TO</label>
													<input id="total_inspection_amount" class="uk-input" value="{{session('total_inspection_amount')}}" type="number" min="0" >
													
										        </div>
										        <div class="uk-margin-remove" uk-grid>
				                            		<div class="uk-width-1-2">
				                            			<button onclick="updateAuditInspection(event);" class="uk-button uk-button-primary uk-width-1-1"><i class="fas fa-filter"></i> APPLY FILTER</button>
				                            		</div>
				                            		<div class="uk-width-1-2">
				                            			<button onclick="$('#totalinspectionbutton').trigger( 'click' );return false;" class="uk-button uk-button-secondary uk-width-1-1"><i class="a-circle-cross"></i> CANCEL</button>
				                            		</div>
				                            	</div>
				            				</fieldset>
				                        </form>
				            			
				                    </div>
								</span>
								<span class="uk-width-1-6 uk-padding-remove-top uk-margin-remove-top uk-text-center uk-link">
									<i id="complianceselectionbutton" class="a-circle-checked"></i>
									<div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="top: 26px; left: 0px; text-align:left;">
				            			<form id="audit_compliance_status_selection" method="post">
				            				<fieldset class="uk-fieldset">
				            					<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">

												<input id="compliance-status-all" class="" type="checkbox" @if(session('compliance-status-all') == 1) checked @endif/>
												<label for="compliance-status-all">ALL COMPLIANCE STATUSES</label>
												
												<input id="compliance-status-rr" class=" complianceselector" type="checkbox" @if(session('compliance-status-rr') == 1) checked @endif/>
												<label for="compliance-status-rr"><i class="a-circle-ellipsis action-required"></i> <span class="action-required">UNITS REQUIRE REVIEW</span></label>
												
												<input id="compliance-status-nc" class=" complianceselector" type="checkbox" @if(session('compliance-status-nc') == 1) checked @endif/>
												<label for="compliance-status-nc"><i class="a-circle-cross action-required"></i> <span class="action-required">NOT COMPLIANT</span></label>
												
												<input id="compliance-status-c" class=" complianceselector" type="checkbox" @if(session('compliance-status-c') == 1) checked @endif/>
												<label for="compliance-status-c"><i class="a-circle-checked ok-actionable"></i><span class="ok-actionable">IS COMPLIANT</span></label>
												
										        </div>
										        <div class="uk-margin-remove" uk-grid>
				                            		<div class="uk-width-1-2">
				                            			<button onclick="updateAuditComplianceStatus(event);" class="uk-button uk-button-primary uk-width-1-1"><i class="fas fa-filter"></i> APPLY FILTER</button>
				                            		</div>
				                            		<div class="uk-width-1-2">
				                            			<button onclick="$('#complianceselectionbutton').trigger( 'click' );return false;" class="uk-button uk-button-secondary uk-width-1-1"><i class="a-circle-cross"></i> CANCEL</button>
				                            		</div>
				                            	</div>
				            				</fieldset>
				                        </form>
				            			
				                    </div>
								</span>
								@endcan
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="@can('access_auditor') uk-width-1-2 @else uk-width-1-1 @endcan uk-padding-remove-top uk-margin-remove-top" title="SORT BY SCHEDULED DATE">
			            		@if($sort_by == 'audit-sort-scheduled-date')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-scheduled-date',  @php echo 1-$sort_order; @endphp);"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-scheduled-date', 1);"></a>
			            		@endif
							</span>
							@can('access_auditor')
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-6 uk-padding-remove-top uk-margin-remove-top" title="SORT BY TOTAL ASSIGNED INSPECTION AREAS">
			            		@if($sort_by == 'audit-sort-assigned-areas')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-assigned-areas',  @php echo 1-$sort_order; @endphp);"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-assigned-areas', 1);"></a>
			            		@endif
							</span>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-6 uk-padding-remove-top uk-margin-remove-top" title="SORT BY TOTAL INSPECTION AREAS">
			            		@if($sort_by == 'audit-sort-total-areas')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-total-areas',  @php echo 1-$sort_order; @endphp);"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-total-areas', 1);"></a>
			            		@endif
							</span>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-6 uk-padding-remove-top uk-margin-remove-top" title="SORT BY COMPLIANCE STATUS">
			            		@if($sort_by == 'audit-sort-compliance-status')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-compliance-status',  @php echo 1-$sort_order; @endphp);"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-compliance-status', 1);"></a>
			            		@endif
							</span>
							@endcan
						</div>
		            </th>
		            <th style="@can('access_auditor') min-width: 80px @else max-width: 50px @endcan">
		            	<div uk-grid>
			            	<div class="filter-box filter-date-expire uk-vertical-align uk-width-1-1 uk-text-center"> 
								<span>
									<i class="a-calendar-8 uk-vertical-align-middle"></i> <i class="uk-icon-asterisk  uk-vertical-align-middle uk-text-small tiny-middle-text"></i> <i class="a-calendar-8 uk-vertical-align-middle"></i>
								</span>
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top" title="SORT BY FOLLOW-UP DATE">
			            		@if($sort_by == 'audit-sort-followup-date')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-followup-date',  @php echo 1-$sort_order; @endphp);"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-followup-date', 1);"></a>
			            		@endif
							</span>
						</div>
					</th>
		            <th style="@can('access_auditor') min-width: 90px; @else max-width: 103px; @endcan ">
		            	<div uk-grid>
			            	<div class="filter-box filter-icons uk-vertical-align uk-width-1-1" uk-grid> 
			            		<span class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top uk-link">
									<i id="file_audit_status_button" class="a-folder"></i>
									<div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="width: 420px; top: 26px; left: 0px; text-align:left;">
				            			<form id="file_audit_status_selection" method="post">
				            				<fieldset class="uk-fieldset">
				            					<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">

												
												
												<input id="file-audit-status-h" class="fileauditselector" type="checkbox" @if(session('file-audit-status-h') == 1) checked @endif/>
												<label for="file-audit-status-h"><i class="a-folder "></i><span class="">HAS FILE AUDIT FINDINGS</span></label>

												<input id="file-audit-status-r" class="fileauditselector" type="checkbox" @if(session('file-audit-status-r') == 1) checked @endif/>
												<label for="file-audit-status-r"><i class="a-folder ok-actionable divider dividericon"></i><span class="ok-actionable">HAS RESOLVED FILE AUDIT FINDINGS</span></label>

												
												
												<input id="file-audit-status-ar" class=" fileauditselector" type="checkbox" @if(session('file-audit-status-ar') == 1) checked @endif/>
												<label for="file-audit-status-ar"><i class="a-folder action-needed divider dividericon"></i> <span class="action-needed">HAS ACTION REQUIRED FILE AUDIT FINDINGS</span></label>
												
												<input id="file-audit-status-c" class=" fileauditselector" type="checkbox" @if(session('file-audit-status-c') == 1) checked @endif/>
												<label for="file-audit-status-c"><i class="a-folder action-required divider dividericon"></i> <span class="action-required">HAS CRITICAL FILE AUDIT FINDINGS</span></label>
												
												<input id="file-audit-status-nf" class=" fileauditselector" type="checkbox" @if(session('file-audit-status-nf') == 1) checked @endif/>
												<label for="file-audit-status-nf"><i class="a-folder"></i> <span class="">DOES NOT HAVE FILE AUDIT FINDINGS</span></label>
												
										        </div>
										        <div class="uk-margin-remove" uk-grid>
				                            		<div class="uk-width-1-2">
				                            			<button onclick="updateFileAuditStatus(event);" class="uk-button uk-button-primary uk-width-1-1"><i class="fas fa-filter"></i> APPLY FILTER</button>
				                            		</div>
				                            		<div class="uk-width-1-2">
				                            			<button onclick="$('#file_audit_status_button').trigger( 'click' );return false;" class="uk-button uk-button-secondary uk-width-1-1"><i class="a-circle-cross"></i> CANCEL</button>
				                            		</div>
				                            	</div>
				            				</fieldset>
				                        </form>
				            			
				                    </div>
								</span>
								<span class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top uk-link">
									<i id="nlt_audit_status_button" class="a-booboo"></i>
									<div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="width: 420px; top: 26px; left: 0px; text-align:left;">
				            			<form id="nlt_audit_status_selection" method="post">
				            				<fieldset class="uk-fieldset">
				            					<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">

												
												
												<input id="nlt-audit-status-h" class="nltauditselector" type="checkbox" @if(session('nlt-audit-status-h') == 1) checked @endif/>
												<label for="nlt-audit-status-h"><i class="a-booboo  "></i><span class="">HAS NLT AUDIT FINDINGS</span></label>

												<input id="nlt-audit-status-r" class="nltauditselector" type="checkbox" @if(session('nlt-audit-status-r') == 1) checked @endif/>
												<label for="nlt-audit-status-r"><i class="a-booboo ok-actionable divider dividericon"></i><span class="ok-actionable">HAS RESOLVED NLT AUDIT FINDINGS</span></label>
												
												<input id="nlt-audit-status-ar" class=" nltauditselector" type="checkbox" @if(session('nlt-audit-status-ar') == 1) checked @endif/>
												<label for="nlt-audit-status-ar"><i class="a-booboo action-needed divider dividericon"></i> <span class="action-needed">HAS ACTION REQUIRED NLT AUDIT FINDINGS</span></label>
												
												<input id="nlt-audit-status-c" class=" nltauditselector" type="checkbox" @if(session('nlt-audit-status-c') == 1) checked @endif/>
												<label for="nlt-audit-status-c"><i class="a-booboo action-required divider dividericon"></i> <span class="action-required">HAS CRITICAL NLT AUDIT FINDINGS</span></label>
												
												<input id="nlt-audit-status-nf" class=" nltauditselector" type="checkbox" @if(session('nlt-audit-status-nf') == 1) checked @endif/>
												<label for="nlt-audit-status-nf"><i class="a-booboo"></i> <span class="">DOES NOT HAVE NLT AUDIT FINDINGS</span></label>
												
										        </div>
										        <div class="uk-margin-remove" uk-grid>
				                            		<div class="uk-width-1-2">
				                            			<button onclick="updateNLTAuditStatus(event);" class="uk-button uk-button-primary uk-width-1-1"><i class="fas fa-filter"></i> APPLY FILTER</button>
				                            		</div>
				                            		<div class="uk-width-1-2">
				                            			<button onclick="$('#nlt_audit_status_button').trigger( 'click' );return false;" class="uk-button uk-button-secondary uk-width-1-1"><i class="a-circle-cross"></i> CANCEL</button>
				                            		</div>
				                            	</div>
				            				</fieldset>
				                        </form>
				            			
				                    </div>
								</span>
								<span class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top uk-link">
									<i id="lt_audit_status_button" class="a-skull"></i>
									<div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="width: 420px; top: 26px; left: 0px; text-align:left;">
				            			<form id="lt_audit_status_selection" method="post">
				            				<fieldset class="uk-fieldset">
				            					<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">

												
												
												<input id="lt-audit-status-h" class="ltauditselector" type="checkbox" @if(session('lt-audit-status-h') == 1) checked @endif/>
												<label for="lt-audit-status-h"><i class="a-skull "></i><span class="">HAS LT AUDIT FINDINGS</span></label>

												<input id="lt-audit-status-r" class="ltauditselector" type="checkbox" @if(session('lt-audit-status-r') == 1) checked @endif/>
												<label for="lt-audit-status-r"><i class="a-skull ok-actionable divider dividericon"></i><span class="ok-actionable">HAS RESOLVED LT AUDIT FINDINGS</span></label>
												
												<input id="lt-audit-status-ar" class=" ltauditselector" type="checkbox" @if(session('lt-audit-status-ar') == 1) checked @endif/>
												<label for="lt-audit-status-ar"><i class="a-skull action-needed divider dividericon"></i> <span class="action-needed">HAS ACTION REQUIRED LT AUDIT FINDINGS</span></label>
												
												<input id="lt-audit-status-c" class=" ltauditselector" type="checkbox" @if(session('lt-audit-status-c') == 1) checked @endif/>
												<label for="lt-audit-status-c"><i class="a-skull action-required divider dividericon"></i> <span class="action-required">HAS CRITICAL LT AUDIT FINDINGS</span></label>
												
												<input id="lt-audit-status-nf" class=" ltauditselector" type="checkbox" @if(session('lt-audit-status-nf') == 1) checked @endif/>
												<label for="lt-audit-status-nf"><i class="a-skull"></i> <span class="">DOES NOT HAVE LT AUDIT FINDINGS</span></label>
												
										        </div>
										        <div class="uk-margin-remove" uk-grid>
				                            		<div class="uk-width-1-2">
				                            			<button onclick="updateLTAuditStatus(event);" class="uk-button uk-button-primary uk-width-1-1"><i class="fas fa-filter"></i> APPLY FILTER</button>
				                            		</div>
				                            		<div class="uk-width-1-2">
				                            			<button onclick="$('#lt_audit_status_button').trigger( 'click' );return false;" class="uk-button uk-button-secondary uk-width-1-1"><i class="a-circle-cross"></i> CANCEL</button>
				                            		</div>
				                            	</div>
				            				</fieldset>
				                        </form>
				            			
				                    </div>
								</span>
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top" title="SORT BY FILE FINDING COUNT">
			            		@if($sort_by == 'audit-sort-finding-file')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-finding-file',  @php echo 1-$sort_order; @endphp);"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-finding-file', 1);"></a>
			            		@endif
							</span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top" title="SORT BY NLT FINDING COUNT">
			            		@if($sort_by == 'audit-sort-finding-nlt')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-finding-nlt',  @php echo 1-$sort_order; @endphp);"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-finding-nlt', 1);"></a>
			            		@endif
							</span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top" title="SORT BY LT FINDING COUNT">
			            		@if($sort_by == 'audit-sort-finding-lt')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-finding-lt',  @php echo 1-$sort_order; @endphp);"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-finding-lt', 1);"></a>
			            		@endif
							</span> 
						</div>
					</th>
		            <th  @can('access_auditor') style="min-width: 80px;" @else style="max-width: 70px;" @endcan >
		            	<div uk-grid>
			            	<div class="filter-box filter-icons uk-vertical-align uk-width-1-1" uk-grid> 
			            		@can('access_auditor')
			            		<span class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top uk-link">
									<i id="scheduleassignmentfilterbutton" class="a-avatar"></i>
									<div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="top: 26px; left: 0px; text-align:left;">
										<form id="schedule_assignment_filter" method="post">
				            				<fieldset class="uk-fieldset">
				            					<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">
													<input id="schedule_assignment_unassigned" class="" type="checkbox" @if(session('schedule_assignment_unassigned') == 1) checked @endif/>
													<label for="schedule_assignment_unassigned">UNASSIGNED AMENITIES</label>
													<input id="schedule_assignment_not_enough" class="" type="checkbox" @if(session('schedule_assignment_not_enough') == 1) checked @endif/>
													<label for="schedule_assignment_not_enough">NOT ENOUGH HOURS SCHEDULED</label>
													<input id="schedule_assignment_too_many" class="" type="checkbox" @if(session('schedule_assignment_too_many') == 1) checked @endif/>
													<label for="schedule_assignment_too_many">TOO MANY HOURS SCHEDULED</label>
													
										        </div>
										        <div class="uk-margin-remove" uk-grid>
				                            		<div class="uk-width-1-2">
				                            			<button onclick="updateAuditScheduleAssignment(event);" class="uk-button uk-button-primary uk-width-1-1"><i class="fas fa-filter"></i> APPLY FILTER</button>
				                            		</div>
				                            		<div class="uk-width-1-2">
				                            			<button onclick="$('#scheduleassignmentfilterbutton').trigger( 'click' );return false;" class="uk-button uk-button-secondary uk-width-1-1"><i class="a-circle-cross"></i> CANCEL</button>
				                            		</div>
				                            	</div>
				            				</fieldset>
				                        </form>
				            			
				                    </div>
								</span>
								@endcan
								<span class="@can('access_auditor') uk-width-1-3 @else uk-width-1-2 @endcan uk-padding-remove-top uk-margin-remove-top uk-link">
									<i class="a-envelope-4"></i>
									<div class="uk-dropdown uk-dropdown-bottom" uk-dropdown="flip: false; pos: bottom-right; animation: uk-animation-slide-top-small; mode: click" style="top: 26px; left: 0px;">
				                        <ul class="uk-nav uk-nav-dropdown uk-text-small uk-list">
				                        	<li>
				                        		<span style="padding-left:10px; border-bottom: 1px solid #ddd;display: block;padding-bottom: 5px;color: #bbb;margin-bottom: 0px;margin-top: 5px;">MESSAGES</span>
				                        	</li>
				                            <li>
				                            	<button class="uk-button uk-text-left uk-button-link uk-button-small" onclick="applyFilter('audit-message',0);">
				                            		@if(session('audit-message') == 0)
				                            		<span class="a-checkbox-checked"></span> 
				                            		@else
				                            		<span class="a-checkbox"></span> 
				                            		@endif
				                            		All messages
				                            	</button>
				                            		
				                            </li>
				                            <li>
				                            	<button class="uk-button uk-text-left uk-button-link uk-button-small" onclick="applyFilter('audit-message',1);">
				                            		@if(session('audit-message') == 1)
				                            		<span class="a-checkbox-checked"></span> 
				                            		@else
				                            		<span class="a-checkbox"></span> 
				                            		@endif
				                            		Unread messages
				                            	</button>
				                            </li>
				                            <li>
				                            	<button class="uk-button uk-text-left uk-button-link uk-button-small" onclick="applyFilter('audit-message',2);">
				                            		@if(session('audit-message') == 2)
				                            		<span class="a-checkbox-checked"></span> 
				                            		@else
				                            		<span class="a-checkbox"></span> 
				                            		@endif
				                            		Has no messages
				                            	</button>
				                            </li>
				                        	<li>
				                        		<span style="padding-left:10px; border-bottom: 1px solid #ddd;padding-top: 5px;display: block;padding-bottom: 5px;color: #bbb;margin-bottom: 0px;margin-top: 5px;">WHO</span>
				                        	</li>
				                            <li>
				                            	<button class="uk-button uk-text-left uk-button-link uk-button-small" onclick="applyFilter('audit-mymessage',1);">
				                            		@if(session('audit-mymessage') == 1)
				                            		<span class="a-checkbox-checked"></span> 
				                            		@else
				                            		<span class="a-checkbox"></span> 
				                            		@endif
				                            		Only messages for me
				                            	</button>
				                            </li>
					                    </ul>
				                    </div>
								</span>
								<span class="@can('access_auditor') uk-width-1-3 @else uk-width-1-2 @endcan uk-padding-remove-top uk-margin-remove-top uk-link">
									<i class="a-files"></i>
								</span>
								
							</div>
							@can('access_auditor')
								<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top" title="SORT BY AUDITOR ASSIGNMENT STATUS">
				            		@if($sort_by == 'audit-sort-status-auditor')
				            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-status-auditor',  @php echo 1-$sort_order; @endphp);"></a>
				            		@else
				            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-status-auditor', 1);"></a>
				            		@endif
								</span> 
							@endif
							<span data-uk-tooltip="{pos:'bottom'}" class="@can('access_auditor') uk-width-1-3 @else uk-width-1-2 @endcan uk-padding-remove-top uk-margin-remove-top" title="SORT BY MESSAGE STATUS">
			            		@if($sort_by == 'audit-sort-status-message')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-status-message',  @php echo 1-$sort_order; @endphp);"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-status-message', 1);"></a>
			            		@endif
							</span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="@can('access_auditor') uk-width-1-3 @else uk-width-1-2 @endcan uk-padding-remove-top uk-margin-remove-top" title="SORT BY DOCUMENT STATUS">
			            		@if($sort_by == 'audit-sort-status-document')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-status-document',  @php echo 1-$sort_order; @endphp);"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-status-document', 1);"></a>
			            		@endif
							</span> 
							
							
						</div>
						
		            </th>
		            @can('access_auditor')
		            <th >
		            	<div uk-grid>
			            	<div class="filter-box filter-icons uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-link">
			            		<i class="a-checklist" id="checklist-button"></i>
			            		<div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="top: 26px; left: 0px;">
			            			<form id="audit_steps_selection" method="post">
			            				<fieldset class="uk-fieldset">
			            					<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">
			            					@if(session('step-all') == 0)
									            <input id="step-all" type="checkbox" />
												<label for="step-all">ALL STEPS (CLICK TO SELECT ALL)</label>
											@else
									            <input id="step-all" type="checkbox" checked/>
												<label for="step-all">ALL STEPS (CLICK TO DESELECT ALL)</label>
											@endif
											@foreach($steps as $step)
												<input id="{{$step->session_name}}" class="stepselector" type="checkbox" @if(session($step->session_name) == 1) checked @endif/>
												<label for="{{$step->session_name}}"><i class="{{$step->icon}}"></i> {{$step->name}}</label>
											@endforeach
									        </div>
									        <div class="uk-margin-remove" uk-grid>
			                            		<div class="uk-width-1-2">
			                            			<button onclick="updateAuditStepSelection(event);" class="uk-button uk-button-primary uk-width-1-1"><i class="fas fa-filter"></i> APPLY FILTER</button>
			                            		</div>
			                            		<div class="uk-width-1-2">
			                            			<button onclick="$('#checklist-button').trigger( 'click' );return false;" class="uk-button uk-button-secondary uk-width-1-1"><i class="a-circle-cross"></i> CANCEL</button>
			                            		</div>
			                            	</div>
			            				</fieldset>
			                        </form>
			            			
			                    </div>
			            	</div>
			            	
			            	<span data-uk-tooltip="{pos:'bottom'}" title="SORT BY NEXT TASK" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top">
			            		@if($sort_by == 'audit-sort-next-task')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-next-task',  @php echo 1-$sort_order; @endphp);"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-next-task', 1);"></a>
			            		@endif
							</span> 

			            </div>
		            </th>@endcan
		            @if(1==0)
		            <th style="vertical-align:top;">
		            	<div uk-grid>
			            	<div class="uk-link uk-width-1-1 archived-icon" onclick="toggleArchivedAudits();" data-uk-tooltip="{pos:'bottom'}" title="Click to Hide Archived Audits">
				            	<i class="a-folder-box"></i>
							</div>
			            	<div class="uk-link uk-width-1-1 archived-icon selected" onclick="toggleArchivedAudits();" data-uk-tooltip="{pos:'bottom'}" title="Click to Show Archived Audits" style="display:none;">
				            	<i class="a-folder-box"></i>
							</div>
						</div>
		            </th>
		            @endif
		        </tr>
		    </thead>
		    <tbody>
		    	@if(0)
			    	@foreach($audits as $audit)
			    	<tr id="audit-r-{{$audit->audit_id}}" class="{{$audit['status']}} @if($audit['status'] != 'critical') notcritical @endif" style=" @if(session('audit-hidenoncritical') == 1 && $audit['status'] != 'critical') display:none; @endif ">
			            <td id="audit-c-1-{{$loop->iteration}}" class="uk-text-center audit-td-lead">
			            	<span id="audit-avatar-badge-1" uk-tooltip="pos:top-left;title:{{$audit->lead_json->name}};" title="" aria-expanded="false" class="user-badge user-badge-{{$audit->lead_json->color}} no-float uk-link">
								{{$audit->lead_json->initials}}
							</span>
							<span id="audit-rid-{{$loop->iteration}}"><small>#{{$loop->iteration}}</small></span>
			            </td>
			            <td id="audit-c-2-{{$loop->iteration}}" class="audit-td-project">
			            	<div class="uk-vertical-align-middle uk-display-inline-block uk-margin-small-top">
			            		<span id="audit-i-project-detail-{{$loop->iteration}}" onclick="projectDetails({{$audit['id']}},{{$loop->iteration}},{{$audit['total_buildings']}});" uk-tooltip="pos:top-left;title:View Buildings and Common Areas;" class="uk-link"><i class="a-menu uk-text-muted"></i></span>
			            	</div>
			            	<div class="uk-vertical-align-middle uk-display-inline-block">
			            		<h3 id="audit-project-name-{{$loop->iteration}}" class="uk-margin-bottom-remove uk-link filter-search-project" uk-tooltip="title:Open Audit Details in Tab;" onClick="loadTab('{{ route('project', $audit['id']) }}', '4', 1, 1);">{{$audit['project_ref']}}</h3>
				            	<small id="audit-project-aid-{{$loop->iteration}}" class="uk-text-muted faded filter-search-project" uk-tooltip="title:View Project's Audit Details;">AUDIT {{$audit['id']}}</small>
				            </div>
			            </td>
			            <td class="audit-td-name">
			            	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
			            		<i class="a-info-circle uk-text-muted uk-link" uk-tooltip="title:View Contact Details;"></i>
			            	</div> 
			            	<div class="uk-vertical-align-top uk-display-inline-block fadetext">
			            		<h3 class="uk-margin-bottom-remove filter-search-pm">{{$audit['title']}}</h3>
				            	<small class="uk-text-muted faded filter-search-pm">{{$audit['pm']}}</small>
			            	</div>
			            </td>
			            <td class="hasdivider audit-td-address">
			            	<div class="divider"></div>
			            	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
			            		<i class="a-marker-basic uk-text-muted uk-link" uk-tooltip="title:View On Map;"></i>
			            	</div> 
			            	<div class="uk-vertical-align-top uk-display-inline-block fullwidthleftpad fadetext">
			            		<h3 class="uk-margin-bottom-remove filter-search-address">{{$audit['address']}}</h3>
				            	<small class="uk-text-muted faded filter-search-address">{{$audit['city']}}, {{$audit['state']}} {{$audit['zip']}}</small>
			            	</div>
			            </td>
			            <td class="hasdivider audit-td-scheduled">
			            	<div class="divider"></div>
			            	<div class="uk-display-inline-block uk-margin-small-top uk-text-center fullwidth" uk-grid>
				            	<div class="uk-width-1-2 uk-padding-remove-top" uk-grid>
				            		<div class="uk-width-1-3">
				            			<i class="a-mobile-repeat {{$audit['inspection_status']}}" uk-tooltip="title:{{$audit['inspection_status_text']}};"></i>
				            		</div>
				            		<div class="uk-width-2-3 uk-padding-remove uk-margin-small-top">
					            		<h3 class="uk-link" uk-tooltip="title:{{$audit['inspection_schedule_text']}};">{{\Carbon\Carbon::createFromFormat('Y-m-d', $audit['inspection_schedule_date'])->format('m/d')}}</h3>
					            		<div class="dateyear">{{\Carbon\Carbon::createFromFormat('Y-m-d', $audit['inspection_schedule_date'])->format('Y')}}</div>
				            		</div>
				            	</div> 
				            	<div class="uk-width-1-6 uk-text-right uk-padding-remove" uk-tooltip="title:{{$audit['inspectable_items']}} INSPECTABLE ITEMS;">{{$audit['inspectable_items']}} /</div> 
				            	<div class="uk-width-1-6 uk-text-left uk-padding-remove">{{$audit['total_items']}}</div> 
				            	<div class="uk-width-1-6 uk-text-left">
				            		<i class="{{$audit['audit_compliance_icon']}} {{$audit['audit_compliance_status']}}"  uk-tooltip="title:{{$audit['audit_compliance_status_text']}};"></i>
				            	</div>
				            </div>
			            </td>
			            <td class="hasdivider audit-td-due">
			            	<div class="divider"></div>
			            	<div class="uk-display-inline-block uk-margin-small-top uk-text-center fullwidth" uk-grid>
				            	<div class="uk-width-1-3">
				            		<i class="a-bell-2 {{$audit['followup_status']}}" uk-tooltip="title:{{$audit['followup_status_text']}};"></i>
				            	</div> 
				            	<div class="uk-width-2-3 uk-padding-remove uk-margin-small-top">
				            		@if($audit['followup_date'])
				            		<h3 class="uk=link" uk-tooltip="title:Click to reschedule audits;">{{\Carbon\Carbon::createFromFormat('Y-m-d', $audit['followup_date'])->format('m/d')}}</h3>
					            	<div class="dateyear">{{\Carbon\Carbon::createFromFormat('Y-m-d', $audit['followup_date'])->format('Y')}}</div>
				            		@else
				            		<i class="a-calendar-pencil" uk-tooltip="title:New followup;"></i>
				            		@endif
				            	</div> 
				            </div>
			            </td>
			            <td class="hasdivider">
			            	<div class="divider"></div>
			            	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top " uk-grid>
				            	<div class="uk-width-1-4 {{$audit['file_audit_status']}}" uk-tooltip="title:{{$audit['file_audit_status_text']}};">
				            		<i class="{{$audit['file_audit_icon']}}"></i>
				            	</div> 
				            	<div class="uk-width-1-4 {{$audit['nlt_audit_status']}}" uk-tooltip="title:{{$audit['nlt_audit_status_text']}};">
				            		<i class="{{$audit['nlt_audit_icon']}}"></i>
				            	</div> 
				            	<div class="uk-width-1-4 {{$audit['lt_audit_status']}}" uk-tooltip="title:{{$audit['lt_audit_status_text']}};">
				            		<i class="{{$audit['lt_audit_icon']}}"></i>
				            	</div> 
				            	<div class="uk-width-1-4 {{$audit['smoke_audit_status']}}" uk-tooltip="title:{{$audit['smoke_audit_status_text']}};">
				            		<i class="{{$audit['smoke_audit_icon']}}"></i>
				            	</div> 
				            </div>
			            </td>
			            <td class="hasdivider">
			            	<div class="divider"></div>
			            	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top " uk-grid>
				            	<div class="uk-width-1-4">
				            		<i class="{{$audit['auditor_status_icon']}} {{$audit['auditor_status']}}" uk-tooltip="title:{{$audit['auditor_status_text']}};"></i>
				            	</div> 
				            	<div class="uk-width-1-4">
				            		<i class="{{$audit['message_status_icon']}} {{$audit['message_status']}}" uk-tooltip="title:{{$audit['message_status_text']}};"></i>
				            	</div> 
				            	<div class="uk-width-1-4">
				            		<i class="{{$audit['document_status_icon']}} {{$audit['document_status']}}" uk-tooltip="title:{{$audit['document_status_text']}};"></i>
				            	</div> 
				            	<div class="uk-width-1-4">
				            		<i class="{{$audit['history_status_icon']}} {{$audit['history_status']}}" uk-tooltip="title:{{$audit['history_status_text']}};"></i>
				            	</div> 
				            </div>
			            </td>
			            <td>
			            	<div class="uk-margin-top" uk-grid>
			            		<div class="uk-width-1-1  uk-padding-remove-top">
				            		<i class="{{$audit['step_status_icon']}} {{$audit['step_status']}}" uk-tooltip="title:{{$audit['step_status_text']}};"></i>
								</div>
			            	</div>
			            </td>
			        </tr>
			    	@endforeach
		    	@endif
		    	<tr is="auditrow" :class="{[audit.notcritical]:true}" :style="{ display: [audit.display] }" v-if="audits" v-for="(audit, index) in audits.slice().reverse()" :id="'audit-r-'+audit.auditId" :key="audit.auditId" :index="index" :audit="audit"></tr>
        		<div id="spinner-audits" class="uk-width-1-1" style="text-align:center;"></div>
		    </tbody>
		</table>
	</div>
</div>


<?php
/*
The following div is defined in this particular tab and pushed to the main layout's footer.
*/
?>
<div id="footer-actions" hidden>
	@can('access_auditor')
		@if(session('audit-hidenoncritical') != 1)
		<button class="uk-button uk-button-primary btnToggleCritical" onclick="toggleCritical();"><i class="a-eye-not"></i> HIDE NON CRITICAL</button>
		<button class="uk-button uk-button-primary btnToggleCritical" onclick="toggleCritical();" style="display:none;"><i class="a-eye-2"></i> SHOW NON CRITICAL</button>
		@else
		<button class="uk-button uk-button-primary btnToggleCritical" onclick="toggleCritical();" style="display:none;"><i class="a-eye-not"></i> HIDE NON CRITICAL</button>
		<button class="uk-button uk-button-primary btnToggleCritical" onclick="toggleCritical();"><i class="a-eye-2"></i> SHOW NON CRITICAL</button>
		@endif
	@endcan
	<a href="#top" id="smoothscrollLink" uk-scroll="{offset: 90}" class="uk-button uk-button-default"><span class="a-arrow-small-up uk-text-small uk-vertical-align-middle"></span> SCROLL TO TOP</a>
</div>

<script>
	$( document ).ready(function() {
		// place tab's buttons on main footer
		$('#footer-actions-tpl').html($('#footer-actions').html());
		@if(session()->has('audit-message'))
			@if(session('audit-message') == 1)

			@endif
		@endif

		// apply filter if any
		$('input.filter-box').each(function(){
			if( $(this).val().length ){
				$(this).keyup();
			}
		});
		@can('access_auditor')
		$('#step-all').click(function() {
			if($('#step-all').prop('checked')){
				$('input.stepselector').prop('checked', false);
			}
	    });

	    $('.stepselector').click(function() { 
	    	if($(this).prop('checked') && $('#step-all').prop('checked')){ 
		    	$('#step-all').prop('checked', false);
			}
	    });
	    @endcan

	    $("#filter-by-project").keypress(function(event) {
		    if (event.which == 13) {
		        event.preventDefault();
		        if($(this).val() != ''){
		        	filterAuditList(this, 'filter-search-project');
		        }else{
		        	filterAudits('filter-search-project', '');
		        }
		    }
		});

		$("#filter-by-name").keypress(function(event) {
		    if (event.which == 13) {
		        event.preventDefault();
		        if($(this).val() != ''){
		        	filterAuditList(this, 'filter-search-pm');
		        }else{
		        	filterAudits('filter-search-pm', '');
		        }
		    }
		});

		$("#filter-by-address").keypress(function(event) {
		    if (event.which == 13) {
		        event.preventDefault();
		        if($(this).val() != ''){
		        	filterAuditList(this, 'filter-search-address');
		        }else{
		        	filterAudits('filter-search-address', '');
		        }
		    }
		});
		$('#compliance-status-all').click(function() {
			if($('#compliance-status-all').prop('checked')){
				$('input.complianceselector').prop('checked', false);
			}
	    });
		$(".complianceselector").click(function() {
			// compliance-status-all, compliance-status-nc, compliance-status-c, compliance-status-rr
			if($(this).prop('checked') && $('#compliance-status-all').prop('checked')){ 
		    	$('#compliance-status-all').prop('checked', false);
			}
		});


		$('#file-audit-status-all').click(function() {
			if($('#file-audit-status-all').prop('checked')){
				$('input.fileauditselector').prop('checked', false);
			}
	    });
		$(".fileauditselector").click(function() {
			// compliance-status-all, compliance-status-nc, compliance-status-c, compliance-status-rr
			if($(this).prop('checked') && $('#file-audit-status-all').prop('checked')){ 
		    	$('#file-audit-status-all').prop('checked', false);
			}
		});
		$('#nlt-audit-status-all').click(function() {
			if($('#nlt-audit-status-all').prop('checked')){
				$('input.nltauditselector').prop('checked', false);
			}
	    });
		$(".nltauditselector").click(function() {
			// compliance-status-all, compliance-status-nc, compliance-status-c, compliance-status-rr
			if($(this).prop('checked') && $('#nlt-audit-status-all').prop('checked')){ 
		    	$('#nlt-audit-status-all').prop('checked', false);
			}
		});
		$('#lt-audit-status-all').click(function() {
			if($('#lt-audit-status-all').prop('checked')){
				$('input.ltauditselector').prop('checked', false);
			}
	    });
		$(".ltauditselector").click(function() {
			// compliance-status-all, compliance-status-nc, compliance-status-c, compliance-status-rr
			if($(this).prop('checked') && $('#lt-audit-status-all').prop('checked')){ 
		    	$('#lt-audit-status-all').prop('checked', false);
			}
		});

		$('.totalinspectionfilter').click(function() {
			$('.totalinspectionfilter').prop('checked', false);
			$(this).prop('checked', true);
	    });

		
    });

	@can('access_auditor')
    function rerunCompliance(audit){

    	UIkit.modal.confirm('<div class="uk-grid"><div class="uk-width-1-1"><h2>RERUN COMPLIANCE SELECTION?</h2></div><div class="uk-width-2-3"><hr class="dashed-hr uk-margin-bottom"><h3>Are you sure you want to rerun the automated compliance selection? <br /><br />Depending on how many are currently being processed, this could take up to 10 minutes to complete.</h3><p><strong>Why does it take up to 10 minutes?</strong> When a compliance selection is run, it performs all the unit selections based on the criteria for each program used by the project. Because each selection process uses an element of randomness, the total number of units that need inspected may be different each time it is run due to overlaps of programs to units. So, we run the process 10 times, and pick the one that has the highest amount of program overlap to units. This keeps the audit federally compliant while also making the most efficient use of your time.</p></div><div class="uk-width-1-3"><hr class="dashed-hr uk-margin-bottom"><h3><em style="color:#ca3a8d">WARNING!</em></h3><p style="color:#ca3a8d"> While this will not affect the days and times you have auditors scheduled for your audit, it will remove any auditor assignments to inspect specific areas and units.<br /><br /><small>PLEASE NOTE THAT THIS WILL NOT RUN ON AUDITS WITH FINDINGS RECORDED. YOU WILL NEED TO DO YOUR SELECTION MANUALY.</small></p></div><div class="uk-width-1-1"></div></div>').then(function() {
		    	console.log('Re-running Audit.');

		    	$.post('/audit/'+audit+'/rerun', {
					'_token' : '{{ csrf_token() }}'
				}, function(data) {
					if(data == 1){
						UIkit.notification('<span uk-icon="icon: check"></span> Compliance Selection In Progress', {pos:'top-right', timeout:1000, status:'success'});

			    		$('#audit-r-'+audit).remove();
			    	}else{
			    		UIkit.notification('<span uk-icon="icon: check"></span> Compliance Selection Failed. Findings were found.', {pos:'top-right', timeout:5000, status:'warning'});
			    	}
				});

		}, function () {
		    console.log('Rejected.')
		});
    }
    @endcan

    @can('access_auditor')
    function assignAuditor(audit_id, building_id, unit_id=0, amenity_id=0, element){
    	dynamicModalLoad('amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/assign/'+element);
    }

    function swapAuditor(auditor_id, audit_id, building_id, unit_id, element, amenity_id=0){
    	dynamicModalLoad('amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/swap/'+auditor_id+'/'+element);
    }

    function deleteAmenity(element, audit_id, building_id, unit_id, amenity_id, has_findings = 0, toplevel=0){
    	if(has_findings){
    		UIkit.modal.alert('<p class="uk-modal-body">This amenity has some findings and cannot be deleted.</p>').then(function () {  });
    	}else{
    		console.log('element '+element);
    		dynamicModalLoad('amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/delete/'+element);
    	}
    	
    }
    function copyAmenity(element, audit_id, building_id, unit_id, amenity_id, toplevel=0){
    	UIkit.modal.confirm('<div class="uk-grid"><div class="uk-width-1-1"><h2>MAKE A DUPLICATE?</h2></div><div class="uk-width-1-1"><hr class="dashed-hr uk-margin-bottom"><h3>Are you sure you want to make a duplicate?</h3></div>').then(function() {
		    	
		    	var newAmenities = [];

		    	$.post('/modals/amenities/save', {
					'project_id' : 0,
					'audit_id' : audit_id,
					'building_id' : building_id,
					'unit_id' : unit_id,
					'new_amenities' : newAmenities,
					'amenity_id' : amenity_id,
					'toplevel': toplevel,
					'_token' : '{{ csrf_token() }}'
				}, function(data) {

					if(toplevel == 1){

						projectDetails(audit_id, audit_id, 10, 1);

					}else{

						console.log('unit or building');
						// locate where to update data
						var mainDivId = '';
						if(unit_id != ''){
							mainDivId = $('.inspection-detail-main-list .inspection-areas').parent().attr("id"); 
						}else{
							mainDivId = $('.inspection-areas').parent().attr("id");
						}
						
						var mainDivContainerId = $('#'+mainDivId).parent().attr("id"); 

						// also get context
						var context = $('.inspection-areas').first().attr("data-context");

						// show spinner
						var spinner = '<div style="height:200px;width: 100%;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>';
						$('#'+mainDivId).html(spinner);
						
						// add a row in .inspection-areas
						var inspectionMainTemplate = $('#inspection-areas-template').html();
						var inspectionAreaTemplate = $('#inspection-area-template').html();

						var areas = '';
						var newarea = '';

						data.amenities.forEach(function(area) {
							newarea = inspectionAreaTemplate;
							newarea = newarea.replace(/areaContext/g, context);
							newarea = newarea.replace(/areaRowId/g, area.id);
							newarea = newarea.replace(/areaName/g, area.name); // missing
							newarea = newarea.replace(/areaStatus/g, area.status);  // missing
							newarea = newarea.replace(/areaAuditorId/g, area.auditor_id);  // missing
							newarea = newarea.replace(/areaAuditorInitials/g, area.auditor_initials);  // missing
							newarea = newarea.replace(/areaAuditorName/g, area.auditor_name);  // missing
							newarea = newarea.replace(/areaCompletedIcon/g, area.completed_icon);  
							newarea = newarea.replace(/areaNLTStatus/g, area.finding_nlt_status);  // missing
							newarea = newarea.replace(/areaLTStatus/g, area.finding_lt_status);
							newarea = newarea.replace(/areaSDStatus/g, area.finding_sd_status);
							newarea = newarea.replace(/areaPicStatus/g, area.finding_photo_status);
							newarea = newarea.replace(/areaCommentStatus/g, area.finding_comment_status);
							newarea = newarea.replace(/areaCopyStatus/g, area.finding_copy_status);
							newarea = newarea.replace(/areaTrashStatus/g, area.finding_trash_status);

							newarea = newarea.replace(/areaDataAudit/g, area.audit_id);
							newarea = newarea.replace(/areaDataBuilding/g, area.building_id);
							newarea = newarea.replace(/areaDataArea/g, area.unit_id);
							newarea = newarea.replace(/areaDataAmenity/g, area.id);

							newarea = newarea.replace(/areaAuditorColor/g, area.auditor_color);
							areas = areas + newarea.replace(/areaDataHasFindings/g, area.has_findings);

							//console.log("unit id "+area.unit_id+" - building_id "+area.building_id);
							// update building auditor's list
							if(area.unit_id == null && area.building_id != null){
			                	console.log('updating building auditors ');

			                	if($('#building-auditors-'+area.building_id).hasClass('hasAuditors')){
			                		
			                		// we don't know if/which unit is open
				                	var unitelement = 'div[id^=unit-auditors-]  .uk-slideshow-items li.uk-active > div';

					                $(unitelement).html('');
					                $.each(data.unit_auditors, function(index, value){
					                	var newcontent = '<div id="unit-auditor-'+value.id+area.audit_id+area.building_id+area.unit_id+'" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+value.full_name+';" title="" aria-expanded="false" class="auditor-badge '+value.badge_color+' no-float use-hand-cursor" onclick="swapAuditor('+value.id+', '+area.audit_id+', '+area.building_id+', '+area.unit_id+', \'unit-auditor-'+value.id+area.audit_id+area.building_id+area.unit_id+'\')">'+value.initials+'</div>';
					                	$(unitelement).append(newcontent);
					                });
			                	}else{
			                		// we don't know if/which unit is open
				                	var unitelement = 'div[id^=unit-auditors-]';

					                $(unitelement).html('');
					                $.each(data.auditor.unit_auditors, function(index, value){
					                	var newcontent = '<div id="unit-auditor-'+value.id+area.audit_id+area.building_id+area.unit_id+'" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+value.full_name+';" title="" aria-expanded="false" class="auditor-badge '+value.badge_color+' no-float use-hand-cursor" onclick="swapAuditor('+value.id+', '+area.audit_id+', '+area.building_id+', '+area.unit_id+', \'unit-auditor-'+value.id+area.audit_id+area.building_id+area.unit_id+'\')">'+value.initials+'</div>';
					                	$(unitelement).append(newcontent);
					                });
			                	}       

				                var buildingelement = '#building-auditors-'+area.building_id+' .uk-slideshow-items li.uk-active > div';
				               
				                $(buildingelement).html('');
				                $.each(data.auditor.building_auditors, function(index, value){
				                	var newcontent = '<div id="unit-auditor-'+value.id+area.audit_id+area.building_id+area.unit_id+'" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+value.full_name+';" title="" aria-expanded="false" class="auditor-badge '+value.badge_color+' no-float use-hand-cursor" onclick="swapAuditor('+value.id+', '+area.audit_id+', '+area.building_id+', 0, \'unit-auditor-'+value.id+area.audit_id+area.building_id+area.unit_id+'\')">'+value.initials+'</div>';
				                	$(buildingelement).append(newcontent);
				                });
				            }else{
							// update unit auditor's list
								console.log('units auditor list update');

			                	var unitelement = '#unit-auditors-'+area.unit_id+' .uk-slideshow-items li.uk-active > div';

				                $(unitelement).html('');
				                //console.log(unitelement);
				                $.each(data.auditor.unit_auditors, function(index, value){
				                	var newcontent = '<div id="unit-auditor-'+value.id+area.audit_id+area.building_id+area.unit_id+'" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+value.full_name+';" title="" aria-expanded="false" class="auditor-badge '+value.badge_color+' no-float use-hand-cursor" onclick="swapAuditor('+value.id+', '+area.audit_id+', '+area.building_id+', '+area.unit_id+', \'unit-auditor-'+value.id+area.audit_id+area.building_id+area.unit_id+'\')">'+value.initials+'</div>';
				                	$(unitelement).append(newcontent);

				                	if($('#unit-auditors-'+area.unit_id).hasClass('hasAuditors')){
				                		$(buildingelement).append(newcontent);
				                	}else{
				                		$(buildingelement).html(newcontent);
				                	}
				                });

				                var buildingelement = '#building-auditors-'+area.building_id+' .uk-slideshow-items li.uk-active > div';
				               //console.log(buildingelement);
				                $(buildingelement).html('');
				                $.each(data.auditor.building_auditors, function(index, value){
				                	var newcontent = '<div id="unit-auditor-'+value.id+area.audit_id+area.building_id+area.unit_id+'" class="building-auditor uk-width-1-2 uk-margin-remove"><div uk-tooltip="pos:top-left;title:'+value.full_name+';" title="" aria-expanded="false" class="auditor-badge '+value.badge_color+' no-float use-hand-cursor" onclick="swapAuditor('+value.id+', '+area.audit_id+', '+area.building_id+', 0, \'unit-auditor-'+value.id+area.audit_id+area.building_id+area.unit_id+'\')">'+value.initials+'</div>';

				                	if($('#building-auditors-'+area.building_id).hasClass('hasAuditors')){
				                		//$('#building-auditors-'+area.building_id).append(newcontent);
				                		$(buildingelement).append(newcontent);
				                	}else{
				                		//$('#building-auditors-'+area.building_id).html(newcontent);
				                		$(buildingelement).html(newcontent);
				                	}
				                	
				                });

				            }

							
						});

						$('#unit-amenity-count-'+audit_id+building_id+unit_id).html(data.amenities.length + ' AMENITIES');

						$('#'+mainDivId).html(inspectionMainTemplate);
						$('#'+mainDivId+' .inspection-areas').html(areas);
						$('#'+mainDivContainerId).fadeIn( "slow", function() {
						    // Animation complete
						    console.log("Area list updated");
						  });
					}
					


		        } );

		    	
		}, function () {
		    console.log('Rejected.')
		});
    }
    @endcan


    function markAmenityComplete(audit_id, building_id, unit_id, amenity_id, element, toplevel = 0){
    	
    	if(element){
    		if($('#'+element).hasClass('a-circle-checked')){
		    	var title = 'MARK THIS INCOMPLETE?';
		    	var message = 'Are you sure you want to mark this incomplete?';
	    	}else{
		    	var title = 'MARK THIS COMPLETE?';
		    	var message = 'Are you sure you want to mark this complete?';
	    	}
    	}else{
    		var title = 'MARK THIS COMPLETE?';
		    var message = 'Are you sure you want to mark this complete?';
    	}
    	
    	
    	UIkit.modal.confirm('<div class="uk-grid"><div class="uk-width-1-1"><h2>'+title+'</h2></div><div class="uk-width-1-1"><hr class="dashed-hr uk-margin-bottom"><h3>'+message+'</h3></div>').then(function() {
		    	
		    	$.post('amenities/'+amenity_id+'/audit/'+audit_id+'/building/'+building_id+'/unit/'+unit_id+'/'+toplevel+'/complete', {
		            '_token' : '{{ csrf_token() }}'
		        }, function(data) {
		            if(data==0){ 
		                UIkit.modal.alert(data,{stack: true});
		            } else {console.log(data.status);
		            	if(data.status == 'complete'){
		            		if(toplevel == 1){
		            			UIkit.notification('<span uk-icon="icon: check"></span> Marked Completed', {pos:'top-right', timeout:1000, status:'success'});
			            		$('#'+element).toggleClass('a-circle');
			            		$('#'+element).toggleClass('a-circle-checked');
		            		}else if(amenity_id == 0){
		            			UIkit.notification('<span uk-icon="icon: check"></span> Marked Completed', {pos:'top-right', timeout:1000, status:'success'});
		            			$('[id^=completed-'+audit_id+building_id+']').removeClass('a-circle');
		            			$('[id^=completed-'+audit_id+building_id+']').addClass('a-circle-checked');
		            		}else{
		            			UIkit.notification('<span uk-icon="icon: check"></span> Marked Completed', {pos:'top-right', timeout:1000, status:'success'});
			            		$('#'+element).toggleClass('a-circle');
			            		$('#'+element).toggleClass('a-circle-checked');
		            		}

		            	}else{
		            		
		            		if(toplevel == 1){
		            			UIkit.notification('<span uk-icon="icon: check"></span> Marked Not Completed', {pos:'top-right', timeout:1000, status:'success'});
			            		$('#'+element).toggleClass('a-circle');
			            		$('#'+element).toggleClass('a-circle-checked');
		            		}else if(amenity_id == 0){
		            			UIkit.notification('<span uk-icon="icon: check"></span> Marked Not Completed', {pos:'top-right', timeout:1000, status:'success'});
		            			$('[id^=completed-'+audit_id+building_id+']').removeClass('a-circle-checked');
		            			$('[id^=completed-'+audit_id+building_id+']').addClass('a-circle');
		            		}else{
		            			UIkit.notification('<span uk-icon="icon: check"></span> Marked Not Completed', {pos:'top-right', timeout:1000, status:'success'});
			            		$('#'+element).toggleClass('a-circle-checked');
			            		$('#'+element).toggleClass('a-circle');
		            		}
		            	}
		            }
		        } );

		    	
		}, function () {
		    console.log('Rejected.')
		});
    }

    function updateAuditInspection(e){
    	e.preventDefault();
    	var form = $('#total_inspection_filter');

    	if($('#total_inspection_more').prop('checked')){
    		var total_inspection_filter = 0;
    	}else{
    		var total_inspection_filter = 1;
    	}
    	
    	var amount = $('#total_inspection_amount').val();

		$.post("/session/", {
            'data' : [['total_inspection_amount', amount], ['total_inspection_filter', total_inspection_filter]],
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            $('#totalinspectionbutton').trigger( 'click' );
            loadTab('{{ route('dashboard.audits') }}','1','','','',1);
        } );
    }

    function updateAuditAssignment(e){
    	e.preventDefault();
    	var form = $('#audit_assignment_selection');

		var selected = [];
		$('#audit_assignment_selection input:checked').each(function() {
		    selected.push($(this).attr('user-id'));
		});

		if(selected.length == 0){
			selected = 0;
		}

        $.post("/session/", {
            'data' : [['assignment-auditor', selected]],
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            $('#assignmentselectionbutton').trigger( 'click' );
            loadTab('{{ route('dashboard.audits') }}','1','','','',1);
        } );

    }

    function updateAuditScheduleAssignment(e){
    	e.preventDefault();
    	var form = $('#schedule_assignment_filter');

    	var alloptions = [];
		$('#schedule_assignment_filter input').each(function() {
		    alloptions.push([$(this).attr('id'), 0]);
		});

		var selected = [];
		$('#schedule_assignment_filter input:checked').each(function() {
		    selected.push([$(this).attr('id'), 1]);
		});

		$.post("/session/", {
            'data' : alloptions,
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            $.post("/session/", {
	            'data' : selected,
	            '_token' : '{{ csrf_token() }}'
	        }, function(data) {
	            $('#scheduleassignmentfilterbutton').trigger( 'click' );
	            loadTab('{{ route('dashboard.audits') }}','1','','','',1);
	        } );
        } );
    }

    function updateFileAuditStatus(e){
    	e.preventDefault();
    	var form = $('#file_audit_status_selection');

    	var alloptions = [];
		$('#file_audit_status_selection input').each(function() {
		    alloptions.push([$(this).attr('id'), 0]);
		});

		var selected = [];
		$('#file_audit_status_selection input:checked').each(function() {
		    selected.push([$(this).attr('id'), 1]);
		});

		$.post("/session/", {
            'data' : alloptions,
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            $.post("/session/", {
	            'data' : selected,
	            '_token' : '{{ csrf_token() }}'
	        }, function(data) {
	            $('#file_audit_status_button').trigger( 'click' );
	            loadTab('{{ route('dashboard.audits') }}','1','','','',1);
	        } );
        } );
    }

    function updateNLTAuditStatus(e){
    	e.preventDefault();
    	var form = $('#nlt_audit_status_selection');

    	var alloptions = [];
		$('#nlt_audit_status_selection input').each(function() {
		    alloptions.push([$(this).attr('id'), 0]);
		});

		var selected = [];
		$('#nlt_audit_status_selection input:checked').each(function() {
		    selected.push([$(this).attr('id'), 1]);
		});

		$.post("/session/", {
            'data' : alloptions,
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            $.post("/session/", {
	            'data' : selected,
	            '_token' : '{{ csrf_token() }}'
	        }, function(data) {
	            $('#nlt_audit_status_button').trigger( 'click' );
	            loadTab('{{ route('dashboard.audits') }}','1','','','',1);
	        } );
        } );
    }

    function updateLTAuditStatus(e){
    	e.preventDefault();
    	var form = $('#lt_audit_status_selection');

    	var alloptions = [];
		$('#lt_audit_status_selection input').each(function() {
		    alloptions.push([$(this).attr('id'), 0]);
		});

		var selected = [];
		$('#lt_audit_status_selection input:checked').each(function() {
		    selected.push([$(this).attr('id'), 1]);
		});

		$.post("/session/", {
            'data' : alloptions,
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            $.post("/session/", {
	            'data' : selected,
	            '_token' : '{{ csrf_token() }}'
	        }, function(data) {
	            $('#lt_audit_status_button').trigger( 'click' );
	            loadTab('{{ route('dashboard.audits') }}','1','','','',1);
	        } );
        } );
    }

    function updateAuditComplianceStatus(e){
    	e.preventDefault();
    	var form = $('#audit_compliance_status_selection');

    	var alloptions = [];
		$('#audit_compliance_status_selection input').each(function() {
		    alloptions.push([$(this).attr('id'), 0]);
		});

		var selected = [];
		$('#audit_compliance_status_selection input:checked').each(function() {
		    selected.push([$(this).attr('id'), 1]);
		});

		$.post("/session/", {
            'data' : alloptions,
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            $.post("/session/", {
	            'data' : selected,
	            '_token' : '{{ csrf_token() }}'
	        }, function(data) {
	            $('#checklist-button').trigger( 'click' );
	            loadTab('{{ route('dashboard.audits') }}','1','','','',1);
	        } );
        } );
    }

    @can('access_auditor')
    function updateAuditStepSelection(e){
		e.preventDefault();
		var form = $('#audit_steps_selection');

		var alloptions = [];
		$('#audit_steps_selection input').each(function() {
		    alloptions.push([$(this).attr('id'), 0]);
		});

		var selected = [];
		$('#audit_steps_selection input:checked').each(function() {
		    selected.push([$(this).attr('id'), 1]);
		});

		$.post("/session/", {
            'data' : alloptions,
            '_token' : '{{ csrf_token() }}'
        }, function(data) {
            $.post("/session/", {
	            'data' : selected,
	            '_token' : '{{ csrf_token() }}'
	        }, function(data) {
	            $('#checklist-button').trigger( 'click' );
	            loadTab('{{ route('dashboard.audits') }}','1','','','',1);
	        } );
        } );
	}
	@endcan
</script>
<script>

    new Vue({
        el: '#auditstable',
        
        data: function() {
             return {
                audits: {!! json_encode($data) !!},
                
                // page: 1,
                // loading: 1,
                // busy: false

            }
        },
        created: function() {
            this.loading = 0;
        },
        methods: {
        	
            
        },

        mounted: function() {
            console.log("Audits Mounted");
            Echo.private('updates.{{Auth::user()->id}}')
            .listen('UpdateEvent', (payload) => {
            			if(payload.data.event == 'audit'){
				        console.log('Audit event received.');
			                // if(data.is_reply){
			                //     console.log("user " + data.userId + " received a new reply for message "+data.id);
			                //     var updateddata = [{
			                //         id: data.id,
			                //         parentId: data.parent_id,
			                //         staffId: data.staff_class,
			                //         programId: data.program_class,
			                //         hasAttachment: data.attachment_class,
			                //         communicationId: data.communication_id,
			                //         communicationUnread: data.communication_unread_class,
			                //         createdDate: data.created,
			                //         createdDateRight: data.created_right,
			                //         recipients: data.recipients,
			                //         userBadgeColor: data.user_badge_color,
			                //         tooltip: data.tooltip,
			                //         unseen: data.unseen,
			                //         auditId: data.audit_id,
			                //         tooltipOrganization: data.tooltip_organization,
			                //         organizationAddress: data.organization_address,
			                //         tooltipFilenames: data.tooltip_filenames,
			                //         subject: data.subject,
			                //         summary: data.summary
			                //     }];
			                //     this.messages = this.messages.map(obj => updateddata.find(o => o.id === obj.id) || obj);
			                // }else{
			                //     console.log("audit " + data.id + " has been updated.");
			                //     this.messages.push({
			                //         id: data.id,
			                //         parentId: data.parent_id,
			                //         staffId: data.staff_class,
			                //         programId: data.program_class,
			                //         hasAttachment: data.attachment_class,
			                //         communicationId: data.communication_id,
			                //         communicationUnread: data.communication_unread_class,
			                //         createdDate: data.created,
			                //         createdDateRight: data.created_right,
			                //         recipients: data.recipients,
			                //         userBadgeColor: data.user_badge_color,
			                //         tooltip: data.tooltip,
			                //         unseen: data.unseen,
			                //         auditId: data.audit_id,
			                //         tooltipOrganization: data.tooltip_organization,
			                //         organizationAddress: data.organization_address,
			                //         tooltipFilenames: data.tooltip_filenames,
			                //         subject: data.subject,
			                //         summary: data.summary
			                //     });
			                // }
			            }
			    });
            
        }
    });

</script>

	<script>window.auditsLoaded = 1; </script>