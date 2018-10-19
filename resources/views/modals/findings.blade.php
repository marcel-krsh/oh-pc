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
				<div class="inspec-tools-tab-findings-container uk-panel uk-panel-scrollable js-findings">
			    	@foreach($data['findings'] as $finding)
			        <div id="inspec-tools-tab-finding-{{$finding['id']}}" class="inspec-tools-tab-finding {{$finding['status']}}" data-ordering-finding="{{$finding['id']}}" data-finding-id="{{$finding['id']}}" data-audit-filter="{{$finding['audit-filter']}} all" data-finding-filter="{{$finding['finding-filter']}} all" uk-grid>
						<div class="inspec-tools-tab-finding-info uk-width-1-1 uk-first-column  uk-active" style="padding-top: 15px;">
		    				<div class="uk-grid-match" uk-grid>
				    			<div class="uk-width-1-4 uk-padding-remove-left uk-first-column">
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
										    <div uk-drop="mode: click">
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
			        		<div class="uk-width-1-5">
			        			<button class="uk-button uk-button-default button-filter uk-active" uk-filter-control><i class="uk-icon-asterisk"></i></button>
					        	<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a id="" class="sort-desc"></a>
								</span>
			        		</div>
			        		<div class="uk-width-1-5">
			        			<button class="uk-button uk-button-default button-filter" uk-filter-control="filter: [data-finding='file'];"><i class="a-folder"></i></button>
					        	<span style="display: none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a id="" class="sort-desc"></a>
								</span>
			        		</div>
			        		<div class="uk-width-1-5">
			        			<button class="uk-button uk-button-default button-filter" uk-filter-control="filter: [data-finding='nlt'];"><i class="a-booboo"></i></button>
					        	<span style="display: none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a id="" class="sort-desc"></a>
								</span>
			        		</div>
			        		<div class="uk-width-1-5">
			        			<button class="uk-button uk-button-default button-filter" uk-filter-control="filter: [data-finding='lt'];"><i class="a-skull"></i></button>
					        	<span style="display: none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a id="" class="sort-asc"></a>
								</span>
			        		</div>
			        		<div class="uk-width-1-5">
			        			<button class="uk-button uk-button-default button-filter" uk-filter-control="filter: [data-finding='sd'];"><i class="a-flames"></i></button>
					        	<span style="display: none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a id="" class="sort-desc"></a>
								</span>
			        		</div>
			        	</div>
			        </div>
			    </div>
			</div>
		</div>
	</div>
</div>

<template class="uk-hidden" id="modal-findings-new-form-template">
	<div class="findings-new-add-comment" data-finding-id="tplFindingId">
	    <div class="findings-new-add-comment-textarea">
	    	<textarea class="uk-textarea">Custom comment based on what I saw... %%date-in-7-days%%</textarea>
	    	<div class="textarea-status">SAVED</div>
	    </div>
	    <div class="findings-new-add-comment-boilerplate-action" uk-grid>
	    	<button class="uk-width-1-3" onclick="useBoilerplate();"><i class="a-file-text"></i> Use a boilerplate</button>
	    	<button class="uk-width-1-3" onclick="clearTextarea();"><i class="a-file-minus"></i> Clear</button>
	    	<button class="uk-width-1-3" onclick="appendBoilerplate();"><i class="a-file-plus"></i> Append a boilerplate</button>
	    </div>
	    <div class="findings-new-add-comment-quick-entry-list">
	    	<span class="uk-badge findings-quick-entry" onclick="insertTag(this);" data-tag="property-manager-contact-name">PROPERTY MANAGER CONTACT NAME</span>
	    	<span class="uk-badge findings-quick-entry" onclick="insertTag(this);" data-tag="address-of-this-building">ADDRESS OF THIS BUILDING</span>
	    	<span class="uk-badge findings-quick-entry" onclick="insertTag(this);" data-tag="date-in-7-days">DATE IN 7 DAYS</span>
	    	<span class="uk-badge findings-quick-entry" onclick="insertTag(this);" data-tag="tomorrow-date">TOMORROW'S DATE</span>
	    	<span class="uk-badge findings-quick-entry" onclick="insertTag(this);" data-tag="head-of-household-name">HEAD OF HOUSEHOLD NAME</span>
	    	<span class="uk-badge findings-quick-entry" onclick="insertTag(this);" data-tag="another-tag">ANOTHER QUICK ENTRY BUTTON</span>
	    </div>
	    <div class="findings-new-add-comment-boilerplate-save" uk-grid>
	    	<div class="uk-width-1-2">
	    		<button onclick="saveBoilerplace();"><i class="a-file-text"></i> Save as new boilerplate for this finding</button>
	    	</div>
	    	<div class="uk-width-1-2">
	    		<button onclick="saveBoilerplaceAndNewFinding();"><i class="a-file-copy-2"></i> Save and add another of this same finding</button>
	    	</div>
	    </div>
	</div>
