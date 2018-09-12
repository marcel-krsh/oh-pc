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
					<a onClick="applyFilter('audit-message',null);" class="uk-dark uk-light">
						<i class="a-circle-cross"></i> 
						<span>MESSAGES FOR ME</span>
					</a>
				</div>
			@endif
			@endif
		</div>
		<div class="uk-width-1-3 uk-text-right uk-margin-top">
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
			            	<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'file-status-sort',1);"></a></span>
			            </div>
		            </th>
		            <th class="uk-table-small" style="width:130px;">
		            	<div uk-grid>
		            		<div class="filter-box uk-width-1-1">
								<input id="filter-by-project" class="filter-box filter-file" type="text" placeholder="FILTER PROJECT & AUDIT">
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
								<input id="filter-by-name" class="filter-box filter-name" type="text" placeholder="FILTER PROJECT / PM NAME">
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-2 uk-padding-remove-top uk-margin-remove-top" title="Sort By">
								<a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'first-name-sort',1);"></a>
							</span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-2 uk-padding-remove-top uk-margin-remove-top" title="Sort By">
								<a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'last-name-sort',1);"></a>
							</span> 
							<div class="uk-dropdown" aria-expanded="false"></div>
						</div>
					</th>
		            <th class="uk-table-expand">
		            	<div uk-grid>
			            	<div class="filter-box uk-width-1-1">
								<input id="filter-by-address" class="filter-box filter-address" type="text" placeholder="FILTER PRIMARY ADDRESS">
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top" title="Sort By">
								<a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'sort-by-street',1);"></a>
							</span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top" title="Sort By">
								<a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'sort-by-city',1);"></a>
							</span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-3 uk-padding-remove-top uk-margin-remove-top" title="Sort By">
								<a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'sort-by-zip',1);"></a>
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
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-2 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'date-aging-range-sort',1);"></a></span>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-6 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'date-aging-days-sort',1);"></a></span>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-6 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'date-aging-days-sort',1);"></a></span>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-6 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'date-aging-days-sort',1);"></a></span>
						</div>
		            </th>
		            <th style="min-width: 80px;">
		            	<div uk-grid>
			            	<div class="filter-box filter-date-expire uk-vertical-align uk-width-1-1 uk-text-center"> 
								<span data-uk-tooltip="" title="Sort By" >
									<a class="uk-link-muted" onclick="dynamicModalLoad('date-aging-range');"><i class="a-calendar-8 uk-vertical-align-middle"></i> <i class="uk-icon-asterisk  uk-vertical-align-middle uk-text-small tiny-middle-text"></i> <i class="a-calendar-8 uk-vertical-align-middle"></i></a>
								</span>
							</div>
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'date-aging-range-sort',1);"></a></span>
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
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'file-status-sort',1);"></a></span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'documents-waiting-sort',1);"></a></span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'home-owner-replied-sort',1);"></a></span> 
							<span data-uk-tooltip="{pos:'bottom-right'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="Sort By Document Send Status"><a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'documents-waiting-sort',1);"></a></span>
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
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'file-status-sort',1);"></a></span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'documents-waiting-sort',1);"></a></span> 
							<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="Sort By"><a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'home-owner-replied-sort',1);"></a></span> 
							<span data-uk-tooltip="{pos:'bottom-right'}" class="uk-width-1-4 uk-padding-remove-top uk-margin-remove-top" title="Sort By Document Send Status"><a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'documents-waiting-sort',1);"></a></span>
						</div>
		            </th>
		            <th >
		            	<div uk-grid>
			            	<div class="filter-box filter-icons uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-link">
			            		<i class="a-checklist"></i>
			            	</div>
			            	<span data-uk-tooltip="{pos:'bottom'}" title="Sort By" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top"><a id="" class="sort-nuetral" onclick="loadListTab(1,null,null,'file-status-sort',1);"></a></span> 
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
		        <tr id="audit-r-1" class="notcritical">
		            <td id="audit-c-1-1" class="uk-text-center audit-td-lead">
		            	<span id="audit-avatar-badge-1" uk-tooltip="pos:top-left;title:Brian Greenwood - Vendor;" title="" aria-expanded="false" class="user-badge user-badge-blue no-float">
							BG
						</span>
						<span id="audit-rid-1"><small>#1</small></span>
		            </td>
		            <td id="audit-c-2-1" class="audit-td-project">
		            	<div class="uk-vertical-align-middle uk-display-inline-block uk-margin-small-top">
		            		<span id="audit-i-project-detail-1" onclick="projectDetails(123,1);" uk-tooltip="pos:top-left;title:Project details;" class="uk-link"><i class="a-list uk-text-muted"></i></span>
		            	</div>
		            	<div class="uk-vertical-align-middle uk-display-inline-block">
		            		<h3 id="audit-project-name-1" class="uk-margin-bottom-remove">19200114</h3>
			            	<small id="audit-project-aid-1" class="uk-text-muted">AUDIT 2015697</small>
			            </div>
		            </td>
		            <td class="audit-td-name">
		            	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
		            		<i class="a-info-circle uk-text-muted"></i>
		            	</div> 
		            	<div class="uk-vertical-align-top uk-display-inline-block fadetext">
		            		<h3 class="uk-margin-bottom-remove">Great American Apartments</h3>
			            	<small class="uk-text-muted">THE NOT SO LONG PROPERTY MANAGER NAME</small>
		            	</div>
		            </td>
		            <td class="hasdivider audit-td-address">
		            	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
		            		<i class="a-marker-basic uk-text-muted"></i>
		            	</div> 
		            	<div class="uk-vertical-align-top uk-display-inline-block fullwidthleftpad fadetext">
		            		<h3 class="uk-margin-bottom-remove">3045 Cumberland Woods Street, Suite 202</h3>
			            	<small class="uk-text-muted">COLUMBUS, OH 43219</small>
		            	</div>
		            	<div class="divider"></div>
		            </td>
		            <td class="hasdivider audit-td-scheduled">
		            	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top" uk-grid>
			            	<div class="uk-width-1-2 uk-padding-remove-top" uk-grid>
			            		<div class="uk-width-1-3">
			            			<i class="a-mobile-repeat action-needed"></i>
			            		</div>
			            		<div class="uk-width-2-3">
			            			12/22
			            		</div>
			            	</div> 
			            	<div class="uk-width-1-6 uk-text-right">0* /</div> 
			            	<div class="uk-width-1-6 uk-text-left">72</div> 
			            	<div class="uk-width-1-6 uk-text-left">
			            		<i class="a-circle-checked ok-actionable"></i>
			            	</div>
			            </div>
		            	<div class="divider"></div>
		            </td>
		            <td class="hasdivider audit-td-due">
		            	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top" uk-grid>
			            	<div class="uk-width-1-3">
			            		<i class="a-bell-2"></i>
			            	</div> 
			            	<div class="uk-width-2-3">
			            		<i class="a-calendar-plus"></i>
			            	</div> 
			            </div>
		            	<div class="divider"></div>
		            </td>
		            <td class="hasdivider">
		            	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top" uk-grid>
			            	<div class="uk-width-1-4">
			            		<i class="a-star-3"></i>
			            	</div> 
			            	<div class="uk-width-1-4">
			            		<i class="a-star-3"></i>
			            	</div> 
			            	<div class="uk-width-1-4">
			            		<i class="a-star-3"></i>
			            	</div> 
			            	<div class="uk-width-1-4">
			            		<i class="a-star-3"></i>
			            	</div> 
			            </div>
		            	<div class="divider"></div>
		            </td>
		            <td class="hasdivider">
		            	<div class="divider"></div>
		            </td>
		            <td>
		            	<div class="uk-margin-small-top" uk-grid>
		            		<div class="uk-width-1-1 no-action">
			            		<i class="a-calendar-7"></i>
							</div>
		            	</div>
		            </td>
		        </tr>
		        <tr id="audit-r-2" class="critical">
		            <td id="audit-c-1-2" class="uk-text-center audit-td-lead">
		            	<span id="audit-avatar-badge-2" uk-tooltip="pos:top-left;title:Brian Greenwood - Vendor;" title="" aria-expanded="false" class="user-badge user-badge-blue no-float">
							BG
						</span>
						<small id="audit-rid-2" class="uk-text-muted">#1</small>
		            </td>
		            <td id="audit-c-2-2" class="audit-td-project">
		            	<div class="uk-vertical-align-middle uk-display-inline-block uk-margin-small-top">
		            		<span id="audit-i-project-detail-2" onclick="projectDetails(123,2);" uk-tooltip="pos:top-left;title:Project details;" class="uk-link"><i class="a-list uk-text-muted"></i></span>
		            	</div> 
		            	<div class="uk-vertical-align-middle uk-display-inline-block">
		            		<h3 id="audit-project-name-2" class="uk-margin-bottom-remove">19200114</h3>
			            	<small id="audit-project-aid-2" class="uk-text-muted">AUDIT 2015697</small>
			            </div>
		            </td>
		            <td class="audit-td-name">
		            	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
		            		<i class="a-info-circle uk-text-muted"></i>
		            	</div> 
		            	<div class="uk-vertical-align-top uk-display-inline-block fadetext">
		            		<h3 class="uk-margin-bottom-remove">Great American Apartments</h3>
			            	<small class="uk-text-muted">THE NOT SO LONG PROPERTY MANAGER NAME</small>
		            	</div>
		            </td>
		            <td class="hasdivider audit-td-address">
		            	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
		            		<i class="a-marker-basic uk-text-muted"></i>
		            	</div> 
		            	<div class="uk-vertical-align-top uk-display-inline-block fullwidthleftpad fadetext">
		            		<h3 class="uk-margin-bottom-remove">3045 Cumberland Woods Street, Suite 202</h3>
			            	<small class="uk-text-muted">COLUMBUS, OH 43219</small>
		            	</div>
		            	<div class="divider"></div>
		            </td>
		            <td class="hasdivider audit-td-scheduled">
		            	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top" uk-grid>
		            		<div class="uk-width-1-2 uk-padding-remove-top" uk-grid>
			            		<div class="uk-width-1-3 uk-margin-small-top">
			            			<i class="a-mobile-repeat"></i>
			            		</div>
			            		<div class="uk-width-2-3 uk-margin-small-top">
			            			<i class="a-calendar-plus"></i>
			            		</div>
			            	</div> 
			            	<div class="uk-width-1-6 uk-text-right">0* /</div> 
			            	<div class="uk-width-1-6 uk-text-left">21</div> 
			            	<div class="uk-width-1-6 uk-text-left">
			            		<i class="a-circle-checked"></i>
			            	</div>
			            </div>
		            	<div class="divider"></div>
		            </td>
		            <td class="hasdivider audit-td-due">
		            	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top" uk-grid>
			            	<div class="uk-width-1-3 uk-text-large">
			            		<i class="a-bell-2"></i>
			            	</div> 
			            	<div class="uk-width-2-3 uk-text-large">
			            		<h3>12/21</h3>
			            		<div class="dateyear">2018</div>
			            	</div> 
			            </div>
		            	<div class="divider"></div>
		            </td>
		            <td class="hasdivider ">
		            	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top" uk-grid>
			            	<div class="uk-width-1-4">
			            		<i class="a-star-3"></i>
			            	</div> 
			            	<div class="uk-width-1-4">
			            		<i class="a-star-3"></i>
			            	</div> 
			            	<div class="uk-width-1-4">
			            		<i class="a-star-3"></i>
			            	</div> 
			            	<div class="uk-width-1-4">
			            		<i class="a-star-3"></i>
			            	</div> 
			            </div>
		            	<div class="divider"></div>
		            </td>
		            <td class="hasdivider">
		            	<div class="divider"></div>
		            </td>
		            <td>
		            	<div class="uk-margin-small-top" uk-grid>
		            		<div class="uk-width-1-1 no-action">
			            		<i class="a-calendar-7"></i>
							</div>
		            	</div>
		            </td>
		        </tr>
		        <tr id="audit-r-3" class="notcritical">
		            <td id="audit-c-1-3" class="uk-text-center audit-td-lead">
		            	<span id="audit-avatar-badge-3" uk-tooltip="pos:top-left;title:Brian Greenwood - Vendor;" title="" aria-expanded="false" class="user-badge user-badge-blue no-float">
							BG
						</span>
						<small id="audit-rid-3" >#1</small>
		            </td>
		            <td id="audit-c-2-3" class="audit-td-project">
		            	<div class="uk-vertical-align-middle uk-display-inline-block uk-margin-small-top">
		            		<span id="audit-i-project-detail-3" onclick="projectDetails(123,3);" uk-tooltip="pos:top-left;title:Project details;" class="uk-link"><i class="a-list uk-text-muted"></i></span>
		            	</div> 
		            	<div class="uk-vertical-align-middle uk-display-inline-block">
		            		<h3 id="audit-project-name-3" class="uk-margin-bottom-remove">19200114</h3>
			            	<small id="audit-project-aid-3" class="uk-text-muted">AUDIT 2015697</small>
			            </div>
		            </td>
		            <td class="audit-td-name">
		            	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
		            		<i class="a-info-circle uk-text-muted"></i>
		            	</div> 
		            	<div class="uk-vertical-align-top uk-display-inline-block fadetext">
		            		<h3 class="uk-margin-bottom-remove">Great American Apartments</h3>
			            	<small class="uk-text-muted">THE NOT SO LONG PROPERTY MANAGER NAME</small>
		            	</div>
		            </td>
		            <td class="hasdivider audit-td-address">
		            	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
		            		<h3><i class="a-marker-basic uk-text-muted"></i></h3>
		            	</div> 
		            	<div class="uk-vertical-align-top uk-display-inline-block fullwidthleftpad fadetext">
		            		<h3 class="uk-margin-bottom-remove">3045 Cumberland Woods Street, Suite 202</h3>
			            	<small class="uk-text-muted">COLUMBUS, OH 43219</small>
		            	</div>
		            	<div class="divider dotted"></div>
		            </td>
		            <td class="hasdivider audit-td-scheduled">
		            	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top" uk-grid>
		            		<div class="uk-width-1-2 uk-padding-remove-top" uk-grid>
			            		<div class="uk-width-1-3">
			            			<i class="a-mobile-repeat"></i>
			            		</div>
			            		<div class="uk-width-2-3">
			            			<i class="a-calendar-plus"></i>
			            		</div>
			            	</div> 
			            	<div class="uk-width-1-6 uk-text-right">0* /</div> 
			            	<div class="uk-width-1-6 uk-text-left">21</div> 
			            	<div class="uk-width-1-6 uk-text-left">
			            		<i class="a-circle-checked"></i>
			            	</div>
			            </div>
		            	<div class="divider dotted"></div>
		            </td>
		            <td class="hasdivider audit-td-due">
		            	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top" uk-grid>
			            	<div class="uk-width-1-3">
			            		<i class="a-bell-2"></i>
			            	</div> 
			            	<div class="uk-width-2-3">
			            		<i class="a-calendar-plus"></i>
			            	</div> 
			            </div>
		            	<div class="divider dotted"></div>
		            </td>
		            <td class="hasdivider">
		            	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top" uk-grid>
			            	<div class="uk-width-1-4 no-action">
			            		<i class="a-folder"></i>
			            	</div> 
			            	<div class="uk-width-1-4 action-needed">
			            		<i class="a-booboo"></i>
			            	</div> 
			            	<div class="uk-width-1-4 action-required">
			            		<i class="a-skull"></i>
			            	</div> 
			            	<div class="uk-width-1-4 in-progress">
			            		<i class="a-flames"></i>
			            	</div> 
			            </div>
		            	<div class="divider dotted"></div>
		            </td>
		            <td class="hasdivider">
		            	<div class="divider dotted"></div>
		            </td>
		            <td>
		            	<div class="uk-margin-small-top" uk-grid>
		            		<div class="uk-width-1-1 no-action">
			            		<i class="a-calendar-7"></i>
							</div>
		            	</div>
		            </td>
		        </tr>
		        <tr id="audit-r-4" class="notcritical">
		            <td id="audit-c-1-4" class="uk-text-center audit-td-lead">
		            	<span id="audit-avatar-badge-4" uk-tooltip="pos:top-left;title:Brian Greenwood - Vendor;" title="" aria-expanded="false" class="user-badge user-badge-blue no-float">
							BG
						</span>
						<small id="audit-rid-4">#1</small>
		            </td>
		            <td id="audit-c-2-4" class="audit-td-project">
		            	<div class="uk-vertical-align-middle uk-display-inline-block">
		            		<span id="audit-i-project-detail-4" onclick="projectDetails(123,4);" uk-tooltip="pos:top-left;title:Project details;" class="uk-link"><i class="a-list uk-text-muted"></i></span>
		            	</div> 
		            	<div class="uk-vertical-align-middle uk-display-inline-block">
		            		<h3 id="audit-project-name-4" class="uk-margin-bottom-remove">19200114</h3>
			            	<small id="audit-project-aid-4" class="uk-text-muted">AUDIT 2015697</small>
			            </div>
		            </td>
		            <td class="audit-td-name">
		            	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
		            		<h3><i class="a-info-circle uk-text-muted"></i></h3>
		            	</div> 
		            	<div class="uk-vertical-align-top uk-display-inline-block fadetext">
		            		<h3 class="uk-margin-bottom-remove">Great American Apartments</h3>
			            	<small class="uk-text-muted">THE NOT SO LONG PROPERTY MANAGER NAME</small>
		            	</div>
		            </td>
		            <td class="hasdivider audit-td-address">
		            	<div class="uk-vertical-align-top uk-display-inline-block uk-margin-small-top uk-margin-small-left">
		            		<i class="a-marker-basic uk-text-muted"></i>
		            	</div> 
		            	<div class="uk-vertical-align-top uk-display-inline-block fullwidthleftpad fadetext">
		            		<h3 class="uk-margin-bottom-remove">3045 Cumberland Woods Street, Suite 202</h3>
			            	<small class="uk-text-muted">COLUMBUS, OH 43219</small>
		            	</div>
		            	<div class="divider dotted"></div>
		            </td>
		            <td class="hasdivider audit-td-scheduled">
		            	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top" uk-grid>
		            		<div class="uk-width-1-2 uk-padding-remove-top" uk-grid>
			            		<div class="uk-width-1-3">
			            			<i class="a-mobile-repeat"></i>
			            		</div>
			            		<div class="uk-width-2-3">
			            			<i class="a-calendar-plus"></i>
			            		</div>
			            	</div> 
			            	<div class="uk-width-1-6 uk-text-right">0* /</div> 
			            	<div class="uk-width-1-6 uk-text-left">21</div> 
			            	<div class="uk-width-1-6 uk-text-left">
			            		<i class="a-circle-checked"></i>
			            	</div>
			            </div>
		            	<div class="divider dotted"></div>
		            </td>
		            <td class="hasdivider audit-td-due">
		            	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top" uk-grid>
			            	<div class="uk-width-1-3">
			            		<i class="a-bell-2"></i>
			            	</div> 
			            	<div class="uk-width-2-3">
			            		<i class="a-calendar-plus"></i>
			            	</div> 
			            </div>
		            	<div class="divider dotted"></div>
		            </td>
		            <td class="hasdivider">
		            	<div class="uk-display-inline-block uk-text-center fullwidth uk-margin-small-top" uk-grid>
			            	<div class="uk-width-1-4">
			            		<i class="a-star-3"></i>
			            	</div> 
			            	<div class="uk-width-1-4">
			            		<i class="a-star-3"></i>
			            	</div> 
			            	<div class="uk-width-1-4">
			            		<i class="a-star-3"></i>
			            	</div> 
			            	<div class="uk-width-1-4">
			            		<i class="a-star-3"></i>
			            	</div> 
			            </div>
		            	<div class="divider dotted"></div>
		            </td>
		            <td class="hasdivider">
		            	<div class="divider dotted"></div>
		            </td>
		            <td>
		            	<div class="uk-margin-small-top" uk-grid>
		            		<div class="uk-width-1-1 no-action">
			            		<i class="a-calendar-7"></i>
							</div>
		            	</div>
		            </td>
		        </tr>
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
	<button class="uk-button uk-button-primary btnToggleCritical" onclick="toggleCritical();"><i class="a-eye-not"></i> HIDE NON CRITICAL</button>
	<button class="uk-button uk-button-primary btnToggleCritical" onclick="toggleCritical();" style="display:none;"><i class="a-eye-2"></i> SHOW NON CRITICAL</button>
	<a href="#top" uk-scroll="{offset: 90}" class="uk-button uk-button-default"><span class="a-arrow-small-up uk-text-small uk-vertical-align-middle"></span> SCROLL TO TOP</a>
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

	function toggleCritical() {
		var shownoncritical = 1;
		@if(session()->has('audit-shownoncritical'))
			@if(session('audit-shownoncritical') != 1)
			shownoncritical = 0;
			@endif
		@endif
		$.get( '/poc/session/filters/audit-shownoncritical/', function( data ) {  
           // reload
			$('#detail-tab-1').trigger("click");
			// UIkit.switcher('#top-tabs').show(0);
        });
		$(".notcritical").fadeToggle();
		$('.btnToggleCritical').toggle();
	}

	function createAudits(){
		console.log("create audits clicked");
	}

	function projectDetails(id, target) {
		if ($('#audit-r-'+target+'-buildings').length){
			// close own details
			$('#audit-r-'+target+'-buildings').remove();
		}else{
			// close all details
			$('tr[id$="-buildings"]').remove();
			// fetch and display new details
			var url = '{{route("audit.buildings", ["audit" => "xi", "target" => "xt"])}}';
			url = url.replace('xi', id);
			url = url.replace('xt', target);
		    $.get(url, {
	            '_token' : '{{ csrf_token() }}'
	            }, function(data) {
	                if(data=='0'){ 
	                    UIkit.modal.alert("There was a problem getting the buildings' information.");
	                } else {
	                	// scroll to row
	                	$('html, body').animate({
							scrollTop: $('#audit-r-'+target).offset().top - 60
							}, 500, 'linear');
						$('#audit-r-'+target).after(data);
	            	}
		    });
		}
	}

	function buildingDetails(id, auditid, target) {
		if ($('#building-r-'+target+'-details').length){
			// close own details
			$('#building-r-'+target+'-details').remove();
			// unblur other building rows
			$('div[id^="building-r-"]').removeClass('blur');
		}else{
			// close all details
			$('div[id$="-details"]').remove();
			// unblur other building rows
			$('div[id^="building-r-"]').removeClass('blur');

			// fetch and display new details
			var url = '{{route("audit.building.details", ["audit" => "xa", "building" => "xi", "target" => "xt"])}}';
			url = url.replace('xi', id);
			url = url.replace('xa', auditid);
			url = url.replace('xt', target);
		    $.get(url, {
	            '_token' : '{{ csrf_token() }}'
	            }, function(data) {
	                if(data=='0'){ 
	                    UIkit.modal.alert("There was a problem getting the building details' information.");
	                } else {
	                	// blur all other building rows
						$('div[id^="building-r-"]').not( 'div[id="building-r-'+target+'"]' ).addClass('blur');

	                	// scroll to row
	                	$('html, body').animate({
							scrollTop: $('#building-r-'+target).offset().top - 60
							}, 500, 'linear');
						$('#building-r-'+target).after(data);
	            	}
		    });
		}
	}

	function addArea() {
		console.log('adding inspectable area');
	}

	function applyFilter(filter, value) {
		// hide tab
		$("#detail-tab-1-content").children().fadeOut();
		// set session value
		$.get( '/poc/session/filters/'+filter+'/'+value, function( data ) {  
           // reload
			$('#detail-tab-1').trigger("click");
			// UIkit.switcher('#top-tabs').show(0);
        });
	}

	var quicklookupbox = new autoComplete({
    	selector: '#filter-by-project',
        minChars: 3,
        cache: 1,
        delay: 150,
		offsetLeft: 0,
		offsetTop: 1,
		menuClass: '',

        source: function(term, suggest){
        	console.log('filtering by name... '+term);
        	$.get( "/poc/autocomplete/auditproject", {
				'search' : term,
				'_token' : '{{ csrf_token() }}'
			},
			function(data) {
				var output = eval(data);
				term = term.toLowerCase();
	            var suggestions = [];
	            for (i=0;i<output.length;i++)
	            	suggestions.push(output[i]);
		        suggest(suggestions);
			},
			'json' );
        },
        renderItem: function (item, search){
		    search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
		    var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");

		    var output = '<div class="autocomplete-suggestion" data-item-id="'+item[4]+'" data-val="'+search+'">';
		    output = output + 'Parcel ID: '+item[3]+'<br />';
		    output = output + item[0]+'<br />';
		    output = output + item[1]+', '+item[2]+' '+item[3]+'<br />';
			output = output + '<span class="hideImport'+item[6]+'">';
		    output = output + '</div>';
		    
		    return output;
		},
	    onSelect: function(e, term, item){
	    	e.preventDefault();
	    	loadDetailTab('/parcel/',item.getAttribute('data-item-id'),'1',0,0);
	    	$('#quick-lookup-box').val('');
	    }
    });
    var quicklookupbox = new autoComplete({
    	selector: '#filter-by-name',
        minChars: 3,
        cache: 1,
        delay: 150,
		offsetLeft: 0,
		offsetTop: 1,
		menuClass: '',

        source: function(term, suggest){
        	console.log('filtering by name... '+term);
        	$.get( "/poc/autocomplete/auditname", {
				'search' : term,
				'_token' : '{{ csrf_token() }}'
			},
			function(data) {
				var output = eval(data);
				term = term.toLowerCase();
	            var suggestions = [];
	            for (i=0;i<output.length;i++)
	            	suggestions.push(output[i]);
		        suggest(suggestions);
			},
			'json' );
        },
        renderItem: function (item, search){
		    search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
		    var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");

		    var output = '<div class="autocomplete-suggestion" data-item-id="'+item[4]+'" data-val="'+search+'">';
		    output = output + 'Parcel ID: '+item[3]+'<br />';
		    output = output + item[0]+'<br />';
		    output = output + item[1]+', '+item[2]+' '+item[3]+'<br />';
			output = output + '<span class="hideImport'+item[6]+'">';
		    output = output + '</div>';
		    
		    return output;
		},
	    onSelect: function(e, term, item){
	    	e.preventDefault();
	    	loadDetailTab('/parcel/',item.getAttribute('data-item-id'),'1',0,0);
	    	$('#quick-lookup-box').val('');
	    }
    });
    var quicklookupbox = new autoComplete({
    	selector: '#filter-by-address',
        minChars: 3,
        cache: 1,
        delay: 150,
		offsetLeft: 0,
		offsetTop: 1,
		menuClass: '',

        source: function(term, suggest){
        	console.log('filtering by name... '+term);
        	$.get( "/poc/autocomplete/auditaddress", {
				'search' : term,
				'_token' : '{{ csrf_token() }}'
			},
			function(data) {
				var output = eval(data);
				term = term.toLowerCase();
	            var suggestions = [];
	            for (i=0;i<output.length;i++)
	            	suggestions.push(output[i]);
		        suggest(suggestions);
			},
			'json' );
        },
        renderItem: function (item, search){
		    search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
		    var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");

		    var output = '<div class="autocomplete-suggestion" data-item-id="'+item[4]+'" data-val="'+search+'">';
		    output = output + 'Parcel ID: '+item[3]+'<br />';
		    output = output + item[0]+'<br />';
		    output = output + item[1]+', '+item[2]+' '+item[3]+'<br />';
			output = output + '<span class="hideImport'+item[6]+'">';
		    output = output + '</div>';
		    
		    return output;
		},
	    onSelect: function(e, term, item){
	    	e.preventDefault();
	    	loadDetailTab('/parcel/',item.getAttribute('data-item-id'),'1',0,0);
	    	$('#quick-lookup-box').val('');
	    }
    });
</script>