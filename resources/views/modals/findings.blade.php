<div id="modal-findings" class="uk-margin-top" >
	<div class="modal-findings-right" uk-filter="target: .js-findings">
		<div class="modal-findings-right-top">
		    <div class="uk-width-1-1 filter-button-set-right" uk-grid>
		        <div class="uk-width-1-4 uk-active findinggroup" uk-filter-control="filter: [data-finding-filter*='my-finding']; group: findingfilter;">
	                <button class="uk-button uk-button-default button-filter button-filter-border-left" >My finding</button>
		        	<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="">
						<a class="sort-asc"></a>
					</span>
	            </div>
	            <div class="uk-width-1-4 findinggroup" uk-filter-control="filter: [data-finding-filter*='all']; group: findingfilter;">    
	            	<button class="uk-button uk-button-default button-filter">All findings</button>
		        	<span style="display:none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="">
						<a class="sort-asc"></a>
					</span>
	            </div>
	            <div class="uk-width-1-4 uk-active auditgroup" uk-filter-control="filter: [data-audit-filter*='this-audit']; group: auditfilter;">
	                <button class="uk-button uk-button-default button-filter">this audit</button>
	                <span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="">
						<a class="sort-asc"></a>
					</span>
	            </div>
	            <div class="uk-width-1-4 auditgroup" uk-filter-control="filter: [data-audit-filter*='all']; group: auditfilter; ">
	                <button class="uk-button uk-button-default button-filter">all audits</button>
	                <span style="display:none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="">
						<a class="sort-asc"></a>
					</span>
		        </div>
		    </div>
		   </div>
	    <div class="modal-findings-right-bottom-container">
			<div class="modal-findings-right-bottom">
				<div class="inspec-tools-tab-findings-container uk-panel uk-panel-scrollable uk-padding-remove js-findings">
			    	@foreach($data['findings'] as $finding)
			        <div id="inspec-tools-tab-finding-{{$finding['id']}}" class="inspec-tools-tab-finding" data-ordering-finding="{{$finding['id']}}" data-finding-id="{{$finding['id']}}" data-audit-filter="{{$finding['audit-filter']}} all" data-finding-filter="{{$finding['finding-filter']}} all" uk-grid>
			        	<div id="inspec-tools-tab-finding-sticky-{{$finding['id']}}" class="inspec-tools-tab-finding-sticky uk-width-1-1 uk-padding-remove  {{$finding['status']}}" style="display:none">
			        		<div class="uk-grid-match" uk-grid>
								<div class="uk-width-1-4 uk-padding-remove-top uk-padding-remove-left">
									<div>
										<i class="uk-inline {{$finding['icon']}}"></i> <i class="uk-inline a-menu" onclick="expandFindingItems(this);"></i>
									</div>
								</div>
								<div class="uk-width-3-4 uk-padding-remove-top uk-padding-remove-right">
									<div>
										{{$finding['date']}}: FN#{{$finding['ref']}}
										<div class="uk-float-right"><i class="a-circle-plus use-hand-cursor"></i></div>
									</div>
								</div>
			        		</div>
			        	</div>

						<div class="inspec-tools-tab-finding-info uk-width-1-1  uk-active {{$finding['status']}}" style="padding-top: 15px;">
		    				<div class="uk-grid-match" uk-grid>
				    			<div class="uk-width-1-4 uk-padding-remove-top uk-padding-remove-left">
				    				<div class="uk-display-block">
					    				<i class="{{$finding['icon']}}"></i><br>
					    				<span class="auditinfo">AUDIT {{$finding['audit']}}</span>
					    			</div>
					    			<div class="uk-display-block" style="margin: 15px 0;">
					    				<button class="uk-button inspec-tools-findings-resolve uk-link"><span class="uk-badge">
									    	 &nbsp; </span>RESOLVE</button>
									</div>
									<div class="inspec-tools-tab-finding-stats" style="margin: 0 0 15px 0;">
										<i class="a-bell-plus"></i> <span id="inspec-tools-tab-finding-stat-reminders">0</span><br />
										<i class="a-comment-plus"></i> 1<br />
										<i class="a-file-plus"></i> 0<br />
										<i class="a-picture"></i> 0<br />

										<i class="a-menu" onclick="expandFindingItems(this);"></i>
									</div>
				    			</div>
				    			<div class="uk-width-3-4 uk-padding-remove-right uk-padding-remove">
				    				<div class="uk-width-1-1 uk-display-block uk-padding-remove inspec-tools-tab-finding-description">
					    				<p>{{$finding['date']}}: FN#{{$finding['ref']}}<br />
					    					By {{$finding['auditor']['name']}}<br>
					    					{{$finding['amenity']['address']}}<br />
					    					{{$finding['amenity']['city']}}, {{$finding['amenity']['state']}} {{$finding['amenity']['zip']}}
					    				</p>
					    				<p>{{$finding['building']['name']}}: {{$finding['amenity']['name']}}<br />{{$finding['description']}}</p>
					    				<div class="inspec-tools-tab-finding-actions">
										    <button class="uk-button uk-link"><i class="a-pencil-2"></i> EDIT</button>
					    					<button class="uk-button uk-link"><i class="a-trash-3"></i> DELETE</button>
					    				</div>
					    				<div class="inspec-tools-tab-finding-top-actions">
					    					<i class="a-circle-plus use-hand-cursor"></i>
										    <div uk-drop="mode: click" style="min-width: 315px;">
										        <div class="uk-card uk-card-body uk-card-default uk-card-small">
										    	 	<div class="uk-drop-grid uk-child-width-1-4" uk-grid>
										    	 		<div class="icon-circle use-hand-cursor" onclick="addChildItem({{$finding['id']}}, 'followup')"><i class="a-bell-plus"></i></div>
										    	 		<div class="icon-circle use-hand-cursor"  onclick="addChildItem({{$finding['id']}}, 'comment')"><i class="a-comment-plus"></i></div>
										    	 		<div class="icon-circle use-hand-cursor"  onclick="addChildItem({{$finding['id']}}, 'document')"><i class="a-file-plus"></i></div>
										    	 		<div class="icon-circle use-hand-cursor"  onclick="addChildItem({{$finding['id']}}, 'photo')"><i class="a-picture"></i></div>
										    	 	</div>
										        </div>
										    </div>
					    				</div>
					    			</div>
				    			</div>
				    		</div>
				    	</div>
					</div>
				    @endforeach
				</div>
			</div>
		</div>

	</div>
	<div class="modal-findings-left" uk-filter="target: .js-filter-findings">
		<div class="modal-findings-left-bottom-container">
			<div class="modal-findings-left-bottom">
				<div id="modal-findings-filters" class="uk-margin uk-child-width-auto" uk-grid>
			        <div class="uk-width-1-1 uk-padding-remove uk-inline">
			            <button class="uk-button button-finding-filter" onclick="alert('Showing all inspectable amenities for the buiding selected');"><i class="a-mobile-home"></i> Elevator #2</button>
					</div>
			        <div class="uk-width-1-1 uk-padding-remove uk-margin-small uk-inline">
			            <button class="uk-button button-finding-filter" onclick="alert('Showing all buildings for the address selected');"><i class="a-buildings"></i> Building</button>
					</div>
			        <div class="uk-width-1-1 uk-padding-remove uk-margin-small uk-inline">
			            <button class="uk-button button-finding-filter" onclick="alert('Change address within project');"><i class="a-buildings"></i> 1234567 Silvegwood Street, Colombus, OH 43219</button>
					</div>
			        <div class="uk-width-1-1 uk-padding-remove uk-margin-small uk-inline">
			            <button class="uk-button button-finding-filter" disabled><i class="a-calendar-pencil"></i> December 22, 2018</button>
					</div>
		        </div>
		        <div class="uk-margin-remove" uk-grid>
            		<div class="uk-width-1-1 uk-padding-remove">
            			<button class="uk-button uk-button-primary button-finding-filter uk-width-1-1 @if(!$checkDoneAddingFindings) uk-modal-close @endif" @if($checkDoneAddingFindings) onclick="completionCheck();return false;" @endif>DONE ADDING FINDINGS</button>
            		</div>
            	</div>
			</div>
		</div>
		<div class="modal-findings-left-main-container">
			<div class="modal-findings-left-main">
				<div id="modal-findings-list-filters" class="uk-margin uk-child-width-auto uk-grid filter-checkbox-list js-filter-findings">
						@foreach($data['finding-types'] as $finding_type)
						<div id="filter-checkbox-list-item-{{$finding_type['id']}}" class=" uk-padding-remove filter-checkbox-list-item" data-finding="{{$finding_type['type']}}" data-title-finding="{{$finding_type['name']}}" uk-grid>
							<div class="uk-width-1-1 uk-padding-remove indented">
					            <input id="filter-findings-filter-{{$finding_type['id']}}" value="" type="checkbox" data-finding="{{$finding_type['type']}}" onclick="newFinding({{$finding_type['id']}});"/>
								<label for="filter-findings-filter-{{$finding_type['id']}}" data-finding="{{$finding_type['type']}}" ><i class="{{$finding_type['icon']}}"></i> {{$finding_type['name']}}</label>
							</div>
						</div>
						@endforeach

		        </div>
			</div>
		</div>
		<div class="modal-findings-left-top" uk-grid>
			<div class="uk-width-1-1 filter-button-set">
				<div uk-grid>
			        <div class="uk-inline uk-width-1-2">
			            <i class="a-magnify-2 uk-form-icon"></i>
			            <input name="finding-description" id="finding-description" class="uk-input button-filter" placeholder="ENTER FINDING DESCRIPTION" type="text">
			        </div>
			        <div class="uk-inline uk-width-1-2">
			        	<div uk-grid>
			        		<div class="uk-width-1-4">
			        			<button id="all-filter-button" data-uk-tooltip="{pos:'bottom'}" class="uk-button uk-button-default button-filter uk-active" uk-filter-control title="Show All Findings (Unfiltered)" onclick="$('#lt-findings-filter').fadeOut();$('#nlt-findings-filter').fadeOut();$('#file-findings-filter').fadeOut();$('#all-findings-filter').fadeIn();"><i class="uk-icon-asterisk"></i></button>
					        	<span id="all-findings-filter"  class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false" @if($type != 'all') style="display: none;" @endIf>
									<a  class="sort-desc"></a>
								</span>
			        		</div>
			        		<div class="uk-width-1-4">
			        			<button id="file-filter-button" data-uk-tooltip="{pos:'bottom'}" class="uk-button uk-button-default button-filter" uk-filter-control="filter: [data-finding='file'];" title="Show File Findings Only" onclick="$('#lt-findings-filter').fadeOut();$('#nlt-findings-filter').fadeOut();$('#file-findings-filter').fadeIn();$('#all-findings-filter').fadeOut();"><i class="a-folder"></i></button>
					        	<span id="file-findings-filter" @if($type != 'file') style="display: none;" @endIf  class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a  class="sort-desc"></a>
								</span>
			        		</div>
			        		<div class="uk-width-1-4">
			        			<button id="nlt-filter-button" data-uk-tooltip="{pos:'bottom'}" class="uk-button uk-button-default button-filter" uk-filter-control="filter: [data-finding='nlt'];" title="Show Non-life Threatning Findings Only" onclick="$('#lt-findings-filter').fadeOut();$('#nlt-findings-filter').fadeIn();$('#file-findings-filter').fadeOut();$('#all-findings-filter').fadeOut();"><i class="a-booboo"></i></button>
					        	<span id="nlt-findings-filter" @if($type != 'nlt') style="display: none;" @endIf class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a  class="sort-desc"></a>
								</span>
			        		</div>
			        		<div class="uk-width-1-4">
			        			<button id="lt-filter-button" data-uk-tooltip="{pos:'bottom'}" class="uk-button uk-button-default button-filter" uk-filter-control="filter: [data-finding='lt'];" title="Show Life Threatning Findings Only" onclick="$('#lt-findings-filter').fadeIn();$('#nlt-findings-filter').fadeOut();$('#file-findings-filter').fadeOut();$('#all-findings-filter').fadeOut();"><i class="a-skull"></i></button>
					        	<span id="lt-findings-filter" @if($type != 'lt') style="display: none;" @endIf  class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a  class="sort-desc"></a>
								</span>
			        		</div>
			        		
			        	</div>
			        </div>
			    </div>
			</div>
		</div>
	</div>