</template>

<template class="uk-hidden" id="modal-findings-new-template">
	<div class="inspec-tools-tab-finding action-needed uk-grid uk-grid-stack" uk-grid="">
		<div class="uk-width-1-1 uk-first-column" style="padding-top: 15px;">
			<div uk-grid="" class="uk-grid">
    			<div class="uk-width-1-4 uk-first-column">
    				<i class="a-booboo"></i> NLT<br>
    				<span class="auditinfo">AUDIT 20120394</span><br /><br />
    				<button class="uk-button inspec-tools-findings-resolve uk-link"><span class="uk-badge">
				    	 &nbsp; </span>RESOLVE</button>
    			</div>
    			<div class="uk-width-3-4 bordered">
    				<p>12/22/2018 12:51:38 PM: By Holly Swisher<br>
    				STAIR #1 : Finding Description Goes here and continues here for when it is long</p>
    			</div>
    		</div>
    	</div>
    	<div class="uk-width-1-1 uk-margin-remove inspec-tools-tab-finding-comment uk-grid-margin uk-first-column">
    		<div uk-grid="" class="uk-grid">
    			<div class="uk-width-1-4 uk-first-column">
    				<i class="a-comment-text"></i> COMMENT
    			</div>
    			<div class="uk-width-3-4 borderedcomment">
    				<p>12/31/2018 12:59:38 PM: By Holly Swisher<br>
    					<span class="finding-comment">Comment goes here and is italicised to show that it is a comment and not a finding.</span></p>
					<button class="uk-button inspec-tools-tab-finding-reply uk-link">
    					<i class="a-comment-pencil"></i> REPLY
				    </button>
    			</div>
    		</div>
    	</div>
    	<div class="uk-width-1-1 uk-margin-remove uk-grid-margin uk-first-column">
    		<div uk-grid="" class="uk-grid">
    			<div class="uk-width-1-4 uk-first-column">
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
</template>

<template class="uk-hidden" id="inspec-tools-tab-finding-item-template">
	<div class="uk-width-1-1 uk-margin-remove inspec-tools-tab-finding-comment uk-first-column">
		<div uk-grid="" class="uk-grid">
			<div class="uk-width-1-4 uk-first-column">
				<i class="a-comment-text"></i> COMMENT
			</div>
			<div class="uk-width-3-4 borderedcomment">
				<p>12/31/2018 12:59:38 PM: By Holly Swisher<br>
					<span class="finding-comment">Comment goes here and is italicised to show that it is a comment and not a finding.</span></p>
				<button class="uk-button inspec-tools-tab-finding-reply uk-link">
					<i class="a-comment-pencil"></i> REPLY
			    </button>
			</div>
		</div>
	</div>

	<div class="uk-width-1-1 uk-margin-remove uk-grid-margin uk-first-column">
		<div uk-grid="" class="uk-grid">
			<div class="uk-width-1-4 uk-first-column">
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
</template>

<template class="uk-hidden" id="inspec-tools-tab-finding-items-template">
	<div class="inspec-tools-tab-finding-items uk-width-1-1 uk-first-column uk-margin-remove" style="display:none">
		<div class="inspec-tools-tab-finding-items-list" uk-grid>
	    	
	    </div>
	</div>
</template>

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

<script>

function completionCheck() {
	UIkit.modal('#modal-findings-completion-check', {center: true, bgclose: false, keyboard:false,  stack:true}).show();
}

function getCurrentFilter() {
	var activeFilterButton = $('.button-filter.uk-active').attr('uk-filter-control');
    var regexFilter = "data-finding='(.*)'"; //[data-finding='sd']
   
    var activeFilter = activeFilterButton.match(regexFilter);

    if(activeFilter !== null){
    	return activeFilter[1];
    }else{
    	return '';
    }
}

