<div id="modal-findings" class="uk-margin-top">
	
	<div class="modal-findings-right">
		
		<div class="modal-findings-right-bottom-container">
			<div class="modal-findings-right-bottom">
				<div class="inspec-tools-tab-findings-container uk-panel uk-panel-scrollable uk-height-large uk-height-max-large">
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
		    	</div>
			</div>
		</div>
		<div class="modal-findings-right-top">
			<div class="uk-width-1-1 filter-button-set">
	        	<div uk-grid>
	        		<div class="uk-width-1-4">
	        			<button class="uk-button uk-button-default button-filter button-filter-border-left button-filter-selected">MY FINDINGS</button>
	        		</div>
	        		<div class="uk-width-1-4">
	        			<button class="uk-button uk-button-default button-filter">ALL FINDINGS</button>
			        	<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="" aria-expanded="false">
							<a id="" class="sort-desc" onclick="loadFindingsList();"></a>
						</span>
	        		</div>
	        		<div class="uk-width-1-4">
	        			<button class="uk-button uk-button-default button-filter">THIS AUDIT</button>
			        	
	        		</div>
	        		<div class="uk-width-1-4">
	        			<button class="uk-button uk-button-default button-filter">ALL AUDITS</button>
			        	<span data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column" title="" aria-expanded="false">
							<a id="" class="sort-asc" onclick="loadFindingsList();"></a>
						</span>
	        		</div>
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
									<a id="" class="sort-desc" onclick="loadFindingsList();"></a>
								</span>
			        		</div>
			        		<div class="uk-width-1-5">
			        			<button class="uk-button uk-button-default button-filter" uk-filter-control="filter: [data-finding='file'];"><i class="a-folder"></i></button>
					        	<span style="display: none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a id="" class="sort-desc" onclick="loadFindingsList();"></a>
								</span>
			        		</div>
			        		<div class="uk-width-1-5">
			        			<button class="uk-button uk-button-default button-filter" uk-filter-control="filter: [data-finding='nlt'];"><i class="a-booboo"></i></button>
					        	<span style="display: none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a id="" class="sort-desc" onclick="loadFindingsList();"></a>
								</span>
			        		</div>
			        		<div class="uk-width-1-5">
			        			<button class="uk-button uk-button-default button-filter" uk-filter-control="filter: [data-finding='lt'];"><i class="a-skull"></i></button>
					        	<span style="display: none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a id="" class="sort-asc" onclick="loadFindingsList();"></a>
								</span>
			        		</div>
			        		<div class="uk-width-1-5">
			        			<button class="uk-button uk-button-default button-filter" uk-filter-control="filter: [data-finding='sd'];"><i class="a-flames"></i></button>
					        	<span style="display: none" data-uk-tooltip="{pos:'bottom'}" class="uk-width-1-1 uk-padding-remove-top uk-margin-remove-top uk-grid-margin uk-first-column order-span" title="" aria-expanded="false">
									<a id="" class="sort-desc" onclick="loadFindingsList();"></a>
								</span>
			        		</div>
			        	</div>
			        </div>
			    </div>
			</div>
		</div>
	</div>
</div>

<template class="uk-hidden" id="modal-findings-new-template">
	<div class="findings-new-add-comment">
	    <div class="findings-new-add-comment-textarea">
	    	<textarea class="uk-textarea">Custom comment based on what I saw... %%date-in-7-days%%</textarea>
	    	<div class="textarea-status">SAVED</div>
	    </div>
	    <div class="findings-new-add-comment-boilerplate-action">
	    	<button><i class="a-file-text"></i> Use a boilerplate</button>
	    	<button><i class="a-file-minus"></i> Clear</button>
	    	<button><i class="a-file-plus"></i> Append a boilerplate</button>
	    </div>
	    <div class="findings-new-add-comment-quick-entry-list">
	    	<span class="uk-badge findings-quick-entry">PROPERTY MANAGER CONTACT NAME</span>
	    	<span class="uk-badge findings-quick-entry">ADDRESS OF THIS BUILDING</span>
	    	<span class="uk-badge findings-quick-entry">DATE IN 7 DAYS</span>
	    	<span class="uk-badge findings-quick-entry">TOMORROW'S DATE</span>
	    	<span class="uk-badge findings-quick-entry">HEAD OF HOUSEHOLD NAME</span>
	    	<span class="uk-badge findings-quick-entry">ANOTHER QUICK ENTRY BUTTON</span>
	    </div>
	    <div class="findings-new-add-comment-boilerplate-save">
	    	<button><i class="a-file-text"></i> Save as new boilerplate for this finding</button>
	    	<button><i class="a-file-copy-2"></i> Save and add another of this same finding</button>
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

function searchFilterTerm(valThis) {
	// combine with active filter
	var currentActiveFilter = $('.button-filter.uk-active');
	var filterFindings= $('.js-filter-findings');

    var activeFilterButton = $('.button-filter.uk-active').attr('uk-filter-control');

    var regexFilter = "data-finding='(.*)'"; //[data-finding='sd']
    var currentFilter = activeFilterButton.match(regexFilter)[1];

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
		$('.filter-checkbox-new-item-'+id).fadeOut("slow", function() {
			// unblur other building inspection rows
			$('div[id^="filter-checkbox-list-item-"]').not( 'div[id="filter-checkbox-list-item-'+id+'"]' ).slideDown();
			$('div[id^="filter-checkbox-list-item-"]').removeClass('blur');


			$(this).remove();
		 });
	}else{
		var newFindingsTemplate = $('#modal-findings-new-template').html();;

		$('div[id^="filter-checkbox-list-item-"]').not( 'div[id="filter-checkbox-list-item-'+id+'"]' ).addClass('blur');
		$('div[id^="filter-checkbox-list-item-"]').not( 'div[id="filter-checkbox-list-item-'+id+'"]' ).slideUp();

		$('#filter-checkbox-list-item-'+id).append('<div style="display:none" class="uk-width-1-1 uk-padding-remove filter-checkbox-new-item-'+id+'">'+newFindingsTemplate+'</div>');

		$('.filter-checkbox-new-item-'+id).fadeIn("slow");
		$('#filter-checkbox-list-item-'+id).attr( "expanded", true );
	}
	
}

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
</script>