</div>


@include('templates.modal-findings-new-form')
@include('templates.modal-findings-new')
@include('templates.modal-findings-items')

<div id="modal-findings-completion-check" uk-modal>
  <div class="uk-modal-dialog uk-modal-body uk-modal-content" uk-overflow-auto> 
  	<a class="uk-modal-close-default" uk-close></a>
  	<div uk-grid>
  		<div class="uk-width-1-2  uk-margin-medium-top">
  			<p>Have you finished inspecting all items for that building/unit/common area?</p> 
  			<div class="uk-padding-remove" uk-grid>
	  			<div class="uk-width-1-1 uk-padding-remove uk-margin-medium-top">
	  				<button class="uk-button uk-button-primary uk-margin-left uk-margin-right uk-padding-remove uk-margin-remove uk-width-1-1">Yes, Mark as Complete and Submit to Lead.</button>
	  			</div>
	  			<div class="uk-width-1-1 uk-padding-remove uk-margin-medium-top">
	  				<button class="uk-button uk-button-primary uk-padding-remove uk-margin-remove uk-width-1-1">Just the Items I have Findings For.</button>
	  			</div>
	  			<div class="uk-width-1-1 uk-padding-remove uk-margin-medium-top">
	  				<button class="uk-button uk-button-default uk-padding-remove uk-margin-remove uk-width-1-1 uk-modal-close">No, I am still working on it.</button>
	  			</div>
	  		</div>
  		</div>
  		<div class="uk-width-1-2  uk-margin-medium-top">
  			<div>bulleted list of items that have not had any findings here<br />
  			<ul class="uk-list">
  				<li>item</li>
  				<li>item</li>
  			</ul>
  		</div>
  	</div>
  </div>
 </div>
 @if($type != 'all')
<script>
	function clickDefault(){
		document.setTimeout(function() {
			$('#{{$type}}-filter-button').trigger('click');
			alert('filtered');
		}, 10);
		
	}
	window.onload(clickDefault());
</script>
@endif