function searchFilterTerm(valThis) {
	// combine with active filter
	var currentActiveFilter = $('.button-filter.uk-active');
	var filterFindings= $('.js-filter-findings');

    var currentFilter = getCurrentFilter();

	var sortableElementParent = $('.js-filter-findings');

  	// if currentFilter is set, only search through the visible items
  	if(currentFilter.length){
  		var sortableElements = sortableElementParent.children("[data-finding='"+currentFilter+"']");
  	}else{
  		var sortableElements = sortableElementParent.children();
  	}
  	sortableElements.each(function(){
     	var text = this.getAttribute('data-title-finding').toLowerCase();
        (text.indexOf(valThis) >= 0) ? $(this).show() : $(this).hide(); 
    });
}

$('#finding-description').keyup(function(){
   var valThis = $(this).val();
   searchFilterTerm(valThis); 
});

function newFinding(id){
	
	// scroll to row early
    $('html, body').animate({
		scrollTop: $('#filter-checkbox-list-item-'+id).offset().top - 59
	}, 500, 'linear');

	if ($('#filter-checkbox-list-item-'+id).attr('expanded')){
		$('#filter-checkbox-list-item-'+id).removeAttr('expanded');
		$('.filter-checkbox-new-item-'+id).slideUp("slow", function() {

			$(this).remove();

			// reapply search
			var valThis = $('#finding-description').val();
   			searchFilterTerm(valThis); 
		 });
	}else{

		var newFindingsFormTemplate = $('#modal-findings-new-form-template').html();
		var newFindingsTemplate = $('#modal-findings-new-form-template').html();

		var newfinding = newFindingsFormTemplate.replace(/tplFindingId/g, id);

		$('#filter-checkbox-list-item-'+id).append('<div style="display:none" class="uk-width-1-1 uk-padding-remove filter-checkbox-new-item-'+id+'">'+newfinding+'</div>');

		$('.filter-checkbox-new-item-'+id).slideDown("slow");
		$('#filter-checkbox-list-item-'+id).attr( "expanded", true );

		$('.findings-new-add-comment-textarea textarea').bind('input propertychange', function() {
		    console.log("trying to save to db... "+$(this).val());
		    $.post('/autosave', {
		        '_token' : '{{ csrf_token() }}'
		        }, function(data) {
		        	console.log("saved textarea returned "+data);
		    });
		});

	}
	
}

$('.filter-button-set-right').find('[uk-filter-control]').click(function() {
	if($(this).find('span').is(':visible')){

		// switch order
		var currentOrdering = 'sort-asc';
	  	if($(this).find('span a').hasClass('sort-desc')){
			currentOrdering = 'sort-desc';
	  	}

	  	// switch the span data and the visual
	  	if(currentOrdering == "sort-asc"){
	  		var sortableElementParent = $('.js-findings');
		  	var sortableElements = sortableElementParent.children(); console.log(sortableElements.length);
		  	sortableElements.sort(function(a,b){
				var an = a.getAttribute('data-ordering-finding'),
					bn = b.getAttribute('data-ordering-finding');

				if(an > bn) {
					return 1;
				}
				if(an < bn) {
					return -1;
				}
				return 0;
			});
			 sortableElements.detach().appendTo(sortableElementParent);
	  		$(this).find('span a').removeClass('sort-asc').addClass('sort-desc');
	  	}else{
	  		var sortableElementParent = $('.js-findings');
		  	var sortableElements = sortableElementParent.children();console.log(sortableElements.length);
		  	sortableElements.sort(function(a,b){
				var an = a.getAttribute('data-ordering-finding'),
					bn = b.getAttribute('data-ordering-finding');

				if(an < bn) {
					return 1;
				}
				if(an > bn) {
					return -1;
				}
				return 0;
			});
		    sortableElements.detach().appendTo(sortableElementParent);
	  		$(this).find('span a').removeClass('sort-desc').addClass('sort-asc');
	  	}
	}else{

		// close all spans in the group
		if($(this).hasClass('auditgroup')){
			$('.auditgroup').find('span').hide();
		}else if($(this).hasClass('findinggroup')){
			$('.findinggroup').find('span').hide();
		}

		// show selected one
		$(this).find('span').toggle();
	}
});

