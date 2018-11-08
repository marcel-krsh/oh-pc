<template class="uk-hidden" id="inspection-left-template">
    <div class="inspection-menu">
    </div>
</template>

<template class="uk-hidden" id="inspection-menu-item-template">
    <button class="uk-button uk-link menuStatus" onclick="switchInspectionMenu('menuAction', 'menuLevel', 'menuTarget');" style="menuStyle"><i class="menuIcon"></i> menuName</button>
</template>

<template class="uk-hidden" id="inspection-areas-template">
    <div class="inspection-areas uk-height-large uk-height-max-large uk-panel uk-panel-scrollable sortable" uk-sortable="handle: .uk-sortable-inspec-area;">
    </div>
</template>

<template class="uk-hidden" id="inspection-area-template">
	    <div id="inspection-areaContext-area-r-areaRowId" class="inspection-area uk-flex uk-flex-row areaStatus" style="padding:6px 0 0 0;">
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
    				<button class="uk-button tool-add-area uk-link"><i class="a-plus"></i> AREA</button>
    			</div>
    			<div class="uk-width-1-3">
    				<button class="uk-button tool-edit uk-link"><i class="a-pencil-2"></i> EDIT</button>
    			</div>
    			<div class="uk-width-1-3 uk-text-right">
    				<i class="a-horizontal-expand"></i>
    			</div>
    		</div>
    	</div>
    	<div class="inspection-tools-tabs uk-width-1-1">
    		<ul class="uk-subnav uk-subnav-pill" uk-switcher="animation: uk-animation-fade">
			    <li><a href="#">FINDINGS</a></li>
			    <li><a href="#">COMMENTS</a></li>
			    <li><a href="#">PHOTOS</a></li>
			    <li><a href="#">DOCUMENTS</a></li>
			    <li><a href="#">FOLLOW UPS</a></li>
			</ul>

			<ul class="uk-switcher uk-margin">
			    <li id="inspec-tools-tab-findings">
			    	<div>
			    		<i class="a-arrow-top-3 flipfont"></i> FILTER FINDINGS
			    	</div>
			    	<div class="inspec-tools-tab-findings-container uk-panel uk-panel-scrollable uk-height-large uk-height-max-large">
			    		<div class="inspec-tools-tab-finding action-needed" uk-grid>
			    			<div class="uk-width-1-1" style="padding-top: 15px;">
			    				<div uk-grid>
					    			<div class="uk-width-1-4">
					    				<i class="a-booboo"></i> NLT<br />
					    				<span class="auditinfo">AUDIT 20120394</span><br />
					    				<button class="uk-button inspec-tools-findings-resolve uk-link"><span class="uk-badge">
									    	 &nbsp; </span>RESOLVE</button>
					    			</div>
					    			<div class="uk-width-3-4 bordered">
					    				<p>12/22/2018 12:51:38 PM: By Holly Swisher<br />
					    				STAIR #1 : Finding Description Goes here and continues here for when it is long</p>
					    			</div>
					    		</div>
					    	</div>
					    	<div class="uk-width-1-1 uk-margin-remove inspec-tools-tab-finding-comment">
					    		<div uk-grid>
					    			<div class="uk-width-1-4">
					    				<i class="a-comment-text"></i> COMMENT
					    			</div>
					    			<div class="uk-width-3-4 borderedcomment">
					    				<p>12/31/2018 12:59:38 PM: By Holly Swisher<br />
					    					<span class="finding-comment">Comment goes here and is italicised to show that it is a comment and not a finding.</span></p>
										<button class="uk-button inspec-tools-tab-finding-reply uk-link">
					    					<i class="a-comment-pencil"></i> REPLY
									    </button>
					    			</div>
					    		</div>
					    	</div>
					    	<div class="uk-width-1-1 uk-margin-remove">
					    		<div uk-grid>
					    			<div class="uk-width-1-4">
					    				<button class="uk-button uk-link inspec-tools-tab-finding-button">
					    					<i class="a-calendar-pencil"></i> FOLLOW UP
									    </button>
					    			</div>
					    			<div class="uk-width-1-4">
					    				<button class="uk-button uk-link inspec-tools-tab-finding-button">
					    					<i class="a-comment-text"></i> COMMENT
									    </button>
					    			</div>
					    			<div class="uk-width-1-4">
					    				<button class="uk-button uk-link inspec-tools-tab-finding-button colored">
					    					<i class="a-file-clock"></i> DOCUMENT
									    </button>
					    			</div>
					    			<div class="uk-width-1-4">
					    				<button class="uk-button uk-link inspec-tools-tab-finding-button">
					    					<i class="a-picture"></i> PHOTO
									    </button>
					    			</div>
					    		</div>
					    	</div>
			    		</div>
			    	</div>
			    </li>
			    <li>Hello again!</li>
			    <li>Bazinga!</li>
			    <li>Hello again 2!</li>
			    <li>Bazinga 3!</li>
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
		<div class="uk-width-1-3 uk-text-right uk-margin-top">
			<example></example>
			 <button class="uk-button uk-button-primary" onclick="createAudits();"><i class="a-mobile-plus"></i> <span class="uk-badge">22</span> CREATE AUDITS</button>
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
			            	<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'file-status-sort',1);"></a></span>
			            </div>
		            </th>
		            <th class="uk-table-small" style="width:130px;">
		            	<div uk-grid>
		            		<div class="filter-box uk-width-1-1">
								<input id="filter-by-project" class="filter-box filter-file" type="text" placeholder="PROJECT & AUDIT">
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top" title="Sort By">
								<a id="" class="sort-asc" onclick="loadListTab(1,null,null,'file-number-sort',1);"></a>
							</span> 
							<div class="uk-dropdown" aria-expanded="false"></div>
						</div>
					</th>
		            <th>
		            	<div uk-grid>
			            	<div class="filter-box uk-width-1-1">
								<input id="filter-by-name" class="filter-box filter-name" type="text" placeholder="PROJECT / PM NAME">
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-2 uk-padding-remove-top uk-margin-remove-top" title="Sort By">
								<a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'first-name-sort',1);"></a>
							</span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-2 uk-padding-remove-top uk-margin-remove-top" title="Sort By">
								<a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'last-name-sort',1);"></a>
							</span> 
							<div class="uk-dropdown" aria-expanded="false"></div>
						</div>
					</th>
		            <th class="uk-table-expand">
		            	<div uk-grid>
			            	<div class="filter-box uk-width-1-1">
								<input id="filter-by-address" class="filter-box filter-address" type="text" placeholder="PRIMARY ADDRESS">
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top" title="Sort By">
								<a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'sort-by-street',1);"></a>
							</span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top" title="Sort By">
								<a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'sort-by-city',1);"></a>
							</span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top" title="Sort By">
								<a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'sort-by-zip',1);"></a>
							</span> 
							<div class="uk-dropdown" aria-expanded="false"></div>
						</div>
		            </th>
		            <th style="min-width:190px;">
		            	<div uk-grid>
			            	<div class="filter-box filter-date-aging uk-vertical-align uk-width-1-1" uk-grid> 
								<!-- SPAN TAG TITLE NEEDS UPDATED TO REFLECT CURRENT DATE RANGE -->
								<span data-uk-tooltip="" title="Sort By" class="uk-width-1-2 uk-text-center uk-padding-remove-top uk-margin-remove-top">
									<a class="uk-link-muted" onclick="dynamicModalLoad('date-aging-range');"><i class="a-calendar-8 uk-vertical-align-middle"></i> <i class="uk-icon-asterisk  uk-vertical-align-middle uk-text-small tiny-middle-text"></i> <i class="a-calendar-8 uk-vertical-align-middle"></i></a>
								</span>
								<span data-uk-tooltip="" title="Sort By" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top uk-text-right uk-link">
									<i class="a-avatar-home"></i> / <i class="a-home-2"></i>
								</span>
								<span data-uk-tooltip="" title="Sort By" class="uk-width-1-6 uk-padding-remove-top uk-margin-remove-top uk-text-center uk-link">
									<i class="a-circle-checked"></i>
								</span>
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-2 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'date-aging-range-sort',1);"></a></span>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-6 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'date-aging-days-sort',1);"></a></span>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-6 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'date-aging-days-sort',1);"></a></span>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-6 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'date-aging-days-sort',1);"></a></span>
						</div>
		            </th>
		            <th style="min-width: 80px;">
		            	<div uk-grid>
			            	<div class="filter-box filter-date-expire uk-vertical-align uk-width-1-1 uk-text-center"> 
								<span data-uk-tooltip="" title="Sort By" >
									<a class="uk-link-muted" onclick="dynamicModalLoad('date-aging-range');"><i class="a-calendar-8 uk-vertical-align-middle"></i> <i class="uk-icon-asterisk  uk-vertical-align-middle uk-text-small tiny-middle-text"></i> <i class="a-calendar-8 uk-vertical-align-middle"></i></a>
								</span>
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'date-aging-range-sort',1);"></a></span>
						</div>
					</th>
		            <th style="min-width: 120px;">
		            	<div uk-grid>
			            	<div class="filter-box filter-icons uk-vertical-align uk-width-1-1" uk-grid> 
			            		<span class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-link">
									<i class="a-folder"></i>
								</span>
								<span class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-link">
									<i class="a-booboo"></i>
								</span>
								<span class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-link">
									<i class="a-skull"></i>
								</span>
								<span class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top uk-link">
									<i class="a-flames"></i>
								</span> 
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'file-status-sort',1);"></a></span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'documents-waiting-sort',1);"></a></span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'home-owner-replied-sort',1);"></a></span> 
							<span data-uk-tooltip="{pos:'bottom-right'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="Sort By Document Send Status"><a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'documents-waiting-sort',1);"></a></span>
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
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'file-status-sort',1);"></a></span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'documents-waiting-sort',1);"></a></span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'home-owner-replied-sort',1);"></a></span> 
							<span data-uk-tooltip="{pos:'bottom-right'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="Sort By Document Send Status"><a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'documents-waiting-sort',1);"></a></span>
						</div>
		            </th>
		            <th >
		            	<div uk-grid>
			            	<div class="filter-box filter-icons uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-link">
			            		<i class="a-checklist"></i>
			            		<div class="uk-dropdown uk-dropdown-bottom filter-dropdown" uk-dropdown="flip: false; pos: bottom-right;" style="top: 26px; left: 0px;">
			            			<form>
			            				<fieldset class="uk-fieldset">
			            					<div class="uk-margin uk-child-width-auto uk-grid">
			            					@if(session('step-all') == 0)
									            <input id="filter-step-all" type="checkbox" />
												<label for="filter-step-all">ALL STEPS (CLICK TO SELECT ALL)</label>
											@else
									            <input id="filter-step-all" type="checkbox" checked/>
												<label for="filter-step-all">ALL STEPS (CLICK TO DESELECT ALL)</label>
											@endif
											@if(session('step-review-area') == 0)
												<input id="filter-step-review-area" type="checkbox" />
												<label for="filter-step-review-area"><i class="a-home-question"></i> Review and Assign Inspectable Areas</label>
											@else	
												<input id="filter-step-review-area" type="checkbox" checked/>
												<label for="filter-step-review-area"><i class="a-home-question"></i> Review and Assign Inspectable Areas</label>
											@endif
												<input id="filter-step-schedule" type="checkbox" />
												<label for="filter-step-schedule"><i class="a-calendar-7"></i> Schedule Audit</label>
												<input id="filter-step-audit-pending" type="checkbox" />
												<label for="filter-step-audit-pending"><i class="a-stopwatch-3"></i> Audit Pending</label>
												<input id="filter-step-audit-progress" type="checkbox" />
												<label for="filter-step-audit-progress"><i class="a-rotate-left"></i> Audit In-Progress</label>
												<input id="filter-step-lead-approval" type="checkbox" />
												<label for="filter-step-lead-approval"><i class="a-file-pen"></i> Lead Approval</label>
												<input id="filter-step-lead-requested" type="checkbox" />
												<label for="filter-step-lead-requested" style="padding-left:40px;"><i class="a-avatar-refresh"></i> Lead Requested Edits</label>
												<input id="filter-step-report-generate" type="checkbox" />
												<label for="filter-step-report-generate"><i class="a-file-chart-3"></i> Generate Report</label>
												<input id="filter-step-report-review" type="checkbox" />
												<label for="filter-step-report-review"><i class="a-magnify-chart-up"></i> Review Report</label>
												<input id="filter-step-report-comment" type="checkbox" />
												<label for="filter-step-report-comment"><i class="a-comment-chart-up"></i> See Report Comments</label>
												<input id="filter-step-report-send" type="checkbox" />
												<label for="filter-step-report-send"><i class="a-mail-chart-up"></i> Send Report</label>
												<input id="filter-step-followup-pending" type="checkbox" />
												<label for="filter-step-followup-pending"><i class="a-avatar-chat-up"></i> Pending Follow-Up</label>
												<input id="filter-step-audit-archive" type="checkbox" />
												<label for="filter-step-audit-archive"><i class="a-folder-box"></i> Archive Audit</label>
												<input id="filter-step-audit-score" type="checkbox" />
												<label for="filter-step-audit-score">00% Audit Score</label>
									        </div>
									        <div class="uk-margin-remove" uk-grid>
			                            		<div class="uk-width-1-2">
			                            			<button class="uk-button uk-button-primary uk-width-1-1"><i class="fas fa-filter"></i> APPLY FILTER</button>
			                            		</div>
			                            		<div class="uk-width-1-2">
			                            			<button class="uk-button uk-button-secondary uk-width-1-1"><i class="a-circle-cross"></i> CANCEL</button>
			                            		</div>
			                            	</div>
			            				</fieldset>
			            			</form>
			                    </div>
			            	</div>
			            	<span data-uk-tooltip="{pos:'bottom'}" title="Sort By" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top"><a id="" class="sort-neutral" onclick="loadListTab(1,null,null,'file-status-sort',1);"></a></span> 
			            </div>
		            </th>
		            <th style="vertical-align:top;">
		            	<div uk-grid>
			            	<div class="uk-link uk-width-1-1" style="background-color:#000;vertical-align:top;height: 26px;padding: 2px;text-align: center;color:#fff;">
				            	<i class="a-folder-box"></i>
							</div>
						</div>
		            </th>
		        </tr>
		    </thead>
		    <tbody>
		    	@foreach($audits as $audit)
		    	<tr id="audit-r-{{$loop->iteration}}" class="{{$audit['status']}} @if($audit['status'] != 'critical') notcritical @endif" style=" @if(session('audit-hidenoncritical') == 1 && $audit['status'] != 'critical') display:none; @endif ">
		            <td id="audit-c-1-{{$loop->iteration}}" class="uk-text-center audit-td-lead">
		            	<span id="audit-avatar-badge-1" uk-tooltip="pos:top-left;title:{{$audit['lead']['name']}};" title="" aria-expanded="false" class="user-badge user-badge-{{$audit['lead']['color']}} no-float uk-link">
							{{$audit['lead']['initials']}}
						</span>
						<span id="audit-rid-{{$loop->iteration}}"><small>#{{$loop->iteration}}</small></span>
		            </td>
		            <td id="audit-c-2-{{$loop->iteration}}" class="audit-td-project">
		            	<div class="uk-vertical-align-middle uk-display-inline-block uk-margin-small-top">
		            		<span id="audit-i-project-detail-{{$loop->iteration}}" onclick="projectDetails({{$audit['id']}},{{$loop->iteration}},{{$audit['total_buildings']}});" uk-tooltip="pos:top-left;title:View Buildings and Common Areas;" class="uk-link"><i class="a-menu uk-text-muted"></i></span>
		            	</div>
		            	<div class="uk-vertical-align-middle uk-display-inline-block">
		            		<h3 id="audit-project-name-{{$loop->iteration}}" class="uk-margin-bottom-remove uk-link" uk-tooltip="title:Open Audit Details in Tab;" onClick="loadTab('{{ route('project', $audit['id']) }}', '4', 1, 1);">{{$audit['audit_id']}}</h3>
			            	<small id="audit-project-aid-{{$loop->iteration}}" class="uk-text-muted" uk-tooltip="title:View Project's Audit Details;">AUDIT {{$audit['audit_id']}}</small>
			            </div>
		            </td>
		            <td class="audit-td-name">
		            	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
		            		<i class="a-info-circle uk-text-muted uk-link" uk-tooltip="title:View Contact Details;"></i>
		            	</div> 
		            	<div class="uk-vertical-align-top uk-display-inline-block fadetext">
		            		<h3 class="uk-margin-bottom-remove">{{$audit['title']}}</h3>
			            	<small class="uk-text-muted">{{$audit['subtitle']}}</small>
		            	</div>
		            </td>
		            <td class="hasdivider audit-td-address">
		            	<div class="divider"></div>
		            	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
		            		<i class="a-marker-basic uk-text-muted uk-link" uk-tooltip="title:View On Map;"></i>
		            	</div> 
		            	<div class="uk-vertical-align-top uk-display-inline-block fullwidthleftpad fadetext">
		            		<h3 class="uk-margin-bottom-remove">{{$audit['address']}}</h3>
			            	<small class="uk-text-muted">{{$audit['city']}}, {{$audit['state']}} {{$audit['zip']}}</small>
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
				            		<h3 class="uk-link" uk-tooltip="title:{{$audit['inspection_schedule_text']}};">{{$audit['inspection_schedule_date']}}</h3>
				            		<div class="dateyear">{{$audit['inspection_schedule_year']}}</div>
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
			            		<h3 class="uk=link" uk-tooltip="title:Click to reschedule audits;">{{$audit['followup_date']}}</h3>
				            	<div class="dateyear">{{$audit['followup_year']}}</div>
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
    });
</script>