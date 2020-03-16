<style type="text/css">
	.audit-list-report-icons i {
		font-size:22px;
		padding-top: 0px;
	}
	.audit-list-report-icons small {
		font-size: 14px;
		padding-top: 0px;
		margin-top: 0px;
		position: relative;
		top: -7px;
		color:black !important;
	}
	.audit-list-report-holder {
		height: 54px;
	}
	.finding-number {
	    font-size: 9px;
	    background: #666;
	    
	    border: 0px;
	    min-width: 13px;
	    max-height: 13px;
	    line-height: 1.5;
	}
	.on-folder {
	    position: relative;
	    left: -4px;
	    top: -17.5px;
	    font-size: 9px;
	    font-weight: bolder;
	}
	.on-boo-boo {
	    position: relative;
	    left: -4px;
	    top: -16.5px;
	    font-size: 9px;
	    font-weight: bolder;
	}
	.on-death {
	    position: relative;
	    left: -4px;
	    top: -16.5px;
	    font-size: 9px;
	    font-weight: bolder;
	}
</style>


<div id="audits" class="uk-no-margin-top" uk-grid>
	<div class="uk-margin-remove-top uk-width-1-1" uk-grid>
		<div id="auditsfilters" class="uk-width-1-1 uk-margin-top">
			
			<div class="uk-badge uk-align-right uk-label uk-margin-top uk-margin-right">{{ count($audits) }}  {{ count($audits) == 1 ?'AUDIT' : 'AUDITS' }} </div>
			@if(isset($auditFilterMineOnly) && $auditFilterMineOnly == 1)
			<div id="audit-filter-mine" class="uk-badge uk-text-right@s badge-filter">
				
				<a onClick="pmFilterAudits('audit-my-audits',0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>MY AUDITS ONLY</span></a>
				
			</div>
			@endif
			@if(isset($auditFilterProjectId) && $auditFilterProjectId != 0)
			<div id="audit-filter-project" class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('filter-search-project');" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>PROJECT/AUDIT ID "{{ $auditFilterProjectId }}"</span></a>
			</div>
			@endif
			@if(isset($auditFilterProjectName) && $auditFilterProjectName != '')
			<div id="audit-filter-name" class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('filter-search-pm', '');" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>PROJECT/PM NAME "{{ $auditFilterProjectName }}"</span></a>
			</div>
			@endif
			@if(isset($auditFilterAddress) && $auditFilterAddress != '')
			<div id="audit-filter-address" class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('filter-search-address', '');" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>ADDRESS "{{ $auditFilterAddress }}"</span></a>
			</div>
			@endif

			@foreach($steps as $step)
			@if(session($step->session_name) == 1)
			<div id="audit-filter-step" class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('{{ $step->session_name }}', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>STEP "{{ $step->name }}"</span></a>
			</div>
			@endif
			@endforeach

			@foreach($report_config['car_status'] as $rkey => $rvalue)
			{{-- CAR --}}
			@if(session($rkey) == 1)
			<div id="audit-filter-car" class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('{{ $rkey }}', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>CAR "{{ $rvalue }}"</span></a>
			</div>
			@endif
			@endforeach

			@foreach($report_config['ehs_status'] as $rkey => $rvalue)
			{{-- CAR --}}
			@if(session($rkey) == 1)
			<div id="audit-filter-ehs" class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('{{ $rkey }}', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>EHS "{{ $rvalue }}"</span></a>
			</div>
			@endif
			@endforeach

			@foreach($report_config['8823_status'] as $rkey => $rvalue)
			{{-- CAR --}}
			@if(session($rkey) == 1)
			<div id="audit-filter-8823" class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('{{ $rkey }}', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>8823 "{{ $rvalue }}"</span></a>
			</div>
			@endif
			@endforeach

			@if(isset($auditFilterComplianceRR) && $auditFilterComplianceRR != '0')
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('compliance-status-rr', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>UNITS REQUIRE REVIEW</span></a>
			</div>
			@endif

			@if(isset($auditFilterComplianceNC) && $auditFilterComplianceNC != '0')
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('compliance-status-nc', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>NOT COMPLIANT</span></a>
			</div>
			@endif

			@if(isset($auditFilterComplianceC) && $auditFilterComplianceC != '0')
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('compliance-status-c', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>COMPLIANT</span></a>
			</div>
			@endif

			@if(session('file-audit-status-h') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('file-audit-status-h', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS FILE AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(session('file-audit-status-r') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('file-audit-status-r', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS ALL RESOLVED FILE AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(session('file-audit-status-ar') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('file-audit-status-ar', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS UNRESOLVED FILE AUDIT FINDINGS</span></a>
			</div>
			@endif

			{{-- @if(session('file-audit-status-c') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('file-audit-status-c', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS CRITICAL FILE AUDIT FINDINGS</span></a>
			</div>
			@endif --}}

			@if(session('file-audit-status-nf') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('file-audit-status-nf', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>DOES NOT HAVE FILE AUDIT FINDINGS</span></a>
			</div>
			@endif


			@if(session('nlt-audit-status-h') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('nlt-audit-status-h', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS NLT AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(session('nlt-audit-status-r') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('nlt-audit-status-r', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS ALL RESOLVED NLT AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(session('nlt-audit-status-ar') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('nlt-audit-status-ar', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS UNRESOLVED NLT AUDIT FINDINGS</span></a>
			</div>
			@endif

			{{-- @if(session('nlt-audit-status-c') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('nlt-audit-status-c', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS CRITICAL NLT AUDIT FINDINGS</span></a>
			</div>
			@endif --}}

			@if(session('nlt-audit-status-nf') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('nlt-audit-status-nf', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>DOES NOT HAVE NLT AUDIT FINDINGS</span></a>
			</div>
			@endif


			@if(session('lt-audit-status-h') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('lt-audit-status-h', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS LT AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(session('lt-audit-status-r') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('lt-audit-status-r', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS ALL RESOLVED LT AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(session('lt-audit-status-ar') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('lt-audit-status-ar', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS UNRESOLVED LT AUDIT FINDINGS</span></a>
			</div>
			@endif
{{--
			@if(session('lt-audit-status-c') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('lt-audit-status-c', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS CRITICAL LT AUDIT FINDINGS</span></a>
			</div>
			@endif --}}

			@if(session('lt-audit-status-nf') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('lt-audit-status-nf', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>DOES NOT HAVE LT AUDIT FINDINGS</span></a>
			</div>
			@endif

			@if(isset($auditFilterInspection) && $auditFilterInspection != '')
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('total_inspection_amount', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>{{ $auditFilterInspection }}</span></a>
			</div>
			@endif

			@if(isset($auditBuildingFilterInspection) && $auditBuildingFilterInspection != '')
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('total_building_inspection_amount', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>{{ $auditBuildingFilterInspection }}</span></a>
			</div>
			@endif


			@if(session('schedule_assignment_unassigned') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('schedule_assignment_unassigned', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>AUDITS WITH UNASSIGNED AMENITIES</span></a>
			</div>
			@endif

			@if(session('schedule_assignment_not_enough') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('schedule_assignment_not_enough', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>AUDITS WITH NOT ENOUGH HOURS SCHEDULED</span></a>
			</div>
			@endif

			@if(session('schedule_assignment_no_estimated') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('schedule_assignment_no_estimated', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>AUDITS WITH NO ESTIMATED HOURS ENTERED</span></a>
			</div>
			@endif

			@if(session('documents_all') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('documents_all', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>ALL DOCUMENT STATUSES</span></a>
			</div>
			@endif
			@if(session('documents_reviewd') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('documents_reviewd', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>DOCUMENTS REVIEWED</span></a>
			</div>
			@endif
			@if(session('documents_needs_review') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('documents_needs_review', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>DOCUMENTS NEED REVIEW</span></a>
			</div>
			@endif
			@if(session('documents_not_found') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('documents_not_found', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>DOCUMENTS NOT AVAILABLE</span></a>
			</div>
			@endif

			@if(session('messages_unread') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('messages_unread', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>UNREAD MESSAGES</span></a>
			</div>
			@endif
			@if(session('messages_all_read') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('messages_all_read', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS NO UNREAD MESSAGES</span></a>
			</div>
			@endif
			@if(session('messages_not_available') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('messages_not_available', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>HAS NO MESSAGES</span></a>
			</div>
			@endif
			@if(session('schedule_date') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('schedule_date', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>SCHEDULE DATE: {{ strtoupper(session('daterange')) }}</span></a>
			</div>
			@endif
			@if(session('schedule_no_date') == 1)
			<div class="uk-badge uk-text-right@s badge-filter">
				<a onClick="pmFilterAudits('schedule_no_date', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>AUDITS WITH NO SCHEDULED DATE</span></a>
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
			foreach($auditors as $auditor){
				if(in_array($auditor->id, session('assignment-auditor'))){
					$counter++;
					if($counter < 3){
						if($assigned_auditors == ''){
							$assigned_auditors = strtoupper($auditor->name);
						}else{
							$assigned_auditors = $assigned_auditors.", ".strtoupper($auditor->name);
						}
					}
					if($assigned_auditors_hover_text == ''){
						$assigned_auditors_hover_text = strtoupper($auditor->name);
					}else{
						$assigned_auditors_hover_text = $assigned_auditors_hover_text.", ".strtoupper($auditor->name);
					}
				}
			}
			if($counter > 2){
				$counter = $counter - 2;
				$assigned_auditors = $assigned_auditors." +".$counter." MORE";
			}
			@endphp
			<div id="audit-filter-address" class="uk-badge uk-text-right@s badge-filter" uk-tooltip="title:{{ $assigned_auditors_hover_text }}">
				<a onClick="pmFilterAudits('assignment-auditor', 0);" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>@if(count(session('assignment-auditor')) > 1)AUDITORS ASSIGNED: @else AUDITOR ASSIGNED:@endif {{ $assigned_auditors }}</span></a>
			</div>
			@endif
		</div>
	</div>

	<div id="auditstable" class="uk-width-1-1 uk-overflow-auto" style="min-height: 700px;">
		<table class="uk-table @if(count($audits) < 50) uk-table-striped uk-table-hover @endIf uk-table-small uk-table-divider" style="min-width: 1320px;">
			<thead>
				<tr>
					<th  style="width:48px;">
						<div uk-grid>
							<div class="filter-box filter-icons uk-text-center uk-width-1-1 uk-link">
								<i class="a-avatar-star" ></i>
								<div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-left; mode: click;" style="top: 26px; left: 0px; text-align:left;">
									<form id="audit_assignment_selection" method="post">
										<fieldset class="uk-fieldset">
											<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">
												<h5>AUDITS FOR:</h5>
												<input id="assignment-auditor-{{ $current_user->id }}" user-id="{{ $current_user->id }}" class="assignmentauditor" type="checkbox" @if(session('audit-my-audits')) @if(session('audit-my-audits') == 1) checked @endif @endif/>
												<label for="assignment-auditor-{{ $current_user->id }}">{{ $current_user->name }}  (My Audits)</label>
												<hr class="dashed-hr uk-margin-bottom uk-width-1-1">
												@foreach($auditors as $auditor)
												<input id="assignment-auditor-{{ $auditor->id }}" user-id="{{ $auditor->id }}" class="assignmentauditor" type="checkbox" @if(is_array(session('assignment-auditor'))) @if(in_array($auditor->id, session('assignment-auditor')) == 1) checked @endif @endif/>
												<label for="assignment-auditor-{{ $auditor->id }}">{{ $auditor->name }}</label>
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

								</div>
							</div>
							<span uk-tooltip="delay:1000" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top" title="SORT BY LEAD AUDITOR">
								@if($sort_by == 'audit-sort-lead')
								<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="pmSortAuditList('audit-sort-lead', @php echo 1-$sort_order; @endphp);"></a>
								@else
								<a id="" class="sort-neutral uk-margin-small-top" onclick="pmSortAuditList('audit-sort-lead', 1);"></a>
								@endif
							</span>
						</div>
					</th>
					<th style="width:100px;">
						<div uk-grid>
							<div class="filter-box uk-width-1-1">
								<input id="filter-by-project" class="filter-box filter-search-project-input" type="text" placeholder="PROJECT" value="@if(session()->has('filter-search-project')){{ session('filter-search-project') }}@endif">
							</div>
							<span uk-tooltip="delay:1000" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top" title="SORT BY PROJECT ID">
								@if($sort_by == 'audit-sort-project')
								<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="pmSortAuditList('audit-sort-project', @php echo 1-$sort_order; @endphp, 'filter-search-project');"></a>
								@else
								<a id="" class="sort-neutral uk-margin-small-top" onclick="pmSortAuditList('audit-sort-project', 1, 'filter-search-project');"></a>
								@endif
							</span>
							<div class="uk-dropdown" aria-expanded="false"></div>
						</div>
					</th>
					<th>
						<div uk-grid>
							<div class="filter-box uk-width-1-1">
								<input id="filter-by-name" class="filter-box filter-search-pm-input" type="text" placeholder="PROJECT / PM NAME" value="@if(session()->has('filter-search-pm')){{ session('filter-search-pm') }}@endif">
							</div>
							<span uk-tooltip="delay:1000" class="uk-width-1-2 uk-padding-remove-top uk-margin-remove-top" title="SORT BY PROJECT NAME">
								@if($sort_by == 'audit-sort-project-name')
								<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="pmSortAuditList('audit-sort-project-name',  @php echo 1-$sort_order; @endphp, 'filter-search-pm-input');"></a>
								@else
								<a id="" class="sort-neutral uk-margin-small-top" onclick="pmSortAuditList('audit-sort-project-name', 1, 'filter-search-pm-input');"></a>
								@endif
							</span>
							<span uk-tooltip="delay:1000" class="uk-width-1-2 uk-padding-remove-top uk-margin-remove-top" title="SORT BY PROPERTY MANAGER NAME">
								@if($sort_by == 'audit-sort-pm')
								<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="pmSortAuditList('audit-sort-pm',  @php echo 1-$sort_order; @endphp, 'filter-search-pm-input');"></a>
								@else
								<a id="" class="sort-neutral uk-margin-small-top" onclick="pmSortAuditList('audit-sort-pm', 1, 'filter-search-pm-input');"></a>
								@endif
							</span>
							<div class="uk-dropdown" aria-expanded="false"></div>
						</div>
					</th>
					<th style="width: 350px;">
						<div uk-grid>
							<div class="filter-box uk-width-1-1">
								<input id="filter-by-address" class="filter-box filter-search-address-input" type="text" placeholder="PRIMARY ADDRESS" value="@if(session()->has('filter-search-address')){{ session('filter-search-address') }}@endif">
							</div>
							<span uk-tooltip="delay:1000" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top" title="SORT BY STREET ADDRESS">
								@if($sort_by == 'audit-sort-address')
								<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="pmSortAuditList('audit-sort-address',  @php echo 1-$sort_order; @endphp, 'filter-search-address-input');"></a>
								@else
								<a id="" class="sort-neutral uk-margin-small-top" onclick="pmSortAuditList('audit-sort-address', 1, 'filter-search-address-input');"></a>
								@endif
							</span>
							<span uk-tooltip="delay:1000" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top" title="SORT BY CITY">
								@if($sort_by == 'audit-sort-city')
								<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="pmSortAuditList('audit-sort-city',  @php echo 1-$sort_order; @endphp, 'filter-search-address-input');"></a>
								@else
								<a id="" class="sort-neutral uk-margin-small-top" onclick="pmSortAuditList('audit-sort-city', 1, 'filter-search-address-input');"></a>
								@endif
							</span>
							<span uk-tooltip="delay:1000" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top" title="SORT BY ZIP">
								@if($sort_by == 'audit-sort-zip')
								<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="pmSortAuditList('audit-sort-zip',  @php echo 1-$sort_order; @endphp, 'filter-search-address-input');"></a>
								@else
								<a id="" class="sort-neutral uk-margin-small-top" onclick="pmSortAuditList('audit-sort-zip', 1, 'filter-search-address-input');"></a>
								@endif
							</span>
							<div class="uk-dropdown" aria-expanded="false"></div>
						</div>
					</th>
					<th style="width:185px;" >
						<div uk-grid>
							<div class="filter-box filter-date-aging uk-vertical-align uk-width-1-1" uk-grid>
								<!-- SPAN TAG TITLE NEEDS UPDATED TO REFLECT CURRENT DATE RANGE -->
								<span class="uk-width-1-2  uk-text-center uk-padding-remove-top uk-margin-remove-top" >
									<span id="daterangefilterbutton" class="use-hand-cursor">
										<i class="a-calendar-8 uk-vertical-align-middle  use-hand-cursor"></i> <i class="uk-icon-asterisk  uk-vertical-align-middle uk-text-small tiny-middle-text  use-hand-cursor"></i> <i class="a-calendar-8 uk-vertical-align-middle  use-hand-cursor" onclick="$('#daterange').trigger( 'click' );"></i>
									</span>
									<div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="top: 26px; left: 0px; text-align:left;">
										<form id="daterange_filter" method="post">
											<fieldset class="uk-fieldset">
												<div class="uk-margin uk-child-width-auto uk-grid">
													<input id="schedule_no_date" class="" type="checkbox" @if(session('schedule_no_date') == 1) checked @endif/>
													<label for="schedule_no_date">NO SCHEDULED DATE</label>
													<input id="schedule_date" class="" type="checkbox" @if(session('schedule_date') == 1) checked @endif/>
													<label for="schedule_date">DATE RANGE</label>
													<div class="uk-form-controls">
														{{-- October 3, 2019 to October 17, 2019 --}}
														<input type="text" id="daterange" name="daterange" value="{{ session('schedule_date') == 1 ? session('daterange') : '' }}" class="uk-input flatpickr flatpickr-input active"/>
													</div>
												</div>
												<div class="uk-margin-remove" uk-grid>
													<div class="uk-width-1-2">
														<button onclick="updateAuditScheduleDate(event);" class="uk-button uk-button-primary uk-width-1-1"><i class="fas fa-filter"></i> APPLY FILTER</button>
													</div>
													<div class="uk-width-1-2">
														<button onclick="$('#daterangefilterbutton').trigger( 'click' );return false;" class="uk-button uk-button-secondary uk-width-1-1"><i class="a-circle-cross"></i> CANCEL</button>
													</div>
												</div>

											</fieldset>
										</form>
									</div>
								</span>
								
								<span class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-text-right uk-link">
									<i id="totalbuildinginspectionbutton" class="a-buildings"></i>
									<div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="top: 26px; left: 0px; text-align:left;">
										<form id="total_building_inspection_filter" method="post">
											<fieldset class="uk-fieldset">
												<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">
													<input id="total_building_inspection_more" class="totalinspectionfilter" type="checkbox" @if(session('total_building_inspection_filter') != 1) checked @endif/>
													<label for="total_building_inspection_more">MORE THAN OR EQUAL TO</label>
													<input id="total_building_inspection_less" class="totalinspectionfilter" type="checkbox" @if(session('total_building_inspection_filter') == 1) checked @endif/>
													<label for="total_building_inspection_less">LESS THAN OR EQUAL TO</label>
													<input id="total_building_inspection_amount" class="uk-input" value="{{ session('total_building_inspection_amount') }}" type="number" min="0" >

												</div>
												<div class="uk-margin-remove" uk-grid>
													<div class="uk-width-1-2">
														<button onclick="updateAuditBuildingInspection(event);" class="uk-button uk-button-primary uk-width-1-1"><i class="fas fa-filter"></i> APPLY FILTER</button>
													</div>
													<div class="uk-width-1-2">
														<button onclick="$('#totalbuildinginspectionbutton').trigger( 'click' );return false;" class="uk-button uk-button-secondary uk-width-1-1"><i class="a-circle-cross"></i> CANCEL</button>
													</div>
												</div>
											</fieldset>
										</form>
									</div>
								</span>
								<span class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-text-right uk-link">
									<i id="totalinspectionbutton" class="a-buildings-2"></i>
									<div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="top: 26px; left: 0px; text-align:left;">
										<form id="total_inspection_filter" method="post">
											<fieldset class="uk-fieldset">
												<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">
													<input id="total_inspection_more" class="totalinspectionfilter" type="checkbox" @if(session('total_inspection_filter') != 1) checked @endif/>
													<label for="total_inspection_more">MORE THAN OR EQUAL TO</label>
													<input id="total_inspection_less" class="totalinspectionfilter" type="checkbox" @if(session('total_inspection_filter') == 1) checked @endif/>
													<label for="total_inspection_less">LESS THAN OR EQUAL TO</label>
													<input id="total_inspection_amount" class="uk-input" value="{{ session('total_inspection_amount') }}" type="number" min="0" >

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
								
							</div>
							<span uk-tooltip="delay:1000" class="uk-width-1-2 uk-padding-remove-top uk-margin-remove-top" title="SORT BY SCHEDULED DATE">
								@if($sort_by == 'audit-sort-scheduled-date')
								<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="pmSortAuditList('audit-sort-scheduled-date',  @php echo 1-$sort_order; @endphp);"></a>
								@else
								<a id="" class="sort-neutral uk-margin-small-top" onclick="pmSortAuditList('audit-sort-scheduled-date', 1);"></a>
								@endif
							</span>
							
							<span uk-tooltip="delay:1000" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="SORT BY TOTAL ASSIGNED INSPECTION AREAS">
								@if($sort_by == 'audit-sort-assigned-areas')
								<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="pmSortAuditList('audit-sort-assigned-areas',  @php echo 1-$sort_order; @endphp);"></a>
								@else
								<a id="" class="sort-neutral uk-margin-small-top" onclick="pmSortAuditList('audit-sort-assigned-areas', 1);"></a>
								@endif
							</span>
							<span uk-tooltip="delay:1000" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="SORT BY TOTAL INSPECTION AREAS">
								@if($sort_by == 'audit-sort-total-areas')
								<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="pmSortAuditList('audit-sort-total-areas',  @php echo 1-$sort_order; @endphp);"></a>
								@else
								<a id="" class="sort-neutral uk-margin-small-top" onclick="pmSortAuditList('audit-sort-total-areas', 1);"></a>
								@endif
							</span>

							
						</div>
					</th>
					<th style=" max-width: 50px;">
						<div uk-grid>
							
							<div class="filter-box filter-date-aging uk-vertical-align uk-width-1-1 uk-text-center fullwidth" uk-grid>
								<!-- SPAN TAG TITLE NEEDS UPDATED TO REFLECT CURRENT DATE RANGE -->
								@php 
									// Allowed Statuses to Show


								@endphp
								<span class="uk-width-1-3 uk-remove-margin uk-padding-remove uk-text-center uk-link">
									<i id="carselectionbutton" class="a-file-chart-3"></i>
									<div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="top: 26px; left: 0px; text-align:left;">
										<form id="car_report_selection" method="post">
											<fieldset class="uk-fieldset">
												<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">
													@foreach($report_config['pm_car_status'] as $rkey => $rvalue)

													<input id="{{ $rkey }}" class="{{ $rkey != 'car-report-selection-all' ? 'carselector' : '' }}" type="checkbox" @if(session($rkey) == 1) checked @endif/>
													<label for="{{ $rkey }}">{{ $rvalue }}</label>
													@endforeach
												</div>
												<div class="uk-margin-remove" uk-grid>
													<div class="uk-width-1-2">
														<button onclick="updateCarFilter(event);" class="uk-button uk-button-primary uk-width-1-1"><i class="fas fa-filter"></i> APPLY FILTER</button>
													</div>
													<div class="uk-width-1-2">
														<button onclick="$('#carselectionbutton').trigger( 'click' );return false;" class="uk-button uk-button-secondary uk-width-1-1"><i class="a-circle-cross"></i> CANCEL</button>
													</div>
												</div>
											</fieldset>
										</form>
									</div>
								</span>

								<span class="uk-width-1-3 uk-remove-margin uk-padding-remove uk-text-center uk-link">
									<i id="ehsselectionbutton" class="a-file-chart-3"></i>
									<div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="top: 26px; left: 0px; text-align:left;">
										<form id="ehs_report_selection" method="post">
											<fieldset class="uk-fieldset">
												<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">
													@foreach($report_config['pm_ehs_status'] as $rkey => $rvalue)
													<input id="{{ $rkey }}" class="{{ $rkey != 'ehs-report-selection-all' ? 'ehsselector' : '' }}" type="checkbox" @if(session($rkey) == 1) checked @endif/>
													<label for="{{ $rkey }}">{{ $rvalue }}</label>
													@endforeach
												</div>
												<div class="uk-margin-remove" uk-grid>
													<div class="uk-width-1-2">
														<button onclick="updateEhsFilter(event);" class="uk-button uk-button-primary uk-width-1-1"><i class="fas fa-filter"></i> APPLY FILTER</button>
													</div>
													<div class="uk-width-1-2">
														<button onclick="$('#ehsselectionbutton').trigger( 'click' );return false;" class="uk-button uk-button-secondary uk-width-1-1"><i class="a-circle-cross"></i> CANCEL</button>
													</div>
												</div>
											</fieldset>
										</form>
									</div>
								</span>
								@if(env('APP_ENV') != 'production')
								<span class="uk-width-1-3 uk-remove-margin uk-padding-remove uk-text-center uk-link">
									<i id="8823selectionbutton" class="a-file-chart-3"></i>
									<div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="top: 26px; left: 0px; text-align:left;">
										<form id="8823_report_selection" method="post">
											<fieldset class="uk-fieldset">
												<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">
													@foreach($report_config['pm_8823_status'] as $rkey => $rvalue)
													<input id="{{ $rkey }}" class="{{ $rkey != '8823-report-selection-all' ? '8823selector' : '' }}" type="checkbox" @if(session($rkey) == 1) checked @endif/>
													<label for="{{ $rkey }}">{{ $rvalue }}</label>
													@endforeach
												</div>
												<div class="uk-margin-remove" uk-grid>
													<div class="uk-width-1-2">
														<button onclick="update8823Filter(event);" class="uk-button uk-button-primary uk-width-1-1"><i class="fas fa-filter"></i> APPLY FILTER</button>
													</div>
													<div class="uk-width-1-2">
														<button onclick="$('#8823selectionbutton').trigger( 'click' );return false;" class="uk-button uk-button-secondary uk-width-1-1"><i class="a-circle-cross"></i> CANCEL</button>
													</div>
												</div>
											</fieldset>
										</form>
									</div>
								</span>
								@endif

								
							</div>
							<span  class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top" >
								@if($sort_by == 'audit-sort-followup-date')
								<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top"></a>
								@else
								<a id="" class="sort-neutral uk-margin-small-top" onclick=""></a>
								@endif
							</span>
						</div>
					</th>
					<th style="@if($auditor_access) width: 100px; @else max-width: 103px; @endif ">
						<div uk-grid>
							<div class="filter-box filter-icons uk-vertical-align uk-width-1-1" uk-grid>
								<span class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top uk-link uk-text-center">
									<i id="file_audit_status_button" class="a-folder"></i>
									<div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="width: 420px; top: 26px; left: 0px; text-align:left;">
										<form id="file_audit_status_selection" method="post">
											<fieldset class="uk-fieldset">
												<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">



													<input id="file-audit-status-h" class="fileauditselector" type="checkbox" @if(session('file-audit-status-h') == 1) checked @endif/>
													<label for="file-audit-status-h"><i class="a-folder "></i><span class="">HAS FILE AUDIT FINDINGS</span></label>

													<input id="file-audit-status-r" class="fileauditselector" type="checkbox" @if(session('file-audit-status-r') == 1) checked @endif/>
													<label for="file-audit-status-r"><i class="a-folder ok-actionable divider dividericon"></i><span class="ok-actionable">HAS ALL RESOLVED FILE AUDIT FINDINGS</span></label>



													<input id="file-audit-status-ar" class=" fileauditselector" type="checkbox" @if(session('file-audit-status-ar') == 1) checked @endif/>
													<label for="file-audit-status-ar"><i class="a-folder action-needed divider dividericon"></i> <span class="action-needed">HAS UNRESOLVED FILE AUDIT FINDINGS</span></label>

													{{-- <input id="file-audit-status-c" class=" fileauditselector" type="checkbox" @if(session('file-audit-status-c') == 1) checked @endif/>
													<label for="file-audit-status-c"><i class="a-folder action-required divider dividericon"></i> <span class="action-required">HAS CRITICAL FILE AUDIT FINDINGS</span></label> --}}

													<input id="file-audit-status-nf" class=" fileauditselector" type="checkbox" @if(session('file-audit-status-nf') == 1) checked @endif/>
													<label for="file-audit-status-nf"><i class="a-folder"></i> <span class="">DOES NOT HAVE FILE AUDIT FINDINGS</span></label>

												</div>
												<div class="uk-margin-remove" uk-grid>
													<div class="uk-width-1-2">
														<button onclick="updateFileAuditStatus(event);" class="uk-button uk-button-primary uk-width-1-1"><i class="fas fa-filter uk-tex"></i> APPLY FILTER</button>
													</div>
													<div class="uk-width-1-2">
														<button onclick="$('#file_audit_status_button').trigger( 'click' );return false;" class="uk-button uk-button-secondary uk-width-1-1"><i class="a-circle-cross"></i> CANCEL</button>
													</div>
												</div>
											</fieldset>
										</form>

									</div>
								</span>
								<span class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top uk-link uk-text-center">
									<i id="nlt_audit_status_button" class="a-booboo uk-text-center"></i>
									<div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="width: 420px; top: 26px; left: 0px; text-align:left;">
										<form id="nlt_audit_status_selection" method="post">
											<fieldset class="uk-fieldset">
												<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">



													<input id="nlt-audit-status-h" class="nltauditselector" type="checkbox" @if(session('nlt-audit-status-h') == 1) checked @endif/>
													<label for="nlt-audit-status-h"><i class="a-booboo  "></i><span class="">HAS NLT AUDIT FINDINGS</span></label>

													<input id="nlt-audit-status-r" class="nltauditselector" type="checkbox" @if(session('nlt-audit-status-r') == 1) checked @endif/>
													<label for="nlt-audit-status-r"><i class="a-booboo ok-actionable divider dividericon"></i><span class="ok-actionable">HAS ALL RESOLVED NLT AUDIT FINDINGS</span></label>

													<input id="nlt-audit-status-ar" class=" nltauditselector" type="checkbox" @if(session('nlt-audit-status-ar') == 1) checked @endif/>
													<label for="nlt-audit-status-ar"><i class="a-booboo action-needed divider dividericon"></i> <span class="action-needed">HAS UNRESOLVED NLT AUDIT FINDINGS</span></label>

													{{-- <input id="nlt-audit-status-c" class=" nltauditselector" type="checkbox" @if(session('nlt-audit-status-c') == 1) checked @endif/>
													<label for="nlt-audit-status-c"><i class="a-booboo action-required divider dividericon"></i> <span class="action-required">HAS CRITICAL NLT AUDIT FINDINGS</span></label> --}}

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
								<span class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top uk-link uk-text-center">
									<i id="lt_audit_status_button" class="a-skull"></i>
									<div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="width: 420px; top: 26px; left: 0px; text-align:left;">
										<form id="lt_audit_status_selection" method="post">
											<fieldset class="uk-fieldset">
												<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">



													<input id="lt-audit-status-h" class="ltauditselector" type="checkbox" @if(session('lt-audit-status-h') == 1) checked @endif/>
													<label for="lt-audit-status-h"><i class="a-skull "></i><span class="">HAS LT AUDIT FINDINGS</span></label>

													<input id="lt-audit-status-r" class="ltauditselector" type="checkbox" @if(session('lt-audit-status-r') == 1) checked @endif/>
													<label for="lt-audit-status-r"><i class="a-skull ok-actionable divider dividericon"></i><span class="ok-actionable">HAS ALL RESOLVED LT AUDIT FINDINGS</span></label>

													<input id="lt-audit-status-ar" class=" ltauditselector" type="checkbox" @if(session('lt-audit-status-ar') == 1) checked @endif/>
													<label for="lt-audit-status-ar"><i class="a-skull action-needed divider dividericon"></i> <span class="action-needed">HAS UNRESOLVED LT AUDIT FINDINGS</span></label>

													{{-- <input id="lt-audit-status-c" class=" ltauditselector" type="checkbox" @if(session('lt-audit-status-c') == 1) checked @endif/>
													<label for="lt-audit-status-c"><i class="a-skull action-required divider dividericon"></i> <span class="action-required">HAS CRITICAL LT AUDIT FINDINGS</span></label> --}}

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
							<span uk-tooltip="delay:1000" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top" title="SORT BY FILE FINDING COUNT">
								@if($sort_by == 'audit-sort-finding-file')
								<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="pmSortAuditList('audit-sort-finding-file',  @php echo 1-$sort_order; @endphp);"></a>
								@else
								<a id="" class="sort-neutral uk-margin-small-top" onclick="pmSortAuditList('audit-sort-finding-file', 1);"></a>
								@endif
							</span>
							<span uk-tooltip="delay:1000" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top" title="SORT BY NLT FINDING COUNT">
								@if($sort_by == 'audit-sort-finding-nlt')
								<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="pmSortAuditList('audit-sort-finding-nlt',  @php echo 1-$sort_order; @endphp);"></a>
								@else
								<a id="" class="sort-neutral uk-margin-small-top" onclick="pmSortAuditList('audit-sort-finding-nlt', 1);"></a>
								@endif
							</span>
							<span uk-tooltip="delay:1000" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top" title="SORT BY LT FINDING COUNT">
								@if($sort_by == 'audit-sort-finding-lt')
								<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="pmSortAuditList('audit-sort-finding-lt',  @php echo 1-$sort_order; @endphp);"></a>
								@else
								<a id="" class="sort-neutral uk-margin-small-top" onclick="pmSortAuditList('audit-sort-finding-lt', 1);"></a>
								@endif
							</span>
						</div>
					</th>
					<th  @if($auditor_access) style="width: 140px;" @else style="max-width: 70px;" @endif >
						<div uk-grid>
							<div class="filter-box filter-icons uk-vertical-align uk-width-1-1" uk-grid>
								
								<span class=" uk-width-1-2 uk-padding-remove-top uk-margin-remove-top uk-link uk-text-center">
									<i id="messagesfilter" class="a-envelope-4"></i>
									<div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="top: 26px; left: 0px; text-align:left;">
										<form id="messages_filter" method="post">
											<fieldset class="uk-fieldset">
												<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">
													<input id="messages_unread" class="" type="checkbox" @if(session('messages_unread') == 1) checked @endif/>
													<label for="messages_unread">UNREAD MESSAGES</label>
													<input id="messages_not_available" class="messagesselector" type="checkbox" @if(session('messages_not_available') == 1) checked @endif/>
													<label for="messages_not_available">HAS NO MESSAGES</label>
													<input id="messages_all_read" class="messagesselector" type="checkbox" @if(session('messages_all_read') == 1) checked @endif/>
													<label for="messages_all_read">HAS NO UNREAD MESSAGES</label>
												</div>
												<div class="uk-margin-remove" uk-grid>
													<div class="uk-width-1-2">
														<button onclick="updateAuditMessages(event);" class="uk-button uk-button-primary uk-width-1-1"><i class="fas fa-filter"></i> APPLY FILTER</button>
													</div>
													<div class="uk-width-1-2">
														<button onclick="$('#messagesfilter').trigger( 'click' );return false;" class="uk-button uk-button-secondary uk-width-1-1"><i class="a-circle-cross"></i> CANCEL</button>
													</div>
												</div>
											</fieldset>
										</form>
									</div>



								</span>
								<span class="uk-width-1-2 uk-padding-remove-top uk-margin-remove-top uk-link uk-text-center">
									<i id="documentfilter" class="a-files"></i>
									<div class="uk-dropdown uk-dropdown-bottom filter-dropdown " uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="top: 26px; left: 0px; text-align:left;">
										<form id="document_filter" method="post">
											<fieldset class="uk-fieldset">
												<div class="dropdown-max-height uk-margin uk-child-width-auto uk-grid">
													<input id="documents_all" class="" type="checkbox" @if(session('documents_all') == 1) checked @endif/>
													<label for="documents_all">ALL DOCUMENT STATUSES</label>
													<input id="documents_needs_review" class="documentselector" type="checkbox" @if(session('documents_needs_review') == 1) checked @endif/>
													<label for="documents_needs_review">DOCUMENT NEEDS REVIEW</label>
													<input id="documents_reviewd" class="documentselector" type="checkbox" @if(session('documents_reviewd') == 1) checked @endif/>
													<label for="documents_reviewd">DOCUMENTS REVIEWED</label>
													<input id="documents_not_found" class="documentselector" type="checkbox" @if(session('documents_not_found') == 1) checked @endif/>
													<label for="documents_not_found">DOCUMENTS NOT FOUND</label>
												</div>
												<div class="uk-margin-remove" uk-grid>
													<div class="uk-width-1-2">
														<button onclick="updateAuditDocuments(event);" class="uk-button uk-button-primary uk-width-1-1"><i class="fas fa-filter"></i> APPLY FILTER</button>
													</div>
													<div class="uk-width-1-2">
														<button onclick="$('#documentfilterbutton').trigger( 'click' );return false;" class="uk-button uk-button-secondary uk-width-1-1"><i class="a-circle-cross"></i> CANCEL</button>
													</div>
												</div>
											</fieldset>
										</form>
									</div>
								</span>

							</div>
							
							<span uk-tooltip="delay:1000" class="@if($auditor_access) uk-width-1-4 @else uk-width-1-2 @endif uk-padding-remove-top uk-margin-remove-top" title="SORT BY MESSAGE STATUS">
								@if($sort_by == 'audit-sort-status-message')
								<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="pmSortAuditList('audit-sort-status-message',  @php echo 1-$sort_order; @endphp);"></a>
								@else
								<a id="" class="sort-neutral uk-margin-small-top" onclick="pmSortAuditList('audit-sort-status-message', 1);"></a>
								@endif
							</span>
							<span uk-tooltip="delay:1000" class="@if($auditor_access) uk-width-1-4 @else uk-width-1-2 @endif uk-padding-remove-top uk-margin-remove-top" title="SORT BY DOCUMENT STATUS">
								@if($sort_by == 'audit-sort-status-document')
								<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="pmSortAuditList('audit-sort-status-document',  @php echo 1-$sort_order; @endphp);"></a>
								@else
								<a id="" class="sort-neutral uk-margin-small-top" onclick="pmSortAuditList('audit-sort-status-document', 1);"></a>
								@endif
							</span>


						</div>

					</th>
					
					
					<th style="vertical-align:top; ">
						<div uk-grid>
							<div class="uk-link uk-width-1-1 archived-icon" onclick="toggleArchivedAudits();" uk-tooltip="delay:1000" title="CLICK TO HIDE ARCHIVED AUDITS" style="height: 30px;">
								<span class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-link uk-text-center">
									<i class="a-folder-box"></i>
								</span>
							</div>
							<div class="uk-link uk-width-1-1 archived-icon selected" onclick="toggleArchivedAudits();" uk-tooltip="delay:1000" title="CLICK TO SHOW ARCHIVED AUDITS" style="display:none; height: 30px;">
								<span class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top  uk-contrast uk-text-center">
									<i class="a-folder-box"></i>
								</span>
							</div>
						</div>
					</th>
					
				</tr>
			</thead>
			<tbody>
				<?php $latestCachedAudit = '2000-01-01 12:00:00';?>
				@foreach($audits as $audit)
				<?php if (strtotime($audit->updated_at) > strtotime($latestCachedAudit)) {
	$latestCachedAudit = $audit->updated_at;
}
?>
				<tr id="audit-r-{{ $audit->audit_id }}" class="{{ $audit['status'] }} @if($audit['status'] != 'critical') notcritical @endif" style=" @if(session('audit-hidenoncritical') == 1 && $audit['status'] != 'critical') display:none; @endif ">
					@include('dashboard.partials.pm_audit_row')
				</tr>
				@endforeach

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


<script>
	var hide_confirm_modal_js = "{{ session()->has('hide_confirm_modal') }}";
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

		$('#documents_all').click(function() {
			if($('#documents_all').prop('checked')){
				$('input.documentselector').prop('checked', false);
			}
		});

		$('.documentselector').click(function() {
			if($(this).prop('checked') && $('#documents_all').prop('checked')){
				$('#documents_all').prop('checked', false);
			}
		});



		@if($auditor_access)
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

		$('#car-report-selection-all').click(function() {
			if($('#car-report-selection-all').prop('checked')){
				$('input.carselector').prop('checked', false);
			}
		});

		$('.carselector').click(function() {
			if($(this).prop('checked') && $('#car-report-selection-all').prop('checked')){
				$('#car-report-selection-all').prop('checked', false);
			}
		});

		$('#ehs-report-selection-all').click(function() {
			if($('#ehs-report-selection-all').prop('checked')){
				$('input.ehsselector').prop('checked', false);
			}
		});

		$('.ehsselector').click(function() {
			if($(this).prop('checked') && $('#ehs-report-selection-all').prop('checked')){
				$('#ehs-report-selection-all').prop('checked', false);
			}
		});

		$('#8823-report-selection-all').click(function() {
			if($('#8823-report-selection-all').prop('checked')){
				$('input.8823selector').prop('checked', false);
			}
		});

		$('.8823selector').click(function() {
			if($(this).prop('checked') && $('#8823-report-selection-all').prop('checked')){
				$('#8823-report-selection-all').prop('checked', false);
			}
		});
		@endif

		$("#filter-by-project").keypress(function(event) {
			if (event.which == 13) {
				event.preventDefault();
				if($(this).val() != ''){
					pmFilterAuditList(this, 'filter-search-project');
				}else{
					pmFilterAudits('filter-search-project', '');
				}
			}
		});

		$("#filter-by-name").keypress(function(event) {
			if (event.which == 13) {
				event.preventDefault();
				if($(this).val() != ''){
					pmFilterAuditList(this, 'filter-search-pm');
				}else{
					pmFilterAudits('filter-search-pm', '');
				}
			}
		});

		$("#filter-by-address").keypress(function(event) {
			if (event.which == 13) {
				event.preventDefault();
				if($(this).val() != ''){
					pmFilterAuditList(this, 'filter-search-address');
				}else{
					pmFilterAudits('filter-search-address', '');
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








function updateAuditInspection(e){
	e.preventDefault();
	var form = $('#total_inspection_filter');
	// debugger;
	if($('#total_inspection_more').prop('checked')){
		var total_inspection_filter = 0;
	}else{
		var total_inspection_filter = 1;
	}

	var amount = $('#total_inspection_amount').val();

	$.post("/pmsession-new/", {
		'data' : [['total_inspection_amount', amount], ['total_inspection_filter', total_inspection_filter]],
		'_token' : '{{ csrf_token() }}'
	}, function(data) {
		$('#totalinspectionbutton').trigger( 'click' );
		loadTab('{{ route('dashboard.pmaudits') }}','1','','','',1);
	} );
}

function updateAuditBuildingInspection(e) {
	e.preventDefault();
	var form = $('#total_building_inspection_filter');
	// debugger;
	if($('#total_building_inspection_more').prop('checked')){
		var total_building_inspection_filter = 0;
	}else{
		var total_building_inspection_filter = 1;
	}

	var amount = $('#total_building_inspection_amount').val();
		// $('#totalbuildinginspectionbutton').trigger( 'click' );

		$.post("/pmsession-new/", {
			'data' : [['total_building_inspection_amount', amount], ['total_building_inspection_filter', total_building_inspection_filter]],
			'_token' : '{{ csrf_token() }}'
		}, function(data) {
			$('#totalbuildinginspectionbutton').trigger( 'click' );
			// UIkit.notification({
			// 	message: 'Buildings inspection filter applied',
			// 	status: 'success',
			// 	pos: 'top-right',
			// 	timeout: 5000
			// });
			loadTab('{{ route('dashboard.pmaudits') }}','1','','','',1);
		});
	}

	function updateAuditAssignment(e){
		e.preventDefault();
		var form = $('#audit_assignment_selection');

		var selected = [];
		$('#audit_assignment_selection input:checked').each(function() {
			selected.push($(this).attr('user-id'));
		});
		//trigger audits-my-audits if logged in user is selected
		var myAudits = 0;
		if(selected.includes("{{$current_user->id}}")){
			myAudits = 1;
					//remove from array
					for( var i = 0; i < selected.length; i++){
						if ( selected[i] === "{{$current_user->id}}") {
							selected.splice(i, 1);

						}
					}
				}
				if(selected.length == 0){
					selected = 0;
				}


				$.post("/pmsession-new/", {
					'data' : [['assignment-auditor', selected],['audit-my-audits',myAudits]],
					'_token' : '{{ csrf_token() }}'
				}, function(data) {
					$('#assignmentselectionbutton').trigger( 'click' );
					loadTab('{{ route('dashboard.pmaudits') }}','1','','','',1);
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
				setSession(alloptions, selected, '#scheduleassignmentfilterbutton');

				// $.post("/session/", {
				// 	'data' : alloptions,
				// 	'_token' : '{{ csrf_token() }}'
				// }, function(data) {
				// 	$.post("/session/", {
				// 		'data' : selected,
				// 		'_token' : '{{ csrf_token() }}'
				// 	}, function(data) {
				// 		$('#scheduleassignmentfilterbutton').trigger( 'click' );
				// 		loadTab('{{ route('dashboard.audits') }}','1','','','',1);
				// 	} );
				// } );
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
				setSession(alloptions, selected, '#file_audit_status_button');

				// $.post("/session/", {
				// 	'data' : alloptions,
				// 	'_token' : '{{ csrf_token() }}'
				// }, function(data) {
				// 	$.post("/session/", {
				// 		'data' : selected,
				// 		'_token' : '{{ csrf_token() }}'
				// 	}, function(data) {
				// 		$('#file_audit_status_button').trigger( 'click' );
				// 		loadTab('{{ route('dashboard.audits') }}','1','','','',1);
				// 	} );
				// } );
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
				setSession(alloptions, selected, '#nlt_audit_status_button');

				// $.post("/session/", {
				// 	'data' : alloptions,
				// 	'_token' : '{{ csrf_token() }}'
				// }, function(data) {
				// 	$.post("/session/", {
				// 		'data' : selected,
				// 		'_token' : '{{ csrf_token() }}'
				// 	}, function(data) {
				// 		$('#nlt_audit_status_button').trigger( 'click' );
				// 		loadTab('{{ route('dashboard.audits') }}','1','','','',1);
				// 	} );
				// } );
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
				setSession(alloptions, selected, '#lt_audit_status_button');

				// $.post("/session/", {
				// 	'data' : alloptions,
				// 	'_token' : '{{ csrf_token() }}'
				// }, function(data) {
				// 	$.post("/session/", {
				// 		'data' : selected,
				// 		'_token' : '{{ csrf_token() }}'
				// 	}, function(data) {
				// 		$('#lt_audit_status_button').trigger( 'click' );
				// 		loadTab('{{ route('dashboard.audits') }}','1','','','',1);
				// 	} );
				// } );
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

				setSession(alloptions, selected, '#checklist-button');

				// $.post("/session/", {
				// 	'data' : alloptions,
				// 	'_token' : '{{ csrf_token() }}'
				// }, function(data) {
				// 	$.post("/session/", {
				// 		'data' : selected,
				// 		'_token' : '{{ csrf_token() }}'
				// 	}, function(data) {
				// 		$('#checklist-button').trigger( 'click' );
				// 		loadTab('{{ route('dashboard.audits') }}','1','','','',1);
				// 	} );
				// } );
			}

			// function setSessionOld(alloptions, selected) {
			// 	debugger;
			// 	$.post("/session/", {
			// 		'data' : alloptions,
			// 		'_token' : '{{ csrf_token() }}'
			// 	}, function(data) {
			// 		$.post("/session/", {
			// 			'data' : selected,
			// 			'_token' : '{{ csrf_token() }}'
			// 		}, function(data) {
			// 			console.log('Applied session');
			// 			// return true;
			// 		});
			// 	});
			// }

			function setSession(alloptions, selected, clickbutton) {
				jQuery.ajax({
					url: "{{ URL::route("pmsession.auditfilters") }}",
					method: 'post',
					data: {
						data: alloptions,
				    selected: selected,
						'_token' : '{{ csrf_token() }}'
					},
					success: function(data){
						if(data) {
							$(clickbutton).trigger( 'click' );
							loadTab('{{ route('dashboard.pmaudits') }}','1','','','',1);
						}
					}
				});
			}

			function updateAuditDocuments(e){
				e.preventDefault();
				var form = $('#document_filter');

				var alloptions = [];
				$('#document_filter input').each(function() {
					alloptions.push([$(this).attr('id'), 0]);
				});

				var selected = [];
				$('#document_filter input:checked').each(function() {
					selected.push([$(this).attr('id'), 1]);
				});
				setSession(alloptions, selected, '#documentfilterbutton');
			}

			function updateAuditScheduleDate(e){
				e.preventDefault();

				var form = $('#daterange_filter');
				// debugger;

				var alloptions = [];

				$('#daterange_filter input').each(function() {
					alloptions.push([$(this).attr('id'), 0]);
				});
				if($('#daterange').val() != '') {
					alloptions.push(['daterange', $('#daterange').val()]);
			    $("#schedule_date").prop('checked', true);
				}

				var selected = [];
				$('#daterange_filter input:checked').each(function() {
					selected.push([$(this).attr('id'), 1]);
				});

				if(!$('#schedule_no_date').is(":checked")) {
					if($("#daterange").val().length === 0) {
						$("#daterange").addClass('uk-form-danger');
						return false;
					}else{
						$("#daterange").removeClass('uk-form-danger');
					}
				}

				// if($('#schedule_date').is(":checked")) {
				// 	alloptions.push(['daterange', $('#daterange').val()]);
				// }
				// var alloptions = [];
				// alloptions.push(['daterange', $('#daterange').val()]);
				// var selected = [];
				// selected.push(['schedule_date', 1]);
				setSession(alloptions, selected, '#daterangefilterbutton');


				// $.post("/session/", {
				// 	'data' : alloptions,
				// 	'_token' : '{{ csrf_token() }}'
				// }, function(data) {
				// 	$.post("/session/", {
				// 		'data' : selected,
				// 		'_token' : '{{ csrf_token() }}'
				// 	}, function(data) {
				// 		$('#daterangefilterbutton').trigger( 'click' );
				// 		loadTab('{{ route('dashboard.audits') }}','1','','','',1);
				// 	} );
				// } );
			}




			function updateAuditMessages(e){
				e.preventDefault();
				var form = $('#messages_filter');

				var alloptions = [];
				$('#messages_filter input').each(function() {
					alloptions.push([$(this).attr('id'), 0]);
				});

				var selected = [];
				$('#messages_filter input:checked').each(function() {
					selected.push([$(this).attr('id'), 1]);
				});
				setSession(alloptions, selected, '#messagesfilter');
				// $.post("/session/", {
				// 	'data' : alloptions,
				// 	'_token' : '{{ csrf_token() }}'
				// }, function(data) {
				// 	$.post("/session/", {
				// 		'data' : selected,
				// 		'_token' : '{{ csrf_token() }}'
				// 	}, function(data) {
				// 		$('#messagesfilter').trigger( 'click' );
				// 		loadTab('{{ route('dashboard.audits') }}','1','','','',1);
				// 	} );
				// } );
			}

			function setDate(date, name){
				$('#daterange').val(date);
				// also make sure the day of the week is selected
				if(!$("input[name='"+name+"']:checkbox").is(':checked')){
					selectday(".dayselector-"+name, name);
				}
			}

			
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
				setSession(alloptions, selected, '#checklist-button');


				// $.post("/session/", {
				// 	'data' : alloptions,
				// 	'_token' : '{{ csrf_token() }}'
				// }, function(data) {
				// 	$.post("/session/", {
				// 		'data' : selected,
				// 		'_token' : '{{ csrf_token() }}'
				// 	}, function(data) {
				// 		$('#checklist-button').trigger( 'click' );
				// 		loadTab('{{ route('dashboard.audits') }}','1','','','',1);
				// 	} );
				// } );
			}

			function updateCarFilter(e){
				e.preventDefault();
				var form = $('#car_report_selection');

				var alloptions = [];
				$('#car_report_selection input').each(function() {
					alloptions.push([$(this).attr('id'), 0]);
				});

				var selected = [];
				$('#car_report_selection input:checked').each(function() {
					selected.push([$(this).attr('id'), 1]);
				});

				setSession(alloptions, selected, '#carselectionbutton');

				// $.post("/session/", {
				// 	'data' : alloptions,
				// 	'_token' : '{{ csrf_token() }}'
				// }, function(data) {
				// 	$.post("/session/", {
				// 		'data' : selected,
				// 		'_token' : '{{ csrf_token() }}'
				// 	}, function(data) {
				// 		$('#carselectionbutton').trigger( 'click' );
				// 		loadTab('{{ route('dashboard.audits') }}','1','','','',1);
				// 	} );
				// } );
			}

			function updateEhsFilter(e){
				e.preventDefault();
				var form = $('#ehs_report_selection');

				var alloptions = [];
				$('#ehs_report_selection input').each(function() {
					alloptions.push([$(this).attr('id'), 0]);
				});

				var selected = [];
				$('#ehs_report_selection input:checked').each(function() {
					selected.push([$(this).attr('id'), 1]);
				});

				setSession(alloptions, selected, '#ehsselectionbutton');

				// $.post("/session/", {
				// 	'data' : alloptions,
				// 	'_token' : '{{ csrf_token() }}'
				// }, function(data) {
				// 	$.post("/session/", {
				// 		'data' : selected,
				// 		'_token' : '{{ csrf_token() }}'
				// 	}, function(data) {
				// 		$('#ehsselectionbutton').trigger( 'click' );
				// 		loadTab('{{ route('dashboard.audits') }}','1','','','',1);
				// 	} );
				// } );
			}

			function update8823Filter(e){
				e.preventDefault();
				var form = $('#8823_report_selection');

				var alloptions = [];
				$('#8823_report_selection input').each(function() {
					alloptions.push([$(this).attr('id'), 0]);
				});

				var selected = [];
				$('#8823_report_selection input:checked').each(function() {
					selected.push([$(this).attr('id'), 1]);
				});

				setSession(alloptions, selected, '#8823selectionbutton');

				// $.post("/session/", {
				// 	'data' : alloptions,
				// 	'_token' : '{{ csrf_token() }}'
				// }, function(data) {
				// 	$.post("/session/", {
				// 		'data' : selected,
				// 		'_token' : '{{ csrf_token() }}'
				// 	}, function(data) {
				// 		$('#8823selectionbutton').trigger( 'click' );
				// 		loadTab('{{ route('dashboard.audits') }}','1','','','',1);
				// 	} );
				// } );
			}


			function openAssignment (projectKey, auditId) {
				loadTab('/pm-projects/view/'+projectKey+'/'+auditId, '4', 1, 1, '', 1, auditId);
                // dynamicModalLoad('projects/'+this.audit.projectKey+'/assignments/addauditor',1,0,1);
            }

			function openFindings (projectKey, auditId) {
				loadTab('/pm-projects/view/'+projectKey+'/'+auditId, '4', 1, 1, '', 1, auditId);
                // dynamicModalLoad('projects/'+this.audit.projectKey+'/assignments/addauditor',1,0,1);
            }

			
			function openProject (projectKey,auditId, subtab = 0) {
				window.selectedProjectKey = projectKey;
				window.selectedAuditId = auditId;
				loadTab('/pm-projects/view/'+projectKey+'/'+auditId, '4', 1, 1, '', 1, auditId);
				if(subtab != 0) {
					window.subtab = subtab;
				}
			}
			
			function openMapLink (mapLink) {
				window.open(mapLink);
			}
			
		// by nature this note is it's history note - so no need to ask them for a comment.


	




	
</script>
<script>
	window.onPageAudits = {@forEach($audits as $audit) '{{ $audit->audit_id }}' :["{{ $audit->audit_id }}","{{ $audit->updated_at }}"] @if(!$loop->last),@endIf @endForEach };
	function checkForAudit(audit_id){
			//
			//console.log('Checking to see if audit '+audit_id+' is on this page.');
			if($("#audit-r-" + audit_id).length > 0) {
			// route: /updated_cached_audit/{audit_id}
			console.log('Found audit '+audit_id+' on the page');
			updateAuditRow(audit_id);
		}

	}

	function updateAuditRow(audit_id){
			// update the audit row with new info

			$("#audit-r-" + audit_id).load('/updated_cached_audit/'+audit_id,function(audit_id){
				console.log('Updated audit row ');

			});
		}

		function checkForUpdatedAudits(){
			if($('#detail-tab-1').hasClass('uk-active')){
				// the audits tab is active - so we can check things.
				if(window.checking_latest_cached_audit == 0){
					window.checking_latest_cached_audit = 1;
					var audits = JSON.stringify(window.onPageAudits);
					//console.log('CHECKING '+onPageAudits+' and '+audits);
					$.post("/cached_audit_check", {
						'audits' : audits,
						'dude' : 'stuff',
						'_token' : '{{ csrf_token() }}'
					}, function(data) {
						if(data !== '0'){
							data = JSON.parse(data);
							//console.log(window.latest_cached_audit,data);
							data.forEach(function(audit_id){
								checkForAudit(audit_id);
							});
							window.checking_latest_cached_audit = 0;
						}else{
							console.log('No Audits');
							window.checking_latest_cached_audit = 0;
						}
					} );
				}
			}
		}

		
		// set the base variables.
		window.latest_cached_audit = '{{ $latestCachedAudit }}';
		window.checking_latest_cached_audit = 0;

		$( document ).ready(function() {
			if (window.hide_confirm_modal_flag === undefined) {
				window.hide_confirm_modal_flag = 0;
			}
			
			console.log( "ready!" );
			@if(!local())
			window.setInterval(function(){
				checkForUpdatedAudits(window.onPageAudits);
			}, 5000);
			@endif

		});
	</script>
	<script>
		flatpickr.defaultConfig.animate = window.navigator.userAgent.indexOf('MSIE') === -1;

		flatpickr("#daterange", {
			inline: true,
			mode: "range",
			// minDate: "today",
			altFormat: "F j, Y",
			dateFormat: "F j, Y",
			"locale": {
		        "firstDayOfWeek": 1 // start week on Monday
		      }
		    });

		  </script>
		  <script>
		  	function openProjectSubtab(project_key, audit_id, subtab = 0) {
		  		openProject(project_key, audit_id, subtab);
		  	}
		  </script>
		  <script>window.auditsLoaded = 1; </script>