$('.filter-button-set').find('button.button-filter').click(function() {
  $('#finding-description').val('');
  // check if selected span is already visible, if so switch the order
  if($(this).closest('div').find('span').is(':visible')){
  	// what is the current ordering
  	var currentOrdering = 'sort-asc';
  	if($(this).closest('div').find('span a').hasClass('sort-desc')){
		currentOrdering = 'sort-desc';
  	}

  	// swtich the span data and the visual
  	if(currentOrdering == "sort-asc"){
  		var sortableElementParent = $('.js-filter-findings');
	  	var sortableElements = sortableElementParent.children();
	  	sortableElements.sort(function(a,b){
			var an = a.getAttribute('data-title-finding'),
				bn = b.getAttribute('data-title-finding');

			if(an > bn) {
				return 1;
			}
			if(an < bn) {
				return -1;
			}
			return 0;
		});
		sortableElements.detach().appendTo(sortableElementParent);

  		$(this).closest('div.uk-grid').find('span a').removeClass('sort-asc').addClass('sort-desc');
  	}else{
  		var sortableElementParent = $('.js-filter-findings');
	  	var sortableElements = sortableElementParent.children();
	  	sortableElements.sort(function(a,b){
			var an = a.getAttribute('data-title-finding'),
				bn = b.getAttribute('data-title-finding');

			if(an < bn) {
				return 1;
			}
			if(an > bn) {
				return -1;
			}
			return 0;
		});
		sortableElements.detach().appendTo(sortableElementParent);

  		$(this).closest('div.uk-grid').find('span a').removeClass('sort-desc').addClass('sort-asc');
  	}
  }else{
  	// hide all orderSpans
	$('.filter-button-set').find('span.order-span').hide();

	// show selected one
	var orderSpan = $(this).closest('div').find('span');
	orderSpan.toggle();
  }

  // is there a search term?
  var searchTerm = $('#finding-description').val();
  if(searchTerm.length > 0){
  	searchFilterTerm(searchTerm); 
  }
  
});

function useBoilerplate(){
	var currentFinding = $('.findings-new-add-comment').data("finding-id");

}
function clearTextarea(){
	var currentFinding = $('.findings-new-add-comment').data("finding-id");

}
function appendBoilerplate(){
	var currentFinding = $('.findings-new-add-comment').data("finding-id");

}
function insertTag(elem){
	var currentFinding = $('.findings-new-add-comment').data("finding-id");
    var cursorPos = $('.findings-new-add-comment-textarea textarea').prop('selectionStart');
    var v = $('.findings-new-add-comment-textarea textarea').val();
    var textBefore = v.substring(0,  cursorPos);
    var textAfter  = v.substring(cursorPos, v.length);

    $('.findings-new-add-comment-textarea textarea').val(textBefore + '%%' + $(elem).data("tag")+ '%%' + textAfter);

}
function saveBoilerplace(){
	var currentFinding = $('.findings-new-add-comment').data("finding-id");

}
function saveBoilerplaceAndNewFinding(){
	var currentFinding = $('.findings-new-add-comment').data("finding-id");

}

function expandFindingItems(element) {

	var parentFindingContainer = $(element).closest("[data-finding-id], .inspec-tools-tab-finding");
	var findingId = parentFindingContainer.data('finding-id');
	console.log(findingId);

	if (parentFindingContainer.attr('expanded')){
		parentFindingContainer.removeAttr('expanded');
		$('#inspec-tools-tab-finding-items-'+findingId).slideUp("slow", function() {

			$(this).remove();
		 });
	}else{

		var tempdiv = '<div id="inspec-tools-tab-finding-items-'+findingId+'" class="uk-width-1-1">';
		tempdiv = tempdiv + '<div style="height:500px;text-align:center;"><div uk-spinner style="margin: 10% 0;"></div></div>';
		tempdiv = tempdiv + '</div>';
		parentFindingContainer.append(tempdiv);

		var findingsItemsTemplate = $('#inspec-tools-tab-finding-items-template').html();
		var findingsItemTemplate = $('#inspec-tools-tab-finding-item-template').html();

		// var newfinding = newFindingsFormTemplate.replace(/tplFindingId/g, id);

		$('#inspec-tools-tab-finding-items-'+findingId).html(findingsItemsTemplate);
		$('#inspec-tools-tab-finding-items-'+findingId).find('.inspec-tools-tab-finding-items-list').html(findingsItemTemplate);

		$('#inspec-tools-tab-finding-items-'+findingId).find('.inspec-tools-tab-finding-items').slideDown("slow");
		parentFindingContainer.attr( "expanded", true );

	}	

	
}

function addChildItem(findingId, type) {
	console.log("adding a child item to this finding");
}


</script>