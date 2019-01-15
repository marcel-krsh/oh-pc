<template class="uk-hidden" id="inspection-left-template">
    <div class="inspection-menu">
    </div>
</template>

<template class="uk-hidden" id="inspection-menu-item-template">
    <button class="uk-button uk-link menuStatus" onclick="switchInspectionMenu('menuAction', 'menuLevel', 'menuTarget');" style="menuStyle"><i class="menuIcon"></i> menuName</button>
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
	    	<div class="uk-inline uk-padding-remove" style="margin-top:7px; ">
    			<div class="area-avatar">
					<div uk-tooltip="pos:top-left;title:areaAuditorName;" title="" aria-expanded="false" class="user-badge auditor-badge-areaAuditorColor no-float">
						areaAuditorInitials
					</div>
				</div>
			</div>
    		<div class="uk-inline uk-padding-remove" style="margin-top:7px; flex:140px;">
    			<div class="area-name">
    				<i class="a-circle-checked"></i>
					areaName
				</div>
			</div>
    		<div class="uk-inline uk-padding-remove">
    			<div class="findings-icon uk-inline areaNLTStatus">
					<i class="a-booboo"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
					</div>
				</div>
				<div class="findings-icon uk-inline areaLTStatus">
					<i class="a-skull"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
					</div>
				</div>
				<div class="findings-icon uk-inline areaSDStatus">
					<i class="a-flames"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
					</div>
				</div>
			</div>
			<div class="uk-inline" style="padding: 0 15px;">
				<div class="findings-icon uk-inline areaPicStatus">
					<i class="a-picture"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
					</div>
				</div>
				<div class="findings-icon uk-inline areaCommentStatus">
					<i class="a-comment-text"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
					</div>
				</div>
			</div>
			<div class="uk-inline uk-padding-remove">	
				<div class="findings-icon uk-inline areaCopyStatus">
					<i class="a-file-copy-2"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
					</div>
				</div>
				<div class="findings-icon uk-inline areaTrashStatus">
					<i class="a-trash-4"></i>
					<div class="findings-icon-status plus">
						<span class="uk-badge">+</span>
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
    				<button class="uk-button tool-add-area uk-link" onclick="addAmenity('tplId', 'tplBuildingOrUnit');"><i class="a-plus"></i> AREA</button>
    			</div>
    			<div class="uk-width-1-3">
    				<button class="uk-button tool-edit uk-link"><i class="a-pencil-2"></i> EDIT</button>
    			</div>
    			<div class="uk-width-1-3 uk-text-right">
    				<i class="a-horizontal-expand"></i>
    			</div>
    		</div>
    	</div>
    	<div class="inspection-tools-tabs uk-width-1-1" uk-filter="target: .js-filter-comments">
    		<ul class="uk-subnav uk-subnav-pill">
			    <li class="uk-badge use-hand-cursor" uk-filter-control=".comment-type-finding">FINDINGS</li>
			    <li class="uk-badge use-hand-cursor" uk-filter-control=".comment-type-comment">COMMENTS</li>
			    <li class="uk-badge use-hand-cursor" uk-filter-control=".comment-type-photo">PHOTOS</li>
			    <li class="uk-badge use-hand-cursor" uk-filter-control=".comment-type-file">DOCUMENTS</li>
			    <li class="uk-badge use-hand-cursor" uk-filter-control=".comment-type-followup">FOLLOW UPS</li>
			</ul>

			<div style="color:#bbb;">
	    		<i class="fas fa-filter"></i> FILTER FINDINGS
	    	</div>

			<ul class="uk-margin js-filter-comments inspec-tools-tab-findings-container uk-panel uk-panel-scrollable uk-height-large uk-height-max-large">
			</ul>
    	</div>
    </div>
</template>

<div id="audits" class="uk-no-margin-top" uk-grid>
	<div class="uk-margin-remove-top uk-width-1-1" uk-grid>
		<div id="auditsfilters" class="uk-width-2-3 uk-margin-top">
			@if(isset($auditFilterMineOnly))
			<div id="audit-filter-mine" class="uk-badge uk-text-right@s badge-filter">
				@if(Auth::user()->isManager)
				<a onClick="loadTab('{{ route('dashboard.audits', ['filter' => 'yes']) }}', '1');" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>MY AUDITS ONLY</span></a>
				@else
				<span>&nbsp;MY AUDITS ONLY</span>
				@endif
			</div>
			@endif
			<div id="audit-filter-project" class="uk-badge uk-text-right@s badge-filter" hidden>
				<a onClick="loadTab('{{ route('dashboard.audits', ['filter' => 'yes']) }}', '1');" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>FILTER PROJECT</span></a>
			</div>
			<div id="audit-filter-name" class="uk-badge uk-text-right@s badge-filter" hidden>
				<a onClick="loadTab('{{ route('dashboard.audits', ['filter' => 'yes']) }}', '1');" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>FILTER PROJECT NAME</span></a>
			</div>
			<div id="audit-filter-address" class="uk-badge uk-text-right@s badge-filter" hidden>
				<a onClick="loadTab('{{ route('dashboard.audits', ['filter' => 'yes']) }}', '1');" class="uk-dark uk-light"><i class="a-circle-cross"></i> <span>FILTER ADDRESS</span></a>
			</div>
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
		</div>
	</div>

	<div id="auditstable" class="uk-width-1-1 uk-overflow-auto">
		<table class="uk-table uk-table-striped uk-table-hover uk-table-small uk-table-divider" style="min-width: 1420px;">
		    <thead>
		        <tr>
		            <th class="uk-table-shrink">
		            	<div uk-grid>
			            	<div class="filter-box filter-icons uk-text-center uk-width-1-1 uk-link">
			            		<i class="a-avatar-star"></i>
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
								<input id="filter-by-project" class="filter-box filter-search-project-input" type="text" placeholder="PROJECT & AUDIT" onkeyup="filterAuditList(this, 'filter-search-project')" value="@if(session()->has('filter-search-project-input')){{session('filter-search-project-input')}}@endif">
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top" title="SORT BY PROJECT ID">
			            		@if($sort_by == 'audit-sort-project')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-project', @php echo 1-$sort_order; @endphp, 'filter-search-project-input');"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-project', 1, 'filter-search-project-input');"></a>
			            		@endif
							</span> 
							<div class="uk-dropdown" aria-expanded="false"></div>
						</div>
					</th>
		            <th>
		            	<div uk-grid>
			            	<div class="filter-box uk-width-1-1">
								<input id="filter-by-name" class="filter-box filter-search-pm-input" type="text" placeholder="PROJECT / PM NAME" onkeyup="filterAuditList(this, 'filter-search-pm')" value="@if(session()->has('filter-search-pm-input')){{session('filter-search-pm-input')}}@endif">
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
		            <th class="uk-table-expand">
		            	<div uk-grid>
			            	<div class="filter-box uk-width-1-1">
								<input id="filter-by-address" class="filter-box filter-search-address-input" type="text" placeholder="PRIMARY ADDRESS" onkeyup="filterAuditList(this, 'filter-search-address')" value="@if(session()->has('filter-search-address-input')){{session('filter-search-address-input')}}@endif">
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
		            <th style="min-width:190px;">
		            	<div uk-grid>
			            	<div class="filter-box filter-date-aging uk-vertical-align uk-width-1-1" uk-grid> 
								<!-- SPAN TAG TITLE NEEDS UPDATED TO REFLECT CURRENT DATE RANGE -->
								<span class="uk-width-1-2 uk-text-center uk-padding-remove-top uk-margin-remove-top">
									<a class="uk-link-muted" onclick="dynamicModalLoad('date-aging-range');"><i class="a-calendar-8 uk-vertical-align-middle"></i> <i class="uk-icon-asterisk  uk-vertical-align-middle uk-text-small tiny-middle-text"></i> <i class="a-calendar-8 uk-vertical-align-middle"></i></a>
								</span>
								<span class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top uk-text-right uk-link">
									<i class="a-avatar-home"></i> / <i class="a-home-2"></i>
								</span>
								<span class="uk-width-1-6 uk-padding-remove-top uk-margin-remove-top uk-text-center uk-link">
									<i class="a-circle-checked"></i>
								</span>
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-2 uk-padding-remove-top uk-margin-remove-top" title="SORT BY SCHEDULED DATE">
			            		@if($sort_by == 'audit-sort-scheduled-date')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-scheduled-date',  @php echo 1-$sort_order; @endphp);"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-scheduled-date', 1);"></a>
			            		@endif
							</span>
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
						</div>
		            </th>
		            <th style="min-width: 80px;">
		            	<div uk-grid>
			            	<div class="filter-box filter-date-expire uk-vertical-align uk-width-1-1 uk-text-center"> 
								<span>
									<a class="uk-link-muted" onclick="dynamicModalLoad('date-aging-range');"><i class="a-calendar-8 uk-vertical-align-middle"></i> <i class="uk-icon-asterisk  uk-vertical-align-middle uk-text-small tiny-middle-text"></i> <i class="a-calendar-8 uk-vertical-align-middle"></i></a>
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
		            <th style="min-width: 90px;">
		            	<div uk-grid>
			            	<div class="filter-box filter-icons uk-vertical-align uk-width-1-1" uk-grid> 
			            		<span class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top uk-link">
									<i class="a-folder"></i>
								</span>
								<span class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top uk-link">
									<i class="a-booboo"></i>
								</span>
								<span class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top uk-link">
									<i class="a-skull"></i>
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
		            <th style="min-width: 120px;">
		            	<div uk-grid>
			            	<div class="filter-box filter-icons uk-vertical-align uk-width-1-1" uk-grid> 
			            		<span class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-link">
									<i class="a-avatar"></i>
								</span>
								<span class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-link">
									<i class="a-envelope-4"></i>
									<div class="uk-dropdown uk-dropdown-bottom" uk-dropdown="flip: false; pos: bottom-right;" style="top: 26px; left: 0px;">
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
								<span class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-link">
									<i class="a-files"></i>
								</span>
								<span class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-link">
									<i class="a-person-clock"></i>
								</span> 
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="SORT BY AUDITOR ASSIGNMENT STATUS">
			            		@if($sort_by == 'audit-sort-status-auditor')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-status-auditor',  @php echo 1-$sort_order; @endphp);"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-status-auditor', 1);"></a>
			            		@endif
							</span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="SORT BY MESSAGE STATUS">
			            		@if($sort_by == 'audit-sort-status-message')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-status-message',  @php echo 1-$sort_order; @endphp);"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-status-message', 1);"></a>
			            		@endif
							</span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="SORT BY DOCUMENT STATUS">
			            		@if($sort_by == 'audit-sort-status-document')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-status-document',  @php echo 1-$sort_order; @endphp);"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-status-document', 1);"></a>
			            		@endif
							</span> 
							<span data-uk-tooltip="{pos:'bottom-right'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="SORT BY HISTORY STATUS">
			            		@if($sort_by == 'audit-sort-status-history')
			            		<a id="" class="@if($sort_order) sort-desc @else sort-asc @endif uk-margin-small-top" onclick="sortAuditList('audit-sort-status-history',  @php echo 1-$sort_order; @endphp);"></a>
			            		@else
			            		<a id="" class="sort-neutral uk-margin-small-top" onclick="sortAuditList('audit-sort-status-history', 1);"></a>
			            		@endif
							</span>
						</div>
		            </th>
		            <th >
		            	<div uk-grid>
			            	<div class="filter-box filter-icons uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-link">
			            		<i class="a-checklist" id="checklist-button"></i>
			            		<div class="uk-dropdown uk-dropdown-bottom filter-dropdown" uk-dropdown="flip: false; pos: bottom-right; mode: click;" style="top: 26px; left: 0px;">
			            			<form id="audit_steps_selection" method="post">
			            				<fieldset class="uk-fieldset">
			            					<div class="uk-margin uk-child-width-auto uk-grid">
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
		            </th>
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
		        </tr>
		    </thead>
		    <tbody>
		    	@if(0)
		    	@foreach($audits as $audit)
		    	<tr id="audit-r-{{$loop->iteration}}" class="{{$audit['status']}} @if($audit['status'] != 'critical') notcritical @endif" style=" @if(session('audit-hidenoncritical') == 1 && $audit['status'] != 'critical') display:none; @endif ">
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
		    	<tr is="auditrow" :class="{[audit.notcritical]:true}" :style="{ display: [audit.display] }" v-if="audits" v-for="(audit, index) in audits.slice().reverse()" :id="'audit-r-'+index" :key="audit.id" :index="index" :audit="audit"></tr>
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
	@if(session('audit-hidenoncritical') != 1)
	<button class="uk-button uk-button-primary btnToggleCritical" onclick="toggleCritical();"><i class="a-eye-not"></i> HIDE NON CRITICAL</button>
	<button class="uk-button uk-button-primary btnToggleCritical" onclick="toggleCritical();" style="display:none;"><i class="a-eye-2"></i> SHOW NON CRITICAL</button>
	@else
	<button class="uk-button uk-button-primary btnToggleCritical" onclick="toggleCritical();" style="display:none;"><i class="a-eye-not"></i> HIDE NON CRITICAL</button>
	<button class="uk-button uk-button-primary btnToggleCritical" onclick="toggleCritical();"><i class="a-eye-2"></i> SHOW NON CRITICAL</button>
	@endif
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
    });

    function rerunCompliance(audit){

    	UIkit.modal.confirm('Are you sure you want to rerun the compliance selection?<br /><br /><em>WARNING!::</em> This will remove all assignments on your audit. IT WILL NOT RUN ON AUDITS WITH FINDINGS.').then(function() {
		    console.log('Call the rerun.')
		}, function () {
		    console.log('Rejected.')
		});
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
            // loadMore: function () {
                // var self = this;
                // self.busy = true;
                // var tempdiv = '<div uk-spinner style="margin: 20px 0;"></div>';
                // $('#spinner').html(tempdiv);

                // setTimeout(() => {
                //     axios.get('dashboard/communications/'+this.page)
                //         .then((response) => {   
                //             $.each(response.data, function(index, value) {
                //                 $('#spinner').html('');
                //                 self.messages.unshift(value);
                //             });
                //         });

                //     this.page = this.page + 1;
                //     this.busy = false;
                // }, 2500);
            //  }
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